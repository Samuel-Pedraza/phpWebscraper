<?php



class Web {
    //http://hofequipment.com/cart.php?m=search_results&catID=&venID=1&search=&shopByPrice=&sortBy=&viewAll=1
    //$website
    //finished
    function hofequipment($url, $website, $sqlconnection) {

         //necessary so that connection does not time out when webscraping
         set_time_limit(0);

         //**need to rewrite**//
         if(!$sqlconnection) {
             echo 'Failed to Connect';
         }

         //instantiates object of simple_html_dom(simplehtmldom.sourceforge.net)
         $html = new simple_html_dom();

         $html->load_file($url);

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

                     if($skuNumber && $price){
                         $this->sqlQuery($skuNumber, $price, $website, $key->href, $sqlconnection);
                     }
                 }
             }
         }
         mysqli_close($sqlconnection);
     }

     //finished

    function industrialsafety($url, $sqlconnection){
        set_time_limit(0);

        for($pages = 1; $pages <= 14; $pages++){
            $html = new simple_html_dom();

            #breaks url into an array
            $myUrl = explode("1", $url);

            $html->load_file($myUrl[0] . $pages . $myUrl[1]);

            //so we dont get banned - gives the webscraper a nice chill pill
            sleep(3);

            $query = $html->find("div.products-grid .grid-product-type li");

            foreach ($query as $product) {

                 $infoArray = [];

                 foreach ($product->find(".product-item-link") as $sku) {

                     $skuArray = explode(" ", trim($sku->plaintext));  //

                     array_push($infoArray, $skuArray[1]); //[0] skuNum
                     array_push($infoArray, $sku->href); //[1] href

                 }

                 foreach($product->find(".price-wrapper .price") as $price){

                     $mprice = preg_replace("/[(),$]/", "", $price->innertext);
                     array_push($infoArray, $mprice); //[2] price

                 }

                 $skuNumber = $infoArray[0];
                 $price     = $infoArray[2];
                 $url       = $infoArray[1];

                 $this->sqlQuery($skuNumber, $price, $website, $url, $sqlconnection);
            }
        }

        mysqli_close($sqlconnection);
    }

    //https://stackoverflow.com/questions/12164196/warning-file-get-contents-failed-to-open-stream-redirection-limit-reached-ab
    //notes to help understand until i can come in and comment this baby up

    //not finished
    function toolfetch($url, $website, $sqlconnection){

        for($j = 1; $j <= 142; $j++){

            //step1
            set_time_limit(0);
            $cSession = curl_init();

            //step2
            // this was all copied and pasted --- who knows what it does?
            curl_setopt($cSession,CURLOPT_URL, $url);
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

                $this->sqlQuery($sku, $price, $website, $url, $sqlconnection);

            }
            mysqli_close($sqlconnection);
        }
    }

    //not finished
    //need to write ability to split url at the end in order to append page number
    function opentip($url, $website, $pagescount, $sqlconnection){

       for($i = 1; $i <= $pagescount; $i++){

            set_time_limit(0);

            $redefinedUrl = explode("", $url);

            $array_pop(explode("", $url));

            $html = new simple_html_dom();
            $html->load_file($url . $i);

            $card = $html->find(".item-detail");

            sleep(3);

            foreach ($card as $key) {

                $sku = $key->find(".products_sku span", 0)->plaintext;

                $skuNumber = preg_replace("/SKU: /", "", $sku);

                $price = $key->find(".products_price", 0)->plaintext;

                $myPrice = preg_replace("/[(),$]/", "", $price);

                $href = $key->find(".data a.title", 0)->href;

                $this->sqlQuery($skuNumber, $myPrice, $website, $href, $sqlconnection);

            }
        }
        mysqli_close($sqlconnection);
    }

    //finished
    function globalindustrial($url, $website, $pagenumbers, $sqlconnection){
        //necessary so that connection does not time out when webscraping
        set_time_limit(0);

        $html = new simple_html_dom();

        if($sqlconnection === false){ die("ERROR: Could not connect. " . mysqli_connect_error()); }

        for ($i=1; $i < $pagenumbers; $i++) {

            $myUrl = explode("1", $url);

            $html->load_file($myUrl[0] . $i);

            $url = $html->find(".grid .prod .title a");

            foreach ($url as $key => $value) {

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

                echo $sku . " " . $price . " " . $url . " " . $website . "<br />";

                $this->sqlQuery($sku, $price, $website, $url, $sqlconnection);
            }
        }
        mysqli_close($sqlconnection);
    }

    //finished
    //make sure when passing a url as a variable for this website, you set limit = to an absurb number -- fix this later

    function source4industries($url, $website, $sqlconnection){
        //necessary so that connection does not time out when webscraping
        set_time_limit(0);

        if($sqlconnection === false){ die("ERROR: Could not connect. " . mysqli_connect_error()); }

        $html = new simple_html_dom();

        $html->load_file($url);

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

            $this->sqlQuery($info, $value1, $website, $url, $sqlconnection);
        }

        mysqli_close($sqlconnection);
    }

    //finished
    function spill911($url, $website, $sqlconnection){
        //necessary so that connection does not time out when webscraping
        set_time_limit(0);

        if($sqlconnection === false){ die("ERROR: Could not connect. " . mysqli_connect_error()); }

        $html = new simple_html_dom();

        $increaseOffsetBy32 = 0;

        $myUrl = explode("searchoffset", $url);

        for ($i=1; $i <= 47 ; $i++) {

            $html->load_file($myUrl[0] . "searchoffset" . $increaseOffsetBy32 . $myUrl[1]);

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

                    echo $info . " " . $value1 . " " . $website .  " " . $url;

                    $this->sqlQuery($info, $value1, $website, $url, $sqlconnection);

                }
            }

            $increaseOffsetBy32 += 32;
        }

        mysqli_close($sqlconnection);
    }

    //finished
    function custommhs($url, $website, $pagescount, $sqlconnection){
        //necessary so that connection does not time out when webscraping
        set_time_limit(0);

        $html = new simple_html_dom();

        $myUrl = explode("1", $url);

        if($sqlconnection === false){ die("ERROR: Could not connect. " . mysqli_connect_error()); }

        for ($i = 1; $i < $pagescount; $i++) {

            $html->load_file($myUrl[0] . $i);

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
                $this->sqlQuery($sku, $priceNice, $website, $url, $sqlconnection);
            }
        }
        mysqli_close($sqlconnection);

    }

    //finished
    function bizchair($url, $website, $pagescount, $sqlconnection){
        set_time_limit(0);

        $html = new simple_html_dom();

        $itemOffset = 0;
        $countOfItemsWithDash = 0;

        for($i = 0; $i <= $pagescount; $i++){

            $html->load_file($url . $itemOffset);

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

                    echo $key;
                    echo $myprice;
                    echo "<br />";
                }
            }
            $itemOffset += 24;
        }
        mysqli_close($sqlconnection);

    }

    function sodyinc($url, $website, $pagescount, $sqlconnection){
        set_time_limit(0);


        $myUrl = explode("1", $url);

        for ($i=1; $i <= $pagescount; $i++) {
                # code...
            $html = new simple_html_dom();

            $html->load_file($myUrl[0] . $i . $myUrl[1]);

            $myinfo = $html->find("h3.itemTitle a");

            foreach ($myinfo as $key => $value) {
                $new_url = new simple_html_dom();

                $mydecodedstring = preg_replace("/amp;/", "", $value->href);

                $new_url->load_file($mydecodedstring);

                $my_sku = array();
                $my_price = array();

                $product_sku = $new_url->find("#productDetailsList li", 0);

                $modified_sku = preg_replace("/Model: /", "", $product_sku->innertext);

                echo $modified_sku;

                array_push($my_sku, $modified_sku);

                sleep(3);

                $product_price = $new_url->find("#productPrices");

                foreach ($product_price as $element => $found) {
                    $modified_price = preg_replace("/[(),$]/", "", $found->innertext);
                    echo $modified_price . "<br />";
                    array_push($my_price, $modified_price);
                }

                $combined_array = array_combine($my_sku, $my_price);

                foreach ($combined_array as $skuNumber => $priceNumber) {

                    $this->sqlQuery($skuNumber, $priceNumber, $website, $url, $sqlconnection);
                }
            }
        }
        mysqli_close($sqlconnection);
    }

    //not finished
    function sustainablesupply(){
        $html = new simple_html_dom();
        $html->load_file("http://www.sustainablesupply.com/search?keywords=little%20giant#filter:custitem_ssc_product_manufacturer:Little$2520Giant/perpage:96/page:2");

        $sku_binding = $html->find("span.sku-code span.ng-binding");

        foreach ($sku_binding as $key => $value) {
            echo $value . "<br />";
        }
    }

    //finished
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

}



  ?>
