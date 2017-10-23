<?php

/*

STRUCTURE OF DOCUMENT AND EXPLANATION - please read if making changes.


Variables:

    Structure:

        Naming Variables:

            one word:
                variables are all lowercase --- eg: $url

            two or more words:
                variables are snake case   ---  eg: $sql_connection


        INPUT VARIABLES: variables that must be passed into a given webscraper function

            $url:
                should be the url that scraping begin at. Includes page number to BEGIN -- related to $page_number

            $website:
                what website we are webscraping, this is inserted into the database and is also an output variable

            $sql_connection:
                declares ip, username, password and database using mysqli_connection()

            $table_name:
                passed to sqlQuery(), so we know what table name to insert or update records, is also an


        OUTPUT VARIABLES: variables that are written to the database

            $sku:
                indicates the sku of a given product

            $price:
                indicates the price of a given product

            $website:
                what website we are webscraping, this is inserted into the database and is also an input variable

Functions:

    Structure:


        hofequipment()
            //description

        industrialsafety($url, $website, $page_count, $sql_connection, $table_name)
            //description

        toolfetch($url, $website, $pagenumbers, $sql_connection, $table_name)
            //description

        opentip($url, $website, $pagescount, $sql_connection, $table_name)
            //description

        globalindustrial($url, $website, $pagenumbers, $sql_connection, $table_name)
            //description

        source4industries($url, $website, $sql_connection, $table_name)
            //description

        spill911()
            //description

        custommhs()
            //description

        bizchair()
            //description

        sodyinc()
            //description

        sqlQuery()
            //description

*/



class Web {
    //http://hofequipment.com/cart.php?m=search_results&catID=&venID=1&search=&shopByPrice=&sortBy=&viewAll=1
    //$website
    //finished
    function hofequipment($url, $website, $sql_connection, $table_name) {

         //necessary so that connection does not time out when webscraping
         set_time_limit(0);

         //**need to rewrite**//
         if(!$sql_connection) {
             echo 'Failed to Connect';
         }

         //instantiates object of simple_html_dom(simplehtmldom.sourceforge.net)
         $html = new simple_html_dom();

         $html->load_file($url);

         //$query is returned as an array
         $query = $html->find(".grid__item div.btn-group a");

         foreach ($query as $key) {

             sleep(2);
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
                         $timestamp = date("Y-m-d H:i:s");

                         //**need to rewrite**//
                         if($skuNumber && $price){
                             //referes to sqlQuery -- cannot call sqlQuery(a,b,c,d);
                             $this->sqlQuery($skuNumber, $price, $website, $table_name, $sql_connection);
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
                     $timestamp = date("Y-m-d H:i:s");

                     if($skuNumber && $price){
                         $this->sqlQuery($skuNumber, $price, $website, $table_name, $sql_connection);
                     }
                 }
             }
         }
         mysqli_close($sql_connection);
     }

     //finished

    function industrialsafety($url, $website, $page_count, $sql_connection, $table_name){
        set_time_limit(0);

        for($pages = 1; $pages <= $page_count; $pages++){
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

                 $this->sqlQuery($skuNumber, $price, $website, $table_name, $sql_connection);
            }
        }

        mysqli_close($sql_connection);
    }

    //https://stackoverflow.com/questions/12164196/warning-file-get-contents-failed-to-open-stream-redirection-limit-reached-ab
    //notes to help understand until i can come in and comment this baby up

    //finished
    function toolfetch($url, $website, $pagenumbers, $sql_connection, $table_name){

        for($j = 1; $j <= $pagenumbers; $j++){

            $myUrl = explode("1", $url);

            //step1
            set_time_limit(0);
            $cSession = curl_init();

            //step2
            // this was all copied and pasted --- who knows what it does?
            curl_setopt($cSession,CURLOPT_URL, $myUrl[0] . $j);
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
                sleep(2);
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

                $table_name = "vestil_products";
                $this->sqlQuery($sku, $price, $website, $table_name, $sql_connection);
            }
            mysqli_close($sql_connection);
        }
    }

    // finished
    function opentip($url, $website, $pagescount, $sql_connection, $table_name){

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

                $this->sqlQuery($skuNumber, $myPrice, $website, $table_name, $sql_connection);
            }
        }
        mysqli_close($sql_connection);
    }

    //finished
    function globalindustrial($url, $website, $pagenumbers, $sql_connection, $table_name){
        //necessary so that connection does not time out when webscraping
        set_time_limit(0);

        $html = new simple_html_dom();

        if($sql_connection === false){ die("ERROR: Could not connect. " . mysqli_connect_error()); }

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

                $this->sqlQuery($sku, $price, $website, $table_name, $sql_connection);
            }
        }
        mysqli_close($sql_connection);
    }

    //finished
    //make sure when passing a url as a variable for this website, you set limit = to an absurb number -- fix this later
    //does not need a sleep function
    function source4industries($url, $website, $sql_connection, $table_name){
        //necessary so that connection does not time out when webscraping
        set_time_limit(0);

        if($sql_connection === false){ die("ERROR: Could not connect. " . mysqli_connect_error()); }

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

            $this->sqlQuery($info, $value1, $website, $table_name, $sql_connection);
        }

        mysqli_close($sql_connection);
    }

    //finished
    function spill911($url, $website, $sql_connection, $table_name){
        //necessary so that connection does not time out when webscraping
        set_time_limit(0);

        if($sql_connection === false){ die("ERROR: Could not connect. " . mysqli_connect_error()); }

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

                    $this->sqlQuery($info, $value1, $website, $table_name, $sql_connection);
                }
            }

            $increaseOffsetBy32 += 32;
        }

        mysqli_close($sql_connection);
    }

    //finished
    //no pages_count because this can be loaded and rendered on one page
    //no sleep function either
    //perhaps later we can construct an object of each product, as to store the url in the database?
    function custommhs($url, $website, $sql_connection, $table_name){
        //necessary so that connection does not time out when webscraping
        set_time_limit(0);

        $html = new simple_html_dom();

        if($sql_connection === false){ die("ERROR: Could not connect. " . mysqli_connect_error()); }

            $html->load_file($url);

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

                $this->sqlQuery($key, $priceNice, $website, $table_name, $sql_connection);
            }

        mysqli_close($sql_connection);

    }

    //finished
    //will need a sleep function when enabling a work around for prices with more than one item
    function bizchair($url, $website, $pages_count, $sql_connection, $table_name){
        set_time_limit(0);

        $html = new simple_html_dom();

        $itemOffset = 0;
        $countOfItemsWithDash = 0;

        for($i = 0; $i <= $pages_count; $i++){

            $html->load_file($url . $itemOffset);

            $sku = $html->find(".product-id span[itemprop=productID]");

            $price = $html->find(".product-sales-price");

            $newskuarray = array();

            foreach ($sku as $element => $info) {
                array_push($newskuarray, $info->innertext);
            }

            $combined = array_combine($newskuarray, $price);

            //counting the price descriptions with a dash, because these must be done by hand/have a work around
            foreach ($combined as $key => $value) {
                if(strpos($value, " - ")){
                    $countOfItemsWithDash += 1;
                } else {
                    $myprice = preg_replace("/[(),$]/", "", $value->innertext);
                    $website = "bizchair";

                    echo $key;
                    echo $myprice;

                    $this->sqlQuery($key, $myprice, $website, $table_name, $sql_connection);
                }
            }
            $itemOffset += 24;
        }
        mysqli_close($sql_connection);

    }

    //URL passed should be in the form of http://www.sodyinc.com/little-giant?sort=20a&page=1
    function sodyinc($url, $website, $page_count, $sql_connection, $table_name){
        //ensures no timing out - php has a 30 second timeout otherwise
        set_time_limit(0);

        //takes $url, takes off the '1' and turns string into an array
        $my_url = explode("1", $url);

        //looping through every page on the website
        //$page_count is length of looping
        for ($i=1; $i <= $page_count; $i++) {

            $html = new simple_html_dom();
            //loads url of the page you want to scrape from
            sleep(3);
            $html->load_file($my_url[0] . $i);

            $links_of_page = $html->find("h3.itemTitle a");

            foreach ($links_of_page as $key => $individual_links) {
                $individual_product_page = new simple_html_dom();

                //takes out amp; from url, or else there will be an error
                $decode_url = preg_replace("/amp;/", "", $individual_links->href);

                $individual_product_page->load_file($decode_url);

                //two arrays declared for sku or price, these will be used later
                $my_sku = array();
                $my_price = array();

                //grabs sku
                $product_sku = $individual_product_page->find("#productDetailsList li", 0);

                //cleans sku
                $modified_sku = preg_replace("/Model: /", "", $product_sku->innertext);

                //pushes a clean sku number to the sku array
                array_push($my_sku, $modified_sku);

                //find price
                $product_price = $individual_product_page->find("#productPrices");

                //loops through product price
                foreach ($product_price as $element => $found_price) {
                    $modified_price = preg_replace("/[(),$]/", "", $found_price->innertext);
                    //pushes a clean price number to the price array
                    array_push($my_price, $modified_price);
                }

                //combined array: key-sku, value->price
                $combined_array = array_combine($my_sku, $my_price);

                foreach ($combined_array as $sku_number => $price_number) {
                    $this->sqlQuery($sku_number, $price_number, $website, $table_name, $sql_connection);                }
            }
        }
        mysqli_close($sql_connection);
    }

    //just started writing this webscraper
    function sustainablesupply(){
        $html = new simple_html_dom();
        $html->load_file("http://www.sustainablesupply.com/search?keywords=little%20giant#filter:custitem_ssc_product_manufacturer:Little$2520Giant/perpage:96/page:2");

        $sku_binding = $html->find("span.sku-code span.ng-binding");

        foreach ($sku_binding as $key => $value) {
            echo $value . "<br />";
        }
    }

    //THIS IS VERY UGLY. I APOLOGIZE.
    //$sql_connection is for sqli_connection
    //$tableName is for which table you want to select from
    //other variables are exactly what they are declared to be
    function sqlQuery($sku, $price, $website, $table_name, $sql_connection){
         $result = mysqli_query($sql_connection, "SELECT * FROM " . $table_name . " WHERE model_number = '" . $sku . "' AND website = '" . $website . "' " );

         //if there are any results returned, update
         if(mysqli_num_rows($result) > 0){
             mysqli_query($sql_connection, "UPDATE '" . $table_name ."' SET price = $price WHERE website = '$website' AND model_number = '$sku' " );
         }
         //else create a BRAND NEW PRODUCT WOW!
          else {
             mysqli_query($sql_connection, "INSERT INTO '" . $table_name ."'(model_number, price, website) VALUES ('$sku', '$price', '$website') ");
         }
     }

}

  ?>
