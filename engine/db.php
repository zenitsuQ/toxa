<?php
	require("config.php");

	$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

	if (!$db)
		die("Ошибка подключения к базе данных! Причина: " . mysqli_connect_error());

	mysqli_query($db, "SET NAMES 'utf8mb4'"); //Стандартная кодировка - UTF-8