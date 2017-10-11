<?php

class LittleGiant {

    function sqlQuery($sku, $price, $website, $url, $conn){
         $result = mysqli_query($conn, "SELECT * FROM test_data WHERE sku = '" . $sku . "' AND website = '" . $website . "' " );

         if(mysqli_num_rows($result) > 0){
             mysqli_query($conn, "UPDATE test_data SET price = $price WHERE website = '$website' AND sku = '$sku' " );
             echo "updated price <br />";
         } else {
             mysqli_query($conn, "INSERT INTO test_data(sku, price, website, url) VALUES ('$sku', '$price', '$website', '$url') ");
             echo "created price <br />";
         }
     }

    function globalindustrial(){
        //necessary so that connection does not time out when webscraping
        set_time_limit(0);

        $html = new simple_html_dom();

        $conn = mysqli_connect('66.112.76.254', '', '', 'sams_test_database');

        if($conn === false){
            die("ERROR: Could not connect. " . mysqli_connect_error());
        }

        for ($i=1; $i < 2; $i++) {
            # code...
            $html->load_file("http://www.globalindustrial.com/shopByBrandName/L/little-giant?cp=1&ps=110");

            $url = $html->find(".grid .prod .title a");

            foreach ($url as $key => $value) {
                # code...

                $new_webpage = new simple_html_dom();

                $new_webpage->load_file("http://www.globalindustrial.com/" . $value->href );

                $price = $new_webpage->find("span[itemprop=price]");

                $sku = $new_webpage->find(".prodSpec ul li ul li span");


                $myArray = [];


                foreach ($sku as $mykey => $myvalue) {
                    if($myvalue->plaintext == "MODEL "){
                        array_push($myArray, $sku[$mykey + 1]->plaintext);
                    }
                }

                foreach ($price as $index => $myprice) {
                    array_push($myArray, $myprice->plaintext);
                }


                //grabs first element in array
                $sku = current($myArray);

                //grabs second element in array
                $price   = next($myArray);

                $url = "http://www.globalindustrial.com/" . $value->href;

                $website = "globalindustrial";

                echo $sku . " " . $price . " " . $url . " " . $website . "<br />";

                $this->sqlQuery($sku, $price, $website, $url, $conn);
            }
        }
        mysqli_close($conn);
    }

    function spill911(){
        //necessary so that connection does not time out when webscraping
        set_time_limit(0);

        $html = new simple_html_dom();

        $html->load_file("https://www.spill911.com/mm5/merchant.mvc?Screen=SRCH&Store_Code=spill911&search=little+giant&offset=&filter_cat=&filter_cf1=Little+Giant&filter_cf2=&filter_cf3=&PowerSearch_Begin_Only=&sort=&range_low=&range_high=&layout=&searchcatcount=&customfield1=brand&customfield2=&customfield3=&filter_price=&priceranges=1");

        $links = $html->find(".ctgy-layout-info strong a");

        foreach ($links as $key => $value) {

            $new_page = new simple_html_dom();

            $new_page->load_file($value->href);

            $price = $new_page->find("h3.prod-price .price-value");

            $sku = $new_page->find(".product-manufacturer-part");

            foreach ($sku as $key1 => $value1) {
                $editedPrice = preg_replace("/Manufacturer Part Number:/", "", $value1->plaintext);
                echo $editedPrice;
            }

            foreach ($price as $key2 => $value2) {
                $priceNice = preg_replace("/[(),$]/", "", $value2->innertext);
                echo $priceNice;
                echo "<Br />";
            }

        }

    }

    // edit fully
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

    function custommhs(){
        $html = new simple_html_dom();

        $html->load_file("https://www.custommhs.com/index.php?route=product/manufacturer&manufacturer_id=50");

        $modelNumber = $html->find(".smallBoxBg ul li a");

        $price = $html->find(".smallBoxBg ul li span");

        $plainTextModelNumbers = array();

        foreach ($modelNumber as $key => $value) {
            $mykey = $value->plaintext;
            array_push($plainTextModelNumbers, $mykey);
        }

        $newArray = array_combine($plainTextModelNumbers, $price);

        foreach ($newArray as $key => $value) {
            echo $key;
            echo $value->plaintext;
            echo "<br />";
        }

    }


    function source4industries(){

        $html = new simple_html_dom();

        $html->load_file("https://source4industries.com/index.php?route=product/manufacturer/info&manufacturer_id=31&limit=327");

        $price = $html->find("li .padding .left .price");

        $sku = $html->find(".left .name a");

        foreach ($sku as $key => $value) {
            echo $value;
        }

        foreach ($price as $key => $value) {
            echo $value;
            echo "<br />";
        }

    }



 ?>
