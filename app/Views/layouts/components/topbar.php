<header class="bg-white border-b p-4 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <button id="hamburger" class="md:hidden px-2 py-1 border rounded">☰</button>
        <div class="text-sm text-gray-600"><?= $breadcrumb ?? '' ?></div>
    </div>
    <div class="flex items-center gap-3">
        <div class="relative">
            <button class="px-3 py-1 rounded hover:bg-gray-100"><i class="fa fa-search"></i></button>
        </div>
        <div class="relative">
            <button class="px-3 py-1 rounded hover:bg-gray-100"><i class="fa fa-bell"></i></button>
        </div>
        <div class="relative">
            <div class="dropdown">
                <button class="flex items-center gap-2"><img src="/assets/images/default-avatar.png" class="w-8 h-8 rounded-full"> <span><?= esc(session('nama') ?? 'Guest') ?></span></button>
            </div>
        </div>
    </div>
</header>