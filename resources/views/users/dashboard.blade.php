<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Kelola Users - Absensi Pegawai</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .modal {
            transition: opacity 0.25s ease;
        }

        body.modal-active {
            overflow-x: hidden;
            overflow-y: visible !important;
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Header -->
    <nav class="bg-gradient-to-r from-blue-600 to-blue-800 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <i class="fas fa-users-cog text-2xl mr-3"></i>
                    <h1 class="text-xl font-bold">Dashboard Kelola Users</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm">Admin Panel - Absensi Pegawai</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3"></i>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3"></i>
                    <p>{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Users</p>
                        <p class="text-3xl font-bold text-gray-800">{{ count($users) }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-4">
                        <i class="fas fa-users text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Registered Face</p>
                        <p class="text-3xl font-bold text-gray-800">
                            {{ collect($users)->filter(function ($user) {return !empty($user['data']['faceDataBase64'] ?? '');})->count() }}
                        </p>
                    </div>
                    <div class="bg-green-100 rounded-full p-4">
                        <i class="fas fa-user-check text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">No Face Data</p>
                        <p class="text-3xl font-bold text-gray-800">
                            {{ collect($users)->filter(function ($user) {return empty($user['data']['faceDataBase64'] ?? '');})->count() }}
                        </p>
                    </div>
                    <div class="bg-yellow-100 rounded-full p-4">
                        <i class="fas fa-user-slash text-yellow-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-list mr-2 text-blue-600"></i>Daftar Users
                    </h2>
                    <button onclick="openModal('addUserModal')"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 transform hover:scale-105">
                        <i class="fas fa-plus mr-2"></i>Tambah User
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-id-badge mr-2"></i>NIP
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-user mr-2"></i>Nama Lengkap
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-envelope mr-2"></i>Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-camera mr-2"></i>Status Face
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-calendar mr-2"></i>Tanggal Dibuat
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-cog mr-2"></i>Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $user['data']['nip'] ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div
                                                class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold">
                                                {{ strtoupper(substr($user['data']['fullName'] ?? 'U', 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $user['data']['fullName'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $user['data']['email'] ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if (!empty($user['data']['faceDataBase64'] ?? ''))
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Terdaftar
                                        </span>
                                    @else
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i> Belum
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ isset($user['data']['createdAt']) ? \Carbon\Carbon::parse($user['data']['createdAt'])->format('d M Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <button onclick="viewUser('{{ $user['id'] }}')"
                                        class="text-blue-600 hover:text-blue-900 mr-3 transition" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="editUser('{{ $user['id'] }}')"
                                        class="text-yellow-600 hover:text-yellow-900 mr-3 transition" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button
                                        onclick="deleteUser('{{ $user['id'] }}', '{{ addslashes($user['data']['fullName'] ?? 'User') }}')"
                                        class="text-red-600 hover:text-red-900 transition" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-400">
                                        <i class="fas fa-inbox text-6xl mb-4"></i>
                                        <p class="text-lg">Belum ada data user</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal"
        class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-50">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

        <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded-lg shadow-lg z-50 overflow-y-auto">
            <div class="modal-content py-4 text-left px-6">
                <div class="flex justify-between items-center pb-3 border-b">
                    <p class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-user-plus mr-2 text-blue-600"></i>Tambah User Baru
                    </p>
                    <button onclick="closeModal('addUserModal')" class="modal-close cursor-pointer z-50">
                        <i class="fas fa-times text-gray-400 hover:text-gray-600 text-xl"></i>
                    </button>
                </div>

                <form id="addUserForm" class="mt-4">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="nip">
                            <i class="fas fa-id-badge mr-1"></i>NIP
                        </label>
                        <input type="text" id="nip" name="nip" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Masukkan NIP">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="fullName">
                            <i class="fas fa-user mr-1"></i>Nama Lengkap
                        </label>
                        <input type="text" id="fullName" name="fullName" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Masukkan nama lengkap">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                            <i class="fas fa-envelope mr-2 text-gray-500"></i>Email
                        </label>
                        <input type="email" id="email" name="email" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="email@example.com">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                            <i class="fas fa-lock mr-2 text-gray-500"></i>Password
                        </label>
                        <input type="password" id="password" name="password" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="Masukkan password">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-camera mr-1"></i>Face Data (Opsional)
                        </label>
                        <p class="text-xs text-gray-500 mb-2">Data wajah akan diisi melalui aplikasi mobile</p>
                    </div>

                    <div class="flex items-center justify-end pt-4 border-t">
                        <button type="button" onclick="closeModal('addUserModal')"
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 mr-2 transition">
                            <i class="fas fa-times mr-1"></i>Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-save mr-1"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal"
        class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-50">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

        <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded-lg shadow-lg z-50 overflow-y-auto">
            <div class="modal-content py-4 text-left px-6">
                <div class="flex justify-between items-center pb-3 border-b">
                    <p class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-edit mr-2 text-yellow-600"></i>Edit User
                    </p>
                    <button onclick="closeModal('editUserModal')" class="modal-close cursor-pointer z-50">
                        <i class="fas fa-times text-gray-400 hover:text-gray-600 text-xl"></i>
                    </button>
                </div>

                <form id="editUserForm" class="mt-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_user_id" name="id">

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_nip">
                            <i class="fas fa-id-badge mr-1"></i>NIP
                        </label>
                        <input type="text" id="edit_nip" name="nip"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100 leading-tight"
                            readonly>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_fullName">
                            <i class="fas fa-user mr-1"></i>Nama Lengkap
                        </label>
                        <input type="text" id="edit_fullName" name="fullName" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_email">
                            <i class="fas fa-envelope mr-1"></i>Email
                        </label>
                        <input type="email" id="edit_email" name="email" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="flex items-center justify-end pt-4 border-t">
                        <button type="button" onclick="closeModal('editUserModal')"
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 mr-2 transition">
                            <i class="fas fa-times mr-1"></i>Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                            <i class="fas fa-save mr-1"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View User Modal -->
    <div id="viewUserModal"
        class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-50">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

        <div class="modal-container bg-white w-11/12 md:max-w-lg mx-auto rounded-lg shadow-lg z-50 overflow-y-auto">
            <div class="modal-content py-4 text-left px-6">
                <div class="flex justify-between items-center pb-3 border-b">
                    <p class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-user-circle mr-2 text-blue-600"></i>Detail User
                    </p>
                    <button onclick="closeModal('viewUserModal')" class="modal-close cursor-pointer z-50">
                        <i class="fas fa-times text-gray-400 hover:text-gray-600 text-xl"></i>
                    </button>
                </div>

                <div id="userDetailContent" class="mt-4">
                    <!-- Content will be loaded dynamically -->
                </div>

                <div class="flex items-center justify-end pt-4 border-t mt-4">
                    <button type="button" onclick="closeModal('viewUserModal')"
                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">
                        <i class="fas fa-times mr-1"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Modal Functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('opacity-0', 'pointer-events-none');
            document.body.classList.add('modal-active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('opacity-0', 'pointer-events-none');
            document.body.classList.remove('modal-active');
        }

        // Add User
        document.getElementById('addUserForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = {
                nip: document.getElementById('nip').value,
                fullName: document.getElementById('fullName').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                faceDataBase64: ''
            };

            try {
                const response = await fetch('/api/users', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    alert('User berhasil ditambahkan!');
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Gagal menambahkan user'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        });

        // View User
        async function viewUser(userId) {
            try {
                const response = await fetch(`/api/users/${userId}`);
                const data = await response.json();

                if (data.success) {
                    const user = data.data.data || data.data; // Handle nested structure
                    const content = `
                        <div class="space-y-4">
                            <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 rounded-lg">
                                <div class="flex items-center justify-center mb-4">
                                    <div class="h-20 w-20 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold text-3xl">
                                        ${(user.fullName || 'U').charAt(0).toUpperCase()}
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500 font-medium"><i class="fas fa-id-badge mr-1"></i>NIP</p>
                                    <p class="text-gray-900 font-semibold">${user.nip || '-'}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium"><i class="fas fa-user mr-1"></i>Nama</p>
                                    <p class="text-gray-900 font-semibold">${user.fullName || '-'}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-sm text-gray-500 font-medium"><i class="fas fa-envelope mr-1"></i>Email</p>
                                    <p class="text-gray-900 font-semibold">${user.email || '-'}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-sm text-gray-500 font-medium"><i class="fas fa-key mr-1"></i>Password</p>
                                    <p class="text-gray-900 font-semibold">${user.passwordHash || '-'}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium"><i class="fas fa-camera mr-1"></i>Status Face</p>
                                    <p class="font-semibold ${user.faceDataBase64 ? 'text-green-600' : 'text-red-600'}">
                                        ${user.faceDataBase64 ? '✓ Terdaftar' : '✗ Belum Terdaftar'}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium"><i class="fas fa-calendar mr-1"></i>Dibuat</p>
                                    <p class="text-gray-900 font-semibold">${user.createdAt ? new Date(user.createdAt).toLocaleDateString('id-ID') : '-'}</p>
                                </div>
                            </div>
                        </div>
                    `;
                    document.getElementById('userDetailContent').innerHTML = content;
                    openModal('viewUserModal');
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        // Edit User
        async function editUser(userId) {
            try {
                const response = await fetch(`/api/users/${userId}`);
                const data = await response.json();

                if (data.success) {
                    const user = data.data.data || data.data; // Handle nested structure
                    document.getElementById('edit_user_id').value = data.data.id || userId;
                    document.getElementById('edit_nip').value = user.nip || '';
                    document.getElementById('edit_fullName').value = user.fullName || '';
                    document.getElementById('edit_email').value = user.email || '';
                    openModal('editUserModal');
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        // Update User
        document.getElementById('editUserForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const userId = document.getElementById('edit_user_id').value;
            const formData = {
                fullName: document.getElementById('edit_fullName').value,
                email: document.getElementById('edit_email').value
            };

            try {
                const response = await fetch(`/api/users/${userId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    alert('User berhasil diupdate!');
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Gagal mengupdate user'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        });

        // Delete User
        async function deleteUser(userId, userName) {
            if (!confirm(`Apakah Anda yakin ingin menghapus user "${userName}"?`)) {
                return;
            }

            try {
                const response = await fetch(`/api/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alert('User berhasil dihapus!');
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        // Close modal when clicking outside
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function() {
                const modal = this.parentElement;
                modal.classList.add('opacity-0', 'pointer-events-none');
                document.body.classList.remove('modal-active');
            });
        });
    </script>
</body>

</html>
