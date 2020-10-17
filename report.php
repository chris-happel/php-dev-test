<html>
	<head>
		<title>PHP Report Test</title>
	</head>
	<body>
		<?php
			// There's got to be a better/more secure way to store this data
			$db_server_name = "localhost";
			$db_username = "root";
			$db_password = "root";

			$db_connection = new mysqli($db_server_name, $db_username, $db_password);
			if ($db_connection->connect_error) {
				echo "Could not connect to the database: $db_connection->connect_error";
			}
			else {
				echo 'Connected to the database!!!';
			}
		?>
	</body>
</html>