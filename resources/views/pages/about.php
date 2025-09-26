<?php $title = 'О компании - ' . config('app.name'); ?>

<?php ob_start(); ?>

<!-- Hero Section -->
<section class="hero bg-primary text-inverse py-2xl">
    <div class="container text-center">
        <h1 class="display-4 mb-md">О нашей компании</h1>
        <p class="lead">
            Более 15 лет на рынке недвижимости. Профессиональный подход к каждому клиенту.
        </p>
    </div>
</section>

<!-- Main Content -->
<section class="py-2xl">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <h2>Наша история</h2>
                <p class="lead">
                    Компания «<?= config('app.name') ?>» была основана в 2008 году с целью предоставления 
                    качественных услуг в сфере недвижимости.
                </p>
                
                <p>
                    За годы работы мы помогли тысячам семей найти идеальное жильё и совершить 
                    успешные сделки с недвижимостью. Наша команда состоит из опытных риелторов, 
                    юристов и оценщиков, которые обеспечивают профессиональное сопровождение 
                    на всех этапах сделки.
                </p>

                <h3>Наши принципы</h3>
                <ul>
                    <li><strong>Профессионализм</strong> — высокий уровень экспертизы в области недвижимости</li>
                    <li><strong>Прозрачность</strong> — честная работа без скрытых комиссий</li>
                    <li><strong>Индивидуальный подход</strong> — учитываем потребности каждого клиента</li>
                    <li><strong>Результат</strong> — гарантируем достижение поставленных целей</li>
                </ul>

                <h3>Лицензии и сертификаты</h3>
                <p>
                    Компания имеет все необходимые лицензии для осуществления деятельности в сфере 
                    недвижимости. Наши специалисты регулярно повышают квалификацию и проходят 
                    сертификацию в ведущих образовательных центрах.
                </p>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Ключевые цифры</h5>
                    </div>
                    <div class="card-body">
                        <div class="stat-item mb-lg">
                            <div class="stat-number text-primary">15+</div>
                            <div class="stat-label">лет на рынке</div>
                        </div>
                        
                        <div class="stat-item mb-lg">
                            <div class="stat-number text-primary">10 000+</div>
                            <div class="stat-label">объектов недвижимости</div>
                        </div>
                        
                        <div class="stat-item mb-lg">
                            <div class="stat-number text-primary">2 500+</div>
                            <div class="stat-label">успешных сделок</div>
                        </div>
                        
                        <div class="stat-item mb-lg">
                            <div class="stat-number text-primary">50+</div>
                            <div class="stat-label">опытных риелторов</div>
                        </div>
                        
                        <div class="stat-item">
                            <div class="stat-number text-primary">98%</div>
                            <div class="stat-label">довольных клиентов</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="bg-secondary py-2xl">
    <div class="container">
        <div class="text-center mb-xl">
            <h2>Наша команда</h2>
            <p class="lead text-secondary">Профессионалы с многолетним опытом</p>
        </div>
        
        <div class="row">
            <?php for ($i = 1; $i <= 4; $i++): ?>
                <div class="col-md-3 col-sm-6 mb-lg text-center">
                    <div class="team-member">
                        <div class="team-avatar mb-md">
                            <img src="https://ui-avatars.com/api/?name=Сотрудник+<?= $i ?>&background=6366f1&color=fff&size=150" 
                                 alt="Сотрудник <?= $i ?>" 
                                 class="rounded-circle" 
                                 width="150" height="150">
                        </div>
                        <h5><?= ['Анна Петрова', 'Михаил Сидоров', 'Елена Козлова', 'Дмитрий Новиков'][$i-1] ?></h5>
                        <p class="text-secondary"><?= ['Директор', 'Ведущий риелтор', 'Юрист', 'Оценщик'][$i-1] ?></p>
                        <p class="text-sm">
                            <?= ['Опыт работы более 15 лет', 'Специалист по премиум-недвижимости', 'Эксперт по сделкам', 'Сертифицированный оценщик'][$i-1] ?>
                        </p>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</section>

<!-- Contact CTA -->
<section class="py-xl">
    <div class="container text-center">
        <h2>Готовы начать сотрудничество?</h2>
        <p class="lead mb-lg">Свяжитесь с нами для бесплатной консультации</p>
        
        <div class="d-flex justify-center gap-md">
            <a href="/contact" class="btn btn-primary btn-lg">Связаться с нами</a>
            <a href="/services" class="btn btn-outline btn-lg">Наши услуги</a>
        </div>
    </div>
</section>

<?php $content = ob_get_clean(); ?>

<!-- Custom styles -->
<?php ob_start(); ?>
.team-member {
    transition: transform var(--transition-normal);
}

.team-member:hover {
    transform: translateY(-5px);
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: var(--text-3xl);
    font-weight: 700;
    display: block;
}

.stat-label {
    font-size: var(--text-sm);
    color: var(--text-secondary);
}
<?php $customCss = ob_get_clean(); ?>

<?php require __DIR__ . '/../layouts/main.php'; ?>