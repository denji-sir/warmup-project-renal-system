# Architecture Decision Record (ADR)

## Решение: Light MVC архитектура на чистом PHP

### Статус
Принято

### Контекст
Необходимо создать систему недвижимости с требованиями:
- Без фреймворков (Laravel/Symfony)
- PHP ≥ 8.2
- MySQL 8
- Vanilla JS + CSS
- Безопасность и производительность
- RBAC (admin/realtor/tenant)

### Рассмотренные варианты

1. **Full Framework** (Laravel/Symfony)
   - ❌ Запрещено требованиями
   - ❌ Избыточность для проекта
   - ✅ Быстрая разработка

2. **Микрофреймворк** (Slim, FastRoute)
   - ❌ Все равно зависимость
   - ✅ Легковесность
   - ❌ Дополнительное изучение API

3. **Light MVC на чистом PHP**
   - ✅ Полный контроль кода
   - ✅ Соответствует требованиям
   - ✅ Простота понимания
   - ✅ Легкая кастомизация
   - ❌ Больше кода "с нуля"

### Решение
**Light MVC архитектура** с разделением на:

#### Core Layer (`/core/`)
- **Router** - URL mapping, RESTful routes
- **Request/Response** - HTTP abstractions  
- **Controller** - Base controller с общими методами
- **Model** - Active Record pattern + PDO
- **View** - Template engine на чистом PHP
- **DB** - PDO factory + connection pool
- **Auth** - Сессии + JWT опционально
- **Session** - Secure session management
- **CSRF** - Cross-site request forgery protection
- **Validator** - Input validation & sanitization
- **Uploader** - File upload handling
- **Logger** - PSR-3 compatible logging

#### Application Layer (`/app/`)
- **Controllers/** - Бизнес-логика по доменам
- **Models/** - Entity models + business logic
- **Views/** - Templates с партиалами

#### Config Layer (`/config/`)
- **env.php** - Environment variables
- **config.php** - App configuration
- **routes.php** - Route definitions

### Архитектурные принципы

#### 1. Single Responsibility
```php
// Каждый класс имеет одну ответственность
Router::class;        // Только роутинг
Validator::class;     // Только валидация  
Uploader::class;      // Только загрузка файлов
```

#### 2. Dependency Injection
```php
class PropertyController extends Controller {
    public function __construct(
        private PropertyModel $propertyModel,
        private Validator $validator,
        private Auth $auth
    ) {}
}
```

#### 3. Interface Segregation
```php
interface Authenticatable {
    public function authenticate(string $email, string $password): ?User;
}

interface Authorizable {
    public function authorize(string $role, string $permission): bool;
}
```

#### 4. Security First
- CSRF токены на всех формах
- Prepared statements только
- Input validation + output escaping
- Role-based access control
- Secure session configuration

#### 5. Progressive Enhancement
```php
// Все действия работают через обычные формы
<form method="POST" action="/properties">
// JavaScript добавляет AJAX progressively
```

### Структура запроса

```
HTTP Request
    ↓
public/index.php (Bootstrap)
    ↓  
Router::dispatch()
    ↓
Controller::method()
    ↓
Model::query() → Database
    ↓
View::render() → Template
    ↓
Response::send() → Browser
```

### Преимущества выбранного решения

1. **Производительность**
   - Нет лишних абстракций
   - Прямые SQL запросы через PDO
   - Минимальный overhead

2. **Безопасность**
   - Полный контроль над кодом
   - Explicit security measures
   - No hidden vulnerabilities

3. **Maintainability**
   - Простая структура
   - Четкое разделение ответственности
   - PHP стандарты (PSR-4)

4. **Scalability**
   - Модульная архитектура
   - Легко добавить кэширование
   - Database optimization готова

### Недостатки и митигации

1. **Больше кода** → Переиспользуемые компоненты в /core/
2. **Нет готовых решений** → Собственные, но проверенные паттерны
3. **Потенциальные ошибки** → Strict typing + тесты

### Метрики успеха

- Время ответа < 200ms для обычных страниц
- Время ответа < 1s для сложных фильтров
- 100% покрытие CSRF защитой
- 0 SQL injection уязвимостей
- WCAG AA compliance

### Альтернативы для будущего

Если проект вырастет:
- Добавить PSR-11 контейнер
- Мигрировать на Symfony Components
- Добавить API слой (GraphQL/REST)
- Кэширование (Redis/Memcached)