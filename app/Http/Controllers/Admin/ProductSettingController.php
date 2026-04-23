<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductSetting;
use App\Services\ProductStockService;
use Illuminate\Http\Request;

class ProductSettingController extends Controller
{
    public function __construct(protected ProductStockService $stockService) {}

    public function index()
    {
        $settings = ProductSetting::first();

        if (!$settings) {
            $settings = ProductSetting::create([
                'minimum_stock_alert' => 10,
                'notify_email_admin' => true,
                'notify_dashboard_only' => false,
                'auto_disable_when_out_of_stock' => true,
                'alert_when_below_minimum' => true,
            ]);
        }

        return view('admin.settings.product', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'stock_minimum' => 'required|integer|min:0|max:9999',
        ]);

        $settings = ProductSetting::first();

        if (!$settings) {
            $settings = new ProductSetting();
        }

        $settings->fill([
            'minimum_stock_alert' => $request->stock_minimum,
            'notify_email_admin' => $request->notification_type === 'email',
            'notify_dashboard_only' => $request->notification_type === 'dashboard',
            'auto_disable_when_out_of_stock' => $request->boolean('auto_nonaktif_stok_habis'),
            'alert_when_below_minimum' => $request->boolean('auto_warning_stok_minimum'),
        ]);

        $settings->save();

        $this->stockService->syncAllProductStatuses();

        return redirect()->route('admin.settings.product')->with('success', 'Pengaturan produk berhasil disimpan.');
    }

public function reset()
{
    $settings = ProductSetting::first();

    if (!$settings) {
        $settings = new ProductSetting();
    }

    $settings->fill([
        'minimum_stock_alert' => 10,
        'notify_email_admin' => true,
        'notify_dashboard_only' => false,
        'auto_disable_when_out_of_stock' => true,
        'alert_when_below_minimum' => true,
    ]);

    $settings->save();

    $this->stockService->syncAllProductStatuses();

    return redirect()->route('admin.settings.product')->with('success', 'Pengaturan produk direset ke default.');
}}