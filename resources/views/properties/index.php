<?php $title = 'Объекты недвижимости'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Фильтры -->
        <div class="col-lg-3">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-filter"></i> Фильтры</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="/properties">
                        <!-- Поиск -->
                        <div class="mb-3">
                            <label class="form-label">Поиск</label>
                            <input type="text" name="search" class="form-control" 
                                   value="<?= htmlspecialchars($filters['search']) ?>" 
                                   placeholder="Название, адрес...">
                        </div>

                        <!-- Тип недвижимости -->
                        <div class="mb-3">
                            <label class="form-label">Тип недвижимости</label>
                            <select name="type" class="form-select">
                                <option value="">Все типы</option>
                                <?php foreach ($types as $key => $label): ?>
                                    <option value="<?= $key ?>" <?= $filters['type'] === $key ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Категория -->
                        <div class="mb-3">
                            <label class="form-label">Категория</label>
                            <select name="category_id" class="form-select">
                                <option value="">Все категории</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" 
                                            <?= (string)$filters['category_id'] === (string)$category['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                        (<?= $category['properties_count'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Город -->
                        <div class="mb-3">
                            <label class="form-label">Город</label>
                            <select name="city" class="form-select">
                                <option value="">Все города</option>
                                <?php foreach ($cities as $cityName => $count): ?>
                                    <option value="<?= htmlspecialchars($cityName) ?>" 
                                            <?= $filters['city'] === $cityName ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cityName) ?> (<?= $count ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Цена -->
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Цена от</label>
                                <input type="number" name="min_price" class="form-control" 
                                       value="<?= htmlspecialchars($filters['min_price']) ?>" 
                                       placeholder="0">
                            </div>
                            <div class="col-6">
                                <label class="form-label">до</label>
                                <input type="number" name="max_price" class="form-control" 
                                       value="<?= htmlspecialchars($filters['max_price']) ?>" 
                                       placeholder="∞">
                            </div>
                        </div>

                        <!-- Количество комнат -->
                        <div class="mb-3">
                            <label class="form-label">Комнат</label>
                            <select name="rooms" class="form-select">
                                <option value="">Любое</option>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <option value="<?= $i ?>" <?= (string)$filters['rooms'] === (string)$i ? 'selected' : '' ?>>
                                        <?= $i ?><?= $i >= 5 ? '+' : '' ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Статус (только для авторизованных) -->
                        <?php if (isLoggedIn()): ?>
                        <div class="mb-3">
                            <label class="form-label">Статус</label>
                            <select name="status" class="form-select">
                                <?php foreach ($statuses as $key => $label): ?>
                                    <option value="<?= $key ?>" <?= $filters['status'] === $key ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Применить фильтры
                        </button>
                        
                        <a href="/properties" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-undo"></i> Сбросить
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Основной контент -->
        <div class="col-lg-9">
            <!-- Шапка с кнопкой добавления -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1>Объекты недвижимости</h1>
                    <p class="text-muted mb-0">
                        Найдено: <?= $pagination['total_count'] ?> объектов
                    </p>
                </div>
                
                <?php if (isLoggedIn() && hasRole(['admin', 'manager', 'owner'])): ?>
                <a href="/properties/create" class="btn btn-success">
                    <i class="fas fa-plus"></i> Добавить объект
                </a>
                <?php endif; ?>
            </div>

            <!-- Сортировка -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="btn-group" role="group">
                        <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'created_at', 'order_direction' => 'DESC'])) ?>" 
                           class="btn btn-outline-secondary <?= $filters['order_by'] === 'created_at' ? 'active' : '' ?>">
                            Новые
                        </a>
                        <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'price', 'order_direction' => 'ASC'])) ?>" 
                           class="btn btn-outline-secondary <?= $filters['order_by'] === 'price' && $filters['order_direction'] === 'ASC' ? 'active' : '' ?>">
                            Цена ↑
                        </a>
                        <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'price', 'order_direction' => 'DESC'])) ?>" 
                           class="btn btn-outline-secondary <?= $filters['order_by'] === 'price' && $filters['order_direction'] === 'DESC' ? 'active' : '' ?>">
                            Цена ↓
                        </a>
                    </div>
                </div>
            </div>

            <!-- Список объектов -->
            <?php if (empty($properties)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-home fa-3x text-muted mb-3"></i>
                    <h4>Объекты не найдены</h4>
                    <p class="text-muted">Попробуйте изменить параметры поиска</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($properties as $property): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm property-card">
                            <!-- Изображение -->
                            <div class="position-relative">
                                <?php 
                                $mainImage = '/images/no-image.jpg'; // Placeholder
                                if ($property['image_count'] > 0) {
                                    // Здесь можно получить главное изображение
                                    $mainImage = '/uploads/properties/placeholder.jpg';
                                }
                                ?>
                                <img src="<?= $mainImage ?>" class="card-img-top property-image" 
                                     alt="<?= htmlspecialchars($property['title']) ?>" 
                                     style="height: 200px; object-fit: cover;">
                                
                                <!-- Статус бейдж -->
                                <?php 
                                $statusClass = [
                                    'active' => 'success',
                                    'rented' => 'warning', 
                                    'inactive' => 'secondary',
                                    'draft' => 'info'
                                ][$property['status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $statusClass ?> position-absolute top-0 start-0 m-2">
                                    <?= $statuses[$property['status']] ?? $property['status'] ?>
                                </span>

                                <!-- Количество изображений -->
                                <?php if ($property['image_count'] > 1): ?>
                                <span class="badge bg-dark position-absolute bottom-0 end-0 m-2">
                                    <i class="fas fa-camera"></i> <?= $property['image_count'] ?>
                                </span>
                                <?php endif; ?>
                            </div>

                            <div class="card-body d-flex flex-column">
                                <!-- Заголовок -->
                                <h5 class="card-title">
                                    <a href="/properties/<?= $property['id'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($property['title']) ?>
                                    </a>
                                </h5>

                                <!-- Тип и категория -->
                                <div class="mb-2">
                                    <span class="badge bg-light text-dark">
                                        <?= $types[$property['type']] ?? $property['type'] ?>
                                    </span>
                                    <?php if ($property['category_name']): ?>
                                    <span class="badge bg-light text-dark">
                                        <?= htmlspecialchars($property['category_name']) ?>
                                    </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Адрес -->
                                <?php if ($property['address']): ?>
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    <?= htmlspecialchars($property['address']) ?>
                                </p>
                                <?php endif; ?>

                                <!-- Характеристики -->
                                <div class="mb-2">
                                    <?php if ($property['rooms']): ?>
                                        <span class="text-muted small">
                                            <i class="fas fa-bed"></i> <?= $property['rooms'] ?> комн.
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if ($property['area']): ?>
                                        <span class="text-muted small ms-2">
                                            <i class="fas fa-ruler-combined"></i> <?= $property['area'] ?> м²
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if ($property['floor']): ?>
                                        <span class="text-muted small ms-2">
                                            <i class="fas fa-building"></i> <?= $property['floor'] ?> эт.
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Цена -->
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4 class="text-primary mb-0">
                                            <?= number_format($property['price'], 0, '', ' ') ?> ₽
                                        </h4>
                                        
                                        <!-- Кнопки действий -->
                                        <div class="btn-group btn-group-sm">
                                            <a href="/properties/<?= $property['id'] ?>" 
                                               class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <?php if (isLoggedIn()): ?>
                                                <?php $currentUser = auth(); ?>
                                                <?php if ($currentUser['id'] === $property['user_id'] || hasRole(['admin', 'manager'])): ?>
                                                    <a href="/properties/<?= $property['id'] ?>/edit" 
                                                       class="btn btn-outline-secondary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Владелец -->
                                    <small class="text-muted">
                                        Владелец: <?= htmlspecialchars($property['owner_name']) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Пагинация -->
                <?php if ($pagination['total_pages'] > 1): ?>
                <nav aria-label="Пагинация объектов">
                    <ul class="pagination justify-content-center">
                        <!-- Предыдущая страница -->
                        <?php if ($pagination['current_page'] > 1): ?>
                        <li class="page-item">
                            <a class="page-link" 
                               href="?<?= http_build_query(array_merge($_GET, ['page' => $pagination['current_page'] - 1])) ?>">
                                <i class="fas fa-chevron-left"></i> Предыдущая
                            </a>
                        </li>
                        <?php endif; ?>

                        <!-- Номера страниц -->
                        <?php
                        $startPage = max(1, $pagination['current_page'] - 2);
                        $endPage = min($pagination['total_pages'], $pagination['current_page'] + 2);
                        ?>
                        
                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                            <a class="page-link" 
                               href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                                <?= $i ?>
                            </a>
                        </li>
                        <?php endfor; ?>

                        <!-- Следующая страница -->
                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                        <li class="page-item">
                            <a class="page-link" 
                               href="?<?= http_build_query(array_merge($_GET, ['page' => $pagination['current_page'] + 1])) ?>">
                                Следующая <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.property-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.property-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.property-image {
    transition: transform 0.3s;
}

.property-card:hover .property-image {
    transform: scale(1.05);
}

.card-img-top {
    overflow: hidden;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>