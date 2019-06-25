<?php require_once 'view/layout/header.php' ?>
<?php RecipeController::addMenu(); ?>

<div class="container_12">
    <a href="/login">
        <button id="change-sign-button">Войти в аккаунт</button>
    </a>

    <form action="/registration" method="post">
        <div id="sign-data">
            <div>
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
        <button>Зарегистрироваться</button>
    </form>
</div>

<?php require_once 'view/layout/footer.php' ?>