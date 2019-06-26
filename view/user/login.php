<?php require_once 'view/layout/header.php' ?>
<?php RecipeController::addMenu(); ?>

<div class="container_12">
    <div id="action-buttons">
        <a href="/registration">
            <button class="btn">Зарегистрироваться</button>
        </a>
    </div>

    <form action="/login" method="post" class="account-form">
        <div id="sign-data">
            <div class="account-title">
                Войти в аккаунт Culina
            </div>
            <div>
                <input type="email" name="email" placeholder="E-mail" required>
            </div>
            <div>
                <input type="password" name="password" placeholder="Пароль" required>
            </div>
        </div>

        <div id="response">
            <?php if (!empty($message)) { echo $message; } ?>
        </div>
        <button class="btn">Войти</button>
    </form>
</div>

<?php require_once 'view/layout/footer.php' ?>



