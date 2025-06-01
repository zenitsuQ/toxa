<?php
	session_name('TTWES');							// Даём имя сессии

	if (!isset($_COOKIE['TTWES']))					// Если есть печенька с SID - не трогаем
		session_id(session_create_id("TTSID-"));	// Если нет - генерим новый SID с префиксом TTSID-

	session_start();								// Запускаем сессии

	require_once("../engine/functions.php");		// Подключаем функции

	$id   = (int) $_GET['id'];
	$page = (int) $_GET['page'];

	if ($page == 0)
		$page = 1;

	$pagedb = ($page - 1) * 10;

	$can_use = T_Admin();

	if (!empty($_SESSION['pass']) && $can_use)
	{
		if (isset($_GET['index']) || empty($_GET))
		{
			$title   = "Главная";
			$current = "index";
		}
		else if (isset($_GET['items']))
		{
			$title   = "Товары";
			$current = "items";
		}
		else if (isset($_GET['feed']))
		{
			$title   = "Отзывы";
			$current = "feed";
		}
		else if (isset($_GET['orders']))
		{
			$title   = "Заказы";
			$current = "orders";
		}
		else if (isset($_GET['news']))
		{
			$title   = "Акции";
			$current = "news";
		}
		else if (isset($_GET['pages']))
		{
			$title   = "Страницы";
			$current = "pages";
		}
		else if (isset($_GET['cats']))
		{
			$title   = "Категории";
			$current = "cats";
		}
		else if (isset($_GET['subcats']))
		{
			$title   = "Подкатегории";
			$current = "subcats";
		}
		else if (isset($_GET['callback']))
		{
			$title   = "Обратная связь";
			$current = "callback";
		}
		else if (isset($_GET['form']))
		{
			$title   = "Заявки на перенос";
			$current = "form";
		}
		else if (isset($_GET['settings']))
		{
			$title   = "Настройки";
			$current = "settings";
		}
		else
		{
			$title   = "Ошибка";
			$current = "err";
		}

		define("CURRENT_PAGE", $current);
		require_once("template/header.php");

		switch ($current)
		{
			//Главная
			case 'index': {
				require_once("modules/main.php");
			}
			break;
			//Заказы
			case 'orders': {
				require_once("modules/orders.php");
			}
			break;
			//Товары
			case 'items': {
				require_once("modules/items.php");
			}
			break;
			//Отзывы
			case 'feed': {
				require_once("modules/feedbacks.php");
			}
			break;
			//Новости
			case 'news': {
				require_once("modules/news.php");
			}
			break;
			//Страницы
			case 'pages': {
				require_once("modules/pages.php");
			}
			break;
			//Категории
			case 'cats': {
				require_once("modules/cats.php");
			}
			break;
			//Подкатегории
			case 'subcats': {
				require_once("modules/subcats.php");
			}
			break;
			//Обратная связь
			case 'callback': {
				require_once("modules/callback.php");
			}
			break;
			//Заявки на перенос
			case 'form': {
				require_once("modules/form.php");
			}
			break;
			//Настройки
			case 'settings': {
				require_once("modules/settings.php");
			}
			break;
			default: {
				echo '<center><strong><font color="red">Раздел не существует! Если он действительно должен быть - напишите его сами.</font></strong></center>';
			}
			break;
		}

		require_once("template/footer.php");
	}
	else
	{
		T_Error(403, "У Вас нет доступа в административный раздел.");
	}
?>