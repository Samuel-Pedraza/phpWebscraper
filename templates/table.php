<?php

    $conn = mysqli_connect('66.112.76.254', 'root', 'adamserver5', 'sams_test_database');
    
    $sql = "SELECT * FROM test_data WHERE website = 'opentip' ";

    $result = mysqli_query($conn, $sql);

    echo "<table>";

    foreach ($result as $key => $value) {
        # code...
        echo "<tr>";
        foreach ($value as $key2 => $value2) {
            # code...

            if ($key2 == "website") {
                #do nothing
            }
            elseif ($key2 == "url" ){
                echo "<td><a href=" . $value2 . ">Opentip</a></td>";
            }
            elseif($key2 == "id" ){
                echo "<td>
                <form action=/edit/" . $value2 . " method=GET>
                    <input type=submit value=edit>
                </form>
                </td>";
            }
            else {
                echo "<td>" . $value2 . "</td>";
            }
        }
        echo "</tr>";
    }

    echo "</table>";

    mysqli_close($conn);
?>
