<?php require_once 'view/layout/header.php' ?>
<?php RecipeController::addMenu(); ?>

<div class="container_12">
    <div id="action-buttons">
        <a href="/login">
            <button class="btn action-btn">Войти в аккаунт</button>
        </a>
    </div>

    <form action="/registration" method="post" class="account-form">
        <div id="sign-data">
            <div class="account-title">
                Зарегистрироваться в Culina
            </div>
            <div>
                <input type="email" name="email" placeholder="E-mail">
            </div>
            <div>
                <input name="name" placeholder="Имя (только буквы)" required>
            </div>
            <div>
                <input name="surname" placeholder="Фамилия (только буквы)" required>
            </div>
            <div>
                <input type="password" name="password" placeholder="Пароль" required>
            </div>
            <div>
                <input type="password" name="password-confirmation" placeholder="Подтвердите пароль" required>
            </div>
            <div id="response">
                <?php if (!empty($message)) { echo $message; } ?>
            </div>
        </div>
        <button class="btn">Зарегистрироваться</button>
    </form>
</div>

<?php require_once 'view/layout/footer.php' ?>