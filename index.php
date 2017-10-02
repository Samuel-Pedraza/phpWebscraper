<?php

    set_time_limit(0);

    include("simple_html_dom.php");



class Vestilwebscraper {

    function hofequipment() {

        $html = new simple_html_dom();

        $html->load_file("http://hofequipment.com/cart.php?m=search_results&catID=&venID=1&search=&shopByPrice=&sortBy=&viewAll=1");

        $query = $html->find(".grid__item div.btn-group a");

        foreach ($query as $key) {
            if(!(preg_match('/javascript/', $key->href))){
                $grabProducts = new simple_html_dom();

                $grabProducts->load_file($key->href);

                if($grabProducts->find("table.responsive_tables tbody tr") == True){
                    foreach ($grabProducts->find("table.responsive_tables tbody tr") as $tr) {

                        foreach ($tr->find("td[data-title=SKU]") as $sku) {
                            echo $sku;
                        }

                        foreach ($tr->find("td[data-title=Price]") as $price) {
                                $mprice = preg_replace("/[(),$]/", "", $price);
                                echo $mprice . "<br />";
                        }
                    }
                } else {
                    foreach ($tr->find("span.field-value") as $sku) {
                        echo $sku;
                    }

                    foreach ($tr->find("#price") as $price) {
                            $mprice = preg_replace("/[(),$]/", "", $price);
                            echo $mprice . "<br />";
                    }
                }
            }
        }

    }

    function industrialsafety(){

        for($pages = 1; $pages <= 13; $pages++){
            $html = new simple_html_dom();

            $html->load_file("https://industrialsafety.com/catalogsearch/result/index/?p=" . $pages . "&product_list_limit=80&product_list_order=name&q=vestil");

            $query = $html->find("div.products-grid .grid-product-type li");

            foreach ($query as $product) {

                foreach ($product->find(".product-item-link") as $sku) {
                    $mySku = trim($sku->plaintext);
                    $skuArray = explode(" ", $mySku);
                    echo $skuArray[1];
                    echo "<br />";
                }

                foreach($product->find(".price-wrapper .price") as $price){
                    echo $price;
                }
            }
        }

    }

    function toolfetch(){

            //step1
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

                $informationforgivenpage = $thiswebsite->find(".add-to-box span.price");
                $productid = $thiswebsite->find("p.product-ids");

                foreach ($productid as $productinformation) {
                    echo $productinformation;
                }

                foreach ($informationforgivenpage as $price) {
                    echo $price;
                }

            }
    }

    function opentip(){
        $html = new simple_html_dom();
        $html->load_file("https://www.opentip.com/search.php?keywords=vestil&limit=100");

        $card = $html->find(".item-detail");

        foreach ($card as $key) {

            $sku = $key->find(".products_sku span");

            $price = $key->find(".products_price");

            foreach ($price as $num) {
                echo $num;
            }

            foreach ($sku as $model) {
                echo $model;
                echo "<br />";
            }

        }
    }

}

$myWebscraper = new Vestilwebscraper;

$myWebscraper->hofequipment();
$myWebscraper->industrialsafety();
$myWebscraper->toolfetch();
$myWebscraper->opentip();

 ?>
