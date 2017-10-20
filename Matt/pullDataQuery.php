<?php

require_once 'insertDataQuery.php';

$db_host = "66.112.76.254";
$db_username = "root";
$db_pass = "adamserver5";
$db_name = "cms";

$conn = mysqli_connect("$db_host", "$db_username", "$db_pass", "$db_name");

	if(!$conn) {
		die('error msg' . mysqli_connect_error());
	}
	else {
		$sql = "INSERT INTO Maxs_shag_house.dbo.manufacturers (manufactID, manufactName) SELECT manufactID, manufactName FROM cms.dbo.manufacturer";
		$result = mysqli_query($conn, $sql);

		foreach($result as $key => $manufactID) {
			foreach($manufactID as $value => $target) {
				echo $target;
			}
		}
		
	}
mysqli_close($conn);
?>