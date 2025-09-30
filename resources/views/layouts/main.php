<!DOCTYPE html>
<html lang="<?= config('app.locale', 'ru') ?>" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <meta name="theme-color" content="#1f3c88">

    <title><?= $title ?? config('app.name') ?></title>
    <meta name="description" content="<?= $description ?? 'Система недвижимости — покупка, продажа и аренда премиальной недвижимости в лучших районах города' ?>">

    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/components.css">

    <?php if (isset($styles)): ?>
        <?php foreach ($styles as $style): ?>
            <link rel="stylesheet" href="<?= $style ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (isset($customCss)): ?>
        <style><?= $customCss ?></style>
    <?php endif; ?>
</head>
<body>
    <div class="site-shell">
        <header class="site-header">
            <div class="top-bar">
                <div class="container top-bar__list">
                    <div class="top-bar__group">
                        <span class="top-bar__item">
                            <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true">
                                <path fill="currentColor" d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.57 17.57 0 0 0 4.168 6.608 17.57 17.57 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.405-.318l-.178-.139a14.6 14.6 0 0 1-3.403-3.403l-.139-.178a1.75 1.75 0 0 1-.318-1.405l.547-2.19a.678.678 0 0 0-.122-.58L3.654 1.328z"/>
                            </svg>
                            <a href="tel:+74951234567">+7 (495) 123-45-67</a>
                        </span>
                        <span class="top-bar__item">
                            <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true">
                                <path fill="currentColor" d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v.586l-8 4.8-8-4.8V4zm0 2.697v5.303A2 2 0 0 0 2 14h12a2 2 0 0 0 2-2V6.697l-7.555 4.53a1 1 0 0 1-1.05 0L0 6.697z"/>
                            </svg>
                            <a href="mailto:info@realestate.ru">info@realestate.ru</a>
                        </span>
                    </div>
                    <div class="top-bar__group">
                        <span class="top-bar__item">
                            <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true">
                                <path fill="currentColor" d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                                <path fill="currentColor" d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            </svg>
                            Пн-Пт 09:00–20:00
                        </span>
                        <div class="footer__social">
                            <a href="#" class="social-link" aria-label="Telegram">
                                <svg width="18" height="18" viewBox="0 0 16 16" aria-hidden="true">
                                    <path fill="currentColor" d="M16 1.5 13.7 14c-.2.9-.7 1.1-1.4.7l-4-3-1.9 1.9c-.2.2-.3.3-.6.3l.2-4.1 7.4-6.7c.3-.3-.1-.4-.4-.2l-9.2 5.8-4-.8c-.9-.2-.9-.9.2-1.3L15.1.2c.7-.2 1.3.1.9 1.3Z"/>
                                </svg>
                            </a>
                            <a href="#" class="social-link" aria-label="Instagram">
                                <svg width="18" height="18" viewBox="0 0 16 16" aria-hidden="true">
                                    <path fill="currentColor" d="M5 1h6a4 4 0 0 1 4 4v6a4 4 0 0 1-4 4H5a4 4 0 0 1-4-4V5a4 4 0 0 1 4-4Zm0 1.5A2.5 2.5 0 0 0 2.5 5v6A2.5 2.5 0 0 0 5 13.5h6a2.5 2.5 0 0 0 2.5-2.5V5A2.5 2.5 0 0 0 11 2.5H5Zm3 2.25A3.25 3.25 0 1 1 4.75 8 3.25 3.25 0 0 1 8 4.75Zm0 1.5A1.75 1.75 0 1 0 9.75 8 1.75 1.75 0 0 0 8 6.25Zm4.25-2.75a.75.75 0 1 1-.75.75.75.75 0 0 1 .75-.75Z"/>
                                </svg>
                            </a>
                            <a href="#" class="social-link" aria-label="VK">
                                <svg width="18" height="18" viewBox="0 0 16 16" aria-hidden="true">
                                    <path fill="currentColor" d="M1.5 3h2.4c.3 0 .5.2.6.4.5 1.2 1.1 2.3 1.9 3.4.3.5.7.9 1.1 1.3.2.2.5.2.7 0 .3-.4.3-.4.1-.8-.4-.8-.9-1.6-1.3-2.4-.2-.4 0-.7.4-.7h2.9c.3 0 .5.1.6.4.2.5.4 1 .7 1.5.5.9 1.1 1.7 1.8 2.4.2.2.4.3.6.1.2-.1.2-.4.1-.6-.4-.9-.8-1.7-1.3-2.5-.2-.3 0-.6.3-.6h2.5c.4 0 .6.2.6.6 0 .2 0 .4-.1.6-.6 1.5-1.5 2.9-2.5 4-.6.7-1.3 1.3-2.1 1.8-.8.4-1.6.5-2.4-.1-.5-.4-1-.9-1.5-1.3-.2-.2-.4-.2-.6 0-.6.6-1.3 1.3-2.1 1.7-.6.3-1.2.3-1.7-.2a14.9 14.9 0 0 1-3.5-5.5c-.5-1.2-.9-2.6-1-4 0-.5.1-.7.6-.7Z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <nav class="navbar">
                <div class="container">
                    <a href="/" class="navbar-brand">
                        <span class="brand-icon" aria-hidden="true">
                            <svg width="32" height="32" viewBox="0 0 24 24" role="presentation">
                                <path fill="currentColor" d="M4 9.5 11.7 3a.6.6 0 0 1 .7 0L20 9.5V21a1 1 0 0 1-1 1h-5.5v-6.25h-3V22H5a1 1 0 0 1-1-1V9.5Z"/>
                            </svg>
                        </span>
                        <span><?= config('app.name') ?></span>
                    </a>

                    <ul class="navbar-nav" data-nav-desktop>
                        <li><a href="/" class="nav-link <?= ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '') ? 'active' : '' ?>">Главная</a></li>
                        <li><a href="/properties" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/properties') ? 'active' : '' ?>">Объекты</a></li>
                        <li><a href="/services" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/services') ? 'active' : '' ?>">Услуги</a></li>
                        <li><a href="/about" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/about') ? 'active' : '' ?>">О компании</a></li>
                        <li><a href="/contact" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/contact') ? 'active' : '' ?>">Контакты</a></li>
                        <li><a href="/blog" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/blog') ? 'active' : '' ?>">Блог</a></li>
                    </ul>

                    <div class="nav-actions">
                        <a href="/comparison" class="btn btn-outline btn-sm nav-pill">
                            <span>Сравнить</span>
                            <span class="badge" data-compare-count style="display: none;">0</span>
                        </a>
                        <a href="/favorites" class="btn btn-outline btn-sm nav-pill">
                            <span>Избранное</span>
                            <span class="badge" data-favorites-count>0</span>
                        </a>
                        <button type="button" class="btn btn-icon" data-theme-toggle aria-label="Переключить тему">
                            <svg width="18" height="18" viewBox="0 0 16 16" aria-hidden="true">
                                <path fill="currentColor" d="M8 1a.75.75 0 0 1 .75.75V3a.75.75 0 0 1-1.5 0V1.75A.75.75 0 0 1 8 1Zm0 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm7-.75a.75.75 0 0 1-.75.75H13a.75.75 0 0 1 0-1.5h1.25a.75.75 0 0 1 .75.75Zm-2.47-7.53a.75.75 0 0 1 0 1.06l-.88.88a.75.75 0 0 1-1.06-1.06l.88-.88a.75.75 0 0 1 1.06 0ZM8 13a.75.75 0 0 1 .75.75V15a.75.75 0 0 1-1.5 0v-1.25A.75.75 0 0 1 8 13Zm-4.47-7.53a.75.75 0 0 1 1.06 0l.88.88a.75.75 0 1 1-1.06 1.06l-.88-.88a.75.75 0 0 1 0-1.06ZM3 8a.75.75 0 0 1-.75.75H1a.75.75 0 0 1 0-1.5h1.25A.75.75 0 0 1 3 8Zm1.47 3.53a.75.75 0 0 1 0 1.06l-.88.88a.75.75 0 1 1-1.06-1.06l.88-.88a.75.75 0 0 1 1.06 0Z"/>
                            </svg>
                        </button>

                        <?php if (auth()): ?>
                            <?php $currentUser = user(); ?>
                            <?php $initial = $currentUser && $currentUser->full_name ? mb_strtoupper(mb_substr($currentUser->full_name, 0, 1)) : 'U'; ?>
                            <details class="nav-user">
                                <summary>
                                    <span class="nav-user__avatar" aria-hidden="true"><?= e($initial) ?></span>
                                    <span class="nav-user__name"><?= e($currentUser->full_name ?? 'Пользователь') ?></span>
                                </summary>
                                <div class="nav-user__menu">
                                    <?php if ($currentUser && method_exists($currentUser, 'hasRole') && $currentUser->hasRole('admin')): ?>
                                        <a href="/admin" class="nav-user__link">Админ-панель</a>
                                    <?php endif; ?>
                                    <?php if ($currentUser && method_exists($currentUser, 'hasRole') && $currentUser->hasRole(['admin', 'realtor'])): ?>
                                        <a href="/dashboard" class="nav-user__link">Панель управления</a>
                                    <?php endif; ?>
                                    <a href="/profile" class="nav-user__link">Профиль</a>
                                    <form action="/auth/logout" method="POST" class="nav-user__link nav-user__logout">
                                        <?= csrf_field() ?>
                                        <button type="submit">Выйти</button>
                                    </form>
                                </div>
                            </details>
                        <?php else: ?>
                            <a href="/auth/login" class="btn btn-ghost btn-sm">Войти</a>
                            <a href="/auth/register" class="btn btn-primary btn-sm">Регистрация</a>
                        <?php endif; ?>

                        <button type="button" class="btn btn-icon nav-toggle" data-mobile-toggle aria-label="Открыть меню">
                            <svg width="18" height="18" viewBox="0 0 16 16" aria-hidden="true">
                                <path fill="currentColor" d="M1.5 3h13a.5.5 0 1 0 0-1h-13a.5.5 0 0 0 0 1Zm0 4.5h13a.5.5 0 0 0 0-1h-13a.5.5 0 0 0 0 1Zm0 4.5h13a.5.5 0 0 0 0-1h-13a.5.5 0 0 0 0 1Z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="mobile-menu" data-mobile-menu>
                    <ul class="navbar-nav">
                        <li><a href="/" class="nav-link">Главная</a></li>
                        <li><a href="/properties" class="nav-link">Объекты</a></li>
                        <li><a href="/services" class="nav-link">Услуги</a></li>
                        <li><a href="/about" class="nav-link">О компании</a></li>
                        <li><a href="/contact" class="nav-link">Контакты</a></li>
                        <li><a href="/blog" class="nav-link">Блог</a></li>
                    </ul>

                    <div class="mobile-menu__actions">
                        <a href="/comparison" class="btn btn-outline btn-sm">Сравнить объекты</a>
                        <a href="/favorites" class="btn btn-outline btn-sm">Избранное</a>

                        <?php if (auth()): ?>
                            <a href="/dashboard" class="btn btn-primary btn-sm">Личный кабинет</a>
                            <form action="/auth/logout" method="POST">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-ghost btn-sm">Выйти</button>
                            </form>
                        <?php else: ?>
                            <a href="/auth/login" class="btn btn-ghost btn-sm">Войти</a>
                            <a href="/auth/register" class="btn btn-primary btn-sm">Создать аккаунт</a>
                        <?php endif; ?>
                    </div>

                    <div class="mobile-menu__meta">
                        <p><strong>Телефон:</strong> +7 (495) 123-45-67</p>
                        <p><strong>Email:</strong> info@realestate.ru</p>
                    </div>
                </div>
            </nav>
        </header>

        <?php if ($flash = session('flash')): ?>
            <div class="alerts">
                <div class="container alerts-stack" data-alerts>
                    <?php foreach ($flash as $type => $messages): ?>
                        <?php foreach ((array)$messages as $message): ?>
                            <div class="alert alert-<?= $type ?>">
                                <?= e($message) ?>
                                <button type="button" class="alert-close" data-dismiss="alert" aria-label="Закрыть">&times;</button>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <main class="main-content">
            <?= $content ?? '' ?>
        </main>

        <footer class="footer">
            <div class="container">
                <div class="footer__top">
                    <div class="footer__brand">
                        <a href="/" class="navbar-brand text-inverse">
                            <span class="brand-icon" aria-hidden="true">
                                <svg width="28" height="28" viewBox="0 0 24 24" role="presentation">
                                    <path fill="currentColor" d="M4 9.5 11.7 3a.6.6 0 0 1 .7 0L20 9.5V21a1 1 0 0 1-1 1h-5.5v-6.25h-3V22H5a1 1 0 0 1-1-1V9.5Z"/>
                                </svg>
                            </span>
                            <span><?= config('app.name') ?></span>
                        </a>
                        <p>Премиальный сервис в сфере недвижимости: аналитика, сопровождение сделок и персональный подбор объектов.</p>
                    </div>

                    <div>
                        <h6 class="footer__title">Объекты</h6>
                        <ul class="footer__list">
                            <li><a href="/properties?type=sale">Купить квартиру</a></li>
                            <li><a href="/properties?type=rent">Снять жильё</a></li>
                            <li><a href="/properties?category=house">Загородные дома</a></li>
                            <li><a href="/properties?category=commercial">Коммерческие помещения</a></li>
                        </ul>
                    </div>

                    <div>
                        <h6 class="footer__title">Компания</h6>
                        <ul class="footer__list">
                            <li><a href="/about">О нас</a></li>
                            <li><a href="/services">Услуги</a></li>
                            <li><a href="/blog">Новости и аналитика</a></li>
                            <li><a href="/contact">Контакты</a></li>
                        </ul>
                    </div>

                    <div>
                        <h6 class="footer__title">Контакты</h6>
                        <ul class="footer__list">
                            <li>+7 (495) 123-45-67</li>
                            <li>info@realestate.ru</li>
                            <li>Москва, ул. Примерная, 123</li>
                        </ul>
                    </div>
                </div>

                <div class="footer__bottom">
                    <p>&copy; <?= date('Y') ?> <?= config('app.name') ?>. Все права защищены.</p>
                    <div class="footer__links">
                        <a href="/privacy">Политика конфиденциальности</a>
                        <span>•</span>
                        <a href="/terms">Пользовательское соглашение</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        window.AppConfig = {
            baseUrl: '<?= url('/') ?>',
            csrfToken: '<?= csrf_token() ?>',
            locale: '<?= config('app.locale') ?>',
            user: <?= (auth() && user()) ? json_encode([
                'id' => user()->id,
                'name' => user()->full_name ?? 'Пользователь',
                'role' => user()->role ?? 'guest'
            ]) : 'null' ?>
        };
    </script>

    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/components.js"></script>

    <?php if (isset($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (isset($customJs)): ?>
        <script><?= $customJs ?></script>
    <?php endif; ?>
</body>
</html>