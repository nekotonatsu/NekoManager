# 開発環境
dev:
	docker compose -f docker-compose.yml -f docker-compose.dev.yml up

dev-build:
	docker compose -f docker-compose.yml -f docker-compose.dev.yml up --build

down:
	docker compose -f docker-compose.yml -f docker-compose.dev.yml down

# 初回セットアップ（開発）
setup-dev:
	docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d --build
	docker compose -f docker-compose.yml -f docker-compose.dev.yml exec app php artisan key:generate
	docker compose -f docker-compose.yml -f docker-compose.dev.yml exec app php artisan migrate

# Laravelコマンド shortcuts
migrate:
	docker compose -f docker-compose.yml -f docker-compose.dev.yml exec app php artisan migrate

fresh:
	docker compose -f docker-compose.yml -f docker-compose.dev.yml exec app php artisan migrate:fresh

tinker:
	docker compose -f docker-compose.yml -f docker-compose.dev.yml exec app php artisan tinker

logs:
	docker compose -f docker-compose.yml -f docker-compose.dev.yml logs -f

# 本番
prod-build:
	docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build

prod-down:
	docker compose -f docker-compose.yml -f docker-compose.prod.yml down