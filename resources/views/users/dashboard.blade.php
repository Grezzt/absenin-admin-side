<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Kelola Users - Absensi Pegawai</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        * {
            font-family: 'Public Sans', sans-serif;
        }

        body {
            background: #FFFBEB;
        }

        .neo-card {
            border: 3px solid #000000;
            box-shadow: 5px 5px 0 #000000;
            transition: all 0.15s ease;
        }

        .neo-card:hover {
            transform: translate(-2px, -2px);
            box-shadow: 7px 7px 0 #000000;
        }

        .neo-button {
            border: 3px solid #000000;
            box-shadow: 4px 4px 0 #000000;
            transition: all 0.1s ease;
            font-weight: 800;
        }

        .neo-button:hover {
            transform: translate(2px, 2px);
            box-shadow: 2px 2px 0 #000000;
        }

        .neo-button:active {
            transform: translate(4px, 4px);
            box-shadow: 0 0 0 #000000;
        }

        .neo-input {
            border: 3px solid #000000;
            box-shadow: 3px 3px 0 #000000;
            transition: all 0.15s ease;
        }

        .neo-input:focus {
            outline: none;
            box-shadow: 5px 5px 0 #000000;
            transform: translate(-1px, -1px);
        }

        .neo-modal {
            border: 4px solid #000000;
            box-shadow: 8px 8px 0 #000000;
        }

        .modal {
            transition: opacity 0.2s ease;
        }

        body.modal-active {
            overflow: hidden;
        }

        .table-row-neo {
            transition: all 0.1s ease;
            border-bottom: 2px solid #000000;
        }

        .table-row-neo:hover {
            background: #FEF3C7;
            transform: translate(-2px, -2px);
        }

        /* Electric Blue */
        .bg-electric {
            background-color: #0EA5E9;
        }

        /* Vibrant Green */
        .bg-vibrant-green {
            background-color: #10B981;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-track {
            background: #FEF3C7;
            border: 2px solid #000000;
        }

        ::-webkit-scrollbar-thumb {
            background: #0EA5E9;
            border: 2px solid #000000;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #0284C7;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <nav class="neo-card bg-white sticky top-0 z-40 mb-6">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="neo-card bg-electric p-3">
                        <i class="fas fa-users-cog text-2xl text-white"></i>
                    </div>
                    <h1 class="text-3xl font-black text-black uppercase tracking-tight">Dashboard Kelola Users</h1>
                </div>
                <div class="flex items-center">
                    <span class="text-sm font-bold text-black uppercase tracking-wide">Admin Panel</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @if (session('success'))
            <div class="neo-card bg-vibrant-green p-4 mb-6" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3 text-white text-2xl"></i>
                    <p class="text-white font-black text-lg">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="neo-card bg-red-500 p-4 mb-6" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3 text-white text-2xl"></i>
                    <p class="text-white font-black text-lg">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="neo-card bg-white p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest mb-2 text-black">Total Users</p>
                        <p class="text-5xl font-black text-black">{{ count($users) }}</p>
                    </div>
                    <div class="neo-card bg-electric p-4">
                        <i class="fas fa-users text-white text-4xl"></i>
                    </div>
                </div>
            </div>

            <div class="neo-card bg-white p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest mb-2 text-black">Registered Face</p>
                        <p class="text-5xl font-black text-black">
                            {{ collect($users)->filter(function ($user) {
    return !empty($user['data']['faceDataBase64'] ?? ''); })->count() }}
                        </p>
                    </div>
                    <div class="neo-card bg-vibrant-green p-4">
                        <i class="fas fa-user-check text-white text-4xl"></i>
                    </div>
                </div>
            </div>

            <div class="neo-card bg-white p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest mb-2 text-black">No Face Data</p>
                        <p class="text-5xl font-black text-black">
                            {{ collect($users)->filter(function ($user) {
    return empty($user['data']['faceDataBase64'] ?? ''); })->count() }}
                        </p>
                    </div>
                    <div class="neo-card bg-yellow-400 p-4">
                        <i class="fas fa-user-slash text-black text-4xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="neo-card bg-white overflow-hidden">
            <div class="px-6 py-5 border-b-4 border-black">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-black text-black uppercase">
                        <i class="fas fa-list mr-3"></i>Daftar Users
                    </h2>
                    <button onclick="openModal('addUserModal')"
                        class="neo-button bg-electric text-white px-8 py-3 uppercase">
                        <i class="fas fa-plus mr-2"></i>Tambah User
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="border-b-4 border-black">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-black">
                                <i class="fas fa-id-badge mr-2"></i>NIP
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-black">
                                <i class="fas fa-user mr-2"></i>Nama Lengkap
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-black">
                                <i class="fas fa-envelope mr-2"></i>Email
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-black">
                                <i class="fas fa-camera mr-2"></i>Status Face
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-black">
                                <i class="fas fa-calendar mr-2"></i>Tanggal Dibuat
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-black uppercase tracking-widest text-black">
                                <i class="fas fa-cog mr-2"></i>Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr class="table-row-neo">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-black text-black">{{ $user['data']['nip'] ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="neo-card bg-electric h-12 w-12 flex items-center justify-center text-white font-black text-lg">
                                                {{ strtoupper(substr($user['data']['fullName'] ?? 'U', 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-black">
                                                {{ $user['data']['fullName'] ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-black">{{ $user['data']['email'] ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if (!empty($user['data']['faceDataBase64'] ?? ''))
                                        <span
                                            class="neo-card bg-vibrant-green px-4 py-2 inline-flex text-xs font-black uppercase text-white">
                                            <i class="fas fa-check-circle mr-2"></i> Terdaftar
                                        </span>
                                    @else
                                        <span
                                            class="neo-card bg-red-500 px-4 py-2 inline-flex text-xs font-black uppercase text-white">
                                            <i class="fas fa-times-circle mr-2"></i> Belum
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-black">
                                    {{ isset($user['data']['createdAt']) ? \Carbon\Carbon::parse($user['data']['createdAt'])->format('d M Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                    <button onclick="viewUser('{{ $user['id'] }}')"
                                        class="neo-button bg-electric text-white px-3 py-2 mr-2" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="editUser('{{ $user['id'] }}')"
                                        class="neo-button bg-yellow-400 text-black px-3 py-2 mr-2" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button
                                        onclick="deleteUser('{{ $user['id'] }}', '{{ addslashes($user['data']['fullName'] ?? 'User') }}')"
                                        class="neo-button bg-red-500 text-white px-3 py-2" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="text-black">
                                        <i class="fas fa-inbox text-8xl mb-4"></i>
                                        <p class="text-2xl font-black uppercase">Belum ada data user</p>
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
        class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-50 p-4">
        <div class="modal-overlay absolute w-full h-full bg-black/60"></div>

        <div class="modal-container neo-modal bg-white w-full md:max-w-md mx-auto z-50 p-6">
            <div class="flex justify-between items-center pb-3 border-b-4 border-black mb-4">
                <p class="text-xl font-black text-black uppercase">
                    <i class="fas fa-user-plus mr-2"></i>Tambah User Baru
                </p>
                <button onclick="closeModal('addUserModal')" class="text-black hover:text-gray-700 text-3xl font-black">
                    ×
                </button>
            </div>

            <form id="addUserForm" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-black text-sm font-black mb-2 uppercase">
                        <i class="fas fa-id-badge mr-2"></i>NIP
                    </label>
                    <input type="text" id="nip" name="nip" required
                        class="neo-input w-full py-3 px-4 text-black bg-white" placeholder="Masukkan NIP">
                </div>

                <div>
                    <label class="block text-black text-sm font-black mb-2 uppercase">
                        <i class="fas fa-user mr-2"></i>Nama Lengkap
                    </label>
                    <input type="text" id="fullName" name="fullName" required
                        class="neo-input w-full py-3 px-4 text-black bg-white" placeholder="Masukkan nama lengkap">
                </div>

                <div>
                    <label class="block text-black text-sm font-black mb-2 uppercase">
                        <i class="fas fa-envelope mr-2"></i>Email
                    </label>
                    <input type="email" id="email" name="email" required
                        class="neo-input w-full py-3 px-4 text-black bg-white" placeholder="email@example.com">
                </div>

                <div>
                    <label class="block text-black text-sm font-black mb-2 uppercase">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <input type="password" id="password" name="password" required
                        class="neo-input w-full py-3 px-4 text-black bg-white" placeholder="Masukkan password">
                </div>

                <div class="neo-card bg-yellow-100 p-4">
                    <label class="block text-black text-xs font-black uppercase">
                        <i class="fas fa-camera mr-2"></i>Face Data (Opsional)
                    </label>
                    <p class="text-xs text-black font-semibold mt-1">Data wajah akan diisi melalui aplikasi mobile</p>
                </div>

                <div class="flex items-center justify-end pt-6 space-x-3 border-t-4 border-black">
                    <button type="button" onclick="closeModal('addUserModal')"
                        class="neo-button bg-gray-300 text-black px-6 py-3 uppercase font-black">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="submit" class="neo-button bg-electric text-white px-6 py-3 uppercase font-black">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal"
        class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-50 p-4">
        <div class="modal-overlay absolute w-full h-full bg-black/60"></div>

        <div class="modal-container neo-modal bg-white w-full md:max-w-md mx-auto z-50 p-6">
            <div class="flex justify-between items-center pb-3 border-b-4 border-black mb-4">
                <p class="text-xl font-black text-black uppercase">
                    <i class="fas fa-edit mr-2"></i>Edit User
                </p>
                <button onclick="closeModal('editUserModal')"
                    class="text-black hover:text-gray-700 text-3xl font-black">
                    ×
                </button>
            </div>

            <form id="editUserForm" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_user_id" name="id">

                <div>
                    <label class="block text-black text-sm font-black mb-2 uppercase">
                        <i class="fas fa-id-badge mr-2"></i>NIP
                    </label>
                    <input type="text" id="edit_nip" name="nip"
                        class="neo-input w-full py-3 px-4 text-gray-600 bg-gray-100" readonly>
                </div>

                <div>
                    <label class="block text-black text-sm font-black mb-2 uppercase">
                        <i class="fas fa-user mr-2"></i>Nama Lengkap
                    </label>
                    <input type="text" id="edit_fullName" name="fullName" required
                        class="neo-input w-full py-3 px-4 text-black bg-white">
                </div>

                <div>
                    <label class="block text-black text-sm font-black mb-2 uppercase">
                        <i class="fas fa-envelope mr-2"></i>Email
                    </label>
                    <input type="email" id="edit_email" name="email" required
                        class="neo-input w-full py-3 px-4 text-black bg-white">
                </div>

                <div class="flex items-center justify-end pt-6 space-x-3 border-t-4 border-black">
                    <button type="button" onclick="closeModal('editUserModal')"
                        class="neo-button bg-gray-300 text-black px-6 py-3 uppercase font-black">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="submit" class="neo-button bg-yellow-400 text-black px-6 py-3 uppercase font-black">
                        <i class="fas fa-save mr-2"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View User Modal -->
    <div id="viewUserModal"
        class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-50 p-4">
        <div class="modal-overlay absolute w-full h-full bg-black/60"></div>

        <div class="modal-container neo-modal bg-white w-full md:max-w-lg mx-auto z-50 p-6">
            <div class="flex justify-between items-center pb-3 border-b-4 border-black mb-4">
                <p class="text-xl font-black text-black uppercase">
                    <i class="fas fa-user-circle mr-2"></i>Detail User
                </p>
                <button onclick="closeModal('viewUserModal')"
                    class="text-black hover:text-gray-700 text-3xl font-black">
                    ×
                </button>
            </div>

            <div id="userDetailContent">
                <!-- Content will be loaded dynamically -->
            </div>

            <div class="flex items-center justify-end pt-6 border-t-4 border-black mt-6">
                <button type="button" onclick="closeModal('viewUserModal')"
                    class="neo-button bg-gray-300 text-black px-6 py-3 uppercase font-black">
                    <i class="fas fa-times mr-2"></i>Tutup
                </button>
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

        // Toggle Password Visibility
        let passwordVisible = false;
        function togglePassword(passwordHash) {
            const passwordDisplay = document.getElementById('passwordDisplay');
            const toggleBtn = document.getElementById('togglePasswordBtn');

            if (!passwordVisible) {
                passwordDisplay.textContent = passwordHash || 'No password';
                toggleBtn.innerHTML = '<i class="fas fa-eye-slash mr-1"></i>Hide Password';
                passwordVisible = true;
            } else {
                passwordDisplay.textContent = '●●●●●●●●●●';
                toggleBtn.innerHTML = '<i class="fas fa-eye mr-1"></i>View Password';
                passwordVisible = false;
            }
        }

        // Add User
        document.getElementById('addUserForm').addEventListener('submit', async function (e) {
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
                            <div class="neo-card bg-electric p-5 text-center">
                                <div class="neo-card bg-white h-20 w-20 mx-auto flex items-center justify-center text-black font-black text-4xl mb-3">
                                    ${(user.fullName || 'U').charAt(0).toUpperCase()}
                                </div>
                                <h3 class="text-xl font-black text-white uppercase">${user.fullName || '-'}</h3>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="neo-card bg-white p-3">
                                    <p class="text-xs font-black uppercase text-black mb-1"><i class="fas fa-id-badge mr-1"></i>NIP</p>
                                    <p class="text-black font-bold text-base">${user.nip || '-'}</p>
                                </div>
                                <div class="neo-card bg-white p-3">
                                    <p class="text-xs font-black uppercase text-black mb-1"><i class="fas fa-envelope mr-1"></i>Email</p>
                                    <p class="text-black font-bold text-sm break-all">${user.email || '-'}</p>
                                </div>
                                <div class="col-span-2 neo-card bg-white p-3">
                                    <p class="text-xs font-black uppercase text-black mb-2"><i class="fas fa-lock mr-1"></i>Password</p>
                                    <div id="passwordContainer">
                                        <p class="text-black font-mono text-sm mb-2" id="passwordDisplay">●●●●●●●●●●</p>
                                        <button onclick="togglePassword('${user.passwordHash || ''}')" id="togglePasswordBtn" class="neo-button bg-yellow-400 text-black px-3 py-1 text-xs uppercase font-black">
                                            <i class="fas fa-eye mr-1"></i>View Password
                                        </button>
                                    </div>
                                </div>
                                <div class="neo-card bg-white p-3">
                                    <p class="text-xs font-black uppercase text-black mb-1"><i class="fas fa-camera mr-1"></i>Status Face</p>
                                    <p class="font-black text-base ${user.faceDataBase64 ? 'text-green-600' : 'text-red-600'}">
                                        ${user.faceDataBase64 ? '<i class="fas fa-check-circle mr-1"></i> Terdaftar' : '<i class="fas fa-times-circle mr-1"></i> Belum'}
                                    </p>
                                </div>
                                <div class="neo-card bg-white p-3">
                                    <p class="text-xs font-black uppercase text-black mb-1"><i class="fas fa-calendar mr-1"></i>Dibuat</p>
                                    <p class="text-black font-bold text-sm">${user.createdAt ? new Date(user.createdAt).toLocaleDateString('id-ID') : '-'}</p>
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
        document.getElementById('editUserForm').addEventListener('submit', async function (e) {
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
            overlay.addEventListener('click', function () {
                const modal = this.parentElement;
                modal.classList.add('opacity-0', 'pointer-events-none');
                document.body.classList.remove('modal-active');
            });
        });
    </script>
</body>

</html>