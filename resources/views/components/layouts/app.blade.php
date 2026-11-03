<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $header ?? config('app.name', 'Festival Inovasi') }} - Festival Inovasi</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Smooth transitions for sidebar */
        #app-sidebar {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar-transition-element {
            transition: opacity 0.2s ease, transform 0.2s ease, width 0.3s ease;
        }
        
        /* Scrollbar vertikal tipis & tak menonjol untuk nav sidebar */
        .sidebar-nav-scroll {
            scrollbar-width: thin;
            scrollbar-color: #e2e8f0 transparent;
        }
        .sidebar-nav-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar-nav-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-nav-scroll::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 9999px;
        }
        .sidebar-nav-scroll::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }

        /* Collapsed Sidebar Styles */
        body.sidebar-collapsed #app-sidebar {
            width: 5rem !important; /* 80px */
        }
        body.sidebar-collapsed .sidebar-label {
            opacity: 0;
            width: 0;
            display: none;
        }
        body.sidebar-collapsed .sidebar-section-header {
            opacity: 0;
            height: 0;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        body.sidebar-collapsed .sidebar-brand-text {
            display: none;
        }
        body.sidebar-collapsed .sidebar-brand-icon {
            margin: 0 auto;
        }
        body.sidebar-collapsed .nav-item-link {
            justify-content: center;
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
        body.sidebar-collapsed .sidebar-collapse-text {
            display: none;
        }
        body.sidebar-collapsed .sidebar-collapse-btn {
            justify-content: center;
        }
        body.sidebar-collapsed .sidebar-collapse-icon {
            transform: rotate(180deg);
        }
        body.sidebar-collapsed .tooltip-when-collapsed {
            opacity: 0;
            pointer-events: none;
        }
        body.sidebar-collapsed .group:hover .tooltip-when-collapsed {
            opacity: 1;
        }
    </style>

    <script>
        // Apply saved collapse state before rendering to prevent visual flicker (FOUC)
        (function() {
            try {
                if (localStorage.getItem('fi_sidebar_collapsed') === 'true') {
                    document.documentElement.classList.add('sidebar-init-collapsed');
                    document.addEventListener('DOMContentLoaded', function() {
                        document.body.classList.add('sidebar-collapsed');
                    });
                }
            } catch (e) {}
        })();
    </script>
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-50 flex flex-col">

    <div class="flex h-screen overflow-hidden bg-slate-50">

        <!-- Mobile Backdrop Overlay -->
        <div id="mobile-sidebar-backdrop" 
             onclick="toggleMobileSidebar()" 
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity duration-300 opacity-0"></div>

        <!-- ========================================================= -->
        <!-- SIDEBAR -->
        <!-- ========================================================= -->
        <aside id="app-sidebar" 
               class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 flex flex-col justify-between transform -translate-x-full lg:translate-x-0 lg:static transition-all duration-300 ease-in-out shrink-0 select-none shadow-xl lg:shadow-none">
            
            <!-- Top: Brand Header -->
            <div>
                <div class="h-16 flex items-center px-4 border-b border-slate-200 bg-white justify-between">
                    <a href="{{ auth()->user()->isSuperadmin() ? route('admin.dashboard') : route('jury.dashboard') }}" 
                       class="flex items-center gap-3 group overflow-hidden">
                        <div class="sidebar-brand-icon w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center text-white shadow-md shadow-blue-500/20 shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div class="sidebar-brand-text truncate">
                            <span class="text-base font-bold text-slate-900 tracking-tight group-hover:text-blue-600 transition-colors">
                                Festival Inovasi
                            </span>
                            <span class="block text-[10px] uppercase font-semibold tracking-wider text-slate-500">
                                {{ auth()->user()->isSuperadmin() ? 'Super Admin Portal' : 'Juri Portal' }}
                            </span>
                        </div>
                    </a>

                    <!-- Close button on Mobile only -->
                    <button type="button" onclick="toggleMobileSidebar()" class="lg:hidden text-slate-500 hover:text-slate-900 p-1.5 rounded-lg hover:bg-slate-100">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Navigation Links List -->
                <nav class="p-3 space-y-6 overflow-y-auto overflow-x-hidden max-h-[calc(100vh-8.5rem)] sidebar-nav-scroll">
                    
                    @if(auth()->user()->isSuperadmin())
                        <!-- Section: UTAMA -->
                        <div>
                            <div class="sidebar-section-header px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-600 sidebar-transition-element">
                                Menu Utama
                            </div>
                            <div class="space-y-1">
                                <!-- Dashboard -->
                                <div class="relative group">
                                    <a href="{{ route('admin.dashboard') }}" 
                                       class="nav-item-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/30' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                        </svg>
                                        <span class="sidebar-label truncate">Dashboard</span>
                                    </a>
                                    <span class="tooltip-when-collapsed absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-lg shadow-xl z-50 pointer-events-none border border-slate-800 whitespace-nowrap opacity-0 transition-opacity duration-150">
                                        Dashboard
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Section: MASTER DATA -->
                        <div>
                            <div class="sidebar-section-header px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-600 sidebar-transition-element">
                                Master Data
                            </div>
                            <div class="space-y-1">
                                <!-- Festival -->
                                <div class="relative group">
                                    <a href="{{ route('admin.festivals.index') }}" 
                                       class="nav-item-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.festivals.*') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/30' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="sidebar-label truncate">Festival</span>
                                    </a>
                                    <span class="tooltip-when-collapsed absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-lg shadow-xl z-50 pointer-events-none border border-slate-800 whitespace-nowrap opacity-0 transition-opacity duration-150">
                                        Festival
                                    </span>
                                </div>

                                <!-- Kategori & Subkategori -->
                                <div class="relative group">
                                    <a href="{{ route('admin.festival-categories.index') }}" 
                                       class="nav-item-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ (request()->routeIs('admin.festival-categories.*') || request()->routeIs('admin.award-categories.*')) ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/30' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                        <span class="sidebar-label truncate">Kategori & Subkategori</span>
                                    </a>
                                    <span class="tooltip-when-collapsed absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-lg shadow-xl z-50 pointer-events-none border border-slate-800 whitespace-nowrap opacity-0 transition-opacity duration-150">
                                        Kategori & Subkategori
                                    </span>
                                </div>

                                <!-- Instansi -->
                                <div class="relative group">
                                    <a href="{{ route('admin.institutions.index') }}" 
                                       class="nav-item-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.institutions.*') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/30' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span class="sidebar-label truncate">Instansi</span>
                                    </a>
                                    <span class="tooltip-when-collapsed absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-lg shadow-xl z-50 pointer-events-none border border-slate-800 whitespace-nowrap opacity-0 transition-opacity duration-150">
                                        Instansi
                                    </span>
                                </div>

                                <!-- Inovasi -->
                                <div class="relative group">
                                    <a href="{{ route('admin.innovations.index') }}" 
                                       class="nav-item-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.innovations.*') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/30' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                        </svg>
                                        <span class="sidebar-label truncate">Inovasi</span>
                                    </a>
                                    <span class="tooltip-when-collapsed absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-lg shadow-xl z-50 pointer-events-none border border-slate-800 whitespace-nowrap opacity-0 transition-opacity duration-150">
                                        Inovasi
                                    </span>
                                </div>

                                <!-- Akun Juri -->
                                <div class="relative group">
                                    <a href="{{ route('admin.juries.index') }}" 
                                       class="nav-item-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.juries.*') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/30' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <span class="sidebar-label truncate">Kelola Akun Juri</span>
                                    </a>
                                    <span class="tooltip-when-collapsed absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-lg shadow-xl z-50 pointer-events-none border border-slate-800 whitespace-nowrap opacity-0 transition-opacity duration-150">
                                        Kelola Akun Juri
                                    </span>
                                </div>

                                <!-- Kriteria Penilaian -->
                                <div class="relative group">
                                    <a href="{{ route('admin.assessment-criteria.index') }}" 
                                       class="nav-item-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.assessment-criteria.*') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/30' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                        <span class="sidebar-label truncate">Kriteria & Bobot</span>
                                    </a>
                                    <span class="tooltip-when-collapsed absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-lg shadow-xl z-50 pointer-events-none border border-slate-800 whitespace-nowrap opacity-0 transition-opacity duration-150">
                                        Kriteria & Bobot
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Section: PENILAIAN & LAPORAN -->
                        <div>
                            <div class="sidebar-section-header px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-600 sidebar-transition-element">
                                Penilaian & Hasil
                            </div>
                            <div class="space-y-1">
                                <!-- Penugasan Juri -->
                                <div class="relative group">
                                    <a href="{{ route('admin.festival-juries.index') }}" 
                                       class="nav-item-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.festival-juries.*') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/30' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                        </svg>
                                        <span class="sidebar-label truncate">Penugasan Juri</span>
                                    </a>
                                    <span class="tooltip-when-collapsed absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-lg shadow-xl z-50 pointer-events-none border border-slate-800 whitespace-nowrap opacity-0 transition-opacity duration-150">
                                        Penugasan Juri
                                    </span>
                                </div>

                                <!-- Monitoring Penilaian -->
                                <div class="relative group">
                                    <a href="{{ route('admin.scoring.monitoring') }}" 
                                       class="nav-item-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.scoring.*') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/30' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        <span class="sidebar-label truncate">Monitoring Nilai</span>
                                    </a>
                                    <span class="tooltip-when-collapsed absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-lg shadow-xl z-50 pointer-events-none border border-slate-800 whitespace-nowrap opacity-0 transition-opacity duration-150">
                                        Monitoring Nilai
                                    </span>
                                </div>

                                <!-- Pemenang -->
                                <div class="relative group">
                                    <a href="{{ route('admin.winners.index') }}" 
                                       class="nav-item-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ (request()->routeIs('admin.winners.*') || request()->routeIs('admin.award-categories.rankings')) ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/30' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                        </svg>
                                        <span class="sidebar-label truncate">Penetapan Pemenang</span>
                                    </a>
                                    <span class="tooltip-when-collapsed absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-lg shadow-xl z-50 pointer-events-none border border-slate-800 whitespace-nowrap opacity-0 transition-opacity duration-150">
                                        Penetapan Pemenang
                                    </span>
                                </div>

                                <!-- Laporan PDF -->
                                <div class="relative group">
                                    <a href="{{ route('admin.reports.index') }}" 
                                       class="nav-item-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.reports.*') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/30' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                        <span class="sidebar-label truncate">Laporan & Publikasi</span>
                                    </a>
                                    <span class="tooltip-when-collapsed absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-lg shadow-xl z-50 pointer-events-none border border-slate-800 whitespace-nowrap opacity-0 transition-opacity duration-150">
                                        Laporan & Publikasi
                                    </span>
                                </div>
                            </div>
                        </div>

                    @elseif(auth()->user()->isJury())
                        <!-- Section: MENU JURI -->
                        <div>
                            <div class="sidebar-section-header px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-600 sidebar-transition-element">
                                Penilaian Juri
                            </div>
                            <div class="space-y-1">
                                <!-- Dashboard Juri -->
                                <div class="relative group">
                                    <a href="{{ route('jury.dashboard') }}" 
                                       class="nav-item-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('jury.dashboard') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/30' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                        <span class="sidebar-label truncate">Dashboard Penilaian</span>
                                    </a>
                                    <span class="tooltip-when-collapsed absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-lg shadow-xl z-50 pointer-events-none border border-slate-800 whitespace-nowrap opacity-0 transition-opacity duration-150">
                                        Dashboard Penilaian
                                    </span>
                                </div>

                                @if(isset($currentFestival) && $currentFestival)
                                    <!-- Pilih Subkategori Partisipasi -->
                                    <div class="relative group">
                                        <a href="{{ route('jury.participations.index', $currentFestival) }}" 
                                            class="nav-item-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('jury.participations.*') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/30' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                            <span class="sidebar-label truncate">Pilih Subkategori</span>
                                        </a>
                                        <span class="tooltip-when-collapsed absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-lg shadow-xl z-50 pointer-events-none border border-slate-800 whitespace-nowrap opacity-0 transition-opacity duration-150">
                                            Pilih Subkategori
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                </nav>
            </div>

            <!-- Bottom: Collapse Toggle Button (Desktop) -->
            <div class="p-3 border-t border-slate-200 bg-slate-50/50">
                <button type="button" 
                        onclick="toggleSidebar()" 
                        class="sidebar-collapse-btn w-full flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                    <svg class="sidebar-collapse-icon w-4 h-4 shrink-0 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                    <span class="sidebar-collapse-text truncate">Ciutkan Menu</span>
                </button>
            </div>
        </aside>

        <!-- ========================================================= -->
        <!-- MAIN CONTENT AREA -->
        <!-- ========================================================= -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            
            <!-- Top Navbar -->
            <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-gray-200/90 shadow-sm shrink-0">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        
                        <!-- Left: Sidebar Toggle + Title/Context -->
                        <div class="flex items-center gap-3">
                            <!-- Mobile Hamburger Button -->
                            <button type="button" 
                                    onclick="toggleMobileSidebar()" 
                                    class="lg:hidden p-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <span class="sr-only">Buka menu</span>
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>

                            <!-- Desktop Sidebar Toggle Icon -->
                            <button type="button" 
                                    onclick="toggleSidebar()" 
                                    title="Toggle Sidebar"
                                    class="hidden lg:flex p-2 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" />
                                </svg>
                            </button>

                            <!-- Context / Breadcrumb / Section Header -->
                            <div class="flex items-center gap-2 text-sm">
                                <a href="{{ route('home') }}" class="text-gray-400 hover:text-gray-600 hidden sm:inline-flex items-center gap-1 font-medium">
                                    <span>Beranda</span>
                                </a>
                                <span class="text-gray-300 hidden sm:inline">/</span>
                                <span class="font-semibold text-gray-900 truncate">
                                    {{ $header ?? 'Dashboard' }}
                                </span>
                            </div>
                        </div>

                        <!-- Right: Active Festival + User Info + Logout -->
                        <div class="flex items-center gap-3 sm:gap-4">
                            
                            <!-- Festival Active Badge (Superadmin only) -->
                            @if(isset($currentFestival) && $currentFestival)
                                <a href="{{ route('admin.select-festival.index') }}" 
                                   title="Klik untuk memilih periode festival lain"
                                   class="hidden md:inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200/80 hover:bg-blue-100 hover:border-blue-300 transition-colors">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>{{ $currentFestival->name }} ({{ $currentFestival->year }})</span>
                                </a>
                            @endif

                            <!-- User Profile Dropdown (Alpine.js) -->
                            <div x-data="{ open: false }" class="relative" @click.away="open = false">
                                <!-- Profile Button -->
                                <button type="button" 
                                        @click="open = !open" 
                                        class="flex items-center gap-2.5 pl-2 sm:border-l border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-lg p-1 transition-colors hover:bg-gray-50"
                                        :class="{ 'bg-gray-50': open }">
                                    <div class="w-8 h-8 rounded-full bg-slate-900 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-sm">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                    </div>
                                    <div class="hidden sm:block text-left">
                                        <div class="text-xs font-semibold text-gray-900 leading-tight">
                                            {{ auth()->user()->name }}
                                        </div>
                                        <div class="text-[10px] font-medium text-slate-500 uppercase tracking-wider">
                                            {{ auth()->user()->role->label() }}
                                        </div>
                                    </div>
                                    <!-- Dropdown Arrow -->
                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 hidden sm:block" 
                                         fill="none" 
                                         viewBox="0 0 24 24" 
                                         stroke="currentColor"
                                         :class="{ 'rotate-180': open }">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                
                                <!-- Dropdown Menu -->
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 transform scale-95"
                                     x-transition:enter-end="opacity-100 transform scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="opacity-100 transform scale-100"
                                     x-transition:leave-end="opacity-0 transform scale-95"
                                     class="absolute right-0 mt-2 w-56 rounded-xl bg-white border border-gray-200 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50 overflow-hidden"
                                     style="display: none;">
                                    
                                    <!-- User Info Section -->
                                    <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ auth()->user()->name }}
                                        </div>
                                        <div class="text-xs text-slate-500 uppercase tracking-wider font-medium">
                                            {{ auth()->user()->role->label() }}
                                        </div>
                                    </div>
                                    
                                    <!-- Logout Action -->
                                    <div class="p-2">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" 
                                                    class="w-full flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium text-red-600 hover:text-white hover:bg-red-600 transition-colors">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                </svg>
                                                <span>Keluar</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Scrollable Body Content -->
            <main class="flex-1 py-8 px-4 sm:px-6 lg:px-8">
                <div class="max-w-7xl mx-auto">
                    
                    <!-- Flash Notifications -->
                    @if(session('success'))
                        <div class="mb-6 flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm shadow-sm">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ session('success') }}</span>
                            </div>
                            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 flex items-center justify-between p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm shadow-sm">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ session('error') }}</span>
                            </div>
                            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-6 flex items-start justify-between p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm shadow-sm">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="mb-6 flex items-center justify-between p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-sm shadow-sm">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>{{ session('warning') }}</span>
                            </div>
                            <button type="button" onclick="this.parentElement.remove()" class="text-amber-500 hover:text-amber-700">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    @endif

                    <!-- Page Slot Content -->
                    {{ $slot }}
                </div>
            </main>

        </div>
    </div>

    {{-- Script tambahan per halaman (monitoring, form penilaian, rankings, dll.) --}}
    @stack('scripts')

    <!-- Toggle Script -->
    <script>
        function toggleSidebar() {
            const isCollapsed = document.body.classList.toggle('sidebar-collapsed');
            try {
                localStorage.setItem('fi_sidebar_collapsed', isCollapsed ? 'true' : 'false');
            } catch (e) {}
        }

        function toggleMobileSidebar() {
            const sidebar = document.getElementById('app-sidebar');
            const backdrop = document.getElementById('mobile-sidebar-backdrop');
            
            const isOpen = !sidebar.classList.contains('-translate-x-full');
            if (isOpen) {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('opacity-0');
                setTimeout(() => backdrop.classList.add('hidden'), 300);
            } else {
                backdrop.classList.remove('hidden');
                setTimeout(() => backdrop.classList.remove('opacity-0'), 10);
                sidebar.classList.remove('-translate-x-full');
            }
        }
    </script>

    <!-- Global Modal Dialogs (pengganti alert()/confirm() native) -->
    <div id="appDialogBackdrop" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs hidden items-center justify-center z-[70] p-4">
        <!-- Confirm -->
        <div id="appConfirmDialog" class="hidden w-full max-w-md bg-white rounded-2xl shadow-2xl p-6" role="dialog" aria-modal="true">
            <div class="flex items-start gap-4">
                <div id="appConfirmIcon" class="shrink-0 w-11 h-11 rounded-full flex items-center justify-center bg-red-100">
                    <svg id="appConfirmIconDanger" class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                    <svg id="appConfirmIconInfo" class="hidden w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 id="appConfirmTitle" class="text-base font-bold text-slate-900">Konfirmasi</h3>
                    <p id="appConfirmMessage" class="mt-1.5 text-sm text-slate-600 leading-relaxed"></p>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-end gap-2.5">
                <button type="button" id="appConfirmCancel" class="px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="button" id="appConfirmOk" class="px-4 py-2 text-sm font-semibold text-white bg-red-600 border border-red-600 rounded-xl hover:bg-red-700 transition-colors">
                    Ya, Lanjutkan
                </button>
            </div>
        </div>

        <!-- Alert -->
        <div id="appAlertDialog" class="hidden w-full max-w-md bg-white rounded-2xl shadow-2xl p-6" role="alertdialog" aria-modal="true">
            <div class="flex items-start gap-4">
                <div id="appAlertIcon" class="shrink-0 w-11 h-11 rounded-full flex items-center justify-center bg-amber-100">
                    <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 id="appAlertTitle" class="text-base font-bold text-slate-900">Perhatian</h3>
                    <p id="appAlertMessage" class="mt-1.5 text-sm text-slate-600 leading-relaxed"></p>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-end">
                <button type="button" id="appAlertOk" class="px-4 py-2 text-sm font-semibold text-white bg-slate-900 border border-slate-900 rounded-xl hover:bg-slate-800 transition-colors">
                    Mengerti
                </button>
            </div>
        </div>
    </div>

    <script>
        // Dialog modal global berbasis Promise — pengganti alert()/confirm() native.
        (function () {
            const backdrop = document.getElementById('appDialogBackdrop');

            function show(el) {
                backdrop.classList.remove('hidden');
                backdrop.classList.add('flex');
                el.classList.remove('hidden');
            }

            function hide(el) {
                el.classList.add('hidden');
                backdrop.classList.add('hidden');
                backdrop.classList.remove('flex');
            }

            let activeConfirm = null; // { resolve }

            window.appConfirm = function ({ title = 'Konfirmasi', message = '', confirmText = 'Ya, Lanjutkan', cancelText = 'Batal', danger = true }) {
                return new Promise((resolve) => {
                    if (activeConfirm) activeConfirm.resolve(false);
                    activeConfirm = { resolve };

                    document.getElementById('appConfirmTitle').textContent = title;
                    document.getElementById('appConfirmMessage').textContent = message;
                    document.getElementById('appConfirmOk').textContent = confirmText;
                    document.getElementById('appConfirmCancel').textContent = cancelText;

                    const okBtn = document.getElementById('appConfirmOk');
                    okBtn.className = danger
                        ? 'px-4 py-2 text-sm font-semibold text-white bg-red-600 border border-red-600 rounded-xl hover:bg-red-700 transition-colors'
                        : 'px-4 py-2 text-sm font-semibold text-white bg-blue-600 border border-blue-600 rounded-xl hover:bg-blue-700 transition-colors';
                    document.getElementById('appConfirmIcon').className = danger
                        ? 'shrink-0 w-11 h-11 rounded-full flex items-center justify-center bg-red-100'
                        : 'shrink-0 w-11 h-11 rounded-full flex items-center justify-center bg-blue-100';
                    document.getElementById('appConfirmIconDanger').classList.toggle('hidden', !danger);
                    document.getElementById('appConfirmIconInfo').classList.toggle('hidden', danger);

                    show(document.getElementById('appConfirmDialog'));
                    document.getElementById('appConfirmOk').focus();
                });
            };

            window.appAlert = function ({ title = 'Perhatian', message = '' }) {
                return new Promise((resolve) => {
                    document.getElementById('appAlertTitle').textContent = title;
                    document.getElementById('appAlertMessage').textContent = message;
                    show(document.getElementById('appAlertDialog'));
                    document.getElementById('appAlertOk').focus();
                    document.getElementById('appAlertOk').onclick = () => {
                        hide(document.getElementById('appAlertDialog'));
                        resolve();
                    };
                });
            };

            function settleConfirm(result) {
                if (!activeConfirm) return;
                hide(document.getElementById('appConfirmDialog'));
                activeConfirm.resolve(result);
                activeConfirm = null;
            }

            document.getElementById('appConfirmOk').addEventListener('click', () => settleConfirm(true));
            document.getElementById('appConfirmCancel').addEventListener('click', () => settleConfirm(false));
            backdrop.addEventListener('click', (e) => { if (e.target === backdrop) settleConfirm(false); });
            document.addEventListener('keydown', (e) => {
                if (e.key !== 'Escape') return;
                if (activeConfirm) settleConfirm(false);
                const alertBox = document.getElementById('appAlertDialog');
                if (!alertBox.classList.contains('hidden')) {
                    hide(alertBox);
                }
            });

            // Helper untuk form: onsubmit="return confirmSubmit(event, 'pesan')"
            window.confirmSubmit = function (event, message, options = {}) {
                event.preventDefault();
                const form = event.target.closest('form');
                appConfirm(Object.assign({ message }, options)).then((ok) => {
                    if (ok && form) form.submit(); // submit() native tidak memicu handler lagi
                });
                return false;
            };
        })();
    </script>
</body>
</html>
