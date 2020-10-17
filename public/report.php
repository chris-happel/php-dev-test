<html>
	<head>
		<title>PHP Report Test</title>
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


			// Because we keep track of the comments to search for in lists,
			// we lose a little bit of flexibility by not being able to add 
			// extra conditions to the search. But we gain future customization
			// by simply adding new extries to these two arrays. These could be
			// added to a config somehow to make it even easier down the road too.
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
			$db_connection->close();
		?>
	</body>
</html>