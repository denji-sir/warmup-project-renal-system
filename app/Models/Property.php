<?php

namespace App\Models;

use Core\Database;
use PDO;

/**
 * Property Model - Модель для управления объектами недвижимости
 */
class Property
{
    private PDO $db;
    
    // Статусы объектов
    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';  
    const STATUS_RENTED = 'rented';
    const STATUS_INACTIVE = 'inactive';
    
    // Типы объектов
    const TYPE_APARTMENT = 'apartment';
    const TYPE_HOUSE = 'house';
    const TYPE_ROOM = 'room';
    const TYPE_COMMERCIAL = 'commercial';
    const TYPE_OFFICE = 'office';
    const TYPE_GARAGE = 'garage';

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Получить все объекты с пагинацией
     */
    public function getAll(int $page = 1, int $limit = 10, array $filters = []): array
    {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT p.*, u.username as owner_name, c.name as category_name,
                       (SELECT COUNT(*) FROM property_images pi WHERE pi.property_id = p.id) as image_count
                FROM properties p 
                LEFT JOIN users u ON p.user_id = u.id 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE 1=1";
        
        $params = [];
        
        // Применение фильтров
        if (!empty($filters['status'])) {
            $sql .= " AND p.status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['type'])) {
            $sql .= " AND p.type = :type";
            $params['type'] = $filters['type'];
        }
        
        if (!empty($filters['city'])) {
            $sql .= " AND p.city LIKE :city";
            $params['city'] = '%' . $filters['city'] . '%';
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= :min_price";
            $params['min_price'] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= :max_price";
            $params['max_price'] = $filters['max_price'];
        }
        
        if (!empty($filters['rooms'])) {
            $sql .= " AND p.rooms = :rooms";
            $params['rooms'] = $filters['rooms'];
        }
        
        if (!empty($filters['user_id'])) {
            $sql .= " AND p.user_id = :user_id";
            $params['user_id'] = $filters['user_id'];
        }
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (p.title LIKE :search OR p.description LIKE :search OR p.address LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        // Сортировка
        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDirection = $filters['order_direction'] ?? 'DESC';
        $sql .= " ORDER BY p.{$orderBy} {$orderDirection}";
        
        $sql .= " LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        
        // Привязка параметров
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Получить общее количество объектов с фильтрами
     */
    public function getCount(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) FROM properties p WHERE 1=1";
        $params = [];
        
        // Применение тех же фильтров что и в getAll
        if (!empty($filters['status'])) {
            $sql .= " AND p.status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['type'])) {
            $sql .= " AND p.type = :type";
            $params['type'] = $filters['type'];
        }
        
        if (!empty($filters['city'])) {
            $sql .= " AND p.city LIKE :city";
            $params['city'] = '%' . $filters['city'] . '%';
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= :min_price";
            $params['min_price'] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= :max_price";
            $params['max_price'] = $filters['max_price'];
        }
        
        if (!empty($filters['rooms'])) {
            $sql .= " AND p.rooms = :rooms";
            $params['rooms'] = $filters['rooms'];
        }
        
        if (!empty($filters['user_id'])) {
            $sql .= " AND p.user_id = :user_id";
            $params['user_id'] = $filters['user_id'];
        }
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (p.title LIKE :search OR p.description LIKE :search OR p.address LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Получить объект по ID
     */
    public function getById(int $id): ?array
    {
        $sql = "SELECT p.*, u.username as owner_name, u.email as owner_email, 
                       c.name as category_name, c.slug as category_slug
                FROM properties p 
                LEFT JOIN users u ON p.user_id = u.id 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $property = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($property) {
            // Получить изображения
            $property['images'] = $this->getPropertyImages($id);
        }
        
        return $property ?: null;
    }

    /**
     * Создать новый объект недвижимости
     */
    public function create(array $data): int
    {
        $sql = "INSERT INTO properties (
                    title, slug, description, type, price, currency,
                    area, rooms, bathrooms, floor, total_floors,
                    address, city, district, latitude, longitude,
                    user_id, category_id, status, created_at, updated_at
                ) VALUES (
                    :title, :slug, :description, :type, :price, :currency,
                    :area, :rooms, :bathrooms, :floor, :total_floors,
                    :address, :city, :district, :latitude, :longitude,
                    :user_id, :category_id, :status, NOW(), NOW()
                )";
        
        $stmt = $this->db->prepare($sql);
        
        // Генерация slug если не указан
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['title']);
        }
        
        $stmt->execute([
            ':title' => $data['title'],
            ':slug' => $data['slug'],
            ':description' => $data['description'] ?? null,
            ':type' => $data['type'],
            ':price' => $data['price'],
            ':currency' => $data['currency'] ?? 'RUB',
            ':area' => $data['area'] ?? null,
            ':rooms' => $data['rooms'] ?? null,
            ':bathrooms' => $data['bathrooms'] ?? null,
            ':floor' => $data['floor'] ?? null,
            ':total_floors' => $data['total_floors'] ?? null,
            ':address' => $data['address'] ?? null,
            ':city' => $data['city'] ?? null,
            ':district' => $data['district'] ?? null,
            ':latitude' => $data['latitude'] ?? null,
            ':longitude' => $data['longitude'] ?? null,
            ':user_id' => $data['user_id'],
            ':category_id' => $data['category_id'] ?? null,
            ':status' => $data['status'] ?? self::STATUS_DRAFT
        ]);
        
        return $this->db->lastInsertId();
    }

    /**
     * Обновить объект недвижимости
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE properties SET 
                    title = :title,
                    slug = :slug,
                    description = :description,
                    type = :type,
                    price = :price,
                    currency = :currency,
                    area = :area,
                    rooms = :rooms,
                    bathrooms = :bathrooms,
                    floor = :floor,
                    total_floors = :total_floors,
                    address = :address,
                    city = :city,
                    district = :district,
                    latitude = :latitude,
                    longitude = :longitude,
                    category_id = :category_id,
                    status = :status,
                    updated_at = NOW()
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        
        // Генерация slug если изменился title
        if (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = $this->generateSlug($data['title']);
        }
        
        return $stmt->execute([
            ':id' => $id,
            ':title' => $data['title'],
            ':slug' => $data['slug'],
            ':description' => $data['description'] ?? null,
            ':type' => $data['type'],
            ':price' => $data['price'],
            ':currency' => $data['currency'] ?? 'RUB',
            ':area' => $data['area'] ?? null,
            ':rooms' => $data['rooms'] ?? null,
            ':bathrooms' => $data['bathrooms'] ?? null,
            ':floor' => $data['floor'] ?? null,
            ':total_floors' => $data['total_floors'] ?? null,
            ':address' => $data['address'] ?? null,
            ':city' => $data['city'] ?? null,
            ':district' => $data['district'] ?? null,
            ':latitude' => $data['latitude'] ?? null,
            ':longitude' => $data['longitude'] ?? null,
            ':category_id' => $data['category_id'] ?? null,
            ':status' => $data['status']
        ]);
    }

    /**
     * Удалить объект недвижимости
     */
    public function delete(int $id): bool
    {
        // Сначала удаляем связанные изображения
        $this->deletePropertyImages($id);
        
        $sql = "DELETE FROM properties WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Получить изображения объекта
     */
    public function getPropertyImages(int $propertyId): array
    {
        $sql = "SELECT * FROM property_images WHERE property_id = :property_id ORDER BY sort_order, created_at";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':property_id', $propertyId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Добавить изображение к объекту
     */
    public function addImage(int $propertyId, string $filename, bool $isMain = false): int
    {
        // Если это главное изображение, убираем флаг у других
        if ($isMain) {
            $this->db->prepare("UPDATE property_images SET is_main = 0 WHERE property_id = :property_id")
                     ->execute([':property_id' => $propertyId]);
        }
        
        $sql = "INSERT INTO property_images (property_id, filename, is_main, created_at) 
                VALUES (:property_id, :filename, :is_main, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':property_id' => $propertyId,
            ':filename' => $filename,
            ':is_main' => $isMain ? 1 : 0
        ]);
        
        return $this->db->lastInsertId();
    }

    /**
     * Удалить изображение
     */
    public function deleteImage(int $imageId): bool
    {
        $sql = "DELETE FROM property_images WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $imageId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Удалить все изображения объекта
     */
    private function deletePropertyImages(int $propertyId): bool
    {
        $sql = "DELETE FROM property_images WHERE property_id = :property_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':property_id', $propertyId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Установить главное изображение
     */
    public function setMainImage(int $propertyId, int $imageId): bool
    {
        // Убираем флаг у всех изображений объекта
        $this->db->prepare("UPDATE property_images SET is_main = 0 WHERE property_id = :property_id")
                 ->execute([':property_id' => $propertyId]);
        
        // Устанавливаем флаг для выбранного изображения
        $sql = "UPDATE property_images SET is_main = 1 WHERE id = :id AND property_id = :property_id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':id' => $imageId,
            ':property_id' => $propertyId
        ]);
    }

    /**
     * Получить популярные объекты
     */
    public function getPopular(int $limit = 5): array
    {
        $sql = "SELECT p.*, u.username as owner_name, 
                       (SELECT filename FROM property_images pi WHERE pi.property_id = p.id AND pi.is_main = 1 LIMIT 1) as main_image,
                       (SELECT COUNT(*) FROM property_views pv WHERE pv.property_id = p.id) as view_count
                FROM properties p 
                LEFT JOIN users u ON p.user_id = u.id 
                WHERE p.status = 'active'
                ORDER BY view_count DESC, p.created_at DESC 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Получить последние объекты
     */
    public function getLatest(int $limit = 5): array
    {
        $sql = "SELECT p.*, u.username as owner_name,
                       (SELECT filename FROM property_images pi WHERE pi.property_id = p.id AND pi.is_main = 1 LIMIT 1) as main_image
                FROM properties p 
                LEFT JOIN users u ON p.user_id = u.id 
                WHERE p.status = 'active'
                ORDER BY p.created_at DESC 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Поиск объектов
     */
    public function search(string $query, int $limit = 20): array
    {
        $sql = "SELECT p.*, u.username as owner_name,
                       (SELECT filename FROM property_images pi WHERE pi.property_id = p.id AND pi.is_main = 1 LIMIT 1) as main_image,
                       MATCH(p.title, p.description, p.address) AGAINST(:query) as relevance
                FROM properties p 
                LEFT JOIN users u ON p.user_id = u.id 
                WHERE p.status = 'active' 
                AND (
                    MATCH(p.title, p.description, p.address) AGAINST(:query)
                    OR p.title LIKE :like_query
                    OR p.description LIKE :like_query
                    OR p.address LIKE :like_query
                    OR p.city LIKE :like_query
                )
                ORDER BY relevance DESC, p.created_at DESC 
                LIMIT :limit";
        
        $likeQuery = '%' . $query . '%';
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':query', $query);
        $stmt->bindParam(':like_query', $likeQuery);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Добавить просмотр объекта
     */
    public function addView(int $propertyId, ?int $userId = null, string $ipAddress = null): bool
    {
        $sql = "INSERT INTO property_views (property_id, user_id, ip_address, viewed_at) 
                VALUES (:property_id, :user_id, :ip_address, NOW())";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':property_id' => $propertyId,
            ':user_id' => $userId,
            ':ip_address' => $ipAddress ?? $_SERVER['REMOTE_ADDR']
        ]);
    }

    /**
     * Получить статистику по объекту
     */
    public function getStats(int $propertyId): array
    {
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM property_views WHERE property_id = :property_id) as views,
                    (SELECT COUNT(*) FROM user_favorites WHERE property_id = :property_id) as favorites
                ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':property_id', $propertyId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['views' => 0, 'favorites' => 0];
    }

    /**
     * Генерировать slug из заголовка
     */
    private function generateSlug(string $title): string
    {
        // Транслитерация
        $slug = $this->transliterate($title);
        
        // Приведение к lowercase и замена пробелов на дефисы
        $slug = strtolower(trim($slug));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Проверка уникальности
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    /**
     * Проверить существование slug
     */
    private function slugExists(string $slug): bool
    {
        $sql = "SELECT COUNT(*) FROM properties WHERE slug = :slug";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Транслитерация русского текста
     */
    private function transliterate(string $text): string
    {
        $translitMap = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
            'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'ts', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'sch', 'ы' => 'y', 'э' => 'e', 'ю' => 'yu',
            'я' => 'ya', 'ъ' => '', 'ь' => ''
        ];
        
        $text = mb_strtolower($text, 'UTF-8');
        
        return strtr($text, $translitMap);
    }

    /**
     * Валидация данных объекта
     */
    public function validate(array $data): array
    {
        $errors = [];
        
        // Обязательные поля
        if (empty($data['title'])) {
            $errors['title'][] = 'Название объекта обязательно';
        }
        
        if (empty($data['type'])) {
            $errors['type'][] = 'Тип объекта обязателен';
        } elseif (!in_array($data['type'], [
            self::TYPE_APARTMENT, self::TYPE_HOUSE, self::TYPE_ROOM,
            self::TYPE_COMMERCIAL, self::TYPE_OFFICE, self::TYPE_GARAGE
        ])) {
            $errors['type'][] = 'Недопустимый тип объекта';
        }
        
        if (empty($data['price'])) {
            $errors['price'][] = 'Цена обязательна';
        } elseif (!is_numeric($data['price']) || $data['price'] <= 0) {
            $errors['price'][] = 'Цена должна быть положительным числом';
        }
        
        if (empty($data['user_id'])) {
            $errors['user_id'][] = 'Владелец объекта обязателен';
        }
        
        // Проверка статуса
        if (!empty($data['status']) && !in_array($data['status'], [
            self::STATUS_DRAFT, self::STATUS_ACTIVE, self::STATUS_RENTED, self::STATUS_INACTIVE
        ])) {
            $errors['status'][] = 'Недопустимый статус объекта';
        }
        
        // Проверка числовых полей
        if (!empty($data['area']) && (!is_numeric($data['area']) || $data['area'] <= 0)) {
            $errors['area'][] = 'Площадь должна быть положительным числом';
        }
        
        if (!empty($data['rooms']) && (!is_numeric($data['rooms']) || $data['rooms'] < 0)) {
            $errors['rooms'][] = 'Количество комнат должно быть неотрицательным числом';
        }
        
        if (!empty($data['floor']) && !is_numeric($data['floor'])) {
            $errors['floor'][] = 'Этаж должен быть числом';
        }
        
        if (!empty($data['total_floors']) && (!is_numeric($data['total_floors']) || $data['total_floors'] < 1)) {
            $errors['total_floors'][] = 'Количество этажей должно быть положительным числом';
        }
        
        return $errors;
    }

    /**
     * Получить доступные статусы
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Черновик',
            self::STATUS_ACTIVE => 'Активно',
            self::STATUS_RENTED => 'Сдано в аренду',
            self::STATUS_INACTIVE => 'Неактивно'
        ];
    }

    /**
     * Получить доступные типы
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_APARTMENT => 'Квартира',
            self::TYPE_HOUSE => 'Дом',
            self::TYPE_ROOM => 'Комната',
            self::TYPE_COMMERCIAL => 'Коммерческая недвижимость',
            self::TYPE_OFFICE => 'Офис',
            self::TYPE_GARAGE => 'Гараж'
        ];
    }
}