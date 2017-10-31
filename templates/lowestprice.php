<?php

set_time_limit(0);

include('db_connect.php');

$sql = "SELECT sku FROM little_giant_products order by little_giant_products.sku ";

$result = mysqli_query($connect, $sql);


echo "<table><tr>
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
    url
    </td>

    <td>
    name
    </td>
</tr>";


    foreach ($result as $info => $data) {
        echo "<tr>";
        foreach ($data as $element) {
            $mysql = "SELECT * FROM little_giant_products WHERE little_giant_products.sku = '$element' ORDER BY little_giant_products.price ASC LIMIT 1";
            $myresult = mysqli_query($conn, $mysql);

            foreach ($myresult as $key => $value) {
                foreach ($value as $row => $td) {
                    echo "<td> " . $td . "</td>";
                }
            }
        }
        echo "<tr />";
    }

echo "</table>";

?>
