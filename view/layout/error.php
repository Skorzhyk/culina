<?php require_once 'view/layout/header.php' ?>
<?php RecipeController::addMenu(); ?>

<div id="error">
    <span><?php echo $message; ?></span>
</div>

<?php require_once 'view/layout/footer.php' ?>