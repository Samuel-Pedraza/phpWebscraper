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
    $myWebscraper->globalindustrial();
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
    $conn = mysqli_connect('66.112.76.254', '', '', 'sams_test_database');

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


class Vestilwebscraper {

    function sqlQuery($sku, $price, $website, $url, $conn){
         $result = mysqli_query($conn, "SELECT * FROM test_data WHERE sku = '" . $sku . "' AND website = '" . $website . "' " );

         if($result){
             mysqli_query($conn, "UPDATE test_data SET price = $price WHERE website = '$website' AND sku = '$sku' " );
             echo "updated price <br />";
         } else {
             mysqli_query($conn, "INSERT INTO test_data(sku, price, website, url) VALUES ($sku, $price, $website, $url)");
             echo "created price <br />";
         }
     }

    function hofequipment() {

         //necessary so that connection does not time out when webscraping
         set_time_limit(0);

         $conn = mysqli_connect('66.112.76.254', '', '', 'sams_test_database');

         //**need to rewrite**//
         if(!$conn) {
             echo 'Failed to Connect';
         }

         //instantiates object of simple_html_dom(simplehtmldom.sourceforge.net)
         $html = new simple_html_dom();

         $html->load_file("http://hofequipment.com/cart.php?m=search_results&search=FOLD-UP+ALUMINUM+PLATFORM+TRUCK");

         //$query is returned as an array
         $query = $html->find(".grid__item div.btn-group a");

         foreach ($query as $key) {
             //filters out the $key->href that contains the word 'javascript' --- we only want hrefs that are actual links
             if(!(preg_match('/javascript/', $key->href))){
                 //reinstantiates object of simple_html_dom(simplehtmldom.sourceforge.net)

                 //**need to rewrite**//
                 $grabProducts = new simple_html_dom();

                 $grabProducts->load_file($key->href);

                 //**need to rewrite**//
                 if($grabProducts->find("table.responsive_tables tbody tr") == True){
                     foreach ($grabProducts->find("table.responsive_tables tbody tr") as $tr) {


                         //productInfoArray holds two values
                         //[0] holds the sku number for a given product
                         //[1] hold the price for a given product
                         $productInfoArray = [];

                         foreach ($tr->find("td[data-title=SKU]") as $sku) {
                             array_push($productInfoArray, $sku->innertext);
                         }

                         foreach ($tr->find("td[data-title=Price]") as $price) {
                             //takes out extra formatting that is not needed nor wanted
                             $editedPrice = preg_replace("/[(),$]/", "", $price->innertext);
                             array_push($productInfoArray, $editedPrice);
                         }

                         //grabs first element in array
                         $skuNumber = current($productInfoArray);

                         //grabs second element in array
                         $price     = next($productInfoArray);

                         //**need to rewrite**//
                         $website   = "hofequipment";

                         if($skuNumber && $price){
                             //referes to sqlQuery -- cannot call sqlQuery(a,b,c,d);
                             $this->sqlQuery($skuNumber, $price, $website, $key->href);
                         }
                     }

                 } else {

                     $productInfoArray = [];

                     foreach ($grabProducts->find("span[itemprop=sku]") as $sku) {
                         array_push($productInfoArray, $sku->innertext);
                     }

                     foreach ($grabProducts->find("div.item-price--product") as $price) {
                         $editedPrice = preg_replace("/[(),$]/", "", $price->innertext);
                         array_push($productInfoArray, $editedPrice);
                     }

                     //first element in array
                     $skuNumber = current($productInfoArray);

                     //second element in array
                     $price     = next($productInfoArray);

                     //**need to rewrite**//
                     $website  = "hofequipment";

                     if($skuNumber && $price){
                         $this->sqlQuery($skuNumber, $price, $website, $key->href);
                     }
                 }
             }
         }
         mysqli_close($conn);
     }

    function industrialsafety(){
        set_time_limit(0);

        $conn = mysqli_connect('66.112.76.254', '', '', 'sams_test_database');

        for($pages = 1; $pages <= 14; $pages++){
            $html = new simple_html_dom();

            $html->load_file("https://industrialsafety.com/catalogsearch/result/index/?p=1&product_list_limit=80&product_list_order=name&q=vestil");
            sleep(3);
            $query = $html->find("div.products-grid .grid-product-type li");

            foreach ($query as $product) {

                $infoArray = [];

                foreach ($product->find(".product-item-link") as $sku) {
                    $mySku = trim($sku->plaintext);
                    $skuArray = explode(" ", $mySku);

                    array_push($infoArray, $skuArray[1]); //[0] skuNum
                    array_push($infoArray, $sku->href); //[1] href
                }

                foreach($product->find(".price-wrapper .price") as $price){
                    $mprice = preg_replace("/[(),$]/", "", $price->innertext);
                    array_push($infoArray, $mprice); //[2] price
                }

                $skuNumber = $infoArray[0];
                $price     = $infoArray[2];
                $website   = "industrialsafety";
                $url       = $infoArray[1];

                $this->sqlQuery($skuNumber, $price, $website, $url);
            }
        }
        mysqli_close($conn);

    }

    function toolfetch(){

        $conn = mysqli_connect('66.112.76.254', '', '', 'sams_test_database');


        for($j = 1; $j <= 142; $j++){
            //step1
            set_time_limit(0);
            $cSession = curl_init();
            //step2
            curl_setopt($cSession,CURLOPT_URL,"http://www.toolfetch.com/by-brand/vestil/l/brand:vestil.html?limit=48&p=1");
            curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($cSession,CURLOPT_HEADER, false);
            //step3
            $result=curl_exec($cSession);
            //step4
            curl_close($cSession);
            //step5
            $html = new simple_html_dom();
            $mywebsite = $html->load($result);

            $array = $mywebsite->find("ul.products-grid li a.product-image");

            foreach ($array as $key) {

                $mysession = curl_init();
                //step2
                curl_setopt($mysession, CURLOPT_URL, $key->href);
                curl_setopt($mysession, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($mysession, CURLOPT_HEADER, false);
                //step3
                $myresult=curl_exec($mysession);
                //step4
                curl_close($mysession);

                $myhtml = new simple_html_dom();
                $thiswebsite = $html->load($myresult);

                $informationforgivenpage = $thiswebsite->find(".add-to-box .price", 0)->plaintext;
                $information = preg_replace("/[(),$]/", "", $informationforgivenpage);

                $productid = $thiswebsite->find(".product-ids", 0)->plaintext;
                $modelNumber = preg_replace("/Part# VES-/", "", $productid);

                $myArray = [];
                $sku = $modelNumber;
                $price = $information;
                $url = $key->href;
                $website = "toolfetch";

                $this->sqlQuery($sku, $price, $website, $url);

            }
            mysqli_close($conn);
     }
    }

    function opentip(){

       for($i = 1; $i <= 74; $i++){

           $conn = mysqli_connect('66.112.76.254', '', '', 'sams_test_database');

            set_time_limit(0);

            $html = new simple_html_dom();
            $html->load_file("https://www.opentip.com/search.php?keywords=vestil&limit=100&page=" . $i);

            $card = $html->find(".item-detail");

            sleep(3);

            foreach ($card as $key) {

                $sku = $key->find(".products_sku span", 0)->plaintext;

                $skuNumber = preg_replace("/SKU: /", "", $sku);

                $price = $key->find(".products_price", 0)->plaintext;

                $myPrice = preg_replace("/[(),$]/", "", $price);

                $href = $key->find(".data a.title", 0)->href;

                $website = "opentip";


                $this->sqlQuery($skuNumber, $myPrice, $website, $href, $conn);

            }
        }
        mysqli_close($conn);
    }

}

/**
 *
 */
class LittleGiant
{
    function globalindustrial(){
        set_time_limit(0);

        $html = new simple_html_dom();

        for ($i=1; $i < 2; $i++) {
            # code...
            $html->load_file("http://www.globalindustrial.com/shopByBrandName/L/little-giant?cp=" . $i . "&ps=110");

            $url = $html->find(".grid .prod .title a");

            foreach ($url as $key => $value) {
                # code...

                $new_webpage = new simple_html_dom();

                $new_webpage->load_file("http://www.globalindustrial.com/" . $value->href );

                $price = $new_webpage->find("span[itemprop=price]");

                $sku = $new_webpage->find(".prodSpec ul li ul li span");

                // foreach ($price as $index => $myprice) {
                //     echo $myprice;
                // }

                foreach ($sku as $skuNumber => $valueNumber) {
                    # code...
                        if($valueNumber->plaintext == "MODEL"){
                            echo $valueNumber->plaintext;
                        }
                }

                echo "<br />";

            }
        }
    }

}
