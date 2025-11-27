<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

<div class="flex h-screen">

    {{-- Sidebar --}}
    <aside class="w-64 bg-white shadow-md border-r hidden md:block">
        <div class="p-4 border-b">
            <h1 class="text-xl font-bold text-gray-800">Admin Panel</h1>
        </div>

        <nav class="mt-4 px-4 space-y-2">

            <a href="{{ route('admin.dashboard') }}"
               class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                Dashboard
            </a>

            <a href="#"
               class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                Kelola User
            </a>

            <a href="{{ route('admin.penjual.index') }}"
                class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                Verifikasi Penjual
            </a>


            <a href="#"
               class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                Kelola Barang
            </a>

            <form action="/logout" method="POST">
                @csrf
                <button
                    class="w-full text-left px-3 py-2 rounded-lg text-red-600 hover:bg-red-50 mt-4">
                    Logout
                </button>
            </form>

        </nav>
    </aside>

    {{-- Mobile sidebar button --}}
    <div class="md:hidden fixed top-4 left-4 z-50">
        <button id="openSidebar" class="p-2 bg-white shadow rounded">
            ☰
        </button>
    </div>

    {{-- Mobile sidebar --}}
    <aside id="mobileSidebar"
           class="fixed inset-0 bg-black bg-opacity-40 hidden z-40">
        <div class="w-64 bg-white h-full shadow-md p-4">
            <button id="closeSidebar" class="mb-4 text-gray-600">✕</button>

            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}"
                   class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                    Dashboard
                </a>

                <a href="#"
                   class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                    Kelola User
                </a>

                <a href="{{ route('admin.penjual.index') }}"
                    class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                        Verifikasi Penjual
                </a>


                <a href="#"
                   class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                    Kelola Barang
                </a>
            </nav>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="flex-1 overflow-y-auto p-6">
        @yield('content')
    </main>

</div>

<script>
    // Mobile sidebar toggle
    const openSidebar = document.getElementById('openSidebar');
    const closeSidebar = document.getElementById('closeSidebar');
    const mobileSidebar = document.getElementById('mobileSidebar');

    openSidebar && openSidebar.addEventListener('click', () => {
        mobileSidebar.classList.remove('hidden');
    });

    closeSidebar && closeSidebar.addEventListener('click', () => {
        mobileSidebar.classList.add('hidden');
    });

    mobileSidebar && mobileSidebar.addEventListener('click', (e) => {
        if (e.target === mobileSidebar) {
            mobileSidebar.classList.add('hidden');
        }
    });
</script>

</body>
</html>
