<?php

/**
 * Database Seeder
 * 
 * Заполняет базу данных тестовыми данными для разработки и демонстрации.
 * Запуск: php database/seed.php
 */

// Загрузка конфигурации
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/helpers.php';

class DatabaseSeeder
{
    private PDO $connection;
    private string $seedsPath;

    public function __construct()
    {
        $this->seedsPath = __DIR__ . '/seeds';
        $this->connect();
    }

    /**
     * Подключение к базе данных
     */
    private function connect(): void
    {
        $host = config('database.host', 'localhost');
        $port = config('database.port', 3306);
        $name = config('database.name', 'realestate_db');
        $user = config('database.user', 'root');
        $password = config('database.password', '');
        $charset = config('database.charset', 'utf8mb4');

        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";

        try {
            $this->connection = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset}"
            ]);

            echo "✓ Подключение к базе данных установлено\n";

        } catch (PDOException $e) {
            echo "✗ Ошибка подключения: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    /**
     * Проверка наличия таблиц
     */
    private function checkTables(): bool
    {
        $requiredTables = [
            'users', 'categories', 'properties', 'property_images', 
            'posts', 'user_favorites', 'property_views', 'contact_requests', 'audit_log'
        ];

        foreach ($requiredTables as $table) {
            $stmt = $this->connection->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$table]);
            
            if (!$stmt->fetch()) {
                echo "✗ Таблица '{$table}' не найдена. Выполните сначала миграции.\n";
                echo "  Команда: php database/migrate.php\n";
                return false;
            }
        }

        return true;
    }

    /**
     * Очистка существующих данных
     */
    private function truncateTables(): void
    {
        echo "\n=== Очистка существующих данных ===\n";

        // Отключаем проверку внешних ключей
        $this->connection->exec("SET FOREIGN_KEY_CHECKS = 0");

        $tables = [
            'audit_log', 'contact_requests', 'property_views', 'user_favorites',
            'property_images', 'properties', 'posts', 'categories', 'users'
        ];

        foreach ($tables as $table) {
            $this->connection->exec("TRUNCATE TABLE {$table}");
            echo "✓ Очищена таблица {$table}\n";
        }

        // Включаем проверку внешних ключей
        $this->connection->exec("SET FOREIGN_KEY_CHECKS = 1");
    }

    /**
     * Выполнение всех сидов
     */
    public function run(bool $fresh = false): void
    {
        echo "\n=== Запуск заполнения базы данных ===\n";

        if (!$this->checkTables()) {
            exit(1);
        }

        if ($fresh) {
            $this->truncateTables();
        }

        echo "\n=== Создание тестовых данных ===\n";

        // Порядок важен из-за внешних ключей
        $this->seedUsers();
        $this->seedCategories();
        $this->seedProperties();
        $this->seedPropertyImages();
        $this->seedPosts();
        $this->seedUserFavorites();
        $this->seedPropertyViews();
        $this->seedContactRequests();
        $this->seedAuditLog();

        echo "\n✅ Все тестовые данные успешно созданы!\n";
        echo "\n=== Тестовые аккаунты ===\n";
        echo "👑 Администратор: admin@realestate.local / password123\n";
        echo "🏢 Риелтор 1: ivan.petrov@realty.com / password123\n";
        echo "🏢 Риелтор 2: elena.sidorova@premium.ru / password123\n";
        echo "🏠 Арендатор 1: alex.buyer@gmail.com / password123\n";
        echo "🏠 Арендатор 2: maria.tenant@yahoo.com / password123\n\n";
    }

    private function seedUsers(): void
    {
        echo "Создание пользователей... ";

        $users = [
            [
                'username' => 'admin',
                'email' => 'admin@realestate.local',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'admin',
                'first_name' => 'Александр',
                'last_name' => 'Администратов',
                'phone' => '+7 (499) 123-45-67',
                'bio' => 'Главный администратор системы недвижимости.',
                'is_active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'preferences' => json_encode([
                    'notifications' => ['email', 'system'],
                    'theme' => 'light',
                    'language' => 'ru'
                ])
            ],
            [
                'username' => 'ivan.petrov',
                'email' => 'ivan.petrov@realty.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'realtor',
                'first_name' => 'Иван',
                'last_name' => 'Петров',
                'phone' => '+7 (495) 987-65-43',
                'bio' => 'Опытный риелтор с 10-летним стажем. Специализируюсь на продаже элитной недвижимости в центре Москвы.',
                'is_active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'preferences' => json_encode([
                    'notifications' => ['email', 'sms', 'system'],
                    'working_hours' => '09:00-21:00',
                    'specialization' => 'elite_properties'
                ])
            ],
            [
                'username' => 'elena.sidorova',
                'email' => 'elena.sidorova@premium.ru',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'realtor',
                'first_name' => 'Елена',
                'last_name' => 'Сидорова',
                'phone' => '+7 (495) 555-77-88',
                'bio' => 'Молодой и энергичный риелтор. Помогу найти квартиру мечты по доступной цене!',
                'is_active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'preferences' => json_encode([
                    'notifications' => ['email', 'system'],
                    'working_hours' => '10:00-20:00',
                    'specialization' => 'apartments'
                ])
            ],
            [
                'username' => 'alex.buyer',
                'email' => 'alex.buyer@gmail.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'tenant',
                'first_name' => 'Алексей',
                'last_name' => 'Покупатель',
                'phone' => '+7 (926) 123-45-67',
                'bio' => 'Ищу квартиру для молодой семьи в спальном районе.',
                'is_active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'preferences' => json_encode([
                    'search_criteria' => [
                        'rooms' => '2-3',
                        'max_price' => 15000000,
                        'districts' => ['Юго-Западная', 'Сокольники']
                    ]
                ])
            ],
            [
                'username' => 'maria.tenant',
                'email' => 'maria.tenant@yahoo.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'tenant',
                'first_name' => 'Мария',
                'last_name' => 'Арендатор',
                'phone' => '+7 (916) 789-01-23',
                'bio' => 'Студентка МГУ, ищу комнату или студию в аренду рядом с университетом.',
                'is_active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'preferences' => json_encode([
                    'search_criteria' => [
                        'operation_type' => 'rent',
                        'max_price' => 50000,
                        'districts' => ['Центральный', 'Сокольническая линия метро']
                    ]
                ])
            ]
        ];

        foreach ($users as $user) {
            $placeholders = implode(',', array_fill(0, count($user), '?'));
            $columns = implode(',', array_keys($user));
            
            $stmt = $this->connection->prepare(
                "INSERT INTO users ({$columns}) VALUES ({$placeholders})"
            );
            $stmt->execute(array_values($user));
        }

        echo "✓ Создано " . count($users) . " пользователей\n";
    }

    private function seedCategories(): void
    {
        echo "Создание категорий... ";

        $categories = [
            [
                'name' => 'Квартиры',
                'slug' => 'apartments',
                'description' => 'Квартиры различной площади и планировки',
                'icon' => 'building',
                'color' => '#3498db',
                'sort_order' => 1,
                'seo_title' => 'Купить квартиру в Москве - цены, фото, планировки',
                'seo_description' => 'Большой выбор квартир в Москве от застройщика и вторичный рынок. Актуальные цены, фото, планировки квартир.'
            ],
            [
                'name' => 'Дома и коттеджи',
                'slug' => 'houses',
                'description' => 'Загородные дома, коттеджи, таунхаусы',
                'icon' => 'home',
                'color' => '#27ae60',
                'sort_order' => 2,
                'seo_title' => 'Купить дом, коттедж в Подмосковье',
                'seo_description' => 'Продажа домов и коттеджей в Московской области. Загородная недвижимость по выгодным ценам.'
            ],
            [
                'name' => 'Коммерческая недвижимость',
                'slug' => 'commercial',
                'description' => 'Офисы, магазины, склады, производственные помещения',
                'icon' => 'briefcase',
                'color' => '#8e44ad',
                'sort_order' => 3,
                'seo_title' => 'Коммерческая недвижимость - аренда и продажа',
                'seo_description' => 'Офисы, торговые помещения, склады. Коммерческая недвижимость в Москве и области.'
            ],
            [
                'name' => 'Земельные участки',
                'slug' => 'land',
                'description' => 'Участки под строительство, дачные участки',
                'icon' => 'map',
                'color' => '#f39c12',
                'sort_order' => 4,
                'seo_title' => 'Купить земельный участок в Подмосковье',
                'seo_description' => 'Земельные участки под строительство дома. Дачные участки в экологически чистых районах.'
            ]
        ];

        foreach ($categories as $category) {
            $placeholders = implode(',', array_fill(0, count($category), '?'));
            $columns = implode(',', array_keys($category));
            
            $stmt = $this->connection->prepare(
                "INSERT INTO categories ({$columns}) VALUES ({$placeholders})"
            );
            $stmt->execute(array_values($category));
        }

        echo "✓ Создано " . count($categories) . " категорий\n";
    }

    private function seedProperties(): void
    {
        echo "Создание объектов недвижимости... ";

        $properties = [
            [
                'user_id' => 2, // Ivan Petrov (realtor)
                'category_id' => 1, // Apartments
                'title' => '3-комнатная квартира в ЖК "Московский"',
                'slug' => '3k-apartment-moskovsky-complex',
                'description' => 'Просторная 3-комнатная квартира в новом жилом комплексе "Московский". Современная планировка, панорамные окна, качественная отделка. Развитая инфраструктура района, рядом метро, школы, детские сады.',
                'short_description' => 'Новая 3-комнатная квартира 95 кв.м в ЖК "Московский" с качественной отделкой',
                'operation_type' => 'sale',
                'price' => 18500000.00,
                'currency' => 'RUB',
                'country' => 'Россия',
                'region' => 'Московская область',
                'city' => 'Москва',
                'district' => 'Юго-Западный',
                'street' => 'ул. Профсоюзная',
                'house_number' => '125',
                'apartment' => '45',
                'postal_code' => '117485',
                'full_address' => 'г. Москва, ул. Профсоюзная, д. 125, кв. 45',
                'latitude' => 55.658667,
                'longitude' => 37.534667,
                'property_type' => 'apartment',
                'rooms' => 3,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'area_total' => 95.50,
                'area_living' => 65.30,
                'area_kitchen' => 15.20,
                'floor' => 12,
                'total_floors' => 25,
                'year_built' => 2023,
                'condition_type' => 'new',
                'features' => json_encode([
                    'parking' => 'underground',
                    'elevator' => true,
                    'balcony' => true,
                    'security' => '24/7',
                    'internet' => 'fiber',
                    'heating' => 'central',
                    'air_conditioning' => true,
                    'furniture' => 'partial'
                ]),
                'status' => 'active',
                'is_featured' => 1,
                'available_from' => date('Y-m-d'),
                'published_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => 2, // Ivan Petrov
                'category_id' => 2, // Houses
                'title' => 'Коттедж с участком в Рублевке',
                'slug' => 'cottage-rublevka-luxury',
                'description' => 'Роскошный коттедж площадью 450 кв.м на участке 20 соток в престижном районе Рублевка. Дом построен из натуральных материалов, имеет современную планировку и эксклюзивный дизайн интерьера.',
                'short_description' => 'Элитный коттедж 450 кв.м на участке 20 соток в Рублевке',
                'operation_type' => 'sale',
                'price' => 85000000.00,
                'currency' => 'RUB',
                'country' => 'Россия',
                'region' => 'Московская область',
                'city' => 'Рублево-Успенское',
                'district' => 'Рублевка',
                'street' => 'Рублево-Успенское шоссе',
                'house_number' => '25А',
                'postal_code' => '143082',
                'full_address' => 'Московская обл., Рублево-Успенское шоссе, 25А',
                'latitude' => 55.745317,
                'longitude' => 37.156234,
                'property_type' => 'house',
                'rooms' => 8,
                'bedrooms' => 5,
                'bathrooms' => 4,
                'area_total' => 450.00,
                'area_living' => 320.00,
                'floor' => 3,
                'total_floors' => 3,
                'year_built' => 2021,
                'condition_type' => 'excellent',
                'features' => json_encode([
                    'parking' => 'garage_3_cars',
                    'pool' => 'indoor',
                    'sauna' => true,
                    'fireplace' => true,
                    'garden' => true,
                    'security' => 'gated_community',
                    'smart_home' => true,
                    'wine_cellar' => true,
                    'guest_house' => true
                ]),
                'status' => 'active',
                'is_featured' => 1,
                'is_urgent' => 0,
                'published_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
            ],
            [
                'user_id' => 3, // Elena Sidorova
                'category_id' => 1, // Apartments
                'title' => '1-комнатная квартира на Арбате',
                'slug' => '1k-apartment-arbat-center',
                'description' => 'Уютная квартира-студия в историческом центре Москвы. Полностью меблирована и готова к проживанию. Идеально подходит для молодых специалистов и студентов.',
                'short_description' => 'Меблированная студия 42 кв.м на Арбате, готова к заселению',
                'operation_type' => 'rent',
                'price' => 0.00,
                'price_per_month' => 75000.00,
                'deposit' => 150000.00,
                'currency' => 'RUB',
                'country' => 'Россия',
                'region' => 'г. Москва',
                'city' => 'Москва',
                'district' => 'Центральный',
                'street' => 'ул. Арбат',
                'house_number' => '34',
                'apartment' => '12',
                'postal_code' => '119019',
                'full_address' => 'г. Москва, ул. Арбат, д. 34, кв. 12',
                'latitude' => 55.750446,
                'longitude' => 37.593834,
                'property_type' => 'apartment',
                'rooms' => 1,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'area_total' => 42.00,
                'area_living' => 28.00,
                'area_kitchen' => 12.00,
                'floor' => 3,
                'total_floors' => 5,
                'year_built' => 1950,
                'condition_type' => 'good',
                'features' => json_encode([
                    'furniture' => 'full',
                    'appliances' => 'full',
                    'internet' => 'wifi',
                    'heating' => 'central',
                    'historic_building' => true,
                    'pets_allowed' => false,
                    'smoking_allowed' => false
                ]),
                'status' => 'active',
                'available_from' => date('Y-m-d', strtotime('+1 week')),
                'published_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ],
            [
                'user_id' => 3, // Elena Sidorova
                'category_id' => 3, // Commercial
                'title' => 'Офисное помещение в БЦ "Москва-Сити"',
                'slug' => 'office-moscow-city-tower',
                'description' => 'Современное офисное помещение площадью 200 кв.м на 35 этаже башни "Федерация". Панорамный вид на Москву, современные коммуникации, круглосуточная охрана.',
                'short_description' => 'Офис 200 кв.м в башне "Федерация" с видом на город',
                'operation_type' => 'rent',
                'price' => 0.00,
                'price_per_month' => 350000.00,
                'currency' => 'RUB',
                'country' => 'Россия',
                'region' => 'г. Москва',
                'city' => 'Москва',
                'district' => 'Пресненский',
                'street' => 'Пресненская наб.',
                'house_number' => '12',
                'postal_code' => '123317',
                'full_address' => 'г. Москва, Пресненская наб., д. 12, БЦ "Федерация"',
                'latitude' => 55.748611,
                'longitude' => 37.539167,
                'property_type' => 'office',
                'area_total' => 200.00,
                'floor' => 35,
                'total_floors' => 62,
                'year_built' => 2017,
                'condition_type' => 'excellent',
                'features' => json_encode([
                    'parking' => 'underground',
                    'conference_rooms' => 2,
                    'air_conditioning' => 'central',
                    'security' => '24/7',
                    'elevator' => 'high_speed',
                    'internet' => 'fiber',
                    'panoramic_view' => true,
                    'kitchen' => true,
                    'reception' => true
                ]),
                'status' => 'active',
                'available_from' => date('Y-m-d', strtotime('+2 weeks')),
                'published_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
            ],
            [
                'user_id' => 2, // Ivan Petrov
                'category_id' => 4, // Land
                'title' => 'Земельный участок 15 соток в Подмосковье',
                'slug' => 'land-plot-15-acres-podolsk',
                'description' => 'Ровный участок правильной формы в коттеджном поселке "Зеленые холмы". Газ, электричество подведены к границе участка. Асфальтированная дорога, круглогодичный подъезд.',
                'short_description' => 'Участок 15 соток в КП "Зеленые холмы" с коммуникациями',
                'operation_type' => 'sale',
                'price' => 4500000.00,
                'currency' => 'RUB',
                'country' => 'Россия',
                'region' => 'Московская область',
                'city' => 'Подольск',
                'district' => 'Подольский район',
                'street' => 'КП "Зеленые холмы"',
                'house_number' => 'уч. 45',
                'postal_code' => '142100',
                'full_address' => 'Московская обл., Подольский р-н, КП "Зеленые холмы", уч. 45',
                'latitude' => 55.425833,
                'longitude' => 37.545556,
                'property_type' => 'land',
                'area_total' => 1500.00,
                'condition_type' => 'good',
                'features' => json_encode([
                    'electricity' => '15kW',
                    'gas' => 'natural',
                    'water' => 'well_possible',
                    'sewerage' => 'septic',
                    'road_access' => 'asphalt',
                    'forest_nearby' => true,
                    'lake_nearby' => false,
                    'flat_terrain' => true,
                    'fenced' => false
                ]),
                'status' => 'active',
                'published_at' => date('Y-m-d H:i:s', strtotime('-1 week'))
            ]
        ];

        foreach ($properties as $property) {
            $placeholders = implode(',', array_fill(0, count($property), '?'));
            $columns = implode(',', array_keys($property));
            
            $stmt = $this->connection->prepare(
                "INSERT INTO properties ({$columns}) VALUES ({$placeholders})"
            );
            $stmt->execute(array_values($property));
        }

        echo "✓ Создано " . count($properties) . " объектов\n";
    }

    private function seedPropertyImages(): void
    {
        echo "Создание изображений недвижимости... ";

        // Примеры изображений для демонстрации
        $images = [
            // Для квартиры (property_id: 1)
            ['property_id' => 1, 'filename' => 'apartment_1_main.jpg', 'is_primary' => 1, 'alt_text' => 'Гостиная с панорамными окнами'],
            ['property_id' => 1, 'filename' => 'apartment_1_kitchen.jpg', 'is_primary' => 0, 'alt_text' => 'Современная кухня'],
            ['property_id' => 1, 'filename' => 'apartment_1_bedroom.jpg', 'is_primary' => 0, 'alt_text' => 'Спальня'],
            ['property_id' => 1, 'filename' => 'apartment_1_bathroom.jpg', 'is_primary' => 0, 'alt_text' => 'Ванная комната'],
            
            // Для коттеджа (property_id: 2)
            ['property_id' => 2, 'filename' => 'cottage_1_exterior.jpg', 'is_primary' => 1, 'alt_text' => 'Фасад коттеджа'],
            ['property_id' => 2, 'filename' => 'cottage_1_living.jpg', 'is_primary' => 0, 'alt_text' => 'Гостиная с камином'],
            ['property_id' => 2, 'filename' => 'cottage_1_pool.jpg', 'is_primary' => 0, 'alt_text' => 'Крытый бассейн'],
            
            // Для студии (property_id: 3)
            ['property_id' => 3, 'filename' => 'studio_1_main.jpg', 'is_primary' => 1, 'alt_text' => 'Общий вид студии'],
            ['property_id' => 3, 'filename' => 'studio_1_view.jpg', 'is_primary' => 0, 'alt_text' => 'Вид из окна на Арбат'],
            
            // Для офиса (property_id: 4)
            ['property_id' => 4, 'filename' => 'office_1_main.jpg', 'is_primary' => 1, 'alt_text' => 'Открытое офисное пространство'],
            ['property_id' => 4, 'filename' => 'office_1_view.jpg', 'is_primary' => 0, 'alt_text' => 'Панорамный вид на Москву'],
            
            // Для участка (property_id: 5)
            ['property_id' => 5, 'filename' => 'land_1_plot.jpg', 'is_primary' => 1, 'alt_text' => 'Земельный участок'],
            ['property_id' => 5, 'filename' => 'land_1_access.jpg', 'is_primary' => 0, 'alt_text' => 'Подъездная дорога']
        ];

        foreach ($images as $i => $imageData) {
            $image = [
                'property_id' => $imageData['property_id'],
                'filename' => $imageData['filename'],
                'original_name' => str_replace('_', ' ', ucfirst(pathinfo($imageData['filename'], PATHINFO_FILENAME))) . '.jpg',
                'path' => 'storage/uploads/properties/' . $imageData['filename'],
                'thumbnail_path' => 'storage/uploads/properties/thumbs/' . $imageData['filename'],
                'mime_type' => 'image/jpeg',
                'size' => rand(500000, 2000000), // Размер от 500KB до 2MB
                'width' => rand(1200, 1920),
                'height' => rand(800, 1080),
                'alt_text' => $imageData['alt_text'],
                'sort_order' => $i % 10,
                'is_primary' => $imageData['is_primary'],
                'uploaded_by' => rand(2, 3) // Риелторы
            ];
            
            $placeholders = implode(',', array_fill(0, count($image), '?'));
            $columns = implode(',', array_keys($image));
            
            $stmt = $this->connection->prepare(
                "INSERT INTO property_images ({$columns}) VALUES ({$placeholders})"
            );
            $stmt->execute(array_values($image));
        }

        echo "✓ Создано " . count($images) . " изображений\n";
    }

    private function seedPosts(): void
    {
        echo "Создание публикаций... ";

        $posts = [
            [
                'user_id' => 2, // Ivan Petrov
                'title' => 'Тенденции рынка недвижимости в 2025 году',
                'slug' => 'real-estate-trends-2025',
                'excerpt' => 'Анализ основных тенденций и прогнозы развития рынка недвижимости в новом году.',
                'content' => 'В 2025 году рынок недвижимости показывает устойчивые тенденции роста. Основными драйверами являются развитие инфраструктуры, государственные программы поддержки и изменение предпочтений покупателей...',
                'post_type' => 'article',
                'status' => 'published',
                'published_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'allow_comments' => 1,
                'is_featured' => 1
            ],
            [
                'user_id' => 3, // Elena Sidorova
                'title' => 'Как выбрать квартиру для молодой семьи',
                'slug' => 'how-to-choose-apartment-young-family',
                'excerpt' => 'Практические советы по выбору первой квартиры: на что обратить внимание и как не ошибиться.',
                'content' => 'Покупка первой квартиры - важный шаг в жизни молодой семьи. В этой статье мы расскажем о ключевых факторах, которые стоит учесть при выборе жилья...',
                'post_type' => 'guide',
                'status' => 'published',
                'published_at' => date('Y-m-d H:i:s', strtotime('-1 week')),
                'allow_comments' => 1
            ],
            [
                'user_id' => 1, // Admin
                'title' => 'Новые возможности на нашем сайте',
                'slug' => 'new-website-features',
                'excerpt' => 'Представляем обновления платформы: улучшенный поиск, новые фильтры и мобильное приложение.',
                'content' => 'Мы постоянно работаем над улучшением нашего сервиса. В этом обновлении вас ждут новые возможности для поиска идеального жилья...',
                'post_type' => 'news',
                'status' => 'published',
                'published_at' => date('Y-m-d H:i:s'),
                'is_featured' => 1,
                'is_sticky' => 1
            ]
        ];

        foreach ($posts as $post) {
            $placeholders = implode(',', array_fill(0, count($post), '?'));
            $columns = implode(',', array_keys($post));
            
            $stmt = $this->connection->prepare(
                "INSERT INTO posts ({$columns}) VALUES ({$placeholders})"
            );
            $stmt->execute(array_values($post));
        }

        echo "✓ Создано " . count($posts) . " публикаций\n";
    }

    private function seedUserFavorites(): void
    {
        echo "Создание избранного... ";

        $favorites = [
            ['user_id' => 4, 'property_id' => 1, 'priority' => 'high', 'notes' => 'Отличная планировка, подходит по бюджету'],
            ['user_id' => 4, 'property_id' => 3, 'priority' => 'medium', 'notes' => 'Хорошее расположение, но дорого для аренды'],
            ['user_id' => 5, 'property_id' => 3, 'priority' => 'high', 'notes' => 'Идеально для студента!'],
            ['user_id' => 5, 'property_id' => 1, 'priority' => 'low', 'notes' => 'На будущее, когда появятся деньги']
        ];

        foreach ($favorites as $favorite) {
            $placeholders = implode(',', array_fill(0, count($favorite), '?'));
            $columns = implode(',', array_keys($favorite));
            
            $stmt = $this->connection->prepare(
                "INSERT INTO user_favorites ({$columns}) VALUES ({$placeholders})"
            );
            $stmt->execute(array_values($favorite));
        }

        echo "✓ Создано " . count($favorites) . " избранных\n";
    }

    private function seedPropertyViews(): void
    {
        echo "Создание просмотров... ";

        // Генерируем просмотры для демонстрации статистики
        for ($i = 0; $i < 50; $i++) {
            $view = [
                'property_id' => rand(1, 5),
                'user_id' => rand(1, 100) > 60 ? rand(1, 5) : null, // 40% анонимных просмотров
                'ip_address' => '192.168.' . rand(1, 255) . '.' . rand(1, 255),
                'user_agent' => 'Mozilla/5.0 (compatible; Demo browser)',
                'device_type' => ['desktop', 'mobile', 'tablet'][rand(0, 2)],
                'is_unique' => rand(0, 1),
                'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(0, 30) . ' days'))
            ];

            $placeholders = implode(',', array_fill(0, count($view), '?'));
            $columns = implode(',', array_keys($view));
            
            $stmt = $this->connection->prepare(
                "INSERT INTO property_views ({$columns}) VALUES ({$placeholders})"
            );
            $stmt->execute(array_values($view));
        }

        echo "✓ Создано 50 просмотров\n";
    }

    private function seedContactRequests(): void
    {
        echo "Создание заявок... ";

        $requests = [
            [
                'user_id' => 4, // Alex
                'property_id' => 1,
                'realtor_id' => 2, // Ivan
                'name' => 'Алексей Покупатель',
                'email' => 'alex.buyer@gmail.com',
                'phone' => '+7 (926) 123-45-67',
                'request_type' => 'viewing_request',
                'subject' => 'Запрос на просмотр квартиры',
                'message' => 'Здравствуйте! Интересует ваша 3-комнатная квартира на Профсоюзной. Можно договориться о просмотре на выходных?',
                'status' => 'new',
                'priority' => 'normal'
            ],
            [
                'user_id' => 5, // Maria
                'property_id' => 3,
                'realtor_id' => 3, // Elena
                'name' => 'Мария Арендатор',
                'email' => 'maria.tenant@yahoo.com',
                'phone' => '+7 (916) 789-01-23',
                'request_type' => 'property_inquiry',
                'subject' => 'Вопрос по аренде студии',
                'message' => 'Добрый день! Интересует студия на Арбате. Возможна ли аренда на длительный срок со скидкой?',
                'status' => 'in_progress',
                'priority' => 'normal'
            ],
            [
                'user_id' => null, // Anonymous
                'name' => 'Дмитрий Инвестор',
                'email' => 'investor@example.com',
                'phone' => '+7 (495) 000-00-00',
                'request_type' => 'general',
                'subject' => 'Консультация по инвестициям',
                'message' => 'Рассматриваю возможность инвестиций в недвижимость. Можете предложить интересные варианты?',
                'status' => 'new',
                'priority' => 'high'
            ]
        ];

        foreach ($requests as $request) {
            $placeholders = implode(',', array_fill(0, count($request), '?'));
            $columns = implode(',', array_keys($request));
            
            $stmt = $this->connection->prepare(
                "INSERT INTO contact_requests ({$columns}) VALUES ({$placeholders})"
            );
            $stmt->execute(array_values($request));
        }

        echo "✓ Создано " . count($requests) . " заявок\n";
    }

    private function seedAuditLog(): void
    {
        echo "Создание записей аудита... ";

        $logs = [
            [
                'user_id' => 1,
                'action' => 'user_login',
                'description' => 'Администратор вошел в систему',
                'severity' => 'low',
                'status' => 'success',
                'ip_address' => '192.168.1.100'
            ],
            [
                'user_id' => 2,
                'action' => 'property_create',
                'entity_type' => 'Property',
                'entity_id' => 1,
                'description' => 'Создан новый объект недвижимости',
                'new_values' => json_encode(['title' => '3-комнатная квартира в ЖК "Московский"']),
                'severity' => 'medium',
                'status' => 'success'
            ],
            [
                'user_id' => 4,
                'action' => 'favorite_add',
                'entity_type' => 'Property',
                'entity_id' => 1,
                'description' => 'Объект добавлен в избранное',
                'severity' => 'low',
                'status' => 'success'
            ]
        ];

        foreach ($logs as $log) {
            $placeholders = implode(',', array_fill(0, count($log), '?'));
            $columns = implode(',', array_keys($log));
            
            $stmt = $this->connection->prepare(
                "INSERT INTO audit_log ({$columns}) VALUES ({$placeholders})"
            );
            $stmt->execute(array_values($log));
        }

        echo "✓ Создано " . count($logs) . " записей аудита\n";
    }
}

// Обработка аргументов командной строки
$fresh = isset($argv[1]) && $argv[1] === '--fresh';

try {
    $seeder = new DatabaseSeeder();
    $seeder->run($fresh);
} catch (Exception $e) {
    echo "✗ Критическая ошибка: " . $e->getMessage() . "\n";
    echo "Файл: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}