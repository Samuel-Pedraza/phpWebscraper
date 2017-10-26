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
                what website we are webscraping, this is inserted into the database and is also considered an output variable

            $sql_connection:
                declares ip, username, password and database using mysqli_connection()

            $table_name:
                passed to sqlQuery(), so we know what table name to insert or update records

            $page_numbers (not required for every webscraper):
                tells us how many pages we must make a request to.


        OUTPUT VARIABLES: variables that are written to the database

            $sku:
                indicates the sku of a given product

            $price:
                indicates the price of a given product

            $website:
                what website we are webscraping, this is inserted into the database and is also an input variable

Functions:

    Structure:




    Order:

        hofequipment:
            INPUT:
                * $url
                * $website
                * $sql_connection

            NOTES:
                set_time_limit(0)

        industrialsafety

        toolfetch

        opentip

        globalindustrial

        spill911

        custommhs

        bizchair

        sodyinc

        sustainablesupply


        sqlQuery


Functions that can abstracted out:

    sanitizing function

    instantiate url and grab page, invoke wait (optional parameter -> return array of elements)




*/



class Web {

    function hofequipment($url, $website, $sql_connection) {

         set_time_limit(0);

         $html = new simple_html_dom();
         $html->load_file($url);
         $product_url = $html->find(".grid__item div.btn-group a");

         foreach ($product_url as $individual_url) {

             sleep(2);

             if(!(preg_match('/javascript/', $individual_url->href))){

                 $grab_single_product = new simple_html_dom();
                 $grab_single_product->load_file($individual_url->href);


                 if($grab_single_product->find("table.responsive_tables tbody tr") == True){
                     foreach ($grab_single_product->find("table.responsive_tables tbody tr") as $tr) {

                         $product_info_array = [];

                         foreach ($tr->find("td[data-title=SKU]") as $raw_sku) {
                             array_push($product_info_array, $raw_sku->innertext);
                         }

                         foreach ($tr->find("td[data-title=Price]") as $raw_price) {
                             array_push($product_info_array, preg_replace("/[(),$]/", "", $raw_price->innertext));
                         }

                         $sku = current($product_info_array);
                         $price = next($product_info_array);

                         if($sku && $price){
                             $this->sqlQuery($sku, $price, $website, $sql_connection);
                         }
                     }
                 } else {

                     $product_info_array = [];

                     foreach ($grab_single_product->find("span[itemprop=sku]") as $raw_sku) {
                         array_push($product_info_array, $raw_sku->innertext);
                     }

                     foreach ($grab_single_product->find("div.item-price--product") as $my_price) {
                         array_push($product_info_array, preg_replace("/[(),$]/", "", $my_price->innertext));
                     }

                     $sku = current($product_info_array);
                     $price = next($product_info_array);

                     if($sku && $price){
                         $this->sqlQuery($sku, $price, $website, $sql_connection);
                     }
                 }
             }
         }
         mysqli_close($sql_connection);
     }

    function industrialsafety($url, $website, $page_numbers, $sql_connection){
        set_time_limit(0);

        for($pages = 1; $pages <= $page_numbers; $pages++){
            $html = new simple_html_dom();

            #breaks url into an array
            $my_url = explode("1", $url);

            $html->load_file($my_url[0] . $pages . $my_url[1]);

            //so we dont get banned - gives the webscraper a nice chill pill
            sleep(3);

            $query = $html->find("div.products-grid .grid-product-type li");

            foreach ($query as $product) {

                 $info_array = [];

                 foreach ($product->find(".product-item-link") as $my_sku) {
                     $sku_array = explode(" ", trim($my_sku->plaintext));
                     array_push($info_array, $sku_array[1]);
                 }

                 foreach($product->find(".price-wrapper .price") as $my_price){
                     array_push($info_array, preg_replace("/[(),$]/", "", $my_price->innertext)); //[2] price
                 }

                 $sku       = $info_array[0];
                 $price     = $info_array[1];

                 $this->sqlQuery($sku, $price, $website, $sql_connection);
            }
        }

        mysqli_close($sql_connection);
    }
    //https://stackoverflow.com/questions/12164196/warning-file-get-contents-failed-to-open-stream-redirection-limit-reached-ab
    //notes to help understand until i can come in and comment this baby up

    function toolfetch($url, $website, $page_numbers, $sql_connection){
        set_time_limit(0);

        for($j = 1; $j <= $page_numbers; $j++){

            $myUrl = explode("1", $url);

            $html = new simple_html_dom();
            $html->load_file($myUrl[0] . $j);

            $sku_raw = $html->find(".col-main .sku span");
            $price_raw = $html->find(".col-main span.price");

            $sku_array = array();
            $price_array = array();

            foreach ($sku_raw as $key => $value) {
                array_push($sku_array, preg_replace("/VES-/", "", $value->innertext));
            }

            foreach ($price_raw as $id => $my_price) {
                array_push($price_array, preg_replace("/[(),$]/", "", $my_price->innertext));
            }

            $combined = array_combine($sku_array, $price_array);

            foreach ($combined as $sku => $price) {
                $this->sqlQuery($sku, $price, $website, $sql_connection);
            }
        }
        mysqli_close($sql_connection);
    }

    function opentip($url, $website, $page_numbers, $sql_connection){

       for($i = 1; $i <= $page_numbers; $i++){

            set_time_limit(0);

            $redefinedUrl = explode("page=1", $url);

            $html = new simple_html_dom();
            $html->load_file($redefinedUrl[0] . "page=" . $i);

            $card = $html->find(".item-detail");

            sleep(3);

            foreach ($card as $key) {

                $skuNumber = $key->find(".products_sku span", 0)->plaintext;

                $sku = preg_replace("/SKU: /", "", $skuNumber);

                $my_price = $key->find(".products_price", 0)->plaintext;

                $price = preg_replace("/[(),$]/", "", $my_price);

                $href = $key->find(".data a.title", 0)->href;

                $this->sqlQuery($sku, $price, $website, $sql_connection);
            }
        }
        mysqli_close($sql_connection);
    }

    function globalindustrial($url, $website, $page_numbers, $sql_connection){
        //necessary so that connection does not time out when webscraping
        set_time_limit(0);

        for($i=1; $i < $page_numbers; $i++) {

            $myUrl = explode("cp=1", $url);

            $html = new simple_html_dom();

            $html->load_file($myUrl[0] . "cp=" . $i);


            $document_links = $html->find(".grid .prod .title a");

            foreach ($document_links as $key => $value) {

                $new_webpage = new simple_html_dom();

                sleep(2);

                $new_webpage->load_file("http://www.globalindustrial.com/" . $value->href );

                $price = $new_webpage->find("span[itemprop=price]");

                $sku = $new_webpage->find(".prodSpec ul li ul li span");

                $myArray = [];

                foreach ($sku as $mykey => $myvalue) {
                    if($myvalue->plaintext == "MANUFACTURERS PART NUMBER "){
                        array_push($myArray, $sku[$mykey + 1]->plaintext);
                    }
                }

                foreach ($price as $index => $myprice) {
                    array_push($myArray, preg_replace("/[(),$]/", "", $myprice->plaintext));
                }

                //grabs first element in array
                $sku = current($myArray);

                //grabs second element in array
                $price = next($myArray);

                $url = "http://www.globalindustrial.com/" . $value->href;

                echo $sku . " " . $price . " " . $website;

                $this->sqlQuery($sku, $price, $website, $sql_connection);
            }
        }

        mysqli_close($sql_connection);
    }

    //make sure when passing a url as a variable for this website, you set limit = to an absurb number -- fix this later
    //does not need a sleep function
    function source4industries($url, $website, $sql_connection){
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

            $this->sqlQuery($info, $value1, $website, $sql_connection);
        }

        mysqli_close($sql_connection);
    }

    function spill911($url, $website, $sql_connection){
        //necessary so that connection does not time out when webscraping
        set_time_limit(0);

        $my_url = explode("searchoffset=0", $url);
        #https://www.spill911.com/mm5/merchant.mvc?Screen=SRCH2&Store_Code=spill911&search=vestil&searchoffset=0&Category_Code=&filter_cat=&PowerSearch_Begin_Only=&sort=&range_low=&range_high=&customfield1=brand&filter_cf1=&customfield2=&filter_cf2=&customfield3=&filter_cf3=&psboost=srchkeys%2Ccode%2Cname&filter_price=&priceranges=1
        $increaseby32 = 0;

        for ($i=1; $i < 8; $i++) {

            $html = new simple_html_dom();
            $html->load_file($my_url[0] . "searchoffset=" . $increaseby32 . $my_url[1]);
            $links = $html->find(".ctgy-layout-info strong a");

            foreach ($links as $key => $link) {

                $individual_webpage = new simple_html_dom();
                $individual_webpage->load_file($link->href);
                $raw_sku = $individual_webpage->find(".product-manufacturer-part");
                $raw_price = $individual_webpage->find(".price-value");

                $sku_array = array();
                $price_array = array();

                foreach ($raw_sku as $info => $single_sku) {

                    array_push($sku_array, preg_replace("/Manufacturer Part Number:/", "", strip_tags($single_sku->innertext)));
                }
                foreach ($raw_price as $index => $single_price) {
                    array_push($price_array, preg_replace("/[(),$]/", "", $single_price->innertext));
                }

                $combined = array_combine($sku_array, $price_array);

                foreach ($combined as $sku => $price) {
                    $this->sqlQuery($sku, $price, $website, $sql_connection);
                }
            };

            $increaseby32 += 32;
        }
        mysqli_close($sql_connection);
    }

    //no pages_count because this can be loaded and rendered on one page
    //no sleep function either
    //perhaps later we can construct an object of each product, as to store the url in the database?
    function custommhs($url, $website, $sql_connection){
        //necessary so that connection does not time out when webscraping
        set_time_limit(0);

        $html = new simple_html_dom();

        if($sql_connection === false){ die("ERROR: Could not connect. " . mysqli_connect_error()); }

            $html->load_file($url);

            $modelNumber = $html->find(".smallBoxBg ul li a");

            $grab_price = $html->find(".smallBoxBg ul li span");

            $plainTextModelNumbers = array();

            foreach ($modelNumber as $key => $value) {
                $mykey = $value->plaintext;
                array_push($plainTextModelNumbers, $mykey);
            }

            $newArray = array_combine($plainTextModelNumbers, $grab_price);

            foreach ($newArray as $sku => $unsanitized_price) {
                echo $sku;
                $price = preg_replace("/[(),$]/", "", $unsanitized_price->plaintext);
                $website = "custommhs";
                $url = " ";

                $this->sqlQuery($sku, $price, $website, $sql_connection);
            }

        mysqli_close($sql_connection);
    }

    //will need a sleep function when enabling a work around for prices with more than one item

    //perhaps grab santizing function and pass for other prices in this document???
    function bizchair($url, $website, $page_numbers, $sql_connection){
        set_time_limit(0);

        $html = new simple_html_dom();

        $itemOffset = 0;
        $countOfItemsWithDash = 0;

        for($i = 0; $i <= $page_numbers; $i++){

            $html->load_file($url . $itemOffset);

            $grab_sku = $html->find(".product-id span[itemprop=productID]");

            $grab_price = $html->find(".product-sales-price");

            $newskuarray = array();

            foreach ($grab_sku as $element => $info) {
                array_push($newskuarray, $info->innertext);
            }

            $combined = array_combine($newskuarray, $grab_price);

            //counting the price descriptions with a dash, because these must be done by hand/have a work around
            foreach ($combined as $sku => $unsanitized_price) {
                if(strpos($value, " - ")){
                    $countOfItemsWithDash += 1;
                } else {
                    $price = preg_replace("/[(),$]/", "", $unsanitized_price->innertext);

                    $this->sqlQuery($sku, $price, $website, $sql_connection);
                }
            }
            $itemOffset += 24;
        }
        mysqli_close($sql_connection);
    }

    //URL passed should be in the form of http://www.sodyinc.com/little-giant?sort=20a&page=1
    function sodyinc($url, $website, $page_numbers, $sql_connection){
        //ensures no timing out - php has a 30 second timeout otherwise
        set_time_limit(0);

        //takes $url, takes off the '1' and turns string into an array
        $my_url = explode("1", $url);

        //looping through every page on the website
        //$page_count is length of looping
        for ($i=1; $i <= $page_numbers; $i++) {

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

                foreach ($combined_array as $sku => $price) {
                    $this->sqlQuery($sku, $price, $website, $sql_connection);
                }
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
    //$sql_connection is for mysqli_connection
    //$table_name is for which table you want to select from
    //other variables are exactly what they are declared to be

    function sqlQuery($model_number, $price, $website, $sql_connection){

         $result = mysqli_query($sql_connection, "SELECT * FROM vestil_products WHERE model_number = '$model_number' AND website = '$website'");

         //if there are any results returned, update
         if(mysqli_num_rows($result) > 0){
             mysqli_query($sql_connection, "UPDATE vestil_products SET price = $price WHERE website = '$website' AND model_number = '$model_number' " );
             echo "updated <br />";
         } else {
            mysqli_query($sql_connection, "INSERT INTO vestil_products(model_number, price, website) VALUES ('$model_number', $price, '$website') ");
            echo "created <br />";
         }
     }

}

  ?>
