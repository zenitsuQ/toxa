<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>
			<?=$site_name?> <?=$site_td?> <?=$_SiteTitle?>
		</title>
		<!-- CSS -->
		<link rel="stylesheet" type="text/css" href="/templates/<?=T_Template()?>/style.css?v=1.0">
		<link href="/templates/<?=T_Template()?>/assets/img/favicon.png" rel="icon" sizes="32x32" type="image/png">
		<!-- JS -->
		<script src="/templates/<?=T_Template()?>/assets/js/jquery-3.5.1.min.js" type="text/javascript"></script>
		<script defer src="/templates/<?=T_Template()?>/assets/js/main.js?v=1.0" type="text/javascript"></script>
	</head>
	<body>
		<header class="wrapper">
			<?php require_once("menu.php"); ?>
		</header>
