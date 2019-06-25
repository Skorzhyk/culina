<?php require_once 'view/layout/config.php' ?>

<header id="header">
    <div class="main">
        <div class="row-top">
            <h1>
                <a href="/">CULINA</a>
            </h1>
            <nav>
                <ul class="sf-menu sf-js-enabled">
                    <?php if (!empty($_SESSION['userId']) && $_SESSION['admin']) : ?>
                        <li><a href="/list">Все рецепты</a></li>
                    <?php endif; ?>
                    <?php if (!empty($_SESSION['userId'])) : ?>
                        <li><a href="/list/user/<?php echo $_SESSION['userId']; ?>">Мои рецепты</a></li>
                        <li><a href="/recipes/favorites">Избранное</a></li>
                        <li><a href="/recipe/edit">Новый рецепт</a></li>
                    <?php endif; ?>
                    <?php if (empty($_SESSION['userId'])) : ?>
                        <li><a href="/login">Войти</a></li>
                    <?php else : ?>
                        <li><a href="/account">Мой аккаунт</a></li>
                        <li><a href="/logout">Выйти</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</header>

<section id="content">
    <div class="container_12">
