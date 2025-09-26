<?php $title = 'Новый пароль - ' . config('app.name'); ?>

<?php ob_start(); ?>

<div class="container">
    <div class="row justify-center mt-xl">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-header text-center">
                    <h2 class="mb-0">Новый пароль</h2>
                    <p class="text-secondary">Введите новый пароль для вашего аккаунта</p>
                </div>
                
                <div class="card-body">
                    <?php if (isset($errors) && !empty($errors)): ?>
                        <div class="alert alert-error mb-lg">
                            <?php if (isset($errors['general'])): ?>
                                <p><?= htmlspecialchars($errors['general']) ?></p>
                            <?php elseif (isset($errors['token'])): ?>
                                <p><?= htmlspecialchars($errors['token']) ?></p>
                                <div class="text-center mt-lg">
                                    <a href="/auth/forgot-password" class="btn btn-primary">
                                        Запросить новую ссылку
                                    </a>
                                </div>
                            <?php else: ?>
                                <ul>
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!isset($errors['token'])): ?>
                        <form action="/auth/reset-password" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                            <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
                            
                            <div class="form-group">
                                <label for="password" class="form-label">Новый пароль</label>
                                <input type="password" id="password" name="password" 
                                       class="form-input <?= isset($errors['password']) ? 'error' : '' ?>" 
                                       required
                                       placeholder="Минимум 6 символов"
                                       autofocus>
                                <?php if (isset($errors['password'])): ?>
                                    <span class="error-message"><?= htmlspecialchars($errors['password']) ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">Подтверждение пароля</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" 
                                       class="form-input <?= isset($errors['password_confirmation']) ? 'error' : '' ?>" 
                                       required
                                       placeholder="Повторите новый пароль">
                                <?php if (isset($errors['password_confirmation'])): ?>
                                    <span class="error-message"><?= htmlspecialchars($errors['password_confirmation']) ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg btn-block">
                                Обновить пароль
                            </button>
                        </form>
                    <?php endif; ?>
                    
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