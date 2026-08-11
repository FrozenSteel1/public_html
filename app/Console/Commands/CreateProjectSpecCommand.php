<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateProjectSpecCommand extends Command
{
    protected $signature = 'project:spec
                            {--full : Генерация полной спецификации с деталями моделей и БД}
                            {--output=PROJECT_SPECIFICATION.md : Имя выходного файла}';

    protected $description = 'Генерация подробной спецификации проекта в формате Markdown';

    protected array $includeDirs = [
        'app', 'bootstrap', 'config', 'database', 'lang',
        'public', 'resources', 'routes', 'storage', 'tests'
    ];

    protected array $excludeDirs = [
        'vendor', 'node_modules', '.git', 'storage/framework',
        'storage/logs', 'bootstrap/cache'
    ];

    public function handle(): int
    {
        $this->info('🔍 Начинаю генерацию спецификации проекта...');

        $basePath = base_path();
        $outputFile = $basePath . '/' . $this->option('output');

        $markdown = $this->generateSpecification($basePath);

        File::put($outputFile, $markdown);
        $this->info("✅ Подробная MD-спецификация создана: {$outputFile}");
        $this->info("📊 Размер файла: " . $this->formatBytes(strlen($markdown)));

        return Command::SUCCESS;
    }

    private function generateSpecification(string $basePath): string
    {
        $markdown = "# 📋 Спецификация проекта: " . basename($basePath) . "\n\n";
        $markdown .= "> **Дата генерации:** " . now()->toDateTimeString() . "\n\n";
        $markdown .= "---\n\n";

        $markdown .= $this->getGeneralInfo();
        $markdown .= $this->getStatistics();
        $markdown .= $this->getEnvironmentInfo();
        $markdown .= $this->getRoutesInfo();
        $markdown .= $this->getDirectoryStructure($basePath);
        $markdown .= $this->getModelsInfo();
        $markdown .= $this->getControllersInfo();
        $markdown .= $this->getMiddlewareInfo();
        $markdown .= $this->getProvidersInfo();
        $markdown .= $this->getConfigInfo();
        $markdown .= $this->getComposerInfo();

        if ($this->option('full')) {
            $markdown .= $this->getDatabaseInfo();
        }

        $markdown .= $this->getPoliciesInfo();
        $markdown .= $this->getEventsInfo();
        $markdown .= $this->getScheduledTasks();
        $markdown .= $this->getTestsInfo();
        $markdown .= $this->getCodeAnalysis();

        return $markdown;
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    private function getGeneralInfo(): string
    {
        $markdown = "## 🏗️ Общая информация\n\n";
        $markdown .= "- **Название проекта:** " . config('app.name', 'Не указано') . "\n";
        $markdown .= "- **Версия Laravel:** " . app()->version() . "\n";
        $markdown .= "- **Версия PHP:** " . phpversion() . "\n";
        $markdown .= "- **Окружение:** " . config('app.env') . "\n";
        $markdown .= "- **Режим отладки:** " . (config('app.debug') ? '✅ Включен' : '❌ Выключен') . "\n";
        $markdown .= "- **URL приложения:** " . config('app.url') . "\n\n";
        return $markdown;
    }

    private function getStatistics(): string
    {
        $markdown = "## 📊 Сводная статистика\n\n";

        try {
            $routes = Route::getRoutes();
            $controllers = File::allFiles(app_path('Http/Controllers'));
            $models = File::allFiles(app_path('Models'));
            $migrations = File::allFiles(database_path('migrations'));
            $views = File::allFiles(resource_path('views'));
            $tests = File::allFiles(base_path('tests'));

            $markdown .= "| Показатель | Значение |\n";
            $markdown .= "| :--- | :--- |\n";
            $markdown .= "| **Всего роутов** | " . count($routes) . " |\n";
            $markdown .= "| **Контроллеры** | " . count($controllers) . " |\n";
            $markdown .= "| **Модели** | " . count($models) . " |\n";
            $markdown .= "| **Миграции** | " . count($migrations) . " |\n";
            $markdown .= "| **Шаблоны (views)** | " . count($views) . " |\n";
            $markdown .= "| **Тесты** | " . count($tests) . " |\n\n";

        } catch (\Exception $e) {
            $markdown .= "_Не удалось получить полную статистику: " . $e->getMessage() . "_\n\n";
        }

        return $markdown;
    }

    private function getEnvironmentInfo(): string
    {
        $markdown = "## 🌍 Информация о среде\n\n";
        $markdown .= "- **Веб-сервер:** " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Не определено') . "\n";
        $markdown .= "- **ОС:** " . PHP_OS . "\n";
        $markdown .= "- **Версия PHP:** " . phpversion() . "\n";
        $markdown .= "- **Лимит памяти:** " . ini_get('memory_limit') . "\n";
        $markdown .= "- **Макс. время выполнения:** " . ini_get('max_execution_time') . " сек\n\n";
        return $markdown;
    }

    private function getRoutesInfo(): string
    {
        $markdown = "## 🛣️ Маршруты приложения\n\n";

        try {
            $routes = Route::getRoutes();
            $markdown .= "**Всего маршрутов:** " . count($routes) . "\n\n";

            $markdown .= "| Метод | URI | Имя | Action |\n";
            $markdown .= "| :--- | :--- | :--- | :--- |\n";

            $counter = 0;
            foreach ($routes as $route) {
                if (str_starts_with($route->uri(), '_')) continue;

                $methods = implode(' ', array_diff($route->methods(), ['HEAD']));
                $uri = '`/' . ltrim($route->uri(), '/') . '`';
                $name = $route->getName() ? '`' . $route->getName() . '`' : '—';
                $action = is_string($route->getActionName()) ? '`' . $route->getActionName() . '`' : 'Closure';

                $markdown .= "| {$methods} | {$uri} | {$name} | {$action} |\n";
                $counter++;
                if ($counter > 100) {
                    $markdown .= "| ... | *показано только 100 маршрутов* | ... | ... |\n";
                    break;
                }
            }
            $markdown .= "\n";

        } catch (\Exception $e) {
            $markdown .= "_Ошибка при получении маршрутов: " . $e->getMessage() . "_\n\n";
        }

        return $markdown;
    }

    private function getDirectoryStructure(string $basePath): string
    {
        $markdown = "## 📂 Структура каталогов и файлов\n\n";
        $markdown .= "```text\n";
        $markdown .= "📁 /\n";

        foreach ($this->includeDirs as $dir) {
            $fullPath = $basePath . '/' . $dir;
            if (File::exists($fullPath) && !$this->isExcluded($fullPath)) {
                $markdown .= "└── 📁 {$dir}/\n";
                $markdown .= $this->getDirStructure($fullPath, 2);
            }
        }

        $markdown .= "```\n\n";
        return $markdown;
    }

    private function getDirStructure(string $dir, int $level): string
    {
        $result = "";
        $indent = str_repeat("    ", $level);

        try {
            $files = File::files($dir);
            $directories = File::directories($dir);

            $directories = array_filter($directories, function($subDir) {
                return !$this->isExcluded($subDir);
            });

            sort($directories);
            sort($files);

            foreach ($directories as $subDir) {
                $dirName = basename($subDir);
                $result .= "{$indent}├── 📁 {$dirName}/\n";
                $result .= $this->getDirStructure($subDir, $level + 1);
            }

            foreach ($files as $file) {
                $result .= "{$indent}├── 📄 " . $file->getFilename() . "\n";
            }

        } catch (\Exception $e) {
            // Игнорируем ошибки
        }

        return $result;
    }

    private function isExcluded(string $path): bool
    {
        foreach ($this->excludeDirs as $exclude) {
            if (str_contains($path, $exclude)) {
                return true;
            }
        }
        return false;
    }

    private function getModelsInfo(): string
    {
        $markdown = "## 🧩 Модели (Models)\n\n";
        $modelsPath = app_path('Models');

        if (!File::exists($modelsPath)) {
            $modelsPath = app_path();
        }

        try {
            $modelFiles = File::allFiles($modelsPath);
            $models = array_filter($modelFiles, function($file) {
                return !Str::contains($file->getFilename(), ['Factory', 'Observer', 'Pivot']);
            });

            if (empty($models)) {
                $markdown .= "_Модели не найдены._\n\n";
                return $markdown;
            }

            $markdown .= "| Модель | Таблица |\n";
            $markdown .= "| :--- | :--- |\n";

            foreach ($models as $file) {
                $modelName = str_replace('.php', '', $file->getFilename());
                $className = 'App\\Models\\' . $modelName;

                if (!class_exists($className)) {
                    $className = 'App\\' . $modelName;
                    if (!class_exists($className)) {
                        continue;
                    }
                }

                try {
                    $instance = new $className();
                    $table = $instance->getTable() ?? '—';
                    $markdown .= "| `{$modelName}` | `{$table}` |\n";
                } catch (\Exception $e) {
                    $markdown .= "| `{$modelName}` | Ошибка |\n";
                }
            }
            $markdown .= "\n";

        } catch (\Exception $e) {
            $markdown .= "_Ошибка при получении моделей: " . $e->getMessage() . "_\n\n";
        }

        return $markdown;
    }

    private function getControllersInfo(): string
    {
        $markdown = "## 🎮 Контроллеры\n\n";
        $controllersPath = app_path('Http/Controllers');

        if (!File::exists($controllersPath)) {
            $markdown .= "_Папка контроллеров не найдена._\n\n";
            return $markdown;
        }

        try {
            $controllers = File::allFiles($controllersPath);

            if (empty($controllers)) {
                $markdown .= "_Контроллеры не найдены._\n\n";
                return $markdown;
            }

            $markdown .= "| Контроллер |\n";
            $markdown .= "| :--- |\n";

            foreach ($controllers as $file) {
                $markdown .= "| `" . $file->getFilenameWithoutExtension() . "` |\n";
            }
            $markdown .= "\n";

        } catch (\Exception $e) {
            $markdown .= "_Ошибка при получении контроллеров: " . $e->getMessage() . "_\n\n";
        }

        return $markdown;
    }

    private function getMiddlewareInfo(): string
    {
        $markdown = "## 🔧 Middleware\n\n";
        $middlewarePath = app_path('Http/Middleware');

        if (!File::exists($middlewarePath)) {
            $markdown .= "_Папка Middleware не найдена._\n\n";
            return $markdown;
        }

        try {
            $middlewareFiles = File::files($middlewarePath);

            if (empty($middlewareFiles)) {
                $markdown .= "_Middleware не найдены._\n\n";
                return $markdown;
            }

            foreach ($middlewareFiles as $file) {
                $name = $file->getFilenameWithoutExtension();
                $markdown .= "- `{$name}`\n";
            }
            $markdown .= "\n";

        } catch (\Exception $e) {
            $markdown .= "_Ошибка при получении middleware: " . $e->getMessage() . "_\n\n";
        }

        return $markdown;
    }

    private function getProvidersInfo(): string
    {
        $markdown = "## 📦 Провайдеры (Service Providers)\n\n";
        $providersPath = app_path('Providers');

        if (!File::exists($providersPath)) {
            $markdown .= "_Папка провайдеров не найдена._\n\n";
            return $markdown;
        }

        try {
            $providers = File::files($providersPath);

            if (empty($providers)) {
                $markdown .= "_Провайдеры не найдены._\n\n";
                return $markdown;
            }

            foreach ($providers as $file) {
                $name = $file->getFilenameWithoutExtension();
                $markdown .= "- `{$name}`\n";
            }
            $markdown .= "\n";

        } catch (\Exception $e) {
            $markdown .= "_Ошибка при получении провайдеров: " . $e->getMessage() . "_\n\n";
        }

        return $markdown;
    }

    private function getConfigInfo(): string
    {
        $markdown = "## ⚙️ Конфигурация\n\n";

        try {
            $configDir = config_path();
            $configFiles = File::files($configDir);

            $markdown .= "| Файл | Описание |\n";
            $markdown .= "| :--- | :--- |\n";

            $descriptions = [
                'app.php' => 'Основные настройки приложения',
                'auth.php' => 'Настройки аутентификации',
                'database.php' => 'Настройки базы данных',
                'cache.php' => 'Настройки кеширования',
                'filesystems.php' => 'Настройки файловой системы',
                'session.php' => 'Настройки сессий',
                'queue.php' => 'Настройки очередей',
                'mail.php' => 'Настройки почты',
            ];

            foreach ($configFiles as $file) {
                $name = $file->getFilename();
                $description = $descriptions[$name] ?? 'Конфигурационный файл';
                $markdown .= "| `{$name}` | {$description} |\n";
            }
            $markdown .= "\n";

            $markdown .= "### Ключевые настройки\n\n";
            $importantConfigs = [
                'app.debug' => config('app.debug') ? '✅ Включен' : '❌ Выключен',
                'app.url' => config('app.url'),
                'database.default' => config('database.default'),
                'cache.default' => config('cache.default'),
                'session.driver' => config('session.driver'),
                'queue.default' => config('queue.default'),
            ];

            foreach ($importantConfigs as $key => $value) {
                $markdown .= "- **{$key}:** `{$value}`\n";
            }
            $markdown .= "\n";

        } catch (\Exception $e) {
            $markdown .= "_Ошибка при получении конфигурации: " . $e->getMessage() . "_\n\n";
        }

        return $markdown;
    }

    private function getComposerInfo(): string
    {
        $markdown = "## 📦 Зависимости (Composer)\n\n";
        $composerPath = base_path('composer.json');

        if (!File::exists($composerPath)) {
            $markdown .= "_Файл composer.json не найден._\n\n";
            return $markdown;
        }

        try {
            $composer = json_decode(File::get($composerPath), true);

            if (isset($composer['require'])) {
                $markdown .= "### Основные зависимости\n\n";
                foreach ($composer['require'] as $package => $version) {
                    if ($package !== 'php') {
                        $markdown .= "- `{$package}`: `{$version}`\n";
                    }
                }
                $markdown .= "\n";
            }

            if (isset($composer['require-dev'])) {
                $markdown .= "### Зависимости для разработки\n\n";
                foreach ($composer['require-dev'] as $package => $version) {
                    $markdown .= "- `{$package}`: `{$version}`\n";
                }
                $markdown .= "\n";
            }

        } catch (\Exception $e) {
            $markdown .= "_Ошибка при чтении composer.json: " . $e->getMessage() . "_\n\n";
        }

        return $markdown;
    }

    private function getDatabaseInfo(): string
    {
        $markdown = "## 🗄️ Информация о базе данных\n\n";

        try {
            DB::connection()->getPdo();
            $database = DB::connection()->getDatabaseName();
            $markdown .= "- **Имя БД:** `{$database}`\n\n";

        } catch (\Exception $e) {
            $markdown .= "_Нет подключения к базе данных: " . $e->getMessage() . "_\n\n";
        }

        return $markdown;
    }

    private function getPoliciesInfo(): string
    {
        $markdown = "## 🛡️ Политики (Policies)\n\n";
        $policiesPath = app_path('Policies');

        if (!File::exists($policiesPath)) {
            $markdown .= "_Папка политик не найдена._\n\n";
            return $markdown;
        }

        try {
            $policies = File::files($policiesPath);

            if (empty($policies)) {
                $markdown .= "_Политики не найдены._\n\n";
                return $markdown;
            }

            foreach ($policies as $file) {
                $markdown .= "- " . $file->getFilenameWithoutExtension() . "\n";
            }
            $markdown .= "\n";

        } catch (\Exception $e) {
            $markdown .= "_Ошибка при получении политик: " . $e->getMessage() . "_\n\n";
        }

        return $markdown;
    }

    private function getEventsInfo(): string
    {
        $markdown = "## 🔔 События и слушатели\n\n";
        $eventsPath = app_path('Events');
        $listenersPath = app_path('Listeners');

        if (File::exists($eventsPath)) {
            $events = File::files($eventsPath);
            if (!empty($events)) {
                foreach ($events as $event) {
                    $markdown .= "- " . $event->getFilenameWithoutExtension() . "\n";
                }
                $markdown .= "\n";
            }
        }

        if (File::exists($listenersPath)) {
            $listeners = File::files($listenersPath);
            if (!empty($listeners)) {
                foreach ($listeners as $listener) {
                    $markdown .= "- " . $listener->getFilenameWithoutExtension() . "\n";
                }
                $markdown .= "\n";
            }
        }

        return $markdown;
    }

    private function getScheduledTasks(): string
    {
        $markdown = "## ⏰ Запланированные задачи\n\n";

        try {
            $schedulePath = app_path('Console/Kernel.php');
            if (File::exists($schedulePath)) {
                $content = File::get($schedulePath);
                preg_match_all('/schedule->([a-zA-Z]+)\(([^)]*)\)/', $content, $matches);

                if (!empty($matches[0])) {
                    foreach ($matches[0] as $task) {
                        $markdown .= "- `" . trim($task) . "`\n";
                    }
                } else {
                    $markdown .= "_Запланированные задачи не найдены._\n";
                }
            }
            $markdown .= "\n";

        } catch (\Exception $e) {
            $markdown .= "_Ошибка при анализе запланированных задач: " . $e->getMessage() . "_\n\n";
        }

        return $markdown;
    }

    private function getTestsInfo(): string
    {
        $markdown = "## 🧪 Тесты\n\n";
        $testsPath = base_path('tests');

        if (!File::exists($testsPath)) {
            $markdown .= "_Папка тестов не найдена._\n\n";
            return $markdown;
        }

        try {
            $tests = File::allFiles($testsPath);

            if (empty($tests)) {
                $markdown .= "_Тесты не найдены._\n\n";
                return $markdown;
            }

            $markdown .= "**Всего тестов:** " . count($tests) . "\n\n";

            foreach ($tests as $test) {
                $markdown .= "- " . $test->getFilenameWithoutExtension() . "\n";
            }
            $markdown .= "\n";

        } catch (\Exception $e) {
            $markdown .= "_Ошибка при получении тестов: " . $e->getMessage() . "_\n\n";
        }

        return $markdown;
    }

    private function getCodeAnalysis(): string
    {
        $markdown = "## 📈 Анализ кода\n\n";

        try {
            $appPath = app_path();
            $files = File::allFiles($appPath);

            $totalFiles = 0;
            $totalLines = 0;
            $phpFiles = array_filter($files, function($file) {
                return $file->getExtension() === 'php';
            });

            foreach ($phpFiles as $file) {
                $totalFiles++;
                $totalLines += count(file($file->getRealPath()));
            }

            $markdown .= "- **Всего PHP файлов:** " . number_format($totalFiles) . "\n";
            $markdown .= "- **Всего строк кода:** " . number_format($totalLines) . "\n";
            $markdown .= "- **Среднее строк на файл:** " . number_format($totalFiles > 0 ? $totalLines / $totalFiles : 0, 1) . "\n\n";

        } catch (\Exception $e) {
            $markdown .= "_Ошибка при анализе кода: " . $e->getMessage() . "_\n\n";
        }

        return $markdown;
    }
}
