DC              ?= docker compose
APP             ?= app
NGINX           ?= nginx
DB              ?= db

.PHONY: up
up: ## Поднять сервисы в фоне
	$(DC) up -d

.PHONY: down
down: ## Остановить и удалить сервисы + сети
	$(DC) down

.PHONY: hard_down
hard_down: ## Остановить и удалить сервисы + сети + хранилищца
	$(DC) down -v

.PHONY: all_build
all_build: ## Пересобрать все образы
	$(DC) build --no-cache --pull

.PHONY: build
build: ## Пересобрать образ container=
	$(DC) build ${container}

.PHONY: limited_build_app
limited_build_app: ## Частично пересобрать контейнер с Laravel (без переустановки пакетов)
	$(DC) build --build-arg BUILD_APP=$(date) app

.PHONY: logs
logs: ## Логи (фолловинг) app+nginx
	$(DC) logs -f $(APP) $(NGINX)

.PHONY: app-shell
app-shell: ## Войти в шелл PHP-контейнера
	$(DC) exec $(APP) sh

.PHONY: db-shell
db-shell: ## Войти в шелл БД-контейнера
	$(DC) exec $(DB) sh

.PHONY: ps
ps: ## Список контейнеров
	$(DC) ps

.PHONY: key
key: ## Сгенерировать APP_KEY
	$(DC) exec $(APP) php artisan key:generate --force

.PHONY: migrate
migrate: ## Миграции
	$(DC) exec $(APP) "php artisan migrate"

.PHONY: migrate_rollback
migrate_rollback: ## Откатить миграции
	$(DC) exec $(APP) "php artisan migrate:rollback"

.PHONY: seed
seed: ## Сидирование
	$(DC) exec $(APP) php artisan db:seed --class=InitDataSeeder

.PHONY: cache-clear
cache-clear: ## Очистка кеша/конфигов/роутов
	$(DC) exec $(APP) php artisan optimize:clear

.PHONY: swag
swag: ## Сгенерировать OpenAPI (l5-swagger)
	$(DC) exec $(APP) php artisan l5-swagger:generate

