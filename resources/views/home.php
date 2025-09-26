<?php $title = 'Главная страница - ' . config('app.name'); ?>
<?php $description = 'Найдите идеальную недвижимость для покупки или аренды. Квартиры, дома, коммерческая недвижимость в лучших районах города.'; ?>

<?php ob_start(); ?>

<!-- Hero Section -->
<section class="hero bg-primary text-inverse py-2xl">
    <div class="container">
        <div class="row items-center">
            <div class="col-md-6">
                <h1 class="display-4 mb-lg">Найдите свой идеальный дом</h1>
                <p class="lead mb-xl">
                    Более 10 000 объектов недвижимости. Квартиры, дома, коммерческая недвижимость. 
                    Профессиональная поддержка на каждом этапе сделки.
                </p>
                
                <!-- Quick Search -->
                <div class="search-bar mb-lg">
                    <form action="/properties" method="GET" class="search-form">
                        <div class="search-input-group">
                            <input type="text" name="q" class="search-input" 
                                   placeholder="Введите район, метро или адрес..." 
                                   value="<?= e($_GET['q'] ?? '') ?>">
                            <select name="type" class="search-select">
                                <option value="">Тип сделки</option>
                                <option value="sale" <?= ($_GET['type'] ?? '') === 'sale' ? 'selected' : '' ?>>Купить</option>
                                <option value="rent" <?= ($_GET['type'] ?? '') === 'rent' ? 'selected' : '' ?>>Снять</option>
                            </select>
                            <select name="category" class="search-select">
                                <option value="">Тип недвижимости</option>
                                <option value="apartment">Квартира</option>
                                <option value="house">Дом</option>
                                <option value="commercial">Коммерческая</option>
                            </select>
                            <button type="submit" class="btn btn-warning btn-lg">
                                Найти
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="hero-stats d-flex gap-lg">
                    <div class="stat-item">
                        <div class="stat-number">10 000+</div>
                        <div class="stat-label">Объектов</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Сделок в месяц</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">50+</div>
                        <div class="stat-label">Риелторов</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="hero-image">
                    <img src="/assets/images/hero-building.jpg" 
                         alt="Современная недвижимость" 
                         class="img-fluid rounded-lg shadow-xl">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Properties -->
<section class="featured-properties py-2xl">
    <div class="container">
        <div class="text-center mb-xl">
            <h2>Рекомендуемые предложения</h2>
            <p class="lead text-secondary">Лучшие объекты недвижимости, отобранные нашими экспертами</p>
        </div>
        
        <div class="row" id="featured-properties">
            <!-- Properties will be loaded via AJAX or PHP -->
            <?php for ($i = 1; $i <= 6; $i++): ?>
                <div class="col-md-4 mb-lg">
                    <div class="card property-card">
                        <div class="property-image">
                            <img src="/assets/images/properties/property-<?= $i ?>.jpg" 
                                 alt="Недвижимость <?= $i ?>" 
                                 data-src="/assets/images/properties/property-<?= $i ?>.jpg">
                            <div class="property-badge <?= $i % 2 ? 'sale' : 'rent' ?>">
                                <?= $i % 2 ? 'Продажа' : 'Аренда' ?>
                            </div>
                            <div class="property-price">
                                <?= $i % 2 ? number_format(5000000 + $i * 500000, 0, '.', ' ') . ' ₽' : number_format(50000 + $i * 10000, 0, '.', ' ') . ' ₽/мес' ?>
                            </div>
                        </div>
                        
                        <div class="property-details">
                            <h5 class="property-title">
                                <?php 
                                $titles = [
                                    'Современная квартира в центре',
                                    'Уютная студия у метро',
                                    'Просторная трёхкомнатная квартира',
                                    'Элитные апартаменты с видом',
                                    'Коттедж в экологическом районе',
                                    'Пентхаус с панорамными окнами'
                                ];
                                echo $titles[$i - 1];
                                ?>
                            </h5>
                            
                            <div class="property-address">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 0a5 5 0 0 0-5 5c0 4 5 11 5 11s5-7 5-11a5 5 0 0 0-5-5zm0 7a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>
                                </svg>
                                <?php
                                $addresses = [
                                    'Центральный район, ул. Тверская',
                                    'Сокольники, м. Сокольники',
                                    'Арбат, м. Смоленская',
                                    'Хамовники, м. Парк Культуры',
                                    'Московская область, Химки',
                                    'Пресненский район, м. Маяковская'
                                ];
                                echo $addresses[$i - 1];
                                ?>
                            </div>
                            
                            <div class="property-features">
                                <div class="property-feature">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                                    </svg>
                                    <?= $i ?> комн.
                                </div>
                                <div class="property-feature">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0Z"/>
                                        <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8.5A3.5 3.5 0 0 1 8 12.5V2a2 2 0 0 1 2-2H2Z"/>
                                    </svg>
                                    <?= 30 + $i * 10 ?> м²
                                </div>
                                <div class="property-feature">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M8 1a.5.5 0 0 1 .5.5V8a.5.5 0 0 1-.5.5H1.5a.5.5 0 0 1-.5-.5V1.5A.5.5 0 0 1 1.5 1H8z"/>
                                    </svg>
                                    <?= 2000 + $i * 100 ?> г.
                                </div>
                            </div>
                            
                            <div class="property-actions d-flex justify-between items-center">
                                <a href="/properties/<?= $i ?>" class="btn btn-primary btn-sm">
                                    Подробнее
                                </a>
                                <div class="property-buttons d-flex gap-sm">
                                    <button type="button" class="btn btn-outline btn-sm" 
                                            data-favorite-toggle="<?= $i ?>" 
                                            aria-label="Добавить в избранное">
                                        <span class="icon">♡</span>
                                    </button>
                                    <button type="button" class="btn btn-outline btn-sm" 
                                            data-compare-add="<?= $i ?>" 
                                            aria-label="Добавить к сравнению">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M0 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L8 13.101l-3.223 2.815A.5.5 0 0 1 4 15.5V2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L12 13.101l-3.223 2.815A.5.5 0 0 1 8 15.5V2z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
        
        <div class="text-center">
            <a href="/properties" class="btn btn-outline btn-lg">
                Посмотреть все объекты
            </a>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services bg-secondary py-2xl">
    <div class="container">
        <div class="text-center mb-xl">
            <h2>Наши услуги</h2>
            <p class="lead text-secondary">Полный спектр услуг в сфере недвижимости</p>
        </div>
        
        <div class="row">
            <div class="col-md-4 text-center mb-lg">
                <div class="service-icon mb-md">
                    <svg width="64" height="64" fill="var(--color-primary)" viewBox="0 0 16 16">
                        <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.707 1.5Z"/>
                    </svg>
                </div>
                <h4>Покупка недвижимости</h4>
                <p>Поможем найти и купить квартиру, дом или коммерческую недвижимость. Юридическое сопровождение сделки.</p>
            </div>
            
            <div class="col-md-4 text-center mb-lg">
                <div class="service-icon mb-md">
                    <svg width="64" height="64" fill="var(--color-primary)" viewBox="0 0 16 16">
                        <path d="M2.5 3A1.5 1.5 0 0 0 1 4.5V6a.5.5 0 0 0 .5.5 1.5 1.5 0 1 1 0 3 .5.5 0 0 0-.5.5v1.5A1.5 1.5 0 0 0 2.5 13h11a1.5 1.5 0 0 0 1.5-1.5V10a.5.5 0 0 0-.5-.5 1.5 1.5 0 0 1 0-3A.5.5 0 0 0 15 6V4.5A1.5 1.5 0 0 0 13.5 3h-11z"/>
                    </svg>
                </div>
                <h4>Аренда жилья</h4>
                <p>Большая база арендных предложений. Проверенные собственники. Помощь в оформлении документов.</p>
            </div>
            
            <div class="col-md-4 text-center mb-lg">
                <div class="service-icon mb-md">
                    <svg width="64" height="64" fill="var(--color-primary)" viewBox="0 0 16 16">
                        <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v1H0V4zM0 7v5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7H0zm3 2h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-1a1 1 0 0 1 1-1z"/>
                    </svg>
                </div>
                <h4>Оценка недвижимости</h4>
                <p>Профессиональная оценка рыночной стоимости. Подготовка отчетов для банков и страховых компаний.</p>
            </div>
        </div>
    </div>
</section>

<!-- Statistics -->
<section class="stats py-xl">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number" data-counter="10000">0</div>
                <div class="stat-label">Объектов недвижимости</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--color-success), #047857);">
                <div class="stat-number" data-counter="2500">0</div>
                <div class="stat-label">Успешных сделок</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--color-warning), #b45309);">
                <div class="stat-number" data-counter="98">0</div>
                <div class="stat-label">% довольных клиентов</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--color-info), #0c4a6e);">
                <div class="stat-number" data-counter="15">0</div>
                <div class="stat-label">Лет на рынке</div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta bg-primary text-inverse py-xl">
    <div class="container text-center">
        <h2 class="mb-md">Готовы найти свой идеальный дом?</h2>
        <p class="lead mb-lg">Оставьте заявку, и наш специалист свяжется с вами в течение 15 минут</p>
        
        <div class="row justify-center">
            <div class="col-md-6">
                <form class="cta-form" action="/contact" method="POST" data-ajax-form>
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <input type="text" name="name" class="form-input" 
                               placeholder="Ваше имя" required data-rules="required">
                    </div>
                    <div class="form-group">
                        <input type="tel" name="phone" class="form-input" 
                               placeholder="Номер телефона" required data-rules="required">
                    </div>
                    <div class="form-group">
                        <select name="service" class="form-select" required>
                            <option value="">Выберите услугу</option>
                            <option value="buy">Купить недвижимость</option>
                            <option value="sell">Продать недвижимость</option>
                            <option value="rent">Снять жильё</option>
                            <option value="lease">Сдать в аренду</option>
                            <option value="evaluate">Оценить недвижимость</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-warning btn-lg btn-block">
                        Получить консультацию
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php $content = ob_get_clean(); ?>

<!-- Custom styles for home page -->
<?php ob_start(); ?>
.hero {
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-hover) 100%);
}

.search-input-group {
    display: flex;
    gap: var(--space-sm);
    flex-wrap: wrap;
    background: var(--bg-primary);
    padding: var(--space-sm);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-lg);
}

.search-input {
    flex: 2;
    min-width: 200px;
    border: none;
    background: transparent;
}

.search-select {
    flex: 1;
    min-width: 120px;
    border: none;
    background: transparent;
}

.hero-stats {
    margin-top: var(--space-xl);
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: var(--text-2xl);
    font-weight: 700;
    display: block;
}

.stat-label {
    font-size: var(--text-sm);
    opacity: 0.9;
}

.service-icon {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100px;
    height: 100px;
    margin: 0 auto;
    background-color: rgba(37, 99, 235, 0.1);
    border-radius: 50%;
}

.cta-form .form-input,
.cta-form .form-select {
    background-color: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: var(--text-inverse);
}

.cta-form .form-input::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

@media (max-width: 768px) {
    .search-input-group {
        flex-direction: column;
    }
    
    .hero-stats {
        justify-content: space-around;
    }
}
<?php $customCss = ob_get_clean(); ?>

<!-- Custom JavaScript for home page -->
<?php ob_start(); ?>
// Counter animation
function animateCounters() {
    const counters = document.querySelectorAll('[data-counter]');
    const options = {
        threshold: 0.5,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = parseInt(counter.dataset.counter);
                const duration = 2000;
                const step = target / (duration / 16);
                let current = 0;

                const timer = setInterval(() => {
                    current += step;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    counter.textContent = Math.floor(current).toLocaleString();
                }, 16);

                observer.unobserve(counter);
            }
        });
    }, options);

    counters.forEach(counter => observer.observe(counter));
}

// Initialize counter animation when DOM is ready
document.addEventListener('DOMContentLoaded', animateCounters);
<?php $customJs = ob_get_clean(); ?>

<?php require __DIR__ . '/layouts/main.php'; ?>