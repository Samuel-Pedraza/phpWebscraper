<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;

require 'vendor/autoload.php';

include("simple_html_dom.php");
include("./models/webModels.php");


//grabs templates for rendering tables in PHP
//https://github.com/slimphp/PHP-View

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$c = new \Slim\Container($configuration);
$app = new \Slim\App($c);
$container = $app->getContainer();
$container['renderer'] = new PhpRenderer("./templates");

//--------------------------//
//                          //
//                          //
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

    //FIX
    //Warning: mysqli_query() expects parameter 1 to be mysqli, null given in
    //https://stackoverflow.com/questions/18933107/warning-mysqli-query-expects-parameter-1-to-be-mysqli-null-given

    // $vestilWebscrapers->zoro();

    // Create connection
    $sql = mysqli_connect('66.112.76.254', '', '', 'sams_test_database');

    // $vestilWebscrapers->fastenal();
    //
    $vestilWebscrapers->hofequipment("http://hofequipment.com/cart.php?m=search_results&catID=&venID=1&search=&shopByPrice=&sortBy=&viewAll=1", "hofequipment", "vestil", mysqli_connect('66.112.76.254', '', '', 'sams_test_database'));
    echo "hofequipment finished";

    $vestilWebscrapers->industrialsafety("https://industrialsafety.com/catalogsearch/result/index/?p=1&product_list_limit=80&q=vestil", "industrialsafety", 14, "vestil", mysqli_connect('66.112.76.254', '', '', 'sams_test_database'));
    echo "industrialsafety finished";

    $vestilWebscrapers->toolfetch("https://www.toolfetch.com/by-brand/vestil/l/brand:vestil.html?limit=48&p=1", "toolfetch", 172, "vestil", mysqli_connect('66.112.76.254', '', '', 'sams_test_database'));
    echo "toolfetch finished";

    $vestilWebscrapers->opentip("https://www.opentip.com/search.php?brand=35098&keywords=vestil&page=1", "opentip", 305,"vestil", mysqli_connect('66.112.76.254', '', '', 'sams_test_database'));
    echo "opentip finished";

    $vestilWebscrapers->globalindustrial("http://www.globalindustrial.com/shopByBrandName/V/vestil-manufacturing?cp=1&ps=72", "globalindustrial", 34,"vestil", mysqli_connect('66.112.76.254', '', '', 'sams_test_database'));
    echo "globalindustrial finished";

    $vestilWebscrapers->source4industries("https://source4industries.com/index.php?route=product/search&search=vestil", "source4industries", "vestil", mysqli_connect('66.112.76.254', '', '', 'sams_test_database'));
    echo "source4industries finished";

    $vestilWebscrapers->spill911("https://www.spill911.com/mm5/merchant.mvc?Screen=SRCH2&Store_Code=spill911&search=vestil&searchoffset=0&Category_Code=&filter_cat=&PowerSearch_Begin_Only=&sort=&range_low=&range_hig=&customfield1=brand&filter_cf1=&customfield2=&filter_cf2=&customfield3=&filter_cf3=&psboost=srchkeys%2Ccode%2Cname&filter_price=&priceranges=1", "spill911", "vestil", mysqli_connect('66.112.76.254', '', '', 'sams_test_database'));
    echo "spill911 finished";

    $vestilWebscrapers->custommhs("https://www.custommhs.com/index.php?route=product/manufacturer&manufacturer_id=42&page=1", "custommhs", 1, "vestil", mysqli_connect('66.112.76.254', '', '', 'sams_test_database'));
    echo "custommhs finished";

    $vestilWebscrapers->sodyinc("http://www.sodyinc.com/index.php?main_page=index&manufacturers_id=2&sort=20a&page=1", "sodyinc", 13, "vestil", mysqli_connect('66.112.76.254', '', '', 'sams_test_database'));
    echo "sodyinc finished";

});

$app->post('/littlegiant', function($request, $response, $args){
    $little_giant = new Web;

    //FIX
    //Warning: mysqli_query() expects parameter 1 to be mysqli, null given in
    //https://stackoverflow.com/questions/18933107/warning-mysqli-query-expects-parameter-1-to-be-mysqli-null-given

    // Create connection
    $sql = mysqli_connect('', '', '', 'sams_test_database');

    $little_giant->globalindustrial("http://www.globalindustrial.com/shopByBrandName/L/little-giant", "globalindustrial", 3, $sql);
    echo "globalindustrial finished";

    $little_giant->source4industries("https://source4industries.com/index.php?route=product/manufacturer/info&manufacturer_id=31&limit=327", "source4industries", $sql);
    echo "source4industries finished";

    $little_giant->spill911("https://www.spill911.com/mm5/merchant.mvc?Screen=SRCH2&Store_Code=spill911&search=little+giant&searchoffset=0&Category_Code=&filter_cat=&PowerSearch_Begin_Only=&sort=&range_low=&range_high=&customfield1=brand&filter_cf1=Little+Giant&customfield2=&filter_cf2=&customfield3=&filter_cf3=&psboost=&filter_price=&priceranges=1", "spill911", mysqli_connect('', '', '', 'sams_test_database'));
    echo "spill911 finished";

    //will not work for multiple pages
    $little_giant->custommhs("https://www.custommhs.com/index.php?route=product/manufacturer&manufacturer_id=42", "custommhs", mysqli_connect('', '', '', 'sams_test_database'));
    echo "custommhs finished";

    $little_giant->sodyinc("http://www.sodyinc.com/little-giant?sort=20a&page=1", "sodyinc", 51, mysqli_connect('', '', '', 'sams_test_database'));
    echo "sodyinc finished";



});

$app->post('/valleycraft', function($request, $response, $args){
    $valleycraftWebscraper = new Web;

    //FIX
    //Warning: mysqli_query() expects parameter 1 to be mysqli, null given in
    //https://stackoverflow.com/questions/18933107/warning-mysqli-query-expects-parameter-1-to-be-mysqli-null-given


    // Create connection
    $sql = mysqli_connect('', '', '', 'sams_test_database');

    $valleycraftWebscraper->globalindustrial("http://www.globalindustrial.com/shopByBrandName/V/vestil-manufacturing?cp=1&ps=72", "globalindustrial", 34, $sql);
    echo "globalindustrial finished";

    $valleycraftWebscraper->source4industries("https://source4industries.com/index.php?route=product/search&search=vestil", "source4industries", $sql);
    echo "source4industries finished";

});

$app->get('/edit/{id}', function($request, $response, $args){
    return $this->renderer->render($response, "/edit.php", $args);
});

$app->get('/lowest', function($request, $response, $args){
    return $this->renderer->render($response, "/lowestprice.php", $args);
});

$app->post('/lowest', function($request, $response, $args){
    return $this->renderer->render($response, "/lowestprice.php", $args);
});

//to update form
$app->put('/edit/{id}', function ($request, $response, $args) {
    // Update book identified by $args['id']
    $conn = mysqli_connect('', '', '', 'sams_test_database');

    $sql = "UPDATE test_data SET price = $price WHERE id = '$id'";

    $result = mysqli_query($conn, $sql);

    mysqli_close($conn);

});

$app->run();
