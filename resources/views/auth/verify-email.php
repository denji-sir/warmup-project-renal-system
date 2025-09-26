<?php $title = 'Подтвердите email - ' . config('app.name'); ?>

<?php ob_start(); ?>

<div class="container">
    <div class="row justify-center mt-xl">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-header text-center">
                    <h2 class="mb-0">Подтвердите ваш email</h2>
                </div>
                
                <div class="card-body">
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success mb-lg">
                            <p><?= htmlspecialchars($success) ?></p>
                        </div>
                    <?php else: ?>
                        <div class="text-center mb-lg">
                            <div class="icon-circle mb-lg">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                            </div>
                            <p class="text-secondary">
                                Мы отправили письмо с подтверждением на адрес:<br>
                                <strong><?= htmlspecialchars($email ?? '') ?></strong>
                            </p>
                            <p class="text-secondary text-sm">
                                Перейдите по ссылке в письме, чтобы подтвердить ваш email адрес.
                            </p>
                        </div>

                        <form action="/auth/resend-verification" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                            <input type="hidden" name="email" value="<?= htmlspecialchars($email ?? '') ?>">
                            
                            <button type="submit" class="btn btn-outline btn-block">
                                Отправить письмо повторно
                            </button>
                        </form>
                    <?php endif; ?>
                    
                    <hr class="mt-lg mb-lg">
                    
                    <div class="text-center">
                        <a href="/" class="btn btn-primary">
                            Перейти на главную
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php require __DIR__ . '/../layouts/main.php'; ?>