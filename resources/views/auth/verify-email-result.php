<?php $title = ($success ? 'Email подтвержден' : 'Ошибка подтверждения') . ' - ' . config('app.name'); ?>

<?php ob_start(); ?>

<div class="container">
    <div class="row justify-center mt-xl">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-header text-center">
                    <h2 class="mb-0">
                        <?= $success ? 'Email подтвержден!' : 'Ошибка подтверждения' ?>
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="text-center">
                        <div class="icon-circle mb-lg <?= $success ? 'success' : 'error' ?>">
                            <?php if ($success): ?>
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22,4 12,14.01 9,11.01"></polyline>
                                </svg>
                            <?php else: ?>
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                            <?php endif; ?>
                        </div>
                        
                        <p class="mb-lg">
                            <?= htmlspecialchars($message) ?>
                        </p>
                        
                        <?php if ($success && isset($redirect_url)): ?>
                            <a href="<?= htmlspecialchars($redirect_url) ?>" class="btn btn-primary btn-lg">
                                Перейти в личный кабинет
                            </a>
                        <?php elseif ($success): ?>
                            <a href="/dashboard" class="btn btn-primary btn-lg">
                                Перейти в личный кабинет
                            </a>
                        <?php else: ?>
                            <a href="/auth/login" class="btn btn-primary btn-lg">
                                Перейти ко входу
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.icon-circle.success {
    background: #d1fae5;
    color: #10b981;
}

.icon-circle.error {
    background: #fee2e2;
    color: #ef4444;
}
</style>

<?php $content = ob_get_clean(); ?>

<?php require __DIR__ . '/../layouts/main.php'; ?>