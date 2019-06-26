<?php require_once 'view/layout/header.php' ?>
<?php RecipeController::addMenu(); ?>

<div class="container_12">
    <form action="/account/save" method="post" class="account-form">
        <div id="sign-data">
            <div class="account-title">
                Данные аккаунта
            </div>
            <div>
                <input type="email" name="email" value="<?php echo $user->getEmail(); ?>" disabled>
            </div>
            <div>
                <input name="name" placeholder="Имя (только буквы)" value="<?php echo $user->getName(); ?>" required>
            </div>
            <div>
                <input name="surname" placeholder="Фамилия (только буквы)" value="<?php echo $user->getSurname(); ?>" required>
            </div>
            <div>
                <input type="password" name="password" placeholder="Обновить пароль">
            </div>
            <div>
                <input type="password" name="password-confirmation" placeholder="Подтвердите пароль">
            </div>
            <div id="response">
                <?php if (!empty($message)) { echo $message; } ?>
            </div>
        </div>

        <button class="btn">Сохранить</button>
    </form>
</div>

<?php require_once 'view/layout/footer.php' ?>