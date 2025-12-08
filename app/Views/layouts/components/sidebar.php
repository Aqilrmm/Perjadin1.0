<aside class="w-64 bg-white border-r hidden md:block">
    <div class="p-4 border-b flex items-center gap-2">
        <img src="/assets/images/logo.png" alt="logo" class="h-8">
        <div class="text-lg font-semibold">Perjadin</div>
    </div>
    <div class="p-3">
        <div class="mb-4 flex items-center gap-3">
            <img src="/assets/images/default-avatar.png" class="w-10 h-10 rounded-full" alt="avatar">
            <div>
                <div class="font-semibold"><?= esc(session('nama') ?? 'Guest') ?></div>
                <div class="text-xs text-gray-500"><?= esc(session('role') ?? '') ?></div>
            </div>
        </div>
        <nav>
            <a href="/" class="block py-2 px-3 rounded hover:bg-gray-100"><i class="fa fa-home mr-2"></i> Dashboard</a>
            <a href="/sppd" class="block py-2 px-3 rounded hover:bg-gray-100"><i class="fa fa-plane mr-2"></i> SPPD</a>
            <a href="/notifications" class="block py-2 px-3 rounded hover:bg-gray-100"><i class="fa fa-bell mr-2"></i> Notifikasi <span class="ml-2 text-sm bg-red-500 text-white px-2 rounded">3</span></a>
        </nav>
        <div class="mt-6">
            <form method="post" action="/auth/logout">
                <?= csrf_field() ?>
                <button class="w-full bg-red-600 text-white py-2 rounded"><i class="fa fa-sign-out-alt mr-2"></i> Logout</button>
            </form>
        </div>
    </div>
</aside>