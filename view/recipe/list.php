<?php require_once 'view/layout/header.php' ?>

<form action="/list" method="post" enctype="multipart/form-data">
    <div id="search-list-container">
        <input id="search-list" name="search" placeholder="Введите текст поиска..."/>
        <button id="search-list-btn" class="btn" type="submit">Искать</button>
    </div>
</form>

<?php if (empty($filter)) : ?>
    <div id="wait-approve-block">
        <a href="<?php echo $_SERVER['REQUEST_URI']; ?>/new"><button class="btn" type="button">Ожидают подтверждения</button> </a>
    </div>
<?php endif; ?>

<table>
    <tr>
        <th>Фотография</th>
        <th>Название</th>
        <th>Количество просмотров</th>
        <th>Статус</th>
    </tr>
    <?php foreach ($recipes as $recipe) : ?>
        <tr class="recipe-line" data-href="/recipe/edit/<?php echo $recipe->getId(); ?>">
            <td><img src="/view/web/images/recipes/<?php echo $recipe->getId() . '/' . $recipe->getMainImage(); ?>"></td>
            <td><?php echo $recipe->getName(); ?></td>
            <td><?php echo $recipe->getVisitNumber(); ?></td>
            <td><?php echo $recipe->getStatus() == 1 ? 'Активен' : 'Ожидает подтверждения'; ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<script>
    $(function () {
        $('.recipe-line').click(function () {
            document.location = $(this).data('href');
        });
    });
</script>

<?php require_once 'view/layout/footer.php' ?>
