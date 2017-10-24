<?php

$db_host = "66.112.76.254";
$db_username = "";
$db_pass = "";
$db_name = "Maxs_shag_house";

$conn2 = mysqli_connect("$db_host", "$db_username", "$db_pass", "$db_name");

	if(!$conn2) {
		die('error msg' . mysqli_connect_error());
	}
	else {
		$query = "INSERT INTO Maxs_shag_house manufacturers (manufactID, manufactName ) SELECT manufactID, manufactName FROM cms manufacturer";
		$insert = mysqli_query($conn2, $query);
	}

mysqli_close($conn2);
?>
