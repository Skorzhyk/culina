<?php

require_once 'model/Recipe.php';
require_once 'model/User.php';
require_once 'model/Category.php';
require_once 'model/Ingredient.php';
require_once 'model/Comment.php';

class RecipeController
{
    public function actionIndex()
    {
        $recipeObject = new Recipe();
        $recipes = $recipeObject->getList();

        $newRecipes = $recipeObject->getList(' ORDER BY created_at DESC LIMIT 3', []);
        $mostViewedRecipes = $recipeObject->getList(' ORDER BY visit_number DESC LIMIT 3', []);
        $mostRatedRecipes = $recipeObject->getList(' ORDER BY likes-dislikes DESC LIMIT 3', []);

        return require_once('view/recipe/index.php');
    }

    public function actionView($recipeId)
    {
        if ($recipeId) {
            $recipe = new Recipe();

            try {
                $recipe->load($recipeId);

                if (empty($_SESSION['visited'][$recipeId])) {
                    $recipe->setVisitNumber($recipe->getVisitNumber() + 1);
                    $_SESSION['visited'][$recipeId] = true;

                    $recipe->save();
                }

                if (!empty($_SESSION['userId'])) {
                    $user = new User();
                    $user->load($_SESSION['userId']);
                }
            } catch (Exception $e) {
                $message = $e->getMessage();

                return require_once('view/layout/error.php');
            }

            return require_once('view/recipe/view.php');
        }

        return true;
    }

    public function actionEdit($recipeId = null)
    {
        if (empty($_POST)) {
            if ($recipeId) {
                $recipe = new Recipe();

                try {
                    $recipe->getForEditing($recipeId);
                } catch (Exception $e) {
                    $message = $e->getMessage();

                    return require_once('view/layout/error.php');
                }
            }

            $categoryObject = new Category();
            $categoryTypes = $categoryObject->getTypes();
            $categoryTree = $categoryObject->getCategoryTree();
            $categories = $categoryObject->getList();

            $ingredientObject = new Ingredient();
            $ingredients = $ingredientObject->getUserIngredients($_SESSION['userId']);

            return require_once('view/recipe/edit.php');
        }

        try {
            $recipe = new Recipe();
            if ($recipeId) {
                $recipe->load($recipeId);
            }

            if (empty($_SESSION['userId']) || (!$recipeId && $_SESSION['admin'] == 0 && ($this->getUserId() != $_SESSION['userId']))) {
                throw new Exception('Недостаточно прав для редактирования данной записи.');
            }

            $recipe->setName($_POST['name']);
            $recipe->setDescription($_POST['description']);
            $recipe->setUserId($_SESSION['userId']);
            $recipe->setStatus($_SESSION['admin']);

            $postCategories = $_POST['cats'];
            foreach ($postCategories as $key => $categoryId) {
                if ($categoryId == 0) {
                    unset($postCategories[$key]);
                }
            }
            if (!empty($postCategories)) {
                $recipe->setCategories($postCategories);
            }

            $recipe->save();
            header('Location: /recipe/edit/' . $recipe->getId());
        } catch (Exception $e) {
            $message = $e->getMessage();

            return require_once('view/layout/error.php');
        }
    }

    public function actionList($filter = null, $value = null)
    {
        $recipeObject = new Recipe();

        $filterString = ' WHERE ';
        $filterParams = [];

        if (!empty($filter) && $filter == 'user') {
            if (empty($_SESSION['userId']) || $_SESSION['userId'] != $value)  {
                header('Location: /login');
                return true;
            }

            $filterString .= 'user_id = ' . DataBase::SYM_QUERY;
            $filterParams[] = $_SESSION['userId'];
        } else {
            if (empty($_SESSION['admin']) || $_SESSION['admin'] == 0) {
                header('Location: /');
                return true;
            }
        }

        if (!empty($filter) && $filter == 'new') {
            $filterString .= 'status = ' . DataBase::SYM_QUERY;
            $filterParams[] = 0;
        }

        if (!empty($_POST['search'])) {
            if (!empty($filterParams)) {
                $filterString .= ' AND ';
            }

            $filterString .= 'name LIKE ' . DataBase::SYM_QUERY;
            $filterParams[] = '%' . $_POST['search'] . '%';
        }

        if (empty($filterParams)) {
            $filterString = '';
        }

        try {
            $recipes = $recipeObject->getList($filterString, $filterParams);
        } catch (Exception $e) {
            $message = $e->getMessage();

            return require_once('view/layout/error.php');
        }


        return require_once('view/recipe/list.php');
    }

    public function actionDelete($recipeId)
    {
        $recipe = new Recipe();

        try {
            $recipe->load($recipeId);
        } catch (Exception $e) {
            $message = $e->getMessage();

            return require_once('view/layout/error.php');
        }

        $recipe->delete();
    }

    public function actionRecipes()
    {
        $recipeObject = new Recipe();

        $filterString = ' WHERE status = ' . DataBase::SYM_QUERY;
        $filterParams = [1];

        if (!empty($_POST['search'])) {
            $filterString .= ' AND name LIKE ' . DataBase::SYM_QUERY;
            $filterParams[] = '%' . $_POST['search'] . '%';
        }

        try {
            $recipes = $recipeObject->getList($filterString, $filterParams);

            if (!empty($_POST['cats'])) {
                $postCategories = $_POST['cats'];
                foreach ($postCategories as $key => $categoryId) {
                    if ($categoryId == 0) {
                        unset($postCategories[$key]);
                    }
                }

                if (!empty($postCategories)) {
                    foreach ($recipes as $key => $recipe) {
                        if (!empty(array_intersect($recipe->getCategories(), $postCategories))) {
                            continue;
                        }

                        unset($recipes[$key]);
                    }
                }

                if (empty($recipes)) {
                    throw new Exception('По вашему запросу ничего не найдено.');
                }
            }
        } catch (Exception $e) {
            $message = $e->getMessage();

            return require_once('view/layout/error.php');
        }

        return require_once('view/recipe/recipes.php');
    }

    public function actionFavorites()
    {
        $recipeObject = new Recipe();

        if (empty($_SESSION['userId']))  {
            header('Location: /login');
            return true;
        }

        try {
            $user = new User();
            $user->load($_SESSION['userId']);

            $favorites = !empty($user->getFavorites()) ? implode(',', $user->getFavorites()) : '';

            $recipes = $recipeObject->getList(' WHERE id IN (' . DataBase::SYM_QUERY . ')', [$favorites]);

            if (empty($recipes)) {
                throw new Exception('Список Избранного пуст.');
            }
        } catch (Exception $e) {
            $message = $e->getMessage();

            return require_once('view/layout/error.php');
        }

        return require_once('view/recipe/recipes.php');
    }

    public function actionComment($recipeId)
    {
        $user = new User();
        $user->load($_SESSION['userId']);

        $comment = new Comment();
        $comment->setText($_POST['comment']);
        $comment->setRecipeId($recipeId);
        $comment->setUserId($user->getId());

        $comment->save();

        if ($comment->getId()) {
            echo $user->getName() . ' ' . $user->getSurname();
        } else {
            echo 'error';
        }
    }

    public function actionRate($recipeId)
    {
        try {
            if (!empty($_SESSION['rates'][$recipeId])) {
                throw new Exception('Рецепт уже был оценен.');

            }

            $response = '';

            $recipe = new Recipe();
            $recipe->load($recipeId);

            if ($_POST['rate'] == 'like') {
                $likes = $recipe->getLikes() + 1;
                $recipe->setLikes($likes);
                $_SESSION['rates'][$recipeId] = 'like';

                $response = $likes;
            }

            if ($_POST['rate'] == 'dislike') {
                $dislikes = $recipe->getDislikes() + 1;
                $recipe->setDislikes($dislikes);
                $_SESSION['rates'][$recipeId] = 'dislike';

                $response = $dislikes;
            }

            $recipe->save();

            echo $response;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function addMenu()
    {
        $categoryObject = new Category();
        $categoryTypes = $categoryObject->getTypes();
        $categoryTree = $categoryObject->getCategoryTree();
        $categories = $categoryObject->getList();

        return require_once('view/layout/menu.php');
    }
}