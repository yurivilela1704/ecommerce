<?php

use \Slim\Slim;
use \Hcode\DB;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model;
use Hcode\Model\Category;

$app->get("/admin/forgot", function () {

    $page = new PageAdmin([
        "header"=>false,
        "footer" => false
    ]);

    $page->setTpl("forgot");

});

$app->post("/admin/forgot", function () {

    $user  = User::getForgot($_POST["email"]);

    header("Location: /admin/forgot/sent");
    exit;

});

$app->get("/admin/forgot/sent", function () {

    $page = new PageAdmin([
        "header"=>false,
        "footer" => false
    ]);

    $page->setTpl("forgot-sent");

});

$app->get("/admin/forgot/reset", function () {

    $user = User::validForgotDecrypt($_GET["code"]);

    $page = new PageAdmin([
        "header"=>false,
        "footer" => false
    ]);

    $page->setTpl("forgot-reset", [
        "name"=>$user["desperson"],
        "code"=>$_GET["code"]
    ]);
});

$app->post("/admin/forgot/reset", function ($user) {

    $forgot = User::validForgotDecrypt($_POST["code"]);

    User::setForgotUsed($user["idrecovery"]);

    $user = new User();

    $user->get((int)$forgot["iduser"]);

    $password = password_hash($_POST["password"], PASSWORD_DEFAULT, array("cost"=>12));

    $user->setPassword($password);

    $page = new PageAdmin([
        "header"=>false,
        "footer" => false
    ]);

    $page->setTpl("forgot-reset-sucess");

});