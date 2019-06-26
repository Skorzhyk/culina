<?php

require_once 'model/AbstractModel.php';
require_once 'model/User.php';
require_once 'model/RecipeImage.php';
require_once 'model/Step.php';
require_once 'model/Ingredient.php';
require_once 'model/Comment.php';

class Recipe extends AbstractModel
{
    protected $tableName = 'recipe';

    protected $id;

    protected $userId;

    protected $name;

    protected $description;

    protected $categories = '';

    protected $likes = 0;

    protected $dislikes = 0;

    protected $visitNumber = 0;

    protected $createdAt;

    protected $status;

    protected $images = [];

    protected $steps = [];

    protected $ingredients = [];

    protected $comments = [];

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getCategories()
    {
        return !empty($this->categories) ? explode(',', $this->categories) : [];
    }

    public function setCategories($categories)
    {
        $this->categories = !empty($categories) ? implode(',', $categories) : '';
    }

    public function getLikes()
    {
        return $this->likes;
    }

    public function setLikes($likes)
    {
        $this->likes = $likes;
    }

    public function getDislikes()
    {
        return $this->dislikes;
    }

    public function setDislikes($dislikes)
    {
        $this->dislikes = $dislikes;
    }

    public function getVisitNumber()
    {
        return $this->visitNumber;
    }

    public function setVisitNumber($visitNumber)
    {
        $this->visitNumber = $visitNumber;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getImages()
    {
        return $this->images;
    }

    public function setImages($images)
    {
        $this->images = $images;
    }

    public function getSteps()
    {
        return $this->steps;
    }

    public function setSteps($steps)
    {
        $this->status = $steps;
    }

    public function getIngredients()
    {
        return $this->ingredients;
    }

    public function setIngredients($ingredients)
    {
        $this->ingredients = $ingredients;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    public function getForEditing($id)
    {
        $this->load($id);

        if (empty($_SESSION['userId']) || (empty($this->getId()) && $_SESSION['admin'] == 0 && ($this->getUserId() != $_SESSION['userId']))) {
            throw new Exception('Недостаточно прав для редактирования данной записи.');
        }

        return $this;
    }

    public function save()
    {
        if (empty($this->getId())) {
            $newId = $this->db->query(
                "INSERT INTO " . $this->tableName . " (user_id, name, description, categories, likes, dislikes, visit_number, status)
            VALUES (" . DataBase::SYM_QUERY . ", " . DataBase::SYM_QUERY . ", " . DataBase::SYM_QUERY . ", " . DataBase::SYM_QUERY . ", " . DataBase::SYM_QUERY . ", " . DataBase::SYM_QUERY . ", " . DataBase::SYM_QUERY . ", " . DataBase::SYM_QUERY . ")",
                [$this->userId, $this->name, $this->description, $this->categories, $this->likes, $this->dislikes, $this->visitNumber, $this->status]
            );

            if ($newId === false) {
                throw new Exception('Не удалось сохранить рецепт.');
            }

            $this->setId($newId);
        } else {
            $this->db->query(
                "UPDATE " . $this->tableName . " SET name = " . DataBase::SYM_QUERY . ", description = " . DataBase::SYM_QUERY . ", categories = " . DataBase::SYM_QUERY . ", likes = " . DataBase::SYM_QUERY . ", dislikes = " . DataBase::SYM_QUERY . ", visit_number = " . DataBase::SYM_QUERY . ", status = " . DataBase::SYM_QUERY . " WHERE id = " . DataBase::SYM_QUERY,
                [$this->name, $this->description, $this->categories, $this->likes, $this->dislikes, $this->visitNumber, $this->status, $this->id]
            );
        }

        if (!empty($_FILES)) {
            foreach ($_FILES['image']['name'] as $key => $value) {
                $fileData = $this->prepareFile(
                    $_FILES['image']['name'][$key],
                    $_FILES['image']['error'][$key],
                    $_FILES['image']['tmp_name'][$key]
                );
                $newFileName = $this->uploadFile($fileData, 'view/web/images/recipes/' . $this->getId() . '/');

                if ($newFileName) {
                    if (!empty($this->images[$key])) {
                        $this->images[$key]->setImage($newFileName);
                        $this->images[$key]->save();
                    } else {
                        $image = new RecipeImage();
                        $image->setRecipeId($this->getId());
                        $image->setOrderNumber($key);
                        $image->setImage($newFileName);
                        $image->save();

                        $this->images[$key] = $image;
                    }
                }
            }
        }

        if (!empty($_POST['image']['delete'])) {
            foreach ($_POST['image']['delete'] as $imageOrderNumber => $isDelete) {
                if (!empty($this->images[$imageOrderNumber])) {
                    $this->images[$imageOrderNumber]->delete();
                    unset($this->images[$imageOrderNumber]);
                }
            }
        }

        $steps = [];
        if (!empty($_POST['step'])) {
            foreach ($_POST['step'] as $key => $stepData) {
                $step = new Step();

                if (!empty($this->steps[$key])) {
                    $step = $this->steps[$key];
                } else {
                    $step->setRecipeId($this->getId());
                    $step->setOrderNumber($key);
                }

                $step->setDescription($stepData['description']);

                if (!empty($stepData['image']['delete'])) {
                    $step->setImage('');
                }
                if (!empty($stepData['video']['delete'])) {
                    $step->setVideo('');
                }

                if (!empty($_FILES)) {
                    $fileData = $this->prepareFile(
                        $_FILES['step']['name'][$key]['image'],
                        $_FILES['step']['error'][$key]['image'],
                        $_FILES['step']['tmp_name'][$key]['image']
                    );
                    $newStepImageName = $this->uploadFile($fileData, 'view/web/images/recipes/' . $this->getId() . '/steps/');

                    $fileData = $this->prepareFile(
                        $_FILES['step']['name'][$key]['video'],
                        $_FILES['step']['error'][$key]['video'],
                        $_FILES['step']['tmp_name'][$key]['video']
                    );
                    $newStepVideoName = $this->uploadFile($fileData, 'view/web/images/recipes/' . $this->getId() . '/steps/');

                    if (!empty($newStepImageName)) {
                        $step->setImage($newStepImageName);
                    }
                    if (!empty($newStepVideoName)) {
                        $step->setVideo($newStepVideoName);
                    }
                }

                $step->save();
                $steps[] = $step;
            }

            if (!empty($this->steps)) {
                foreach ($this->steps as $oldStep) {
                    $isDelete = true;
                    foreach ($steps as $newStep) {
                        if ($oldStep->getId() == $newStep->getId()) {
                            $isDelete = false;
                            break;
                        }
                    }

                    if ($isDelete) {
                        $oldStep->delete();
                    }
                }
            }

            $this->steps = [];
            foreach ($steps as $step) {
                $this->steps[$step->getOrderNumber()] = $step;
            }
        }

        if (!empty($_POST['ingredients'])) {
            $ingredientObject = new Ingredient();

            foreach ($_POST['ingredients'] as $ingredient) {
                if (!empty($ingredient['delete'])) {
                    if (!empty($ingredient['id'])) {
                        $ingredientObject->deleteFromRecipe($ingredient['id']);
                    }

                    continue;
                }

                if (empty($ingredient['ingredient_id'])) {
                    $newIngredient = new Ingredient();
                    $newIngredient->setName($ingredient['name']);
                    $newIngredient->setUserId($_SESSION['userId']);

                    $newIngredient->save();

                    $ingredient['ingredient_id'] = $newIngredient->getId();
                }

                $map = [];

                $map['recipe_id'] = $this->getId();
                $map['ingredient_id'] = $ingredient['ingredient_id'];
                $map['amount'] = $ingredient['amount'];

                if (!empty($ingredient['id'])) {
                    $map['id'] = $ingredient['id'];
                }

                $ingredientObject->addToRecipe($map);
            }
        }
    }

    private function prepareFile($fileName, $fileError, $fileTmp)
    {
        return [
            'fileName' => $fileName,
            'fileError' => $fileError,
            'fileTmp' => $fileTmp
        ];
    }

    private function uploadFile($fileData, $destination)
    {
        if ($fileData['fileError'] == 0) {
            $extension = strtolower(substr(strrchr($fileData['fileName'], '.'), 1));
            $newFileName = uniqid() . '.' . $extension;

            if (!file_exists($destination)) {
                mkdir($destination);
            }
            move_uploaded_file($fileData['fileTmp'], $_SERVER['DOCUMENT_ROOT'] . '/' . $destination . $newFileName);

            return $newFileName;
        }

        return false;
    }

    public function load($value, $field = 'id')
    {
        parent::load($value, $field);

        if (empty($this->getId())) {
            throw new Exception('Запись с указанным идентификатором не найдена.');
        }

        $recipeImageObject = new RecipeImage();
        $images = $recipeImageObject->getList(' WHERE recipe_id = ' . DataBase::SYM_QUERY, [$this->getId()]);

        if (!empty($images)) {
            foreach ($images as $image) {
                $this->images[$image->getOrderNumber()] = $image;
            }
        }

        $stepObject = new Step();
        $steps = $stepObject->getList(' WHERE recipe_id = ' . DataBase::SYM_QUERY, [$this->getId()]);

        if (!empty($steps)) {
            foreach ($steps as $step) {
                $this->steps[$step->getOrderNumber()] = $step;
            }
        }

        $ingredientObject = new Ingredient();
        $ingredients = $ingredientObject->getRecipeIngredients($this->getId());
        $this->setIngredients($ingredients);

        $commentObject = new Comment();
        $comments = $commentObject->getList(' WHERE recipe_id = ' . DataBase::SYM_QUERY, [$this->getId()]);
        $this->setComments($comments);
    }

    public function getMainImage()
    {
        if (!empty($this->getImages())) {
            foreach ($this->getImages() as $image) {
                return $image->getImage();
            }
        }

        return null;
    }

    public function getList($filter = '', $filterParams = [])
    {
        $data = parent::getList($filter, $filterParams);

        if (empty($data)) {
            throw new Exception('По вашему запросу ничего не найдено.');
        }

        $recipes = [];
        foreach ($data as $key => $item) {
            $recipe = new self();
            $recipe->load($key);

            $recipes[$recipe->getId()] = $recipe;
        }

        return $recipes;
    }
}