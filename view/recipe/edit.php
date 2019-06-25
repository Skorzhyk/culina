<?php require_once 'view/layout/header.php' ?>

<form id="edit-form" action="/recipe/edit<?php echo $recipeId ? '/' . $recipeId : ''; ?>" method="post" enctype="multipart/form-data">
    <div id="action-buttons">
        <?php if ($recipeId) : ?>
            <a href="/recipe/delete/<?php echo $recipeId; ?>"><button class="btn" id="delete-button" type="button">Удалить</button></a>
        <?php endif; ?>
        <?php if ($_SESSION['admin'] && $recipeId && $recipe->getStatus() == 0) : ?>
            <button class="btn" type="submit">Опубликовать</button>
        <?php endif; ?>
        <button class="btn" type="submit">Сохранить</button>
    </div>
    <fieldset>
        <div>
            <h7>Название рецепта</h7>
            <input name="name" required value="<?php echo $recipeId ? $recipe->getName() : ''; ?>">
        </div>
        <div>
            <div>Описание рецепта</div>
            <textarea name="description" required><?php echo $recipeId ? $recipe->getDescription() : ''; ?></textarea>
        </div>
        <div id="categories">
            <div>Категории рецепта</div>

            <?php $i = 0; ?>
            <?php foreach ($categoryTree as $typeId => $categoriesByType) : ?>
                <div class="category-type" data-type-id="<?php echo $typeId; ?>">
                    <select name="cats[]" data-order-number="<?php echo ++$i; ?>">
                        <option value="0"><?php echo $categoryTypes[$typeId]; ?></option>
                        <?php foreach ($categoriesByType as $categoryId => $data) : ?>
                            <option value="<?php echo $categoryId; ?>" <?php echo $recipeId && in_array($categoryId, $recipe->getCategories()) ? 'selected' : ''; ?>><?php echo $categories[$categoryId]->getName(); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php foreach ($categoriesByType as $categoryId => $subCategory) : ?>
                        <?php if (!empty($subCategory)) : ?>
                            <select name="cats[]" data-order-number="<?php echo ++$i; ?>" data-parent-category="<?php echo $categoryId; ?>" <?php echo $recipeId && in_array($categoryId, $recipe->getCategories()) ? '' : 'hidden'; ?>>
                                <option value="0">Выберите подкатегорию</option>
                                <?php foreach ($subCategory as $subCategoryId => $data) : ?>
                                    <option value="<?php echo $subCategoryId; ?>" <?php echo $recipeId && in_array($subCategoryId, $recipe->getCategories()) ? 'selected' : ''; ?>><?php echo $categories[$subCategoryId]->getName(); ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div id="ingredients-tab">
            <div>Ингредиенты</div>

            <script>
                var ingredients = JSON.parse('<?php echo json_encode($ingredients); ?>');
                var ingredientNames = [];

                ingredients.forEach(function (item) {
                    ingredientNames.push(item['name']);
                });
            </script>
            <div id="ingredients">
                <?php if ($recipeId && !empty($recipe->getIngredients())) : ?>
                    <?php $i = 0; ?>
                    <?php foreach ($recipe->getIngredients() as $ingredient) : ?>
                        <div class="ingredient" data-order-number="<?php echo ++$i; ?>">
                            <input class="map-id" name="ingredients[<?php echo $i; ?>][id]" value="<?php echo $ingredient['id']; ?>" hidden>
                            <input class="ingredient-id" name="ingredients[<?php echo $i; ?>][ingredient_id]" value="<?php echo $ingredient['ingredient_id']; ?>" hidden>
                            <div class="ingredient-name"><input name="ingredients[<?php echo $i; ?>][name]" value="<?php echo $ingredient['name']; ?>" placeholder="Имя"></div>
                            <div><input name="ingredients[<?php echo $i; ?>][amount]" value="<?php echo $ingredient['amount']; ?>" placeholder="Количество"></div>
                            <label class="ingredient-delete"><input type="checkbox" name="ingredients[<?php echo $i; ?>][delete]">Удалить ингредиент</label>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button class="btn" type="button" id="add-ingredient">Добавить ингредиент</button>
        </div>
        <div>
            <div><span>Галерея рецепта</span></div>
            <?php for ($i = 1; $i <= 5; $i++) : ?>
                <div class="image-container">
                    <?php $isUploaded = $recipeId && !empty($recipe->getImages()[$i]); ?>
                    <?php if ($isUploaded) : ?>
                        <div class="image">
                            <img src="/view/web/images/recipes/<?php echo $recipe->getId() . '/' . $recipe->getImages()[$i]->getImage(); ?>"/>
                        </div>
                    <?php endif; ?>
                    <div class="image-field" <?php echo $isUploaded ? 'hidden' : ''; ?>>
                        <input type="file" name="image[<?php echo $i; ?>]">
                    </div>
                    <label class="image-delete" <?php echo !$isUploaded ? 'hidden' : ''; ?>><input type="checkbox" name="image[delete][<?php echo $i; ?>]">Удалить изображение</label>
                </div>
            <?php endfor; ?>
        </div>
        <div id="steps-tab">
            <div id="steps">
                <?php if ($recipeId && !empty($recipe->getSteps())) : ?>
                    <?php foreach ($recipe->getSteps() as $step) : ?>
                        <div class="step" id="step-<?php echo $step->getOrderNumber(); ?>" data-order-number="<?php echo $step->getOrderNumber(); ?>">
                            <div><span>ШАГ <?php echo $step->getOrderNumber(); ?></span></div>
                            <div class="image-container">
                                <?php $isUploaded = !empty($step->getImage()); ?>
                                <span>Изображение</span>
                                <?php if ($isUploaded) : ?>
                                    <div class="image">
                                        <img src="/view/web/images/recipes/<?php echo $recipe->getId() . '/steps/' . $step->getImage(); ?>"/>
                                    </div>
                                <?php endif; ?>
                                <div class="image-field" <?php echo $isUploaded ? 'hidden' : ''; ?>>
                                    <input type="file" name="step[<?php echo $step->getOrderNumber(); ?>][image]">
                                </div>
                                <label class="image-delete" <?php echo !$isUploaded ? 'hidden' : ''; ?>><input type="checkbox" name="step[<?php echo $step->getOrderNumber(); ?>][image][delete]" <?php echo empty($step->getImage()) ? 'checked' : ''; ?>>Удалить изображение</label>
                            </div>
                            <div class="video-container">
                                <?php $isUploaded = !empty($step->getVideo()); ?>
                                <span>Видео</span>
                                <?php if ($isUploaded) : ?>
                                    <div class="video">
                                        <video autoplay muted="muted">
                                            <source src="/view/web/images/recipes/<?php echo $recipe->getId() . '/steps/' . $step->getVideo(); ?>">
                                        </video>
                                    </div>
                                <?php endif; ?>
                                <div class="video-field" <?php echo $isUploaded ? 'hidden' : ''; ?>>
                                    <input type="file" name="step[<?php echo $step->getOrderNumber(); ?>][video]">
                                </div>
                                <label class="video-delete" <?php echo !$isUploaded ? 'hidden' : ''; ?>><input type="checkbox" name="step[<?php echo $step->getOrderNumber(); ?>][video][delete]" <?php echo empty($step->getVideo()) ? 'checked' : ''; ?>>Удалить видео</label>
                            </div>
                            <div>
                                <span>Описание шага</span>
                                <textarea name="step[<?php echo $step->getOrderNumber(); ?>][description]" required><?php echo $step->getDescription(); ?></textarea>
                            </div>

                            <button class="remove-step btn" type="button">Удалить шаг</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button class="btn" type="button" id="add-step">Добавить шаг приготовления</button>
        </div>
    </fieldset>
</form>

<script>
    $(function () {
        $('.ingredient-name input').autocomplete({source: ingredientNames});

        $('#add-step').click(function () {
            var id = 1;

            var lastStep = $('.step').last();
            if (lastStep.length > 0) {
                id = lastStep.data('order-number') + 1;
            }

            var stepTemplate = '<div class="step" id="step-' + id + '" data-order-number="' + id + '">' +
                '<div><span>ШАГ ' + id + '</span></div>' +
                '<div class="image-container">' +
                '<span>Изображение</span>' +
                '<div class="image-field"><input type="file" name="step[' + id + '][image]"></div>' +
                '<label class="image-delete" hidden><input type="checkbox" name="step[' + id + '][image][delete]" checked>Удалить изображение</label>' +
                '</div>' +
                '<div><span>Видео</span><input type="file" name="step[' + id + '][video]"></div>' +
                '<label><input type="checkbox" name="step[' + id + '][video][delete]" checked>Удалить видео</label>' +
                '<div><span>Описание шага</span><textarea name="step[' + id + '][description]" required></textarea></div>' +
                '<button class="btn remove-step" type="button">Удалить шаг</button>' +
                '</div>';

            $('#steps').append(stepTemplate);
        });

        $('body').on('click', '.remove-step', function () {
            $(this).closest('.step').remove();
        });

        $('.category-type').find('select').change(function () {
            var selectedCat = $(this);
            var typeCats = selectedCat.closest('.category-type').find('select');

            typeCats.each(function (index, elem) {
                if ($(elem).data('order-number') > selectedCat.data('order-number')) {
                    $(elem).val(0);
                    $(elem).hide();
                }

                if ($(elem).data('parent-category') == selectedCat.val()) {
                    $(elem).css('display', 'inline-block');
                }
            });
        });

        var imageContainer = $('.image-container');

        imageContainer.find('.image-delete input').change(function () {
            var container = $(this).closest('.image-container');

            container.find('.image').hide();
            container.find('.image-field').show();
            $(this).parent().hide();
        });

        imageContainer.find('.image-field input').change(function () {
            var container = $(this).closest('.image-container');

            if ($(this).val() === '') {
                container.find('.image-delete input').prop('checked', true);
            } else {
                container.find('.image-delete input').prop('checked', false);
            }
        });

        var videoContainer = $('.video-container');

        videoContainer.find('.video-delete input').change(function () {
            var container = $(this).closest('.video-container');

            container.find('.video').hide();
            container.find('.video-field').show();
            $(this).parent().hide();
        });

        videoContainer.find('.video-field input').change(function () {
            var container = $(this).closest('.video-container');

            if ($(this).val() === '') {
                container.find('.video-delete input').prop('checked', true);
            } else {
                container.find('.video-delete input').prop('checked', false);
            }
        });

        $('#add-ingredient').click(function () {
            var id = 1;

            var lastIngredient = $('.ingredient').last();
            if (lastIngredient.length > 0) {
                id = lastIngredient.data('order-number') + 1;
            }

            var ingredientTemplate = '<div class="ingredient" data-order-number="' + id + '">' +
                '<input class="ingredient-id" name="ingredients[' + id + '][ingredient_id]" hidden>' +
                '<div class="ingredient-name"><input name="ingredients[' + id + '][name]" placeholder="Имя"></div>' +
                '<div><input name="ingredients[' + id + '][amount]" placeholder="Количество"></div>' +
                '<label class="ingredient-delete"><input type="checkbox" name="ingredients[' + id + '][delete]">Удалить ингредиент</label>' +
                '</div>';

            $('#ingredients').append(ingredientTemplate);

            $('.ingredient-name input').autocomplete({source: ingredientNames});
        });

        $('body').on('change', '#ingredients .ingredient-name input', function () {
            var match = false;
            var value = $(this).val();
            var idField = $(this).closest('.ingredient').find('.ingredient-id');

            ingredients.forEach(function (item) {
                if (item['name'] == value) {
                    idField.val(item['id']);
                    match = true;
                }
            });

            if (!match) {
                idField.val('');
            }
        });

        $('body').on('change', '.ingredient-delete input', function () {
            var ingredient = $(this).closest('.ingredient');

            if (ingredient.find('.map-id').length > 0) {
                ingredient.hide();
            } else {
                ingredient.remove();
            }
        });
    });
</script>

<?php require_once 'view/layout/footer.php' ?>
