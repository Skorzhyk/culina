<?php require_once 'view/layout/header.php' ?>
<?php RecipeController::addMenu(); ?>

<div class="recipe">
    <div><span><?php echo $recipe->getName(); ?></span></div>
    <div><p><?php echo $recipe->getDescription(); ?></p></div>
    <div>
        <?php if (!empty($recipe->getImages())) : ?>
            <?php foreach ($recipe->getImages() as $image) : ?>
                <div>
                    <img src="/view/web/images/recipes/<?php echo $recipe->getId() . '/' . $image->getImage(); ?>"/>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div id="rate">
        <?php
            $rate = !empty($_SESSION['rates'][$recipe->getId()]) ? $_SESSION['rates'][$recipe->getId()] : false;
            $likeClass = $dislikeClass = '';

            if ($rate) {
                $likeClass = $dislikeClass = 'disabled';

                if ($rate == 'like') {
                    $likeClass .= ' active';
                } else {
                    $dislikeClass .= ' active';
                }
            }
        ?>
        <div>
            <button id="like" class="<?php echo $likeClass; ?>">Нравится</button>
            <span id="like-number"><?php echo $recipe->getLikes(); ?></span>
        </div>
        <div>
            <button id="dislike" class="<?php echo $dislikeClass; ?>">Не нравится</button>
            <span id="dislike-number"><?php echo $recipe->getDislikes(); ?></span>
        </div>
    </div>
    <div>
        <?php if (!empty($_SESSION['userId'])) : ?>
            <?php $isFavorite = in_array($recipe->getId(), $user->getFavorites()); ?>
            <div>
                <button id="add-to-favorites" type="button" <?php echo $isFavorite ? 'hidden' : ''; ?>>В Избранное</button>
                <button id="remove-from-favorites" type="button" <?php echo $isFavorite ? '' : 'hidden'; ?>>Удалить из Избранного</button>
            </div>
        <?php endif; ?>
    </div>
    <div class="view-steps">
        <?php foreach ($recipe->getSteps() as $step) : ?>
            <div>
                <div><span>Шаг <?php echo $step->getOrderNumber(); ?></span></div>
                <div>
                    <?php if (!empty($step->getImage())) : ?>
                        <div>
                            <img src="/view/web/images/recipes/<?php echo $recipe->getId() . '/steps/' . $step->getImage(); ?>"/>
                        </div>
                    <?php elseif (!empty($step->getVideo())) : ?>
                        <div>
                            <video controls="controls">
                                <source src="/view/web/images/recipes/<?php echo $recipe->getId() . '/steps/' . $step->getVideo(); ?>">
                            </video>
                        </div>
                    <?php endif; ?>
                </div>
                <div>
                    <p><?php echo $step->getDescription(); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div id="comments">
        <span>Комментарии</span>
        <?php if (!empty($recipe->getComments())) : ?>
            <?php foreach ($recipe->getComments() as $comment) : ?>
                <div class="comment">
                    <div><span><?php echo $comment->getUserName(); ?></span></div>
                    <div><p><?php echo $comment->getText(); ?></p></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['userId'])) : ?>
            <button type="button" id="add-comment">Оставить комментарий</button>
        <?php endif; ?>
    </div>
</div>

<script>
    $(function () {
        $('#add-comment').click(function () {
            var commentTemplate = '<form action="/recipe/comment/<?php echo $recipe->getId(); ?>" method="post" enctype="multipart/form-data"><div id="comment-form">' +
                '<div><input name="comment" required></div>' +
                '<button id="save-comment" type="button">Опубликовать</button>' +
                '</div></form>';

            $(this).hide();
            $('#comments').append(commentTemplate);
        });

        $('body').on('click', '#save-comment', function () {
            var form = $(this).closest('form');
            var comment = form.find('input').val();

            $.ajax({
                method: 'POST',
                data: form.serialize(),
                url: form.attr('action'),

                success: function(response) {
                    if (response !== 'error') {
                        var newComment = '<div class="comment">' +
                            '<div><span>' + response + '</span></div>' +
                            '<div><p>' + comment + '</p></div>' +
                            '</div>';

                        form.remove();
                        $('#add-comment').show();
                        $('#add-comment').before(newComment);
                    }
                }
            });
        });

        $('#like').click(function () {
            if ($(this).hasClass('disabled') === false) {
                rateRecipe('like');
            }
        });

        $('#dislike').click(function () {
            if ($(this).hasClass('disabled') === false) {
                rateRecipe('dislike');
            }
        });

        function rateRecipe(rate) {
            var data = {rate: rate};

            $.ajax({
                method: 'POST',
                data: data,
                url: '/recipe/rate/<?php echo $recipe->getId(); ?>',

                success: function(response) {
                    $('#' + rate).addClass('active');
                    $('#' + rate + '-number').html(response);
                }
            });
        }

        $('#add-to-favorites').click(function () {
            $.ajax({
                method: 'GET',
                url: '/user/favorites/add/<?php echo $recipe->getId(); ?>',

                success: function() {
                    $('#add-to-favorites').hide();
                    $('#remove-from-favorites').show();
                }
            });
        });

        $('#remove-from-favorites').click(function () {
            $.ajax({
                method: 'GET',
                url: '/user/favorites/remove/<?php echo $recipe->getId(); ?>',

                success: function() {
                    $('#remove-from-favorites').hide();
                    $('#add-to-favorites').show();
                }
            });
        });
    });
</script>

<?php require_once 'view/layout/footer.php' ?>
