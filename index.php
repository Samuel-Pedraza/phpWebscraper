<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;
require 'vendor/autoload.php';
include("simple_html_dom.php");
include("./models/webModels.php");




//grabs templates for rendering tables in PHP
//https://github.com/slimphp/PHP-View
$app = new \Slim\App();
$container = $app->getContainer();
$container['renderer'] = new PhpRenderer("./templates");






//--------------------------//
//         ROUTES           //
//                          //
//                          //
//--------------------------//
//Resource to help:
//https://www.slimframework.com/docs/objects/router.html


$app->get('/', function ($request, $response, $args) {
    return $this->renderer->render($response, "/home.php", $args);
});

$app->post('/vestil', function($request, $response, $args){
    $vestilWebscrapers = new Web;
    $sql = mysqli_connect("", "", "", "");
    $vestilWebscrapers->hofequipment("http://hofequipment.com/cart.php?m=search_results&search=wp-4848", "hofequipment", $sql);
});

$app->post('/littlegiant', function($request, $response, $args){

});

$app->post('/valleycraft', function($request, $response, $args){

});

$app->get('/vestil', function($request, $response, $args){
    return $this->renderer->render($response, "/table.php", $args);
});

$app->get('/edit/{id}', function($request, $response, $args){
    return $this->renderer->render($response, "/edit.php", $args);
});

$app->get('/lowest', function($request, $response, $args){
    return $this->renderer->render($response, "/lowestprice.php", $args);
});

//to update form
$app->put('/edit/{id}', function ($request, $response, $args) {
    // Update book identified by $args['id']
    $conn = mysqli_connect('', '', '', '');

    $sql = "UPDATE test_data SET price = $price WHERE id = '$id'";

    $result = mysqli_query($conn, $sql);

    mysqli_close($conn);

});

$app->run();
