<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>
			<?=$site_name?> <?=$site_td?> <?=$_SiteTitle?>
		</title>
		<!-- CSS -->
		<link rel="stylesheet" type="text/css" href="/templates/<?=T_Template()?>/main.css?v=1.0">
		<!-- JS -->
		<script src="/templates/<?=T_Template()?>/assets/js/jquery-3.5.1.min.js" type="text/javascript"></script>
		<script defer src="/templates/<?=T_Template()?>/assets/js/main.js?v=1.0" type="text/javascript"></script>
	</head>
	<body>
		<div id="header">
			<div id="logo">
				<img src="/templates/<?=T_Template()?>/assets/img/logo.png" id="logo-img" alt="Логотип">
			</div>
			<div id="title">
				Team-Tech Web Engine Default Theme
			</div>
<?php if (!T_Authorized()): ?>
			<div id="auth">
				<button id="header-button" onclick="location.href='/auth'">Авторизация</button>
			</div>
<?php else: ?>
			<div id="profile">
				<img id="profile-img" src="/templates/<?=T_Template()?>/assets/img/avatars/default_m.png" alt="Аватар">
				<div id="profile-user"><?=$_SESSION['login']?></div>
				<img id="profile-exit" src="/templates/<?=T_Template()?>/assets/img/exit.png" alt="Выйти" title="Выйти" onclick="location.href='/exit'">
			</div>
<?php endif; ?>
		</div>
<?php require_once("menu.php"); ?>
		<div id="main">
			<div id="page-title">
				<?=$_SiteTitle?>
			</div>
