<?php

    set_time_limit(0);

    include("simple_html_dom.php");

    // $html = new simple_html_dom();
    //
    // $html->load_file("http://hofequipment.com/cart.php?m=search_results&catID=&venID=1&search=&shopByPrice=&sortBy=&viewAll=1");
    //
    // $query = $html->find(".grid__item div.btn-group a");
    //
    // foreach ($query as $key) {
    //     if(!(preg_match('/javascript/', $key->href))){
            $grabProducts = new simple_html_dom();

            $grabProducts->load_file("http://hofequipment.com/90-Degree-Triple-Elbow-Guards-p512.html");

            $table = $grabProducts->find("table.responsive_tables tbody tr");

            for($i = 0; $i < sizeof($table); $i++){
                echo gettype($table[$i]), "<br />";
            }
    //     }
    // }

 ?>
