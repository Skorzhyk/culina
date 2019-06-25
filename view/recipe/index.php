<?php require_once 'view/layout/header.php' ?>
<?php RecipeController::addMenu(); ?>

<!--<div><span>Новые рецепты</span></div>-->
<?php foreach ($newRecipes as $recipe) : ?>
    <a href="/recipe/view/<?php echo $recipe->getId(); ?>">
        <article class="grid_4">
            <figure class="box-img">
                <img src="/view/web/images/recipes/<?php echo $recipe->getId() . '/' . $recipe->getMainImage(); ?>"/>
            </figure>
            <h3><?php echo $recipe->getName(); ?></h3>
        </article>
    </a>
<?php endforeach; ?>

<!--<div><span>Наиболее просматриваемые рецепты</span></div>-->
<?php foreach ($mostViewedRecipes as $recipe) : ?>
    <a href="/recipe/view/<?php echo $recipe->getId(); ?>">
        <article class="grid_4">
            <figure class="box-img">
                <img src="/view/web/images/recipes/<?php echo $recipe->getId() . '/' . $recipe->getMainImage(); ?>"/>
            </figure>
            <h3><?php echo $recipe->getName(); ?></h3>
        </article>
    </a>
<?php endforeach; ?>

<!--<div><span>Лучшие рецепты</span></div>-->
<?php foreach ($mostRatedRecipes as $recipe) : ?>
    <a href="/recipe/view/<?php echo $recipe->getId(); ?>">
        <article class="grid_4">
            <figure class="box-img">
                <img src="/view/web/images/recipes/<?php echo $recipe->getId() . '/' . $recipe->getMainImage(); ?>"/>
            </figure>
            <h3><?php echo $recipe->getName(); ?></h3>
        </article>
    </a>
<?php endforeach; ?>

<?php require_once 'view/layout/footer.php' ?>
