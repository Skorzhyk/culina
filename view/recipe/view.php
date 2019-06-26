<?php require_once 'view/layout/header.php' ?>
<?php RecipeController::addMenu(); ?>

<div class="recipe">
    <div class="recipe-name"><?php echo $recipe->getName(); ?></div>

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
            <button id="like" class="btn <?php echo $likeClass; ?>">Нравится</button>
            <span id="like-number"><?php echo $recipe->getLikes(); ?></span>
        </div>
        <div>
            <button id="dislike" class="btn <?php echo $dislikeClass; ?>">Не нравится</button>
            <span id="dislike-number"><?php echo $recipe->getDislikes(); ?></span>
        </div>

        <div id="favorites-btns">
            <?php if (!empty($_SESSION['userId'])) : ?>
                <?php $isFavorite = in_array($recipe->getId(), $user->getFavorites()); ?>
                <div>
                    <button id="add-to-favorites" class="btn <?php echo $isFavorite ? 'hidden' : ''; ?>" type="button">В Избранное</button>
                    <button id="remove-from-favorites" class="btn <?php echo $isFavorite ? '' : 'hidden'; ?>" type="button">Удалить из Избранного</button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="recipe-gallery">
        <div class="owl-carousel">
            <?php if (!empty($recipe->getImages())) : ?>
                <?php foreach ($recipe->getImages() as $image) : ?>
                    <div>
                        <img src="/view/web/images/recipes/<?php echo $recipe->getId() . '/' . $image->getImage(); ?>"/>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="description"><p><?php echo $recipe->getDescription(); ?></p></div>

    <div id="ingredients">
        <div class="ingredients-title">Ингредиенты</div>
        <?php foreach ($recipe->getIngredients() as $ingredient) : ?>
            <div class="ingredient">
                <div><?php echo $ingredient['name']; ?></div>
                <div><?php echo $ingredient['amount']; ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="view-steps">
        <div class="owl-carousel">
            <?php foreach ($recipe->getSteps() as $step) : ?>
                <div>
                    <div class="step-title">Шаг <?php echo $step->getOrderNumber(); ?></div>
                    <?php if (!empty($step->getImage())) : ?>
                        <div class="step-media">
                            <img src="/view/web/images/recipes/<?php echo $recipe->getId() . '/steps/' . $step->getImage(); ?>"/>
                        </div>
                    <?php elseif (!empty($step->getVideo())) : ?>
                        <div class="step-media">
                            <video controls="controls">
                                <source src="/view/web/images/recipes/<?php echo $recipe->getId() . '/steps/' . $step->getVideo(); ?>">
                            </video>
                        </div>
                    <?php endif; ?>
                    <div class="description">
                        <p><?php echo $step->getDescription(); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="comments">
        <div class="comments-title">Комментарии</div>
        <?php if (!empty($recipe->getComments())) : ?>
            <?php foreach ($recipe->getComments() as $comment) : ?>
                <div class="comment">
                    <div class="comment-owner"><?php echo $comment->getUserName(); ?></div>
                    <div class="comment-text"><?php echo $comment->getText(); ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['userId'])) : ?>
            <button class="btn" type="button" id="add-comment">Оставить комментарий</button>
        <?php endif; ?>
    </div>
</div>

<script>
    $(function () {
        $(".owl-carousel").owlCarousel({
            items: 1,
            nav: true,
            dots: false,
            navText : ['<i class="fa fa-angle-left" aria-hidden="true"></i>','<i class="fa fa-angle-right" aria-hidden="true"></i>']
        });

        $('#add-comment').click(function () {
            var commentTemplate = '<form action="/recipe/comment/<?php echo $recipe->getId(); ?>" method="post" enctype="multipart/form-data"><div id="comment-form">' +
                '<div><input name="comment" required></div>' +
                '<div><button class="btn" id="save-comment" type="button">Опубликовать</button><div>' +
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
                            '<div class="comment-owner">' + response + '</div>' +
                            '<div class="comment-text">' + comment + '</div>' +
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
                    $('#like').addClass('disabled');
                    $('#dislike').addClass('disabled');
                }
            });
        }

        $('#add-to-favorites').click(function () {
            $.ajax({
                method: 'GET',
                url: '/user/favorites/add/<?php echo $recipe->getId(); ?>',

                success: function() {
                    $('#add-to-favorites').hide();
                    $('#remove-from-favorites').css('display', 'inline-block');
                }
            });
        });

        $('#remove-from-favorites').click(function () {
            $.ajax({
                method: 'GET',
                url: '/user/favorites/remove/<?php echo $recipe->getId(); ?>',

                success: function() {
                    $('#remove-from-favorites').hide();
                    $('#add-to-favorites').css('display', 'inline-block');
                }
            });
        });
    });
</script>

<?php require_once 'view/layout/footer.php' ?>
