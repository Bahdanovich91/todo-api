# ToDo-Api

REST API для управления списком задач.

## 🚀 Быстрый старт

### Автоматическая установка

```bash
# Запуск скрипта автоматической настройки
./setup.sh
```

Скрипт автоматически:
- Проверит наличие Laravel
- Запустит Docker контейнеры
- Установит зависимости
- Настроит базу данных Mysql
- Сгенерирует ключ приложения
- Запустит миграции

## Тестирование

```bash
# Запуск всех тестов
docker compose exec app php artisan test

# Запуск конкретного теста
docker compose exec app php artisan test tests/Feature/TaskControllerTest.php
```

## Запуск phpstan и cs-fixer

```bash
# cs-fixer
vendor/bin/php-cs-fixer fix
vendor/bin/php-cs-fixer fix --dry-run --diff

# phpstan
vendor/bin/phpstan analyse --configuration=phpstan.neon
```
