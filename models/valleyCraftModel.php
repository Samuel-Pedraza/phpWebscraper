<?php

//----------------------------------------------------------------------------------------------------
//  **VALLEY CRAFT CLASS**
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

class ValleyCraft {

    function globalindustrial(){
        //necessary so that connection does not time out when webscraping
        set_time_limit(0);

        $html = new simple_html_dom();

        $conn = mysqli_connect('66.112.76.254', '', '', 'sams_test_database');

        if($conn === false){
            die("ERROR: Could not connect. " . mysqli_connect_error());
        }

        for ($i=1; $i < 9; $i++) {
            # code...
            $html->load_file("http://www.globalindustrial.com/searchResult?cp=" . $i ."&p=attr_brand%3DValley%20Craft&ps=72&q=valley%20craft");

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

    function source4industries(){
        //necessary so that connection does not time out when webscraping
        set_time_limit(0);

        $conn = mysqli_connect('66.112.76.254', '', '', 'sams_test_database');

        if($conn === false){
            die("ERROR: Could not connect. " . mysqli_connect_error());
        }

        $html = new simple_html_dom();

        $html->load_file("https://source4industries.com/index.php?route=product/manufacturer/info&manufacturer_id=36&limit=75");

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
        mysqli_close($conn);

    }

    function zoro(){
        set_time_limit(0);

        $html = new simple_html_dom();
        $html->load_file("https://www.zoro.com/search?brand=VALLEY+CRAFT&q=valley+craft");

        $manufacturer = $html->find("span.mfr-no");

        foreach ($manufacturer as $key => $value) {
            # code...
            echo $value . "<br />";
        }

    }


    function getPage ($url) {


        $useragent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36';
        $timeout= 120;
        $dir            = dirname(__FILE__);
        $cookie_file    = $dir . '/cookies/' . md5($_SERVER['REMOTE_ADDR']) . '.txt';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt($ch, CURLOPT_ENCODING, "" );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_AUTOREFERER, true );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout );
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout );
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.google.com/');

        $content = curl_exec($ch);

            if(curl_errno($ch)){
                echo 'error:' . curl_error($ch);
            }
            else {
                return $content;
            }

            curl_close($ch);

    }





}

 ?>
