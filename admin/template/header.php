<!DOCTYPE html>
<html lang="ru">
	<head>
		<meta charset="utf-8">
		<title><?=$site_name?> | Административная панель | <?=$title?></title>
		<link href="style/main.css" rel="stylesheet" type="text/css">
		<script defer src="style/js/actions.js" type="text/javascript"></script>
	</head>
	<body>
		<div id="header">
			<a href="/" title="Вернуться на сайт"><img id="logo" src="../templates/<?=T_Template()?>/assets/img/favicon.png" alt="Логотип"></a>
			<div id="title">Административная панель</div>
		</div>
		<div id="menu">
			<div class="<?=T_CurrentPage("index")?>" onclick="location.href='?index'">
				Главная
			</div>
			<div class="<?=T_CurrentPage("items")?>" onclick="location.href='?items'">
				Товары
			</div>
			<div class="<?=T_CurrentPage("orders")?>" onclick="location.href='?orders'">
				Заказы
			</div>
			<div class="<?=T_CurrentPage("news")?>" onclick="location.href='?news'">
				Новости
			</div>
			<div class="<?=T_CurrentPage("pages")?>" onclick="location.href='?pages'">
				Страницы
			</div>
			<div class="<?=T_CurrentPage("cats")?>" onclick="location.href='?cats'">
				Категории
			</div>
			<div class="<?=T_CurrentPage("subcats")?>" onclick="location.href='?subcats'">
				Подкатегории
			</div>
			<div class="<?=T_CurrentPage("callback")?>" onclick="location.href='?callback'">
				Обратная связь
			</div>
			<div class="<?=T_CurrentPage("settings")?>" onclick="location.href='?settings'">
				Настройки
			</div>
		</div>
		<div id="main">
