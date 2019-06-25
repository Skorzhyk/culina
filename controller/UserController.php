<?php

require_once 'model/User.php';
require_once 'controller/RecipeController.php';

class UserController
{
    public function actionLogin()
    {
        if (!empty($_SESSION['userId'])) {
            header('Location: /');
            return true;
        }

        if (empty($_POST)) {
            return require_once('view/user/login.php');
        }

        try {
            $user = User::login($_POST['email'], $_POST['password']);
        } catch (Exception $e) {
            $message = $e->getMessage();

            return require_once('view/user/login.php');
        }

        $_SESSION['userId'] = $user->getId();
        $_SESSION['admin'] = $user->getAdmin();
        header('Location: /');
    }

    public function actionLogout()
    {
        session_destroy();

        header('Location: /');
    }

    public function actionRegistration()
    {
        if (!empty($_SESSION['userId'])) {
            header('Location: /');
            return true;
        }

        if (empty($_POST)) {
            return require_once('view/user/registration.php');
        }

        $user = new User();

        $user->setEmail($_POST['email']);
        $user->setPassword(password_hash($_POST['password'], PASSWORD_DEFAULT));
        $user->setName($_POST['name']);
        $user->setSurname($_POST['surname']);

        try {
            $user->save();
        } catch (Exception $e) {
            $message = $e->getMessage();

            return require_once('view/user/registration.php');
        }

        $_SESSION['userId'] = $user->getId();
        $_SESSION['admin'] = $user->getAdmin();
        header('Location: /');
    }

    public function actionAccount()
    {
        if (empty($_SESSION['userId'])) {
            header('Location: /login');
            return true;
        }

        if (empty($_POST)) {
            $user = new User();
            $user->load($_SESSION['userId']);

            return require_once('view/user/account.php');
        }

        $user = new User();
        $user->load($_SESSION['userId']);

        $user->setName($_POST['name']);
        $user->setSurname($_POST['surname']);

        if (!empty($_POST['password'])) {
            $user->setPassword(password_hash($_POST['password'], PASSWORD_DEFAULT));
        }

        $user->save();

        header('Location: /account');
    }

    public function actionFavorites($action, $recipeId)
    {
        if (empty($_SESSION['userId'])) {
            return false;
        }

        $user = new User();
        $user->load($_SESSION['userId']);

        $favorites = $user->getFavorites();

        if ($action == 'add') {
            if (!in_array($recipeId, $favorites)) {
                $favorites[] = $recipeId;
            }
        }

        if ($action == 'remove') {
            if (in_array($recipeId, $favorites)) {
                unset($favorites[array_search($recipeId, $favorites)]);
            }
        }

        $user->setFavorites($favorites);
        $user->save();
    }
}