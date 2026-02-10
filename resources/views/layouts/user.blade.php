<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'User Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans antialiased">

    <div class="flex h-screen">
        <aside class="w-64 bg-white border-r border-gray-200 flex flex-col">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center">
                        <span class="text-white font-semibold">{{ substr(Auth::user()->username ?? 'S', 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Aplikasi SPP</p>
                        <p class="text-sm text-gray-500">Siswa Dashboard</p>
                    </div>
                </div>
            </div>
            
            <nav class="flex-1 px-4 py-4 space-y-1">
                <a href="{{ route('user.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('user.dashboard') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-home w-5 h-5 mr-3 text-center"></i> Dashboard
                </a>
                
                <a href="{{ route('user.riwayat.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('user.riwayat.*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-history w-5 h-5 mr-3 text-center"></i> Riwayat Transaksi
                </a>

                <a href="{{ route('user.chat.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('user.chat.*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-comments w-5 h-5 mr-3 text-center"></i> Chat Admin
                </a>
            </nav>

            <div class="p-4 border-t border-gray-200">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="flex items-center w-full px-4 py-3 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors">
                        <i class="fas fa-sign-out-alt w-5 h-5 mr-3 text-center"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm p-4 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">@yield('header', 'Dashboard')</h2>
                <div class="flex items-center">
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full uppercase">
                        {{ Auth::user()->role === 'user' ? 'SISWA' : strtoupper(Auth::user()->role) }}
                    </span>
                </div>
            </header>

            <div class="p-6 overflow-y-auto space-y-6">
                @yield('content')
            </div>
        </main>
    </div>

</body>
</html>
