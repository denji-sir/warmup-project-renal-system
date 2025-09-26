<?php $title = 'Личный кабинет'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Добро пожаловать, <?= htmlspecialchars($user['username'] ?? $user['email']) ?>!</h1>
                <form action="/logout" method="POST" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="fas fa-sign-out-alt"></i> Выйти
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-user"></i> Информация о пользователе</h5>
                </div>
                <div class="card-body">
                    <p><strong>ID:</strong> <?= $user['id'] ?></p>
                    <p><strong>Имя пользователя:</strong> <?= htmlspecialchars($user['username'] ?? 'Не указано') ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    <p><strong>Роль:</strong> 
                        <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'manager' ? 'warning' : 'primary') ?>">
                            <?= ucfirst($user['role'] ?? 'tenant') ?>
                        </span>
                    </p>
                    <p><strong>Email подтвержден:</strong> 
                        <?php if ($user['email_verified_at']): ?>
                            <span class="badge bg-success">Да</span>
                        <?php else: ?>
                            <span class="badge bg-warning">Нет</span>
                        <?php endif; ?>
                    </p>
                    <p><strong>Зарегистрирован:</strong> <?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-tachometer-alt"></i> Панель управления</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php if (hasRole('admin') || hasRole('manager')): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5>Управление пользователями</h5>
                                    <p>Просмотр и редактирование пользователей</p>
                                    <a href="/admin/users" class="btn btn-light">Перейти</a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="col-md-6 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5>Мои аренды</h5>
                                    <p>Просмотр текущих и прошлых аренд</p>
                                    <a href="/rentals" class="btn btn-light">Перейти</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5>Объекты недвижимости</h5>
                                    <p>Просмотр доступных объектов</p>
                                    <a href="/properties" class="btn btn-light">Перейти</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5>Платежи</h5>
                                    <p>История платежей и счета</p>
                                    <a href="/payments" class="btn btn-light">Перейти</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!$user['email_verified_at']): ?>
            <div class="alert alert-warning mt-3">
                <h6><i class="fas fa-exclamation-triangle"></i> Требуется подтверждение email</h6>
                <p>Для полного доступа к системе необходимо подтвердить ваш email адрес.</p>
                <form action="/resend-verification" method="POST" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-envelope"></i> Отправить письмо повторно
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-clock"></i> Последняя активность</h5>
                </div>
                <div class="card-body">
                    <p><strong>Последний вход:</strong> <?= date('d.m.Y H:i', strtotime($user['last_login_at'] ?? $user['created_at'])) ?></p>
                    <p><strong>IP-адрес:</strong> <?= $_SERVER['REMOTE_ADDR'] ?></p>
                    <p><strong>Браузер:</strong> <?= htmlspecialchars($_SERVER['HTTP_USER_AGENT']) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    margin-bottom: 1rem;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.bg-primary { background-color: #0d6efd !important; }
.bg-success { background-color: #198754 !important; }
.bg-info { background-color: #0dcaf0 !important; }
.bg-warning { background-color: #ffc107 !important; }
.bg-danger { background-color: #dc3545 !important; }
</style>