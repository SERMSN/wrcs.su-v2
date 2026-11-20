# wrcs.ops

Портфолио DevOps / SRE-инженера: профиль, кейсы, интерактивный рейтинг, инфографика,
тёмная/светлая темы и два языка (RU / EN). Идентичность на сайте — только бренд
**wrcs.ops**, персональных имени и фамилии в проекте нет.

- **Laravel 13**
- **PHP 8.4** (локально и на сервере)
- **JSON вместо базы данных** — `data/*.json`
- Blade + чистый CSS/JS, без сборщика
- Docker — только для локальной разработки
- Продакшен — файлы + PHP-FPM + nginx, без контейнера

---

## Структура

```text
app/
  Http/Controllers/          HomeController, ProjectController, RatingController, LocaleController
  Http/Middleware/SetLocale  сессия → Accept-Language → APP_LOCALE
  Repositories/              ProfileRepository, ProjectRepository, RatingRepository
  Support/
    JsonFile.php             JSON-«база»: flock, атомарная запись, кэш на запрос
    ProjectPresenter.php     проект + рейтинг + URL
    Heatmap.php              детерминированная heatmap
    helpers.php              loc(), loc_lines(), count_label(), years_phrase()
data/
  profile.json               бренд, роль, тексты, навыки, опыт, KPI
  projects.json              9 кейсов
  ratings.json               голоса (runtime, в git не входит)
lang/{ru,en}/ui.php          подписи интерфейса
resources/views/             layout, главная, кейс, 404, Blade-компоненты
public/                      точка входа и assets (css, js, шрифты, svg)
docker/dev/                  Dockerfile + entrypoint для локального запуска
docker-compose.yml           локальный контейнер
deploy/nginx/wrcs.su.conf    боевой nginx (PHP-FPM 8.4)
.github/workflows/deploy.yml CI/CD: тесты → production vendor → rsync → optimize
```

Лишнего в репозитории нет: production-контейнер, старые скриншоты и генератор
ассетов удалены. Обложки кейсов и OG-картинка лежат готовыми SVG в `public/assets/img/`.

---

## Локально (Docker)

Нужен Docker с Compose; Composer ставится внутри контейнера.

```bash
docker compose up -d --build
```

Открыть **http://127.0.0.1:8080**.

Если UID/GID отличаются от `1000`, запускайте так:

```bash
DOCKER_UID=$(id -u) DOCKER_GID=$(id -g) docker compose up -d --build
```

При первом старте `docker/dev/entrypoint.sh`:
- копирует `.env.example` → `.env`, если файла нет;
- выполняет `composer install`, если нет `vendor/`;
- создаёт каталоги `storage/` и генерирует `APP_KEY`.

Код смонтирован внутрь (`./:/app`), поэтому правки видны сразу — пересборка не нужна.
Контейнер работает на PHP 8.4, как и продакшен.

```bash
docker compose exec app php artisan test      # тесты
docker compose exec app php artisan route:list
docker compose logs -f app
docker compose down
```

---

## Данные и профиль

Все тексты двуязычные: `{"ru": "…", "en": "…"}`, хелпер `loc()` берёт текущий язык.

`data/profile.json` — единственное место, где меняется «личность» сайта:

| Поле | Что это |
| --- | --- |
| `identity.name` | имя/бренд в мета-тегах и карточке профиля; по умолчанию `wrcs.ops` |
| `identity.headline` | крупный заголовок в hero; по умолчанию «Инфраструктура без сюрпризов» |
| `identity.nickname`, `handle`, `brand` | `WRCS`, `wrcs`, `wrcs.ops` |
| `identity.role`, `tagline`, `location`, `timezone`, `status`, `since` | роль, слоган, город, статус, год старта |
| `bio`, `principles` | текст «о себе» и принципы |
| `contacts` | email / telegram / github / linkedin |
| `metrics` | KPI: значение, суффикс, подписи, `spark` |
| `skills` | `name`, `level` 0–100, `tier`: `expert` / `advanced` / `solid` |
| `timeSplit` | данные donut-диаграммы |
| `experience`, `certifications` | таймлайн и сертификаты |

Если позже захочется вернуть имя — поменяйте `name` и `headline` в профиле, шаблоны
подхватят значения без правок кода.

`data/projects.json` — массив кейсов: `slug`, `title`, `summary`, `description`
(массив абзацев), `cover`, `stack`, `metrics`, `role`, `client`, `year`, `featured`,
`accent`, `rating.histogram` (`[5★, 4★, 3★, 2★, 1★]`).

`data/ratings.json` — runtime-файл с голосами. Он в `.gitignore`, создаётся приложением
и не перезаписывается деплоем.

Рейтинг: один голос на браузер (HMAC от IP + User-Agent), повторный клик меняет оценку.
Запись идёт под `flock`, без гонок и «битого» JSON; на карточке и странице кейса средняя
и распределение обновляются без перезагрузки.

---

## Продакшен (без контейнера)

Деплой запускается пушем в `master` (или вручную через **Actions → Deploy Production →
Run workflow**):

```text
push → master
  ├─ PHP 8.4: composer install + php artisan test
  ├─ rsync файлов в /var/www/wrcs.su
  ├─ сборка production vendor (--no-dev) и upload vendor
  ├─ php artisan optimize
  └─ smoke test: https://wrcs.su/up
```

Контейнер на сервере **не собирается и не запускается**. `rsync` работает без `--delete`:
он только добавляет и обновляет файлы, ничего не удаляя на сервере.

### Что должно быть на сервере

- PHP **8.4.27**: CLI + FPM, расширения `mbstring`, `xml`, `curl`, `zip`, `openssl`, `tokenizer`;
- nginx;
- `rsync`, `unzip`, `acl` (для `setfacl`; без него деплой использует `chmod`);
- SSH-пользователь с правом записи в `/var/www/wrcs.su`.

Пример для Ubuntu:

```bash
sudo apt-get install -y php8.4-cli php8.4-fpm php8.4-mbstring php8.4-xml \
  php8.4-curl nginx rsync acl
```

### GitHub Secrets / Variables

| Имя | Обязательно | Что это |
| --- | --- | --- |
| `DEPLOY_HOST` | да | IP или домен сервера |
| `DEPLOY_PORT` | нет (22) | SSH-порт |
| `DEPLOY_USER` | да | SSH-пользователь |
| `DEPLOY_PASSWORD` | да | пароль SSH |
| `APP_URL` | нет | `https://wrcs.su` |

Поддерживается fallback `secrets.X || vars.X` — значения можно хранить в Secrets или
в Variables.

### Что переживает деплой

| Путь | Почему не перезаписывается |
| --- | --- |
| `.env` | исключён из rsync, правится на сервере |
| `data/ratings.json` | исключён; голоса посетителей сохраняются |
| `storage/` | исключён: логи, сессии, кэш, compiled views |
| `vendor/` | собирается в CI с `--no-dev` и копируется вместе с проектом |
| `bootstrap/cache/*.php` | исключён и пересобирается через `artisan optimize` |

`profile.json` и `projects.json` приезжают из репозитория — это конфигурация сайта.
`ratings.json` и `.env` живут только на сервере.

### Первый деплой

Если `.env` нет, workflow создаст его из `.env.example`, сгенерирует `APP_KEY`,
выставит `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` и `SESSION_SECURE_COOKIE=true`.
Существующий `.env` не удаляется; служебные значения (`APP_ENV`, `APP_DEBUG`, `APP_URL`,
`LOG_LEVEL`) приводятся к production при каждом деплое.

Права на `storage`, `bootstrap/cache` и `data` выдаются через `setfacl` пользователю
`www-data`; без `setfacl` используется `chmod 777` (проще, но менее строго).

### Nginx

Конфиг для справки: [`deploy/nginx/wrcs.su.conf`](deploy/nginx/wrcs.su.conf).
Он отдаёт `/var/www/wrcs.su/public` и передаёт PHP в `php8.4-fpm.sock`.

**Workflow не копирует `deploy/` на сервер и не перезаписывает nginx.** Конфиг уже
настроен на сервере вручную; деплой его не трогает.

Если нужно поднять сайт на другом сервере, скопируйте файл вручную и примените:

```bash
scp deploy/nginx/wrcs.su.conf user@server:/tmp/
ssh user@server
sudo install -m 644 /tmp/wrcs.su.conf /etc/nginx/sites-available/wrcs.su
sudo ln -sfn /etc/nginx/sites-available/wrcs.su /etc/nginx/sites-enabled/wrcs.su
sudo nginx -t && sudo systemctl reload nginx
```

Smoke test ожидает, что nginx уже настроен на `/var/www/wrcs.su/public`.

### Обслуживание

```bash
# логи Laravel
ssh user@server 'tail -f /var/www/wrcs.su/storage/logs/laravel.log'

# ручной перезапуск кэшей после правки .env
ssh user@server 'cd /var/www/wrcs.su && php artisan optimize:clear && php artisan optimize'

# бэкап конфига и голосов
tar czf ~/wrcs-backup-$(date +%F).tgz -C /var/www/wrcs.su data .env
```

Откат — revert коммита и повторный пуш в `master`; артефактов контейнера нет.

---

## Тесты

```bash
docker compose exec app php artisan test
```

15 тестов: рендер главной и кейса, RU/EN, 404, валидация и дедупликация оценок,
атомарность `JsonFile`, агрегация гистограммы. Тесты используют временные каталоги
и не трогают `data/`.

---

## Кастомизация

- **Тексты, роль, навыки, KPI** — `data/profile.json`.
- **Кейсы** — `data/projects.json`; обложки — `public/assets/img/covers/{slug}.svg`.
- **Бренд в логотипе/футере** — `PORTFOLIO_BRAND` в `.env` (по умолчанию `wrcs.ops`).
- **Цвета и типографика** — токены в начале `public/assets/css/app.css`.
- **Порядок фильтров портфолио** — `config/portfolio.php` → `filters.stack_priority`.

---

## Требования

- Laravel 13, PHP `^8.4` (`config.platform.php = 8.4.27` в `composer.json`);
- Composer 2 — только для локальной разработки и CI;
- для локального запуска — Docker + Compose;
- для продакшена — PHP-FPM 8.4 + nginx; Composer на сервере не нужен.
