<?php
	$q      = T_GetData("SHOW TABLE STATUS", "", 1);
	$dbsize = 0;

	foreach($q as $row)
		$dbsize += $row["Data_length"] + $row["Index_length"];

	$ocount  = T_GetData("SELECT COUNT(`id`) AS `cnt` FROM `shop_orders` WHERE `status` <> 0", "cnt");
	$pcount  = T_GetData("SELECT COUNT(`id`) AS `cnt` FROM `pages`", "cnt");
	$ucount  = T_GetData("SELECT COUNT(`id`) AS `cnt` FROM `users`", "cnt");
	$icount  = T_GetData("SELECT COUNT(`id`) AS `cnt` FROM `shop_items`", "cnt");
	//$fcount  = T_GetData("SELECT COUNT(`id`) AS `cnt` FROM `feedbacks`", "cnt");
	$ncount  = T_GetData("SELECT COUNT(`id`) AS `cnt` FROM `news`", "cnt");

	$dbsize  = T_FormatFileSize($dbsize);
	$mysql_s = $db->server_info;
	$mysql_s = MB_SubStr($mysql_s, 0, MB_StrPos($mysql_s, "-"), "UTF-8");
	$php_v   = phpversion();
	$server_v = "Apache 2.4.59";
	//$server_v = apache_get_version();
	//MB_SubStr(apache_get_version(), 0, MB_StrPos(apache_get_version(), " ", "UTF-8"), "UTF-8");
	//$php_v   = MB_SubStr($php_v, 0, MB_StrPos($php_v, "-"), "UTF-8");
	$date    = T_DateFormat(date("Y-m-d H:i:s"), $site_df);
	$engine_v = ENGINE_VERSION;

	PRINT <<<HERE
<center>
	<em><strong>Добро пожаловать в административную панель, $_SESSION[login]!</strong></em>
</center>
<br>
<table width="100%" align="center">
	<tr>
		<td colspan="4" align="center" class="tds">Информация о сайте<br><br></td>
	</tr>
	<tr>
		<td width="25%"><strong>Ваш IP:</strong></td>
		<td width="35%">$_SERVER[REMOTE_ADDR]</td>
		<td width="25%"><strong>Заказов:</strong></td>
		<td width="15%">$ocount</td>
	</tr>
	<tr>
		<td><strong>Сегодня:</strong></td>
		<td>$date</td>
		<td width="25%"><strong>Товаров:</strong></td>
		<td width="15%">$icount</td>
	</tr>
	<tr>
		<td><strong>Размер БД:</strong></td>
		<td>$dbsize</td>
		<td width="25%"><strong>Страниц:</strong></td>
		<td width="15%">$pcount</td>
	</tr>
	<tr>
		<td><strong>Версия PHP:</strong></td>
		<td>$php_v</td>
		<td><strong>Акций:</strong></td>
		<td>$ncount</td>
	</tr>
	<tr>
		<td><strong>Версия MySQL:</strong></td>
		<td>$mysql_s</td>
		<td><strong>Пользователей:</strong></td>
		<td>$ucount</td>
	</tr>
	<tr>
		<td><strong>Версия Веб-сервера:</strong></td>
		<td>$server_v</td>
		
	</tr>
</table>
HERE;
