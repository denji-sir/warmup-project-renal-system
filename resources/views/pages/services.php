<?php $title = 'Наши услуги - ' . config('app.name'); ?>

<?php ob_start(); ?>

<!-- Hero Section -->
<section class="hero bg-primary text-inverse py-2xl">
    <div class="container text-center">
        <h1 class="display-4 mb-md">Наши услуги</h1>
        <p class="lead">
            Полный спектр услуг в сфере недвижимости для частных лиц и бизнеса
        </p>
    </div>
</section>

<!-- Services Grid -->
<section class="py-2xl">
    <div class="container">
        <div class="row">
            <?php foreach ($services as $index => $service): ?>
                <div class="col-md-4 mb-xl">
                    <div class="service-card card h-100">
                        <div class="card-header">
                            <div class="service-icon">
                                <?php 
                                $icons = [
                                    '<svg width="48" height="48" fill="currentColor" viewBox="0 0 16 16"><path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.707 1.5Z"/></svg>',
                                    '<svg width="48" height="48" fill="currentColor" viewBox="0 0 16 16"><path d="M2.5 3A1.5 1.5 0 0 0 1 4.5V6a.5.5 0 0 0 .5.5 1.5 1.5 0 1 1 0 3 .5.5 0 0 0-.5.5v1.5A1.5 1.5 0 0 0 2.5 13h11a1.5 1.5 0 0 0 1.5-1.5V10a.5.5 0 0 0-.5-.5 1.5 1.5 0 0 1 0-3A.5.5 0 0 0 15 6V4.5A1.5 1.5 0 0 0 13.5 3h-11z"/></svg>',
                                    '<svg width="48" height="48" fill="currentColor" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v1H0V4zM0 7v5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7H0zm3 2h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-1a1 1 0 0 1 1-1z"/></svg>'
                                ];
                                echo $icons[$index] ?? $icons[0];
                                ?>
                            </div>
                            <h4><?= e($service['title']) ?></h4>
                        </div>
                        
                        <div class="card-body">
                            <p class="text-secondary mb-lg">
                                <?= e($service['description']) ?>
                            </p>
                            
                            <h6>Что включено:</h6>
                            <ul class="service-features">
                                <?php foreach ($service['features'] as $feature): ?>
                                    <li>
                                        <svg width="16" height="16" fill="var(--color-success)" viewBox="0 0 16 16">
                                            <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                                        </svg>
                                        <?= e($feature) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        
                        <div class="card-footer">
                            <a href="/contact?service=<?= urlencode(strtolower($service['title'])) ?>" 
                               class="btn btn-primary btn-block">
                                Получить консультацию
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Additional Services -->
<section class="bg-secondary py-xl">
    <div class="container">
        <div class="text-center mb-xl">
            <h2>Дополнительные услуги</h2>
            <p class="lead text-secondary">Полное сопровождение сделок с недвижимостью</p>
        </div>
        
        <div class="row">
            <div class="col-md-3 col-sm-6 text-center mb-lg">
                <div class="additional-service">
                    <div class="service-icon-small mb-md">
                        <svg width="32" height="32" fill="var(--color-primary)" viewBox="0 0 16 16">
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                        </svg>
                    </div>
                    <h6>Юридическое сопровождение</h6>
                    <p class="text-sm text-secondary">
                        Проверка документов, составление договоров
                    </p>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6 text-center mb-lg">
                <div class="additional-service">
                    <div class="service-icon-small mb-md">
                        <svg width="32" height="32" fill="var(--color-primary)" viewBox="0 0 16 16">
                            <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                        </svg>
                    </div>
                    <h6>Ипотечное консультирование</h6>
                    <p class="text-sm text-secondary">
                        Подбор программ, помощь в оформлении
                    </p>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6 text-center mb-lg">
                <div class="additional-service">
                    <div class="service-icon-small mb-md">
                        <svg width="32" height="32" fill="var(--color-primary)" viewBox="0 0 16 16">
                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                        </svg>
                    </div>
                    <h6>Дизайн и ремонт</h6>
                    <p class="text-sm text-secondary">
                        Консультации по планировке и отделке
                    </p>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6 text-center mb-lg">
                <div class="additional-service">
                    <div class="service-icon-small mb-md">
                        <svg width="32" height="32" fill="var(--color-primary)" viewBox="0 0 16 16">
                            <path d="M0 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2h2a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2H2a2 2 0 0 1-2-2V2zm5.5 10a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1zM7 6a1 1 0 0 0-2 0v5a1 1 0 0 0 2 0V6zM4.5 0a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1h-3z"/>
                        </svg>
                    </div>
                    <h6>Страхование недвижимости</h6>
                    <p class="text-sm text-secondary">
                        Защита вашей собственности
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Process Section -->
<section class="py-2xl">
    <div class="container">
        <div class="text-center mb-xl">
            <h2>Как мы работаем</h2>
            <p class="lead text-secondary">Простой и понятный процесс</p>
        </div>
        
        <div class="row">
            <div class="col-md-3 text-center mb-lg">
                <div class="process-step">
                    <div class="step-number">1</div>
                    <h5>Консультация</h5>
                    <p class="text-secondary">
                        Обсуждаем ваши потребности и бюджет
                    </p>
                </div>
            </div>
            
            <div class="col-md-3 text-center mb-lg">
                <div class="process-step">
                    <div class="step-number">2</div>
                    <h5>Подбор вариантов</h5>
                    <p class="text-secondary">
                        Находим подходящие объекты
                    </p>
                </div>
            </div>
            
            <div class="col-md-3 text-center mb-lg">
                <div class="process-step">
                    <div class="step-number">3</div>
                    <h5>Просмотры</h5>
                    <p class="text-secondary">
                        Организуем показы недвижимости
                    </p>
                </div>
            </div>
            
            <div class="col-md-3 text-center mb-lg">
                <div class="process-step">
                    <div class="step-number">4</div>
                    <h5>Оформление сделки</h5>
                    <p class="text-secondary">
                        Сопровождаем до завершения
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="bg-primary text-inverse py-xl">
    <div class="container text-center">
        <h2>Нужна консультация?</h2>
        <p class="lead mb-lg">Наши эксперты ответят на все ваши вопросы</p>
        
        <div class="d-flex justify-center gap-md">
            <a href="/contact" class="btn btn-warning btn-lg">Связаться с нами</a>
            <a href="tel:+74951234567" class="btn btn-outline-light btn-lg">
                +7 (495) 123-45-67
            </a>
        </div>
    </div>
</section>

<?php $content = ob_get_clean(); ?>

<!-- Custom styles -->
<?php ob_start(); ?>
.service-card {
    transition: transform var(--transition-normal), box-shadow var(--transition-normal);
    border: none;
    box-shadow: var(--shadow-sm);
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.service-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 80px;
    height: 80px;
    background-color: rgba(37, 99, 235, 0.1);
    color: var(--color-primary);
    border-radius: 50%;
    margin: 0 auto var(--space-md);
}

.service-features {
    list-style: none;
    padding: 0;
}

.service-features li {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    margin-bottom: var(--space-sm);
    font-size: var(--text-sm);
}

.additional-service {
    padding: var(--space-lg);
    background-color: var(--bg-primary);
    border-radius: var(--radius-lg);
    transition: transform var(--transition-fast);
}

.additional-service:hover {
    transform: translateY(-3px);
}

.service-icon-small {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    background-color: rgba(37, 99, 235, 0.1);
    border-radius: 50%;
    margin: 0 auto;
}

.process-step {
    position: relative;
}

.step-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    background-color: var(--color-primary);
    color: var(--text-inverse);
    font-size: var(--text-xl);
    font-weight: 700;
    border-radius: 50%;
    margin-bottom: var(--space-md);
}

@media (max-width: 768px) {
    .service-card {
        margin-bottom: var(--space-lg);
    }
    
    .additional-service {
        padding: var(--space-md);
    }
}
<?php $customCss = ob_get_clean(); ?>

<?php require __DIR__ . '/../layouts/main.php'; ?>