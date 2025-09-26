<!DOCTYPE html>
<html lang="<?= config('app.locale', 'ru') ?>" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    
    <title><?= $title ?? config('app.name') ?></title>
    <meta name="description" content="<?= $description ?? 'Система недвижимости - продажа и аренда квартир, домов, коммерческой недвижимости' ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    
    <!-- Additional CSS -->
    <?php if (isset($styles)): ?>
        <?php foreach ($styles as $style): ?>
            <link rel="stylesheet" href="<?= $style ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Custom styles -->
    <?php if (isset($customCss)): ?>
        <style><?= $customCss ?></style>
    <?php endif; ?>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="d-flex justify-between items-center">
                <!-- Logo -->
                <a href="/" class="navbar-brand">
                    <?= config('app.name') ?>
                </a>
                
                <!-- Main Navigation -->
                <ul class="navbar-nav d-none d-md-flex">
                    <li><a href="/" class="nav-link <?= ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '') ? 'active' : '' ?>">Главная</a></li>
                    <li><a href="/properties" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/properties') ? 'active' : '' ?>">Недвижимость</a></li>
                    <li><a href="/properties?type=sale" class="nav-link">Купить</a></li>
                    <li><a href="/properties?type=rent" class="nav-link">Снять</a></li>
                    <li><a href="/blog" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/blog') ? 'active' : '' ?>">Блог</a></li>
                    <li><a href="/contact" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/contact') ? 'active' : '' ?>">Контакты</a></li>
                </ul>
                
                <!-- User Actions -->
                <div class="d-flex items-center gap-md">
                    <!-- Comparison Counter -->
                    <a href="/comparison" class="btn btn-outline btn-sm position-relative">
                        Сравнить
                        <span class="badge" data-compare-count style="display: none;">0</span>
                    </a>
                    
                    <!-- Favorites Counter -->
                    <a href="/favorites" class="btn btn-outline btn-sm position-relative">
                        Избранное
                        <span class="badge" data-favorites-count>0</span>
                    </a>
                    
                    <!-- Theme Toggle -->
                    <button type="button" class="btn btn-outline btn-sm" data-theme-toggle aria-label="Переключить тему">
                        🌙
                    </button>
                    
                    <!-- Authentication -->
                    <?php if (auth()): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline btn-sm dropdown-toggle" data-dropdown-toggle>
                                <?= user()->full_name ?>
                            </button>
                            <div class="dropdown-menu">
                                <?php if (user()->hasRole('admin')): ?>
                                    <a href="/admin" class="dropdown-item">Админ-панель</a>
                                <?php endif; ?>
                                <?php if (user()->hasRole(['admin', 'realtor'])): ?>
                                    <a href="/dashboard" class="dropdown-item">Панель управления</a>
                                <?php endif; ?>
                                <a href="/profile" class="dropdown-item">Профиль</a>
                                <div class="dropdown-divider"></div>
                                <form action="/auth/logout" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="dropdown-item">Выйти</button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="/auth/login" class="btn btn-outline btn-sm">Войти</a>
                        <a href="/auth/register" class="btn btn-primary btn-sm">Регистрация</a>
                    <?php endif; ?>
                    
                    <!-- Mobile Menu Toggle -->
                    <button type="button" class="btn btn-outline btn-sm d-md-none" data-mobile-toggle aria-label="Открыть меню">
                        ☰
                    </button>
                </div>
            </div>
            
            <!-- Mobile Navigation -->
            <div class="mobile-menu d-md-none" data-mobile-menu>
                <ul class="navbar-nav">
                    <li><a href="/" class="nav-link">Главная</a></li>
                    <li><a href="/properties" class="nav-link">Недвижимость</a></li>
                    <li><a href="/properties?type=sale" class="nav-link">Купить</a></li>
                    <li><a href="/properties?type=rent" class="nav-link">Снять</a></li>
                    <li><a href="/blog" class="nav-link">Блог</a></li>
                    <li><a href="/contact" class="nav-link">Контакты</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Flash Messages -->
    <?php if ($flash = session('flash')): ?>
        <div data-alerts>
            <?php foreach ($flash as $type => $messages): ?>
                <?php foreach ((array)$messages as $message): ?>
                    <div class="alert alert-<?= $type ?>">
                        <?= e($message) ?>
                        <button type="button" class="alert-close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- Page Content -->
    <main class="main-content">
        <?= $content ?? '' ?>
    </main>
    
    <!-- Footer -->
    <footer class="footer bg-secondary text-inverse mt-xl">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h5><?= config('app.name') ?></h5>
                    <p>Ваш надежный партнер в мире недвижимости. Помогаем найти идеальный дом для жизни и инвестиций.</p>
                </div>
                
                <div class="col-md-3">
                    <h6>Недвижимость</h6>
                    <ul class="list-unstyled">
                        <li><a href="/properties?type=sale">Купить квартиру</a></li>
                        <li><a href="/properties?type=rent">Снять квартиру</a></li>
                        <li><a href="/properties?category=commercial">Коммерческая</a></li>
                        <li><a href="/properties?category=house">Загородная</a></li>
                    </ul>
                </div>
                
                <div class="col-md-3">
                    <h6>Компания</h6>
                    <ul class="list-unstyled">
                        <li><a href="/about">О нас</a></li>
                        <li><a href="/services">Услуги</a></li>
                        <li><a href="/blog">Новости</a></li>
                        <li><a href="/contact">Контакты</a></li>
                    </ul>
                </div>
                
                <div class="col-md-3">
                    <h6>Контакты</h6>
                    <p>
                        <strong>Телефон:</strong><br>
                        +7 (495) 123-45-67
                    </p>
                    <p>
                        <strong>Email:</strong><br>
                        info@realestate.ru
                    </p>
                    <p>
                        <strong>Адрес:</strong><br>
                        Москва, ул. Примерная, 123
                    </p>
                </div>
            </div>
            
            <hr class="mt-lg mb-lg">
            
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?= date('Y') ?> <?= config('app.name') ?>. Все права защищены.</p>
                </div>
                <div class="col-md-6 text-right">
                    <p>
                        <a href="/privacy">Политика конфиденциальности</a> |
                        <a href="/terms">Пользовательское соглашение</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script>
        // Global configuration
        window.AppConfig = {
            baseUrl: '<?= url('/') ?>',
            csrfToken: '<?= csrf_token() ?>',
            locale: '<?= config('app.locale') ?>',
            user: <?= auth() ? json_encode([
                'id' => user()->id,
                'name' => user()->full_name,
                'role' => user()->role
            ]) : 'null' ?>
        };
    </script>
    
    <!-- Core JavaScript -->
    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/components.js"></script>
    
    <!-- Additional JavaScript -->
    <?php if (isset($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Custom JavaScript -->
    <?php if (isset($customJs)): ?>
        <script><?= $customJs ?></script>
    <?php endif; ?>
</body>
</html>