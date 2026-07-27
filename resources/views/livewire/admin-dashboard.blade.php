<div class="container mx-auto px-4 py-8 max-w-7xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">⚙️ Админ-панель</h1>
        <p class="text-gray-500 text-sm">Управление системой</p>
    </div>

    <!-- Ссылки на разделы -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('admin.companies') }}" class="bg-white rounded-lg shadow p-6 text-center hover:shadow-lg transition">
            <div class="text-3xl mb-2">🏢</div>
            <div class="text-sm font-medium text-gray-700">Компании</div>
        </a>
        <a href="{{ route('admin.scenarios') }}" class="bg-white rounded-lg shadow p-6 text-center hover:shadow-lg transition">
            <div class="text-3xl mb-2">📋</div>
            <div class="text-sm font-medium text-gray-700">Сценарии</div>
        </a>
        <a href="{{ route('admin.scenes') }}" class="bg-white rounded-lg shadow p-6 text-center hover:shadow-lg transition">
            <div class="text-3xl mb-2">🎬</div>
            <div class="text-sm font-medium text-gray-700">Сцены</div>
        </a>
        <a href="{{ route('admin.events') }}" class="bg-white rounded-lg shadow p-6 text-center hover:shadow-lg transition">
            <div class="text-3xl mb-2">⚡</div>
            <div class="text-sm font-medium text-gray-700">События</div>
        </a>
        <a href="{{ route('admin.effect-types') }}" class="bg-white rounded-lg shadow p-6 text-center hover:shadow-lg transition">
            <div class="text-3xl mb-2">🎯</div>
            <div class="text-sm font-medium text-gray-700">Типы эффектов</div>
        </a>
        <a href="{{ route('admin.actors') }}" class="bg-white rounded-lg shadow p-6 text-center hover:shadow-lg transition">
            <div class="text-3xl mb-2">👤</div>
            <div class="text-sm font-medium text-gray-700">Акторы</div>
        </a>
        <a href="{{ route('admin.presets') }}" class="bg-white rounded-lg shadow p-6 text-center hover:shadow-lg transition">
            <div class="text-3xl mb-2">💾</div>
            <div class="text-sm font-medium text-gray-700">Предустановки</div>
        </a>
        <a href="{{ route('admin.games') }}" class="bg-white rounded-lg shadow p-6 text-center hover:shadow-lg transition">
            <div class="text-3xl mb-2">🎮</div>
            <div class="text-sm font-medium text-gray-700">Игры</div>
        </a>
    </div>
</div>
