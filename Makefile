.PHONY: composer-install migrate up up-build down restart logs shell clean test mailhog adminer dev


composer-install:
	docker run --rm -v ${PWD}:/app -w /app hyperf/hyperf:8.3-alpine-v3.19-swoole composer install

migrate:
	docker compose exec app php bin/hyperf.php migrate
	
# Start services
up:
	docker-compose up -d

# Start services with build
up-build:
	docker-compose up --build -d

# Stop and remove services
down:
	docker-compose down

# Restart services
restart:
	docker-compose restart app

# Show logs from all services
logs:
	docker-compose logs -f app

# Open shell in app container
shell:
	docker-compose exec app sh

# Remove containers, networks, and volumes
clean:
	docker-compose down -v --remove-orphans
	docker system prune -f

# Run tests in app container
test:
	docker-compose exec app php bin/hyperf.php test

# Open Mailhog web interface (macOS)
mailhog:
	open http://localhost:8025

adminer:
	open http://localhost:8080


dev: composer-install up-build
