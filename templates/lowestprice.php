<?php

set_time_limit(0);

$my_manufacturer = $_POST["manufacturer"];

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


$sql = "SELECT sku FROM webscraping WHERE manufacturer = '$my_manufacturer' order by sku";

$connect = mysqli_connect('', '', '', 'sams_test_database');

$result = mysqli_query($connect, $sql);

$post = array();

while($row = mysqli_fetch_assoc($result)){
    foreach ($row as $key) {
        array_push($post, trim($key));
    }
}

$unique_array = array_unique($post);

foreach ($unique_array as $key => $value) {
    $my_sql = "SELECT * FROM webscraping WHERE webscraping.sku = '$value' ORDER BY webscraping.price ASC LIMIT 1";
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
