# Тестовое задание «Создание REST API приложения»

Проект реализует REST API для справочника организаций, зданий и видов деятельности с документацией Swagger UI.

**Основные Сущности**
- Организация: название, несколько телефонов, одно здание, несколько видов деятельности.
- Здание: адрес, географические координаты (широта/долгота).
- Деятельность: иерархическая классификация (до 3 уровней вложенности).

**Функционал API**
- Список организаций в конкретном здании.
- Список организаций по виду деятельности (с учетом дочерних видов).
- Поиск организаций по радиусу/прямоугольной области от точки.
- Получение организации по идентификатору.
- Поиск организаций по названию.

Бэкенд: Laravel 12, авторизация через Bearer-токен (Sanctum). База данных: PostgreSQL + PostGIS. Документация: Swagger UI доступна на корневом пути.

**Требования**
- Установленные `Docker` и `Docker Compose` (v2).
- Опционально: `make` для удобного запуска команд.

**Развёртывание**
- Вариант A — запуск с готовым образом:
    - Запуск: `docker compose -f compose.yaml up -d`
    - Остановка: `docker compose -f compose.yaml down`
- Вариант B — сборка из исходников:
    - Перед сборкой создайте файлы окружения (см. раздел "Переменные окружения").
    - Запуск: `docker compose -f compose.build.yaml up -d --build`
    - Остановка: `docker compose -f compose.build.yaml down`

Если в терминале доступен `make`:
- Запуск: `make up`
- Остановка: `make down`
- Полная пересборка: `make all_build`
- Точечная сборка сервиса: `make build container=<имя_сервиса>` (например, `container=app`)

**Переменные Окружения**
- Nginx: скопируйте `.env.nginx.example` в `.env.nginx` и при необходимости измените порт (`PORT=8080` по умолчанию).
- База данных: скопируйте `.env.database.example` в `.env.database` и при необходимости измените `POSTGRES_*` значения.
- Приложение (`.env`): для варианта B необходимо создать `.env` на основе `.env.example` и указать параметры подключения к БД:
    - `DB_CONNECTION=pgsql`
    - `DB_HOST=db`
    - `DB_PORT=5432`
    - `DB_DATABASE=${POSTGRES_DB}`
    - `DB_USERNAME=${POSTGRES_USER}`
    - `DB_PASSWORD=${POSTGRES_PASSWORD}`
    - Рекомендуется указать `APP_URL=http://localhost:${PORT}` и режим `APP_ENV=local|production`.

Примечание: при первом старте приложение сгенерирует `APP_KEY` и выполнит миграции/наполнение, если база пуста.

**Инициализация БД**
- При первом запуске контейнера приложения выполняются миграции и наполнение тестовыми данными (`InitDataSeeder`).
- Ручной запуск из Makefile:
    - Миграции: `make migrate`
    - Откат миграций: `make migrate_rollback`
    - Наполнение данными: `make seed`

**Команды Makefile**
- `make up` — запуск сервисов в фоне.
- `make down` — остановка и удаление сервисов.
- `make hard_down` — остановка с удалением томов БД.
- `make all_build` — полная пересборка всех сервисов.
- `make build container=<имя>` — сборка указанного сервиса.
- `make logs` — просмотр логов `app` и `nginx`.
- `make app-shell` — оболочка внутри контейнера приложения.
- `make db-shell` — оболочка внутри контейнера БД.
- `make ps` — статус контейнеров.
- `make key` — генерация `APP_KEY` (внутри контейнера).
- `make migrate` / `make migrate_rollback` — миграции.
- `make seed` — инициализация тестовыми данными.
- `make cache-clear` — очистка кэшей.
- `make swag` — генерация OpenAPI-спецификации.

Альтернатива без `make`:
- Запуск: `docker compose up -d`
- Остановка: `docker compose down`

**Swagger UI**
- Интерфейс доступен по URL: `http://localhost:${PORT}/` (по умолчанию `http://localhost:8080/`).
- Для обновления спецификации выполните `make swag`.

**Аутентификация**
- Тип: Bearer-токен (Laravel Sanctum). Передавайте заголовок `Authorization: Bearer <token>`.
- Получение токена: `POST /api/users/login` после `POST /api/users/registration`.

**Маршруты (фрагменты)**
- Пользователи: `/api/users/login`, `/api/users/registration`.
- Организации: `/api/organizations` и `/api/organizations/{organization}` — защищенные маршруты.

**Примечания**
- Готовый образ: `compose.yaml` использует `danisimoo/pet-project-php-laravel:1.0.1`.
- Сборка из исходников: `compose.build.yaml` использует `docker/php/Dockerfile`.
- База данных: `postgis/postgis:16-3.4-alpine`, данные хранятся в томе `dbdata`.
