<div>
    <div class="app-workspace app-workspace--results app-workspace--{{ $annualTab }}">
        <section class="app-workspace__main">
            <div class="dossier dossier--results">
                <div class="dossier__cover" aria-hidden="true">
                    <div class="dossier__cover-branding">
                        <div class="dossier__emblem-placeholder" aria-hidden="true">
                            <x-game.icon name="emblem" class="dossier__emblem-fallback" />
                            <span class="dossier__emblem-asset"></span>
                        </div>
                        <p class="dossier__folder-title">
                            <span>АДМИНИСТРАЦИЯ</span>
                            <span>МУНИЦИПАЛЬНОГО ОКРУГА</span>
                        </p>
                    </div>
                </div>

                <section class="dossier__document-area" aria-label="Область документа">
                    {{-- ========== ЭКРАН 14: ИТОГИ ГОДА ========== --}}
                    @if($annualTab === 'year-results')
                        @php $results = $this->getAnnualResults(); @endphp
                        <article class="document-sheet document-sheet--compact document-sheet--year-results">
                            <header class="document-sheet__header annual-header">
                                <x-game.icon name="emblem" class="document-sheet__emblem" />
                                <p class="annual-header__completion">{{ $results['completionStatus'] }} · {{ $totalSteps }} сцен · {{ $results['period'] }}</p>
                                <h1 class="document-sheet__title">Итоги года</h1>
                                <p class="document-sheet__subtitle">{{ $results['subtitle'] }}</p>
                                <div class="document-sheet__divider" aria-hidden="true"><span></span></div>
                            </header>
                            <div class="document-sheet__body annual-document__body" tabindex="0" role="region" aria-label="Содержимое годового отчёта">
                                <section class="annual-portrait">
                                    <h2>ОБЩИЙ ПОРТРЕТ ОКРУГА</h2>
                                    <div class="annual-portrait__columns">
                                        <div>
                                            @foreach(array_slice($results['portrait'], 0, 3) as $paragraph)
                                                <p>{{ $paragraph }}</p>
                                            @endforeach
                                        </div>
                                        <div>
                                            @foreach(array_slice($results['portrait'], 3) as $paragraph)
                                                <p>{{ $paragraph }}</p>
                                            @endforeach
                                        </div>
                                    </div>
                                </section>

                                <div class="annual-outcomes">
                                    <section class="annual-outcome annual-outcome--strengthened">
                                        <header>
                                            <x-game.icon name="scales" class="annual-outcome__icon" />
                                            <h2>УКРЕПИЛОСЬ</h2>
                                        </header>
                                        <ul>
                                            @foreach($results['strengthened'] as $item)<li>{{ $item }}</li>@endforeach
                                        </ul>
                                    </section>
                                    <section class="annual-outcome annual-outcome--weakened">
                                        <header>
                                            <x-game.icon name="chart" class="annual-outcome__icon" />
                                            <h2>ОСЛАБЛО</h2>
                                        </header>
                                        <ul>
                                            @foreach($results['weakened'] as $item)<li>{{ $item }}</li>@endforeach
                                        </ul>
                                    </section>
                                    <section class="annual-outcome annual-outcome--vulnerable">
                                        <header>
                                            <x-game.icon name="question" class="annual-outcome__icon" />
                                            <h2>ОСТАЛОСЬ УЯЗВИМЫМ</h2>
                                        </header>
                                        <ul>
                                            @foreach($results['vulnerable'] as $item)<li>{{ $item }}</li>@endforeach
                                        </ul>
                                    </section>
                                </div>

                                <section class="annual-balance">
                                    <h2>БАЛАНС ВЛИЯНИЯ АКТОРОВ</h2>
                                    <div class="annual-actors">
                                        @foreach($results['actors'] as $actor)
                                            <article class="annual-actor">
                                                <x-game.icon name="{{ $actor['icon'] }}" class="annual-actor__icon" />
                                                <div>
                                                    <h3>{{ $actor['name'] }}</h3>
                                                    <p>{{ $actor['summary'] }}</p>
                                                </div>
                                            </article>
                                        @endforeach
                                    </div>
                                </section>

                                <section class="annual-final">
                                    <x-game.icon name="emblem" class="annual-final__icon" />
                                    <div>
                                        <h2>ФИНАЛЬНОЕ СОСТОЯНИЕ ОКРУГА</h2>
                                        <h3>{{ $results['title'] }}</h3>
                                        @foreach($results['finalDescription'] as $paragraph)
                                            <p>{{ $paragraph }}</p>
                                        @endforeach
                                    </div>
                                </section>
                            </div>
                            <footer class="document-sheet__actions annual-actions">
                                <button type="button" class="document-action document-action--primary" wire:click="setAnnualTab('management-review')">Перейти к разбору управления</button>
                                <button type="button" class="document-action document-action--text" wire:click="setAnnualTab('year-chronicle')">Подробная хроника года ›</button>
                            </footer>
                        </article>

                        {{-- ========== ЭКРАН 15: РАЗБОР УПРАВЛЕНИЯ ========== --}}
                    @elseif($annualTab === 'management-review')
                        @php $review = $this->getManagementReview(); @endphp
                        <article class="document-sheet document-sheet--compact document-sheet--management-review">
                            <header class="document-sheet__header review-header">
                                <x-game.icon name="emblem" class="document-sheet__emblem" />
                                <p class="review-header__context">{{ $review['context'] }}</p>
                                <h1 class="document-sheet__title">Разбор управления</h1>
                                <p class="document-sheet__subtitle">Управленческий профиль и ключевые решения года</p>
                                <div class="document-sheet__divider" aria-hidden="true"><span></span></div>
                            </header>
                            <div class="document-sheet__body management-review__body" tabindex="0" role="region" aria-label="Содержимое годового отчёта">
                                <section class="review-profile">
                                    <h2>УПРАВЛЕНЧЕСКИЙ ПРОФИЛЬ</h2>
                                    <h3>{{ $review['profileTitle'] }}</h3>
                                    <div class="review-profile__narrative">
                                        @foreach($review['profileNarrative'] as $paragraph)
                                            <p>{{ $paragraph }}</p>
                                        @endforeach
                                    </div>
                                    <ul class="review-profile__markers">
                                        @foreach($review['markers'] as $marker)<li>{{ $marker }}</li>@endforeach
                                    </ul>
                                </section>

                                <div class="review-qualities">
                                    <section class="review-quality review-quality--strength">
                                        <h2>СИЛЬНЫЕ СТОРОНЫ</h2>
                                        <div class="review-card-list review-card-list--strength">
                                            @foreach($review['strengths'] as $card)
                                                <article class="review-card review-card--strength">
                                                    <h3>{{ $card['title'] }}</h3>
                                                    <p>{{ $card['description'] }}</p>
                                                </article>
                                            @endforeach
                                        </div>
                                    </section>
                                    <section class="review-quality review-quality--risk">
                                        <h2>СИСТЕМНЫЕ РИСКИ</h2>
                                        <div class="review-card-list review-card-list--risk">
                                            @foreach($review['risks'] as $card)
                                                <article class="review-card review-card--risk">
                                                    <h3>{{ $card['title'] }}</h3>
                                                    <p>{{ $card['description'] }}</p>
                                                </article>
                                            @endforeach
                                        </div>
                                    </section>
                                </div>

                                <section class="review-turning">
                                    <h2>ПОВОРОТНЫЕ РЕШЕНИЯ ГОДА</h2>
                                    <div class="review-turning__grid">
                                        @foreach($review['turningPoints'] as $point)
                                            <article class="turning-card">
                                                <p class="turning-card__month">{{ $point['month'] }}</p>
                                                <h3>{{ $point['situation'] }}</h3>
                                                <dl>
                                                    <div><dt>Выбранное решение</dt><dd>{{ $point['decision'] }}</dd></div>
                                                    <div><dt>Наблюдаемый результат</dt><dd>{{ $point['result'] }}</dd></div>
                                                    <div><dt>Управленческое последствие</dt><dd>{{ $point['consequence'] }}</dd></div>
                                                </dl>
                                                <button type="button" class="turning-card__action" wire:click="openChronicleFromReview('{{ $point['id'] }}')">Открыть в хронике ›</button>
                                            </article>
                                        @endforeach
                                    </div>
                                </section>

                                <section class="review-pattern">
                                    <x-game.icon name="compass" class="review-pattern__icon" />
                                    <div>
                                        <h2>КЛЮЧЕВОЙ ПАТТЕРН УПРАВЛЕНИЯ</h2>
                                        @foreach($review['keyPattern'] as $paragraph)
                                            <p>{{ $paragraph }}</p>
                                        @endforeach
                                    </div>
                                </section>

                                <section class="review-recommendations">
                                    <h2>ПЕРСОНАЛЬНЫЕ РЕКОМЕНДАЦИИ</h2>
                                    <div class="review-card-list review-card-list--recommendation">
                                        @foreach($review['recommendations'] as $card)
                                            <article class="review-card review-card--recommendation">
                                                <h3>{{ $card['title'] }}</h3>
                                                <p>{{ $card['description'] }}</p>
                                            </article>
                                        @endforeach
                                    </div>
                                </section>

                                <section class="review-priorities">
                                    <h2>ПРИОРИТЕТЫ НА СЛЕДУЮЩИЙ ГОД</h2>
                                    <ul class="review-priorities__list">
                                        @foreach($review['priorities'] as $priority)<li>{{ $priority }}</li>@endforeach
                                    </ul>
                                    <p>{{ $review['prioritySummary'] }}</p>
                                </section>
                            </div>
                            <footer class="document-sheet__actions review-actions">
                                <button type="button" class="document-action document-action--primary" wire:click="setAnnualTab('year-chronicle')">Открыть подробную хронику</button>
                                <button type="button" class="document-action document-action--secondary" wire:click="setAnnualTab('year-results')">Вернуться к итогам года</button>
                                <button type="button" class="document-action document-action--text" wire:click="openReplayConfirmation">Пройти сценарий снова</button>
                            </footer>
                        </article>

                        {{-- ========== ЭКРАН 16: ХРОНИКА ГОДА ========== --}}
                    @else
                        @php
                            $chronicle = $this->getChronicle();
                            $entries = $this->getVisibleChronicleEntries();
                            $summary = $this->getChronicleSummary();
                            $turningMonths = collect($chronicle['entries'])->filter(fn ($e) => $e['turningPoint'])->pluck('month');
                        @endphp
                        <article class="document-sheet document-sheet--compact document-sheet--year-chronicle">
                            <header class="document-sheet__header chronicle-header">
                                <x-game.icon name="emblem" class="document-sheet__emblem" />
                                <p class="chronicle-header__context">Сценарий «{{ $scenarioName }}» · Глава округа · {{ $totalSteps }} месяцев</p>
                                <h1 class="document-sheet__title">Хроника года</h1>
                                <p class="document-sheet__subtitle">Подробная история решений и последствий</p>
                                <p class="chronicle-header__support">Проследите, как управленческие решения в течение года влияли на аппарат, жителей, подрядчиков, публичную среду и устойчивость округа.</p>
                                <div class="document-sheet__divider" aria-hidden="true"><span></span></div>
                            </header>
                            <div class="document-sheet__body chronicle-document__body" tabindex="0" role="region" aria-label="Содержимое годовой хроники">
                                <section class="chronicle-summary">
                                    <h2>Год в решениях</h2>
                                    <dl>
                                        <div><dd>{{ $summary['scenes'] }}</dd><dt>Сцен</dt></div>
                                        <div><dd>{{ $summary['decisions'] }}</dd><dt>Решений</dt></div>
                                        <div><dd>{{ $summary['turning'] }}</dd><dt>Поворотных</dt></div>
                                        <div><dd>{{ $summary['delayed'] }}</dd><dt>Отложенных</dt></div>
                                        <div><dd>{{ $summary['shown'] }}</dd><dt>Показано</dt></div>
                                    </dl>
                                    <p><strong>Итоговое состояние:</strong> {{ $summary['finalState'] }}</p>
                                </section>

                                <section class="chronicle-toolbar" aria-label="Фильтры хроники">
                                    <h2 class="visually-hidden">Фильтры хроники</h2>
                                    <label class="chronicle-toolbar__field">
                                        <span>Вид</span>
                                        <select wire:model.live="chronicleFilter">
                                            <option value="all">Все месяцы</option>
                                            <option value="turning">Только поворотные</option>
                                            <option value="delayed">Только с отложенными последствиями</option>
                                        </select>
                                    </label>
                                    <label class="chronicle-toolbar__field">
                                        <span>Актор</span>
                                        <select wire:model.live="chronicleActorFilter">
                                            <option value="all">Все акторы</option>
                                            <option value="Аппарат">Аппарат</option>
                                            <option value="Жители">Жители</option>
                                            <option value="Подрядчики">Подрядчики</option>
                                            <option value="СМИ">СМИ</option>
                                            <option value="Регион">Регион</option>
                                        </select>
                                    </label>
                                    <label class="chronicle-toolbar__field">
                                        <span>Порядок</span>
                                        <select wire:model.live="chronicleSort">
                                            <option value="chronological">Январь → Декабрь</option>
                                            <option value="reverse">Декабрь → Январь</option>
                                        </select>
                                    </label>
                                </section>

                                <ol class="chronicle-timeline">
                                    @forelse($entries as $entry)
                                        <li class="chronicle-timeline__item">
                                            <article id="chronicle-{{ $entry['id'] }}"
                                                     data-chronicle-entry-id="{{ $entry['id'] }}"
                                                     class="chronicle-entry @if($entry['turningPoint']) chronicle-entry--turning @endif @if($highlightedChronicleId === $entry['id']) chronicle-entry--highlighted @endif">
                                                <span class="chronicle-entry__marker" aria-hidden="true"></span>
                                                <header class="chronicle-entry__header">
                                                    <div>
                                                        <p class="chronicle-entry__meta">{{ $entry['month'] }} · Сцена {{ $entry['scene'] }}</p>
                                                        <h2>{{ $entry['title'] }}</h2>
                                                    </div>
                                                    <div class="chronicle-entry__badges">
                                                        @if($entry['turningPoint'])<span class="chronicle-badge chronicle-badge--turning">Поворотное решение</span>@endif
                                                        @if($entry['hasDelayedEffect'])<span class="chronicle-badge chronicle-badge--delayed">Отложенное последствие</span>@endif
                                                    </div>
                                                </header>
                                                <div class="chronicle-entry__summary">
                                                    <p><strong>Решение:</strong> {{ $entry['decision'] }}</p>
                                                    <p><strong>Результат:</strong> {{ $entry['result'] }}</p>
                                                </div>
                                                <button type="button" class="chronicle-entry__toggle"
                                                        wire:click="toggleChronicleEntry('{{ $entry['id'] }}')"
                                                        aria-expanded="{{ $this->isChronicleExpanded($entry['id']) ? 'true' : 'false' }}"
                                                        aria-controls="chronicle-details-{{ $entry['id'] }}">
                                                    {{ $this->isChronicleExpanded($entry['id']) ? 'Свернуть' : 'Развернуть' }}
                                                </button>
                                                <div id="chronicle-details-{{ $entry['id'] }}" class="chronicle-entry__details" @unless($this->isChronicleExpanded($entry['id'])) hidden @endunless>
                                                    <section class="chronicle-detail chronicle-detail--standard">
                                                        <h3>Ситуация</h3>
                                                        <p>{{ $entry['situation'] }}</p>
                                                    </section>
                                                    <section class="chronicle-detail chronicle-detail--standard">
                                                        <h3>Немедленный результат</h3>
                                                        <p>{{ $entry['result'] }}</p>
                                                    </section>
                                                    @if($entry['delayed'])
                                                        <section class="chronicle-detail chronicle-detail--standard">
                                                            <h3>Отложенное последствие</h3>
                                                            <p>{{ $entry['delayed'] }}</p>
                                                        </section>
                                                    @endif
                                                    @if($entry['related'])
                                                        <section class="chronicle-detail chronicle-detail--standard">
                                                            <h3>Связанное будущее событие</h3>
                                                            <p>{{ $entry['related'] }}</p>
                                                            @if($entry['relatedId'])
                                                                <button type="button" class="chronicle-detail__link" wire:click="goToLinkedEntry('{{ $entry['relatedId'] }}')">Перейти к связанному событию</button>
                                                            @endif
                                                        </section>
                                                    @endif
                                                    <section class="chronicle-detail chronicle-detail--recommendation">
                                                        <h3>Рекомендация</h3>
                                                        <p>{{ $entry['recommendation'] }}</p>
                                                    </section>
                                                    <div class="chronicle-entry__actors">
                                                        <strong>Участники</strong>
                                                        <ul>
                                                            @foreach($entry['actors'] as $actor)<li>{{ $actor }}</li>@endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </article>
                                        </li>
                                    @empty
                                        <li class="chronicle-empty">
                                            <p>События по выбранному фильтру отсутствуют.</p>
                                            <button type="button" class="document-action document-action--secondary" wire:click="resetChronicleFilters">Сбросить фильтры</button>
                                        </li>
                                    @endforelse
                                </ol>
                            </div>
                            <footer class="document-sheet__actions chronicle-actions">
                                <button type="button" class="document-action document-action--primary" wire:click="setAnnualTab('management-review')">Вернуться к разбору управления</button>
                                <button type="button" class="document-action document-action--secondary" wire:click="setAnnualTab('year-results')">Вернуться к итогам года</button>
                                <button type="button" class="document-action document-action--text" wire:click="openReplayConfirmation">Пройти сценарий снова</button>
                            </footer>
                        </article>
                    @endif
                </section>

                <div class="dossier__tabs-rail">
                    <nav class="folder-tabs folder-tabs--annual" aria-label="Годовые разделы папки">
                        <ul class="folder-tabs__list">
                            <li class="folder-tabs__item">
                                <button type="button" class="folder-tabs__button" disabled>
                                    <span class="folder-tabs__label">Сценарий</span>
                                </button>
                            </li>
                            <li class="folder-tabs__item">
                                <button type="button" class="folder-tabs__button" wire:click="setAnnualTab('year-results')" @if($annualTab === 'year-results') aria-current="page" @endif>
                                    <span class="folder-tabs__label">Итоги года</span>
                                </button>
                            </li>
                            <li class="folder-tabs__item">
                                <button type="button" class="folder-tabs__button" wire:click="setAnnualTab('management-review')" @if($annualTab === 'management-review') aria-current="page" @endif>
                                    <span class="folder-tabs__label">Разбор управления</span>
                                </button>
                            </li>
                            <li class="folder-tabs__item">
                                <button type="button" class="folder-tabs__button" wire:click="setAnnualTab('year-chronicle')" @if($annualTab === 'year-chronicle') aria-current="page" @endif>
                                    <span class="folder-tabs__label">Хроника года</span>
                                </button>
                            </li>
                            <li class="folder-tabs__item">
                                <button type="button" class="folder-tabs__button" disabled>
                                    <span class="folder-tabs__label">Приложения</span>
                                </button>
                            </li>
                        </ul>
                    </nav>
                </div>

                <div class="dossier__clasp" aria-hidden="true"></div>
            </div>
        </section>

        <aside class="app-workspace__sidebar" aria-label="@if($annualTab === 'year-results') Итоговая информация @elseif($annualTab === 'management-review') Краткий разбор управления @else Навигация по годовой хронике @endif">
            {{-- Сайдбар: итоги года --}}
            @if($annualTab === 'year-results')
                @php $results = $this->getAnnualResults(); @endphp
                <div class="sidebar sidebar--results">
                    <section class="sidebar-panel">
                        <header class="sidebar-panel__header">
                            <x-game.icon name="flag" class="sidebar-panel__icon" />
                            <h2 class="sidebar-panel__title">Статус сценария</h2>
                        </header>
                        <div class="sidebar-panel__body">
                            <p class="results-completion__value">{{ $results['completionStatus'] }}</p>
                            <p class="results-completion__supporting">Пройдено {{ $totalSteps }} сцен.</p>
                        </div>
                    </section>
                    <section class="sidebar-panel">
                        <header class="sidebar-panel__header">
                            <x-game.icon name="group" class="sidebar-panel__icon" />
                            <h2 class="sidebar-panel__title">Портрет года</h2>
                        </header>
                        <div class="sidebar-panel__body">
                            <ul class="results-list">
                                @foreach($results['sidebar']['portrait'] as $item)<li>{{ $item }}</li>@endforeach
                            </ul>
                        </div>
                    </section>
                    <section class="sidebar-panel">
                        <header class="sidebar-panel__header">
                            <x-game.icon name="building" class="sidebar-panel__icon" />
                            <h2 class="sidebar-panel__title">Ключевые последствия</h2>
                        </header>
                        <div class="sidebar-panel__body">
                            <ul class="results-list">
                                @foreach($results['sidebar']['consequences'] as $item)<li>{{ $item }}</li>@endforeach
                            </ul>
                        </div>
                    </section>
                    <section class="sidebar-panel">
                        <header class="sidebar-panel__header">
                            <x-game.icon name="compass" class="sidebar-panel__icon" />
                            <h2 class="sidebar-panel__title">Следующий шаг</h2>
                        </header>
                        <div class="sidebar-panel__body">
                            <p class="results-summary">{{ $results['sidebar']['nextStep'] }}</p>
                        </div>
                    </section>
                </div>

                {{-- Сайдбар: разбор управления --}}
            @elseif($annualTab === 'management-review')
                @php $reviewSidebar = $this->getManagementReview()['sidebar']; @endphp
                <div class="app-sidebar app-sidebar--review">
                    <section class="sidebar-panel">
                        <header class="sidebar-panel__header">
                            <x-game.icon name="group" class="sidebar-panel__icon" />
                            <h2 class="sidebar-panel__title">Профиль управления</h2>
                        </header>
                        <div class="sidebar-panel__body">
                            <p class="review-sidebar__value">{{ $reviewSidebar['profile']['title'] }}</p>
                            <ul class="review-sidebar__list">
                                @foreach($reviewSidebar['profile']['items'] as $item)<li>{{ $item }}</li>@endforeach
                            </ul>
                        </div>
                    </section>
                    <section class="sidebar-panel">
                        <header class="sidebar-panel__header">
                            <x-game.icon name="scales" class="sidebar-panel__icon" />
                            <h2 class="sidebar-panel__title">Сильная сторона</h2>
                        </header>
                        <div class="sidebar-panel__body">
                            <p class="review-sidebar__value">{{ $reviewSidebar['strength']['title'] }}</p>
                            <p class="review-sidebar__description">{{ $reviewSidebar['strength']['description'] }}</p>
                        </div>
                    </section>
                    <section class="sidebar-panel">
                        <header class="sidebar-panel__header">
                            <x-game.icon name="question" class="sidebar-panel__icon" />
                            <h2 class="sidebar-panel__title">Главный риск</h2>
                        </header>
                        <div class="sidebar-panel__body">
                            <p class="review-sidebar__value">{{ $reviewSidebar['risk']['title'] }}</p>
                            <p class="review-sidebar__description">{{ $reviewSidebar['risk']['description'] }}</p>
                        </div>
                    </section>
                    <section class="sidebar-panel">
                        <header class="sidebar-panel__header">
                            <x-game.icon name="compass" class="sidebar-panel__icon" />
                            <h2 class="sidebar-panel__title">Рекомендация</h2>
                        </header>
                        <div class="sidebar-panel__body">
                            <p class="review-sidebar__value">{{ $reviewSidebar['recommendation']['title'] }}</p>
                            <p class="review-sidebar__description">{{ $reviewSidebar['recommendation']['description'] }}</p>
                        </div>
                    </section>
                </div>

                {{-- Сайдбар: хроника года --}}
            @else
                @php
                    $chronicleData = $this->getChronicle();
                    $sidebarTurning = collect($chronicleData['entries'])->filter(fn ($e) => $e['turningPoint'])->pluck('month');
                @endphp
                <div class="app-sidebar app-sidebar--chronicle">
                    <section class="sidebar-panel">
                        <header class="sidebar-panel__header">
                            <x-game.icon name="clock" class="sidebar-panel__icon" />
                            <h2 class="sidebar-panel__title">Хроника года</h2>
                        </header>
                        <div class="sidebar-panel__body">
                            <p class="review-sidebar__value">{{ $totalSteps }} месяцев управления</p>
                            <p class="review-sidebar__description">Решения, результаты и отложенные последствия.</p>
                        </div>
                    </section>
                    <section class="sidebar-panel">
                        <header class="sidebar-panel__header">
                            <x-game.icon name="scales" class="sidebar-panel__icon" />
                            <h2 class="sidebar-panel__title">Поворотные решения</h2>
                        </header>
                        <div class="sidebar-panel__body">
                            <p class="review-sidebar__value">{{ $sidebarTurning->count() }} ключевых эпизода</p>
                            <ul class="review-sidebar__list">
                                @foreach($sidebarTurning as $month)<li>{{ $month }}</li>@endforeach
                            </ul>
                        </div>
                    </section>
                    <section class="sidebar-panel">
                        <header class="sidebar-panel__header">
                            <x-game.icon name="chart" class="sidebar-panel__icon" />
                            <h2 class="sidebar-panel__title">Главная связь</h2>
                        </header>
                        <div class="sidebar-panel__body">
                            <p class="review-sidebar__value">Быстрые решения усиливали зависимость системы от главы</p>
                            <p class="review-sidebar__description">Краткосрочная управляемость росла быстрее, чем самостоятельность аппарата.</p>
                        </div>
                    </section>
                    <section class="sidebar-panel">
                        <header class="sidebar-panel__header">
                            <x-game.icon name="compass" class="sidebar-panel__icon" />
                            <h2 class="sidebar-panel__title">Навигация</h2>
                        </header>
                        <div class="sidebar-panel__body">
                            <p class="review-sidebar__value">Связи между событиями</p>
                            <p class="review-sidebar__description">Используйте фильтры и связи между событиями, чтобы проследить влияние решений на последующие месяцы.</p>
                        </div>
                    </section>
                </div>
            @endif
        </aside>
    </div>

    {{-- Модальное окно подтверждения переигровки --}}
    @if($replayConfirmationOpen)
        <div class="replay-backdrop" wire:click="closeReplayConfirmation" x-on:keydown.escape.window="$wire.closeReplayConfirmation()">
            <article class="replay-dialog" role="dialog" aria-modal="true" aria-labelledby="replay-dialog-title" aria-describedby="replay-dialog-description" wire:click.stop>
                <header class="replay-dialog__header">
                    <x-game.icon name="question" class="replay-dialog__icon" />
                    <h2 id="replay-dialog-title">Начать сценарий заново?</h2>
                    <button type="button" class="replay-dialog__close" wire:click="closeReplayConfirmation" aria-label="Закрыть подтверждение">×</button>
                </header>
                <p id="replay-dialog-description" class="replay-dialog__copy">Текущий прогресс прохождения будет сброшен.</p>
                <footer class="replay-dialog__footer">
                    <button type="button" class="document-action document-action--secondary" wire:click="closeReplayConfirmation">Отмена</button>
                    <button type="button" class="document-action document-action--primary replay-dialog__confirm" wire:click="confirmReplay">Начать заново</button>
                </footer>
            </article>
        </div>
    @endif
</div>
