<?php

set_time_limit(0);

$db_host = "66.112.76.254";
$db_username = "root";
$db_pass = "adamserver5";
$db_name = "cms";

$conn = mysqli_connect("$db_host", "$db_username", "$db_pass", "$db_name");

if(!$conn) {
		die('error msg' . mysqli_connect_error());
	}
	else {
		$sql = "SELECT * FROM part a INNER JOIN part_class b on a.classID = b.classID INNER JOIN manufacturer c ON b.manufactID = c.manufactID WHERE c.manufactID = 57";

		$result = mysqli_query($conn, $sql);

		if(mysqli_num_rows($result) > 0) {
			$GLOBALS["result"] = $result;
		}
		else {
			echo "No Results Found!";
		}
	}
mysqli_close($conn);
?>