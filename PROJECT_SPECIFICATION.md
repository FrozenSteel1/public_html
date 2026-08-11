# 📋 Спецификация проекта: public_html

> **Дата генерации:** 2026-08-10 16:05:33

---

## 🏗️ Общая информация

- **Название проекта:** Laravel
- **Версия Laravel:** 13.7.0
- **Версия PHP:** 8.5.1
- **Окружение:** local
- **Режим отладки:** ✅ Включен
- **URL приложения:** http://localhost

## 📊 Сводная статистика

| Показатель | Значение |
| :--- | :--- |
| **Всего роутов** | 57 |
| **Контроллеры** | 13 |
| **Модели** | 14 |
| **Миграции** | 25 |
| **Шаблоны (views)** | 66 |
| **Тесты** | 16 |

## 🌍 Информация о среде

- **Веб-сервер:** Не определено
- **ОС:** WINNT
- **Версия PHP:** 8.5.1
- **Лимит памяти:** 1024M
- **Макс. время выполнения:** 0 сек

## 🛣️ Маршруты приложения

**Всего маршрутов:** 57

| Метод | URI | Имя | Action |
| :--- | :--- | :--- | :--- |
| GET | `/login` | `login` | `Laravel\Fortify\Http\Controllers\AuthenticatedSessionController@create` |
| POST | `/login` | `login.store` | `Laravel\Fortify\Http\Controllers\AuthenticatedSessionController@store` |
| POST | `/logout` | `logout` | `Laravel\Fortify\Http\Controllers\AuthenticatedSessionController@destroy` |
| GET | `/forgot-password` | `password.request` | `Laravel\Fortify\Http\Controllers\PasswordResetLinkController@create` |
| GET | `/reset-password/{token}` | `password.reset` | `Laravel\Fortify\Http\Controllers\NewPasswordController@create` |
| POST | `/forgot-password` | `password.email` | `Laravel\Fortify\Http\Controllers\PasswordResetLinkController@store` |
| POST | `/reset-password` | `password.update` | `Laravel\Fortify\Http\Controllers\NewPasswordController@store` |
| GET | `/register` | `register` | `Laravel\Fortify\Http\Controllers\RegisteredUserController@create` |
| POST | `/register` | `register.store` | `Laravel\Fortify\Http\Controllers\RegisteredUserController@store` |
| PUT | `/user/profile-information` | `user-profile-information.update` | `Laravel\Fortify\Http\Controllers\ProfileInformationController@update` |
| PUT | `/user/password` | `user-password.update` | `Laravel\Fortify\Http\Controllers\PasswordController@update` |
| GET | `/user/confirm-password` | `password.confirm` | `Laravel\Fortify\Http\Controllers\ConfirmablePasswordController@show` |
| GET | `/user/confirmed-password-status` | `password.confirmation` | `Laravel\Fortify\Http\Controllers\ConfirmedPasswordStatusController@show` |
| POST | `/user/confirm-password` | `password.confirm.store` | `Laravel\Fortify\Http\Controllers\ConfirmablePasswordController@store` |
| GET | `/two-factor-challenge` | `two-factor.login` | `Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController@create` |
| POST | `/two-factor-challenge` | `two-factor.login.store` | `Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController@store` |
| POST | `/user/two-factor-authentication` | `two-factor.enable` | `Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController@store` |
| POST | `/user/confirmed-two-factor-authentication` | `two-factor.confirm` | `Laravel\Fortify\Http\Controllers\ConfirmedTwoFactorAuthenticationController@store` |
| DELETE | `/user/two-factor-authentication` | `two-factor.disable` | `Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController@destroy` |
| GET | `/user/two-factor-qr-code` | `two-factor.qr-code` | `Laravel\Fortify\Http\Controllers\TwoFactorQrCodeController@show` |
| GET | `/user/two-factor-secret-key` | `two-factor.secret-key` | `Laravel\Fortify\Http\Controllers\TwoFactorSecretKeyController@show` |
| GET | `/user/two-factor-recovery-codes` | `two-factor.recovery-codes` | `Laravel\Fortify\Http\Controllers\RecoveryCodeController@index` |
| POST | `/user/two-factor-recovery-codes` | `two-factor.regenerate-recovery-codes` | `Laravel\Fortify\Http\Controllers\RecoveryCodeController@store` |
| GET | `/user/profile` | `profile.show` | `Laravel\Jetstream\Http\Controllers\Livewire\UserProfileController@show` |
| GET | `/sanctum/csrf-cookie` | `sanctum.csrf-cookie` | `Laravel\Sanctum\Http\Controllers\CsrfCookieController@show` |
| POST | `/livewire/update` | `default.livewire.update` | `Livewire\Mechanisms\HandleRequests\HandleRequests@handleUpdate` |
| GET | `/livewire/livewire.js` | — | `Livewire\Mechanisms\FrontendAssets\FrontendAssets@returnJavaScriptAsFile` |
| GET | `/livewire/livewire.min.js.map` | — | `Livewire\Mechanisms\FrontendAssets\FrontendAssets@maps` |
| POST | `/livewire/upload-file` | `livewire.upload-file` | `Livewire\Features\SupportFileUploads\FileUploadController@handle` |
| GET | `/livewire/preview-file/{filename}` | `livewire.preview-file` | `Livewire\Features\SupportFileUploads\FilePreviewController@handle` |
| GET | `/api/user` | — | `Closure` |
| GET | `/up` | — | `Closure` |
| GET | `/` | — | `Closure` |
| GET | `/dashboard` | `player.dashboard` | `App\Livewire\PlayerDashboard` |
| GET | `/admin` | `admin.dashboard` | `App\Livewire\AdminDashboard` |
| GET | `/admin/companies` | `admin.companies` | `App\Livewire\CompaniesManager` |
| GET | `/admin/scenarios` | `admin.scenarios` | `App\Livewire\ScenariosManager` |
| GET | `/admin/scenes` | `admin.scenes` | `App\Livewire\ScenesManager` |
| GET | `/admin/events` | `admin.events` | `App\Livewire\EventsManager` |
| GET | `/admin/effect-types` | `admin.effect-types` | `App\Livewire\EffectTypesManager` |
| GET | `/admin/actors` | `admin.actors` | `App\Livewire\ActorsManager` |
| GET | `/admin/company-scenarios` | `admin.company-scenarios` | `App\Livewire\CompanyScenarioManager` |
| GET | `/admin/presets` | `admin.presets` | `App\Livewire\PresetsManager` |
| GET | `/admin/games` | `admin.games` | `App\Livewire\GamesManager` |
| GET | `/scenarios` | `scenarios` | `App\Livewire\ScenarioSelector` |
| GET | `/my-games` | `user.games` | `App\Livewire\PlayerDashboard` |
| GET | `/game/results/{gameId}` | `game.results` | `App\Livewire\GameResults` |
| GET | `/game/play/{scenarioId}/{difficulty?}` | `game.play` | `App\Livewire\GamePlay` |
| GET | `/game/continue/{gameId}` | `game.continue` | `App\Livewire\GamePlay` |
| GET | `/storage/{path}` | `storage.local` | `Closure` |
| PUT | `/storage/{path}` | `storage.local.upload` | `Closure` |

## 📂 Структура каталогов и файлов

```text
📁 /
└── 📁 app/
        ├── 📁 Actions/
            ├── 📁 Fortify/
                ├── 📄 CreateNewUser.php
                ├── 📄 PasswordValidationRules.php
                ├── 📄 ResetUserPassword.php
                ├── 📄 UpdateUserPassword.php
                ├── 📄 UpdateUserProfileInformation.php
            ├── 📁 Jetstream/
                ├── 📄 DeleteUser.php
        ├── 📁 Console/
            ├── 📁 Commands/
                ├── 📄 ClearGames.php
                ├── 📄 CreateProjectSpecCommand.php
                ├── 📄 TestGame.php
        ├── 📁 Http/
            ├── 📁 Controllers/
                ├── 📄 ActorController.php
                ├── 📄 ChoiceController.php
                ├── 📄 CompanyController.php
                ├── 📄 Controller.php
                ├── 📄 EffectController.php
                ├── 📄 EffectTypeController.php
                ├── 📄 EventController.php
                ├── 📄 GameController.php
                ├── 📄 GameHistoryController.php
                ├── 📄 PresetController.php
                ├── 📄 ScenarioController.php
                ├── 📄 SceneController.php
                ├── 📄 TestController.php
            ├── 📁 Middleware/
                ├── 📄 CheckAdmin.php
            ├── 📁 Requests/
                ├── 📄 StoreActorRequest.php
                ├── 📄 StoreChoiceRequest.php
                ├── 📄 StoreCompanyRequest.php
                ├── 📄 StoreEffectRequest.php
                ├── 📄 StoreEffectTypeRequest.php
                ├── 📄 StoreEventRequest.php
                ├── 📄 StoreGameHistoryRequest.php
                ├── 📄 StoreGameRequest.php
                ├── 📄 StorePresetRequest.php
                ├── 📄 StoreScenarioRequest.php
                ├── 📄 StoreSceneRequest.php
                ├── 📄 UpdateActorRequest.php
                ├── 📄 UpdateChoiceRequest.php
                ├── 📄 UpdateCompanyRequest.php
                ├── 📄 UpdateEffectRequest.php
                ├── 📄 UpdateEffectTypeRequest.php
                ├── 📄 UpdateEventRequest.php
                ├── 📄 UpdateGameHistoryRequest.php
                ├── 📄 UpdateGameRequest.php
                ├── 📄 UpdatePresetRequest.php
                ├── 📄 UpdateScenarioRequest.php
                ├── 📄 UpdateSceneRequest.php
        ├── 📁 Livewire/
            ├── 📄 ActorsManager.php
            ├── 📄 AdminDashboard.php
            ├── 📄 CompaniesManager.php
            ├── 📄 CompanyScenarioManager.php
            ├── 📄 EffectTypesManager.php
            ├── 📄 EventsManager.php
            ├── 📄 GamePlay.php
            ├── 📄 GameResults.php
            ├── 📄 GamesManager.php
            ├── 📄 PlayerDashboard.php
            ├── 📄 PresetsManager.php
            ├── 📄 ScenarioSelector.php
            ├── 📄 ScenariosManager.php
            ├── 📄 ScenesManager.php
            ├── 📄 UserGames.php
        ├── 📁 Models/
            ├── 📄 Actor.php
            ├── 📄 Choice.php
            ├── 📄 Company.php
            ├── 📄 CompanyScenario.php
            ├── 📄 Effect.php
            ├── 📄 EffectType.php
            ├── 📄 Event.php
            ├── 📄 Game.php
            ├── 📄 GameHistory.php
            ├── 📄 ParameterDefinition.php
            ├── 📄 Preset.php
            ├── 📄 Scenario.php
            ├── 📄 Scene.php
            ├── 📄 User.php
        ├── 📁 Policies/
            ├── 📄 ActorPolicy.php
            ├── 📄 ChoicePolicy.php
            ├── 📄 CompanyPolicy.php
            ├── 📄 EffectPolicy.php
            ├── 📄 EffectTypePolicy.php
            ├── 📄 EventPolicy.php
            ├── 📄 GameHistoryPolicy.php
            ├── 📄 GamePolicy.php
            ├── 📄 PresetPolicy.php
            ├── 📄 ScenarioPolicy.php
            ├── 📄 ScenePolicy.php
        ├── 📁 Providers/
            ├── 📄 AppServiceProvider.php
            ├── 📄 FortifyServiceProvider.php
            ├── 📄 JetstreamServiceProvider.php
        ├── 📁 Services/
            ├── 📁 Effects/
                ├── 📄 DelayedMessageHandler.php
                ├── 📄 EffectHandlerInterface.php
                ├── 📄 EffectManager.php
                ├── 📄 MessageHandler.php
                ├── 📄 ParameterChangeHandler.php
                ├── 📄 ParameterDecreaseHandler.php
                ├── 📄 SceneTransitionHandler.php
                ├── 📄 SupportHandler.php
            ├── 📄 GameService.php
        ├── 📁 View/
            ├── 📁 Components/
                ├── 📄 AppLayout.php
                ├── 📄 GuestLayout.php
└── 📁 bootstrap/
        ├── 📁 cache/
            ├── 📄 packages.php
            ├── 📄 services.php
        ├── 📄 app.php
        ├── 📄 providers.php
└── 📁 config/
        ├── 📄 app.php
        ├── 📄 auth.php
        ├── 📄 cache.php
        ├── 📄 database.php
        ├── 📄 filesystems.php
        ├── 📄 fortify.php
        ├── 📄 jetstream.php
        ├── 📄 livewire.php
        ├── 📄 logging.php
        ├── 📄 mail.php
        ├── 📄 queue.php
        ├── 📄 sanctum.php
        ├── 📄 services.php
        ├── 📄 session.php
└── 📁 database/
        ├── 📁 factories/
            ├── 📄 ActorFactory.php
            ├── 📄 ChoiceFactory.php
            ├── 📄 CompanyFactory.php
            ├── 📄 EffectFactory.php
            ├── 📄 EffectTypeFactory.php
            ├── 📄 EventFactory.php
            ├── 📄 GameFactory.php
            ├── 📄 GameHistoryFactory.php
            ├── 📄 PresetFactory.php
            ├── 📄 ScenarioFactory.php
            ├── 📄 SceneFactory.php
            ├── 📄 UserFactory.php
        ├── 📁 migrations/
            ├── 📄 0001_01_01_000000_create_users_table.php
            ├── 📄 0001_01_01_000001_create_cache_table.php
            ├── 📄 0001_01_01_000002_create_jobs_table.php
            ├── 📄 2026_04_25_121708_add_two_factor_columns_to_users_table.php
            ├── 📄 2026_04_25_121726_create_personal_access_tokens_table.php
            ├── 📄 2026_04_27_161258_create_companies_table.php
            ├── 📄 2026_04_27_174549_create_scenarios_table.php
            ├── 📄 2026_04_27_175020_create_company_scenario_table.php
            ├── 📄 2026_04_27_180544_create_presets_table.php
            ├── 📄 2026_04_27_182311_create_scenes_table.php
            ├── 📄 2026_04_27_183717_create_actors_table.php
            ├── 📄 2026_04_27_184431_create_events_table.php
            ├── 📄 2026_04_27_184499_create_effect_types_table.php
            ├── 📄 2026_04_27_184500_create_effects_table.php
            ├── 📄 2026_04_27_192056_create_games_table.php
            ├── 📄 2026_04_27_192750_create_game_histories_table.php
            ├── 📄 2026_04_27_194613_create_choices_table.php
            ├── 📄 2026_05_11_172639_add_company_id_to_scenarios_table.php
            ├── 📄 2026_07_18_140503_add_game_columns_to_games_table.php
            ├── 📄 2026_07_18_153950_add_indexes_for_performance.php
            ├── 📄 2026_07_22_154810_create_parameter_definitions_table.php
            ├── 📄 2026_07_22_183710_add_time_limit_to_scenes_table.php
            ├── 📄 2026_07_23_211543_add_source_column_to_game_histories_table.php
            ├── 📄 2026_07_23_215534_add_scene_id_to_game_histories_table.php
            ├── 📄 2026_07_27_164926_add_role_to_users_table.php
        ├── 📁 seeders/
            ├── 📄 ActorSeeder.php
            ├── 📄 ChoiceSeeder.php
            ├── 📄 CompaniesSeeder.php
            ├── 📄 CompanySeeder.php
            ├── 📄 DatabaseSeeder.php
            ├── 📄 EffectSeeder.php
            ├── 📄 EffectTypeSeeder.php
            ├── 📄 EffectTypesSeeder.php
            ├── 📄 EventSeeder.php
            ├── 📄 GameHistorySeeder.php
            ├── 📄 GameSeeder.php
            ├── 📄 MessageEffectTypesSeeder.php
            ├── 📄 ParameterDefinitionsSeeder.php
            ├── 📄 PresetSeeder.php
            ├── 📄 ScenarioSeeder.php
            ├── 📄 SceneSeeder.php
        ├── 📄 database.sqlite
└── 📁 public/
        ├── 📁 build/
            ├── 📁 assets/
                ├── 📄 app-34mOoJaZ.js
                ├── 📄 app-BSwAYXOD.css
            ├── 📄 manifest.json
        ├── 📁 storage/
        ├── 📄 favicon.ico
        ├── 📄 index.php
        ├── 📄 robots.txt
        ├── 📄 storage
└── 📁 resources/
        ├── 📁 css/
            ├── 📄 app.css
        ├── 📁 js/
            ├── 📄 app.js
        ├── 📁 markdown/
            ├── 📄 policy.md
            ├── 📄 terms.md
        ├── 📁 views/
            ├── 📁 api/
                ├── 📄 api-token-manager.blade.php
                ├── 📄 index.blade.php
            ├── 📁 auth/
                ├── 📄 confirm-password.blade.php
                ├── 📄 forgot-password.blade.php
                ├── 📄 login.blade.php
                ├── 📄 register.blade.php
                ├── 📄 reset-password.blade.php
                ├── 📄 two-factor-challenge.blade.php
                ├── 📄 verify-email.blade.php
            ├── 📁 components/
                ├── 📄 action-message.blade.php
                ├── 📄 action-section.blade.php
                ├── 📄 application-logo.blade.php
                ├── 📄 application-mark.blade.php
                ├── 📄 authentication-card-logo.blade.php
                ├── 📄 authentication-card.blade.php
                ├── 📄 banner.blade.php
                ├── 📄 button.blade.php
                ├── 📄 checkbox.blade.php
                ├── 📄 confirmation-modal.blade.php
                ├── 📄 confirms-password.blade.php
                ├── 📄 danger-button.blade.php
                ├── 📄 dialog-modal.blade.php
                ├── 📄 dropdown-link.blade.php
                ├── 📄 dropdown.blade.php
                ├── 📄 form-section.blade.php
                ├── 📄 input-error.blade.php
                ├── 📄 input.blade.php
                ├── 📄 label.blade.php
                ├── 📄 modal.blade.php
                ├── 📄 nav-link.blade.php
                ├── 📄 responsive-nav-link.blade.php
                ├── 📄 secondary-button.blade.php
                ├── 📄 section-border.blade.php
                ├── 📄 section-title.blade.php
                ├── 📄 switchable-team.blade.php
                ├── 📄 validation-errors.blade.php
                ├── 📄 welcome.blade.php
            ├── 📁 emails/
                ├── 📄 team-invitation.blade.php
            ├── 📁 layouts/
                ├── 📄 app.blade.php
                ├── 📄 guest.blade.php
            ├── 📁 livewire/
                ├── 📄 actors-manager.blade.php
                ├── 📄 admin-dashboard.blade.php
                ├── 📄 companies-manager.blade.php
                ├── 📄 company-scenario-manager.blade.php
                ├── 📄 effect-types-manager.blade.php
                ├── 📄 events-manager.blade.php
                ├── 📄 game-play.blade.php
                ├── 📄 game-results.blade.php
                ├── 📄 games-manager.blade.php
                ├── 📄 player-dashboard.blade.php
                ├── 📄 presets-manager.blade.php
                ├── 📄 scenario-selector.blade.php
                ├── 📄 scenarios-manager.blade.php
                ├── 📄 scenes-manager.blade.php
                ├── 📄 user-games.blade.php
            ├── 📁 profile/
                ├── 📄 delete-user-form.blade.php
                ├── 📄 logout-other-browser-sessions-form.blade.php
                ├── 📄 show.blade.php
                ├── 📄 two-factor-authentication-form.blade.php
                ├── 📄 update-password-form.blade.php
                ├── 📄 update-profile-information-form.blade.php
            ├── 📄 dashboard.blade.php
            ├── 📄 navigation-menu.blade.php
            ├── 📄 policy.blade.php
            ├── 📄 terms.blade.php
            ├── 📄 welcome.blade.php
└── 📁 routes/
        ├── 📄 api.php
        ├── 📄 console.php
        ├── 📄 web.php
└── 📁 storage/
        ├── 📁 app/
            ├── 📁 private/
            ├── 📁 public/
        ├── 📁 debugbar/
            ├── 📄 01KYJ6ZTW20TCY7T6WVMT1TAZT.json
            ├── 📄 01KYJ6ZVHRYDKT1YR5RCPPGR61.json
            ├── 📄 01KYJ707R55GGNRSG6E2HQKBF5.json
            ├── 📄 01KYJ708PTQR5CB51E9WZZAW2T.json
            ├── 📄 01KYJ70GG889XN14KND6FKC00X.json
            ├── 📄 01KYJ70P8H6EZ8S23Q3XGMVWP1.json
            ├── 📄 01KYJ70SY7YQV5TH9JA7YHQ478.json
            ├── 📄 01KYJ70TVCDK22CG67D55Q6NPT.json
            ├── 📄 01KYJ70ZP7GK4J2SK5ZERXJBEN.json
            ├── 📄 01KYJ71278NRMRFEFV4BQD221Z.json
            ├── 📄 01KYJ714R47AKQRS37FAC02A9V.json
            ├── 📄 01KYJ718HKMW5NMTA8PF4FH6ZP.json
            ├── 📄 01KYJ71A6F2427YA4TVBF25SKF.json
            ├── 📄 01KYJ71ANEAYB1KMJ25ZTGKJE2.json
            ├── 📄 01KYJ71FTHCZSTQD9P0QP24XDJ.json
            ├── 📄 01KYJ71H79S8MG9S1YCSQRHGMJ.json
            ├── 📄 01KYJ71KSR4B4BR3T7MSGY52FS.json
            ├── 📄 01KYJ71S51KWM3M77B4VWJ8122.json
            ├── 📄 01KYJ7KA8GW0D5C08W4K0EEJG1.json
            ├── 📄 01KYJ84XD2ZYJZKYDPRMJ9J35X.json
            ├── 📄 01KYJ84XGBZZGP32MZRMB9EJY2.json
            ├── 📄 01KYJ84ZQAQQR3T7VXJ4WAB8KB.json
            ├── 📄 01KYJ855E961T102WAGBE5H7CA.json
            ├── 📄 01KYJ85742NAD223FNDFQCVG9A.json
            ├── 📄 01KYJ87HDT572Z2M4RX9CEMX1V.json
            ├── 📄 01KYJ87KAHFBSKFYH5723X9PEN.json
            ├── 📄 01KYJ87Q9HCBXRJPTQCRM50V3V.json
            ├── 📄 01KYJ87VH9TH1F7PE525JGVNHX.json
            ├── 📄 01KYJ8A6QF428AVJBJDDZK498N.json
            ├── 📄 01KYJ8AA3NP80H3FEBR8K6Y31Z.json
            ├── 📄 01KYJ8CAAN23AJ2RCK3RCCJMF8.json
            ├── 📄 01KYJ8CAFRY1HTMCESF5D3R2AP.json
            ├── 📄 01KYJ8CMKPSGMFMF04GYRN3VF1.json
            ├── 📄 01KYJ8CNRXPW3ZZHY824644XA6.json
            ├── 📄 01KYJ8CQEZM98KWXE3XTV4TKRP.json
            ├── 📄 01KYJ8K02AG063MKJJ81R61PPD.json
            ├── 📄 01KYJ8N69SFRNR126646YGFXDR.json
            ├── 📄 01KYJ8PD1J5ADPZRVKRDG2HH11.json
            ├── 📄 01KYJ8PRT3DVWMCWMETE6ATK1W.json
            ├── 📄 01KYJ8PWRVNSFX3P5MZY15TR99.json
            ├── 📄 01KYJ8YNF24C7RCDW7AGQJC0YD.json
            ├── 📄 01KYJ8YR590BSQFKKT0VR3TVWZ.json
            ├── 📄 01KYJ8YWTMJ80TGNYFNNSW20V6.json
            ├── 📄 01KYJ8Z12ADT5KMNVHDVQF006G.json
            ├── 📄 01KYJ8Z2VNWFG4DWWGVE0T6DG0.json
            ├── 📄 01KYJ8Z3RJE79V39SBTQGJT4Y6.json
            ├── 📄 01KYJ8Z5DCN658HGZJPERJVB8Q.json
            ├── 📄 01KYJ8Z6EG0W1X63F70MJHDG8H.json
            ├── 📄 01KYJ8Z8FV3TA0DWS74QZB2T5M.json
            ├── 📄 01KYJ8ZAHJQV1YSMGNPZGR23VB.json
            ├── 📄 01KYJ8ZC1DESMEZSNTF2SXH473.json
            ├── 📄 01KYJ8ZW6XZ08R4Q3T9G0Q73V2.json
            ├── 📄 01KYJ8ZYAGDJ0AKEWSE316N2S8.json
            ├── 📄 01KYJ900HHEBJTH980EBZTD73Y.json
            ├── 📄 01KYJ90344KWX7JF8CTWFB43P1.json
            ├── 📄 01KYJ9037CG4HM3GDA0QN3F6HW.json
            ├── 📄 01KYJ906K4FNBKFFYVA37MJ82R.json
            ├── 📄 01KYJ908PRHNQ6E119GX3EGF3J.json
            ├── 📄 01KYJ90AQW7WSRKPT6043RC03F.json
            ├── 📄 01KYJ90P4PD542TYCKKY3DY9AA.json
            ├── 📄 01KYJ90P7HDWV7PPE9J30F7S51.json
            ├── 📄 01KYJ90QXX4K29TNP7WVMWBCNE.json
            ├── 📄 01KYJ916EH5KKFHFY78J13R4XN.json
            ├── 📄 01KYJ916GMZ8P2Z4WTQXEDZKK8.json
            ├── 📄 01KYJ91A36E7HHEKJ5BFE85ZTC.json
            ├── 📄 01KYJ91A6G9E2BJ69YBRTA3AYD.json
            ├── 📄 01KYJ91BPNNSRRXKS2JZZKH3SX.json
            ├── 📄 01KYJ91BVA18DC917CHD5PQBP4.json
            ├── 📄 01KYJ91DS9F18G9HMA8JZPCZ0Y.json
            ├── 📄 01KYJ91EY9VJW8VNGPC17E0D45.json
            ├── 📄 01KYJ91K152TK2Y54Z8RH40PR5.json
            ├── 📄 01KYJ92NJCFAD45D99VGRZVGQJ.json
            ├── 📄 01KYJ92PMWW32SQHAHXH8XMZHN.json
            ├── 📄 01KYJ92QPFEPETZDRP54ZSDJAV.json
            ├── 📄 01KYJ92RMS7G4J174W3WBHT9CX.json
            ├── 📄 01KYJ92SPXX0F60YXX9FVH1P61.json
            ├── 📄 01KYJ92TGH5MNDCBKZ0CJ4J6ZN.json
            ├── 📄 01KYJ92TPGC0XRB1S99WVF3HDJ.json
            ├── 📄 01KYJ931KFD3DD5HCN269MB880.json
            ├── 📄 01KYJ934SA6C1VZFH2QTQ0FPJP.json
            ├── 📄 01KYJ9355AWGCF0PYBQ8ARENC7.json
            ├── 📄 01KYJ9377P05MM2HPMV8TF6TNK.json
            ├── 📄 01KYJ93HGDZZ61J673Z3KV8FKA.json
            ├── 📄 01KYJ95Z2CYXSGZ7SFSP0S9X2J.json
            ├── 📄 01KYJ95Z593WACWF81PF3ZNHSB.json
            ├── 📄 01KYJ961HVSN1QBFY9MKA6R516.json
            ├── 📄 01KYJ967SHNYKF5897407YRN2T.json
            ├── 📄 01KYJ9683EMFVM10DFQ2SA29GY.json
            ├── 📄 01KYJ96EJMAW6G33YVAQR9WZVH.json
            ├── 📄 01KYJB7TPTCKXHTP4FX63VDGSW.json
            ├── 📄 01KYJB7VNMPBRHAT5E25P2PJHT.json
            ├── 📄 01KYJB7YEWQRNWMZ7ZM7CJGGBS.json
            ├── 📄 01KYJB7YK0GA064Q2R887CWKM4.json
            ├── 📄 01KYJB7ZVZE4XX26YB5DBDC77R.json
            ├── 📄 01KYJB81D2CP0F3P2M24EETYAZ.json
            ├── 📄 01KYJB81JPJ5NVMF3NGZV6KGY8.json
            ├── 📄 01KYJB83PZ9N5X3B10E7R4Z35K.json
            ├── 📄 01KYJB85KD71Y60PZMDWSBZWX3.json
            ├── 📄 01KYJB94V6VNPN78T30PWG2VJY.json
            ├── 📄 01KYJBA2DJW9KR4PXZFED2FT32.json
            ├── 📄 01KYJBA361JBMHDM40CJKMJEKZ.json
            ├── 📄 01KYJBA38HABGMYNTRWXQVQJZ6.json
            ├── 📄 01KYJBA59H6Z6M392TDKCWQXB1.json
            ├── 📄 01KYJBA6QBX06KH5QXT15MJB3H.json
            ├── 📄 01KYJBB59P1QSCGBEX1VKSSXZ1.json
            ├── 📄 01KYJBD0XTTMF87WHWFTPZZDES.json
            ├── 📄 01KYJBDN1265YGQA8778G1AY0E.json
            ├── 📄 01KYJBDN3J1HNM5PYM5CRDCSRW.json
            ├── 📄 01KYJBDQ4YPZHZKXBAKPNG2NV5.json
            ├── 📄 01KYJBDREFV1V4W6GQ2X2ZMWPS.json
            ├── 📄 01KYJBEQKH4QKN78M7CETYV8XJ.json
            ├── 📄 01KYJBFYVV96E4YQKPN1AGETHG.json
            ├── 📄 01KYJBG00G22BSVXVBT71V05MN.json
            ├── 📄 01KYJBG02XFD2BR7W28B8356FQ.json
            ├── 📄 01KYJBG2BA93KSBZSDS9M4EZNS.json
            ├── 📄 01KYJBG3DW1FCB15VMGJ8GDAZH.json
            ├── 📄 01KYJBH2SNSDNK72XXAX0CF8Q4.json
            ├── 📄 01KYJBNMD0ZHXGWTD5KXJKBHZ1.json
            ├── 📄 01KYJBNNDY5R5WPNPYH9Q4GAX1.json
            ├── 📄 01KYJBNNHTWZBQY1BVBER6PE3N.json
            ├── 📄 01KYJBNQY45Y94KYYS4TKZP5HY.json
            ├── 📄 01KYJBNSBZZW200X5N37EHS7S5.json
            ├── 📄 01KYJBPRE7QX3TA9SR067V53ZQ.json
            ├── 📄 01KYJBQQMHZSH6FWS5VM91KK2Z.json
            ├── 📄 01KYJBRPX3SHPXCP4SXGAGZ0PJ.json
            ├── 📄 01KYJBSXAC9KRS37P535PWJYFQ.json
            ├── 📄 01KYJBSXE5TTVSHFZAD9DVEEWX.json
            ├── 📄 01KYJBSZS86QV0MNQ7DZ0GHYHV.json
            ├── 📄 01KYJBT12213TXGWDB8BR5HA6Y.json
            ├── 📄 01KYJBTSC6MFJJZ5RNKXRMWYEA.json
            ├── 📄 01KYJBTSG1KRAD4T8QTM2M8T71.json
            ├── 📄 01KYJBTTZQ573HY484WMT91ZY4.json
            ├── 📄 01KYJBTW14SS3W8C31SEQRE163.json
            ├── 📄 01KYJBW951TRFACR3KKY4WZJDB.json
            ├── 📄 01KYJBYH2GKCAK7909ZRQNC3XJ.json
            ├── 📄 01KYJBYJAM4KS87W61X5B0ZC6S.json
            ├── 📄 01KYJBYJED797FZWA8NTF4RZ5F.json
            ├── 📄 01KYJBYMPD1T903CSKPB1BKWCG.json
            ├── 📄 01KYJBYQ3M86ZJ6SVE736G03F3.json
            ├── 📄 01KYJBYRYNRS02JEM0HYPR57QA.json
            ├── 📄 01KYJBYT06EEFNZ2M3WZPYDCW9.json
            ├── 📄 01KYJBYT4000B0W0EHNQJF6B0T.json
            ├── 📄 01KYJBYWKMADBKCM6W789J9RX1.json
            ├── 📄 01KYJBZSVXM0WXHX86R9JPA8QK.json
            ├── 📄 01KYJBZSYVKFZZ0FHVAM4A09F8.json
            ├── 📄 01KYJBZW0JV3EZXBMVEPZ33TGX.json
            ├── 📄 01KYJBZYP9NBNEXZE9XFHCM3EF.json
            ├── 📄 01KYJC005AVX76GYCVJX7GE0EF.json
            ├── 📄 01KYJC04W0956Q33XK7JR81C9W.json
            ├── 📄 01KYJC07HJCDAY4MAEMGCKZ973.json
            ├── 📄 01KYJC09CK4841D8XDG1TJR675.json
            ├── 📄 01KYJC0AJ0MVFC7CX19Z0J3KE0.json
            ├── 📄 01KYJC0BM4B3HSYK5XG00CVBB0.json
            ├── 📄 01KYJC0BT5T32P32G9MQ6BT0MZ.json
            ├── 📄 01KYJC2HP4AZSKKXQJK7JG1RGA.json
            ├── 📄 01KYJC2HSMXP88K067WH1GZ1TB.json
            ├── 📄 01KYJC2KWGVCGCG9YWY2V4G77C.json
            ├── 📄 01KYJC2MVTNYZ59SMVEJDQQZ0N.json
            ├── 📄 01KYJC2N0AMEAJQ2WHM5KFWC2P.json
            ├── 📄 01KYJC2RBFXZV0BRZHZ0DMKBCC.json
            ├── 📄 01KYJC2TPR8QHKPN85RVYF6SK0.json
            ├── 📄 01KYJC2XQPNW2C0XSE4QSNKNX5.json
            ├── 📄 01KYJC31JVF3E5M91GTKYKZ31X.json
            ├── 📄 01KYJC34WY89EV7F26NN8FRHDW.json
            ├── 📄 01KYJC375KA92R70SKJ4SGE5CS.json
            ├── 📄 01KYJC3CB062NCM58H93KSASA0.json
            ├── 📄 01KYJC3EWT97QAJ04JM9TDS94T.json
            ├── 📄 01KYJC4B9546VSGER16EZNAN5B.json
            ├── 📄 01KYJC8KZCBF513EEYCQCJGJH7.json
            ├── 📄 01KYJC8M36D6B2491JDND28D7N.json
            ├── 📄 01KYJC8P42745J63ZS1Q0PQ7CM.json
            ├── 📄 01KYJC8WDKWDAYE421SDH6TY2X.json
            ├── 📄 01KYJC8WHDVNN6HTVW7P0EE3NH.json
            ├── 📄 01KYJC8ZTMMK2D78ZFGKE3MAFX.json
            ├── 📄 01KYJC91BQ07MR44YR4NENZNQT.json
            ├── 📄 01KYJC9VPKXA0GPM1DQSYJZQ7B.json
            ├── 📄 01KYJCATYP5MTJ6C1NTFK460RA.json
            ├── 📄 01KYJCBE7D9H3BQM60MXN488DY.json
            ├── 📄 01KYJCBEA05AN8315432406PK3.json
            ├── 📄 01KYJCBG4VKPC9J44MXQ58GZAD.json
            ├── 📄 01KYJCBKKDFQ5JR438EHE2HQCW.json
            ├── 📄 01KYJCBKNRGH9R5MA2DS0NT1GJ.json
            ├── 📄 01KYJCBP57FX63KAC5Q6FWYTVB.json
            ├── 📄 01KYJCBRV2EARKNVVZT8MCE29P.json
            ├── 📄 01KYJCBVRFS2YPPBKKGXFWDBHG.json
            ├── 📄 01KYJCBY8AW63S41M6J7Y7STVV.json
            ├── 📄 01KYJCC0WHE6JP76Y6JFQWXWT8.json
            ├── 📄 01KYJCD0BMD341KG8EK74J7A4D.json
            ├── 📄 01KYJCFV90W6ABEBKXXW6JWF5K.json
            ├── 📄 01KYJCFWEXNXMW4NM7J55R8XWT.json
            ├── 📄 01KYJCFWJVSGEF1DTMBNGJFXQ0.json
            ├── 📄 01KYJCFXW6CFTX4Z91V7SW7MM2.json
            ├── 📄 01KYJCFYQTD1P9GQ8N8A7QK4XS.json
            ├── 📄 01KYJCG38ABY3S96CRGRW5FPM7.json
            ├── 📄 01KYJCG5A92SJ2Q1EWS9HPR9QE.json
            ├── 📄 01KYJCG5CG585ZWRSMYQDPYZQN.json
            ├── 📄 01KYJCG7YRTW58JBT4N1YW2XZZ.json
            ├── 📄 01KYJCG95R51AVEV4MRP7M19NT.json
            ├── 📄 01KYJCGCZJASGMJT7XHBEDBK6S.json
            ├── 📄 01KYJCGET62FZ0KM6XGWGKPCR4.json
            ├── 📄 01KYJCH83W4KAJAPDY0K67PYVG.json
            ├── 📄 01KYJCJ79RR15RREN2N2C02JBQ.json
            ├── 📄 01KYJCPREX6FW5A64VB6P0TJ2G.json
            ├── 📄 01KYJCPRJZ0F3HD1NXN3B38SZ5.json
            ├── 📄 01KYJCPW74B49XD6BYK0RZ5Q06.json
            ├── 📄 01KYJCQRCDW9J32JJGXPTTQSBC.json
            ├── 📄 01KYJCTTRR321KC1QGCPGQ7050.json
            ├── 📄 01KYJCTTX00E6YDXS0V5W81056.json
            ├── 📄 01KYJCTWAC2P3AQYYQPYNX1039.json
            ├── 📄 01KYJCV3GECAK8CNKBF3EX5K59.json
            ├── 📄 01KYJCV3PYY3Z1GMKQHSWQS0DQ.json
            ├── 📄 01KYJCV5743HHF4J1XRJ7S65RD.json
            ├── 📄 01KYJCVK5Z536QKKF0SBBXG5VT.json
            ├── 📄 01KYJCVK8KQD1H94G3HXY7CT17.json
            ├── 📄 01KYJCVNBAE69R8G17945WZPCY.json
            ├── 📄 01KYJCVPGN7W4CWA1Z8VT6C67V.json
            ├── 📄 01KYJCVTQNR96MR3A2ZN47QXGX.json
            ├── 📄 01KYJCVW1267MZKFNFQYFB1WZK.json
            ├── 📄 01KYJCVW4HV8FZ3W4DT03TR6YN.json
            ├── 📄 01KYJCVYACDYN6T67PXZSX97CP.json
            ├── 📄 01KYJCVYXG5H14HTCPQ29R8DPH.json
            ├── 📄 01KYJCW19A6237SXT3004R51T6.json
            ├── 📄 01KYJCW1C741CH1NHWTAYV2G8D.json
            ├── 📄 01KYJCW48SR4S02KANA7APRZD3.json
            ├── 📄 01KYJCW4B3XCFDVXZ9QKT1KKBK.json
            ├── 📄 01KYJCW6V9P4V9TA56EFXZ3A1Z.json
            ├── 📄 01KYJCW84GGH26BGXTCWF87QZX.json
            ├── 📄 01KYJCWC22ZBTNT27BKWDX64Q0.json
            ├── 📄 01KYJCWEHKK7ED8H7KGA3FX09F.json
            ├── 📄 01KYJCWGMSWPMTZT28V2625D19.json
            ├── 📄 01KYJCWK20SEHPYMTVF87DYG0E.json
            ├── 📄 01KYJCWK4RYTRS7F6NYK11FDQ0.json
            ├── 📄 01KYJCWN01HKZ75RMWW5J8R6PA.json
            ├── 📄 01KYJCWP3RBCRX73VVTG0KCYN1.json
            ├── 📄 01KYJCWS6CB3RB8C0XA9715FBF.json
            ├── 📄 01KYJCWVJQ0P09T331RX09GN8M.json
            ├── 📄 01KYJCWX549SWGJXS20D1K36VW.json
            ├── 📄 01KYJCWY7CYMG9KWYB3FDW2DG8.json
            ├── 📄 01KYJCWYAPWP8BT6K69HH4A0BC.json
            ├── 📄 01KYJCX0GAXETTDDGAG7JB5T76.json
            ├── 📄 01KYJCX1R95B53QJ3323R3G74P.json
            ├── 📄 01KYJCX3GJKK0KWXT5RPBS3GSA.json
            ├── 📄 01KYJCX3JPDM04AHNY5XMJGAEQ.json
            ├── 📄 01KYJCX4ZFFWPWFD4WD3BNSXDP.json
            ├── 📄 01KYJCX64SC7NH7H3CR69YGHC7.json
            ├── 📄 01KYJCY54FECKQCBBCWXB6T2ZJ.json
            ├── 📄 01KYJD09HJ8AGNKRCX3XBRQ7AE.json
            ├── 📄 01KYJD4D367TR3K3AM2EG1D0FB.json
            ├── 📄 01KYJD4D6T9B2NCZDVAV22KQDF.json
            ├── 📄 01KYJD4FPBTTPXT75NH4JFX2EG.json
            ├── 📄 01KYJD4GRE509SEX2Q5FJPR5RZ.json
            ├── 📄 01KYJD4J5T6HQ3SX5B7KXEEAJ8.json
            ├── 📄 01KYJD4J984A3011G2ZQJ75XJN.json
            ├── 📄 01KYJD4KVPK6M06P069FZ2TG2D.json
            ├── 📄 01KYJD4NDPQRA5K8J0Q6CH0D4Q.json
            ├── 📄 01KYJD5MN5J9PVTPPB63Y2QMN7.json
            ├── 📄 01KYJD6Q2M16GH61051FR008AY.json
            ├── 📄 01KYJD6R4XQ4P2FS2KBBZ3KX8Z.json
            ├── 📄 01KYJD6R78FCDE76PW397AD40A.json
            ├── 📄 01KYJD6T5CEQHW4TTR9GZGK3F9.json
            ├── 📄 01KYJD7Q3WGG4E37ZWVEW7VX1D.json
            ├── 📄 01KYJD8SG99SG7SQM43J417RT6.json
            ├── 📄 01KYJD8SM561HVRTQ4WCR8XNT3.json
            ├── 📄 01KYJD8V7T84DRHGJHDSC9DPP2.json
            ├── 📄 01KYJD8WBSKKMEXBMHPN0A0037.json
            ├── 📄 01KYJD9V6PRDGMBVYRGR2QTHRQ.json
            ├── 📄 01KYJD9ZRA0QXRMY5DDCCT617E.json
            ├── 📄 01KYJD9ZVQCBTQKWE3PVHM21HA.json
            ├── 📄 01KYJDB4J4S6CP9P7CR263VCMA.json
            ├── 📄 01KYJDB4MQ8QH94KR4H1MBCTT1.json
            ├── 📄 01KYJDCDF3N61JGD49FS6ZF4MV.json
            ├── 📄 01KYJDCDHRFF6TZ0EVHCED3MRQ.json
            ├── 📄 01KYJDKSW7TX7573VC2QF7E3QT.json
            ├── 📄 01KYJDKT04WZ2472FHMDHQFQ7Y.json
            ├── 📄 01KYJDQ5AQQSMCS793BP8Q4N6D.json
            ├── 📄 01KYJDQ5F5MV8EY4K6GQZ2K5XT.json
            ├── 📄 01KYJDQFP8541NFCVDNBVX24S0.json
            ├── 📄 01KYJDQFTT1XYM7V77QS0RYA57.json
            ├── 📄 01KYJDR3F5JDY44AV1R2KE22X2.json
            ├── 📄 01KYJDR3HETKHV3M1AYVECKWPQ.json
            ├── 📄 01KYJDR4YCXKZQWV4GZGVMT7X5.json
            ├── 📄 01KYJDR6A087PJQH4DYED32YF4.json
            ├── 📄 01KYJDS5CWTQZD12P2RDEG43CZ.json
            ├── 📄 01KYJDWZC0VPAWR14PY54E2RRY.json
            ├── 📄 01KYJDX239NSN2PBW69JB9C63Z.json
            ├── 📄 01KYJDX2FHS47YCVCX5C3WPAQF.json
            ├── 📄 01KYJDX4QWQTYZXJ30HV7QSHK6.json
            ├── 📄 01KYJDXRGM97D8667T93BJW3D2.json
            ├── 📄 01KYJDXRMKJVNJ4KQXBVGBC3Z0.json
            ├── 📄 01KYJDZ9A86FFX5NBF4N45MCZS.json
            ├── 📄 01KYJDZ9CYGF632TSZHHME6GVC.json
            ├── 📄 01KYJDZB38XYEK8RY18KBPB4F6.json
            ├── 📄 01KYJE2GZ8HTCCA148VC77ES9K.json
            ├── 📄 01KYJE2H4AF4ZH8P0R68CG50AE.json
            ├── 📄 01KYJE2K2HTTCA9HDQJ8DABYZG.json
            ├── 📄 01KYJE3GSRMRVCHJ59JKRT0WZP.json
            ├── 📄 01KYJE4TQKMFBKVYGEN3739F9C.json
            ├── 📄 01KYJE4TW1K3B5X6YG3FF2MHXB.json
            ├── 📄 01KYJE4X4BB0MRY6TJZ74BPXYX.json
            ├── 📄 01KYJE5TEFW17YA5FK0EKE2NH5.json
            ├── 📄 01KYJE662B3MCK9FEAEE5A5S84.json
            ├── 📄 01KYJE665E859S3H4PR0EFQ7SM.json
            ├── 📄 01KYJE67ZYTZ9T7CDYPS7X57MY.json
            ├── 📄 01KYJE6H4PY20KT7F1Q4HEXZAA.json
            ├── 📄 01KYJE6H7E0JRCSAEEHYZNBJ6K.json
            ├── 📄 01KYJE6JTNFWVVQW3X2CDW33YA.json
            ├── 📄 01KYJE78YWSVG9HHWH9G2E5CA6.json
            ├── 📄 01KYJE795ANA0D04EGEC8MREPV.json
            ├── 📄 01KYJE8844Z243FQWCHKCVE652.json
            ├── 📄 01KYJE886ZH9FG9ZZ8J7MTVXYM.json
            ├── 📄 01KYJE94919MN12071YGP8Y74T.json
            ├── 📄 01KYJE94BBEWA9J8ARNCX979HR.json
            ├── 📄 01KYJE96DA2Y3SN4D48VCGHW1F.json
            ├── 📄 01KYJEA3PNEEFBXFBV894VCNRV.json
            ├── 📄 01KYJEA9EHCX51MZ0249M7HZSK.json
            ├── 📄 01KYJEA9JX71CT0CS3PDK31J65.json
            ├── 📄 01KYJEAB5WXWZQD9THB42K7D0K.json
            ├── 📄 01KYJEB8VT3J0TJAGRS8RAP8P9.json
            ├── 📄 01KYJEBA06BFKDNHMXYEDGBHDV.json
            ├── 📄 01KYJEBA4HDMY36NY4ZYBXWW27.json
            ├── 📄 01KYJEBBW5XC8N493ARCXFW80T.json
            ├── 📄 01KYJEC95PVPBX79K4F1WCZ7TN.json
            ├── 📄 01KYJECR6Q3RREJFRX17KESBAY.json
            ├── 📄 01KYJECR9MM1RG8DTGXNQAWTYA.json
            ├── 📄 01KYJEDHHTK3JN5X3GC3ANMBM9.json
            ├── 📄 01KYJEDHN5D0YF4MHGYRJNTCXS.json
            ├── 📄 01KYJEDRT5X3JB5RHVZH36F36A.json
            ├── 📄 01KYJEDRWNSH96NVZCPGAW1ATT.json
            ├── 📄 01KYJEDT4ZVKBPH58BFTWJ5HRD.json
            ├── 📄 01KYJEER9K6HH79XKZVC49VK36.json
            ├── 📄 01KYJEG73YXQ4TDNM1VMCJ3QX2.json
            ├── 📄 01KYJEG774B16ZRCXYSPN8NMQ9.json
            ├── 📄 01KYJEG8TJ9XF3QZB3E8EM6FVX.json
            ├── 📄 01KYJEH4XCMPJ9BCTC95JGE1H0.json
            ├── 📄 01KYJEH5MDWTJP4CJ5W0NJ2CM6.json
            ├── 📄 01KYJEH5SK0N6YZEF4CQV0P3C5.json
            ├── 📄 01KYJEH6XY11MXBZ0V441VWQT1.json
            ├── 📄 01KYJEJ66D85ZC9Y0WE7SYKM64.json
            ├── 📄 01KYJEK5H86XG8SX3NX68FX5VJ.json
            ├── 📄 01KYJEKGT09M4FGCHKRK2VKQNK.json
            ├── 📄 01KYJEKGWKBVD41E60C4DBT750.json
            ├── 📄 01KYJEKJYN8ESXWCVBT5NC84RS.json
            ├── 📄 01KYJEMJCBZ188XNFPW88D7XP9.json
            ├── 📄 01KYJENHMTQ9CXJB1CVFHKYD9V.json
            ├── 📄 01KYJEPGWMXGBBD9R0A9K6GK8B.json
            ├── 📄 01KYJESPENJHSWAFMZ99NDGBCP.json
            ├── 📄 01KYJETNSQX56Y9WRVZKXEA9CN.json
            ├── 📄 01KYJEVMYHYMC501TMT6PZ2Y7X.json
            ├── 📄 01KYJEWM603VT82C651Q6D6FD1.json
            ├── 📄 01KYJEWMCBGF3Y6GXESG3KD6Q1.json
            ├── 📄 01KYJEWZVE66H7GF09ZGTXZC3H.json
            ├── 📄 01KYJEX0YHD9JYTJWVF5ZTGPFP.json
            ├── 📄 01KYJEX12HZ8F1484NGZ153WK2.json
            ├── 📄 01KYJEX38E68GY5Y35N4F8Z408.json
            ├── 📄 01KYJEY22A3QGX65PRP9C7QEW3.json
            ├── 📄 01KYJEYJN7RRYX2M8TSNHTEDAW.json
            ├── 📄 01KYJEYJS3Q1JFGCDWY384D56N.json
            ├── 📄 01KYJEYMDRYZ4R70HKACPGFN2D.json
            ├── 📄 01KYJEZ8B28VX1E7P73EBS0M9S.json
            ├── 📄 01KYJEZ8EYC2Z4GTMJGN8CXZTC.json
            ├── 📄 01KYJEZ9YPA63JQXRMWKBS620H.json
            ├── 📄 01KYJF0AFP63B8BRRY5RTWBMKZ.json
            ├── 📄 01KYJF2M3RJVFKR8Z8ZADCFWS5.json
            ├── 📄 01KYJF2MCY9BV46B0J8ZCHXEBT.json
            ├── 📄 01KYJF2MGJ39Y9XXWWE0Q2N59W.json
            ├── 📄 01KYJF2NV1AT6HV6HT59QH8NZA.json
            ├── 📄 01KYJF3MVGKWA2HVR221R4FH0B.json
            ├── 📄 01KYJF5SJ8GX99PMZF6NV7S327.json
            ├── 📄 01KYJF5SWEC3XH6XVTS4ZNFJC4.json
            ├── 📄 01KYJF5T08BWGD9E278S3ZN4MZ.json
            ├── 📄 01KYJF5VR257E8SX7MYXZ93RQR.json
            ├── 📄 01KYJF69E72S06KAEVZ9H7XERB.json
            ├── 📄 01KYJF69JX7M26882ZN5MNTC77.json
            ├── 📄 01KYJF6NG49K9YKF5QDC9AWF11.json
            ├── 📄 01KYJF6NYCE7D8C4D829GHQFH9.json
            ├── 📄 01KYJF6QHRRD23F4M5B4NH88YE.json
            ├── 📄 01KYJF7RYFJMZ8Q8YA5B1DHXZX.json
            ├── 📄 01KYJF7S272GV3F2MRS9RXGVGX.json
            ├── 📄 01KYJF9NZ7XZT415X9VXTKEMKR.json
            ├── 📄 01KYJF9P1WZ274E40VJEQVX3CG.json
            ├── 📄 01KYJF9Q5SJEJWPYW6T5VKDQGZ.json
            ├── 📄 01KYJFAMWD165FD554896E60T9.json
            ├── 📄 01KYJFBJ5J457G29STRSMAHDYP.json
            ├── 📄 01KYJFBJ856F6A3R41139F3BRN.json
            ├── 📄 01KYJFBM2G0963RZDYR13G333D.json
            ├── 📄 01KYJFBNAGYZAD6NCNRHPMC1D8.json
            ├── 📄 01KYJFBVTVRQM3JG68X0Z8CBQG.json
            ├── 📄 01KYJFBXMMQW5P3PZEJZ4WGX78.json
            ├── 📄 01KYJFBXW17K3PW70D1M5K3S0S.json
            ├── 📄 01KYJFC07JM5S0XAX185VDSN3G.json
            ├── 📄 01KYJFCZMD8AA8PD5M98FPMR53.json
            ├── 📄 01KYJFGVPGD9K2FWS8VM9SVB1Q.json
            ├── 📄 01KYJG34MVXHH57ACFR9GT7A60.json
            ├── 📄 01KYJG43WHF3HX6VJPFMZH7D13.json
            ├── 📄 01KYJG534Z8VYVSGYTF5FNBHZ8.json
            ├── 📄 01KYJG62DCGFE7PH1SZ60D7NMA.json
            ├── 📄 01KYJGAA4VH53VG5YCZTVBHN1R.json
            ├── 📄 01KYJGADSS56CXGNYN2NECTX2Q.json
            ├── 📄 01KYJGB3TWJEXJKFJ3ZBVRTW89.json
            ├── 📄 01KYJGB3YYZ1WHPZ7RA6AJPTXD.json
            ├── 📄 01KYJGB60Q8VQ0942B3T0WED9B.json
            ├── 📄 01KYJGB71W8W4N9JZH1EN9TMYK.json
            ├── 📄 01KYJGC5NFJ3B3MEXEKPE2ABPQ.json
            ├── 📄 01KYJGD4XVE829FXVBR52F9MWD.json
            ├── 📄 01KYJGEXKMK1WDSW118ZP9B0G9.json
            ├── 📄 01KYJGFWVW87RX2TJ8ZGRBFXPK.json
            ├── 📄 01KYJGGVEJZ105NPF9FG7RGTDD.json
            ├── 📄 01KYJGGWJWFWVG9FJ2HPN8ZPRE.json
            ├── 📄 01KYJGGWPBGPPBD5HF8CVXS665.json
            ├── 📄 01KYJGGYDM9H7M19F1S8B0YVYA.json
            ├── 📄 01KYJGHX8W791E924NQBY97V9V.json
            ├── 📄 01KYJGJWH06N4VGJMDC172QFTV.json
            ├── 📄 01KYJGKVSE5SXTBFMZW79CWC76.json
            ├── 📄 01KYJGN2ZSSHZJHW834B6N1BY9.json
            ├── 📄 01KYJGN33KNV4HT0A828KWZWQA.json
            ├── 📄 01KYJGN4ZBHX39Y1QMWAAF3QPA.json
            ├── 📄 01KYJGP42KGPVD9ETZS4NM9PDQ.json
            ├── 📄 01KYJGPTHVH3T3ZKJC0AYA2NGZ.json
            ├── 📄 01KYJGPXT187S1R5Q3HW7KWD6K.json
            ├── 📄 01KYJGQ0JQQ041SKHVWDM5YNJP.json
            ├── 📄 01KYJGQZRYDHP11J16A7Y818KW.json
            ├── 📄 01KYJGRYW618YHBY122E9ZR306.json
            ├── 📄 01KYJGTXV6X2Z7HGVJ77W81Q5V.json
            ├── 📄 01KYJGTY4WWTQX0G6Y5NNFQABJ.json
            ├── 📄 01KYJGV1GQF2FMJZM3VWQ1MJT4.json
            ├── 📄 01KYJGV4ZVJCSRH358N2RCZJ0H.json
            ├── 📄 01KYJGV7RH3EV4TGYRM9JM102M.json
            ├── 📄 01KYJGVBRBYKBB9TMD4F0RGZ2E.json
            ├── 📄 01KYJGVDAND3XGXB06M2NJ6NM5.json
            ├── 📄 01KYJGW6BNJ27M4BPHN2TY1NJ2.json
            ├── 📄 01KYJGX4Z7N0PRFB18G0WRGHDQ.json
            ├── 📄 01KYJGXB3WETKXSJ24X46TMK8H.json
            ├── 📄 01KYJGXGJZ4T6RNDPJS8XBJSAQ.json
            ├── 📄 01KYJGXJF7HT57Q3K294KJPBB2.json
            ├── 📄 01KYJGXN1GWPKQNR7741X86GPT.json
            ├── 📄 01KYJGXQNGC8Z6RJ4Y2A2HNFQE.json
            ├── 📄 01KYJGXSE48XBW9W031M3Q94KG.json
            ├── 📄 01KYJGYEEVMM27B92R3BDD87EN.json
            ├── 📄 01KYJGZ5X34VSNA0NNHX7DS85V.json
            ├── 📄 01KYJHC0VWCWSG1E8X14GJHVVX.json
            ├── 📄 01KYJHC11W6XXYTDB2BKYKK59P.json
            ├── 📄 01KYJHC39KWWJZ7VG70PQ38WSR.json
            ├── 📄 01KYJHC7NQPN2T2D21HSMHX4A9.json
            ├── 📄 01KYJHCDCCNXTYMTGZWKMVF9M3.json
            ├── 📄 01KYJHD0RMD16538ZP5MT0GGK0.json
            ├── 📄 01KYJHD66MBCDATHBW5D06CP9Z.json
            ├── 📄 01KYJHGR6YSJZCXDQJMCDTKKV4.json
            ├── 📄 01KYJHGVV53P5YCJH48HEJMQ4M.json
            ├── 📄 01KYJHGZN0PWKB75RC1PPKY4KA.json
            ├── 📄 01KYJHH43J6N8Y8M3K88870R41.json
            ├── 📄 01KYJHH53FWKDJ3V87MCAX1880.json
            ├── 📄 01KYJHH6B8MY75HWDM8FC1CAAH.json
            ├── 📄 01KYJHH7QNNV9VKN6YP5M4QCFW.json
            ├── 📄 01KYJHH9FE1DY0AVPJVH0MPC77.json
            ├── 📄 01KYJHHA4K57GHG06W9J7EMY1M.json
            ├── 📄 01KYJHPPA34MH0R3YE3G0YE54C.json
            ├── 📄 01KYJHPPEAPYJAF7H7HTW2YZ3H.json
            ├── 📄 01KYJHPSTQ7WMTBEVWZCSDRN46.json
            ├── 📄 01KYJHPVYNJ1W4RWR7WEP2Z5CB.json
            ├── 📄 01KYJHQ0DK58QY2DCJ51N23JE7.json
            ├── 📄 01KYJHQEG21E483HFHP4C48NE0.json
            ├── 📄 01KYJHQH897SR19J1XQAWVR1F3.json
            ├── 📄 01KYJHQJJAQ54QBG9B9TNHCH6V.json
            ├── 📄 01KYJHV8ZF3DETJN8S2JK5D29T.json
            ├── 📄 01KYJHV96968J9KJ50T8HSNV5S.json
            ├── 📄 01KYJHVB9J991NJ5H39T1V9QHS.json
            ├── 📄 01KYJHVDR5EBBZ0SJVEK2SP1AX.json
            ├── 📄 01KYJHVHF9325HV5SZ4T5VZ1QM.json
            ├── 📄 01KYJHVQ9QGJ6ARW72JW7MY7RW.json
            ├── 📄 01KYJJ79024FP8CJQ1XB13YN75.json
        ├── 📁 framework/
            ├── 📁 cache/
                ├── 📁 data/
            ├── 📁 sessions/
            ├── 📁 testing/
                ├── 📁 disks/
                    ├── 📁 local/
            ├── 📁 views/
                ├── 📄 04eaf8bd8efa8babb5212c4e14495ac9.php
                ├── 📄 07123ce5f125a877f5f46e828f6cbf8e.php
                ├── 📄 08a89315b2a1892d902a18ce7a5c4c04.php
                ├── 📄 15bd56346a79331c8c9584bd8cae0ff9.php
                ├── 📄 1728fc29ae22ba5b8de2310dbe4ae989.php
                ├── 📄 1f918cd372c7ef3fec9295d3c3025426.php
                ├── 📄 30294883492e5dd737e2920d0591bc3f.php
                ├── 📄 34b8646a69c794321e6de87064ced42a.php
                ├── 📄 3f807195ecf226682a2009c404ce432d.php
                ├── 📄 41146bee49bb2869480cb54d9ca35136.php
                ├── 📄 42b08793abe4d8c12f66431f20a02633.php
                ├── 📄 438c726daa764591a62975f3094427be.php
                ├── 📄 448d5583530f1e082ff3f3768f4bdede.php
                ├── 📄 4943bc92ebba41e8b0e508149542e0ad.blade.php
                ├── 📄 55b850eaa327994f8c6dc2425012d92d.php
                ├── 📄 60bb040b1cd19c0118762fae3533957c.php
                ├── 📄 6646ba08241ce0cad17e3a9ce79383c4.php
                ├── 📄 68ca0c6463299418a928e8f505412ba1.php
                ├── 📄 70591ccc3d9315acb3281fda56d12e81.php
                ├── 📄 76bc461f09e2c682214df310cba8bb7b.php
                ├── 📄 79ae1539afe0350802919bb4e7365388.php
                ├── 📄 8191aceac12a5cb556fa75079ed61d0b.php
                ├── 📄 837d0b605f975d39117359148886475d.php
                ├── 📄 855ae3417c9fa4d87b29f91d4ee7c2ac.php
                ├── 📄 8a7cb3771c45dfd64a0ea0a1be2fbd27.php
                ├── 📄 8a886c64988d1ec8aef742273ad04fc8.php
                ├── 📄 968884768a1b7c0f7bcbbb60f7e6a2b6.php
                ├── 📄 a267c42ba145501a5ab7ee5cf323bb4e.php
                ├── 📄 adcbacee3f1add647ca5dd03b056d531.php
                ├── 📄 ae62d32a4717b297a136da52cc866273.php
                ├── 📄 b19195661020e9440d5bf533bd1fc471.php
                ├── 📄 b35034a4fb8461b1c403735882e6346d.php
                ├── 📄 b5cb33449399a86df40a452822141d5a.php
                ├── 📄 bb6e1bd3b76007e0d56dce6ae22ec6e2.php
                ├── 📄 c53b3c51dc251b75880aa1ad7b8da233.php
                ├── 📄 c92de45521f28de38a5bc87d592f5f78.php
                ├── 📄 ccd8258129c43f0a8e8865d644ec830c.php
                ├── 📄 ce1b24d2fe517fd8a1b458bb0cbd778f.php
                ├── 📄 d4b1dfb07e42478cdefed6f7a3f6a965.php
                ├── 📄 d97a319f175eb7c50bd05bde1cec2d79.php
                ├── 📄 db886821b1db54be68a25e7cb853ec11.php
                ├── 📄 dbe494394b9154c5d2683e2ff592a50d.php
                ├── 📄 e528677e25fd82aead14f081eb9a523f.php
                ├── 📄 ea348a91258fea209de6b411bb6db8c5.php
                ├── 📄 f630cab53f2d29b297afaeb7d862dff9.php
                ├── 📄 fa4e7a29460147268587e53dd2cb2d71.php
                ├── 📄 fa93a243338a36f9874cbd5c97628f24.php
                ├── 📄 fe84ba6e54e1c75cba632914b7d2bae1.php
                ├── 📄 ff43717a7a15c6bf33b87f92b79c8f41.php
                ├── 📄 fff7a32de63ac32de8c369256987ae9b.php
        ├── 📁 logs/
            ├── 📄 browser.log
            ├── 📄 laravel.log
└── 📁 tests/
        ├── 📁 Feature/
            ├── 📄 ApiTokenPermissionsTest.php
            ├── 📄 AuthenticationTest.php
            ├── 📄 BrowserSessionsTest.php
            ├── 📄 CreateApiTokenTest.php
            ├── 📄 DeleteAccountTest.php
            ├── 📄 DeleteApiTokenTest.php
            ├── 📄 EmailVerificationTest.php
            ├── 📄 ExampleTest.php
            ├── 📄 PasswordConfirmationTest.php
            ├── 📄 PasswordResetTest.php
            ├── 📄 ProfileInformationTest.php
            ├── 📄 RegistrationTest.php
            ├── 📄 TwoFactorAuthenticationSettingsTest.php
            ├── 📄 UpdatePasswordTest.php
        ├── 📁 Unit/
            ├── 📄 ExampleTest.php
        ├── 📄 TestCase.php
```

## 🧩 Модели (Models)

| Модель | Таблица |
| :--- | :--- |
| `Actor` | `actors` |
| `Choice` | `choices` |
| `Company` | `companies` |
| `CompanyScenario` | `company_scenarios` |
| `Effect` | `effects` |
| `EffectType` | `effect_types` |
| `Event` | `events` |
| `Game` | `games` |
| `GameHistory` | `game_histories` |
| `ParameterDefinition` | `parameter_definitions` |
| `Preset` | `presets` |
| `Scenario` | `scenarios` |
| `Scene` | `scenes` |
| `User` | `users` |

## 🎮 Контроллеры

| Контроллер |
| :--- |
| `ActorController` |
| `ChoiceController` |
| `CompanyController` |
| `Controller` |
| `EffectController` |
| `EffectTypeController` |
| `EventController` |
| `GameController` |
| `GameHistoryController` |
| `PresetController` |
| `ScenarioController` |
| `SceneController` |
| `TestController` |

## 🔧 Middleware

- `CheckAdmin`

## 📦 Провайдеры (Service Providers)

- `AppServiceProvider`
- `FortifyServiceProvider`
- `JetstreamServiceProvider`

## ⚙️ Конфигурация

| Файл | Описание |
| :--- | :--- |
| `app.php` | Основные настройки приложения |
| `auth.php` | Настройки аутентификации |
| `cache.php` | Настройки кеширования |
| `database.php` | Настройки базы данных |
| `filesystems.php` | Настройки файловой системы |
| `fortify.php` | Конфигурационный файл |
| `jetstream.php` | Конфигурационный файл |
| `livewire.php` | Конфигурационный файл |
| `logging.php` | Конфигурационный файл |
| `mail.php` | Настройки почты |
| `queue.php` | Настройки очередей |
| `sanctum.php` | Конфигурационный файл |
| `services.php` | Конфигурационный файл |
| `session.php` | Настройки сессий |

### Ключевые настройки

- **app.debug:** `✅ Включен`
- **app.url:** `http://localhost`
- **database.default:** `mysql`
- **cache.default:** `database`
- **session.driver:** `database`
- **queue.default:** `database`

## 📦 Зависимости (Composer)

### Основные зависимости

- `laravel/framework`: `^13.0`
- `laravel/jetstream`: `^5.5`
- `laravel/sanctum`: `^4.0`
- `laravel/tinker`: `^3.0`
- `livewire/livewire`: `^3.6.4`
- `spatie/laravel-data`: `^4.22`

### Зависимости для разработки

- `barryvdh/laravel-ide-helper`: `^3.7`
- `fakerphp/faker`: `^1.23`
- `fruitcake/laravel-debugbar`: `^4.2`
- `laravel/boost`: `^2.4`
- `laravel/pail`: `^1.2.5`
- `laravel/pint`: `^1.27`
- `mockery/mockery`: `^1.6`
- `nunomaduro/collision`: `^8.6`
- `phpunit/phpunit`: `^12.5.12`

## 🗄️ Информация о базе данных

- **Имя БД:** `game`

## 🛡️ Политики (Policies)

- ActorPolicy
- ChoicePolicy
- CompanyPolicy
- EffectPolicy
- EffectTypePolicy
- EventPolicy
- GameHistoryPolicy
- GamePolicy
- PresetPolicy
- ScenarioPolicy
- ScenePolicy

## 🔔 События и слушатели

## ⏰ Запланированные задачи


## 🧪 Тесты

**Всего тестов:** 16

- ApiTokenPermissionsTest
- AuthenticationTest
- BrowserSessionsTest
- CreateApiTokenTest
- DeleteAccountTest
- DeleteApiTokenTest
- EmailVerificationTest
- ExampleTest
- PasswordConfirmationTest
- PasswordResetTest
- ProfileInformationTest
- RegistrationTest
- TwoFactorAuthenticationSettingsTest
- UpdatePasswordTest
- TestCase
- ExampleTest

## 📈 Анализ кода

- **Всего PHP файлов:** 99
- **Всего строк кода:** 8,602
- **Среднее строк на файл:** 86.9

