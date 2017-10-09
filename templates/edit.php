<?php
$conn = mysqli_connect('66.112.76.254', '', '', 'sams_test_database');

$sql = "SELECT * FROM test_data WHERE id = " . $id;

$result = mysqli_query($conn, $sql);

echo "<form action=/edit/" . $id .  "method=PUT >";
foreach ($result as $key => $value) {
    foreach ($value as $key1 => $value1) {
        echo "<div>" . $key1 . "</div>";
        echo "<input value=" . $value1 . " name=" . $key1 . "/> <br />";
    }
}
echo "<input type='submit' />";
echo "</form>";
 ?>
