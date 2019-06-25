<?php require_once 'view/layout/header.php' ?>
<?php RecipeController::addMenu(); ?>

<div class="container_12">
    <a href="/registration">
        <button id="change-sign-button">Зарегистрироваться</button>
    </a>

    <form action="/login" method="post">
        <div id="sign-data">
            <div>
                Войти в аккаунт Culina
            </div>
            <div class="user-input">
                <input type="email" name="email" placeholder="E-mail">
            </div>
            <div class="user-input">
                <input type="password" name="password" placeholder="Пароль">
            </div>
            <div id="response">
                <?php if (!empty($message)) { echo $message; } ?>
            </div>
        </div>

        <button class="center-button">Войти</button>
    </form>
</div>

<?php require_once 'view/layout/footer.php' ?>



