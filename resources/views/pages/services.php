<?php $title = 'Услуги — ' . config('app.name'); ?>

<?php ob_start(); ?>

<section class="section section--muted">
    <div class="container">
        <div class="section__header">
            <span class="eyebrow">Полный цикл услуг</span>
            <h1 class="section__title">Решения для покупателей, продавцов и инвесторов</h1>
            <p class="section__subtitle section__subtitle--muted">
                Мы объединяем аналитику рынка, юридическую экспертизу и персональный подход, чтобы каждая сделка проходила безопасно и прозрачно.
            </p>
        </div>
        <div class="feature-grid">
            <?php foreach ($services as $service): ?>
                <article class="feature-card">
                    <span class="feature-card__icon">
                        <svg width="24" height="24" viewBox="0 0 16 16" aria-hidden="true">
                            <path fill="currentColor" d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708Z"/>
                        </svg>
                    </span>
                    <h3><?= e($service['title']) ?></h3>
                    <p><?= e($service['description']) ?></p>
                    <ul class="service-features">
                        <?php foreach ($service['features'] as $feature): ?>
                            <li><?= e($feature) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="/contact?service=<?= urlencode(strtolower($service['title'])) ?>" class="btn btn-outline btn-sm">Получить консультацию</a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section__header">
            <span class="eyebrow">Дополнительные возможности</span>
            <h2 class="section__title">Сервис, который закрывает все вопросы</h2>
        </div>
        <div class="feature-grid">
            <?php
            $addons = [
                ['title' => 'Юридическое сопровождение', 'text' => 'Полная проверка и подготовка договоров, защита интересов клиента.'],
                ['title' => 'Инвестиционный консалтинг', 'text' => 'Расчёт доходности, подбор стратегий, анализ налоговой нагрузки.'],
                ['title' => 'Дизайн и строительство', 'text' => 'Концепция интерьера, подбор подрядчиков, контроль строительных работ.'],
                ['title' => 'Управление недвижимостью', 'text' => 'Аренда, сервис для арендаторов, отчётность и сервис 24/7.'],
            ];
            foreach ($addons as $addon): ?>
                <article class="feature-card">
                    <h3><?= e($addon['title']) ?></h3>
                    <p><?= e($addon['text']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--muted">
    <div class="container">
        <div class="section__header">
            <span class="eyebrow">Как мы работаем</span>
            <h2 class="section__title">Четкая и прозрачная методология</h2>
        </div>
        <div class="process-grid">
            <?php
            $steps = [
                ['title' => 'Диагностика', 'text' => 'Выявляем цели, бюджет и собираем критерии.'],
                ['title' => 'Подбор и просмотры', 'text' => 'Формируем shortlist, организуем показы и переговоры.'],
                ['title' => 'Структурирование сделки', 'text' => 'Проверяем объект, готовим договор и схему расчётов.'],
                ['title' => 'Сопровождение после сделки', 'text' => 'Помогаем с ремонтами, сдачей в аренду и управлением.'],
            ];
            $counter = 1;
            foreach ($steps as $step): ?>
                <article class="process-step">
                    <span class="process-step__number">0<?= $counter ?></span>
                    <h3><?= e($step['title']) ?></h3>
                    <p><?= e($step['text']) ?></p>
                </article>
            <?php $counter++; endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--dark">
    <div class="container">
        <div class="cta-panel">
            <div>
                <h2 class="section__title">Нужна стратегия по недвижимости?</h2>
                <p class="section__subtitle">Мы подготовим аналитику рынка и подборку объектов, соответствующих вашим задачам.</p>
            </div>
            <div class="cta-panel__actions">
                <a href="/contact" class="btn btn-outline-light btn-lg">Запланировать звонок</a>
                <a href="tel:+74951234567" class="btn btn-primary btn-lg">+7 (495) 123-45-67</a>
            </div>
        </div>
    </div>
</section>

<?php $content = ob_get_clean(); ?>

<?php require __DIR__ . '/../layouts/main.php'; ?>
