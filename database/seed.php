<?php

require_once __DIR__ . '/../config/helpers.php';

class DatabaseSeeder
{
    private $connection;

    public function __construct()
    {
        try {
            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', 8889);  // MAMP default port
            $name = env('DB_NAME', 'realestate');
            $user = env('DB_USER', 'root');
            $password = env('DB_PASS', 'root');  // MAMP default password
            
            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
            
            $this->connection = new PDO(
                $dsn,
                $user,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
        } catch (PDOException $e) {
            die("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }

    public function run($fresh = false)
    {
        echo "Начинаем заполнение базы данных...\n";

        if ($fresh) {
            $this->truncateTables();
        }

        $this->seedUsers();
        $this->seedCategories();
        $this->seedProperties();
        $this->seedPropertyImages();

        echo "Заполнение базы данных завершено успешно!\n";
    }

    private function truncateTables()
    {
        echo "Очистка таблиц...\n";

        $tables = [
            'property_images',
            'properties',
            'categories',
            'users'
        ];

        $this->connection->exec('SET FOREIGN_KEY_CHECKS = 0');

        foreach ($tables as $table) {
            try {
                $this->connection->exec("TRUNCATE TABLE `$table`");
                echo "✓ Таблица $table очищена\n";
            } catch (PDOException $e) {
                echo "⚠ Ошибка при очистке таблицы $table: " . $e->getMessage() . "\n";
            }
        }

        $this->connection->exec('SET FOREIGN_KEY_CHECKS = 1');
    }

    private function seedUsers()
    {
        echo "Создание пользователей... ";

        $users = [
            [
                'username' => 'admin',
                'email' => 'admin@realestate.local',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'first_name' => 'Администратор',
                'last_name' => 'Системы',
                'phone' => '+7 (999) 123-45-67',
                'role' => 'admin',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'ivan_petrov',
                'email' => 'ivan.petrov@realestate.local',
                'password' => password_hash('realtor123', PASSWORD_DEFAULT),
                'first_name' => 'Иван',
                'last_name' => 'Петров',
                'phone' => '+7 (999) 234-56-78',
                'role' => 'realtor',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'elena_sidorova',
                'email' => 'elena.sidorova@realestate.local',
                'password' => password_hash('realtor123', PASSWORD_DEFAULT),
                'first_name' => 'Елена',
                'last_name' => 'Сидорова',
                'phone' => '+7 (999) 345-67-89',
                'role' => 'realtor',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'client_user',
                'email' => 'client@example.com',
                'password' => password_hash('client123', PASSWORD_DEFAULT),
                'first_name' => 'Алексей',
                'last_name' => 'Клиентов',
                'phone' => '+7 (999) 456-78-90',
                'role' => 'tenant',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
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

    private function seedCategories()
    {
        echo "Создание категорий... ";

        $categories = [
            [
                'name' => 'Квартиры',
                'slug' => 'apartments',
                'description' => 'Квартиры различной планировки',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Дома',
                'slug' => 'houses',
                'description' => 'Частные дома и коттеджи',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Коммерческая недвижимость',
                'slug' => 'commercial',
                'description' => 'Офисы, магазины, склады',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Земельные участки',
                'slug' => 'land',
                'description' => 'Участки под строительство',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
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

    private function seedProperties()
    {
        echo "Создание объектов недвижимости... ";

        $properties = [
            [
                'title' => '3-комнатная квартира в ЖК "Московский"',
                'slug' => '3k-apartment-moskovsky-complex',
                'description' => 'Просторная 3-комнатная квартира в новом жилом комплексе "Московский". Современная планировка, панорамные окна, качественная отделка. Развитая инфраструктура района, рядом метро, школы, детские сады.',
                'price' => 18500000.00,
                'currency' => 'RUB',
                'property_type' => 'apartment',
                'status' => 'published',
                'bedrooms' => 2,
                'bathrooms' => 2,
                'area_total' => 95.50,
                'floor' => 12,
                'total_floors' => 25,
                'year_built' => 2023,
                'full_address' => 'г. Москва, ул. Профсоюзная, д. 125, кв. 45',
                'city' => 'Москва',
                'region' => 'Московская область',
                'latitude' => 55.658667,
                'longitude' => 37.534667,
                'category_id' => 1,
                'user_id' => 2,
                'is_featured' => 1,
                'status' => 'active',
                'published_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Коттедж с участком в Рублевке',
                'slug' => 'cottage-rublevka-luxury',
                'description' => 'Роскошный коттедж в престижном районе Рублевка. Собственный участок 20 соток, бассейн, гараж на 3 машины. Элитная отделка, панорамные окна, камин.',
                'price' => 85000000.00,
                'currency' => 'RUB',
                'property_type' => 'house',
                'status' => 'active',
                'bedrooms' => 5,
                'bathrooms' => 4,
                'area_total' => 450.00,
                'floor' => 1,
                'total_floors' => 3,
                'year_built' => 2021,
                'full_address' => 'Московская область, Рублево-Успенское шоссе, д. 15',
                'city' => 'Рублевка',
                'region' => 'Московская область',
                'latitude' => 55.756667,
                'longitude' => 37.234667,
                'category_id' => 2,
                'user_id' => 2,
                'is_featured' => 1,
                'status' => 'active',
                'published_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Студия в центре на Арбате',
                'slug' => 'studio-arbat-center',
                'description' => 'Уютная студия в историческом центре Москвы на Арбате. Высокие потолки, отличная транспортная доступность, рядом театры и музеи.',
                'price' => 35000.00,
                'currency' => 'RUB',
                'property_type' => 'apartment',
                'status' => 'active',
                'bedrooms' => 1,
                'bathrooms' => 1,
                'area_total' => 42.00,
                'floor' => 3,
                'total_floors' => 5,
                'year_built' => 1960,
                'full_address' => 'г. Москва, ул. Арбат, д. 25, кв. 12',
                'city' => 'Москва',
                'region' => 'Московская область',
                'latitude' => 55.752667,
                'longitude' => 37.593667,
                'category_id' => 1,
                'user_id' => 3,
                'is_featured' => 0,
                'status' => 'active',
                'published_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Офис в БЦ "Федерация"',
                'slug' => 'office-federation-tower',
                'description' => 'Современный офис в престижном бизнес-центре "Федерация". Панорамные окна с видом на Москву-реку, развитая инфраструктура.',
                'price' => 180000.00,
                'currency' => 'RUB',
                'property_type' => 'commercial',
                'status' => 'active',
                'bedrooms' => 0,
                'bathrooms' => 2,
                'area_total' => 120.00,
                'floor' => 35,
                'total_floors' => 95,
                'year_built' => 2018,
                'full_address' => 'г. Москва, Пресненская наб., д. 12, БЦ "Федерация"',
                'city' => 'Москва',
                'region' => 'Московская область',
                'latitude' => 55.748667,
                'longitude' => 37.538667,
                'category_id' => 3,
                'user_id' => 3,
                'is_featured' => 0,
                'status' => 'active',
                'published_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Земельный участок в Истре',
                'slug' => 'land-plot-istra',
                'description' => 'Участок под строительство загородного дома в экологически чистом районе Истры. Коммуникации рядом, хорошая транспортная доступность.',
                'price' => 4500000.00,
                'currency' => 'RUB',
                'property_type' => 'land',
                'status' => 'active',
                'bedrooms' => 0,
                'bathrooms' => 0,
                'area_total' => 1500.00,
                'floor' => null,
                'total_floors' => null,
                'year_built' => null,
                'full_address' => 'Московская область, Истринский район, д. Лесная',
                'city' => 'Истра',
                'region' => 'Московская область',
                'latitude' => 55.912667,
                'longitude' => 36.862667,
                'category_id' => 4,
                'user_id' => 2,
                'is_featured' => 0,
                'status' => 'active',
                'published_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
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

    private function seedPropertyImages()
    {
        echo "Создание изображений недвижимости... ";

        $images = [
            // Для квартиры (property_id: 1)
            [
                'property_id' => 1, 
                'filename' => 'apartment_1_main.jpg', 
                'original_name' => 'apartment_1_main.jpg',
                'path' => '/uploads/properties/apartment_1_main.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 1024000,
                'is_primary' => 1, 
                'alt_text' => 'Гостиная с панорамными окнами'
            ],
            [
                'property_id' => 1, 
                'filename' => 'apartment_1_kitchen.jpg', 
                'original_name' => 'apartment_1_kitchen.jpg',
                'path' => '/uploads/properties/apartment_1_kitchen.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 856000,
                'is_primary' => 0, 
                'alt_text' => 'Современная кухня'
            ],
            [
                'property_id' => 1, 
                'filename' => 'apartment_1_bedroom.jpg', 
                'original_name' => 'apartment_1_bedroom.jpg',
                'path' => '/uploads/properties/apartment_1_bedroom.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 742000,
                'is_primary' => 0, 
                'alt_text' => 'Спальня'
            ],
            
            // Для коттеджа (property_id: 2)
            [
                'property_id' => 2, 
                'filename' => 'cottage_1_exterior.jpg', 
                'original_name' => 'cottage_1_exterior.jpg',
                'path' => '/uploads/properties/cottage_1_exterior.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 1256000,
                'is_primary' => 1, 
                'alt_text' => 'Фасад коттеджа'
            ],
            [
                'property_id' => 2, 
                'filename' => 'cottage_1_living.jpg', 
                'original_name' => 'cottage_1_living.jpg',
                'path' => '/uploads/properties/cottage_1_living.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 987000,
                'is_primary' => 0, 
                'alt_text' => 'Гостиная с камином'
            ],
            [
                'property_id' => 2, 
                'filename' => 'cottage_1_pool.jpg', 
                'original_name' => 'cottage_1_pool.jpg',
                'path' => '/uploads/properties/cottage_1_pool.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 1134000,
                'is_primary' => 0, 
                'alt_text' => 'Крытый бассейн'
            ],
            
            // Для студии (property_id: 3)
            [
                'property_id' => 3, 
                'filename' => 'studio_1_main.jpg', 
                'original_name' => 'studio_1_main.jpg',
                'path' => '/uploads/properties/studio_1_main.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 678000,
                'is_primary' => 1, 
                'alt_text' => 'Общий вид студии'
            ],
            [
                'property_id' => 3, 
                'filename' => 'studio_1_bathroom.jpg', 
                'original_name' => 'studio_1_bathroom.jpg',
                'path' => '/uploads/properties/studio_1_bathroom.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 456000,
                'is_primary' => 0, 
                'alt_text' => 'Ванная комната'
            ],
            
            // Для офиса (property_id: 4)
            [
                'property_id' => 4, 
                'filename' => 'office_1_main.jpg', 
                'original_name' => 'office_1_main.jpg',
                'path' => '/uploads/properties/office_1_main.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 892000,
                'is_primary' => 1, 
                'alt_text' => 'Офисное пространство'
            ],
            [
                'property_id' => 4, 
                'filename' => 'office_1_view.jpg', 
                'original_name' => 'office_1_view.jpg',
                'path' => '/uploads/properties/office_1_view.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 1345000,
                'is_primary' => 0, 
                'alt_text' => 'Вид из окна'
            ],
            
            // Для участка (property_id: 5)
            [
                'property_id' => 5, 
                'filename' => 'land_1_main.jpg', 
                'original_name' => 'land_1_main.jpg',
                'path' => '/uploads/properties/land_1_main.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 1023000,
                'is_primary' => 1, 
                'alt_text' => 'Общий вид участка'
            ],
            [
                'property_id' => 5, 
                'filename' => 'land_1_access.jpg', 
                'original_name' => 'land_1_access.jpg',
                'path' => '/uploads/properties/land_1_access.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 567000,
                'is_primary' => 0, 
                'alt_text' => 'Подъездная дорога'
            ]
        ];

        foreach ($images as $image) {
            $image['created_at'] = date('Y-m-d H:i:s');
            $image['updated_at'] = date('Y-m-d H:i:s');
            
            $placeholders = implode(',', array_fill(0, count($image), '?'));
            $columns = implode(',', array_keys($image));
            
            $stmt = $this->connection->prepare(
                "INSERT INTO property_images ({$columns}) VALUES ({$placeholders})"
            );
            $stmt->execute(array_values($image));
        }

        echo "✓ Создано " . count($images) . " изображений\n";
    }
}

// Запуск скрипта
if (php_sapi_name() === 'cli') {
    $fresh = in_array('--fresh', $argv);
    
    $seeder = new DatabaseSeeder();
    $seeder->run($fresh);
} else {
    echo "Этот скрипт должен запускаться из командной строки.\n";
}