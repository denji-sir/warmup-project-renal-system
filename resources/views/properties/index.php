<?php $title = 'Объекты недвижимости — ' . config('app.name'); ?>

<?php ob_start(); ?>

<?php $totalCount = $pagination['total_count'] ?? count($properties); ?>
<?php $currentUser = auth(); ?>

<section class="section section--muted">
    <div class="container">
        <div class="section__header">
            <span class="eyebrow">Каталог объектов</span>
            <h1 class="section__title">Объекты недвижимости</h1>
            <p class="section__subtitle section__subtitle--muted">Найдено: <?= number_format($totalCount, 0, ',', ' ') ?> объектов</p>
        </div>

        <div class="listing-layout">
            <aside class="filters-panel">
                <h3 class="filters-title">
                    <svg width="18" height="18" viewBox="0 0 16 16" aria-hidden="true"><path fill="currentColor" d="M1.5 2h13a.5.5 0 0 1 .4.8L10 9.333V13a1 1 0 0 1-.553.894l-2 1A1 1 0 0 1 6 14V9.333L1.1 2.8A.5.5 0 0 1 1.5 2Z"/></svg>
                    Фильтры
                </h3>
                <form method="GET" action="/properties" class="filters-form">
                    <div class="form-group">
                        <label for="filter-search" class="form-label">Поиск</label>
                        <input id="filter-search" type="text" name="search" class="form-input" placeholder="Название, адрес"
                               value="<?= e($filters['search']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="filter-type" class="form-label">Тип сделки</label>
                        <select id="filter-type" name="type" class="form-select">
                            <option value="">Все типы</option>
                            <?php foreach ($types as $key => $label): ?>
                                <option value="<?= $key ?>" <?= $filters['type'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="filter-category" class="form-label">Категория</label>
                        <select id="filter-category" name="category_id" class="form-select">
                            <option value="">Все категории</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= (string)$filters['category_id'] === (string)$category['id'] ? 'selected' : '' ?>>
                                    <?= e($category['name']) ?> (<?= $category['properties_count'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="filter-city" class="form-label">Город</label>
                        <select id="filter-city" name="city" class="form-select">
                            <option value="">Все города</option>
                            <?php foreach ($cities as $cityName => $count): ?>
                                <option value="<?= e($cityName) ?>" <?= $filters['city'] === $cityName ? 'selected' : '' ?>>
                                    <?= e($cityName) ?> (<?= $count ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Бюджет, ₽</label>
                        <div class="split-grid">
                            <input type="number" name="min_price" class="form-input" placeholder="От" value="<?= e($filters['min_price']) ?>">
                            <input type="number" name="max_price" class="form-input" placeholder="До" value="<?= e($filters['max_price']) ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="filter-rooms" class="form-label">Количество комнат</label>
                        <select id="filter-rooms" name="rooms" class="form-select">
                            <option value="">Любое</option>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?= $i ?>" <?= (string)$filters['rooms'] === (string)$i ? 'selected' : '' ?>><?= $i ?><?= $i >= 5 ? '+' : '' ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <?php if (isLoggedIn()): ?>
                        <div class="form-group">
                            <label for="filter-status" class="form-label">Статус</label>
                            <select id="filter-status" name="status" class="form-select">
                                <?php foreach ($statuses as $key => $label): ?>
                                    <option value="<?= $key ?>" <?= $filters['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary btn-lg w-100">Применить фильтры</button>
                    <a href="/properties" class="btn btn-outline btn-sm w-100 mt-sm">Сбросить</a>
                </form>
            </aside>

            <div class="listing-results">
                <div class="listing-toolbar">
                    <div>
                        <strong><?= number_format($totalCount, 0, ',', ' ') ?></strong>
                        <span class="text-secondary">актуальных предложений</span>
                    </div>
                    <div class="sort-buttons">
                        <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'created_at', 'order_direction' => 'DESC'])) ?>"
                           class="btn btn-outline btn-sm <?= $filters['order_by'] === 'created_at' ? 'active' : '' ?>">Новые</a>
                        <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'price', 'order_direction' => 'ASC'])) ?>"
                           class="btn btn-outline btn-sm <?= $filters['order_by'] === 'price' && $filters['order_direction'] === 'ASC' ? 'active' : '' ?>">Цена ↑</a>
                        <a href="?<?= http_build_query(array_merge($_GET, ['order_by' => 'price', 'order_direction' => 'DESC'])) ?>"
                           class="btn btn-outline btn-sm <?= $filters['order_by'] === 'price' && $filters['order_direction'] === 'DESC' ? 'active' : '' ?>">Цена ↓</a>
                        <?php if (isLoggedIn() && hasRole(['admin', 'manager', 'owner'])): ?>
                            <a href="/properties/create" class="btn btn-primary btn-sm">Добавить объект</a>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (empty($properties)): ?>
                    <div class="card">
                        <div class="card-body text-center">
                            <h3>Мы не нашли объекты по выбранным фильтрам</h3>
                            <p class="text-secondary">Попробуйте изменить критерии поиска или обратитесь к нашему эксперту — мы подготовим индивидуальную подборку.</p>
                            <a href="/contact" class="btn btn-primary btn-sm">Связаться с консультантом</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="result-grid">
                        <?php foreach ($properties as $property): ?>
                            <?php
                                $mainImage = '/images/no-image.jpg';
                                if (!empty($property['image_count'])) {
                                    $mainImage = '/uploads/properties/placeholder.jpg';
                                }
                                $statusKey = $property['status'] ?? 'active';
                                $statusLabel = $statuses[$statusKey] ?? $statusKey;
                                $badgeClass = in_array($statusKey, ['rented', 'inactive'], true) ? 'rent' : 'sale';
                            ?>
                            <article class="property-card">
                                <div class="property-image">
                                    <img src="<?= $mainImage ?>" alt="<?= e($property['title']) ?>">
                                    <span class="property-badge <?= $badgeClass ?>"><?= e($statusLabel) ?></span>
                                    <div class="property-price">
                                        <?= number_format($property['price'], 0, ',', ' ') ?> ₽
                                        <span>стоимость</span>
                                    </div>
                                </div>
                                <div class="property-details">
                                    <h3 class="property-title">
                                        <a href="/properties/<?= $property['id'] ?>"><?= e($property['title']) ?></a>
                                    </h3>
                                    <?php if (!empty($property['address'])): ?>
                                        <p class="property-address">
                                            <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path fill="currentColor" d="M8 15s6-5.686 6-10A6 6 0 1 0 2 5c0 4.314 6 10 6 10Zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/></svg>
                                            <?= e($property['address']) ?>
                                        </p>
                                    <?php endif; ?>
                                    <div class="property-features">
                                        <?php if (!empty($property['rooms'])): ?>
                                            <span class="property-feature"><?= (int)$property['rooms'] ?> комн.</span>
                                        <?php endif; ?>
                                        <?php if (!empty($property['area'])): ?>
                                            <span class="property-feature"><?= (int)$property['area'] ?> м²</span>
                                        <?php endif; ?>
                                        <?php if (!empty($property['floor'])): ?>
                                            <span class="property-feature"><?= (int)$property['floor'] ?> этаж</span>
                                        <?php endif; ?>
                                        <?php if (!empty($property['city'])): ?>
                                            <span class="property-feature"><?= e($property['city']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="property-actions">
                                        <a href="/properties/<?= $property['id'] ?>" class="btn btn-primary btn-sm">Подробнее</a>
                                        <div class="property-buttons d-flex gap-sm">
                                            <button type="button" class="btn btn-outline btn-sm" data-favorite-toggle="<?= $property['id'] ?>" aria-label="Добавить в избранное">♡</button>
                                            <?php
                                                $ownerId = $property['user_id'] ?? null;
                                                $canEdit = $currentUser && (
                                                    $currentUser->id === $ownerId || hasRole(['admin', 'manager'])
                                                );
                                            ?>
                                            <?php if ($canEdit): ?>
                                                <a href="/properties/<?= $property['id'] ?>/edit" class="btn btn-outline btn-sm" aria-label="Редактировать">✎</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <small class="text-secondary">Владелец: <?= e($property['owner_name'] ?? 'Не указан') ?></small>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($pagination['total_pages'] > 1): ?>
                        <nav class="pagination" aria-label="Пагинация объектов">
                            <?php if ($pagination['current_page'] > 1): ?>
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $pagination['current_page'] - 1])) ?>">‹</a>
                            <?php endif; ?>

                            <?php
                            $startPage = max(1, $pagination['current_page'] - 2);
                            $endPage = min($pagination['total_pages'], $pagination['current_page'] + 2);
                            for ($i = $startPage; $i <= $endPage; $i++):
                                $isActive = $i === $pagination['current_page'];
                            ?>
                                <a class="page-link <?= $isActive ? 'active' : '' ?>" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $pagination['current_page'] + 1])) ?>">›</a>
                            <?php endif; ?>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php $content = ob_get_clean(); ?>

<?php require __DIR__ . '/../layouts/main.php'; ?>
