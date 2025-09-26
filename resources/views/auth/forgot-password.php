<?php $title = 'Восстановление пароля - ' . config('app.name'); ?>

<?php ob_start(); ?>

<div class="container">
    <div class="row justify-center mt-xl">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-header text-center">
                    <h2 class="mb-0">Восстановление пароля</h2>
                </div>
                
                <div class="card-body">
                    <p class="text-center text-secondary mb-lg">
                        Введите ваш email адрес. Мы отправим вам ссылку для восстановления пароля.
                    </p>
                    
                    <?php if (isset($errors) && !empty($errors)): ?>
                        <div class="alert alert-error mb-lg">
                            <?php if (isset($errors['general'])): ?>
                                <p><?= htmlspecialchars($errors['general']) ?></p>
                            <?php else: ?>
                                <ul>
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success mb-lg">
                            <p><?= htmlspecialchars($success) ?></p>
                        </div>
                    <?php endif; ?>

                    <form action="/auth/forgot-password" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" 
                                   class="form-input <?= isset($errors['email']) ? 'error' : '' ?>" 
                                   required
                                   value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                                   placeholder="example@domain.com"
                                   autofocus>
                            <?php if (isset($errors['email'])): ?>
                                <span class="error-message"><?= htmlspecialchars($errors['email']) ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            Отправить ссылку
                        </button>
                    </form>
                    
                    <hr class="mt-lg mb-lg">
                    
                    <div class="text-center">
                        <a href="/auth/login" class="btn btn-outline">
                            ← Вернуться к входу
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php require __DIR__ . '/../layouts/main.php'; ?>