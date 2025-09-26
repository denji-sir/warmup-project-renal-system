# Структура проекта Real Estate System

```
warmup-project-renal-system/
├── .env.example                     # Environment configuration template
├── .gitignore                       # Git ignore patterns
├── composer.json                    # Dependencies and autoload
├── composer.lock                    # Lock file
├── README.md                        # Project documentation
├── Makefile                         # Development commands
├── docker-compose.yml               # Docker development environment
├── 
├── public/                          # Web accessible directory
│   ├── index.php                    # Application entry point
│   ├── .htaccess                    # Apache rewrite rules
│   ├── uploads/                     # Symlink to storage/uploads/
│   └── assets/                      # Compiled/minified assets
│       ├── css/
│       │   ├── main.css            # Main stylesheet
│       │   └── main.min.css        # Minified version
│       ├── js/
│       │   ├── main.js             # Main JavaScript
│       │   └── main.min.js         # Minified version
│       └── img/                     # Static images
│           ├── logo.svg
│           ├── placeholder.jpg
│           └── icons/
│
├── core/                           # Framework core classes
│   ├── Router.php                  # URL routing and dispatching
│   ├── Request.php                 # HTTP request wrapper
│   ├── Response.php                # HTTP response wrapper
│   ├── Controller.php              # Base controller class
│   ├── Model.php                   # Base model with PDO
│   ├── View.php                    # Template rendering engine
│   ├── DB.php                      # Database connection factory
│   ├── Auth.php                    # Authentication service
│   ├── Session.php                 # Session management
│   ├── CSRF.php                    # CSRF protection
│   ├── Validator.php               # Input validation
│   ├── Uploader.php                # File upload handling
│   ├── Logger.php                  # Logging service
│   ├── Utils.php                   # Utility functions
│   └── Exceptions/                 # Custom exceptions
│       ├── ValidationException.php
│       ├── AuthException.php
│       └── RouterException.php
│
├── app/                            # Application logic
│   ├── Controllers/                # Request handlers
│   │   ├── AuthController.php      # Registration/login/logout
│   │   ├── HomeController.php      # Landing page + search
│   │   ├── PropertyController.php  # CRUD properties + catalog
│   │   ├── FavoriteController.php  # Favorites management
│   │   ├── DevOrderController.php  # Dev cart functionality
│   │   ├── PostController.php      # Blog posts/articles
│   │   ├── ProfileController.php   # Public user profiles
│   │   ├── DashboardController.php # Tenant dashboard
│   │   ├── RealtorController.php   # Realtor dashboard
│   │   ├── AdminController.php     # Admin panel
│   │   └── ApiController.php       # AJAX endpoints
│   │
│   ├── Models/                     # Data models
│   │   ├── User.php               # User entity + auth logic
│   │   ├── RealtorProfile.php     # Extended realtor data
│   │   ├── Property.php           # Property entity + business logic
│   │   ├── PropertyImage.php      # Property images
│   │   ├── Favorite.php           # User favorites
│   │   ├── Post.php               # Blog posts
│   │   ├── ExportJob.php          # Export tracking
│   │   ├── AuditLog.php           # Audit trail
│   │   └── DevOrder.php           # Development cart orders
│   │
│   ├── Views/                      # Templates
│   │   ├── layouts/               # Layout templates
│   │   │   ├── base.php           # Main layout
│   │   │   ├── auth.php           # Authentication layout
│   │   │   ├── dashboard.php      # Dashboard layout
│   │   │   └── admin.php          # Admin layout
│   │   │
│   │   ├── partials/              # Reusable components
│   │   │   ├── header.php         # Site header
│   │   │   ├── footer.php         # Site footer
│   │   │   ├── navigation.php     # Main navigation
│   │   │   ├── breadcrumbs.php    # Breadcrumb navigation
│   │   │   ├── filters.php        # Search filters form
│   │   │   ├── card-property.php  # Property card component
│   │   │   ├── pagination.php     # Pagination component
│   │   │   ├── toasts.php         # Flash messages
│   │   │   └── modal.php          # Modal dialogs
│   │   │
│   │   ├── auth/                  # Authentication pages
│   │   │   ├── login.php          # Login form
│   │   │   ├── register.php       # Registration form
│   │   │   ├── forgot.php         # Password reset
│   │   │   └── verify.php         # Email verification
│   │   │
│   │   ├── home/                  # Landing pages
│   │   │   ├── index.php          # Homepage
│   │   │   └── about.php          # About page
│   │   │
│   │   ├── properties/            # Property pages
│   │   │   ├── index.php          # Catalog listing
│   │   │   ├── show.php           # Property detail
│   │   │   ├── create.php         # Add property form
│   │   │   └── edit.php           # Edit property form
│   │   │
│   │   ├── dashboard/             # Tenant dashboard
│   │   │   ├── index.php          # Dashboard home
│   │   │   ├── profile.php        # Profile settings
│   │   │   ├── favorites.php      # Saved properties
│   │   │   └── orders.php         # Dev purchase history
│   │   │
│   │   ├── realtor/               # Realtor dashboard  
│   │   │   ├── index.php          # Realtor dashboard
│   │   │   ├── properties.php     # Manage properties
│   │   │   ├── posts.php          # Manage blog posts
│   │   │   ├── analytics.php      # Performance analytics
│   │   │   └── exports.php        # Data exports
│   │   │
│   │   ├── admin/                 # Admin panel
│   │   │   ├── index.php          # Admin dashboard
│   │   │   ├── users.php          # User management
│   │   │   ├── moderation.php     # Content moderation
│   │   │   ├── properties.php     # Property oversight
│   │   │   ├── posts.php          # Post moderation
│   │   │   ├── exports.php        # Export management
│   │   │   ├── audit.php          # Audit log viewer
│   │   │   └── settings.php       # System settings
│   │   │
│   │   ├── profiles/              # Public profiles
│   │   │   ├── user.php           # User profile page
│   │   │   └── realtor.php        # Realtor profile page
│   │   │
│   │   ├── posts/                 # Blog section
│   │   │   ├── index.php          # Posts listing
│   │   │   └── show.php           # Post detail
│   │   │
│   │   └── errors/                # Error pages
│   │       ├── 404.php            # Not found
│   │       ├── 403.php            # Forbidden
│   │       └── 500.php            # Server error
│   │
│   └── Middleware/                 # Request middleware
│       ├── AuthMiddleware.php      # Authentication check
│       ├── RoleMiddleware.php      # Role-based access
│       ├── CSRFMiddleware.php      # CSRF protection
│       └── RateLimitMiddleware.php # Rate limiting
│
├── config/                         # Configuration files
│   ├── env.php                     # Environment loader
│   ├── config.php                  # Application config
│   ├── routes.php                  # Route definitions
│   └── database.php                # Database settings
│
├── migrations/                     # Database migrations
│   ├── 001_create_users_table.sql
│   ├── 002_create_realtor_profiles_table.sql
│   ├── 003_create_properties_table.sql
│   ├── 004_create_property_images_table.sql
│   ├── 005_create_favorites_table.sql
│   ├── 006_create_posts_table.sql
│   ├── 007_create_exports_table.sql
│   ├── 008_create_audit_log_table.sql
│   └── 009_create_orders_dev_table.sql
│
├── seeds/                          # Database seeders
│   ├── UsersSeeder.php            # Sample users and admins
│   ├── PropertiesSeeder.php       # Sample properties
│   ├── PostsSeeder.php            # Sample blog posts
│   └── run_seeds.php              # Seeder runner
│
├── storage/                        # Storage directory
│   ├── uploads/                    # User uploaded files
│   │   ├── avatars/               # User avatars
│   │   ├── properties/            # Property images
│   │   └── posts/                 # Post cover images
│   ├── exports/                    # Generated export files
│   │   ├── properties/            # Property exports
│   │   ├── users/                 # User exports
│   │   └── posts/                 # Post exports
│   └── logs/                       # Application logs
│       ├── app.log                # General application log
│       ├── error.log              # Error log
│       └── audit.log              # Audit trail log
│
├── resources/                      # Raw resources
│   ├── assets/                     # Source assets
│   │   ├── css/                   # Source stylesheets
│   │   │   ├── main.css           # Main stylesheet
│   │   │   ├── components/        # Component styles
│   │   │   └── pages/             # Page-specific styles
│   │   ├── js/                    # Source JavaScript
│   │   │   ├── main.js            # Main JavaScript
│   │   │   ├── components/        # JS components
│   │   │   └── pages/             # Page-specific JS
│   │   └── img/                   # Source images
│   └── lang/                       # Internationalization
│       ├── en.php                 # English translations
│       └── ru.php                 # Russian translations
│
├── tests/                          # Test suite
│   ├── Unit/                      # Unit tests
│   │   ├── Models/               
│   │   └── Core/
│   ├── Integration/               # Integration tests
│   │   └── Controllers/
│   ├── TestCase.php              # Base test class
│   └── bootstrap.php             # Test bootstrap
│
├── docs/                          # Documentation
│   ├── ERD.md                    # Entity Relationship Diagram
│   ├── ADR.md                    # Architecture Decision Record
│   ├── API.md                    # API documentation
│   ├── DEPLOYMENT.md             # Deployment guide
│   └── SECURITY.md               # Security guidelines
│
└── vendor/                        # Composer dependencies
    └── autoload.php              # Composer autoloader
```

## Ключевые особенности структуры

### 1. Разделение ответственности
- `core/` - фреймворк-агностик код
- `app/` - бизнес-логика приложения
- `config/` - конфигурация
- `resources/` - исходные ресурсы
- `public/` - веб-доступная директория

### 2. Безопасность
- Все PHP файлы вне `public/`
- Uploads через symlink с проверками
- Separate error pages для разных кодов

### 3. Масштабируемость  
- Модульная структура контроллеров
- Партиальные виды для переиспользования
- Middleware pipeline
- Отдельные директории для разных типов тестов

### 4. Development Experience
- Makefile для частых команд
- Docker compose для локальной разработки
- Четкое разделение source/compiled assets
- Comprehensive документация