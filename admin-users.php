<?php

use \Slim\Slim;
use \Hcode\DB;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model;
use Hcode\Model\Category;

$app->get("/admin/users", function () {

    User::verifyLogin();

    //metodo estatico que ira listar os usuarios do banco
    $users = User::listAll();

    $page = new PageAdmin();

    $page->setTpl("users", [
        "users"=>$users
    ]);
});

$app->get("/admin/users/create", function () {

    User::verifyLogin();

    $page = new PageAdmin();

    $page->setTpl("users-create");

});

//colocado acima do users-update, se não ele sempre pararia nele, ja que o path é o mesmo ate o delete
$app->get("/admin/users/:iduser/delete", function ($iduser) {

    User::verifyLogin();

    $user = new User();

    $user->get((int)$iduser);

    $user->delete();

    header("Location: /admin/users");
    exit;

});

$app->get("/admin/users/:iduser", function ($iduser) {

    User::verifyLogin();

    $user = new User();

    $user->get((int)$iduser);

    $page = new PageAdmin();

    $page->setTpl("users-update", [
        "user" => $user->getValues()
    ]);
});

$app->post("/admin/users/create", function () {

    User::verifyLogin();

    $user = new User();

    $_POST["inadmin"] = (isset($_POST["inadmin"]))? 1 : 0;

    //$_POST cria um array que vai registrar os dados do novo cadastro
    $user->setData($_POST);

    //cadastra os dados capturados anteriormente dentro do banco
    $user->save();

    header("Location: /admin/users");
    exit;

});

$app->post("/admin/users/:iduser", function ($iduser) {

    User::verifyLogin();

    $user = new User();

    $user->get((int)$iduser);

    $user->setData($_POST);

    $user->update();

    header("Location: /admin/users");
    exit;
});