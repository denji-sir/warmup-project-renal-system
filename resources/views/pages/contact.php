<?php $title = 'Контакты — ' . config('app.name'); ?>

<?php ob_start(); ?>

<section class="section section--muted">
    <div class="container">
        <div class="section__header">
            <span class="eyebrow">Свяжитесь с нами</span>
            <h1 class="section__title">Мы на связи 7 дней в неделю</h1>
            <p class="section__subtitle section__subtitle--muted">
                Ответим на вопросы, организуем просмотр и подготовим аналитику по рынку в течение 24 часов.
            </p>
        </div>
        <div class="split-grid">
            <div class="card">
                <div class="card-body">
                    <h3>Контакты</h3>
                    <div class="contact-info">
                        <?php
                        $contacts = [
                            ['title' => 'Телефон', 'value' => '+7 (495) 123-45-67', 'extra' => '+7 (800) 555-01-23', 'icon' => '<path fill="currentColor" d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.57 17.57 0 0 0 4.168 6.608 17.57 17.57 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.405-.318l-.178-.139a14.6 14.6 0 0 1-3.403-3.403l-.139-.178a1.75 1.75 0 0 1-.318-1.405l.547-2.19a.678.678 0 0 0-.122-.58Z"/>'],
                            ['title' => 'Email', 'value' => 'info@realestate.ru', 'extra' => 'support@realestate.ru', 'icon' => '<path fill="currentColor" d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v.586l-8 4.8-8-4.8V4zm0 2.697v5.303A2 2 0 0 0 2 14h12a2 2 0 0 0 2-2V6.697l-7.555 4.53a1 1 0 0 1-1.05 0L0 6.697z"/>'],
                            ['title' => 'Офис', 'value' => 'Москва, ул. Тверская, 123', 'extra' => 'м. Тверская, м. Пушкинская', 'icon' => '<path fill="currentColor" d="M8 15s6-5.686 6-10A6 6 0 1 0 2 5c0 4.314 6 10 6 10Zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/>'],
                            ['title' => 'График', 'value' => 'Пн-Пт: 09:00–20:00', 'extra' => 'Сб-Вс: 10:00–18:00', 'icon' => '<path fill="currentColor" d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/><path fill="currentColor" d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>'],
                        ];
                        foreach ($contacts as $contact): ?>
                            <div class="contact-item">
                                <span class="contact-icon">
                                    <svg width="20" height="20" viewBox="0 0 16 16" aria-hidden="true"><?= $contact['icon'] ?></svg>
                                </span>
                                <div class="contact-details">
                                    <h5><?= e($contact['title']) ?></h5>
                                    <p><?= e($contact['value']) ?><br><?= e($contact['extra']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="cta-panel__actions mt-lg">
                        <a href="tel:+74951234567" class="btn btn-primary btn-sm">Позвонить сейчас</a>
                        <a href="https://yandex.ru/maps" class="btn btn-outline btn-sm" target="_blank" rel="noopener">Построить маршрут</a>
                    </div>
                </div>
            </div>
            <div class="contact-card">
                <h3 class="mb-lg">Оставьте заявку</h3>
                <p class="text-secondary mb-lg">Заполните форму, и мы свяжемся с вами в течение 15 минут.</p>
                <form action="/contact" method="POST" data-ajax-form>
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="name" class="form-label">Ваше имя *</label>
                        <input type="text" id="name" name="name" class="form-input" required data-rules="required|min:2|max:100" value="<?= e($_POST['name'] ?? '') ?>" placeholder="Иван Иванов">
                    </div>
                    <div class="form-group">
                        <label for="phone" class="form-label">Телефон *</label>
                        <input type="tel" id="phone" name="phone" class="form-input" required data-rules="required|min:10|max:20" value="<?= e($_POST['phone'] ?? '') ?>" placeholder="+7 (900) 123-45-67">
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-input" data-rules="email|max:255" value="<?= e($_POST['email'] ?? '') ?>" placeholder="example@domain.com">
                    </div>
                    <div class="form-group">
                        <label for="service" class="form-label">Услуга *</label>
                        <select id="service" name="service" class="form-select" required data-rules="required|in:buy,sell,rent,lease,evaluate">
                            <option value="">Выберите услугу</option>
                            <option value="buy" <?= ($_POST['service'] ?? '') === 'buy' ? 'selected' : '' ?>>Купить недвижимость</option>
                            <option value="sell" <?= ($_POST['service'] ?? '') === 'sell' ? 'selected' : '' ?>>Продать недвижимость</option>
                            <option value="rent" <?= ($_POST['service'] ?? '') === 'rent' ? 'selected' : '' ?>>Снять жильё</option>
                            <option value="lease" <?= ($_POST['service'] ?? '') === 'lease' ? 'selected' : '' ?>>Сдать в аренду</option>
                            <option value="evaluate" <?= ($_POST['service'] ?? '') === 'evaluate' ? 'selected' : '' ?>>Оценить объект</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="message" class="form-label">Сообщение</label>
                        <textarea id="message" name="message" class="form-textarea" data-rules="max:1000" placeholder="Расскажите о проекте и ваших сроках..."><?= e($_POST['message'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Отправить заявку</button>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="section section--compact">
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h3 class="mb-md">Офис на карте</h3>
                <p class="text-secondary mb-lg">Москва, ул. Тверская, 123. Мы находимся в 3 минутах от метро «Тверская».</p>
                <div class="map-container map-placeholder">
                    <div class="text-center">
                        <h5>Здесь будет карта</h5>
                        <p class="text-secondary">Подключите Яндекс.Карты или Google Maps для интерактивной схемы проезда.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php $content = ob_get_clean(); ?>

<?php require __DIR__ . '/../layouts/main.php'; ?>
