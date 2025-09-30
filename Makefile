# Development Makefile for Real Estate System

.PHONY: help setup install migrate seed serve clean test test-coverage docker-up docker-down docker-rebuild permissions

# Colors for output
GREEN = \033[0;32m
YELLOW = \033[1;33m
RED = \033[0;31m
NC = \033[0m # No Color

# Default target
help: ## Show this help message
	@echo "Real Estate System - Development Commands"
	@echo ""
	@echo "Usage: make [target]"
	@echo ""
	@echo "Available targets:"
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  ${GREEN}%-15s${NC} %s\n", $$1, $$2}' $(MAKEFILE_LIST)

setup: install permissions migrate seed ## Complete project setup
	@echo "${GREEN}✓ Project setup completed successfully!${NC}"
	@echo "${YELLOW}You can now run 'make serve' to start the development server${NC}"

install: ## Install composer dependencies
	@echo "${YELLOW}Installing composer dependencies...${NC}"
	composer install
	@if [ ! -f .env ]; then \
		cp .env.example .env; \
		echo "${GREEN}✓ Created .env file from template${NC}"; \
	fi

permissions: ## Set up directories and permissions
	@echo "${YELLOW}Setting up directories and permissions...${NC}"
	@mkdir -p storage/uploads storage/exports storage/logs
	@mkdir -p public/assets/css public/assets/js public/assets/img
	@chmod -R 755 storage/
	@if [ ! -L public/uploads ] && [ -d storage/uploads ]; then \
		ln -sf ../storage/uploads public/uploads; \
		echo "${GREEN}✓ Created uploads symlink${NC}"; \
	fi

migrate: ## Run database migrations
	@echo "${YELLOW}Running database migrations...${NC}"
	@if [ -f database/migrate.php ]; then \
		php database/migrate.php; \
	else \
		echo "${RED}Migration script not found${NC}"; \
	fi

seed: ## Seed database with sample data
	@echo "${YELLOW}Seeding database with sample data...${NC}"
	@if [ -f database/seed.php ]; then \
		php database/seed.php; \
	else \
		echo "${RED}Seeder script not found${NC}"; \
	fi

serve: ## Start development server
	@echo "${GREEN}Starting development server at http://localhost:8000${NC}"
	@echo "${YELLOW}Press Ctrl+C to stop${NC}"
	php -S localhost:8000 -t public

test: ## Run PHPUnit tests
	@echo "${YELLOW}Running tests...${NC}"
	vendor/bin/phpunit

test-coverage: ## Run tests with coverage report
	@echo "${YELLOW}Running tests with coverage...${NC}"
	vendor/bin/phpunit --coverage-html coverage
	@echo "${GREEN}Coverage report generated in coverage/ directory${NC}"

clean: ## Clean cache, logs and temporary files
	@echo "${YELLOW}Cleaning up...${NC}"
	@rm -rf storage/logs/*.log
	@rm -rf storage/exports/*
	@rm -rf coverage/
	@echo "${GREEN}✓ Cleaned cache and logs${NC}"

docker-up: ## Start Docker development environment
	@echo "${YELLOW}Starting Docker containers...${NC}"
	docker-compose up -d
	@echo "${GREEN}✓ Docker containers started${NC}"
	@echo "${YELLOW}Web: http://localhost:8080${NC}"
	@echo "${YELLOW}Database: localhost:3306${NC}"
	@echo "${YELLOW}MailPit: http://localhost:8025${NC}"

docker-down: ## Stop Docker containers
	@echo "${YELLOW}Stopping Docker containers...${NC}"
	docker-compose down
	@echo "${GREEN}✓ Docker containers stopped${NC}"

docker-rebuild: ## Rebuild Docker containers
	@echo "${YELLOW}Rebuilding Docker containers...${NC}"
	docker-compose down
	docker-compose build --no-cache
	docker-compose up -d
	@echo "${GREEN}✓ Docker containers rebuilt${NC}"

docker-logs: ## Show Docker logs
	docker-compose logs -f

docker-shell: ## Access application container shell
	docker-compose exec app bash

db-create: ## Create database (requires MySQL client)
	@echo "${YELLOW}Creating database...${NC}"
	@read -p "Enter MySQL root password: " -s password; \
	echo ""; \
	mysql -u root -p$$password -e "CREATE DATABASE IF NOT EXISTS realestate CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;"
	@echo "${GREEN}✓ Database created${NC}"

db-reset: ## Reset database (DROP and CREATE)
	@echo "${RED}WARNING: This will delete all data!${NC}"
	@read -p "Are you sure? (y/N): " confirm; \
	if [ "$$confirm" = "y" ] || [ "$$confirm" = "Y" ]; then \
		read -p "Enter MySQL root password: " -s password; \
		echo ""; \
		mysql -u root -p$$password -e "DROP DATABASE IF EXISTS realestate; CREATE DATABASE realestate CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;"; \
		echo "${GREEN}✓ Database reset${NC}"; \
		make migrate seed; \
	fi

assets-build: ## Build and minify assets
	@echo "${YELLOW}Building assets...${NC}"
	@# CSS minification (simple approach)
	@if command -v cssnano >/dev/null 2>&1; then \
		cssnano resources/assets/css/main.css public/assets/css/main.min.css; \
	else \
		cp resources/assets/css/main.css public/assets/css/main.css; \
	fi
	@# JS minification (simple approach)  
	@if command -v terser >/dev/null 2>&1; then \
		terser resources/assets/js/main.js -o public/assets/js/main.min.js; \
	else \
		cp resources/assets/js/main.js public/assets/js/main.js; \
	fi
	@echo "${GREEN}✓ Assets built${NC}"

assets-watch: ## Watch and rebuild assets on changes
	@echo "${YELLOW}Watching assets for changes...${NC}"
	@echo "${YELLOW}Press Ctrl+C to stop${NC}"
	@while true; do \
		inotifywait -r -e modify resources/assets/ 2>/dev/null || sleep 2; \
		make assets-build; \
	done

check-syntax: ## Check PHP syntax in all files
	@echo "${YELLOW}Checking PHP syntax...${NC}"
	@find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \; | grep -v "No syntax errors"
	@echo "${GREEN}✓ Syntax check completed${NC}"

check-security: ## Basic security check
	@echo "${YELLOW}Performing basic security checks...${NC}"
	@# Check for .env in public directory
	@if [ -f public/.env ]; then \
		echo "${RED}WARNING: .env file found in public directory!${NC}"; \
	fi
	@# Check file permissions
	@find storage/ -type f -perm /044 -exec echo "WARNING: World-readable file: {}" \;
	@echo "${GREEN}✓ Security check completed${NC}"

info: ## Show project information
	@echo "${GREEN}Real Estate System${NC}"
	@echo "PHP Version: $(shell php -v | head -n1)"
	@echo "Composer Version: $(shell composer -V)"
	@echo "Project Directory: $(PWD)"
	@echo "Environment: $(shell grep APP_ENV .env 2>/dev/null | cut -d'=' -f2 || echo 'development')"
	@echo "Debug Mode: $(shell grep APP_DEBUG .env 2>/dev/null | cut -d'=' -f2 || echo 'true')"

backup: ## Create database backup
	@echo "${YELLOW}Creating database backup...${NC}"
	@mkdir -p backups
	@DB_NAME=$$(grep DB_NAME .env | cut -d'=' -f2); \
	DB_USER=$$(grep DB_USER .env | cut -d'=' -f2); \
	TIMESTAMP=$$(date +%Y%m%d_%H%M%S); \
	read -p "Enter database password: " -s password; \
	echo ""; \
	mysqldump -u$$DB_USER -p$$password $$DB_NAME > backups/backup_$$TIMESTAMP.sql
	@echo "${GREEN}✓ Backup created in backups/ directory${NC}"

restore: ## Restore database from backup
	@echo "${YELLOW}Available backups:${NC}"
	@ls -la backups/*.sql 2>/dev/null || echo "No backups found"
	@read -p "Enter backup filename: " backup; \
	if [ -f "backups/$$backup" ]; then \
		DB_NAME=$$(grep DB_NAME .env | cut -d'=' -f2); \
		DB_USER=$$(grep DB_USER .env | cut -d'=' -f2); \
		read -p "Enter database password: " -s password; \
		echo ""; \
		mysql -u$$DB_USER -p$$password $$DB_NAME < backups/$$backup; \
		echo "${GREEN}✓ Database restored${NC}"; \
	else \
		echo "${RED}Backup file not found${NC}"; \
	fi