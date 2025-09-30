<?php $title = 'Главная — ' . config('app.name'); ?>
<?php $description = 'Эксперт в элитной недвижимости: квартиры, пентхаусы и коммерческие пространства с полным сопровождением сделки.'; ?>

<?php ob_start(); ?>

<section class="hero section">
    <div class="container">
        <div class="hero__grid">
            <div class="hero__content">
                <span class="eyebrow">Недвижимость премиум-класса</span>
                <h1 class="hero__title">Найдите пространство, созданное для вашей жизни</h1>
                <p class="hero__subtitle">
                    Эксперты <?= config('app.name') ?> подберут квартиру, дом или коммерческий объект с учётом ваших целей,
                    бюджета и образа жизни. От первой консультации до передачи ключей — всё под контролем.
                </p>
                <div class="hero__actions">
                    <a href="/properties" class="btn btn-primary btn-lg">Подобрать объект</a>
                    <a href="/services" class="btn btn-outline btn-lg">Экспертная консультация</a>
                </div>
                <div class="stat-grid">
                    <div class="stat-card">
                        <div class="stat-card__value"><?= number_format($stats['total_properties'], 0, ',', ' ') ?>+</div>
                        <div class="stat-card__label">Объектов в каталоге</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card__value"><?= number_format($stats['monthly_deals'], 0, ',', ' ') ?>+</div>
                        <div class="stat-card__label">Сделок ежемесячно</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card__value"><?= number_format($stats['years_experience']) ?>+</div>
                        <div class="stat-card__label">Лет опыта на рынке</div>
                    </div>
                </div>
            </div>
            <div class="hero__media">
                <div class="hero-card">
                    <span class="hero-card__pill">Эксклюзив недели</span>
                    <div class="hero-card__image">
                        <img src="/assets/images/hero-building.jpg" alt="Современный жилой комплекс">
                    </div>
                    <div class="property-meta">
                        <span class="property-meta__item">
                            <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path fill="currentColor" d="M8 3.293 1.5 9.793l.707.707L2 10.707V14h4v-3h4v3h4v-3.293l-.207-.207.707-.707L8 3.293zM8 1l8 8v7H9v-3H7v3H0V9l8-8z"/></svg>
                            Панорамные виды на Москва-Сити
                        </span>
                        <span class="property-meta__item">
                            <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path fill="currentColor" d="M4.5 1h7a1.5 1.5 0 0 1 1.5 1.5V4h-10V2.5A1.5 1.5 0 0 1 4.5 1ZM3 5h10v8.5A1.5 1.5 0 0 1 11.5 15h-7A1.5 1.5 0 0 1 3 13.5V5Zm3 2v6h1.5V7H6Zm3.5 0v6H11V7H9.5Z"/></svg>
                            Авторский дизайн интерьера
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="search-panel">
            <form action="/properties" method="GET" class="search-panel__form">
                <div class="search-panel__field">
                    <label for="search-location">Локация</label>
                    <input id="search-location" type="text" name="q" class="form-input"
                           placeholder="Район, метро или улица"
                           value="<?= e($_GET['q'] ?? '') ?>">
                </div>
                <div class="search-panel__field">
                    <label for="search-type">Тип сделки</label>
                    <select id="search-type" name="type" class="form-select">
                        <option value="">Любой формат</option>
                        <option value="sale" <?= ($_GET['type'] ?? '') === 'sale' ? 'selected' : '' ?>>Купить</option>
                        <option value="rent" <?= ($_GET['type'] ?? '') === 'rent' ? 'selected' : '' ?>>Арендовать</option>
                    </select>
                </div>
                <div class="search-panel__field">
                    <label for="search-category">Категория</label>
                    <select id="search-category" name="category" class="form-select">
                        <option value="">Все категории</option>
                        <option value="apartment">Квартиры</option>
                        <option value="house">Дома и резиденции</option>
                        <option value="commercial">Коммерческие площади</option>
                    </select>
                </div>
                <div class="search-panel__field">
                    <label for="search-budget">Бюджет, ₽</label>
                    <input id="search-budget" type="number" name="max_price" class="form-input" placeholder="До">
                </div>
                <button type="submit" class="btn btn-primary btn-lg">Найти объект</button>
            </form>
            <div class="floating-strip">
                <?php foreach (['Сити', 'Золотая Миля', 'Хамовники', 'Барвиха', 'Сколково'] as $label): ?>
                    <span class="pill">#<?= $label ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="section section--muted">
    <div class="container">
        <div class="section__header">
            <span class="eyebrow">Подборка от экспертов</span>
            <h2 class="section__title">Лучшие предложения недели</h2>
            <p class="section__subtitle section__subtitle--muted">
                Объекты с уникальной архитектурой, панорамными видами и готовыми дизайнерскими решениями.
            </p>
        </div>
        <div class="property-grid">
            <?php for ($i = 1; $i <= 6; $i++): ?>
                <article class="property-card">
                    <div class="property-image">
                        <img src="/assets/images/properties/property-<?= $i ?>.jpg" alt="Премиальная недвижимость">
                        <span class="property-badge <?= $i % 2 ? 'sale' : 'rent' ?>">
                            <?= $i % 2 ? 'Продажа' : 'Аренда' ?>
                        </span>
                        <div class="property-price">
                            <?= $i % 2 ? number_format(45000000 + $i * 3500000, 0, '.', ' ') . ' ₽' : number_format(250000 + $i * 40000, 0, '.', ' ') . ' ₽' ?>
                            <span><?= $i % 2 ? 'стоимость' : 'в месяц' ?></span>
                        </div>
                    </div>
                    <div class="property-details">
                        <h3 class="property-title"><?= ['Sky Garden Residences', 'River Park Deluxe', 'Light House', 'Prime Boulevard', 'Aurora House', 'Icon Loft'][$i-1] ?></h3>
                        <p class="property-address">
                            <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path fill="currentColor" d="M8 15s6-5.686 6-10A6 6 0 1 0 2 5c0 4.314 6 10 6 10Zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/></svg>
                            Москва, <?= ['Пресненская наб., 12', 'Фрунзенская наб., 44', 'Большая Полянка, 7', 'Звенигородское ш., 5', 'Рублёвское ш., 55', 'Пятницкая, 47'][$i-1] ?>
                        </p>
                        <div class="property-features">
                            <span class="property-feature">
                                <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path fill="currentColor" d="M4 1h8a2 2 0 0 1 2 2v10H2V3a2 2 0 0 1 2-2Zm0 1a1 1 0 0 0-1 1v1h10V3a1 1 0 0 0-1-1H4Zm9 3H3v7h10V5Z"/></svg>
                                <?= 3 + $i ?> спальни
                            </span>
                            <span class="property-feature">
                                <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path fill="currentColor" d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16Zm.5-12v4.25L11 10l-.75.66L7.5 7.75V4h1Z"/></svg>
                                <?= 120 + $i * 20 ?> м²
                            </span>
                            <span class="property-feature">
                                <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path fill="currentColor" d="M2 2h12v5H2z"/><path fill="currentColor" d="M3 8h2v6H3zm8 0h2v6h-2zM6 8h4v6H6z"/></svg>
                                <?= 20 + $i ?> этаж
                            </span>
                        </div>
                        <div class="property-actions">
                            <a href="/properties/<?= $i ?>" class="btn btn-primary btn-sm">Детали объекта</a>
                            <div class="property-buttons d-flex gap-sm">
                                <button type="button" class="btn btn-outline btn-sm" data-favorite-toggle="<?= $i ?>" aria-label="Добавить в избранное">♡</button>
                                <button type="button" class="btn btn-outline btn-sm" data-compare-add="<?= $i ?>" aria-label="Сравнить">⇄</button>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endfor; ?>
        </div>
        <div class="text-center mt-xl">
            <a href="/properties" class="btn btn-outline btn-lg">Посмотреть весь каталог</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section__header">
            <span class="eyebrow">Что вы получаете</span>
            <h2 class="section__title">Комплексный сервис, который экономит время</h2>
            <p class="section__subtitle section__subtitle--muted">
                Работая с нами, вы получаете команду экспертов: аналитиков, юристов, брокеров и дизайнеров.
            </p>
        </div>
        <div class="feature-grid">
            <?php
            $features = [
                [
                    'title' => 'Персональный брокер',
                    'text' => 'Эксперт сопровождает вас на каждом этапе: от подбора до подписания договора.',
                    'icon' => '<path fill="currentColor" d="M8 0a5 5 0 0 1 5 5c0 2.485-2 5.5-5 9.5C5 10.5 3 7.485 3 5a5 5 0 0 1 5-5Zm0 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/>',
                ],
                [
                    'title' => 'Юридическая экспертиза',
                    'text' => 'Проверяем документы, оцениваем риски, согласуем условия сделки и скрытые расходы.',
                    'icon' => '<path fill="currentColor" d="M2 1h12l1 3-7 11-7-11 1-3Zm2 .5-1 2 5 8 5-8-1-2H4Zm2 2h1v2H6V3.5Zm3 0h1v2H9V3.5Z"/>',
                ],
                [
                    'title' => 'Финансовые решения',
                    'text' => 'Привлекаем лучшие ипотечные программы и инвестиционные инструменты.',
                    'icon' => '<path fill="currentColor" d="M1 5 8 1l7 4-7 4-7-4Zm7 5 7-4v4l-7 4-7-4V6l7 4Z"/>',
                ],
            ];
            foreach ($features as $feature): ?>
                <article class="feature-card">
                    <span class="feature-card__icon">
                        <svg width="28" height="28" viewBox="0 0 16 16" aria-hidden="true"><?= $feature['icon'] ?></svg>
                    </span>
                    <h3><?= e($feature['title']) ?></h3>
                    <p><?= e($feature['text']) ?></p>
                    <div class="pill-list">
                        <span class="pill">Аналитика</span>
                        <span class="pill">Проверка</span>
                        <span class="pill">Сопровождение</span>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--muted">
    <div class="container">
        <div class="section__header">
            <span class="eyebrow">Путь клиента</span>
            <h2 class="section__title">От первой встречи до вручения ключей</h2>
        </div>
        <div class="timeline">
            <?php
            $steps = [
                ['title' => 'Диагностика запроса', 'text' => 'Определяем критерии, бюджет и формируем стратегию поиска.'],
                ['title' => 'Подбор и просмотры', 'text' => 'Предлагаем shortlist объектов, организуем приватные показы.'],
                ['title' => 'Переговоры и проверка', 'text' => 'Согласовываем условия, проводим due diligence, готовим пакет документов.'],
                ['title' => 'Сделка и пост-сервис', 'text' => 'Контролируем расчёты, подписываем договор, передаём ключи и помогаем с переездом.'],
            ];
            foreach ($steps as $step): ?>
                <div class="timeline__item">
                    <h4><?= e($step['title']) ?></h4>
                    <p><?= e($step['text']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--dark">
    <div class="container">
        <div class="section__header">
            <span class="eyebrow">Отзывы клиентов</span>
            <h2 class="section__title">92% покупателей приходят по рекомендации</h2>
            <p class="section__subtitle">Нам доверяют предприниматели, инвесторы и семейные пары, ищущие новое пространство для жизни.</p>
        </div>
        <div class="testimonial-grid">
            <?php
            $testimonials = [
                ['name' => 'Алексей и Мария', 'role' => 'Купили пентхаус на Пресне', 'text' => 'Команда быстро поняла запрос, подобрала идеальный вариант и закрыла сделку за три недели. Отдельное спасибо за дизайн-концепцию интерьера.'],
                ['name' => 'Елена', 'role' => 'Инвестор', 'text' => 'Получила подбор из 7 ликвидных объектов под сдачу. Все расчёты по окупаемости — прозрачные и понятные. Уже оформили две сделки.'],
                ['name' => 'Игорь', 'role' => 'Основатель IT-сервиса', 'text' => 'Сопровождение сделки на уровне private banking: юристы, финансисты, аналитика по рынку. Чувствовал поддержку на каждом этапе.'],
            ];
            foreach ($testimonials as $review): ?>
                <article class="testimonial-card">
                    <div class="testimonial-card__rating">★★★★★</div>
                    <p><?= e($review['text']) ?></p>
                    <div>
                        <div class="testimonial-card__author"><?= e($review['name']) ?></div>
                        <small><?= e($review['role']) ?></small>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--compact">
    <div class="container">
        <div class="cta-panel">
            <div>
                <h2 class="section__title text-inverse">Начните с персональной встречи</h2>
                <p class="section__subtitle">Расскажите нам о своих планах, и мы подготовим подборку объектов в течение 24 часов.</p>
            </div>
            <div class="cta-panel__actions">
                <a href="/contact" class="btn btn-outline-light btn-lg">Записаться на консультацию</a>
                <a href="tel:+74951234567" class="btn btn-primary btn-lg">+7 (495) 123-45-67</a>
            </div>
        </div>
    </div>
</section>

<?php $content = ob_get_clean(); ?>

<?php require __DIR__ . '/layouts/main.php'; ?>
