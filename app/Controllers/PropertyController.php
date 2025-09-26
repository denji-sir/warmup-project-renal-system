<?php

namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\CSRF;
use Core\Validator;
use Core\Logger;
use App\Models\Property;
use App\Models\Category;

/**
 * Property Controller - Управление объектами недвижимости
 */
class PropertyController extends Controller
{
    private Property $propertyModel;
    private Category $categoryModel;
    private Auth $auth;
    private Session $session;

    public function __construct()
    {
        $this->propertyModel = new Property();
        $this->categoryModel = new Category();
        $this->auth = new Auth();
        $this->session = new Session();
    }

    /**
     * Список всех объектов недвижимости
     */
    public function index()
    {
        // Получение параметров запроса
        $page = (int)($_GET['page'] ?? 1);
        $limit = 12;
        $search = $_GET['search'] ?? '';
        
        // Фильтры
        $filters = [
            'status' => $_GET['status'] ?? 'active',
            'type' => $_GET['type'] ?? '',
            'city' => $_GET['city'] ?? '',
            'min_price' => $_GET['min_price'] ?? '',
            'max_price' => $_GET['max_price'] ?? '',
            'rooms' => $_GET['rooms'] ?? '',
            'category_id' => $_GET['category_id'] ?? '',
            'search' => $search,
            'order_by' => $_GET['order_by'] ?? 'created_at',
            'order_direction' => $_GET['order_direction'] ?? 'DESC'
        ];

        // Ограничение по роли пользователя
        if ($this->auth->check()) {
            $user = $this->auth->user();
            
            // Владельцы видят только свои объекты
            if ($user['role'] === 'owner' && !$this->auth->hasRole('admin')) {
                $filters['user_id'] = $user['id'];
            }
        } else {
            // Гости видят только активные объекты
            $filters['status'] = 'active';
        }

        // Получение данных
        $properties = $this->propertyModel->getAll($page, $limit, $filters);
        $totalCount = $this->propertyModel->getCount($filters);
        $totalPages = ceil($totalCount / $limit);
        
        // Получение категорий для фильтра
        $categories = $this->categoryModel->getActive();

        // Получение статистики по городам
        $cities = $this->getCitiesStats();
        
        return $this->view('properties/index', [
            'properties' => $properties,
            'categories' => $categories,
            'cities' => $cities,
            'filters' => $filters,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_count' => $totalCount,
                'per_page' => $limit
            ],
            'types' => Property::getTypes(),
            'statuses' => Property::getStatuses(),
            'title' => 'Объекты недвижимости'
        ]);
    }

    /**
     * Просмотр конкретного объекта
     */
    public function show($id)
    {
        $property = $this->propertyModel->getById($id);
        
        if (!$property) {
            $this->session->setFlash('error', 'Объект не найден');
            return $this->redirect('/properties');
        }

        // Проверка доступа к объекту
        if (!$this->canViewProperty($property)) {
            $this->session->setFlash('error', 'У вас нет доступа к этому объекту');
            return $this->redirect('/properties');
        }

        // Добавление просмотра
        $userId = $this->auth->check() ? $this->auth->user()['id'] : null;
        $this->propertyModel->addView($id, $userId);

        // Получение статистики
        $stats = $this->propertyModel->getStats($id);
        
        // Получение похожих объектов
        $similarProperties = $this->getSimilarProperties($property, 4);

        return $this->view('properties/show', [
            'property' => $property,
            'stats' => $stats,
            'similarProperties' => $similarProperties,
            'title' => $property['title']
        ]);
    }

    /**
     * Форма создания объекта
     */
    public function create()
    {
        // Проверка авторизации
        if (!$this->auth->check()) {
            return $this->redirect('/login');
        }

        // Проверка прав
        if (!$this->canCreateProperty()) {
            $this->session->setFlash('error', 'У вас нет прав для создания объектов');
            return $this->redirect('/properties');
        }

        $categories = $this->categoryModel->getActive();
        
        return $this->view('properties/create', [
            'categories' => $categories,
            'types' => Property::getTypes(),
            'statuses' => Property::getStatuses(),
            'title' => 'Добавить объект'
        ]);
    }

    /**
     * Сохранение нового объекта
     */
    public function store()
    {
        // Проверка авторизации и прав
        if (!$this->auth->check() || !$this->canCreateProperty()) {
            return $this->redirect('/login');
        }

        // Проверка CSRF
        if (!CSRF::verify($_POST['_token'] ?? '')) {
            $this->session->setFlash('error', 'Недействительный токен безопасности');
            return $this->redirect('/properties/create');
        }

        // Валидация данных
        $data = $this->preparePropertyData($_POST);
        $data['user_id'] = $this->auth->user()['id'];
        
        $errors = $this->propertyModel->validate($data);
        
        if (!empty($errors)) {
            $this->session->set('errors', $errors);
            $this->session->set('old_input', $_POST);
            return $this->redirect('/properties/create');
        }

        try {
            $propertyId = $this->propertyModel->create($data);
            
            // Обработка загруженных изображений
            $this->handleImageUploads($propertyId, $_FILES['images'] ?? []);
            
            // Логирование
            Logger::info("Property created", [
                'property_id' => $propertyId,
                'user_id' => $this->auth->user()['id'],
                'title' => $data['title']
            ]);
            
            $this->session->setFlash('success', 'Объект успешно создан');
            return $this->redirect("/properties/{$propertyId}");
            
        } catch (\Exception $e) {
            Logger::error("Property creation failed", [
                'error' => $e->getMessage(),
                'user_id' => $this->auth->user()['id']
            ]);
            
            $this->session->setFlash('error', 'Ошибка при создании объекта');
            $this->session->set('old_input', $_POST);
            return $this->redirect('/properties/create');
        }
    }

    /**
     * Форма редактирования объекта
     */
    public function edit($id)
    {
        $property = $this->propertyModel->getById($id);
        
        if (!$property) {
            $this->session->setFlash('error', 'Объект не найден');
            return $this->redirect('/properties');
        }

        // Проверка прав на редактирование
        if (!$this->canEditProperty($property)) {
            $this->session->setFlash('error', 'У вас нет прав для редактирования этого объекта');
            return $this->redirect('/properties');
        }

        $categories = $this->categoryModel->getActive();
        
        return $this->view('properties/edit', [
            'property' => $property,
            'categories' => $categories,
            'types' => Property::getTypes(),
            'statuses' => Property::getStatuses(),
            'title' => 'Редактировать объект: ' . $property['title']
        ]);
    }

    /**
     * Обновление объекта
     */
    public function update($id)
    {
        $property = $this->propertyModel->getById($id);
        
        if (!$property || !$this->canEditProperty($property)) {
            $this->session->setFlash('error', 'Объект не найден или у вас нет прав для его редактирования');
            return $this->redirect('/properties');
        }

        // Проверка CSRF
        if (!CSRF::verify($_POST['_token'] ?? '')) {
            $this->session->setFlash('error', 'Недействительный токен безопасности');
            return $this->redirect("/properties/{$id}/edit");
        }

        // Валидация данных
        $data = $this->preparePropertyData($_POST);
        $errors = $this->propertyModel->validate($data);
        
        if (!empty($errors)) {
            $this->session->set('errors', $errors);
            $this->session->set('old_input', $_POST);
            return $this->redirect("/properties/{$id}/edit");
        }

        try {
            $this->propertyModel->update($id, $data);
            
            // Обработка новых изображений
            if (!empty($_FILES['images'])) {
                $this->handleImageUploads($id, $_FILES['images']);
            }
            
            Logger::info("Property updated", [
                'property_id' => $id,
                'user_id' => $this->auth->user()['id'],
                'title' => $data['title']
            ]);
            
            $this->session->setFlash('success', 'Объект успешно обновлен');
            return $this->redirect("/properties/{$id}");
            
        } catch (\Exception $e) {
            Logger::error("Property update failed", [
                'property_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => $this->auth->user()['id']
            ]);
            
            $this->session->setFlash('error', 'Ошибка при обновлении объекта');
            $this->session->set('old_input', $_POST);
            return $this->redirect("/properties/{$id}/edit");
        }
    }

    /**
     * Удаление объекта
     */
    public function delete($id)
    {
        $property = $this->propertyModel->getById($id);
        
        if (!$property || !$this->canDeleteProperty($property)) {
            $this->session->setFlash('error', 'Объект не найден или у вас нет прав для его удаления');
            return $this->redirect('/properties');
        }

        // Проверка CSRF
        if (!CSRF::verify($_POST['_token'] ?? '')) {
            $this->session->setFlash('error', 'Недействительный токен безопасности');
            return $this->redirect("/properties/{$id}");
        }

        try {
            // Удаление файлов изображений
            $this->deletePropertyImages($id);
            
            // Удаление объекта из БД
            $this->propertyModel->delete($id);
            
            Logger::info("Property deleted", [
                'property_id' => $id,
                'user_id' => $this->auth->user()['id'],
                'title' => $property['title']
            ]);
            
            $this->session->setFlash('success', 'Объект успешно удален');
            return $this->redirect('/properties');
            
        } catch (\Exception $e) {
            Logger::error("Property deletion failed", [
                'property_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => $this->auth->user()['id']
            ]);
            
            $this->session->setFlash('error', 'Ошибка при удалении объекта');
            return $this->redirect("/properties/{$id}");
        }
    }

    /**
     * Удаление изображения
     */
    public function deleteImage($propertyId, $imageId)
    {
        $property = $this->propertyModel->getById($propertyId);
        
        if (!$property || !$this->canEditProperty($property)) {
            http_response_code(403);
            echo json_encode(['error' => 'Доступ запрещен']);
            return;
        }

        // Проверка CSRF
        if (!CSRF::verify($_POST['_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['error' => 'Недействительный токен безопасности']);
            return;
        }

        try {
            // Получение информации об изображении
            $images = $this->propertyModel->getPropertyImages($propertyId);
            $imageToDelete = null;
            
            foreach ($images as $image) {
                if ($image['id'] == $imageId) {
                    $imageToDelete = $image;
                    break;
                }
            }
            
            if (!$imageToDelete) {
                http_response_code(404);
                echo json_encode(['error' => 'Изображение не найдено']);
                return;
            }
            
            // Удаление файла
            $imagePath = __DIR__ . '/../../public/uploads/properties/' . $imageToDelete['filename'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            
            // Удаление из БД
            $this->propertyModel->deleteImage($imageId);
            
            echo json_encode(['success' => true, 'message' => 'Изображение удалено']);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Ошибка при удалении изображения']);
        }
    }

    /**
     * Поиск объектов (AJAX)
     */
    public function search()
    {
        $query = $_GET['q'] ?? '';
        $limit = (int)($_GET['limit'] ?? 10);
        
        if (strlen($query) < 3) {
            echo json_encode([]);
            return;
        }
        
        $results = $this->propertyModel->search($query, $limit);
        
        // Форматирование результатов для автодополнения
        $formatted = array_map(function($property) {
            return [
                'id' => $property['id'],
                'title' => $property['title'],
                'address' => $property['address'],
                'price' => number_format($property['price'], 0, '', ' ') . ' ₽',
                'type' => Property::getTypes()[$property['type']] ?? $property['type'],
                'url' => "/properties/{$property['id']}"
            ];
        }, $results);
        
        echo json_encode($formatted);
    }

    /**
     * Подготовка данных объекта из POST запроса
     */
    private function preparePropertyData(array $input): array
    {
        return [
            'title' => trim($input['title'] ?? ''),
            'slug' => trim($input['slug'] ?? ''),
            'description' => trim($input['description'] ?? ''),
            'type' => $input['type'] ?? '',
            'price' => (float)($input['price'] ?? 0),
            'currency' => $input['currency'] ?? 'RUB',
            'area' => !empty($input['area']) ? (float)$input['area'] : null,
            'rooms' => !empty($input['rooms']) ? (int)$input['rooms'] : null,
            'bathrooms' => !empty($input['bathrooms']) ? (int)$input['bathrooms'] : null,
            'floor' => !empty($input['floor']) ? (int)$input['floor'] : null,
            'total_floors' => !empty($input['total_floors']) ? (int)$input['total_floors'] : null,
            'address' => trim($input['address'] ?? ''),
            'city' => trim($input['city'] ?? ''),
            'district' => trim($input['district'] ?? ''),
            'latitude' => !empty($input['latitude']) ? (float)$input['latitude'] : null,
            'longitude' => !empty($input['longitude']) ? (float)$input['longitude'] : null,
            'category_id' => !empty($input['category_id']) ? (int)$input['category_id'] : null,
            'status' => $input['status'] ?? Property::STATUS_DRAFT
        ];
    }

    /**
     * Обработка загрузки изображений
     */
    private function handleImageUploads(int $propertyId, array $files): void
    {
        if (empty($files['name'][0])) {
            return;
        }
        
        $uploadDir = __DIR__ . '/../../public/uploads/properties/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }
            
            // Проверка типа файла
            if (!in_array($files['type'][$i], $allowedTypes)) {
                continue;
            }
            
            // Проверка размера файла
            if ($files['size'][$i] > $maxSize) {
                continue;
            }
            
            // Генерация имени файла
            $extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $filename = $propertyId . '_' . uniqid() . '.' . $extension;
            $filepath = $uploadDir . $filename;
            
            // Перемещение файла
            if (move_uploaded_file($files['tmp_name'][$i], $filepath)) {
                // Добавление в БД
                $isMain = ($i === 0); // Первое изображение - главное
                $this->propertyModel->addImage($propertyId, $filename, $isMain);
            }
        }
    }

    /**
     * Удаление изображений объекта
     */
    private function deletePropertyImages(int $propertyId): void
    {
        $images = $this->propertyModel->getPropertyImages($propertyId);
        $uploadDir = __DIR__ . '/../../public/uploads/properties/';
        
        foreach ($images as $image) {
            $filepath = $uploadDir . $image['filename'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }
    }

    /**
     * Получение похожих объектов
     */
    private function getSimilarProperties(array $property, int $limit = 4): array
    {
        $filters = [
            'type' => $property['type'],
            'status' => 'active',
            'city' => $property['city']
        ];
        
        $similar = $this->propertyModel->getAll(1, $limit + 1, $filters);
        
        // Убираем текущий объект из списка
        return array_filter($similar, function($p) use ($property) {
            return $p['id'] != $property['id'];
        });
    }

    /**
     * Получение статистики по городам
     */
    private function getCitiesStats(): array
    {
        // Это упрощенная версия, в реальном проекте можно сделать отдельный метод в модели
        return [
            'Москва' => 150,
            'Санкт-Петербург' => 89,
            'Новосибирск' => 45,
            'Екатеринбург' => 38,
            'Казань' => 29
        ];
    }

    /**
     * Проверка прав на просмотр объекта
     */
    private function canViewProperty(array $property): bool
    {
        // Активные объекты видят все
        if ($property['status'] === 'active') {
            return true;
        }
        
        // Остальные статусы видят только владелец и админы
        if (!$this->auth->check()) {
            return false;
        }
        
        $user = $this->auth->user();
        return $user['id'] === $property['user_id'] || $this->auth->hasRole('admin');
    }

    /**
     * Проверка прав на создание объекта
     */
    private function canCreateProperty(): bool
    {
        if (!$this->auth->check()) {
            return false;
        }
        
        return $this->auth->hasRole(['admin', 'manager', 'owner']);
    }

    /**
     * Проверка прав на редактирование объекта
     */
    private function canEditProperty(array $property): bool
    {
        if (!$this->auth->check()) {
            return false;
        }
        
        $user = $this->auth->user();
        return $user['id'] === $property['user_id'] || $this->auth->hasRole(['admin', 'manager']);
    }

    /**
     * Проверка прав на удаление объекта
     */
    private function canDeleteProperty(array $property): bool
    {
        if (!$this->auth->check()) {
            return false;
        }
        
        $user = $this->auth->user();
        return $user['id'] === $property['user_id'] || $this->auth->hasRole('admin');
    }
}