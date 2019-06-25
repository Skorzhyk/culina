<?php

return [
    '^$' => 'recipe/index',
    'recipes/([0-9]+)' => 'recipe/view/$1',
    'recipes/favorites' => 'recipe/favorites',
    'recipes' => 'recipe/recipes',
    'recipe/edit/([0-9]+)' => 'recipe/edit/$1',
    'recipe/edit' => 'recipe/edit',
    'recipe/view/([0-9]+)' => 'recipe/view/$1',
    'recipe/delete/([0-9]+)' => 'recipe/delete/$1',
    'recipe/comment/([0-9]+)' => 'recipe/comment/$1',
    'recipe/rate/([0-9]+)' => 'recipe/rate/$1',
    'login' => 'user/login',
    'logout' => 'user/logout',
    'account' => 'user/account',
    'user/favorites' => 'user/favorites',
    'registration' => 'user/registration',
    'list/([0-9]+)/([0-9]+)' => 'recipe/list/$1/$2',
    'list/([0-9]+)' => 'recipe/list/$1',
    'list' => 'recipe/list'
];