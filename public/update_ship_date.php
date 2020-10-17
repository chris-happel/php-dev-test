<html>
	<head>
		<title>PHP Update Ship Date Test</title>
	</head>
	<body>
		<?php
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
							echo "Could not update orderid " . $row["orderid"] . " with found date [$date_str]. Error: $db_connection->error";
						}
					}
				}
				$query_result->free_result();
			}

			$db_connection->close();
		?>
	</body>
</html>