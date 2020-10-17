<?php
	// This is slightly more secure, but I'm positive it could be better using a real .env file
	putenv("DB_SERVER_NAME=localhost");
	putenv("DB_USERNAME=username");
	putenv("DB_PASSWORD=password");
?>