<?php
	// ========================= БД =========================

	$db_host = "127.0.0.1";										// Хост/IP
	$db_user = "c1_agrofresh";									// Имя пользователя
	$db_pass = "NXkLVhkb#Ak5VzvafM9N";							// Пароль
	$db_name = "c1_agrofresh";									// Имя БД

	// ======================= Движок =======================

	$engine_key = "Mqz+3QqctW<W0,(+WJJ;iS:n=y[)=NCGXRG,lkCPnKbL7V081s:fW/1bL4-iA{%";

	if (!defined('ENGINE_DEBUG'))
		define("ENGINE_DEBUG", false);							// Режим отладки

	if (!defined('ENGINE_ENCRYPTION_KEY'))
		define("ENGINE_ENCRYPTION_KEY", $engine_key);			// Ключ шифрования 

	// ===================== ReCaptcha ======================

	// https://www.google.com/recaptcha/admin/
	$rc_key    = "6Lc33sIUAAAAAKQoj42SjK9vrawxbWvPFhdOD7ob"; 	// Публичный ключ
	$rc_secret = "6Lc33sIUAAAAAJFDp99ZwHYApt_JPBOklMsk1QRl";	// Секретный ключ

	// ============== Обязательные действия =================

	if (ENGINE_DEBUG)
	{
		ini_set('display_errors', 1);
		error_reporting(E_ALL);
		mysqli_report(MYSQLI_REPORT_ALL ^ ~MYSQLI_REPORT_STRICT);
	}
	else
		ini_set('display_errors', 0);