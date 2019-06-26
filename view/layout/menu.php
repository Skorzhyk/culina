<div id="menu">
    <form action="/recipes" method="post" enctype="multipart/form-data">
        <?php $i = 0; ?>
        <?php foreach ($categoryTree as $typeId => $categoriesByType) : ?>
            <div class="category-type" data-type-id="<?php echo $typeId; ?>">
                <select name="cats[]" data-order-number="<?php echo ++$i; ?>">
                    <option value="0"><?php echo $categoryTypes[$typeId]; ?></option>
                    <?php foreach ($categoriesByType as $categoryId => $data) : ?>
                        <option value="<?php echo $categoryId; ?>" <?php echo !empty($_POST['cats']) && in_array($categoryId, $_POST['cats']) ? 'selected' : ''; ?>><?php echo $categories[$categoryId]->getName(); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php
                    $hiddenTag = 'disabled';

                    if (!empty($_POST['cats'])) {
                        $categoriesByTypeIds = array_keys($categoriesByType);

                        if (!empty(array_intersect($categoriesByTypeIds, $_POST['cats']))) {
                            $hiddenTag = 'hidden disabled';
                        }
                    }
                ?>
                <?php foreach ($categoriesByType as $categoryId => $subCategory) : ?>
                    <?php if (!empty($subCategory)) : ?>
                        <select name="cats[]" data-order-number="<?php echo ++$i; ?>" data-parent-category="<?php echo $categoryId; ?>" <?php echo !empty($_POST['cats']) && in_array($categoryId, $_POST['cats']) ? '' : $hiddenTag; ?>>
                            <option value="0">Выберите подкатегорию</option>
                            <?php foreach ($subCategory as $subCategoryId => $data) : ?>
                                <option value="<?php echo $subCategoryId; ?>" <?php echo !empty($_POST['cats']) && in_array($subCategoryId, $_POST['cats']) ? 'selected' : ''; ?>><?php echo $categories[$subCategoryId]->getName(); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php
                            if ($hiddenTag == 'disabled') {
                                $hiddenTag = 'hidden disabled';
                            }
                        ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <div><input name="search" placeholder="Введите текст поиска..."/></div>

        <button class="btn" type="submit">Искать</button>
    </form>
</div>

<script>
    $(function () {
        $('.category-type').find('select').change(function () {
            var selectedCat = $(this);
            var typeCats = selectedCat.closest('.category-type').find('select');

            typeCats.each(function (index, elem) {
                if ($(elem).data('order-number') > selectedCat.data('order-number')) {
                    $(elem).val(0);

                    $(elem).prop('disabled', true);
                    $(elem).hide();

                    if (selectedCat.val() == 0 && index == 1) {
                        $(elem).css('display', 'inline-block');
                    }
                }

                if ($(elem).data('parent-category') == selectedCat.val()) {
                    $(elem).css('display', 'inline-block');
                    $(elem).prop('disabled', false);
                }
            });
        });
    });
</script>