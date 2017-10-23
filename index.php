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
//                          //
//         ROUTES           //
//                          //
//--------------------------//
//Resource to help:
//https://www.slimframework.com/docs/objects/router.html


$app->get('/', function ($request, $response, $args) {
    return $this->renderer->render($response, "/home.php", $args);
});

$app->post('/vestil', function($request, $response, $args){
    $vestilWebscrapers = new Web;
    $sql = mysqli_connect('66..76.254', '', '', '');

    $vestilWebscrapers->hofequipment("http://hofequipment.com/cart.php?m=search_results&search=wp-4848", "hofequipment", "vestil_products", $sql);
    echo "hofequipment finished";

    $vestilWebscrapers->industrialsafety("https://industrialsafety.com/catalogsearch/result/index/?p=1&product_list_limit=80&q=vestil", "industrialsafety", 14, $sql, "vestil_products");
    echo "industrialsafety finished";

    $vestilWebscrapers->toolfetch("https://www.toolfetch.com/by-brand/vestil/l/brand:vestil.html?limit=48&p=1", "toolfetch", 172, $sql, "vestil_products");
    echo "toolfetch finished";

    $vestilWebscrapers->opentip("https://www.opentip.com/search.php?brand=35098&keywords=vestil&page=1", "opentip", 305, $sql, "vestil_products");
    echo "opentip finished";

    $vestilWebscrapers->globalindustrial("http://www.globalindustrial.com/shopByBrandName/V/vestil-manufacturing?cp=1&ps=72", "globalindustrial", 34, $sql, "vestil_products");
    echo "globalindustrial finished";

    $vestilWebscrapers->source4industries("https://source4industries.com/index.php?route=product/search&search=vestil", "source4industries", $sql, "vestil_products");
    echo "source4industries finished";

    $vestilWebscrapers->spill911("https://www.spill911.com/mm5/merchant.mvc?Screen=SRCH2&Store_Code=spill911&search=vestil&searchoffset=0&Category_Code=&filter_cat=&PowerSearch_Begin_Only=&sort=&range_low=&range_high=&customfield1=brand&filter_cf1=&customfield2=&filter_cf2=&customfield3=&filter_cf3=&psboost=srchkeys%2Ccode%2Cname&filter_price=&priceranges=1", "spill911", $sql, "vestil_products");
    echo "spill911 finished";

    $vestilWebscrapers->custommhs("https://www.custommhs.com/index.php?route=product/manufacturer&manufacturer_id=42", "custommhs", $sql, "vestil_products");
    echo "custommhs finished";

    $vestilWebscrapers->sodyinc("http://www.sodyinc.com/index.php?main_page=index&manufacturers_id=2&sort=20a&page=1", "sodyinc", 13, $sql, "vestil_products");
    echo "sodyinc finished";

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
