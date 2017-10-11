<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;

require 'vendor/autoload.php';

include("simple_html_dom.php");

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
    $myWebscraper->source4industries();
});

$app->get('/vestil', function($request, $response, $args){
    return $this->renderer->render($response, "/table.php", $args);
});

$app->get('/edit/{id}', function($request, $response, $args){
    return $this->renderer->render($response, "/edit.php", $args);
});


//to update form
$app->put('/edit/{id}', function ($request, $response, $args) {
    // Update book identified by $args['id']
    $conn = mysqli_connect('66.112.76.254', 'root', 'adamserver5', 'sams_test_database');

    $sql = "UPDATE test_data SET price = $price WHERE id = '$id'";

    $result = mysqli_query($conn, $sql);

    mysqli_close($conn);


});

$app->run();




//----------------------------------------------------------------------------------------------------
//  **VESTIL CLASS**
//
//  dependencies:
//    simple_html_dom -> simplehtmldom.sourceforge.net
//
//  functions:
//    named after website
//
//  sqlQuery:
//    *arguments*      - takes a $skuNumber(string), $price(decimal), $website(string), $url(string), $conn(variable defined as the mysqli_connect)
//    *functionality*  - takes arguments, and then executes a query to see if record exisits. if exisits, it updates the price. if it does not exist, it creates the record.
//-----------------------------------------------------------------------------------------------------




    // walmart -> https://developer.walmartlabs.com/docs

    //fastenal -> https://www.fastenal.com/products?term=Little+Giant%5BREG%5D&r=~%7Cmanufacturer:%5E%22Little%20Giant[REG]%22$%7C~&pageno=1

    //northern equipment -> http://www.northerntool.com/shop/tools/category_little-giant http://www.northerntool.com/shop/tools/category_little-giant-hand-truck http://www.northerntool.com/shop/tools/category_little-giant-ladder

    // zoro --> https://www.zoro.com/search?q=little+giant&brand=LITTLE+GIANT&page=2

    // sodyinc --> http://www.sodyinc.com/little-giant?zenid=8jgptlkbvjb37gp72f9hfnqmr7

    // shop.com --> http://developer.shop.com/

    //hayneedle --> https://search.hayneedle.com/search/index.cfm?categoryId=0&selectedFacets=Brand%7CLittle%2520Giant~%5E&page=1&sortBy=preferred&checkCache=true&qs=&fm=&pageType=SEARCH&view=48&Ntt=little%20giant

    //source4industries --> https://source4industries.com/index.php?route=product/manufacturer/info&manufacturer_id=31

    //metalcabinetstore --> http://metalcabinetstore.com/shopping/shopdisplayproducts.asp?Search=Yes&sppp=21&page=1&category=ALL&highprice=0&lowprice=0&allwords=little%20giant&exact=&atleast=&without=&cprice=&searchfields=

    //bizchair --> https://www.bizchair.com/search?q=little%20giant&prefn1=brand&prefv1=Little%20Giant

     //industrialproducts.com,

      //bmhequipment.com


}
