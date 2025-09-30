<?php $title = 'О компании — ' . config('app.name'); ?>

<?php ob_start(); ?>

<section class="section section--muted">
    <div class="container">
        <div class="section__header">
            <span class="eyebrow">15 лет в недвижимости</span>
            <h1 class="section__title">Мы создаём для клиентов новые сценарии жизни</h1>
            <p class="section__subtitle section__subtitle--muted">
                <?= config('app.name') ?> помогает людям и бизнесу находить пространства, которые вдохновляют и работают на их цели.
            </p>
        </div>
        <div class="split-grid">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h3>История и ценности</h3>
                    <p>
                        Компания была основана в 2008 году командой брокеров, которые хотели сделать рынок недвижимости прозрачным и комфортным.
                        Сегодня мы сопровождаем сделки любой сложности: от покупки квартиры в новостройке до продажи доходного бизнес-центра.
                    </p>
                    <p>
                        В портфеле — более 10 000 объектов и 2 500 успешно закрытых сделок. Мы работаем на доверии, аналитике и долгосрочных отношениях.
                    </p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="stat-grid">
                        <div class="stat-card">
                            <div class="stat-card__value">15+</div>
                            <div class="stat-card__label">Лет на рынке</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card__value">98%</div>
                            <div class="stat-card__label">Клиентов рекомендуют нас</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card__value">50+</div>
                            <div class="stat-card__label">Экспертов в команде</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card__value">24/7</div>
                            <div class="stat-card__label">Поддержка клиентов</div>
                        </div>
                    </div>
                    <div class="timeline mt-lg">
                        <div class="timeline__item">
                            <strong>2008</strong>
                            <p>Запустили первую консультационную службу по премиальной недвижимости.</p>
                        </div>
                        <div class="timeline__item">
                            <strong>2014</strong>
                            <p>Открыли отдел инвестиций и запустили сервис аналитики доходности объектов.</p>
                        </div>
                        <div class="timeline__item">
                            <strong>2021</strong>
                            <p>Вышли на международный рынок и создали архитектурное бюро для клиентов.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section__header">
            <span class="eyebrow">Что нас отличает</span>
            <h2 class="section__title">Стандарты работы команды</h2>
        </div>
        <div class="feature-grid">
            <?php
            $values = [
                ['title' => 'Прозрачность', 'text' => 'Каждый шаг сделки фиксирован, вся информация по объекту и рискам доступна клиенту.'],
                ['title' => 'Индивидуальность', 'text' => 'Создаём решения под конкретный запрос: личное жильё, инвестиции, коммерция.'],
                ['title' => 'Технологичность', 'text' => 'Используем аналитические отчёты, VR-показы и диджитал-досье на объекты.'],
            ];
            foreach ($values as $value): ?>
                <article class="feature-card">
                    <h3><?= e($value['title']) ?></h3>
                    <p><?= e($value['text']) ?></p>
                    <div class="pill-list">
                        <span class="pill">Экспертиза</span>
                        <span class="pill">Ответственность</span>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--muted">
    <div class="container">
        <div class="section__header">
            <span class="eyebrow">Команда</span>
            <h2 class="section__title">Люди, которые создают ваш результат</h2>
        </div>
        <div class="team-grid">
            <?php
            $team = [
                ['name' => 'Анна Петрова', 'role' => 'Управляющий партнёр', 'bio' => '15 лет на рынке. Специализация — премиальные объекты в Москве и Санкт-Петербурге.', 'img' => 'https://ui-avatars.com/api/?name=AP&background=1f3c88&color=fff'],
                ['name' => 'Михаил Сидоров', 'role' => 'Руководитель брокерского отдела', 'bio' => 'Знает каждую новостройку бизнес-класса. Эксперт по переговорам.', 'img' => 'https://ui-avatars.com/api/?name=MS&background=0ea5e9&color=fff'],
                ['name' => 'Елена Козлова', 'role' => 'Главный юрист', 'bio' => '20+ лет в недвижимости. Гарантия чистоты сделки и безопасности инвестиций.', 'img' => 'https://ui-avatars.com/api/?name=EK&background=f97316&color=fff'],
                ['name' => 'Дмитрий Новиков', 'role' => 'Head of Investments', 'bio' => 'Создаёт инвестиционные стратегии с доходностью до 18% годовых.', 'img' => 'https://ui-avatars.com/api/?name=DN&background=1f3c88&color=fff'],
            ];
            foreach ($team as $member): ?>
                <article class="team-card">
                    <img src="<?= $member['img'] ?>" alt="<?= e($member['name']) ?>">
                    <h4><?= e($member['name']) ?></h4>
                    <p class="team-card__role"><?= e($member['role']) ?></p>
                    <p><?= e($member['bio']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--compact">
    <div class="container">
        <div class="cta-panel">
            <div>
                <h2 class="section__title text-inverse">Хотите обсудить проект?</h2>
                <p class="section__subtitle">Назначим встречу в удобном формате, подготовим аналитику рынка и подборку объектов.</p>
            </div>
            <div class="cta-panel__actions">
                <a href="/contact" class="btn btn-outline-light btn-lg">Связаться с нами</a>
                <a href="/services" class="btn btn-primary btn-lg">Посмотреть услуги</a>
            </div>
        </div>
    </div>
</section>

<?php $content = ob_get_clean(); ?>

<?php require __DIR__ . '/../layouts/main.php'; ?>
