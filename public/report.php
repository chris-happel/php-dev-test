<html>
	<head>
		<title>PHP Report Test</title>
	</head>
	<body>
		<?php
			include '../private/env.php';

			$db_server_name = getenv("DB_SERVER_NAME");
			$db_username = getenv("DB_USERNAME");
			$db_password = getenv("DB_PASSWORD");

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