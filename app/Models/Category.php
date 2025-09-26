<?php

namespace App\Models;

use Core\Database;
use PDO;

/**
 * Category Model - Модель для управления категориями недвижимости
 */
class Category
{
    private PDO $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Получить все категории
     */
    public function getAll(): array
    {
        $sql = "SELECT c.*, 
                       (SELECT COUNT(*) FROM properties p WHERE p.category_id = c.id AND p.status = 'active') as properties_count
                FROM categories c 
                ORDER BY c.sort_order ASC, c.name ASC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Получить активные категории
     */
    public function getActive(): array
    {
        $sql = "SELECT c.*,
                       (SELECT COUNT(*) FROM properties p WHERE p.category_id = c.id AND p.status = 'active') as properties_count
                FROM categories c 
                WHERE c.is_active = 1
                ORDER BY c.sort_order ASC, c.name ASC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Получить категорию по ID
     */
    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM categories WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Получить категорию по slug
     */
    public function getBySlug(string $slug): ?array
    {
        $sql = "SELECT * FROM categories WHERE slug = :slug";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Создать новую категорию
     */
    public function create(array $data): int
    {
        $sql = "INSERT INTO categories (name, slug, description, is_active, sort_order, created_at, updated_at) 
                VALUES (:name, :slug, :description, :is_active, :sort_order, NOW(), NOW())";
        
        $stmt = $this->db->prepare($sql);
        
        // Генерация slug если не указан
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }
        
        // Получение следующего порядка сортировки
        if (empty($data['sort_order'])) {
            $data['sort_order'] = $this->getNextSortOrder();
        }
        
        $stmt->execute([
            ':name' => $data['name'],
            ':slug' => $data['slug'],
            ':description' => $data['description'] ?? null,
            ':is_active' => $data['is_active'] ?? 1,
            ':sort_order' => $data['sort_order']
        ]);
        
        return $this->db->lastInsertId();
    }

    /**
     * Обновить категорию
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE categories SET 
                    name = :name,
                    slug = :slug,
                    description = :description,
                    is_active = :is_active,
                    sort_order = :sort_order,
                    updated_at = NOW()
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        
        // Генерация slug если изменилось название
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }
        
        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':slug' => $data['slug'],
            ':description' => $data['description'] ?? null,
            ':is_active' => $data['is_active'] ?? 1,
            ':sort_order' => $data['sort_order']
        ]);
    }

    /**
     * Удалить категорию
     */
    public function delete(int $id): bool
    {
        // Проверяем, есть ли объекты в этой категории
        $sql = "SELECT COUNT(*) FROM properties WHERE category_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            return false; // Нельзя удалить категорию с объектами
        }
        
        $sql = "DELETE FROM categories WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Получить следующий порядок сортировки
     */
    private function getNextSortOrder(): int
    {
        $sql = "SELECT MAX(sort_order) FROM categories";
        $stmt = $this->db->query($sql);
        $maxOrder = $stmt->fetchColumn();
        
        return ($maxOrder ?? 0) + 10;
    }

    /**
     * Генерировать slug из названия
     */
    private function generateSlug(string $name): string
    {
        // Транслитерация
        $slug = $this->transliterate($name);
        
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
        $sql = "SELECT COUNT(*) FROM categories WHERE slug = :slug";
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
     * Валидация данных категории
     */
    public function validate(array $data): array
    {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors['name'][] = 'Название категории обязательно';
        }
        
        if (!empty($data['sort_order']) && !is_numeric($data['sort_order'])) {
            $errors['sort_order'][] = 'Порядок сортировки должен быть числом';
        }
        
        return $errors;
    }
}