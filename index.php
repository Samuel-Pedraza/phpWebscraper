<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;

require 'vendor/autoload.php';

include("simple_html_dom.php");
include("./controllers/littleGiantController.php");
include("./controllers/vestilControllers.php");

$app = new \Slim\App();

//grabs templates for rendering tables in PHP
//https://github.com/slimphp/PHP-View
$container = $app->getContainer();
$container['renderer'] = new PhpRenderer("./templates");

//--------------------------//
// Routing in SLIM framework//
//https://www.slimframework.com/docs/objects/router.html
//--------------------------//

$app->get('/', function ($request, $response, $args) {
    return $this->renderer->render($response, "/home.php", $args);
});

$app->post('/', function($request, $response, $args){
    $myWebscraper = new LittleGiant;
    $myWebscraper->industrialproducts();
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
    $conn = mysqli_connect('66.112.76.254', '', '', 'sams_test_database');

    $sql = "UPDATE test_data SET price = $price WHERE id = '$id'";

    $result = mysqli_query($conn, $sql);

    mysqli_close($conn);


});

$app->run();
