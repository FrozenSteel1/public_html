@if($phase === 'closed-folder')
    {{-- ========== ЭКРАН 02: ЗАКРЫТАЯ ПАПКА ========== --}}
    <div class="closed-folder-screen">
        <section class="closed-folder-stage">
            <article class="closed-dossier" role="button" tabindex="0"
                     wire:click="openFolder"
                     wire:keydown.enter="openFolder"
                     aria-label="Закрытое дело: {{ $scenarioInfo['name'] ?? '—' }}, роль Глава округа, сложность {{ $this->getDifficultyLabel($game->difficulty) }}">
                <span class="closed-dossier__paper-edges" aria-hidden="true"></span>
                <span class="closed-dossier__spine" aria-hidden="true"></span>
                <div class="closed-dossier__ornament">
                    <span class="closed-dossier__emblem" aria-hidden="true"></span>
                    <h1 class="closed-dossier__title">
                        <span>Стратегические шахматы</span>
                        <span>Госуправление</span>
                    </h1>
                    <dl class="closed-dossier__identity">
                        <div><dt>Должность</dt><dd>Глава округа</dd></div>
                        <div><dt>Сценарий</dt><dd>{{ $scenarioInfo['name'] ?? '—' }}</dd></div>
                        <div><dt>Сложность</dt><dd>{{ $this->getDifficultyLabel($game->difficulty) }}</dd></div>
                    </dl>
                    <p class="closed-dossier__prompt">Нажмите, чтобы открыть дело</p>
                </div>
                <span class="closed-dossier__clasp" aria-hidden="true"></span>
            </article>
            <button type="button" class="document-action document-action--secondary closed-folder-back" wire:click="backToScenarios">← Назад к сценариям</button>
        </section>

        <aside class="closed-folder-sidebar" aria-label="Информация о выбранном сценарии">
            <section class="closed-folder-info">
                <span class="closed-folder-info__icon"><x-game.icon name="scenario" class="closed-folder-info__svg" /></span>
                <div class="closed-folder-info__copy">
                    <h2>Выбранный сценарий</h2>
                    <strong>{{ $scenarioInfo['name'] ?? '—' }}</strong>
                    <p>{{ $scenarioInfo['description'] ?: 'Управленческий год нового главы округа.' }}</p>
                </div>
            </section>

            <section class="closed-folder-info">
                <span class="closed-folder-info__icon"><x-game.icon name="role" class="closed-folder-info__svg" /></span>
                <div class="closed-folder-info__copy">
                    <h2>Роль</h2>
                    <strong>Глава округа</strong>
                    <p>Примите ключевых управленческих решений и ответственность за результат.</p>
                </div>
            </section>

            <section class="closed-folder-info">
                <span class="closed-folder-info__icon"><x-game.icon name="chart" class="closed-folder-info__svg" /></span>
                <div class="closed-folder-info__copy">
                    <h2>Сложность</h2>
                    <strong>{{ $this->getDifficultyLabel($game->difficulty) }}</strong>
                    <p>{{ $this->getDifficultyDescription($game->difficulty) }}</p>
                </div>
            </section>

            <section class="closed-folder-info closed-folder-info--ready">
                <span class="closed-folder-info__icon"><x-game.icon name="flag" class="closed-folder-info__svg" /></span>
                <div class="closed-folder-info__copy">
                    <h2>Готово к запуску</h2>
                    <p>После открытия дела начнётся вводная часть сценария.</p>
                </div>
            </section>

            <button type="button" class="document-action document-action--primary closed-folder-open" wire:click="openFolder">Открыть дело →</button>
        </aside>
    </div>
@elseif($phase === 'introduction')
    {{-- ========== ЭКРАН 03: ВВЕДЕНИЕ ========== --}}
    <div class="introduction-layout">
        <section class="introduction-layout__main">
            <div class="dossier dossier--introduction">
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
                    <article class="document-sheet document-sheet--compact document-sheet--introduction">
                        <header class="document-sheet__header">
                            <x-game.icon name="emblem" class="document-sheet__emblem" />
                            <p class="document-sheet__eyebrow">СТРАТЕГИЧЕСКИЕ ШАХМАТЫ: ГОСУПРАВЛЕНИЕ · СЦЕНАРИЙ «{{ $scenarioInfo['name'] ?? '—' }}»</p>
                            <h1 class="document-sheet__title">Вступление в должность</h1>
                            <p class="document-sheet__subtitle">Исходное состояние муниципального округа</p>
                            <div class="document-sheet__divider" aria-hidden="true"><span></span></div>
                        </header>
                        <div class="document-sheet__body">
                            <p class="introduction-document__lead">Вы только вступили в должность главы округа. На первом совещании команда, оставшаяся от предыдущего руководителя, вводит вас в курс текущего положения дел.</p>

                            <section class="document-section">
                                <h2 class="document-section__title">
                                    <x-game.icon name="building" class="document-section__icon" />
                                    <span class="document-section__number">1.</span>
                                    ОБЩЕЕ СОСТОЯНИЕ ОКРУГА
                                </h2>
                                <p>В целом округ находится в рабочем состоянии. Основные процессы идут, критических сбоев нет. Однако многие вопросы приходится вручную доводить до результата, особенно там, где требуется взаимодействие нескольких подразделений.</p>
                            </section>

                            <section class="document-section">
                                <h2 class="document-section__title">
                                    <x-game.icon name="group" class="document-section__icon" />
                                    <span class="document-section__number">2.</span>
                                    НАСТРОЕНИЯ ЖИТЕЛЕЙ
                                </h2>
                                <p>Жалобы поступают регулярно, но пока без резких всплесков. Основное недовольство связано не только с качеством услуг, но и с ощущением формальности, непрозрачности и недостатка обратной связи при принятии решений.</p>
                            </section>

                            <section class="document-section">
                                <h2 class="document-section__title">
                                    <x-game.icon name="briefcase" class="document-section__icon" />
                                    <span class="document-section__number">3.</span>
                                    СОСТОЯНИЕ АППАРАТА
                                </h2>
                                <p>Аппарат сохраняет управляемость, но работает с высокой нагрузкой. При росте давления или возникновении нестандартных ситуаций возможны задержки, несогласованность действий и управленческие сбои.</p>
                            </section>

                            <section class="document-section document-section--conclusion">
                                <h2 class="document-section__title">
                                    <x-game.icon name="scales" class="document-section__icon" />
                                    <span class="document-section__number">4.</span>
                                    ГЛАВНЫЙ ВЫВОД
                                </h2>
                                <p>Система пока держится, но запас прочности ограничен.</p>
                                <p>Пока ситуация остаётся стабильной, необходимо выстроить процессы, укрепить взаимодействие подразделений и восстановить доверие к процедурам.</p>
                            </section>
                        </div>
                    </article>
                </section>

                <div class="dossier__tabs-rail">
                    <nav class="folder-tabs folder-tabs--introduction" aria-label="Разделы дела">
                        <ul class="folder-tabs__list">
                            <li class="folder-tabs__item">
                                <button type="button" class="folder-tabs__button" aria-current="page">
                                    <span class="folder-tabs__label">Введение</span>
                                </button>
                            </li>
                            <li class="folder-tabs__item">
                                <button type="button" class="folder-tabs__button" disabled>
                                    <span class="folder-tabs__label">Итоги года</span>
                                </button>
                            </li>
                        </ul>
                    </nav>
                </div>

                <div class="dossier__clasp" aria-hidden="true"></div>
            </div>
        </section>

        <aside class="introduction-sidebar" aria-label="Сведения о вступлении в должность">
            <section class="closed-folder-info">
                <span class="closed-folder-info__icon"><x-game.icon name="scenario" class="closed-folder-info__svg" /></span>
                <div class="closed-folder-info__copy">
                    <h2>Выбранный сценарий</h2>
                    <strong>{{ $scenarioInfo['name'] ?? '—' }}</strong>
                    <p>Управленческий год нового главы округа.</p>
                </div>
            </section>

            <section class="closed-folder-info">
                <span class="closed-folder-info__icon"><x-game.icon name="role" class="closed-folder-info__svg" /></span>
                <div class="closed-folder-info__copy">
                    <h2>Роль</h2>
                    <strong>Глава округа</strong>
                    <p>Принимайте ключевые управленческие решения и несите ответственность за результат.</p>
                </div>
            </section>

            <section class="closed-folder-info">
                <span class="closed-folder-info__icon"><x-game.icon name="chart" class="closed-folder-info__svg" /></span>
                <div class="closed-folder-info__copy">
                    <h2>Сложность</h2>
                    <strong>{{ $this->getDifficultyLabel($game->difficulty) }}</strong>
                    <p>{{ $this->getDifficultyDescription($game->difficulty) }}</p>
                </div>
            </section>

            <section class="closed-folder-info closed-folder-info--ready">
                <span class="closed-folder-info__icon"><x-game.icon name="clock" class="closed-folder-info__svg" /></span>
                <div class="closed-folder-info__copy">
                    <h2>Стадия</h2>
                    <strong>Вступление в должность</strong>
                    <p>Ознакомьтесь с исходным состоянием округа.</p>
                </div>
            </section>

            <button type="button" class="document-action document-action--primary introduction-accept" wire:click="acceptDuties">Принять дела →</button>
        </aside>
    </div>
@else
    {{-- ========== ЭКРАНЫ 04+: ИГРОВОЕ ДОСЬЕ ========== --}}
    <div class="app-workspace app-workspace--gameplay">
        <section class="app-workspace__main">
            <div class="dossier dossier--gameplay">
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
                    @if($gameplayTab === 'scenario')
                        <article class="document-sheet document-sheet--compact document-sheet--scene">
                            <header class="document-sheet__header">
                                <x-game.icon name="emblem" class="document-sheet__emblem" />
                                <p class="document-sheet__eyebrow">СТРАТЕГИЧЕСКИЕ ШАХМАТЫ: ГОСУПРАВЛЕНИЕ · СЦЕНАРИЙ «{{ $scenarioInfo['name'] ?? '—' }}»</p>
                                <h1 class="document-sheet__title">{{ $scene->title ?? 'Сцена' }}</h1>
                                <p class="document-sheet__subtitle">Сцена {{ $scene->order ?? '' }}. {{ $monthNames[$scene->order] ?? '' }}. Управленческая ситуация</p>
                                <div class="document-sheet__divider" aria-hidden="true"><span></span></div>
                            </header>
                            <div class="document-sheet__body">
                                @if(session()->has('message'))
                                    <div class="document-section__callout" role="status">{{ session('message') }}</div>
                                @endif
                                @if(session()->has('error'))
                                    <div class="document-section__callout" role="alert">{{ session('error') }}</div>
                                @endif
                                    @if($hasDelayedMessages && !$showDelayedModal)
                                        <aside class="delayed-trigger">
                                            <x-game.icon name="clock" class="delayed-trigger__icon" />
                                            <div>
                                                <h2>Эхо прошлого решения</h2>
                                                <p>Проявились последствия выбора, сделанного ранее</p>
                                                <span class="delayed-trigger__status">Проявилось</span>
                                            </div>
                                            <button type="button" class="document-action document-action--secondary" wire:click="openDelayedModal">Открыть</button>
                                        </aside>
                                    @endif
                                <section class="document-section">
                                    <h2 class="document-section__title">
                                        <x-game.icon name="group" class="document-section__icon" />
                                        <span class="document-section__number">1.</span>
                                        СИТУАЦИЯ
                                    </h2>
                                    @foreach(explode("\n", $scene->situation ?? '') as $paragraph)
                                        @if(trim($paragraph) !== '')
                                            <p>{{ trim($paragraph) }}</p>
                                        @endif
                                    @endforeach
                                </section>

                                @php
                                    $additional = collect($this->getAdditionalData())
                                        ->filter(fn ($item) => is_array($item) && isset($item['key'], $item['value']));
                                @endphp
                                @if($additional->count() > 0)
                                    <section class="document-section">
                                        <h2 class="document-section__title">
                                            <x-game.icon name="chart" class="document-section__icon" />
                                            <span class="document-section__number">2.</span>
                                            ДОПОЛНИТЕЛЬНЫЕ ДАННЫЕ
                                        </h2>
                                        <div class="document-section__callout">
                                            @foreach($additional as $item)
                                                <strong>{{ $item['key'] }}:</strong> {{ $item['value'] }}<br>
                                            @endforeach
                                        </div>
                                    </section>
                                @endif
                            </div>
                            <footer class="document-sheet__actions">
                                <button type="button" class="document-action document-action--text" wire:click="forceNewGame">Начать заново</button>
                                <button type="button" class="document-action document-action--primary" wire:click="setGameplayTab('decisions')">Перейти к решениям ›</button>
                            </footer>
                        </article>
                    @elseif($gameplayTab === 'decisions')
                        @if($decisionsDoc === 'result')
                            {{-- ========== ЭКРАН 11: РЕШЕНИЕ ПРИНЯТО ========== --}}
                            <article class="document-sheet document-sheet--compact document-sheet--immediate-result">
                                <header class="document-sheet__header">
                                    <x-game.icon name="emblem" class="document-sheet__emblem" />
                                    <p class="result-document__context">{{ $resultSnapshot['scene'] ?? '' }}</p>
                                    <h1 class="document-sheet__title">Решение принято</h1>
                                    <p class="document-sheet__subtitle">Непосредственный результат управленческого действия</p>
                                    <div class="document-sheet__divider" aria-hidden="true"><span></span></div>
                                </header>
                                <div class="document-sheet__body result-document__body">
                                    <section class="result-selected">
                                        <x-game.icon name="scales" class="result-selected__icon" />
                                        <div>
                                            <h2>ВЫБРАННОЕ РЕШЕНИЕ</h2>
                                            <h3>{{ $resultSnapshot['decision'] ?? 'Решение принято' }}</h3>
                                        </div>
                                    </section>

                                    <section class="result-section result-section--positive">
                                        <x-game.icon name="chart" class="result-section__icon" />
                                        <h2>1. НЕПОСРЕДСТВЕННЫЙ РЕЗУЛЬТАТ</h2>
                                        <div class="result-section__body">
                                            <p>Решение применено. Управление переведено на сцену «{{ $resultSnapshot['scene'] ?? '' }}».</p>
                                        </div>
                                    </section>

                                    <section class="result-section result-section--warning">
                                        <x-game.icon name="group" class="result-section__icon" />
                                        <h2>2. РЕАКЦИЯ АКТОРОВ</h2>
                                        <div class="result-section__body">
                                            @if(count($resultSnapshot['events'] ?? []) > 0)
                                                <ul>
                                                    @foreach($resultSnapshot['events'] as $event)
                                                        <li>{{ $event['actor'] }} — {{ $event['event'] }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p>Непосредственных реакций акторов не зафиксировано.</p>
                                            @endif
                                        </div>
                                    </section>

                                    <section class="result-section result-section--neutral">
                                        <x-game.icon name="building" class="result-section__icon" />
                                        <h2>3. ТЕКУЩАЯ ОБСТАНОВКА</h2>
                                        <div class="result-section__body">
                                            @php
                                                $situationFirst = collect(explode("\n", $scene->situation ?? ''))
                                                    ->map(fn ($line) => trim($line))->filter()->first();
                                            @endphp
                                            <p>{{ $situationFirst ?? 'Обстановка уточняется.' }}</p>
                                        </div>
                                    </section>

                                    @if(count($inboxMessages) > 0)
                                        <section class="result-messages">
                                            <h2>НОВЫЕ ВХОДЯЩИЕ</h2>
                                            <ul>
                                                @foreach($inboxMessages as $message)
                                                    <li>
                                                        <x-game.icon name="mail" class="result-message__icon" />
                                                        <span>
                                                            <strong>{{ $message['actor'] }} актор</strong>
                                                            <span>{{ $message['subject'] }}</span>
                                                        </span>
                                                        <span class="result-message__status">Новое</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </section>
                                    @endif
                                </div>
                                <footer class="document-sheet__actions result-actions">
                                    <button type="button" class="document-action document-action--secondary" wire:click="openInbox">Открыть входящие</button>
                                    <button type="button" class="document-action document-action--text" wire:click="continueFromResult">Вернуться к сцене</button>
                                    <button type="button" class="document-action document-action--primary" wire:click="continueFromResult">Продолжить ›</button>
                                </footer>
                            </article>
                        @elseif($decisionsDoc === 'inbox')
                            {{-- ========== ЭКРАН 12: ВХОДЯЩИЕ ========== --}}
                            @php
                                $inboxSummary = $this->getInboxSummary();
                                $visibleInbox = $this->getVisibleInboxMessages();
                                $inboxActors = collect($inboxMessages)->pluck('actor')->unique()->values();
                            @endphp
                            <article class="document-sheet document-sheet--compact document-sheet--inbox">
                                <header class="document-sheet__header inbox-header">
                                    <x-game.icon name="mail" class="inbox-header__icon" />
                                    <h1 class="document-sheet__title">Входящие сообщения</h1>
                                    <p class="document-sheet__subtitle">Реакции акторов после принятого решения</p>
                                    <p class="inbox-header__supporting">Изучите поступившие сообщения, чтобы понять позиции участников, внутренние риски и изменение общественной реакции.</p>
                                    <div class="document-sheet__divider" aria-hidden="true"></div>
                                </header>
                                <div class="document-sheet__body inbox-document__body">
                                    <section class="inbox-summary" aria-label="Сводка входящих сообщений">
                                        <div><strong>{{ $inboxSummary['total'] }}</strong><span>Всего сообщений</span></div>
                                        <div><strong>{{ $inboxSummary['unread'] }}</strong><span>Непрочитано</span></div>
                                        <div><strong>{{ $inboxSummary['urgent'] }}</strong><span>Срочные</span></div>
                                        <div><strong>{{ $inboxSummary['actors'] }}</strong><span>Акторов с сообщениями</span></div>
                                    </section>

                                    <section class="inbox-toolbar" aria-label="Фильтры входящих сообщений">
                                        <label class="inbox-filter">
                                            <span>Статус</span>
                                            <select wire:model.live="inboxFilter" aria-label="Статус">
                                                <option value="all">Все</option>
                                                <option value="new">Новые</option>
                                                <option value="urgent">Срочные</option>
                                                <option value="read">Прочитанные</option>
                                            </select>
                                        </label>
                                        <label class="inbox-filter">
                                            <span>Актор</span>
                                            <select wire:model.live="inboxActorFilter" aria-label="Актор">
                                                <option value="all">Все акторы</option>
                                                @foreach($inboxActors as $actorName)
                                                    <option value="{{ $actorName }}">{{ $actorName }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                        <label class="inbox-filter">
                                            <span>Сортировка</span>
                                            <select wire:model.live="inboxSort" aria-label="Сортировка">
                                                <option value="unread-first">Сначала непрочитанные</option>
                                                <option value="new-first">Сначала новые</option>
                                                <option value="priority">По приоритету</option>
                                            </select>
                                        </label>
                                    </section>

                                    <ul class="inbox-list" aria-label="Список входящих сообщений">
                                        @forelse($visibleInbox as $message)
                                            @php $read = $this->isInboxRead($message['id']); @endphp
                                            <li class="inbox-message">
                                                <button type="button"
                                                        class="inbox-message__button {{ $read ? 'inbox-message__button--read' : 'inbox-message__button--unread' }} @if($message['urgent']) inbox-message__button--urgent @endif"
                                                        wire:click="selectInboxMessage({{ $message['id'] }})"
                                                        aria-label="{{ $read ? 'Прочитано' : 'Непрочитано' }}. {{ $message['actor'] }}. {{ $message['subject'] }}. Приоритет: {{ $message['priority'] }}">
                                                    <x-game.icon name="mail" class="inbox-message__icon" />
                                                    <span class="inbox-message__copy">
                                                        <span class="inbox-message__actor">{{ $message['actor'] }} актор · {{ $message['sender'] }}</span>
                                                        <strong class="inbox-message__subject">{{ $message['subject'] }}</strong>
                                                        <span class="inbox-message__preview">{{ $message['preview'] }}</span>
                                                    </span>
                                                    <span class="inbox-message__metadata">
                                                        <span class="inbox-message__time">{{ $message['time'] }}</span>
                                                        <span class="inbox-message__status @if($read) inbox-message__status--read @endif">{{ $read ? 'Прочитано' : ($message['urgent'] ? 'Срочно' : 'Новое') }}</span>
                                                        <span class="inbox-message__priority">{{ $message['priority'] }} приоритет</span>
                                                    </span>
                                                    <span class="inbox-message__chevron" aria-hidden="true">›</span>
                                                </button>
                                            </li>
                                        @empty
                                            <li class="inbox-empty">
                                                <p>Сообщения по выбранному фильтру отсутствуют.</p>
                                                <button type="button" class="document-action document-action--secondary" wire:click="resetInboxFilters">Сбросить фильтры</button>
                                            </li>
                                        @endforelse
                                    </ul>
                                </div>
                                <footer class="document-sheet__actions inbox-actions">
                                    <button type="button" class="document-action document-action--secondary" wire:click="backToResult">‹ Назад к результату</button>
                                    <button type="button" class="document-action document-action--text" wire:click="markAllInboxRead" @disabled($inboxSummary['unread'] === 0)>Отметить все как прочитанные</button>
                                    <button type="button" class="document-action document-action--primary" wire:click="continueFromResult">Продолжить ›</button>
                                </footer>
                            </article>
                        @else
                            <article class="document-sheet document-sheet--compact document-sheet--decisions">
                            <header class="document-sheet__header">
                                <x-game.icon name="emblem" class="document-sheet__emblem" />
                                <h1 class="document-sheet__title">{{ $scene->title ?? 'Решения' }}</h1>
                                <div class="document-sheet__divider" aria-hidden="true"><span></span></div>
                            </header>
                            <div class="document-sheet__body decisions-document__body">
                                <section class="decisions-introduction">
                                    <div>
                                        <h2 class="decisions-introduction__title">ВАРИАНТЫ РЕШЕНИЯ</h2>
                                        <p class="decisions-introduction__lead">Выберите один вариант управленческого решения. Подтверждение запускает последствия хода.</p>
                                    </div>
                                    <div class="decision-timer @if($decisionExpired) decision-timer--expired @endif"
                                         x-data="{
                                            now: Math.floor(Date.now() / 1000),
                                            deadline: {{ $decisionDeadline ?? 'null' }},
                                            expired: @json($decisionExpired),
                                            announce: '',
                                            fmt() {
                                                const s = Math.max(0, (this.deadline ?? this.now) - this.now);
                                                const m = Math.floor(s / 60);
                                                const r = s % 60;
                                                return String(m).padStart(2, '0') + ':' + String(r).padStart(2, '0');
                                            }
                                        }"
                                         x-init="if (deadline && !expired) {
                                            const t = setInterval(() => {
                                                now = Math.floor(Date.now() / 1000);
                                                const left = deadline - now;
                                                if (left === 60) announce = 'До окончания времени на решение осталась одна минута.';
                                                if (left === 30) announce = 'До окончания времени на решение осталось тридцать секунд.';
                                                if (left <= 0) { expired = true; clearInterval(t); $wire.timeExpired(); }
                                            }, 500);
                                        }">
                                        <div class="decision-timer__line">
                                            <x-game.icon name="clock" class="decision-timer__icon" />
                                            <span>Время на решение:</span>
                                            <strong class="decision-timer__value" x-text="fmt()">--:--</strong>
                                        </div>
                                        <p class="decision-timer__help">
                                            {{ $decisionExpired ? 'Время на принятие решения истекло.' : 'Если время истечёт, решение будет считаться непринятым.' }}
                                        </p>
                                        <span class="visually-hidden" aria-live="polite" x-text="announce"></span>
                                    </div>
                                </section>

                                <fieldset class="decision-options">
                                    <legend class="visually-hidden">Выберите один вариант управленческого решения</legend>
                                    @foreach ($this->getVisibleChoices() as $option)
                                        <button type="button"
                                                class="decision-option @if($selectedChoiceId === $option['id']) decision-option--selected @endif"
                                                wire:click="selectOption({{ $option['id'] }})"
                                                @disabled($decisionExpired)
                                                aria-pressed="{{ $selectedChoiceId === $option['id'] ? 'true' : 'false' }}">
                                            <span class="decision-option__badge">{{ $option['letter'] }}</span>
                                            <span class="decision-option__copy">
                                                <strong class="decision-option__title">{{ $option['title'] }}</strong>
                                            </span>
                                            <span class="decision-option__marker" aria-hidden="true">@if($selectedChoiceId === $option['id'])✓@endif</span>
                                        </button>
                                    @endforeach
                                </fieldset>

                                @php $selectedOption = collect($this->getVisibleChoices())->firstWhere('id', $selectedChoiceId); @endphp
                                @if ($selectedOption)
                                    <section class="decision-summary">
                                        <h2 class="decision-summary__title">ВЫБРАННОЕ ДЕЙСТВИЕ</h2>
                                        <div class="decision-summary__content">
                                            <x-game.icon name="broadcast" class="decision-summary__icon" />
                                            <div>
                                                <p>{{ $selectedOption['title'] }}</p>
                                            </div>
                                        </div>
                                    </section>
                                @endif
                            </div>
                            <footer class="document-sheet__actions decisions-actions">
                                <button type="button" class="document-action document-action--secondary" wire:click="setGameplayTab('scenario')">‹ Назад к сценарию</button>
                                <button type="button" class="document-action document-action--primary decisions-actions__confirm"
                                        wire:click="confirmDecision"
                                    @disabled(!$selectedChoiceId || $decisionExpired)>Подтвердить решение</button>
                                <button type="button" class="document-action document-action--text" wire:click="setGameplayTab('materials')">Открыть материалы ↗</button>
                            </footer>
                            </article>
                        @endif
                    @elseif($gameplayTab === 'materials')
                        @php
                            $visibleMaterials = $this->getVisibleMaterials();
                        @endphp
                        <article class="document-sheet document-sheet--compact document-sheet--materials">
                            <header class="materials-header">
                                <div class="materials-header__heading">
                                    <x-game.icon name="folderOpen" class="materials-header__icon" />
                                    <div>
                                        <h1 class="materials-header__title">Материалы дела</h1>
                                        <p class="materials-header__subtitle">Документы и исходные материалы по ситуации с уборкой снега</p>
                                    </div>
                                </div>
                                <p class="materials-header__support">Изучите документы, чтобы уточнить условия контракта, распределение ответственности и фактическое исполнение работ.</p>
                            </header>
                            <div class="document-sheet__body materials-document__body">
                                <div class="materials-toolbar">
                                    <div class="materials-toolbar__controls">
                                        <label class="materials-control">
                                            <span>Категория</span>
                                            <select class="materials-control__select" wire:model.live="materialCategory">
                                                <option value="all">Все материалы</option>
                                                <option value="contracts">Контракты</option>
                                                <option value="plans">Планы</option>
                                                <option value="reports">Отчёты</option>
                                                <option value="schemes">Схемы</option>
                                                <option value="schedules">Графики</option>
                                            </select>
                                        </label>
                                        <label class="materials-control">
                                            <span>Сортировка</span>
                                            <select class="materials-control__select" wire:model.live="materialSort">
                                                <option value="importance">По важности</option>
                                                <option value="date">По дате</option>
                                                <option value="type">По типу</option>
                                            </select>
                                        </label>
                                    </div>
                                    <div class="materials-view" role="group" aria-label="Вид каталога">
                                        <span class="materials-view__label">Вид</span>
                                        <button type="button"
                                                class="materials-view__button @if($materialView === 'cards') materials-view__button--active @endif"
                                                wire:click="setMaterialView('cards')"
                                                aria-pressed="{{ $materialView === 'cards' ? 'true' : 'false' }}">Карточки</button>
                                        <button type="button"
                                                class="materials-view__button @if($materialView === 'list') materials-view__button--active @endif"
                                                wire:click="setMaterialView('list')"
                                                aria-pressed="{{ $materialView === 'list' ? 'true' : 'false' }}">Список</button>
                                    </div>
                                    <p class="materials-toolbar__count" aria-live="polite">Найдено: {{ count($visibleMaterials) }}</p>
                                </div>

                                <section class="materials-catalogue materials-catalogue--{{ $materialView }}" aria-label="Материалы дела">
                                    @forelse($visibleMaterials as $material)
                                        @php $studied = in_array($material['id'], $studiedMaterialIds, true); @endphp
                                        <article class="material-item @if($studied) material-item--studied @endif">
                                            <x-game.icon name="scenario" class="material-item__icon" />
                                            <div class="material-item__copy">
                                                <p class="material-item__eyebrow">{{ $material['eyebrow'] }}</p>
                                                <h3 class="material-item__title">{{ $material['title'] }}</h3>
                                                <p class="material-item__description">{{ $material['description'] }}</p>
                                                <ul class="material-item__metadata">
                                                    @foreach($material['metadata'] as $meta)
                                                        <li>{{ $meta }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <footer class="material-item__footer">
                                                <span class="material-item__importance @if($material['priority'] >= 5) material-item__importance--high @endif">
                                                    {{ $studied ? '✓ Изучено' : 'Важность: ' . $material['importance'] }}
                                                </span>
                                                <button type="button" class="material-item__open"
                                                        wire:click="openMaterial('{{ $material['id'] }}')"
                                                        aria-label="Открыть материал: {{ $material['title'] }}">
                                                    {{ $studied ? 'Открыть снова' : 'Открыть' }}
                                                </button>
                                            </footer>
                                        </article>
                                    @empty
                                        <p class="materials-catalogue__empty">По выбранным условиям материалы не найдены.</p>
                                    @endforelse
                                </section>
                            </div>
                            <footer class="document-sheet__actions materials-actions">
                                <button type="button" class="document-action document-action--secondary" wire:click="setGameplayTab('scenario')">‹ Назад к сценарию</button>
                                <button type="button" class="document-action document-action--primary" wire:click="setGameplayTab('decisions')">Перейти к решениям ›</button>
                            </footer>
                        </article>
                    @elseif($gameplayTab === 'references')
                        @php
                            $visibleReferences = $this->getVisibleReferences();
                        @endphp
                        <article class="document-sheet document-sheet--compact document-sheet--materials document-sheet--references">
                            <header class="materials-header references-header">
                                <div class="materials-header__heading">
                                    <x-game.icon name="briefcase" class="materials-header__icon references-header__icon" />
                                    <div>
                                        <h1 class="materials-header__title">Справки по делу</h1>
                                        <p class="materials-header__subtitle">Служебная и аналитическая информация для принятия решения</p>
                                    </div>
                                </div>
                                <p class="materials-header__support">Изучите подготовленные справки, чтобы оценить ресурсы, ограничения, риски и позиции участников ситуации.</p>
                            </header>
                            <div class="document-sheet__body materials-document__body references-document__body">
                                <div class="materials-toolbar references-toolbar">
                                    <div class="materials-toolbar__controls">
                                        <label class="materials-control">
                                            <span>Категория</span>
                                            <select class="materials-control__select" wire:model.live="referenceCategory">
                                                <option value="all">Все справки</option>
                                                <option value="internal">Внутренние</option>
                                                <option value="financial">Финансовые</option>
                                                <option value="regional">Региональные</option>
                                                <option value="analytical">Аналитические</option>
                                                <option value="risks">Риски</option>
                                            </select>
                                        </label>
                                        <label class="materials-control">
                                            <span>Сортировка</span>
                                            <select class="materials-control__select" wire:model.live="referenceSort">
                                                <option value="importance">По важности</option>
                                                <option value="date">По дате</option>
                                                <option value="type">По типу</option>
                                            </select>
                                        </label>
                                    </div>
                                    <div class="materials-view" role="group" aria-label="Вид каталога справок">
                                        <span class="materials-view__label">Вид</span>
                                        <button type="button"
                                                class="materials-view__button @if($referenceView === 'cards') materials-view__button--active @endif"
                                                wire:click="setReferenceView('cards')"
                                                aria-pressed="{{ $referenceView === 'cards' ? 'true' : 'false' }}">Карточки</button>
                                        <button type="button"
                                                class="materials-view__button @if($referenceView === 'list') materials-view__button--active @endif"
                                                wire:click="setReferenceView('list')"
                                                aria-pressed="{{ $referenceView === 'list' ? 'true' : 'false' }}">Список</button>
                                    </div>
                                    <p class="materials-toolbar__count" aria-live="polite">Найдено: {{ count($visibleReferences) }}</p>
                                </div>

                                <section class="materials-catalogue materials-catalogue--{{ $referenceView }} references-catalogue references-catalogue--{{ $referenceView }}" aria-label="Справки по делу">
                                    @forelse($visibleReferences as $reference)
                                        @php $studied = in_array($reference['id'], $studiedReferenceIds, true); @endphp
                                        <article class="material-item reference-card reference-card--{{ $reference['category'] }} @if($studied) material-item--studied reference-card--studied @endif">
                                            <x-game.icon name="{{ $reference['icon'] }}" class="material-item__icon reference-card__icon" />
                                            <div class="material-item__copy">
                                                <p class="material-item__eyebrow reference-card__eyebrow">{{ $reference['eyebrow'] }}</p>
                                                <h3 class="material-item__title">{{ $reference['title'] }}</h3>
                                                <p class="material-item__description">{{ $reference['description'] }}</p>
                                                <ul class="material-item__metadata">
                                                    @foreach($reference['metadata'] as $meta)
                                                        <li>{{ $meta }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <footer class="material-item__footer">
                                                <span class="material-item__importance @if($reference['importance'] === 'Высокая') material-item__importance--high @endif">
                                                    {{ $studied ? '✓ Изучено' : 'Важность: ' . $reference['importance'] }}
                                                </span>
                                                <button type="button" class="material-item__open"
                                                        wire:click="openReference('{{ $reference['id'] }}')"
                                                        aria-label="Открыть справку: {{ $reference['title'] }}">
                                                    {{ $studied ? 'Открыть снова' : 'Открыть' }}
                                                </button>
                                            </footer>
                                        </article>
                                    @empty
                                        <p class="materials-catalogue__empty">Справки по выбранному фильтру отсутствуют.</p>
                                    @endforelse
                                </section>
                            </div>
                            <footer class="document-sheet__actions materials-actions references-actions">
                                <button type="button" class="document-action document-action--secondary" wire:click="setGameplayTab('scenario')">‹ Назад к сценарию</button>
                                <button type="button" class="document-action document-action--text" wire:click="setGameplayTab('materials')">Открыть материалы</button>
                                <button type="button" class="document-action document-action--primary" wire:click="setGameplayTab('decisions')">Перейти к решениям ›</button>
                            </footer>
                        </article>
                    @elseif($gameplayTab === 'media')
                        @php
                            $visibleMedia = $this->getVisibleMedia();
                        @endphp
                        <article class="document-sheet document-sheet--compact document-sheet--materials document-sheet--media">
                            <header class="materials-header media-header">
                                <div class="materials-header__heading">
                                    <x-game.icon name="broadcast" class="materials-header__icon media-header__icon" />
                                    <div>
                                        <h1 class="materials-header__title">СМИ и информационное поле</h1>
                                        <p class="materials-header__subtitle">Публикации, запросы и сигналы по ситуации с уборкой снега</p>
                                    </div>
                                </div>
                                <p class="materials-header__support">Изучите информационный фон, чтобы понять уровень общественного внимания, позицию редакций и динамику публичного давления.</p>
                            </header>
                            <div class="document-sheet__body materials-document__body media-document__body">
                                <div class="materials-toolbar media-toolbar">
                                    <div class="materials-toolbar__controls">
                                        <label class="materials-control">
                                            <span>Категория</span>
                                            <select class="materials-control__select" wire:model.live="mediaCategory">
                                                <option value="all">Все материалы</option>
                                                <option value="requests">Запросы СМИ</option>
                                                <option value="publications">Публикации</option>
                                                <option value="monitoring">Мониторинг</option>
                                                <option value="social">Социальные сети</option>
                                                <option value="quotes">Цитаты</option>
                                            </select>
                                        </label>
                                        <label class="materials-control">
                                            <span>Сортировка</span>
                                            <select class="materials-control__select" wire:model.live="mediaSort">
                                                <option value="importance">По важности</option>
                                                <option value="time">По времени</option>
                                                <option value="resonance">По резонансу</option>
                                            </select>
                                        </label>
                                    </div>
                                    <div class="materials-view" role="group" aria-label="Вид медиакаталога">
                                        <span class="materials-view__label">Вид</span>
                                        <button type="button"
                                                class="materials-view__button @if($mediaView === 'cards') materials-view__button--active @endif"
                                                wire:click="setMediaView('cards')"
                                                aria-pressed="{{ $mediaView === 'cards' ? 'true' : 'false' }}">Карточки</button>
                                        <button type="button"
                                                class="materials-view__button @if($mediaView === 'list') materials-view__button--active @endif"
                                                wire:click="setMediaView('list')"
                                                aria-pressed="{{ $mediaView === 'list' ? 'true' : 'false' }}">Список</button>
                                    </div>
                                    <p class="materials-toolbar__count" aria-live="polite">Найдено: {{ count($visibleMedia) }}</p>
                                </div>

                                <section class="materials-catalogue materials-catalogue--{{ $mediaView }} media-catalogue media-catalogue--{{ $mediaView }}" aria-label="СМИ и информационное поле">
                                    @forelse($visibleMedia as $item)
                                        @php $reviewed = in_array($item['id'], $reviewedMediaIds, true); @endphp
                                        <article class="material-item media-card media-card--{{ $item['type'] }} @if($reviewed) material-item--studied media-card--reviewed @endif">
                                            <x-game.icon name="{{ $item['icon'] }}" class="material-item__icon media-card__icon" />
                                            <div class="material-item__copy">
                                                <p class="material-item__eyebrow media-card__eyebrow">{{ $item['eyebrow'] }}</p>
                                                <p class="media-card__source">{{ $item['source'] }}</p>
                                                <h3 class="material-item__title">{{ $item['title'] }}</h3>
                                                <p class="material-item__description">{{ $item['description'] }}</p>
                                                <ul class="material-item__metadata">
                                                    @foreach($item['metadata'] as $meta)
                                                        <li>{{ $meta }}</li>
                                                    @endforeach
                                                </ul>
                                                <div class="media-card__badges">
                                                    <span class="media-card__badge media-card__badge--tone-{{ mb_strtolower($item['tone']) }}">Тон: {{ $item['tone'] }}</span>
                                                    <span class="media-card__badge media-card__badge--resonance">Резонанс: {{ $item['resonance'] }}</span>
                                                    <span class="media-card__badge media-card__badge--status">{{ $item['status'] }}</span>
                                                </div>
                                            </div>
                                            <footer class="material-item__footer">
                                                <span class="material-item__importance @if($item['priority'] === 'Высокая') material-item__importance--high @endif">
                                                    {{ $reviewed ? '✓ Изучено' : 'Приоритет: ' . $item['priority'] }}
                                                </span>
                                                <button type="button" class="material-item__open"
                                                        wire:click="openMedia('{{ $item['id'] }}')"
                                                        aria-label="Открыть материал СМИ: {{ $item['title'] }}">
                                                    {{ $reviewed ? 'Открыть снова' : 'Открыть' }}
                                                </button>
                                            </footer>
                                        </article>
                                    @empty
                                        <p class="materials-catalogue__empty">Материалы по выбранному фильтру отсутствуют.</p>
                                    @endforelse
                                </section>
                            </div>
                            <footer class="document-sheet__actions materials-actions media-actions">
                                <button type="button" class="document-action document-action--secondary" wire:click="setGameplayTab('scenario')">‹ Назад к сценарию</button>
                                <button type="button" class="document-action document-action--text" wire:click="setGameplayTab('references')">Открыть справки</button>
                                <button type="button" class="document-action document-action--primary" wire:click="setGameplayTab('decisions')">Перейти к решениям ›</button>
                            </footer>
                        </article>
                    @elseif($gameplayTab === 'appeals')
                        @php
                            $visibleAppeals = $this->getVisibleAppeals();
                        @endphp
                        <article class="document-sheet document-sheet--compact document-sheet--materials document-sheet--appeals">
                            <header class="materials-header appeals-header">
                                <div class="materials-header__heading">
                                    <x-game.icon name="group" class="materials-header__icon appeals-header__icon" />
                                    <div>
                                        <h1 class="materials-header__title">Обращения жителей</h1>
                                        <p class="materials-header__subtitle">Жалобы, повторные обращения и сигналы по ситуации с уборкой снега</p>
                                    </div>
                                </div>
                                <p class="materials-header__support">Изучите обращения, чтобы определить основные темы недовольства, проблемные территории и динамику повторных жалоб.</p>
                            </header>
                            <div class="document-sheet__body materials-document__body appeals-document__body">
                                <div class="materials-toolbar appeals-toolbar">
                                    <div class="materials-toolbar__controls">
                                        <label class="materials-control">
                                            <span>Категория</span>
                                            <select class="materials-control__select" wire:model.live="appealCategory">
                                                <option value="all">Все обращения</option>
                                                <option value="repeat">Повторные</option>
                                                <option value="address">По адресам</option>
                                                <option value="social">Социальные сети</option>
                                                <option value="complaints">Жалобы</option>
                                                <option value="urgent">Срочные</option>
                                            </select>
                                        </label>
                                        <label class="materials-control">
                                            <span>Сортировка</span>
                                            <select class="materials-control__select" wire:model.live="appealSort">
                                                <option value="priority">По приоритету</option>
                                                <option value="time">По времени</option>
                                                <option value="repeat">По повторяемости</option>
                                            </select>
                                        </label>
                                    </div>
                                    <div class="materials-view" role="group" aria-label="Вид каталога обращений">
                                        <span class="materials-view__label">Вид</span>
                                        <button type="button"
                                                class="materials-view__button @if($appealView === 'cards') materials-view__button--active @endif"
                                                wire:click="setAppealView('cards')"
                                                aria-pressed="{{ $appealView === 'cards' ? 'true' : 'false' }}">Карточки</button>
                                        <button type="button"
                                                class="materials-view__button @if($appealView === 'list') materials-view__button--active @endif"
                                                wire:click="setAppealView('list')"
                                                aria-pressed="{{ $appealView === 'list' ? 'true' : 'false' }}">Список</button>
                                    </div>
                                    <p class="materials-toolbar__count" aria-live="polite">Найдено: {{ count($visibleAppeals) }}</p>
                                </div>

                                <section class="materials-catalogue materials-catalogue--{{ $appealView }} appeals-catalogue appeals-catalogue--{{ $appealView }}" aria-label="Обращения жителей">
                                    @forelse($visibleAppeals as $appeal)
                                        @php $reviewed = in_array($appeal['id'], $reviewedAppealIds, true); @endphp
                                        <article class="material-item appeal-card appeal-card--{{ $appeal['type'] }} @if($reviewed) material-item--studied appeal-card--reviewed @endif">
                                            <x-game.icon name="{{ $appeal['icon'] }}" class="material-item__icon appeal-card__icon" />
                                            <div class="material-item__copy">
                                                <p class="material-item__eyebrow appeal-card__eyebrow">{{ $appeal['eyebrow'] }}</p>
                                                <p class="appeal-card__source">{{ $appeal['source'] }}</p>
                                                <h3 class="material-item__title">{{ $appeal['title'] }}</h3>
                                                <p class="material-item__description">{{ $appeal['description'] }}</p>
                                                <p class="appeal-card__address">{{ $appeal['address'] }}</p>
                                                <ul class="material-item__metadata">
                                                    @foreach($appeal['metadata'] as $meta)
                                                        <li>{{ $meta }}</li>
                                                    @endforeach
                                                </ul>
                                                <div class="appeal-card__badges">
                                                    <span class="appeal-card__badge appeal-card__badge--priority">Приоритет: {{ $appeal['priority'] }}</span>
                                                    <span class="appeal-card__badge appeal-card__badge--status">{{ $appeal['status'] }}</span>
                                                </div>
                                            </div>
                                            <footer class="material-item__footer">
                                                <span class="material-item__importance @if($appeal['priorityValue'] >= 3) material-item__importance--high @endif">
                                                    {{ $reviewed ? '✓ Изучено' : ($appeal['repeatCount'] > 1 ? 'Повторений: ' . $appeal['repeatCount'] : $appeal['categoryLabel']) }}
                                                </span>
                                                <button type="button" class="material-item__open"
                                                        wire:click="openAppeal('{{ $appeal['id'] }}')"
                                                        aria-label="Открыть обращение: {{ $appeal['title'] }}">
                                                    {{ $reviewed ? 'Открыть снова' : 'Открыть' }}
                                                </button>
                                            </footer>
                                        </article>
                                    @empty
                                        <p class="materials-catalogue__empty">Обращения по выбранному фильтру отсутствуют.</p>
                                    @endforelse
                                </section>
                            </div>
                            <footer class="document-sheet__actions materials-actions appeals-actions">
                                <button type="button" class="document-action document-action--secondary" wire:click="setGameplayTab('scenario')">‹ Назад к сценарию</button>
                                <button type="button" class="document-action document-action--text" wire:click="setGameplayTab('media')">Открыть СМИ</button>
                                <button type="button" class="document-action document-action--primary" wire:click="setGameplayTab('decisions')">Перейти к решениям ›</button>
                            </footer>
                        </article>
                    @else
                        @php
                            $tabTitles = [
                                'decisions' => 'Решения',
                                'materials' => 'Материалы',
                                'references' => 'Справки',
                                'media' => 'СМИ',
                                'appeals' => 'Обращения',
                            ];
                        @endphp
                        <article class="document-sheet document-sheet--compact document-sheet--annual-placeholder">
                            <header class="document-sheet__header">
                                <x-game.icon name="emblem" class="document-sheet__emblem" />
                                <h1 class="document-sheet__title">{{ $tabTitles[$gameplayTab] ?? 'Раздел' }}</h1>
                                <p class="document-sheet__subtitle">Следующий этап интерфейса</p>
                                <div class="document-sheet__divider" aria-hidden="true"><span></span></div>
                            </header>
                            <div class="document-sheet__body annual-placeholder__body">
                                <section class="annual-placeholder__note">
                                    <h2>Раздел готовится</h2>
                                    <p>Раздел «{{ $tabTitles[$gameplayTab] ?? '' }}» будет реализован на следующем шаге.</p>
                                </section>
                            </div>
                            <footer class="document-sheet__actions">
                                <button type="button" class="document-action document-action--secondary" wire:click="setGameplayTab('scenario')">‹ Вернуться к сценарию</button>
                            </footer>
                        </article>
                    @endif
                </section>

                <div class="dossier__tabs-rail">
                    <nav class="folder-tabs folder-tabs--gameplay" aria-label="Разделы дела">
                        <ul class="folder-tabs__list">
                            @foreach(['scenario' => 'Сценарий', 'decisions' => 'Решения', 'materials' => 'Материалы', 'references' => 'Справки', 'media' => 'СМИ', 'appeals' => 'Обращения'] as $tabId => $tabLabel)
                                <li class="folder-tabs__item">
                                    <button type="button" class="folder-tabs__button"
                                            wire:click="setGameplayTab('{{ $tabId }}')"
                                            @if($gameplayTab === $tabId) aria-current="page" @endif>
                                        <span class="folder-tabs__label">{{ $tabLabel }}</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                </div>

                <div class="dossier__clasp" aria-hidden="true"></div>
            </div>
        </section>

        <aside class="app-workspace__sidebar" aria-label="Контекстная информация">
            <div class="sidebar">
                {{-- Метрики системы --}}
                <section class="sidebar-panel sidebar-panel--compact">
                    <header class="sidebar-panel__header">
                        <x-game.icon name="chart" class="sidebar-panel__icon" />
                        <h2 class="sidebar-panel__title">Метрики системы</h2>
                    </header>
                    <div class="sidebar-panel__body">
                        @if(count($currentState) > 0)
                            <div class="metric-list">
                                @foreach($currentState as $key => $value)
                                    <div class="metric-row metric-row--{{ $this->getMetricTone($value) }}">
                                        <div class="metric-row__heading">
                                            <span class="metric-row__label">{{ $this->getParameterLabel($key) }}</span>
                                            <span class="metric-row__value">{{ $value }}</span>
                                        </div>
                                        <div class="metric-row__track" role="progressbar"
                                             aria-label="{{ $this->getParameterLabel($key) }}"
                                             aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $value }}">
                                            <span class="metric-row__bar" style="--metric-value: {{ $value }}%"></span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="sidebar-panel__empty">Нет данных о состоянии системы</p>
                        @endif
                    </div>
                </section>

                {{-- История событий --}}
                <section class="sidebar-panel sidebar-panel--compact">
                    <header class="sidebar-panel__header">
                        <x-game.icon name="clock" class="sidebar-panel__icon" />
                        <h2 class="sidebar-panel__title">История событий</h2>
                    </header>
                    <div class="sidebar-panel__body">
                        @if(count($gameHistoryWithMonths) > 0)
                            <ol class="event-history">
                                @foreach($gameHistoryWithMonths as $index => $event)
                                    <li class="event-history__item @if($index === count($gameHistoryWithMonths) - 1) event-history__item--current @endif">
                                        <span class="event-history__marker" aria-hidden="true"></span>
                                        <div>
                                            <div class="event-history__time">{{ $event['month'] }}</div>
                                            <div class="event-history__label">
                                                @if($event['is_actor_event'])
                                                    ↳ {{ $event['actor_name'] ?? 'Актор' }} — {{ $event['actor_type'] ?? $event['event_name'] }}
                                                @else
                                                    {{ $event['event_name'] }}
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        @else
                            <p class="sidebar-panel__empty">История пуста</p>
                        @endif
                    </div>
                </section>

                {{-- Сообщения от акторов --}}
                <section class="sidebar-panel sidebar-panel--compact">
                    <header class="sidebar-panel__header">
                        <x-game.icon name="mail" class="sidebar-panel__icon" />
                        <h2 class="sidebar-panel__title">Сообщения от акторов</h2>
                    </header>
                    <div class="sidebar-panel__body">
                        <ul class="actor-message-list">
                            @if($hasDelayedMessages)
                                <li class="actor-message-row actor-message-row--unread">
                                    <button type="button" class="actor-message-row__button" wire:click="openDelayedModal" aria-label="Открыть новые сообщения">
                                        <x-game.icon name="mail" class="actor-message-row__icon" />
                                        <span class="actor-message-row__name">Новые сообщения</span>
                                        <span class="actor-message-row__count">{{ count($delayedMessages) }}</span>
                                    </button>
                                </li>
                            @endif
                            @foreach($sceneActors as $actor)
                                <li class="actor-message-row">
                                    <x-game.icon name="{{ $this->getActorIcon($actor['name']) }}" class="actor-message-row__icon" />
                                    <span class="actor-message-row__name">{{ $actor['name'] }}</span>
                                    <span class="actor-message-row__count" aria-label="Новых сообщений нет">—</span>
                                </li>
                            @endforeach
                        </ul>
                        @if(count($sceneActors) === 0 && ! $hasDelayedMessages)
                            <p class="sidebar-panel__empty">Нет акторов в этой сцене</p>
                        @endif
                    </div>
                </section>
            </div>
        </aside>
    </div>

    {{-- ========== ЭКРАН 10: ВХОДЯЩЕЕ СООБЩЕНИЕ ========== --}}
    @if($showMessageModal && !empty($currentModalMessage))
        @php
            $msgActor = $currentModalMessage['actor'] ?? $currentModalMessage['type'] ?? 'Актор';
            $msgSender = $currentModalMessage['sender'] ?? $currentModalMessage['source'] ?? 'Служебный канал';
            $msgSubject = $currentModalMessage['subject'] ?? 'Сообщение';
            $msgPriority = $currentModalMessage['priority'] ?? 'Средний';
            $msgTime = $currentModalMessage['time'] ?? $currentModalMessage['created_at'] ?? now()->format('d.m.Y H:i');
            $msgBody = $currentModalMessage['text'] ?? $currentModalMessage['message'] ?? '';
        @endphp
        <div class="incoming-message-backdrop" wire:click="closeMessageModal" x-on:keydown.escape.window="$wire.closeMessageModal()">
            <article class="incoming-message" role="dialog" aria-modal="true" aria-labelledby="incoming-message-title" wire:click.stop>
                <header class="incoming-message__header">
                    <x-game.icon name="mail" class="incoming-message__header-icon" />
                    <h2 id="incoming-message-title">Входящее сообщение</h2>
                    <button type="button" class="incoming-message__close" wire:click="closeMessageModal" aria-label="Закрыть входящее сообщение">×</button>
                </header>
                <div class="incoming-message__divider"></div>
                <div class="incoming-message__content">
                    <dl class="incoming-message__metadata">
                        <dt>Отправитель</dt><dd>{{ $msgActor }} актор</dd>
                        <dt>Источник</dt><dd>{{ $msgSender }}</dd>
                        <dt>Тема</dt><dd>{{ $msgSubject }}</dd>
                        <dt>Приоритет</dt><dd>{{ $msgPriority }}</dd>
                        <dt>Время</dt><dd>{{ $msgTime }}</dd>
                    </dl>
                    <section class="incoming-message__body">
                        @foreach(explode("\n", $msgBody) as $paragraph)
                            @if(trim($paragraph) !== '')
                                <p>{{ trim($paragraph) }}</p>
                            @endif
                        @endforeach
                    </section>
                </div>
                <footer class="incoming-message__footer">
                    <button type="button" class="document-action document-action--secondary" wire:click="closeMessageModal">Закрыть</button>
                    <button type="button" class="document-action document-action--primary" wire:click="closeMessageModal">Продолжить</button>
                </footer>
            </article>
        </div>
    @endif

    <!-- Модальное окно для отложенных сообщений -->
    @if($showDelayedModal && !empty($delayedMessages))
        <div
            wire:key="delayed-modal-{{ $game->id }}"
            style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;"
            wire:click.self="closeDelayedModal"
        >
            <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; max-height: 80vh; margin: 0 auto; padding: 24px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow-y: auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h3 style="font-size: 18px; font-weight: 600; color: #1f2937;">✉️ Сообщения</h3>
                    <button
                        wire:click="closeDelayedModal"
                        style="background: none; border: none; font-size: 24px; cursor: pointer; color: #9ca3af;"
                    >
                        ×
                    </button>
                </div>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @foreach($delayedMessages as $index => $message)
                        <div style="padding: 12px 16px; background: #f3f4f6; border-radius: 8px; border-left: 4px solid #3b82f6;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                <span style="font-size: 12px; font-weight: 600; color: #3b82f6;">
                                    {{ $message['type'] ?? 'Сообщение' }}
                                </span>
                                <span style="font-size: 10px; color: #9ca3af;">
                                    {{ $message['created_at'] ?? now()->format('d.m.Y H:i') }}
                                </span>
                            </div>
                            <p style="color: #374151; font-size: 14px; line-height: 1.5; margin: 0;">
                                {{ $message['message'] }}
                            </p>
                        </div>

                        @if($index < count($delayedMessages) - 1)
                            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 4px 0;">
                        @endif
                    @endforeach
                </div>

                <button
                    wire:click="closeDelayedModal"
                    style="margin-top: 20px; padding: 10px 24px; background: #3b82f6; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; width: 100%;"
                >
                    Понятно
                </button>
            </div>
        </div>
    @endif
    {{-- Модальное окно предпросмотра материала (экран 06) --}}
    @if($activeMaterial = $this->getActiveMaterial())
        <div class="material-modal-backdrop" wire:click="closeMaterialModal" x-on:keydown.escape.window="$wire.closeMaterialModal()">
            <section class="material-modal" role="dialog" aria-modal="true" aria-labelledby="material-modal-title" wire:click.stop>
                <h2 id="material-modal-title">Предпросмотр документа</h2>
                <h3>{{ $activeMaterial['title'] }}</h3>
                <p>Полноценное содержимое документа будет подключено при интеграции с рабочей системой.</p>
                <p class="material-modal__metadata">{{ $activeMaterial['fileType'] }} · {{ $activeMaterial['categoryLabel'] }} · {{ implode(' · ', $activeMaterial['metadata']) }}</p>
                <div class="material-modal__actions">
                    <button type="button" class="document-action document-action--secondary" wire:click="closeMaterialModal">Закрыть</button>
                    @php $studiedActive = in_array($activeMaterial['id'], $studiedMaterialIds, true); @endphp
                    <button type="button" class="document-action document-action--primary" wire:click="markMaterialStudied" @disabled($studiedActive)>
                        {{ $studiedActive ? 'Изучено' : 'Отметить как изученное' }}
                    </button>
                </div>
            </section>
        </div>
    @endif
    {{-- Модальное окно просмотра справки (экран 07) --}}
    @if($activeReference = $this->getActiveReference())
        <div class="material-modal-backdrop reference-modal-backdrop" wire:click="closeReferenceModal" x-on:keydown.escape.window="$wire.closeReferenceModal()">
            <section class="material-modal reference-modal" role="dialog" aria-modal="true" aria-labelledby="reference-modal-title" wire:click.stop>
                <h2 id="reference-modal-title">Просмотр справки</h2>
                <h3>{{ $activeReference['title'] }}</h3>
                <p>Полное содержание справки будет подключено при интеграции с рабочей системой.</p>
                <p class="material-modal__metadata">{{ $activeReference['categoryLabel'] }} · {{ $activeReference['source'] }} · {{ implode(' · ', $activeReference['metadata']) }}</p>
                <div class="material-modal__actions">
                    <button type="button" class="document-action document-action--secondary" wire:click="closeReferenceModal">Закрыть</button>
                    @php $studiedRef = in_array($activeReference['id'], $studiedReferenceIds, true); @endphp
                    <button type="button" class="document-action document-action--primary" wire:click="markReferenceStudied" @disabled($studiedRef)>
                        {{ $studiedRef ? 'Изучено' : 'Отметить как изученную' }}
                    </button>
                </div>
            </section>
        </div>
    @endif
    {{-- Модальное окно просмотра материала СМИ (экран 08) --}}
    @if($activeMedia = $this->getActiveMedia())
        <div class="material-modal-backdrop media-modal-backdrop" wire:click="closeMediaModal" x-on:keydown.escape.window="$wire.closeMediaModal()">
            <section class="material-modal media-modal" role="dialog" aria-modal="true" aria-labelledby="media-modal-title" wire:click.stop>
                <h2 id="media-modal-title">{{ $activeMedia['type'] === 'request' ? 'Запрос редакции' : 'Просмотр публикации' }}</h2>
                <h3>{{ $activeMedia['title'] }}</h3>
                <p class="media-modal__source">{{ $activeMedia['categoryLabel'] }} · {{ $activeMedia['source'] }}</p>
                <p>{{ $activeMedia['description'] }}</p>
                <p class="material-modal__metadata">{{ implode(' · ', $activeMedia['metadata']) }} · Тон: {{ $activeMedia['tone'] }} · Резонанс: {{ $activeMedia['resonance'] }} · {{ $activeMedia['status'] }}</p>
                <div class="material-modal__actions">
                    <button type="button" class="document-action document-action--secondary" wire:click="closeMediaModal">Закрыть</button>
                    @php $reviewedActive = in_array($activeMedia['id'], $reviewedMediaIds, true); @endphp
                    <button type="button" class="document-action document-action--primary" wire:click="markMediaReviewed" @disabled($reviewedActive)>
                        {{ $reviewedActive ? 'Изучено' : 'Отметить как изученное' }}
                    </button>
                </div>
            </section>
        </div>
    @endif
    {{-- Модальное окно карточки обращения (экран 09) --}}
    @if($activeAppeal = $this->getActiveAppeal())
        <div class="material-modal-backdrop appeal-modal-backdrop" wire:click="closeAppealModal" x-on:keydown.escape.window="$wire.closeAppealModal()">
            <section class="material-modal appeal-modal" role="dialog" aria-modal="true" aria-labelledby="appeal-modal-title" wire:click.stop>
                <h2 id="appeal-modal-title">Карточка обращения</h2>
                <h3>{{ $activeAppeal['title'] }}</h3>
                <p class="appeal-modal__source">{{ $activeAppeal['categoryLabel'] }} · {{ $activeAppeal['source'] }}</p>
                <p>{{ $activeAppeal['description'] }}</p>
                <p class="appeal-modal__address">Адрес: {{ $activeAppeal['address'] }}</p>
                <p class="material-modal__metadata">{{ implode(' · ', $activeAppeal['metadata']) }} · Приоритет: {{ $activeAppeal['priority'] }} · Статус: {{ $activeAppeal['status'] }}</p>
                <div class="material-modal__actions">
                    <button type="button" class="document-action document-action--secondary" wire:click="closeAppealModal">Закрыть</button>
                    @php $reviewedActive = in_array($activeAppeal['id'], $reviewedAppealIds, true); @endphp
                    <button type="button" class="document-action document-action--primary" wire:click="markAppealReviewed" @disabled($reviewedActive)>
                        {{ $reviewedActive ? 'Изучено' : 'Отметить как изученное' }}
                    </button>
                </div>
            </section>
        </div>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                // Слушаем события отладки
                Livewire.on('console-log', (data) => {
                    const logData = data[0] || data;

                    console.log('%c' + '='.repeat(60), 'color: #888; font-size: 12px;');
                    console.log('%c' + logData.title, 'color: #2196F3; font-size: 14px; font-weight: bold;');
                    console.log('%c' + '='.repeat(60), 'color: #888; font-size: 12px;');

                    Object.keys(logData).forEach(key => {
                        if (key === 'type' || key === 'title') return;

                        console.group(`%c📌 ${key}`, 'color: #FF5722; font-weight: bold;');

                        if (typeof logData[key] === 'object') {
                            console.log(JSON.stringify(logData[key], null, 2));
                        } else {
                            console.log(logData[key]);
                        }
                        console.groupEnd();
                    });

                    console.log('%c' + '='.repeat(60), 'color: #888; font-size: 12px;');
                    console.log('');
                });
            });
        </script>
    @endpush
</div>
@endif
