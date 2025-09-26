<?php $title = 'Регистрация - ' . config('app.name'); ?>

<?php ob_start(); ?>

<div class="container">
    <div class="row justify-center mt-xl">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header text-center">
                    <h2 class="mb-0">Регистрация</h2>
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

                    <form action="/auth/register" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                        
                        <div class="form-group">
                            <label for="username" class="form-label">Имя пользователя *</label>
                            <input type="text" id="username" name="username" 
                                   class="form-input <?= isset($errors['username']) ? 'error' : '' ?>" 
                                   required
                                   value="<?= htmlspecialchars($old['username'] ?? '') ?>"
                                   placeholder="username123">
                            <?php if (isset($errors['username'])): ?>
                                <span class="error-message"><?= htmlspecialchars($errors['username']) ?></span>
                            <?php endif; ?>
                            <small class="text-secondary">Только латинские буквы и цифры, 3-50 символов</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_name" class="form-label">Имя *</label>
                                    <input type="text" id="first_name" name="first_name" 
                                           class="form-input <?= isset($errors['first_name']) ? 'error' : '' ?>" 
                                           required
                                           value="<?= htmlspecialchars($old['first_name'] ?? '') ?>"
                                           placeholder="Иван">
                                    <?php if (isset($errors['first_name'])): ?>
                                        <span class="error-message"><?= htmlspecialchars($errors['first_name']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_name" class="form-label">Фамилия *</label>
                                    <input type="text" id="last_name" name="last_name" 
                                           class="form-input <?= isset($errors['last_name']) ? 'error' : '' ?>" 
                                           required
                                           value="<?= htmlspecialchars($old['last_name'] ?? '') ?>"
                                           placeholder="Иванов">
                                    <?php if (isset($errors['last_name'])): ?>
                                        <span class="error-message"><?= htmlspecialchars($errors['last_name']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email *</label>
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
                            <label for="phone" class="form-label">Телефон *</label>
                            <input type="tel" id="phone" name="phone" 
                                   class="form-input <?= isset($errors['phone']) ? 'error' : '' ?>" 
                                   value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
                                   placeholder="+7 (900) 123-45-67">
                            <?php if (isset($errors['phone'])): ?>
                                <span class="error-message"><?= htmlspecialchars($errors['phone']) ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password" class="form-label">Пароль *</label>
                                    <input type="password" id="password" name="password" 
                                           class="form-input <?= isset($errors['password']) ? 'error' : '' ?>" 
                                           required
                                           placeholder="Минимум 6 символов">
                                    <?php if (isset($errors['password'])): ?>
                                        <span class="error-message"><?= htmlspecialchars($errors['password']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label">Подтверждение пароля *</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" 
                                           class="form-input <?= isset($errors['password_confirmation']) ? 'error' : '' ?>" 
                                           required
                                           placeholder="Повторите пароль">
                                    <?php if (isset($errors['password_confirmation'])): ?>
                                        <span class="error-message"><?= htmlspecialchars($errors['password_confirmation']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="d-flex items-center gap-sm">
                                <input type="checkbox" name="terms" value="1" required>
                                Я согласен с 
                                <a href="/terms" target="_blank">пользовательским соглашением</a> 
                                и 
                                <a href="/privacy" target="_blank">политикой конфиденциальности</a>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            Зарегистрироваться
                        </button>
                    </form>
                    
                    <hr class="mt-lg mb-lg">
                    
                    <div class="text-center">
                        <p class="text-secondary">Уже есть аккаунт?</p>
                        <a href="/auth/login" class="btn btn-outline btn-lg">
                            Войти в систему
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php require __DIR__ . '/../layouts/main.php'; ?>