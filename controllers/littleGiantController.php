<?php


    //walmart -> https://developer.walmartlabs.com/docs

    //fastenal -> https://www.fastenal.com/products?term=Little+Giant%5BREG%5D&r=~%7Cmanufacturer:%5E%22Little%20Giant[REG]%22$%7C~&pageno=1

    //northern equipment -> http://www.northerntool.com/shop/tools/category_little-giant http://www.northerntool.com/shop/tools/category_little-giant-hand-truck http://www.northerntool.com/shop/tools/category_little-giant-ladder

    //zoro --> https://www.zoro.com/search?q=little+giant&brand=LITTLE+GIANT&page=2

    //sodyinc --> http://www.sodyinc.com/little-giant?zenid=8jgptlkbvjb37gp72f9hfnqmr7

    //shop.com --> http://developer.shop.com/


class LittleGiant {

    function sqlQuery($sku, $price, $website, $url, $conn){
         $result = mysqli_query($conn, "SELECT * FROM little_giant_products WHERE sku = '" . $sku . "' AND website = '" . $website . "' " );

         if(mysqli_num_rows($result) > 0){
             mysqli_query($conn, "UPDATE little_giant_products SET price = $price WHERE website = '$website' AND sku = '$sku' " );
             echo "updated price <br />";
         } else {
             mysqli_query($conn, "INSERT INTO little_giant_products(sku, price, website, url) VALUES ('$sku', '$price', '$website', '$url') ");
             echo "created price <br />";
         }
     }

     //finished

    //finished
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

                sleep(2);

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

    //finished
    function spill911(){
        //necessary so that connection does not time out when webscraping
        set_time_limit(0);

        $conn = mysqli_connect('66.112.76.254', '', '', 'sams_test_database');

        if($conn === false){
            die("ERROR: Could not connect. " . mysqli_connect_error());
        }

        $html = new simple_html_dom();


        $increaseOffsetBy32 = 0;

        for ($i=1; $i <= 47 ; $i++) {

            $html->load_file("https://www.spill911.com/mm5/merchant.mvc?Screen=SRCH2&Store_Code=spill911&search=little+giant&searchoffset=" . $increaseOffsetBy32 . "&Category_Code=&filter_cat=&PowerSearch_Begin_Only=&sort=&range_low=&range_high=&customfield1=brand&filter_cf1=Little+Giant&customfield2=&filter_cf2=&customfield3=&filter_cf3=&psboost=&filter_price=&priceranges=1");

            $links = $html->find(".ctgy-layout-info strong a");

            foreach ($links as $key => $value) {

                $new_page = new simple_html_dom();
                sleep(2);
                $new_page->load_file($value->href);

                $price = $new_page->find("h3.prod-price .price-value");

                $sku = $new_page->find(".product-manufacturer-part");

                $myPriceArray = array();
                $mySkuArray   = array();

                foreach ($sku as $key1 => $value1) {
                    $editedSku = preg_replace("/Manufacturer Part Number:/", "", $value1->plaintext);
                    array_push($mySkuArray, $editedSku);
                }

                foreach ($price as $key2 => $value2) {
                    $priceNice = preg_replace("/[(),$]/", "", $value2->innertext);
                    array_push($myPriceArray, $priceNice);
                }

                $myArray = array_combine($mySkuArray, $myPriceArray);

                foreach ($myArray as $info => $value1) {
                    $website = "spill911";
                    $url = "";

                    echo $info . " " . $value1 . " " . $website .  " " . $url;

                    $this->sqlQuery($info, $value1, $website, $url, $conn);
                }
            }

            $increaseOffsetBy32 += 32;
        }
    }

    //finished
    function custommhs(){
        //necessary so that connection does not time out when webscraping
        set_time_limit(0);

        $html = new simple_html_dom();

        $conn = mysqli_connect('66.112.76.254', '', '', 'sams_test_database');

        if($conn === false){
            die("ERROR: Could not connect. " . mysqli_connect_error());
        }

        for ($i = 1; $i < 2; $i++) {

            $html->load_file("https://www.custommhs.com/index.php?route=product/manufacturer&manufacturer_id=50&page=" . $i);

            $modelNumber = $html->find(".smallBoxBg ul li a");

            $price = $html->find(".smallBoxBg ul li span");

            $plainTextModelNumbers = array();

            foreach ($modelNumber as $key => $value) {
                $mykey = $value->plaintext;
                array_push($plainTextModelNumbers, $mykey);
            }

            $newArray = array_combine($plainTextModelNumbers, $price);

            foreach ($newArray as $sku => $price) {
                echo $key;
                $priceNice = preg_replace("/[(),$]/", "", $price->plaintext);
                $website = "custommhs";
                $url = " ";
                $this->sqlQuery($sku, $priceNice, $website, $url, $conn);
            }
        }

    }

    //finished
    function source4industries(){
        //necessary so that connection does not time out when webscraping
        set_time_limit(0);

        $conn = mysqli_connect('66.112.76.254', '', '', 'sams_test_database');

        if($conn === false){
            die("ERROR: Could not connect. " . mysqli_connect_error());
        }

        $html = new simple_html_dom();

        $html->load_file("https://source4industries.com/index.php?route=product/manufacturer/info&manufacturer_id=31&limit=327");

        $price = $html->find("li .price");

        $sku = $html->find(".name a");


        $mySku = array();
        $myPrice = array();

        foreach ($price as $key => $value) {
            $mprice = preg_replace("/[(),$]/", "", $value->innertext);
            array_push($myPrice, $mprice);
        }

        foreach ($sku as $skuKey => $skuValue) {
            $grabLast = explode(" ", $skuValue->plaintext);
            array_push($mySku, $grabLast[sizeof($grabLast) - 1]);
        }

        $myArray = array_combine($mySku, $myPrice);

        foreach ($myArray as $info => $value1) {
            $website = "source4industries";
            $url = "";

            echo $info . " " . $value1 . " " . $website .  " " . $url;

            $this->sqlQuery($info, $value1, $website, $url, $conn);
        }

    }

    //finished???
    function bizchair(){
        set_time_limit(0);

        $conn = mysqli_connect('66.112.76.254', '', '', 'sams_test_database');

        $html = new simple_html_dom();

        $itemOffset = 0;
        $countOfItemsWithDash = 0;

        for($i = 0; $i <= 14; $i++){
            $html->load_file("https://www.bizchair.com/search?q=Little%20Giant&prefn1=brand&sz=24&start=" . $itemOffset . "&prefv1=Little%20Giant");

            $sku = $html->find(".product-id span[itemprop=productID]");

            $price = $html->find(".product-sales-price");


            $newskuarray = array();

            foreach ($sku as $element => $info) {
                array_push($newskuarray, $info->innertext);
            }

            $combined = array_combine($newskuarray, $price);

            foreach ($combined as $key => $value) {
                if(strpos($value, " - ")){
                    $countOfItemsWithDash += 1;
                } else {
                    $myprice = preg_replace("/[(),$]/", "", $value->innertext);
                    $website = "bizchair";
                    $url = "";

                    echo $key;
                    echo $myprice;
                    echo "<br />";
                }
            }
            $itemOffset += 24;
        }
    }

    //
    function industrialproducts(){
        set_time_limit(0);

        $html = new simple_html_dom();

        $html->load_file("https://www.industrialproducts.com/search/show/all?cat=0&q=little+giant");

        $info = $html->find(".catalogsearch-result-index .category-products .products-grid .product-name a");

        foreach ($info as $key => $value) {
            if(!(strpos($value->href, "little-giant-sheet-steel-box-trucks-with-hinged-lids.html"))){
                $new_page = new simple_html_dom();
                $new_page->load_file($value->href);
                $table = $new_page->find("table tr td a");

                foreach ($table as $a => $link) {
                    $individual_page = new simple_html_dom();
                    $individual_page->load_file($link->href);

                    $price = $individual_page->find("span.map");

                    foreach ($price as $myKey => $myPrice) {
                        echo $myPrice;
                    }
                }

            }
        }
    }
}



 ?>
