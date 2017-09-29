<?php

    set_time_limit(0);

    include("simple_html_dom.php");


class Webscraper {

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

}

$hofequipmentScraper = new Webscraper;

$hofequipmentScraper->industrialsafety();

 ?>
