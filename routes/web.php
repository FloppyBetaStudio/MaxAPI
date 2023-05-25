<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/



$router->get('/', function () use ($router) {
    return "";
});

$router->post("/auth/login", ["uses" => "AuthController@login" ]);
$router->get("/money", ["middleware" => ["auth"], "uses" => "PlayerController@GetBalance"]);
$router->post("/money/pay/{targetUsername}", ["middleware" => ["auth"], "uses" => "PlayerController@PayMoney"]);
$router->post("/auth/changePassword", ["middleware" => "auth", "uses" => "AuthController@changePassword"]);
$router->get("/public/playerList", ["uses" => "PlayerController@PlayerList"]);
$router->post("/game/auth", ["middleware" => ["auth"], "uses" => "AuthController@loginGame"]);
$router->delete("/auth/login", ["middleware" => ["auth"], "uses" =>"AuthController@logoutAll"]);
