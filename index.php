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

    function toolfetch(){
        $ch = curl_init();  // Initialising cURL
        curl_setopt($ch, CURLOPT_URL, "http://www.toolfetch.com/by-brand/vestil/l/brand:vestil.html?limit=48&p=1");    // Setting cURL's URL option with the $url variable passed into the function
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // Setting cURL's option to return the webpage data
        $data = curl_exec($ch); // Executing the cURL request and assigning the returned data to the $data variable
        curl_close($ch);    // Closing cURL
        echo $data;

        // 171 pages 48 resutls
    }

}

$hofequipmentScraper = new Webscraper;

$hofequipmentScraper->toolfetch();

 ?>
