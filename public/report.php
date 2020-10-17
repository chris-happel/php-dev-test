<?php
	function generateReport($db_connection) {
		// Because we keep track of the comments to search for in lists,
		// we lose a little bit of flexibility by not being able to add 
		// extra conditions to the search. But we gain future customization
		// by simply adding new extries to these two arrays. These could also
		// be added to a config somehow to make it even easier down the road.
		$reported_orders = "";
		$sql_query = "select orderid, comments from sweetwater_test ";
		$comment_group_names = [ "candy", "call me / don't call me", "who referred me", "signature requirements" ];
		$comment_group_like  = [ "candy", "call me",                 "referred",        "signature" ];
		for ($i = 0; $i < count($comment_group_names); $i++) {
			$sql_where_clause = "where comments like '%$comment_group_like[$i]%'";
			if ($query_result = $db_connection->query($sql_query . $sql_where_clause)) {
				echo "Comments about $comment_group_names[$i]: $query_result->num_rows [<br>";
				if ($query_result->num_rows > 0) {
					while($row = $query_result->fetch_assoc()) {
						echo $row["comments"] . "<br>";

						// Keep track of all the orders we've already reported so they don't
						// get displayed twice.
						$reported_orders = $reported_orders . $row["orderid"] . ", ";
					}
				}
				echo "]<br><br>";
				$query_result->free_result();
			}
		}

		// Get everything not already reported
		$reported_orders = substr($reported_orders, 0, -2);
		$sql_where_clause = "where orderid not in ($reported_orders)";
		if ($query_result = $db_connection->query($sql_query . $sql_where_clause)) {
			echo "Miscellaneous comments: $query_result->num_rows [<br>";
			if ($query_result->num_rows > 0) {
				while($row = $query_result->fetch_assoc()) {
					echo $row["comments"] . "<br>";
				}
			}
			echo "]<br><br>";

			$query_result->free_result();
		}
	}

	function updateShipDates($db_connection) {
		// I do not believe the test data set has any dates not in the MM/DD/YY format,
		// but I thought it best to include extra filtering in the like statement to
		// make it so it won't break if the format is changed in the future.
		$sql_query = "select orderid, comments from sweetwater_test where comments like '%Expected Ship Date: __/__/__%'";
		if ($query_result = $db_connection->query($sql_query)) {
			echo "Found $query_result->num_rows rows with an expected ship date.";
			if ($query_result->num_rows > 0) {
				while($row = $query_result->fetch_assoc()) {
					// Find the date portion of the string
					$date_pos = strpos($row["comments"], "Expected Ship Date: ") + strlen("Expected Ship Date: ");
					$date_str = substr($row["comments"], $date_pos, 8);
					$date = DateTime::createFromFormat("m/d/y", $date_str);
					$date_str = $date->format("Y-m-d");

					$sql_update = "update sweetwater_test set shipdate_expected = '$date_str' where orderid = " . $row["orderid"];
					if ($db_connection->query($sql_update) == FALSE) {
						echo "Could not update orderid " . $row["orderid"] . " with found date [$date_str]. Error: $db_connection->error<br>";
					}
				}
			}
			$query_result->free_result();
		}
	}

	// Grab db info.
	include '../private/env.php';

	$db_server_name = getenv("DB_SERVER_NAME");
	$db_username = getenv("DB_USERNAME");
	$db_password = getenv("DB_PASSWORD");
	$db_name = getenv("DB_NAME");

	// Connect to db.
	$db_connection = new mysqli($db_server_name, $db_username, $db_password, $db_name);
	if ($db_connection->connect_error) {
		die("Could not connect to the database: $db_connection->connect_error");
	}

	generateReport($db_connection);
	updateShipDates($db_connection);

	$db_connection->close();
?>