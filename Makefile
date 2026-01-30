PROJECT_NAME := $(shell grep PROJECT_NAME .env | cut -d '=' -f 2)
COMPOSE := docker-compose -p $(PROJECT_NAME) -f docker-compose.yml

.PHONY: build up down start stop ps logs goto

build:
	@$(COMPOSE) build --no-cache

up:
	@$(COMPOSE) up -d

down:
	@$(COMPOSE) down

start:
	@$(COMPOSE) start

stop:
	@$(COMPOSE) stop

top:
	@$(COMPOSE) top

ps:
	@$(COMPOSE) ps -a

# Ver logs de un servicio específico
logs:
	@read -p "Service name (php, nginx, database): " SERVICE; \
	$(COMPOSE) logs -f $$SERVICE

# Entrar al contenedor (Alpine friendly con sh)
goto:
	@read -p "Service name (php, nginx, database): " SERVICE; \
	docker exec -it $(PROJECT_NAME)_$$SERVICE sh

# Make no procese los argumentos como objetivos
%:
	@:
