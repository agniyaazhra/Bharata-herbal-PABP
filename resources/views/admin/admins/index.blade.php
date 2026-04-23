<x-layouts.admin>
    <x-slot name="title">Manajemen Admin</x-slot>
    <x-slot name="subtitle">Kelola akun admin dan super admin yang dapat mengakses panel ini</x-slot>

    <div class="max-w-5xl" x-data="adminManagement()">

        @if (session('success'))
        <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl p-4 text-green-800 text-sm">
            <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
        @endif

        @if (session('error'))
        <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 rounded-xl p-4 text-red-800 text-sm">
            <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            {{ session('error') }}
        </div>
        @endif

        {{-- Header + Search + Add --}}
        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between mb-6">
            <form action="{{ route('admin.admins.index') }}" method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama atau email..."
                    class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm w-72 focus:ring-2 focus:ring-green-500/30 outline-none">
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2.5 rounded-xl text-sm text-gray-700 transition">
                    Cari
                </button>
            </form>
            <button @click="openCreateModal()"
                class="flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Admin
            </button>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left font-semibold text-gray-600 px-6 py-4">Admin</th>
                            <th class="text-left font-semibold text-gray-600 px-4 py-4">Role</th>
                            <th class="text-left font-semibold text-gray-600 px-4 py-4">Login Terakhir</th>
                            <th class="text-right font-semibold text-gray-600 px-6 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($admins as $admin)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center font-bold text-green-700 text-sm shrink-0">
                                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">
                                            {{ $admin->name }}
                                            @if ($admin->id === $currentUser->id)
                                            <span class="ml-1 text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">Anda</span>
                                            @endif
                                        </p>
                                        <p class="text-gray-500 text-xs">{{ $admin->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                @if($admin->role === 'super_admin')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">Super Admin</span>
                                @else
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Admin</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-gray-500 text-xs">
                                {{ $admin->last_login ? $admin->last_login->diffForHumans() : 'Belum pernah' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openEditModal({{ json_encode($admin) }})"
                                        class="p-2 rounded-lg hover:bg-blue-50 text-blue-600 transition" title="Edit">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </button>
                                    @if ($admin->id !== $currentUser->id)
                                    <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST"
                                        onsubmit="return confirm('Hapus admin {{ $admin->name }}? Tindakan ini tidak bisa dibatalkan.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg hover:bg-red-50 text-red-500 transition" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                <i data-lucide="users" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                <p>Tidak ada admin ditemukan.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($admins->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $admins->links() }}
            </div>
            @endif
        </div>

        {{-- Modal --}}
        <x-forms.admin-form-modal />

    </div>

    <script>
        function adminManagement() {
            return {
                showModal: false,
                isEditMode: false,
                isLoading: false,
                showPassword: false,
                errors: [],
                fieldErrors: {},
                formAction: '',
                editUser: null,
                formData: {
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    phone: '',
                    role: 'admin',
                },

                openCreateModal() {
                    this.isEditMode = false;
                    this.resetForm();
                    this.formAction = '{{ route("admin.admins.store") }}';
                    this.showModal = true;
                },

                openEditModal(user) {
                    this.isEditMode = true;
                    this.editUser = user;
                    this.formData = {
                        name: user.name,
                        email: user.email,
                        password: '',
                        password_confirmation: '',
                        phone: user.phone || '',
                        role: user.role,
                    };
                    this.formAction = `/admin/admins/${user.id}`;
                    this.errors = [];
                    this.fieldErrors = {};
                    this.showModal = true;
                },

                resetForm() {
                    this.formData = {
                        name: '',
                        email: '',
                        password: '',
                        password_confirmation: '',
                        phone: '',
                        role: 'admin',
                    };
                    this.errors = [];
                    this.fieldErrors = {};
                    this.editUser = null;
                    this.showPassword = false;
                },

                closeModal() {
                    this.showModal = false;
                    setTimeout(() => this.resetForm(), 300);
                },

                async submitForm() {
                    this.isLoading = true;
                    this.errors = [];
                    this.fieldErrors = {};

                    try {
                        const formElement = document.querySelector('form[action]');
                        const formData = new FormData(formElement);

                        if (this.isEditMode) {
                            formData.append('_method', 'PUT');
                        }

                        const response = await fetch(this.formAction, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        });

                        if (!response.ok) {
                            const data = await response.json();

                            if (data.errors) {
                                this.fieldErrors = data.errors;
                                this.errors = Object.values(data.errors).flat();
                            } else if (data.message) {
                                this.errors = [data.message];
                            }
                            return;
                        }

                        window.location.reload();
                    } catch (error) {
                        this.errors = ['Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'];
                        console.error(error);
                    } finally {
                        this.isLoading = false;
                    }
                }
            }
        }
    </script>

</x-layouts.admin>