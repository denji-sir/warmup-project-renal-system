<?php $title = 'Контакты - ' . config('app.name'); ?>

<?php ob_start(); ?>

<div class="container py-xl">
    <div class="row">
        <!-- Contact Information -->
        <div class="col-md-6 mb-lg">
            <h2>Свяжитесь с нами</h2>
            <p class="lead mb-lg">
                Мы всегда готовы ответить на ваши вопросы и помочь с выбором недвижимости.
            </p>
            
            <div class="contact-info">
                <div class="contact-item mb-lg">
                    <div class="contact-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122L9.98 10.97a.678.678 0 0 1-.198-.02c-.537-.118-.977-.558-1.095-1.095a.678.678 0 0 1-.02-.198l.537-1.804a.678.678 0 0 0-.122-.58L6.288 5.467a.678.678 0 0 0-1.015-.063L3.654 1.328z"/>
                        </svg>
                    </div>
                    <div class="contact-details">
                        <h5>Телефон</h5>
                        <p>+7 (495) 123-45-67<br>+7 (800) 555-01-23 (бесплатный)</p>
                    </div>
                </div>
                
                <div class="contact-item mb-lg">
                    <div class="contact-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M2 2a2 2 0 0 0-2 2v8.01A2 2 0 0 0 2 14h5.5a.5.5 0 0 0 0-1H2a1 1 0 0 1-.966-.741l5.64-3.471L8 9.583l7-4.2V8.5a.5.5 0 0 0 1 0V4a2 2 0 0 0-2-2H2Zm3.708 6.208L1 11.105V5.383l4.708 2.825ZM1 4.217V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v.217l-7 4.2-7-4.2Z"/>
                        </svg>
                    </div>
                    <div class="contact-details">
                        <h5>Email</h5>
                        <p>info@realestate.ru<br>support@realestate.ru</p>
                    </div>
                </div>
                
                <div class="contact-item mb-lg">
                    <div class="contact-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                        </svg>
                    </div>
                    <div class="contact-details">
                        <h5>Адрес офиса</h5>
                        <p>г. Москва, ул. Тверская, д. 123, офис 456<br>м. Тверская, м. Пушкинская</p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                        </svg>
                    </div>
                    <div class="contact-details">
                        <h5>Часы работы</h5>
                        <p>Пн-Пт: 9:00 - 20:00<br>Сб-Вс: 10:00 - 18:00</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contact Form -->
        <div class="col-md-6">
            <div class="contact-card">
                <h3 class="mb-lg">Оставьте заявку</h3>
                <p class="text-secondary mb-lg">
                    Заполните форму, и мы свяжемся с вами в течение 15 минут
                </p>
                
                <form action="/contact" method="POST" data-ajax-form>
                    <?= csrf_field() ?>
                    
                    <div class="form-group">
                        <label for="name" class="form-label">Ваше имя *</label>
                        <input type="text" id="name" name="name" class="form-input" 
                               required data-rules="required|min:2|max:100"
                               value="<?= e($_POST['name'] ?? '') ?>"
                               placeholder="Иван Иванов">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone" class="form-label">Телефон *</label>
                        <input type="tel" id="phone" name="phone" class="form-input" 
                               required data-rules="required|min:10|max:20"
                               value="<?= e($_POST['phone'] ?? '') ?>"
                               placeholder="+7 (900) 123-45-67">
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-input" 
                               data-rules="email|max:255"
                               value="<?= e($_POST['email'] ?? '') ?>"
                               placeholder="example@domain.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="service" class="form-label">Услуга *</label>
                        <select id="service" name="service" class="form-select" 
                                required data-rules="required|in:buy,sell,rent,lease,evaluate">
                            <option value="">Выберите услугу</option>
                            <option value="buy" <?= ($_POST['service'] ?? '') === 'buy' ? 'selected' : '' ?>>
                                Купить недвижимость
                            </option>
                            <option value="sell" <?= ($_POST['service'] ?? '') === 'sell' ? 'selected' : '' ?>>
                                Продать недвижимость
                            </option>
                            <option value="rent" <?= ($_POST['service'] ?? '') === 'rent' ? 'selected' : '' ?>>
                                Снять жильё
                            </option>
                            <option value="lease" <?= ($_POST['service'] ?? '') === 'lease' ? 'selected' : '' ?>>
                                Сдать в аренду
                            </option>
                            <option value="evaluate" <?= ($_POST['service'] ?? '') === 'evaluate' ? 'selected' : '' ?>>
                                Оценить недвижимость
                            </option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message" class="form-label">Сообщение</label>
                        <textarea id="message" name="message" class="form-textarea" 
                                  data-rules="max:1000"
                                  placeholder="Расскажите подробнее о ваших потребностях..."><?= e($_POST['message'] ?? '') ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                        Отправить заявку
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Map Section -->
<section class="bg-secondary py-xl">
    <div class="container">
        <h3 class="text-center mb-lg">Как нас найти</h3>
        
        <div class="row">
            <div class="col-12">
                <!-- Map placeholder -->
                <div id="contact-map" class="map-container" style="height: 400px; background: #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <div class="text-center">
                        <h5>Карта офиса</h5>
                        <p class="text-secondary">г. Москва, ул. Тверская, д. 123</p>
                        <small>Интеграция с Яндекс.Карты</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php $content = ob_get_clean(); ?>

<!-- Custom styles -->
<?php ob_start(); ?>
.contact-info .contact-item {
    display: flex;
    align-items: flex-start;
    gap: var(--space-md);
}

.contact-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    background-color: var(--color-primary);
    color: var(--text-inverse);
    border-radius: 50%;
    flex-shrink: 0;
}

.contact-details h5 {
    margin-bottom: var(--space-xs);
    color: var(--text-primary);
}

.contact-details p {
    margin-bottom: 0;
    color: var(--text-secondary);
}

.contact-card {
    background-color: var(--bg-secondary);
    padding: var(--space-xl);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
}

.map-container {
    box-shadow: var(--shadow-sm);
}

@media (max-width: 768px) {
    .contact-item {
        flex-direction: column;
        text-align: center;
    }
    
    .contact-card {
        padding: var(--space-lg);
    }
}
<?php $customCss = ob_get_clean(); ?>

<?php require __DIR__ . '/../layouts/main.php'; ?>