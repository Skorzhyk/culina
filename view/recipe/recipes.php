<?php require_once 'view/layout/header.php' ?>
<?php RecipeController::addMenu(); ?>

<?php foreach ($recipes as $recipe) : ?>
<a href="/recipe/view/<?php echo $recipe->getId(); ?>">
    <article class="grid_4 recipe-preview">
        <figure class="box-img">
            <img src="/view/web/images/recipes/<?php echo $recipe->getId() . '/' . $recipe->getMainImage(); ?>"/>
        </figure>
        <div class="recipe-name"><?php echo $recipe->getName(); ?></div>
    </article>
</a>
<?php endforeach; ?>

<?php require_once 'view/layout/footer.php' ?>
