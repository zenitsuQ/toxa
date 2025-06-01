<?php
	// ================ Сессии ================

	session_name('TTWES');							// Даём имя сессии

	if (!isset($_COOKIE['TTWES']))					// Если есть печенька с SID - не трогаем
	{
		if (empty($_COOKIE['remember']))
			session_id(session_create_id("TTSID-"));	// Если нет - генерим новый SID с префиксом TTSID-
		else
			session_id($_COOKIE['sid']);				// Восстанавливаем старую сессию, если стояла галочка "Запомнить меня"
	}

	session_start();								// Запускаем сессии

	require_once("engine/functions.php");			// Подключаем основные функции

	$is_auth = -1;									// Переменная проверки авторизации
	if (isset($_SESSION['id']))
		if (!empty($_SESSION['id']))
			$is_auth = T_CheckSession(session_id());

	// Проверка сессии на жизнеспособность
	if ($is_auth != -1)
		if ($is_auth == 3)
			T_Error(500, "Невозможно сохранить сессию. Доступ к сайту ограничен.");
		else
			if ($is_auth != 1)
				header("Location: /");

	// ================== ЧПУ ==================

	$_action  = "";
	$_service = "index";
	$_param   = "";
	$_extra   = "";
	$_extra1  = "";

	// Семантический URL + навигация по сайту
	if ($_SERVER['REQUEST_URI'] != '/')
	{
		$url_path = Parse_URL(MB_StrToLower($_SERVER['REQUEST_URI'], "UTF-8"), PHP_URL_PATH);
		$url_parts = Explode('/', Trim($url_path, ' /'));
		
		$_service = $url_parts[0];
		$_action  = $url_parts[1];
		$_param   = $url_parts[2];
		$_extra   = $url_parts[3];
		$_extra1  = $url_parts[4];
	}
	else
	{
		$_action  = "";
		$_service = "index";
		$_param   = "";
		$_extra   = "";
		$_extra1  = "";
	}

	define("CURRENT_SECTION", $_service);
	define("CURRENT_ACTION", $_action);
	define("CURRENT_PARAM", $_param);
	define("CURRENT_EXTRA", $_extra);
	define("CURRENT_EXTRA1", $_extra1);

	// Проверка активен ли сайт
	if (T_SiteActive())
	{
		// Распределение между разделами
		switch (CURRENT_SECTION)
		{
			case 'index': 		require("engine/main.php");				break; 	// Главная
			case 'api': 		require("engine/api.php");				break; 	// API
			case 'cron': 		require("engine/cron.php");				break; 	// CRON
			case 'auth': 		require("engine/users.php");			break;	// Авторизация
			case 'exit':		require("engine/users.php");			break;	// Выход
			case 'reg': 		require("engine/users.php");			break;	// Регистрация
			case 'restore': 	require("engine/users.php");			break;	// Восстановление пароля
			case 'pages': 		require("engine/pages.php");			break;	// Страницы сайта
			case 'pa':			require("engine/pa.php");				break;	// Личный кабинет
			default:
			{
				// Автоматическое подключение модулей из БД
				$modules  = T_GetData("SELECT `pointer` FROM `modules` WHERE `is_active` = 1 ORDER BY `pointer`", "", 1);
				$services = array();

				foreach ($modules as $module)
				{
					$m = $module['pointer'];

					$services[$m] = $m;
				}

				if (!empty($services[CURRENT_SECTION]))
					require("modules/" . CURRENT_SECTION . ".php");
				else
					T_Error(404, "Такого раздела или файла не существует!");
			}
		}
	}
	else
	{
		$_SiteTitle = "Доступ закрыт";
		require("templates/" . T_Template() . "/layouts/header.php");

		$inactive_text = "<center>" . T_GetData("SELECT `inactive_message` FROM `settings`", "inactive_message") . "</center>";

		echo $inactive_text;
	}

	require("templates/" . T_Template() . "/layouts/footer.php");				// Подключаем подвал сайта

	// === Team-Tech.ru Web Engine v. 1.4 beta ===
	// ====== Kizilov Vladimir Vladimirovich ======
	// =================== 2020 ===================
?>