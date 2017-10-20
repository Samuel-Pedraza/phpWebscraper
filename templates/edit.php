<?php
$conn = mysqli_connect('66.112.76.254', '', '', 'sams_test_database');

$sql = "SELECT * FROM little_giant_products WHERE id = " . $id;

$result = mysqli_query($conn, $sql);

echo "<form method=PUT action=/edit/" . $id .  " >";
foreach ($result as $key => $value) {
    foreach ($value as $key1 => $value1) {
        echo "<div>" . $key1 . "</div>";
        echo "<input name = " . $key1 . " value = " . $value1 . "> <br />";
    }
}
echo "<br />";

echo "<input type='submit' />";
echo "</form>";

echo "<a href='/vestil'>Back</a>";

if (isset($_GET["price"])){

    $price = $_GET["price"];
    $id = $_GET["id"];
    $website = $_GET["website"];
    $url = $_GET["url"];
    $sku = $_GET["sku"];

    $sql = "UPDATE test_data SET price = $price WHERE id = '$id'";

    $result = mysqli_query($conn, $sql);

    echo "updated";
}


mysqli_close($conn);

 ?>
