<?php 
$title = isset($property) ? 'Редактировать объект: ' . htmlspecialchars($property['title']) : 'Добавить объект недвижимости';
$isEdit = isset($property);
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Заголовок -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1><?= $isEdit ? 'Редактировать объект' : 'Добавить объект недвижимости' ?></h1>
                    <p class="text-muted mb-0">
                        <?= $isEdit ? 'Обновите информацию об объекте' : 'Заполните все обязательные поля' ?>
                    </p>
                </div>
                <a href="/properties" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Назад к списку
                </a>
            </div>

            <!-- Сообщения об ошибках -->
            <?php if (hasFlash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= flash('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty(errors())): ?>
                <div class="alert alert-danger">
                    <h6><i class="fas fa-exclamation-triangle"></i> Исправьте следующие ошибки:</h6>
                    <ul class="mb-0">
                        <?php foreach (errors() as $field => $fieldErrors): ?>
                            <?php foreach ($fieldErrors as $error): ?>
                                <li><strong><?= ucfirst($field) ?>:</strong> <?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Форма -->
            <form method="POST" 
                  action="<?= $isEdit ? "/properties/{$property['id']}" : '/properties' ?>"
                  enctype="multipart/form-data" 
                  class="needs-validation" 
                  novalidate>
                
                <?= csrf_field() ?>

                <div class="row">
                    <!-- Основная информация -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Основная информация</h5>
                            </div>
                            <div class="card-body">
                                <!-- Название -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">
                                        Название объекта <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control <?= hasError('title') ? 'is-invalid' : '' ?>" 
                                           id="title" 
                                           name="title" 
                                           value="<?= old('title', $property['title'] ?? '') ?>"
                                           placeholder="Например: 2-комнатная квартира в центре"
                                           required>
                                    <?php if (hasError('title')): ?>
                                        <div class="invalid-feedback">
                                            <?= firstError('title') ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="form-text">
                                        Краткое и привлекательное название объекта
                                    </div>
                                </div>

                                <!-- SEO URL (slug) -->
                                <div class="mb-3">
                                    <label for="slug" class="form-label">
                                        SEO URL (необязательно)
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">/properties/</span>
                                        <input type="text" 
                                               class="form-control <?= hasError('slug') ? 'is-invalid' : '' ?>" 
                                               id="slug" 
                                               name="slug" 
                                               value="<?= old('slug', $property['slug'] ?? '') ?>"
                                               placeholder="будет создан автоматически">
                                        <?php if (hasError('slug')): ?>
                                            <div class="invalid-feedback">
                                                <?= firstError('slug') ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-text">
                                        Оставьте пустым для автоматической генерации
                                    </div>
                                </div>

                                <!-- Описание -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Описание</label>
                                    <textarea class="form-control <?= hasError('description') ? 'is-invalid' : '' ?>" 
                                              id="description" 
                                              name="description" 
                                              rows="5"
                                              placeholder="Подробное описание объекта, его особенности, преимущества..."><?= old('description', $property['description'] ?? '') ?></textarea>
                                    <?php if (hasError('description')): ?>
                                        <div class="invalid-feedback">
                                            <?= firstError('description') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Тип и категория -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="type" class="form-label">
                                                Тип недвижимости <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select <?= hasError('type') ? 'is-invalid' : '' ?>" 
                                                    id="type" 
                                                    name="type" 
                                                    required>
                                                <option value="">Выберите тип</option>
                                                <?php foreach ($types as $key => $label): ?>
                                                    <option value="<?= $key ?>" 
                                                            <?= old('type', $property['type'] ?? '') === $key ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($label) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (hasError('type')): ?>
                                                <div class="invalid-feedback">
                                                    <?= firstError('type') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Категория</label>
                                            <select class="form-select <?= hasError('category_id') ? 'is-invalid' : '' ?>" 
                                                    id="category_id" 
                                                    name="category_id">
                                                <option value="">Без категории</option>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?= $category['id'] ?>" 
                                                            <?= (string)old('category_id', $property['category_id'] ?? '') === (string)$category['id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($category['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (hasError('category_id')): ?>
                                                <div class="invalid-feedback">
                                                    <?= firstError('category_id') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Цена -->
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="price" class="form-label">
                                                Цена <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" 
                                                       class="form-control <?= hasError('price') ? 'is-invalid' : '' ?>" 
                                                       id="price" 
                                                       name="price" 
                                                       value="<?= old('price', $property['price'] ?? '') ?>"
                                                       min="0"
                                                       step="1000"
                                                       placeholder="50000"
                                                       required>
                                                <span class="input-group-text">₽</span>
                                                <?php if (hasError('price')): ?>
                                                    <div class="invalid-feedback">
                                                        <?= firstError('price') ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="currency" class="form-label">Валюта</label>
                                            <select class="form-select" id="currency" name="currency">
                                                <option value="RUB" <?= old('currency', $property['currency'] ?? 'RUB') === 'RUB' ? 'selected' : '' ?>>RUB (₽)</option>
                                                <option value="USD" <?= old('currency', $property['currency'] ?? '') === 'USD' ? 'selected' : '' ?>>USD ($)</option>
                                                <option value="EUR" <?= old('currency', $property['currency'] ?? '') === 'EUR' ? 'selected' : '' ?>>EUR (€)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Характеристики -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-ruler-combined"></i> Характеристики</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="area" class="form-label">Площадь (м²)</label>
                                            <input type="number" 
                                                   class="form-control <?= hasError('area') ? 'is-invalid' : '' ?>" 
                                                   id="area" 
                                                   name="area" 
                                                   value="<?= old('area', $property['area'] ?? '') ?>"
                                                   min="1"
                                                   step="0.1"
                                                   placeholder="65.5">
                                            <?php if (hasError('area')): ?>
                                                <div class="invalid-feedback">
                                                    <?= firstError('area') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="rooms" class="form-label">Количество комнат</label>
                                            <select class="form-select <?= hasError('rooms') ? 'is-invalid' : '' ?>" 
                                                    id="rooms" 
                                                    name="rooms">
                                                <option value="">Не указано</option>
                                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                                    <option value="<?= $i ?>" 
                                                            <?= (string)old('rooms', $property['rooms'] ?? '') === (string)$i ? 'selected' : '' ?>>
                                                        <?= $i ?><?= $i >= 5 ? '+' : '' ?>
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                            <?php if (hasError('rooms')): ?>
                                                <div class="invalid-feedback">
                                                    <?= firstError('rooms') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="bathrooms" class="form-label">Санузлы</label>
                                            <select class="form-select" id="bathrooms" name="bathrooms">
                                                <option value="">Не указано</option>
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <option value="<?= $i ?>" 
                                                            <?= (string)old('bathrooms', $property['bathrooms'] ?? '') === (string)$i ? 'selected' : '' ?>>
                                                        <?= $i ?>
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="floor" class="form-label">Этаж</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="floor" 
                                                   name="floor" 
                                                   value="<?= old('floor', $property['floor'] ?? '') ?>"
                                                   min="0"
                                                   max="100"
                                                   placeholder="5">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="total_floors" class="form-label">Всего этажей</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="total_floors" 
                                                   name="total_floors" 
                                                   value="<?= old('total_floors', $property['total_floors'] ?? '') ?>"
                                                   min="1"
                                                   max="200"
                                                   placeholder="9">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Адрес -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Адрес и местоположение</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Полный адрес</label>
                                    <input type="text" 
                                           class="form-control <?= hasError('address') ? 'is-invalid' : '' ?>" 
                                           id="address" 
                                           name="address" 
                                           value="<?= old('address', $property['address'] ?? '') ?>"
                                           placeholder="ул. Пушкина, д. 10, кв. 5">
                                    <?php if (hasError('address')): ?>
                                        <div class="invalid-feedback">
                                            <?= firstError('address') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="city" class="form-label">Город</label>
                                            <input type="text" 
                                                   class="form-control <?= hasError('city') ? 'is-invalid' : '' ?>" 
                                                   id="city" 
                                                   name="city" 
                                                   value="<?= old('city', $property['city'] ?? '') ?>"
                                                   placeholder="Москва"
                                                   list="cities-list">
                                            
                                            <!-- Датасет популярных городов -->
                                            <datalist id="cities-list">
                                                <option value="Москва">
                                                <option value="Санкт-Петербург">
                                                <option value="Новосибирск">
                                                <option value="Екатеринбург">
                                                <option value="Казань">
                                                <option value="Нижний Новгород">
                                                <option value="Челябинск">
                                                <option value="Самара">
                                                <option value="Омск">
                                                <option value="Ростов-на-Дону">
                                            </datalist>
                                            
                                            <?php if (hasError('city')): ?>
                                                <div class="invalid-feedback">
                                                    <?= firstError('city') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="district" class="form-label">Район</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="district" 
                                                   name="district" 
                                                   value="<?= old('district', $property['district'] ?? '') ?>"
                                                   placeholder="Центральный">
                                        </div>
                                    </div>
                                </div>

                                <!-- Координаты (для карты) -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="latitude" class="form-label">Широта</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="latitude" 
                                                   name="latitude" 
                                                   value="<?= old('latitude', $property['latitude'] ?? '') ?>"
                                                   step="0.000001"
                                                   placeholder="55.755826">
                                            <div class="form-text">Для отображения на карте</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="longitude" class="form-label">Долгота</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="longitude" 
                                                   name="longitude" 
                                                   value="<?= old('longitude', $property['longitude'] ?? '') ?>"
                                                   step="0.000001"
                                                   placeholder="37.617300">
                                            <div class="form-text">Для отображения на карте</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Боковая панель -->
                    <div class="col-lg-4">
                        <!-- Изображения -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="fas fa-camera"></i> Изображения</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="images" class="form-label">Загрузить изображения</label>
                                    <input type="file" 
                                           class="form-control" 
                                           id="images" 
                                           name="images[]" 
                                           multiple 
                                           accept="image/jpeg,image/png,image/webp">
                                    <div class="form-text">
                                        Максимум 10 изображений. Форматы: JPG, PNG, WebP. Размер до 5MB каждое.
                                    </div>
                                </div>

                                <!-- Превью изображений -->
                                <div id="image-preview" class="row g-2"></div>

                                <!-- Существующие изображения (для редактирования) -->
                                <?php if ($isEdit && !empty($property['images'])): ?>
                                    <div class="mt-3">
                                        <h6>Текущие изображения:</h6>
                                        <div class="row g-2" id="existing-images">
                                            <?php foreach ($property['images'] as $image): ?>
                                                <div class="col-6" id="image-<?= $image['id'] ?>">
                                                    <div class="position-relative">
                                                        <img src="/uploads/properties/<?= htmlspecialchars($image['filename']) ?>" 
                                                             class="img-fluid rounded" 
                                                             style="aspect-ratio: 1; object-fit: cover;">
                                                        
                                                        <?php if ($image['is_main']): ?>
                                                            <span class="badge bg-success position-absolute top-0 start-0 m-1">
                                                                Главное
                                                            </span>
                                                        <?php endif; ?>
                                                        
                                                        <button type="button" 
                                                                class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1"
                                                                onclick="deleteImage(<?= $property['id'] ?>, <?= $image['id'] ?>)">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Статус -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0"><i class="fas fa-toggle-on"></i> Настройки публикации</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Статус</label>
                                    <select class="form-select <?= hasError('status') ? 'is-invalid' : '' ?>" 
                                            id="status" 
                                            name="status">
                                        <?php foreach ($statuses as $key => $label): ?>
                                            <option value="<?= $key ?>" 
                                                    <?= old('status', $property['status'] ?? 'draft') === $key ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($label) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (hasError('status')): ?>
                                        <div class="invalid-feedback">
                                            <?= firstError('status') ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="form-text">
                                        <strong>Черновик</strong> - не отображается на сайте<br>
                                        <strong>Активно</strong> - отображается для всех<br>
                                        <strong>Сдано в аренду</strong> - помечено как занятое
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Кнопки действий -->
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save"></i> 
                                        <?= $isEdit ? 'Сохранить изменения' : 'Создать объект' ?>
                                    </button>
                                    
                                    <a href="/properties" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Отменить
                                    </a>

                                    <?php if ($isEdit): ?>
                                        <button type="button" 
                                                class="btn btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal">
                                            <i class="fas fa-trash"></i> Удалить объект
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно удаления (только для редактирования) -->
<?php if ($isEdit): ?>
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

<script>
// Превью загружаемых изображений
document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    
    Array.from(e.target.files).forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-6';
                col.innerHTML = `
                    <div class="position-relative">
                        <img src="${e.target.result}" 
                             class="img-fluid rounded" 
                             style="aspect-ratio: 1; object-fit: cover;">
                        ${index === 0 ? '<span class="badge bg-success position-absolute top-0 start-0 m-1">Главное</span>' : ''}
                    </div>
                `;
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        }
    });
});

// Удаление существующего изображения
function deleteImage(propertyId, imageId) {
    if (confirm('Удалить это изображение?')) {
        fetch(`/properties/${propertyId}/images/${imageId}/delete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({
                _token: document.querySelector('input[name="_token"]').value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`image-${imageId}`).remove();
            } else {
                alert('Ошибка при удалении изображения');
            }
        })
        .catch(error => {
            alert('Ошибка при удалении изображения');
        });
    }
}

// Автоматическая генерация slug из названия
document.getElementById('title').addEventListener('input', function() {
    const slugField = document.getElementById('slug');
    if (!slugField.value || slugField.dataset.manual !== 'true') {
        const slug = transliterate(this.value)
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        slugField.value = slug;
    }
});

// Помечаем slug как введенный вручную
document.getElementById('slug').addEventListener('input', function() {
    this.dataset.manual = 'true';
});

// Функция транслитерации
function transliterate(text) {
    const ru = 'абвгдеёжзийклмнопрстуфхцчшщъыьэюя';
    const en = 'abvgdeejzijklmnoprstufhcchshschyeyuya';
    
    return text.split('').map(char => {
        const index = ru.indexOf(char.toLowerCase());
        return index !== -1 ? en[index] : char;
    }).join('');
}

// Валидация формы Bootstrap
(function() {
    'use strict';
    window.addEventListener('load', function() {
        const forms = document.getElementsByClassName('needs-validation');
        Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>

<style>
.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.card {
    border: none;
}

.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

#image-preview img, 
#existing-images img {
    transition: transform 0.2s;
    cursor: pointer;
}

#image-preview img:hover, 
#existing-images img:hover {
    transform: scale(1.05);
}

.position-relative .btn {
    opacity: 0.8;
}

.position-relative .btn:hover {
    opacity: 1;
}
</style>