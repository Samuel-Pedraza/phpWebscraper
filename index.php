<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;

require 'vendor/autoload.php';

include("simple_html_dom.php");

$app = new \Slim\App();
$container = $app->getContainer();
$container['renderer'] = new PhpRenderer("./templates");

$app->get('/', function ($request, $response, $args) {
    return $this->renderer->render($response, "/home.php", $args);
});

$app->post('/', function($request, $response, $args){
    $myWebscraper = new Vestilwebscraper;

    $myWebscraper->opentip();

});

$app->run();

class Vestilwebscraper {

    function hofequipment() {

        set_time_limit(0);
    
        if(!$conn) {
        	echo 'Failed to Connect';
        }

        $html = new simple_html_dom();

        $html->load_file("http://hofequipment.com/cart.php?m=search_results&search=FOLD-UP+ALUMINUM+PLATFORM+TRUCK");

        $query = $html->find(".grid__item div.btn-group a");

        foreach ($query as $key) {
            if(!(preg_match('/javascript/', $key->href))){
                $grabProducts = new simple_html_dom();

                $grabProducts->load_file($key->href);

                if($grabProducts->find("table.responsive_tables tbody tr") == True){
                    foreach ($grabProducts->find("table.responsive_tables tbody tr") as $tr) {

                        $productInfoArray = [];

                        foreach ($tr->find("td[data-title=SKU]") as $sku) {

                            array_push($productInfoArray, $sku->innertext);
                        }

                        foreach ($tr->find("td[data-title=Price]") as $price) {
                            $mprice = preg_replace("/[(),$]/", "", $price->innertext);


                            array_push($productInfoArray, $mprice);
                        }

                        $skuNumber = current($productInfoArray);
                        $price     = next($productInfoArray);
                        $website   = "hofequipment";
                        $url       = $key->href;

                        if($skuNumber && $price){
                            echo $skuNumber . " " . $price . " " . $website . " " . $url . " <br />";

                            mysqli_query($conn, "SELECT * FROM vestil_products");
                            mysqli_query($conn, "INSERT INTO vestil_products(sku, price, website, url)
                                                 VALUES ('$skuNumber', '$price', '$website', '$url')");
                        }
                    }

                } else {

                    $productInfoArray = [];

                    foreach ($grabProducts->find("span[itemprop=sku]") as $sku) {
                        array_push($productInfoArray, $sku->innertext);
                    }

                    foreach ($grabProducts->find("div.item-price--product") as $price) {
                        $mprice = preg_replace("/[(),$]/", "", $price->innertext);


                        array_push($productInfoArray, $mprice);
                    }

                    $skuNumber = current($productInfoArray);
                    $price     = next($productInfoArray);
                    $website   = "hofequipment";
                    $url       = $key->href;

                    if($skuNumber && $price){
                        echo $skuNumber . " " . $price . " " . $website . " " . $url . " <br />";

                        print_r($productInfoArray);

                        mysqli_query($conn,"SELECT * FROM vestil_products");
                        mysqli_query($conn, "INSERT INTO vestil_products(sku, price, website, url)
                                             VALUES ('$skuNumber', '$price', '$website', '$url')");

                    }
                }
            }
        }
        mysqli_close($conn);
    }

    function industrialsafety(){
        set_time_limit(0);

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

                mysqli_query($conn, "SELECT * FROM test_data");
                mysqli_query($conn, "INSERT INTO test_data(sku, price, website, url)
                                     VALUES ('$skuNumber', '$price', '$website', '$url')");
            }
        }

        mysqli_close($conn);

    }

    function toolfetch(){


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

                mysqli_query($conn, "SELECT * FROM test_data");
                mysqli_query($conn, "INSERT INTO test_data(sku, price, website, url) VALUES ('$sku', '$price', '$website', '$url')");

            }
            mysqli_close($conn);
     }
    }

    function opentip(){

       for($i = 1; $i <= 74; $i++){

            set_time_limit(0);

            $html = new simple_html_dom();
            $html->load_file("https://www.opentip.com/search.php?keywords=vestil&limit=100&page=1");

            $card = $html->find(".item-detail");

            sleep(3);

            foreach ($card as $key) {

                $sku = $key->find(".products_sku span", 0)->plaintext;

                $skuNumber = preg_replace("/SKU: /", "", $sku);

                $price = $key->find(".products_price", 0)->plaintext;

                $myPrice = preg_replace("/[(),$]/", "", $price);

                $href = $key->find(".data a.title", 0)->href;

                $website = "opentip";

                mysqli_query($conn, "SELECT * FROM test_data");
                mysqli_query($conn, "INSERT INTO test_data(sku, price, website, url) VALUES ('$skuNumber', '$myPrice', '$website', '$href')");

            }
        }
        mysqli_close($conn);

    }



}
