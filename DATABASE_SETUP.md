# 🗄️ Настройка базы данных - Пошаговая инструкция

## 📋 Предварительные требования

Убедитесь, что у вас установлены:
- **MySQL 8.0+** или **MariaDB 10.4+**
- **PHP 8.2+** с расширением PDO MySQL
- Доступ к созданию базы данных

## 🚀 Быстрая настройка (рекомендуется)

### 1. Клонирование и переход в проект
```bash
cd /Users/ilya/Desktop/rental/warmup-project-renal-system
```

### 2. Настройка конфигурации базы данных
Отредактируйте файл `.env` (он уже создан):
```bash
# Откройте файл в редакторе
nano .env
# или
vim .env
# или любой другой редактор
```

**Важные настройки для базы данных:**
```env
# База данных - НАСТРОЙТЕ ПОД ВАШУ СРЕДУ
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=realestate_db
DB_USER=root
DB_PASS=your_mysql_password_here
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_0900_ai_ci
```

### 3. Создание базы данных

#### Вариант А: Через MySQL командную строку
```bash
# Подключитесь к MySQL
mysql -u root -p

# Создайте базу данных
CREATE DATABASE realestate_db CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;

# Проверьте создание
SHOW DATABASES;

# Выйдите из MySQL
EXIT;
```

#### Вариант Б: Через Makefile (если MySQL настроен)
```bash
make db-create
```

### 4. Выполнение миграций и загрузка данных
```bash
# Полная настройка (миграции + тестовые данные)
make setup

# Или по отдельности:
make migrate          # Только миграции
make seed            # Только тестовые данные
make fresh           # Очистить и создать заново
```

### 5. Запуск сервера разработки
```bash
make serve
```

Откройте браузер: **http://localhost:8000**

## 🔧 Детальная настройка

### Проверка подключения к MySQL

1. **Проверьте, что MySQL запущен:**
```bash
# macOS (если установлен через Homebrew)
brew services list | grep mysql

# Запуск MySQL если не запущен
brew services start mysql

# Или через системные настройки macOS
sudo /usr/local/mysql/support-files/mysql.server start
```

2. **Проверьте подключение:**
```bash
mysql -u root -p
```

### Настройка пользователя базы данных (опционально)

Для повышения безопасности создайте отдельного пользователя:

```sql
-- Подключитесь к MySQL как root
mysql -u root -p

-- Создайте пользователя
CREATE USER 'realestate_user'@'localhost' IDENTIFIED BY 'secure_password_123';

-- Дайте права на базу данных
GRANT ALL PRIVILEGES ON realestate_db.* TO 'realestate_user'@'localhost';

-- Примените изменения
FLUSH PRIVILEGES;

-- Проверьте пользователя
SELECT User, Host FROM mysql.user WHERE User = 'realestate_user';

EXIT;
```

Затем обновите `.env`:
```env
DB_USER=realestate_user
DB_PASS=secure_password_123
```

### Проверка успешной настройки

```bash
# Проверить статус миграций
make migrate-status

# Посмотреть созданные таблицы
mysql -u root -p realestate_db -e "SHOW TABLES;"

# Проверить количество пользователей
mysql -u root -p realestate_db -e "SELECT COUNT(*) as user_count FROM users;"

# Проверить объекты недвижимости
mysql -u root -p realestate_db -e "SELECT COUNT(*) as properties_count FROM properties;"
```

## 👥 Тестовые аккаунты

После выполнения `make seed` или `make setup`:

```bash
# Показать все тестовые аккаунты
make accounts
```

**Основные аккаунты:**
- **Админ**: `admin@realestate.local` / `password123`
- **Риелтор**: `ivan.petrov@realty.com` / `password123`
- **Покупатель**: `alex.buyer@gmail.com` / `password123`

## 🛠️ Полезные команды

```bash
# Информация о проекте
make info

# Просмотр логов
make logs

# Создание резервной копии
make backup

# Очистка временных файлов
make clean

# Проверка синтаксиса PHP
make check-syntax

# Остановка сервера (если запущен в фоне)
make stop
```

## 🔍 Диагностика проблем

### Ошибка подключения к базе данных

1. **Проверьте настройки в `.env`:**
```bash
grep -E "DB_" .env
```

2. **Проверьте, что MySQL запущен:**
```bash
# macOS
brew services list | grep mysql
sudo netstat -tlnp | grep :3306

# Попробуйте подключиться напрямую
mysql -h 127.0.0.1 -P 3306 -u root -p
```

3. **Проверьте права пользователя:**
```sql
SHOW GRANTS FOR 'root'@'localhost';
```

### Ошибки миграций

1. **Проверьте статус:**
```bash
php database/migrate.php status
```

2. **Посмотрите логи:**
```bash
tail -f storage/logs/error.log
```

3. **Пересоздайте базу:**
```bash
make db-reset  # ОСТОРОЖНО: удалит все данные!
```

### Проблемы с правами доступа

```bash
# Установить правильные права
make permissions

# Или вручную
chmod -R 755 .
chmod -R 777 storage/
```

## 📊 Структура базы данных

После успешной настройки будут созданы **9 таблиц**:

1. **users** - пользователи системы (админы, риелторы, арендаторы)
2. **categories** - категории недвижимости (квартиры, дома, коммерческая, земля)
3. **properties** - объекты недвижимости с полными характеристиками
4. **property_images** - фотографии объектов с метаданными
5. **posts** - публикации, статьи, новости
6. **user_favorites** - избранные объекты пользователей
7. **property_views** - статистика просмотров
8. **contact_requests** - заявки обратной связи
9. **audit_log** - журнал всех действий пользователей

## 🔒 Безопасность

### Рекомендации для продакшена:

1. **Смените пароли по умолчанию:**
```env
# В .env измените:
CSRF_SECRET=your-unique-long-secret-key-here
JWT_SECRET=your-unique-jwt-secret-key-here
DB_PASS=your-strong-database-password
```

2. **Отключите отладку:**
```env
APP_DEBUG=false
APP_ENV=production
```

3. **Настройте права доступа:**
```bash
chmod 644 .env
chmod -R 755 storage/
```

## ✅ Проверочный чек-лист

- [ ] MySQL установлен и запущен
- [ ] Файл `.env` настроен с правильными данными БД
- [ ] База данных `realestate_db` создана
- [ ] Миграции выполнены успешно (`make migrate`)
- [ ] Тестовые данные загружены (`make seed`)
- [ ] Сервер запускается без ошибок (`make serve`)
- [ ] Можно войти под тестовым аккаунтом
- [ ] Логи не содержат критических ошибок

## 🎉 Готово!

После выполнения всех шагов система готова к использованию:
- **Веб-интерфейс**: http://localhost:8000
- **Админка**: Войдите как `admin@realestate.local`
- **API**: Все endpoints доступны через веб-интерфейс

---

**Если возникли проблемы**, проверьте:
1. Логи в `storage/logs/`
2. Настройки в `.env`
3. Что MySQL запущен и доступен
4. Права доступа к файлам

**Для получения помощи:** `make help`