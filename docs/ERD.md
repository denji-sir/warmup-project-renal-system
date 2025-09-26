# Entity Relationship Diagram - Real Estate System

## Таблицы и связи

```
users (1) ----< properties (M)
users (1) ----< realtor_profiles (1)
users (1) ----< posts (M)
users (M) ----< favorites (M) >---- properties (M) 
users (1) ----< exports (M)
users (1) ----< audit_log (M)
users (1) ----< orders_dev (M)
properties (1) ----< property_images (M)
properties (1) ----< orders_dev (M)
```

## Детальное описание таблиц

### users
- **PK**: id (BIGINT UNSIGNED AUTO_INCREMENT)
- email VARCHAR(255) UNIQUE NOT NULL
- pass_hash VARCHAR(255) NOT NULL
- role ENUM('admin','realtor','tenant') NOT NULL DEFAULT 'tenant'
- name VARCHAR(255) NOT NULL
- avatar_url VARCHAR(500) NULL
- bio TEXT NULL
- phone VARCHAR(32) NULL
- is_verified BOOLEAN NOT NULL DEFAULT FALSE
- created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

**Индексы:**
- PRIMARY KEY (id)
- UNIQUE KEY uk_users_email (email)
- KEY idx_users_role_verified (role, is_verified)

### realtor_profiles
- **PK**: user_id (BIGINT UNSIGNED)
- **FK**: user_id -> users.id ON DELETE CASCADE
- agency VARCHAR(255) NULL
- license_no VARCHAR(128) NULL
- rating DECIMAL(3,2) DEFAULT 0.00
- public_slug VARCHAR(128) UNIQUE NOT NULL
- socials_json JSON NULL
- created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

**Индексы:**
- PRIMARY KEY (user_id)
- UNIQUE KEY uk_realtor_slug (public_slug)
- KEY idx_realtor_rating (rating DESC)

### properties
- **PK**: id (BIGINT UNSIGNED AUTO_INCREMENT)
- **FK**: realtor_id -> users.id ON DELETE CASCADE
- title VARCHAR(500) NOT NULL
- slug VARCHAR(500) UNIQUE NOT NULL
- status ENUM('draft','pending','published','reserved','sold','archived') NOT NULL DEFAULT 'draft'
- type ENUM('sale','rent') NOT NULL
- category VARCHAR(64) NULL
- city VARCHAR(128) NOT NULL
- district VARCHAR(128) NULL
- address VARCHAR(500) NULL
- lat DECIMAL(9,6) NULL
- lng DECIMAL(9,6) NULL
- price DECIMAL(12,2) NOT NULL
- currency CHAR(3) NOT NULL DEFAULT 'RUB'
- area_total DECIMAL(8,2) NULL
- rooms TINYINT UNSIGNED NULL
- floor SMALLINT NULL
- floors_total SMALLINT UNSIGNED NULL
- year_built SMALLINT UNSIGNED NULL
- condition VARCHAR(64) NULL
- description MEDIUMTEXT NULL
- features_json JSON NULL
- published_at DATETIME NULL
- archived_at DATETIME NULL
- created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

**Индексы:**
- PRIMARY KEY (id)
- UNIQUE KEY uk_properties_slug (slug)
- KEY idx_properties_listing (status, type, city, price, area_total, rooms, created_at)
- KEY idx_properties_realtor (realtor_id, status)
- KEY idx_properties_location (city, district)
- FULLTEXT KEY ft_properties_search (title, description)

### property_images
- **PK**: id (BIGINT UNSIGNED AUTO_INCREMENT)
- **FK**: property_id -> properties.id ON DELETE CASCADE
- url VARCHAR(500) NOT NULL
- alt VARCHAR(255) NULL
- sort INT NOT NULL DEFAULT 0
- created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

**Индексы:**
- PRIMARY KEY (id)
- KEY idx_property_images (property_id, sort)

### favorites
- **FK**: user_id -> users.id ON DELETE CASCADE
- **FK**: property_id -> properties.id ON DELETE CASCADE
- created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

**Индексы:**
- PRIMARY KEY (user_id, property_id)
- KEY idx_favorites_property (property_id)

### posts
- **PK**: id (BIGINT UNSIGNED AUTO_INCREMENT)
- **FK**: author_id -> users.id ON DELETE CASCADE
- title VARCHAR(500) NOT NULL
- slug VARCHAR(500) UNIQUE NOT NULL
- content_md MEDIUMTEXT NULL
- cover_url VARCHAR(500) NULL
- status ENUM('draft','pending','published') NOT NULL DEFAULT 'draft'
- published_at DATETIME NULL
- created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

**Индексы:**
- PRIMARY KEY (id)
- UNIQUE KEY uk_posts_slug (slug)
- KEY idx_posts_status_published (status, published_at DESC)
- KEY idx_posts_author (author_id, status)

### exports
- **PK**: id (BIGINT UNSIGNED AUTO_INCREMENT)
- **FK**: user_id -> users.id ON DELETE CASCADE
- scope ENUM('properties','posts','users') NOT NULL
- filter_json JSON NULL
- format ENUM('csv','xlsx') NOT NULL
- file_path VARCHAR(500) NOT NULL
- created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

**Индексы:**
- PRIMARY KEY (id)
- KEY idx_exports_user_created (user_id, created_at DESC)

### audit_log
- **PK**: id (BIGINT UNSIGNED AUTO_INCREMENT)
- **FK**: user_id -> users.id ON DELETE SET NULL
- entity_type VARCHAR(32) NOT NULL
- entity_id BIGINT UNSIGNED NOT NULL
- action VARCHAR(32) NOT NULL
- before_json JSON NULL
- after_json JSON NULL
- created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

**Индексы:**
- PRIMARY KEY (id)
- KEY idx_audit_entity (entity_type, entity_id, created_at DESC)
- KEY idx_audit_user (user_id, created_at DESC)

### orders_dev
- **PK**: id (BIGINT UNSIGNED AUTO_INCREMENT)
- **FK**: buyer_id -> users.id ON DELETE CASCADE
- **FK**: property_id -> properties.id ON DELETE CASCADE
- action ENUM('buy','reserve') NOT NULL
- status ENUM('done','reverted') NOT NULL DEFAULT 'done'
- note TEXT NULL
- created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- reverted_at DATETIME NULL

**Индексы:**
- PRIMARY KEY (id)
- KEY idx_orders_buyer (buyer_id, status, created_at DESC)
- KEY idx_orders_property (property_id, status)
- UNIQUE KEY uk_orders_active (property_id, status) -- Обеспечивает одну активную покупку на объект

## Бизнес-правила и ограничения

1. **Роли пользователей**: admin, realtor, tenant
2. **Статусы объектов**: draft → pending → published → (reserved|sold) или archived
3. **Dev-корзина**: Только один активный order_dev на property (статус='done')
4. **Избранное**: Составной первичный ключ предотвращает дублирование
5. **Slug уникальность**: Для properties, posts, realtor_profiles
6. **Каскадное удаление**: При удалении пользователя удаляются связанные данные
7. **Мягкие ограничения**: rating от 0.00 до 5.00, координаты в пределах Земли