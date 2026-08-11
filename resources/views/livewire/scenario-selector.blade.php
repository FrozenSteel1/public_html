<div>
    <div class="scenario-selection-layout">
        <section class="scenario-selection-main">

            {{-- Баннер активной игры (согласованное отклонение E) --}}
            @if ($activeGame && ! $showModal)
                <section class="closed-folder-info closed-folder-info--ready active-game-banner">
                    <span class="closed-folder-info__icon">
                        <x-game.icon name="scenario" class="closed-folder-info__svg" />
                    </span>
                    <div class="closed-folder-info__copy">
                        <h2>У вас есть активная игра</h2>
                        <strong>{{ $activeGame['scenario_name'] }}</strong>
                        <p>Сцена: {{ $activeGame['current_scene_title'] }} · Шагов: {{ $activeGame['steps'] }} · Начата: {{ $activeGame['created_at'] }}</p>
                        <button type="button" class="document-action document-action--secondary" wire:click="continueActiveGame">Продолжить</button>
                    </div>
                </section>
            @endif

            <header class="scenario-selection-heading">
                <h1>Выбор сценария</h1>
                <p>Выберите сценарий и откройте дело для управления округом</p>
            </header>

            @if ($selected)
                <article class="scenario-featured" aria-label="Выбранный сценарий: {{ $selected['name'] }}">
                    <div class="scenario-featured__cover">
                        <span class="scenario-featured__emblem" aria-hidden="true"></span>
                        <p class="scenario-featured__cover-label">
                            <span>РЕКОМЕНДОВАНО</span>
                            <span>НАЧАТЬ С ЭТОГО</span>
                            <span>СЦЕНАРИЯ</span>
                        </p>
                    </div>
                    <div class="scenario-featured__content">
                        <span class="scenario-featured__badge">☆ РЕКОМЕНДОВАНО</span>
                        <h2 class="scenario-featured__title">{{ $selected['name'] }}</h2>
                        <p class="scenario-featured__description">
                            {{ $selected['description'] ?: 'Управленческий год главы округа в условиях кризисов, общественного давления и системных ограничений.' }}
                        </p>
                        <ul class="scenario-featured__signals">
                            <li>С чего начинается сценарий?</li>
                            <li>Какая исходная проблема или вызов?</li>
                            <li>Что происходит на территории / в системе?</li>
                            <li>Кто создаёт давление или формирует повестку?</li>
                            <li>Какие сроки, рамки или ограничения есть у игрока?</li>
                            <li>Почему ситуация управленчески сложная?</li>
                        </ul>
                        <div class="scenario-metadata">
                            <span class="scenario-metadata__chip"><x-game.icon name="building" /> Муниципальный уровень</span>
                            <span class="scenario-metadata__chip"><x-game.icon name="role" /> Глава округа</span>
                            <span class="scenario-metadata__chip"><x-game.icon name="calendar" /> {{ $selected['scenes_count'] }} сцен / 12 месяцев</span>
                            <span class="scenario-metadata__chip"><x-game.icon name="calendar" /> Годовой сценарий</span>
                            <span class="scenario-metadata__chip scenario-metadata__chip--danger">
                                <x-game.icon name="chart" /> Сложность: {{ mb_strtolower($this->getDifficultyLabel($selectedDifficulties[$selected['id']] ?? 'easy')) }}
                            </span>
                        </div>
                    </div>
                </article>

                <div class="scenario-card-grid">
                    @foreach ($others as $scenario)
                        @php $available = count($scenario['presets']) > 0; @endphp
                        <button type="button"
                                class="scenario-card @unless ($available) scenario-card--unavailable @endunless"
                                wire:click="selectScenario({{ $scenario['id'] }})"
                                @disabled(! $available)
                                aria-label="{{ $available ? 'Выбрать сценарий' : 'Сценарий скоро появится' }}: {{ $scenario['name'] }}">
                            <span class="scenario-card__strip">
                                <span class="scenario-card__strip-emblem" aria-hidden="true"></span>
                                <span class="scenario-card__strip-label">{{ $available ? 'ДОСТУПНО' : 'СКОРО' }}</span>
                            </span>
                            <span class="scenario-card__body">
                                <h3>{{ $scenario['name'] }}</h3>
                                <p>{{ $scenario['description'] ?: 'Описание будет опубликовано позже.' }}</p>
                                <ul>
                                    <li><x-game.icon name="question" /> С чего начинается сценарий?</li>
                                    <li><x-game.icon name="role" /> Какая проблема уже накоплена?</li>
                                    <li><x-game.icon name="scales" /> Что требует преобразований?</li>
                                </ul>
                                <span class="scenario-card__metadata">
                                    <span><x-game.icon name="calendar" /> {{ $scenario['scenes_count'] }} сцен</span>
                                    <span @class(['scenario-metadata__chip--danger' => in_array($scenario['difficulty'], ['hard', 'expert'])])>
                                        <x-game.icon name="chart" /> {{ $this->getDifficultyLabel($scenario['difficulty']) }}
                                    </span>
                                </span>
                            </span>
                        </button>
                    @endforeach
                </div>
            @else
                <div class="technical-placeholder">
                    <h1>Сценарии не найдены</h1>
                    <p>Попробуйте изменить поисковый запрос или обратитесь к администратору.</p>
                </div>
            @endif

            <p class="scenario-selection-note">
                ⓘ Сценарии можно проходить последовательно. Ваши решения влияют на развитие округа и открывают новые возможности.
                <label class="game-search">
                    <span>Поиск сценария</span>
                    <input type="search" wire:model.live.debounce.400ms="search" placeholder="Название сценария">
                </label>
            </p>
        </section>

        <aside class="scenario-config" aria-label="Настройка сценария">
            {{-- 1. Роль (статично по макету, решение G) --}}
            <section class="scenario-config__section">
                <h2 class="scenario-config__heading">
                    <span class="scenario-config__number">1</span>Роль<span class="scenario-config__rule"></span>
                </h2>
                <div class="role-options">
                    <button type="button" class="role-option role-option--selected" aria-pressed="true">
                        <span class="role-option__icon"><x-game.icon name="role" class="role-option__svg" /></span>
                        <span class="role-option__copy">
                            <strong>Глава округа</strong>
                            <small>Ключевая роль. Принятие управленческих решений и ответственность за результат.</small>
                        </span>
                        <span class="role-option__status">✓</span>
                    </button>
                    <button type="button" class="role-option" disabled>
                        <span class="role-option__icon"><x-game.icon name="group" class="role-option__svg" /></span>
                        <span class="role-option__copy">
                            <strong>Заместитель главы</strong>
                            <small>Доступно в других сценариях</small>
                        </span>
                        <span class="role-option__status"><x-game.icon name="lock" class="role-option__lock" /></span>
                    </button>
                    <button type="button" class="role-option" disabled>
                        <span class="role-option__icon"><x-game.icon name="briefcase" class="role-option__svg" /></span>
                        <span class="role-option__copy">
                            <strong>Руководитель департамента</strong>
                            <small>Доступно в других сценариях</small>
                        </span>
                        <span class="role-option__status"><x-game.icon name="lock" class="role-option__lock" /></span>
                    </button>
                </div>
            </section>

            {{-- 2. Сложность --}}
            <section class="scenario-config__section">
                <h2 class="scenario-config__heading">
                    <span class="scenario-config__number">2</span>Сложность<span class="scenario-config__rule"></span>
                </h2>
                <div class="difficulty-options">
                    @foreach (\App\Livewire\ScenarioSelector::DIFFICULTIES as $key => $meta)
                        @php
                            $available = $selected && in_array($key, $this->getAvailableDifficulties($selected['id']), true);
                            $current = ($selectedDifficulties[$selected['id'] ?? 0] ?? null) === $key;
                        @endphp
                        <button type="button"
                                class="difficulty-option @if ($current) difficulty-option--selected @endif"
                                wire:click="selectDifficulty('{{ $key }}')"
                                @disabled(! $available)
                                aria-pressed="{{ $current ? 'true' : 'false' }}">
                            <span class="scenario-level" aria-hidden="true">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i @if ($i <= $meta['level']) class="is-active" @endif></i>
                                @endfor
                            </span>
                            <span>{{ $meta['label'] }}</span>
                            @if ($current)
                                <span class="difficulty-option__check">✓</span>
                            @endif
                        </button>
                    @endforeach
                </div>
            </section>

            {{-- 3. Паспорт сценария (собирается из данных БД, решение C) --}}
            @if ($selected)
                <section class="scenario-config__section scenario-passport">
                    <h2 class="scenario-config__heading">
                        <span class="scenario-config__number">3</span>Паспорт сценария<span class="scenario-config__rule"></span>
                    </h2>
                    <div class="scenario-passport__content">
                        <dl class="scenario-passport__list">
                            <dt>Сюжет:</dt>
                            <dd>{{ $selected['name'] }}</dd>
                            <dt>Период:</dt>
                            <dd>12 месяцев</dd>
                            <dt>Формат:</dt>
                            <dd>{{ $selected['scenes_count'] }} сцен / 1 управленческий год</dd>
                            <dt>Фокус:</dt>
                            <dd>Кризисное управление, аппарат, публичное давление, устойчивость</dd>
                            <dt>Контекст:</dt>
                            <dd>{{ $selected['description'] ?: 'Новый глава округа, ограниченные ресурсы, влияние акторов и отложенные последствия' }}</dd>
                        </dl>
                        <div class="scenario-passport__preview" aria-hidden="true">
                            <span class="scenario-passport__emblem"></span>
                            <span>СТРАТЕГИЧЕСКИЕ ШАХМАТЫ: ГОСУПРАВЛЕНИЕ</span>
                            <span>ДЕЛО ОКРУГА</span>
                        </div>
                    </div>
                </section>
            @endif

            <div class="scenario-config__actions">
                <button type="button" class="document-action document-action--secondary" wire:click="preview">
                    <x-game.icon name="eye" class="scenario-action__icon" /> Предпросмотр
                </button>
                <button type="button" class="document-action document-action--primary" wire:click="tryStartGame({{ $selected['id'] ?? 0 }})">
                    <x-game.icon name="folderOpen" class="scenario-action__icon" /> Открыть дело
                </button>
            </div>
        </aside>
    </div>

    {{-- Модальное окно активной игры (в стиле replay-dialog макета) --}}
    @if ($showModal && $activeGame)
        <div class="replay-backdrop" role="dialog" aria-modal="true" aria-labelledby="active-game-title"
             x-on:keydown.escape.window="$wire.closeModal()">
            <article class="replay-dialog">
                <header class="replay-dialog__header">
                    <x-game.icon name="question" class="replay-dialog__icon" />
                    <h2 id="active-game-title">У вас есть активная игра</h2>
                    <button type="button" class="replay-dialog__close" wire:click="closeModal" aria-label="Закрыть">×</button>
                </header>
                <p class="replay-dialog__copy">
                    Вы уже начали прохождение сценария «{{ $activeGame['scenario_name'] }}».
                    Сцена: {{ $activeGame['current_scene_title'] }}. Сложность: {{ $this->getDifficultyLabel($activeGame['difficulty'] ?? 'easy') }}.
                    Сделано шагов: {{ $activeGame['steps'] }}. Начата: {{ $activeGame['created_at'] }}. Что вы хотите сделать?
                </p>
                <footer class="replay-dialog__footer">
                    <button type="button" class="document-action document-action--secondary" wire:click="closeModal">Отмена</button>
                    <button type="button" class="document-action document-action--secondary" wire:click="startNewGame({{ $pendingScenarioId }})">Начать новую</button>
                    <button type="button" class="document-action document-action--primary replay-dialog__confirm" wire:click="continueActiveGame">Продолжить игру</button>
                </footer>
            </article>
        </div>
    @endif

    {{-- Тост (поведение макета) --}}
    @if ($toast)
        <div class="scenario-toast" role="status" x-data x-init="setTimeout(() => $wire.clearToast(), 3000)">
            {{ $toast }}
        </div>
    @endif
</div>
