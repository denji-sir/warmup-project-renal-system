<?php $title = 'Вход в систему - ' . config('app.name'); ?>

<?php ob_start(); ?>

<div class="container">
    <div class="row justify-center mt-xl">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-header text-center">
                    <h2 class="mb-0">Вход в систему</h2>
                </div>
                
                <div class="card-body">
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

                    <form action="/auth/login" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" 
                                   class="form-input <?= isset($errors['email']) ? 'error' : '' ?>" 
                                   required
                                   value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                                   placeholder="example@domain.com">
                            <?php if (isset($errors['email'])): ?>
                                <span class="error-message"><?= htmlspecialchars($errors['email']) ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="form-label">Пароль</label>
                            <input type="password" id="password" name="password" 
                                   class="form-input <?= isset($errors['password']) ? 'error' : '' ?>" 
                                   required
                                   placeholder="Введите пароль">
                            <?php if (isset($errors['password'])): ?>
                                <span class="error-message"><?= htmlspecialchars($errors['password']) ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <div class="d-flex justify-between items-center">
                                <label class="d-flex items-center gap-xs">
                                    <input type="checkbox" name="remember" value="1">
                                    Запомнить меня
                                </label>
                                
                                <a href="/auth/forgot-password" class="text-sm">
                                    Забыли пароль?
                                </a>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            Войти
                        </button>
                    </form>
                    
                    <hr class="mt-lg mb-lg">
                    
                    <div class="text-center">
                        <p class="text-secondary">Ещё не зарегистрированы?</p>
                        <a href="/auth/register" class="btn btn-outline btn-lg">
                            Создать аккаунт
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php require __DIR__ . '/../layouts/main.php'; ?>