<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'customer')
            ->withCount('orders')
            ->withSum('orders', 'total_price')
            ->with('latestOrder');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        match ($request->sort) {
            'oldest'      => $query->oldest(),
            'most_orders' => $query->orderByDesc('orders_count'),
            default       => $query->latest(),
        };

        if ($request->boolean('export')) {
            return $this->exportCustomers($query->get());
        }

        $customers = $query->paginate(15)->withQueryString();

        $totalCustomers  = User::where('role', 'customer')->count();
        $activeCustomers = User::where('role', 'customer')->whereHas('orders')->count();
        $totalOrders     = \App\Models\Order::count();
        $totalRevenue    = \App\Models\Order::where('status', '!=', 'cancelled')->sum('total_price');
        $newThisMonth    = User::where('role', 'customer')->whereMonth('created_at', now()->month)->count();

        return view('admin.customers.index', compact(
            'customers',
            'totalCustomers',
            'activeCustomers',
            'totalOrders',
            'totalRevenue',
            'newThisMonth'
        ));
    }

    public function show(User $user)
    {
        $orders = $user->orders()->with('items.product', 'payment')->latest()->paginate(10);
        return view('admin.customers.show', compact('user', 'orders'));
    }

    private function exportCustomers($customers)
    {
        $filename = 'pelanggan_' . now()->format('Ymd_His') . '.csv';
        $headers = ['Content-Type' => 'text/csv; charset=UTF-8'];

        $callback = function () use ($customers) {
            $handle = fopen('php://output', 'w');
           
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'No',
                'Nama Pelanggan',
                'Email',
                'Nomor Telepon',
                'Tanggal Bergabung',
                'Total Pesanan',
                'Total Belanja (Rp)',
                'Terakhir Transaksi',
                'Status'
            ]);

            foreach ($customers as $index => $customer) {
                $lastOrder = $customer->latestOrder;
                $totalSpent = $customer->orders_sum_total_price ?? 0;

                fputcsv($handle, [
                    $index + 1,
                    $customer->name,
                    $customer->email,
                    $customer->phone ?? '-',
                    $customer->created_at->format('d/m/Y'),
                    $customer->orders_count,
                    number_format($totalSpent, 0, ',', '.'),
                    $lastOrder ? $lastOrder->created_at->format('d/m/Y H:i') : '-',
                    $customer->orders_count > 0 ? 'Aktif' : 'Tidak Aktif'
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }
}
