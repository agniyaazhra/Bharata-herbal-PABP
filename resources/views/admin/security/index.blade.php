<x-layouts.admin>
    <x-slot name="title">Pengaturan Keamanan</x-slot>
    <x-slot name="subtitle">Kelola akses admin dan sesi perangkat yang aktif</x-slot>

    <div class="space-y-8" x-data="securityManagement()">

        {{-- ════════════════════════════════════════════════════════════════════ --}}
        {{-- SECTION 1: MANAJEMEN AKSES ADMIN --}}
        {{-- ════════════════════════════════════════════════════════════════════ --}}

        <div class="max-w-5xl">

            {{-- Section Header --}}
            <div class="mb-6">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2 mb-1">
                    <span class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="shield-alert" class="w-5 h-5 text-purple-600"></i>
                    </span>
                    Manajemen Akses Admin
                </h2>
                <p class="text-sm text-gray-500 ml-10">Kelola daftar admin dan super admin yang dapat mengakses panel ini</p>
            </div>

            {{-- Success/Error Messages --}}
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

            {{-- Tombol Tambah Admin --}}
            <div class="mb-6 flex justify-end">
                <button @click="openAdminFormModal()"
                    class="flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition">
                    <i data-lucide="plus" class="w-4 h-4"></i> Tambah Admin Baru
                </button>
            </div>

            {{-- Admin Table --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="text-left font-semibold text-gray-600 px-6 py-4">Nama Admin</th>
                                <th class="text-left font-semibold text-gray-600 px-4 py-4">Email</th>
                                <th class="text-left font-semibold text-gray-600 px-4 py-4">Role</th>
                                <th class="text-left font-semibold text-gray-600 px-4 py-4">Terakhir Login</th>
                                <th class="text-right font-semibold text-gray-600 px-6 py-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($admins as $admin)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-purple-100 flex items-center justify-center font-bold text-purple-700 text-sm shrink-0">
                                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800">
                                                {{ $admin->name }}
                                                @if ($admin->id === $currentUser->id)
                                                <span class="ml-1 text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">Anda</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-gray-600 text-xs">{{ $admin->email }}</td>
                                <td class="px-4 py-4">
                                    @if($admin->role === 'super_admin')
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">Super Admin</span>
                                    @else
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Admin</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-gray-500 text-xs">
                                    {{ $admin->last_login ? $admin->last_login->diffForHumans() : 'Belum pernah' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openAdminFormModal(adminsData[{{ $loop->index }}])"
                                            class="p-2 rounded-lg hover:bg-blue-50 text-blue-600 transition" title="Edit Admin">
                                            <i data-lucide="pencil" class="w-4 h-4"></i>
                                        </button>
                                        @if ($admin->id !== $currentUser->id)
                                        <button @click="openDeleteConfirmation(adminsData[{{ $loop->index }}])"
                                            class="p-2 rounded-lg hover:bg-red-50 text-red-500 transition" title="Hapus Admin">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                    <i data-lucide="users" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                    <p class="font-medium">Tidak ada admin ditemukan.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($admins->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 text-sm">
                    {{ $admins->links() }}
                </div>
                @endif
            </div>

            {{-- Info pembelian tentang Super Admin --}}
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-800">
                <p class="font-semibold mb-2">ℹ️ Tentang Role Super Admin</p>
                <p class="text-blue-700">
                    Super Admin memiliki akses penuh termasuk manajemen admin, pengaturan keamanan, dan kontrol akses sistem.
                    Pastikan hanya pengguna terpercaya yang memiliki role ini.
                </p>
            </div>

        </div>

        {{-- ════════════════════════════════════════════════════════════════════ --}}
        {{-- SECTION 2: MANAJEMEN SESI AKTIF --}}
        {{-- ════════════════════════════════════════════════════════════════════ --}}

        <div class="max-w-4xl">

            {{-- Section Header --}}
            <div class="mb-6">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2 mb-1">
                    <span class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="monitor" class="w-5 h-5 text-orange-600"></i>
                    </span>
                    Manajemen Sesi Aktif
                </h2>
                <p class="text-sm text-gray-500 ml-10">Kelola semua perangkat yang sedang login ke akun Anda</p>
            </div>

            {{-- Logout dari Semua Perangkat --}}
            <div class="bg-orange-50 border border-orange-200 rounded-2xl p-6 mb-6 flex items-center justify-between gap-6">
                <div class="flex-1">
                    <p class="font-semibold text-orange-900 text-sm mb-1">Logout dari Semua Perangkat Lain</p>
                    <p class="text-orange-700 text-xs">
                        Sesi saat ini akan tetap aktif. Semua perangkat lain akan dikeluarkan dan harus login ulang.
                    </p>
                </div>
                <button @click="openLogoutAllConfirmation()"
                    class="shrink-0 bg-orange-600 hover:bg-orange-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium transition flex items-center gap-2">
                    <i data-lucide="log-out" class="w-4 h-4"></i> Logout Semua
                </button>
            </div>

            {{-- Daftar Sesi/Perangkat Aktif --}}
            <div class="space-y-4">
                @forelse ($sessions as $session)
                @php
                $icon = match(true) {
                str_contains(strtolower($session->device_name ?? ''), 'mobile') => '📱',
                str_contains(strtolower($session->device_name ?? ''), 'tablet') => '📟',
                default => '💻'
                };
                @endphp
                <div class="bg-white rounded-2xl border {{ $session->is_current ? 'border-green-300 shadow-green-100 shadow-md' : 'border-gray-100 shadow-sm' }} p-6 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-4">
                        {{-- Device Info --}}
                        <div class="flex items-start gap-4 flex-1">
                            {{-- Device Icon --}}
                            <div class="text-3xl shrink-0 pt-0.5">{{ $icon }}</div>

                            {{-- Device Details --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <p class="font-semibold text-gray-800">
                                        {{ $session->device_name ?? 'Perangkat Tidak Diketahui' }}
                                    </p>
                                    @if($session->is_current)
                                    <span class="inline-block text-xs bg-green-100 text-green-700 px-2.5 py-0.5 rounded-full font-semibold">
                                        Sesi Saat Ini
                                    </span>
                                    @endif
                                </div>

                                {{-- Device Info Grid --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2">
                                    <div class="flex items-center gap-2 text-xs text-gray-600">
                                        <i data-lucide="globe" class="w-4 h-4 text-gray-400 shrink-0"></i>
                                        <span class="font-medium">Browser:</span>
                                        <span class="text-gray-700">{{ $session->browser ?? 'Tidak dikenal' }}</span>
                                    </div>

                                    <div class="flex items-center gap-2 text-xs text-gray-600">
                                        <i data-lucide="map-pin" class="w-4 h-4 text-gray-400 shrink-0"></i>
                                        <span class="font-medium">IP:</span>
                                        <span class="text-gray-700 font-mono">{{ $session->ip_address ?? '-' }}</span>
                                    </div>

                                    @if($session->location)
                                    <div class="flex items-center gap-2 text-xs text-gray-600">
                                        <i data-lucide="navigation" class="w-4 h-4 text-gray-400 shrink-0"></i>
                                        <span class="font-medium">Lokasi:</span>
                                        <span class="text-gray-700">{{ $session->location }}</span>
                                    </div>
                                    @endif

                                    <div class="flex items-center gap-2 text-xs text-gray-600">
                                        <i data-lucide="clock" class="w-4 h-4 text-gray-400 shrink-0"></i>
                                        <span class="font-medium">Terakhir Aktif:</span>
                                        <span class="text-gray-700">{{ $session->last_active_for_humans ?? 'Tidak diketahui' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Action Button --}}
                        <div class="shrink-0 pl-4 border-l border-gray-100">
                            @if (!$session->is_current)
                            <button @click="openLogoutSessionConfirmation(sessionsData[{{ $loop->index }}])"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-medium border border-red-200 hover:bg-red-50 text-red-600 transition">
                                <i data-lucide="x-circle" class="w-4 h-4"></i>
                                <span class="hidden sm:inline">Akhiri Sesi</span>
                            </button>
                            @else
                            <div class="flex items-center justify-center px-4 py-2 text-green-600 text-xs font-semibold">
                                <i data-lucide="check-circle-2" class="w-4 h-4 mr-1"></i>
                                <span class="hidden sm:inline">Aktif Sekarang</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center text-gray-400">
                    <i data-lucide="monitor" class="w-10 h-10 mx-auto mb-3 opacity-30"></i>
                    <p class="font-medium text-gray-600">Tidak ada sesi aktif yang tercatat.</p>
                    <p class="text-sm text-gray-400 mt-1">Sesi akan otomatis tercatat saat Anda login.</p>
                </div>
                @endforelse
            </div>

            {{-- Info tentang sesi --}}
            <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-800">
                <p class="font-semibold mb-2">💡 Tips Keamanan</p>
                <ul class="text-amber-700 space-y-1 text-xs">
                    <li>• Periksa perangkat yang tidak dikenal dan logout jika perlu.</li>
                    <li>• Gunakan **Logout Semua Perangkat** setelah bermain sendirian atau ganti password.</li>
                    <li>• Pastikan Anda hanya login di perangkat yang Anda percayai.</li>
                </ul>
            </div>

        </div>

        {{-- ════════════════════════════════════════════════════════════════════ --}}
        {{-- ADMIN FORM MODAL (Inside x-data scope) --}}
        {{-- ════════════════════════════════════════════════════════════════════ --}}
        <x-forms.admin-form-modal />

        {{-- ════════════════════════════════════════════════════════════════════ --}}
        {{-- CONFIRMATION MODALS (Inside x-data scope) --}}
        {{-- ════════════════════════════════════════════════════════════════════ --}}
        <x-modals.confirmation-modal id="deleteAdminModal" />
        <x-modals.confirmation-modal id="logoutSessionModal" />
        <x-modals.confirmation-modal id="logoutAllModal" />

    </div>

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- ALPINE.JS DATA FUNCTION --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
    <script>
        function securityManagement() {
            return {
               
                adminsData: @json($adminsArray),
                sessionsData: @json($sessionsArray),

                showModal: false,
                isEditMode: false,
                formAction: '',
                formData: {
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    phone: '',
                    role: 'admin',
                },
                editUser: null,
                errors: [],
                fieldErrors: {},
                isLoading: false,
                showPassword: false,

                confirmationModals: {
                    deleteAdminModal: {
                        show: false,
                        title: 'Hapus Admin?',
                        message: 'Apakah yakin ingin menghapus admin ini? Tindakan ini tidak bisa dibatalkan.',
                        icon: 'trash-2',
                        iconColor: 'red',
                        cancelText: 'Batal',
                        confirmText: 'Ya, hapus',
                        isLoading: false,
                        action: null,
                        data: null,
                    },
                    logoutSessionModal: {
                        show: false,
                        title: 'Akhiri Sesi?',
                        message: 'Keluar dari perangkat ini?',
                        icon: 'log-out',
                        iconColor: 'red',
                        cancelText: 'Batal',
                        confirmText: 'Ya, keluar',
                        isLoading: false,
                        action: null,
                        data: null,
                    },
                    logoutAllModal: {
                        show: false,
                        title: 'Logout Semua Perangkat?',
                        message: 'Logout dari semua perangkat lain? Sesi Anda saat ini akan tetap aktif.',
                        icon: 'log-out',
                        iconColor: 'orange',
                        cancelText: 'Batal',
                        confirmText: 'Ya, logout semua',
                        isLoading: false,
                        action: null,
                        data: null,
                    },
                },

                openAdminFormModal(admin = null) {
                    this.showModal = true;
                    this.errors = [];
                    this.fieldErrors = {};

                    if (admin) {
                        this.isEditMode = true;
                        this.formAction = `/admin/admins/${admin.id}`;
                        this.formData = {
                            name: admin.name,
                            email: admin.email,
                            password: '',
                            password_confirmation: '',
                            phone: admin.phone || '',
                            role: admin.role,
                        };
                        this.editUser = {
                            created_at: new Date(admin.created_at).toLocaleDateString('id-ID'),
                            last_login: admin.last_login ? new Date(admin.last_login).toLocaleDateString('id-ID') : 'Belum pernah',
                        };
                    } else {
                        this.isEditMode = false;
                        this.formAction = '/admin/admins';
                        this.formData = {
                            name: '',
                            email: '',
                            password: '',
                            password_confirmation: '',
                            phone: '',
                            role: 'admin',
                        };
                        this.editUser = null;
                    }
                },

                closeAdminModal() {
                    this.showModal = false;
                    this.isEditMode = false;
                    this.errors = [];
                    this.fieldErrors = {};
                    this.formData = {
                        name: '',
                        email: '',
                        password: '',
                        password_confirmation: '',
                        phone: '',
                        role: 'admin',
                    };
                    this.editUser = null;
                    this.showPassword = false;
                },

                async submitForm() {
                    this.isLoading = true;
                    this.errors = [];
                    this.fieldErrors = {};

                    try {
                        const method = this.isEditMode ? 'PUT' : 'POST';
                        const response = await fetch(this.formAction, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify(this.formData),
                        });

                        const data = await response.json();

                        if (response.ok) {
                           
                            setTimeout(() => {
                                window.location.reload();
                            }, 500);
                        } else {
                           
                            if (data.errors) {
                                this.fieldErrors = data.errors;
                            } else if (data.message) {
                                this.errors.push(data.message);
                            }
                        }
                    } catch (error) {
                        this.errors.push('Terjadi kesalahan. Silakan coba lagi.');
                        console.error('Error:', error);
                    } finally {
                        this.isLoading = false;
                    }
                },

                openDeleteConfirmation(admin) {
                    this.confirmationModals.deleteAdminModal.show = true;
                    this.confirmationModals.deleteAdminModal.data = admin;
                    this.confirmationModals.deleteAdminModal.action = 'deleteAdmin';
                },

                openLogoutSessionConfirmation(session) {
                    this.confirmationModals.logoutSessionModal.show = true;
                    this.confirmationModals.logoutSessionModal.data = session;
                    this.confirmationModals.logoutSessionModal.action = 'logoutSession';
                },

                openLogoutAllConfirmation() {
                    this.confirmationModals.logoutAllModal.show = true;
                    this.confirmationModals.logoutAllModal.action = 'logoutAll';
                },

                closeConfirmationModal(id) {
                    if (this.confirmationModals[id]) {
                        this.confirmationModals[id].show = false;
                        this.confirmationModals[id].action = null;
                        this.confirmationModals[id].data = null;
                    }
                },

                async submitConfirmation(id) {
                    const modal = this.confirmationModals[id];
                    if (!modal || !modal.action) return;

                    modal.isLoading = true;

                    try {
                        if (modal.action === 'deleteAdmin') {
                            await this.deleteAdmin(modal.data);
                        } else if (modal.action === 'logoutSession') {
                            await this.logoutSession(modal.data);
                        } else if (modal.action === 'logoutAll') {
                            await this.logoutAll();
                        }
                    } finally {
                        modal.isLoading = false;
                    }
                },

                async deleteAdmin(admin) {
                    try {
                        const response = await fetch(`/admin/admins/${admin.id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                        });

                        if (response.ok) {
                            this.closeConfirmationModal('deleteAdminModal');
                            setTimeout(() => {
                                window.location.reload();
                            }, 500);
                        } else {
                            const data = await response.json();
                            alert(data.message || 'Gagal menghapus admin.');
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                        console.error('Error:', error);
                    }
                },

                async logoutSession(session) {
                    try {
                        const response = await fetch(`/admin/sessions/${session.id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                        });

                        if (response.ok) {
                            this.closeConfirmationModal('logoutSessionModal');
                            setTimeout(() => {
                                window.location.reload();
                            }, 500);
                        } else {
                            const data = await response.json();
                            alert(data.message || 'Gagal mengakhiri sesi.');
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                        console.error('Error:', error);
                    }
                },

                async logoutAll() {
                    try {
                        const response = await fetch('/admin/sessions', {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                        });

                        if (response.ok) {
                            this.closeConfirmationModal('logoutAllModal');
                            setTimeout(() => {
                                window.location.reload();
                            }, 500);
                        } else {
                            const data = await response.json();
                            alert(data.message || 'Gagal logout dari semua perangkat.');
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                        console.error('Error:', error);
                    }
                },
            };
        }
    </script>

</x-layouts.admin>