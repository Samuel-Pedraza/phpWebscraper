<?php

set_time_limit(0);


echo "<table>
<tr>
    <td>
    id
    </td>

    <td>
    sku
    </td>

    <td>
    price
    </td>

    <td>
    name
    </td>
</tr>";


$sql = "SELECT model_number FROM vestil_products order by vestil_products.model_number";

$connect = mysqli_connect('66.112.76.254', '', '', 'sams_test_database');

$result = mysqli_query($connect, $sql);

$post = array();

while($row = mysqli_fetch_assoc($result)){
    foreach ($row as $key) {
        array_push($post, trim($key));
    }
}

$unique_array = array_unique($post);


foreach ($unique_array as $key => $value) {
    $my_sql = "SELECT * FROM vestil_products WHERE vestil_products.model_number = '$value' ORDER BY vestil_products.price ASC LIMIT 1";
    $my_result = mysqli_query($connect, $my_sql);

    foreach ($my_result as $a => $b) {
        echo "<tr>";
        foreach ($b as $c => $d) {
            echo "<td>";
            echo $d;
            echo "</td>";
        }
        echo "</tr>";
    }
}

echo "</table>";

mysqli_close($connect);

?>
