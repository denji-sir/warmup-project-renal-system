# 🎯 Руководство по запуску системы недвижимости

## 📋 Предварительные требования

Убедитесь, что у вас установлены:
- **PHP 8.2+** с расширениями: pdo, pdo_mysql, json, mbstring
- **MySQL 8.0+** 
- **Веб-сервер** (Apache/Nginx)

## 🚀 Быстрый старт

### 1. Настройка базы данных

Создайте базу данных MySQL:
```sql
CREATE DATABASE realestate_db CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
```

### 2. Настройка конфигурации

Отредактируйте файл `.env`:
```bash
# Основные настройки
DB_HOST=127.0.0.1
DB_NAME=realestate_db
DB_USER=your_username
DB_PASS=your_password

# Измените секретные ключи в продакшене!
CSRF_SECRET=your-unique-csrf-secret-here
JWT_SECRET=your-unique-jwt-secret-here
```

### 3. Выполнение миграций

Создайте структуру базы данных:
```bash
cd /path/to/project
php database/migrate.php
```

Команды мигратора:
```bash
php database/migrate.php           # Выполнить все новые миграции
php database/migrate.php status    # Показать статус миграций
php database/migrate.php rollback  # Откатить последнюю миграцию
```

### 4. Заполнение тестовыми данными

Загрузите демонстрационные данные:
```bash
php database/seed.php              # Добавить к существующим данным
php database/seed.php --fresh      # Очистить и создать заново
```

### 5. Настройка веб-сервера

#### Apache
Убедитесь, что DocumentRoot указывает на папку `public/`:
```apache
<VirtualHost *:80>
    DocumentRoot /path/to/project/public
    ServerName realestate.local
    
    <Directory /path/to/project/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx  
```nginx
server {
    listen 80;
    server_name realestate.local;
    root /path/to/project/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

#### Локальная разработка
Для быстрого тестирования можно использовать встроенный сервер PHP:
```bash
cd public
php -S localhost:8000
```

## 👥 Тестовые аккаунты

После выполнения `php database/seed.php` доступны следующие аккаунты:

### 👑 Администраторы
- **Email**: `admin@realestate.local`
- **Пароль**: `password123`
- **Права**: полный доступ к системе

### 🏢 Риелторы
- **Иван Петров**: `ivan.petrov@realty.com` / `password123`
- **Елена Сидорова**: `elena.sidorova@premium.ru` / `password123`  
- **Сергей Волков**: `sergey.volkov@luxrealty.ru` / `password123`
- **Анна Комарова**: `anna.komarova@newbuild.moscow` / `password123`

### 🏠 Арендаторы/Покупатели  
- **Алексей Покупатель**: `alex.buyer@gmail.com` / `password123`
- **Мария Арендатор**: `maria.tenant@yahoo.com` / `password123`
- **Елена Смирнова**: `elena.smirnova@family.com` / `password123`
- **Павел Инвестор**: `pavel.business@invest.capital` / `password123`

## 📁 Структура проекта

```
warmup-project-renal-system/
├── app/
│   ├── Controllers/    # Контроллеры MVC
│   └── Models/         # Модели данных
├── config/
│   ├── config.php      # Основная конфигурация
│   ├── env.php         # Загрузчик переменных окружения
│   ├── helpers.php     # Вспомогательные функции
│   └── routes.php      # Маршруты приложения
├── core/               # Ядро Light MVC фреймворка
├── database/
│   ├── migrations/     # SQL миграции
│   ├── seeds/          # Файлы с тестовыми данными
│   ├── migrate.php     # Скрипт миграций
│   └── seed.php        # Скрипт заполнения данными
├── public/
│   ├── index.php       # Точка входа
│   └── .htaccess       # Конфигурация Apache
├── resources/
│   ├── assets/         # CSS, JS, изображения
│   └── views/          # Шаблоны страниц
└── storage/
    ├── logs/           # Лог-файлы
    └── uploads/        # Загруженные файлы
```

## 🔧 Основные возможности

### Для всех пользователей
- ✅ Просмотр каталога недвижимости
- ✅ Поиск и фильтрация по параметрам
- ✅ Детальная информация об объектах
- ✅ Контактные формы

### Для зарегистрированных пользователей  
- ✅ Личный кабинет
- ✅ Избранные объекты
- ✅ История просмотров
- ✅ Уведомления

### Для риелторов
- ✅ Добавление и редактирование объектов
- ✅ Загрузка фотографий
- ✅ Управление публикациями
- ✅ Статистика просмотров
- ✅ Обработка заявок

### Для администраторов
- ✅ Управление пользователями
- ✅ Модерация контента
- ✅ Журнал аудита
- ✅ Системная аналитика

## 🔒 Безопасность

Система включает:
- **CSRF защита** для всех форм
- **SQL injection защита** через PDO
- **XSS защита** при выводе данных
- **Ролевая авторизация** (RBAC)
- **Аудит всех действий** пользователей
- **Хеширование паролей** с солью

## 📊 База данных

Система использует **9 основных таблиц**:

1. **users** - пользователи системы
2. **categories** - категории недвижимости  
3. **properties** - объекты недвижимости
4. **property_images** - фотографии объектов
5. **posts** - публикации и новости
6. **user_favorites** - избранные объекты
7. **property_views** - статистика просмотров
8. **contact_requests** - заявки обратной связи
9. **audit_log** - журнал аудита действий

## 🐛 Отладка

### Логи системы
```bash
tail -f storage/logs/app.log      # Основные логи
tail -f storage/logs/error.log    # Ошибки
tail -f storage/logs/audit.log    # Аудит действий
```

### Режим отладки
В `.env` файле:
```
APP_DEBUG=true
LOG_LEVEL=debug
QUERY_LOG_ENABLED=true
```

## 🔄 Обновления

### Добавление новой миграции
1. Создайте файл `database/migrations/XXX_description.sql`
2. Выполните `php database/migrate.php`

### Добавление тестовых данных
1. Отредактируйте файлы в `database/seeds/`
2. Выполните `php database/seed.php --fresh`

## 📞 Поддержка

При возникновении проблем:

1. **Проверьте логи** в папке `storage/logs/`
2. **Убедитесь в правильности** настроек БД в `.env`
3. **Проверьте права доступа** к папкам `storage/` и `public/`
4. **Выполните миграции** если есть изменения в БД

## 🎉 Готово!

Система готова к использованию! Откройте в браузере адрес вашего сайта и войдите под одним из тестовых аккаунтов.

---

**Версия**: 1.0.0-alpha  
**Дата**: Сентябрь 2025  
**PHP**: 8.2+  
**MySQL**: 8.0+