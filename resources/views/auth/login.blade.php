<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk ke Sistem - KOMPLAIN</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="h-full text-slate-800 antialiased selection:bg-blue-500 selection:text-white">
    <div class="min-h-full flex flex-col justify-center py-10 sm:px-6 lg:px-8">
        <!-- Logo & Header Brand -->
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3 group focus:outline-none">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/25 group-hover:scale-105 transition duration-200">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </div>
                <div class="text-left">
                    <span class="text-2xl font-bold tracking-tight text-slate-900 block leading-tight">KOMPLAIN</span>
                    <span class="text-xs font-semibold uppercase tracking-wider text-blue-600 block">Layanan Pengaduan & Aspirasi</span>
                </div>
            </a>
            <h2 class="mt-6 text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
                Pintu Masuk Pengguna
            </h2>
            <p class="mt-1 text-sm text-slate-600">
                Silakan pilih jenis akun Anda untuk masuk ke sistem.
            </p>
        </div>

        <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Flash Message Status -->
            @if (session('status'))
                <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 flex items-start gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            <div class="bg-white py-8 px-5 sm:px-10 shadow-xl shadow-slate-200/50 rounded-2xl border border-slate-100">
                <!-- Role Selector Tabs -->
                @php
                    $activeRole = old('role_target', $defaultRole ?? 'pengadu');
                @endphp

                <div class="flex rounded-xl bg-slate-100 p-1 mb-6 border border-slate-200/70" role="tablist">
                    <button type="button"
                            id="tab-pengadu"
                            onclick="switchRole('pengadu')"
                            class="flex-1 py-2.5 px-3 rounded-lg text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-2 {{ $activeRole !== 'admin' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        <svg class="w-4 h-4 {{ $activeRole !== 'admin' ? 'text-blue-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Pengadu / Pelapor</span>
                    </button>

                    <button type="button"
                            id="tab-admin"
                            onclick="switchRole('admin')"
                            class="flex-1 py-2.5 px-3 rounded-lg text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-2 {{ $activeRole === 'admin' ? 'bg-white text-indigo-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        <svg class="w-4 h-4 {{ $activeRole === 'admin' ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>Administrator</span>
                    </button>
                </div>

                <!-- Info Box Context -->
                <div id="info-pengadu" class="{{ $activeRole === 'admin' ? 'hidden' : 'block' }} mb-6 p-3.5 rounded-xl bg-blue-50/70 border border-blue-100 text-xs text-blue-900">
                    <p class="font-semibold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Portal Pengadu (Pelapor / Pegawai Internal)
                    </p>
                    <p class="mt-1 text-blue-700">
                        Masuk untuk menyampaikan laporan aduan, melihat histori tiket, dan memantau status penyelesaian secara real-time.
                    </p>
                </div>

                <div id="info-admin" class="{{ $activeRole === 'admin' ? 'block' : 'hidden' }} mb-6 p-3.5 rounded-xl bg-indigo-50/70 border border-indigo-100 text-xs text-indigo-900">
                    <p class="font-semibold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Portal Administrator Sistem
                    </p>
                    <p class="mt-1 text-indigo-700">
                        Masuk ke dashboard untuk verifikasi aduan masuk, penugasan teknis, pemberian tanggapan, dan rekapitulasi data.
                    </p>
                </div>

                <!-- Form Login -->
                <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="role_target" id="role_target" value="{{ $activeRole }}">

                    <!-- Field Login (Email / NIP) -->
                    <div>
                        <label for="login" class="block text-sm font-medium text-slate-700">
                            Email atau NIP
                        </label>
                        <div class="mt-1.5 relative rounded-lg shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                                </svg>
                            </div>
                            <input type="text"
                                   name="login"
                                   id="login"
                                   value="{{ old('login') }}"
                                   required
                                   autocomplete="username"
                                   placeholder="misal: nama@komplain.test atau NIP"
                                   class="block w-full rounded-lg border pl-10 pr-3 py-2.5 text-sm transition focus:outline-none focus:ring-2 focus:ring-offset-1 {{ $errors->has('login') ? 'border-rose-300 text-rose-900 focus:border-rose-500 focus:ring-rose-500 bg-rose-50/30' : 'border-slate-300 focus:border-blue-500 focus:ring-blue-500' }}">
                        </div>
                        @error('login')
                            <p class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Field Password -->
                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-sm font-medium text-slate-700">
                                Password
                            </label>
                        </div>
                        <div class="mt-1.5 relative rounded-lg shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   required
                                   autocomplete="current-password"
                                   placeholder="••••••••"
                                   class="block w-full rounded-lg border pl-10 pr-10 py-2.5 text-sm transition focus:outline-none focus:ring-2 focus:ring-offset-1 {{ $errors->has('password') ? 'border-rose-300 text-rose-900 focus:border-rose-500 focus:ring-rose-500 bg-rose-50/30' : 'border-slate-300 focus:border-blue-500 focus:ring-blue-500' }}">
                            
                            <!-- Toggle Show Password -->
                            <button type="button"
                                    onclick="togglePasswordVisibility()"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-none"
                                    title="Tampilkan / Sembunyikan Password">
                                <svg id="eye-icon-show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eye-icon-hide" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox"
                                   name="remember"
                                   id="remember"
                                   class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-xs sm:text-sm text-slate-600">Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                                id="btn-submit"
                                class="w-full py-2.5 px-4 rounded-xl text-white font-semibold text-sm transition-all duration-200 shadow-md shadow-blue-500/20 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $activeRole === 'admin' ? 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 shadow-indigo-500/20' : 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 shadow-blue-500/20' }}">
                            <span id="btn-submit-text">
                                {{ $activeRole === 'admin' ? 'Masuk sebagai Administrator' : 'Masuk sebagai Pengadu' }}
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Quick Fill Demo Accounts -->
                <div class="mt-6 pt-5 border-t border-slate-100">
                    <p class="text-xs font-medium text-slate-500 mb-2.5 text-center">
                        Masuk cepat dengan akun simulasi / demo:
                    </p>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button"
                                onclick="fillDemo('pengadu@komplain.test', 'password', 'pengadu')"
                                class="py-2 px-2.5 rounded-lg border border-slate-200 text-left text-xs hover:bg-blue-50/50 hover:border-blue-200 transition group focus:outline-none">
                            <span class="block font-semibold text-slate-800 group-hover:text-blue-700">Akun Pengadu</span>
                            <span class="block text-[11px] text-slate-500 font-mono truncate">pengadu@komplain.test</span>
                        </button>

                        <button type="button"
                                onclick="fillDemo('admin@komplain.test', 'password', 'admin')"
                                class="py-2 px-2.5 rounded-lg border border-slate-200 text-left text-xs hover:bg-indigo-50/50 hover:border-indigo-200 transition group focus:outline-none">
                            <span class="block font-semibold text-slate-800 group-hover:text-indigo-700">Akun Admin</span>
                            <span class="block text-[11px] text-slate-500 font-mono truncate">admin@komplain.test</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Public Ticket Check & Back Home -->
            <div class="mt-6 text-center space-y-2">
                <p class="text-xs text-slate-500">
                    Punya kode tiket aduan?
                    <a href="{{ route('cek-pengajuan.index') }}" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline">
                        Cek Status Laporan tanpa login
                    </a>
                </p>

                <p class="text-xs text-slate-400">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-1 hover:text-slate-600 hover:underline">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke Halaman Utama
                    </a>
                </p>
            </div>
        </div>
    </div>

    <!-- Interactive UI Scripts -->
    <script>
        function switchRole(role) {
            const roleInput = document.getElementById('role_target');
            const tabPengadu = document.getElementById('tab-pengadu');
            const tabAdmin = document.getElementById('tab-admin');
            const infoPengadu = document.getElementById('info-pengadu');
            const infoAdmin = document.getElementById('info-admin');
            const btnSubmit = document.getElementById('btn-submit');
            const btnSubmitText = document.getElementById('btn-submit-text');
            const loginInput = document.getElementById('login');

            roleInput.value = role;

            if (role === 'admin') {
                // Tab styling
                tabAdmin.className = 'flex-1 py-2.5 px-3 rounded-lg text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-2 bg-white text-indigo-700 shadow-sm';
                tabPengadu.className = 'flex-1 py-2.5 px-3 rounded-lg text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-2 text-slate-600 hover:text-slate-900';

                // Tab icon colors
                tabAdmin.querySelector('svg').className = 'w-4 h-4 text-indigo-600';
                tabPengadu.querySelector('svg').className = 'w-4 h-4 text-slate-400';

                // Content display
                infoAdmin.classList.remove('hidden');
                infoPengadu.classList.add('hidden');

                // Button styling
                btnSubmit.className = 'w-full py-2.5 px-4 rounded-xl text-white font-semibold text-sm transition-all duration-200 bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-md shadow-indigo-500/20';
                btnSubmitText.textContent = 'Masuk sebagai Administrator';
                loginInput.placeholder = 'admin@komplain.test atau NIP';
            } else {
                // Tab styling
                tabPengadu.className = 'flex-1 py-2.5 px-3 rounded-lg text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-2 bg-white text-blue-700 shadow-sm';
                tabAdmin.className = 'flex-1 py-2.5 px-3 rounded-lg text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-2 text-slate-600 hover:text-slate-900';

                // Tab icon colors
                tabPengadu.querySelector('svg').className = 'w-4 h-4 text-blue-600';
                tabAdmin.querySelector('svg').className = 'w-4 h-4 text-slate-400';

                // Content display
                infoPengadu.classList.remove('hidden');
                infoAdmin.classList.add('hidden');

                // Button styling
                btnSubmit.className = 'w-full py-2.5 px-4 rounded-xl text-white font-semibold text-sm transition-all duration-200 bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-md shadow-blue-500/20';
                btnSubmitText.textContent = 'Masuk sebagai Pengadu';
                loginInput.placeholder = 'pengadu@komplain.test atau NIP';
            }
        }

        function fillDemo(email, password, role) {
            switchRole(role);
            document.getElementById('login').value = email;
            document.getElementById('password').value = password;
        }

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeShow = document.getElementById('eye-icon-show');
            const eyeHide = document.getElementById('eye-icon-hide');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeShow.classList.add('hidden');
                eyeHide.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeShow.classList.remove('hidden');
                eyeHide.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
