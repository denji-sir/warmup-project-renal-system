<?php $title = htmlspecialchars($property['title']); ?>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Основной контент -->
        <div class="col-lg-8">
            <!-- Навигация -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item"><a href="/properties">Недвижимость</a></li>
                    <?php if ($property['category_name']): ?>
                        <li class="breadcrumb-item">
                            <a href="/properties?category_id=<?= $property['category_id'] ?>">
                                <?= htmlspecialchars($property['category_name']) ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?= mb_strimwidth(htmlspecialchars($property['title']), 0, 50, "...") ?>
                    </li>
                </ol>
            </nav>

            <!-- Заголовок и действия -->
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h1 class="mb-2"><?= htmlspecialchars($property['title']) ?></h1>
                    
                    <!-- Статус и тип -->
                    <div class="mb-3">
                        <?php 
                        $statusClass = [
                            'active' => 'success',
                            'rented' => 'warning', 
                            'inactive' => 'secondary',
                            'draft' => 'info'
                        ][$property['status']] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?= $statusClass ?> me-2">
                            <?= \App\Models\Property::getStatuses()[$property['status']] ?? $property['status'] ?>
                        </span>
                        
                        <span class="badge bg-light text-dark me-2">
                            <?= \App\Models\Property::getTypes()[$property['type']] ?? $property['type'] ?>
                        </span>

                        <?php if ($property['category_name']): ?>
                            <span class="badge bg-light text-dark">
                                <?= htmlspecialchars($property['category_name']) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Адрес -->
                    <?php if ($property['address']): ?>
                        <p class="text-muted mb-3">
                            <i class="fas fa-map-marker-alt text-primary"></i>
                            <?= htmlspecialchars($property['address']) ?>
                            <?php if ($property['city']): ?>
                                , <?= htmlspecialchars($property['city']) ?>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Кнопки управления -->
                <?php if (isLoggedIn()): ?>
                    <?php $currentUser = auth(); ?>
                    <?php if ($currentUser['id'] === $property['user_id'] || hasRole('admin')): ?>
                        <div class="btn-group">
                            <a href="/properties/<?= $property['id'] ?>/edit" class="btn btn-outline-primary">
                                <i class="fas fa-edit"></i> Редактировать
                            </a>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash"></i> Удалить
                            </button>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Галерея изображений -->
            <?php if (!empty($property['images'])): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-0">
                        <div id="propertyCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <?php foreach ($property['images'] as $index => $image): ?>
                                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                        <img src="/uploads/properties/<?= htmlspecialchars($image['filename']) ?>" 
                                             class="d-block w-100" 
                                             style="height: 400px; object-fit: cover;"
                                             alt="<?= htmlspecialchars($property['title']) ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php if (count($property['images']) > 1): ?>
                                <button class="carousel-control-prev" type="button" data-bs-target="#propertyCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#propertyCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                                
                                <div class="carousel-indicators">
                                    <?php foreach ($property['images'] as $index => $image): ?>
                                        <button type="button" 
                                                data-bs-target="#propertyCarousel" 
                                                data-bs-slide-to="<?= $index ?>"
                                                <?= $index === 0 ? 'class="active"' : '' ?>></button>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Миниатюры -->
                        <?php if (count($property['images']) > 1): ?>
                            <div class="p-3">
                                <div class="row g-2">
                                    <?php foreach ($property['images'] as $index => $image): ?>
                                        <div class="col-2">
                                            <img src="/uploads/properties/<?= htmlspecialchars($image['filename']) ?>" 
                                                 class="img-fluid rounded thumbnail-image cursor-pointer" 
                                                 style="aspect-ratio: 1; object-fit: cover;"
                                                 data-bs-target="#propertyCarousel" 
                                                 data-bs-slide-to="<?= $index ?>"
                                                 alt="Миниатюра <?= $index + 1 ?>">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Заглушка без изображений -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-image fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Изображения не загружены</h5>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Описание -->
            <?php if ($property['description']): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-align-left text-primary"></i> Описание</h5>
                    </div>
                    <div class="card-body">
                        <div class="description-text">
                            <?= nl2br(htmlspecialchars($property['description'])) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Карта (если есть координаты) -->
            <?php if ($property['latitude'] && $property['longitude']): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-map text-primary"></i> Расположение на карте</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="map" style="height: 300px; background: #f8f9fa;">
                            <!-- Здесь будет интегрирована карта (Яндекс.Карты или другой сервис) -->
                            <div class="d-flex align-items-center justify-content-center h-100">
                                <div class="text-center">
                                    <i class="fas fa-map-marked-alt fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">Карта будет загружена</p>
                                    <small class="text-muted">
                                        Координаты: <?= $property['latitude'] ?>, <?= $property['longitude'] ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Похожие объекты -->
            <?php if (!empty($similarProperties)): ?>
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-home text-primary"></i> Похожие объекты</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($similarProperties as $similar): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 border-0 bg-light">
                                        <div class="row g-0">
                                            <div class="col-4">
                                                <?php 
                                                $similarImage = '/images/no-image.jpg';
                                                // Здесь можно получить главное изображение
                                                ?>
                                                <img src="<?= $similarImage ?>" 
                                                     class="img-fluid rounded-start" 
                                                     style="height: 100px; object-fit: cover;">
                                            </div>
                                            <div class="col-8">
                                                <div class="card-body p-2">
                                                    <h6 class="card-title mb-1">
                                                        <a href="/properties/<?= $similar['id'] ?>" class="text-decoration-none">
                                                            <?= mb_strimwidth(htmlspecialchars($similar['title']), 0, 30, "...") ?>
                                                        </a>
                                                    </h6>
                                                    <p class="card-text">
                                                        <strong class="text-primary">
                                                            <?= number_format($similar['price'], 0, '', ' ') ?> ₽
                                                        </strong>
                                                    </p>
                                                    <small class="text-muted">
                                                        <?= htmlspecialchars($similar['city'] ?? '') ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Боковая панель -->
        <div class="col-lg-4">
            <!-- Цена и основная информация -->
            <div class="card shadow-sm mb-4 sticky-top" style="top: 20px;">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h2 class="text-primary mb-0">
                            <?= number_format($property['price'], 0, '', ' ') ?> 
                            <small class="text-muted"><?= $property['currency'] ?></small>
                        </h2>
                        <small class="text-muted">за объект</small>
                    </div>

                    <!-- Характеристики -->
                    <div class="row text-center mb-4">
                        <?php if ($property['area']): ?>
                            <div class="col-4">
                                <div class="border-end">
                                    <i class="fas fa-ruler-combined text-primary d-block mb-1"></i>
                                    <strong><?= $property['area'] ?></strong>
                                    <small class="text-muted d-block">м²</small>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($property['rooms']): ?>
                            <div class="col-4">
                                <div class="<?= $property['floor'] ? 'border-end' : '' ?>">
                                    <i class="fas fa-bed text-primary d-block mb-1"></i>
                                    <strong><?= $property['rooms'] ?></strong>
                                    <small class="text-muted d-block">комн.</small>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($property['floor']): ?>
                            <div class="col-4">
                                <i class="fas fa-building text-primary d-block mb-1"></i>
                                <strong><?= $property['floor'] ?></strong>
                                <small class="text-muted d-block">этаж</small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Дополнительные характеристики -->
                    <div class="mb-4">
                        <?php if ($property['bathrooms']): ?>
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted">Санузлы:</span>
                                <strong><?= $property['bathrooms'] ?></strong>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($property['total_floors']): ?>
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted">Этажность:</span>
                                <strong><?= $property['total_floors'] ?> эт.</strong>
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">ID объекта:</span>
                            <strong>#<?= $property['id'] ?></strong>
                        </div>
                    </div>

                    <!-- Контактная информация -->
                    <div class="border-top pt-3">
                        <h6><i class="fas fa-user text-primary"></i> Владелец</h6>
                        <p class="mb-2">
                            <strong><?= htmlspecialchars($property['owner_name']) ?></strong>
                        </p>
                        
                        <?php if (isLoggedIn()): ?>
                            <div class="d-grid gap-2">
                                <a href="tel:+7" class="btn btn-success">
                                    <i class="fas fa-phone"></i> Позвонить
                                </a>
                                <a href="mailto:<?= htmlspecialchars($property['owner_email']) ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-envelope"></i> Написать
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <small>
                                    <i class="fas fa-info-circle"></i>
                                    <a href="/login" class="alert-link">Войдите</a> 
                                    для просмотра контактов
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Статистика -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-chart-line text-primary"></i> Статистика</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <i class="fas fa-eye text-primary d-block mb-1"></i>
                            <strong><?= $stats['views'] ?? 0 ?></strong>
                            <small class="text-muted d-block">просмотров</small>
                        </div>
                        <div class="col-6">
                            <i class="fas fa-heart text-danger d-block mb-1"></i>
                            <strong><?= $stats['favorites'] ?? 0 ?></strong>
                            <small class="text-muted d-block">в избранном</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <small class="text-muted">
                        <i class="fas fa-calendar-alt"></i>
                        Добавлено: <?= date('d.m.Y', strtotime($property['created_at'])) ?>
                    </small>
                    
                    <?php if ($property['updated_at'] !== $property['created_at']): ?>
                        <br>
                        <small class="text-muted">
                            <i class="fas fa-edit"></i>
                            Обновлено: <?= date('d.m.Y', strtotime($property['updated_at'])) ?>
                        </small>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Действия -->
            <?php if (isLoggedIn()): ?>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-danger" onclick="toggleFavorite(<?= $property['id'] ?>)">
                                <i class="fas fa-heart"></i> В избранное
                            </button>
                            <button class="btn btn-outline-info" onclick="shareProperty()">
                                <i class="fas fa-share-alt"></i> Поделиться
                            </button>
                            <button class="btn btn-outline-warning" onclick="reportProperty()">
                                <i class="fas fa-flag"></i> Пожаловаться
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Модальное окно удаления -->
<?php if (isLoggedIn()): ?>
    <?php $currentUser = auth(); ?>
    <?php if ($currentUser['id'] === $property['user_id'] || hasRole('admin')): ?>
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Удалить объект</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Вы действительно хотите удалить этот объект?</p>
                        <p><strong><?= htmlspecialchars($property['title']) ?></strong></p>
                        <p class="text-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            Это действие нельзя отменить!
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Отмена
                        </button>
                        <form method="POST" action="/properties/<?= $property['id'] ?>/delete" class="d-inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Удалить
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<script>
// Функция добавления в избранное
function toggleFavorite(propertyId) {
    // Здесь будет AJAX запрос для добавления/удаления из избранного
    console.log('Toggle favorite for property:', propertyId);
    
    // Пример:
    // fetch(`/api/favorites/toggle/${propertyId}`, { method: 'POST' })
    //     .then(response => response.json())
    //     .then(data => {
    //         // Обновить UI
    //     });
}

// Функция поделиться
function shareProperty() {
    if (navigator.share) {
        navigator.share({
            title: '<?= addslashes($property['title']) ?>',
            text: 'Смотрите этот объект недвижимости',
            url: window.location.href
        });
    } else {
        // Fallback для браузеров без поддержки Web Share API
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Ссылка скопирована в буфер обмена!');
        });
    }
}

// Функция жалобы
function reportProperty() {
    // Здесь можно открыть модальное окно или перенаправить на форму жалобы
    alert('Функция жалобы в разработке');
}

// Улучшения для галереи
document.addEventListener('DOMContentLoaded', function() {
    // Клик по миниатюре переключает слайд
    document.querySelectorAll('.thumbnail-image').forEach(function(thumb) {
        thumb.addEventListener('click', function() {
            const carousel = new bootstrap.Carousel(document.getElementById('propertyCarousel'));
            const slideIndex = parseInt(this.dataset.bsSlideTo);
            carousel.to(slideIndex);
        });
    });
    
    // Подсветка активной миниатюры
    const carousel = document.getElementById('propertyCarousel');
    if (carousel) {
        carousel.addEventListener('slide.bs.carousel', function(event) {
            // Убираем активный класс у всех миниатюр
            document.querySelectorAll('.thumbnail-image').forEach(function(thumb) {
                thumb.classList.remove('border', 'border-primary');
            });
            
            // Добавляем активный класс к текущей миниатюре
            const activeThumb = document.querySelector(`[data-bs-slide-to="${event.to}"]`);
            if (activeThumb) {
                activeThumb.classList.add('border', 'border-primary');
            }
        });
    }
});
</script>

<style>
.sticky-top {
    z-index: 1020;
}

.thumbnail-image {
    cursor: pointer;
    transition: all 0.3s ease;
}

.thumbnail-image:hover {
    transform: scale(1.05);
    opacity: 0.8;
}

.description-text {
    line-height: 1.6;
}

.carousel-control-prev,
.carousel-control-next {
    width: 5%;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    background-color: rgba(0, 0, 0, 0.5);
    border-radius: 50%;
    width: 40px;
    height: 40px;
}

.carousel-indicators {
    bottom: 10px;
}

.carousel-indicators button {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin: 0 3px;
}

.border-end {
    border-right: 1px solid #dee2e6 !important;
}

@media (max-width: 768px) {
    .sticky-top {
        position: static !important;
    }
    
    .border-end {
        border-right: none !important;
    }
}
</style>