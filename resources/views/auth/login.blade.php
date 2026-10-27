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
<body class="min-h-full bg-slate-50 text-slate-800 antialiased selection:bg-blue-600 selection:text-white flex items-center justify-center p-4 sm:p-6 lg:p-8">
    <div class="max-w-4xl w-full bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden grid grid-cols-1 md:grid-cols-12 my-auto">
        <!-- Kolom Kiri: Ilustrasi Resmi & Identitas Sistem (Latar Lembut, Nyaman di Mata) -->
        <div class="md:col-span-6 bg-slate-50/70 p-6 sm:p-8 flex flex-col justify-between items-center border-b md:border-b-0 md:border-r border-slate-100">
            <!-- Header Brand -->
            <div class="w-full flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-sm shrink-0">
                    K
                </div>
                <div>
                    <span class="font-bold text-slate-900 text-base leading-tight block">KOMPLAIN</span>
                    <span class="text-[11px] text-slate-500 block">Layanan Pengaduan & Aspirasi Internal</span>
                </div>
            </div>

            <!-- Ilustrasi Tengah -->
            <div class="my-6 w-full flex items-center justify-center">
                <img src="{{ asset('images/ilustrasi-login.png') }}"
                     alt="Ilustrasi KOMPLAIN"
                     class="max-h-[280px] sm:max-h-[320px] w-auto object-contain">
            </div>

            <!-- Keterangan Sistem -->
            <div class="w-full text-center">
                <p class="text-xs text-slate-500 leading-relaxed">
                    Komunikasi Online Masalah Pelayanan & Laporan Aduan INternal
                </p>
            </div>
        </div>

        <!-- Kolom Kanan: Formulir Login Langsung (Single Input Form) -->
        <div class="md:col-span-6 p-6 sm:p-8 sm:px-10 flex flex-col justify-center bg-white">
            <div>
                <!-- Judul & Keterangan -->
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                    Masuk ke Sistem
                </h1>

                <!-- Notifikasi Status Flash -->
                @if (session('status'))
                    <div class="mt-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-start gap-2 shadow-sm">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mt-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800 flex items-start gap-2 shadow-sm">
                        <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Form Login Langsung -->
                <form action="{{ route('login.post') }}" method="POST" class="mt-5 space-y-4">
                    @csrf

                    <!-- Input Email atau NIP -->
                    <div>
                        <label for="login" class="block text-xs font-semibold text-slate-700 mb-1">
                            Email atau NIP <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               name="login"
                               id="login"
                               value="{{ old('login') }}"
                               required
                               autofocus
                               autocomplete="username"
                               placeholder="Masukkan Email atau NIP Anda..."
                               class="w-full text-xs sm:text-sm rounded-xl border {{ $errors->has('login') ? 'border-rose-300 bg-rose-50/20 text-rose-900 focus:border-rose-500 focus:ring-rose-500/20' : 'border-slate-300 text-slate-800 placeholder-slate-400 focus:border-blue-600 focus:ring-blue-500/20' }} py-2.5 px-3.5 focus:outline-none focus:ring-2 transition shadow-sm font-medium">
                        @error('login')
                            <p class="mt-1 text-xs text-rose-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Input Password -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">
                            Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password"
                                   name="password"
                                   id="password"
                                   required
                                   autocomplete="current-password"
                                   placeholder="Masukkan password Anda..."
                                   class="w-full text-xs sm:text-sm rounded-xl border {{ $errors->has('password') ? 'border-rose-300 bg-rose-50/20 text-rose-900 focus:border-rose-500 focus:ring-rose-500/20' : 'border-slate-300 text-slate-800 placeholder-slate-400 focus:border-blue-600 focus:ring-blue-500/20' }} py-2.5 pl-3.5 pr-10 focus:outline-none focus:ring-2 transition shadow-sm font-medium">
                            <button type="button"
                                    onclick="togglePasswordVisibility()"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition"
                                    title="Tampilkan / Sembunyikan Password">
                                <svg id="eye-show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eye-hide" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-xs text-rose-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Ingat Saya -->
                    <div class="flex items-center justify-between pt-0.5">
                        <label class="flex items-center gap-2 cursor-pointer select-none text-xs text-slate-600">
                            <input type="checkbox"
                                   name="remember"
                                   id="remember"
                                   class="w-3.5 h-3.5 rounded text-blue-600 border-slate-300 focus:ring-blue-500/20">
                            <span>Ingat saya</span>
                        </label>
                    </div>

                    <!-- Tombol Masuk -->
                    <div class="pt-2">
                        <button type="submit"
                                class="w-full py-2.5 px-4 rounded-xl font-semibold text-xs sm:text-sm text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 transition shadow-sm flex items-center justify-center gap-1.5">
                            <span>Masuk ke Sistem</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer Hak Cipta Ringkas -->
            <p class="text-[11px] text-slate-400 text-center mt-6">
                KOMPLAIN &copy; {{ date('Y') }}
            </p>
        </div>
    </div>

    <!-- Script Tampilkan / Sembunyikan Password -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeShow = document.getElementById('eye-show');
            const eyeHide = document.getElementById('eye-hide');

            if (!passwordInput) return;

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                if (eyeShow) eyeShow.classList.add('hidden');
                if (eyeHide) eyeHide.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                if (eyeShow) eyeShow.classList.remove('hidden');
                if (eyeHide) eyeHide.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
