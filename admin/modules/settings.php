<?php
	if ((empty($_POST['name']) || empty($_POST['delimiter'])) && isset($_POST['done']))
		echo '<center><font color="red"><strong>Заполнению подлежат <u>ВСЕ</u> поля, '.$_SESSION[login].'!</strong></font></center>';

	$sen      = (int) $_POST['sen'];
	$nname    = $_POST['name'];
	$ndel     = $_POST['delimiter'];
	$dformat  = (int) $_POST['dformat'];
	$curr_tmp = (int) $_POST['template'];
	$istext   = $_POST['istext'];

	if (!isset($_POST['done']) || (isset($_POST['done']) && (empty($_POST['name']) || empty($_POST['delimiter']))))
	{
		$q = T_GetData("SELECT * FROM `settings`");

		$nname    = $q['site_name'];
		$ndel     = $q['site_delimiter'];
		$dformat  = (int) $q['date_format'];
		$sen      = (int) $q['site_enabled'];
		$istext   = $q['inactive_message'];
		$curr_tmp = (int) $q['site_template'];

		if (!empty($sen))
			$ses = " checked";
		else
			$ses = "";

		switch ($dformat)
		{
			case 1: {
				$c1 = " selected";
				$c2 = "";
			}
			break;
			case 2: {
				$c1 = "";
				$c2 = " selected";
			}
			break;
			default: {
				$c1 = "";
				$c2 = " selected";
			}
			break;
		}

		$format1 = T_DateFormat(date("Y-m-d H:i:s"), 1);
		$format2 = T_DateFormat(date("Y-m-d H:i:s"), 2);

		$templates = T_GetData("SELECT * FROM `templates`", "", 1);
		$tmplist = "";

		foreach ($templates as $template)
		{
			if ($template['id'] == $curr_tmp)
				$cktmp = " selected";
			else
				$cktmp = "";

			$tmplist .= '<option value="' . $template['id'] . '"'. $cktmp .'>' . $template['name'] . '</option>';
		}

		PRINT <<<HERE
<center>
<strong>Основные настройки</strong>
<br><br>
<form action="?settings" method="POST">
	<div class="left_text">Имя сайта:</div>
	<input type="text" name="name" placeholder="Введите имя сайта" style="width: 90%;" value='$nname' title="Имя сайта" maxlength="100">
	<div class="left_text">Разделитель:</div>
	<input type="text" name="delimiter" placeholder="Введите разделитель заголовка" style="width: 90%;" value='$ndel' title="Разделитель заголовка" maxlength="3">
	<div class="left_text">Формат даты/времени:</div>
	<select name="dformat" style="width: 90%;" title="Формат даты и времени">
		<option disabled>Выберите формат даты и времени</option>
		<option value="1"$c1>$format1</option>
		<option value="2"$c2>$format2</option>
	</select>
	<div class="left_text">Шаблон сайта:</div>
	<select name="template" style="width: 90%;" title="Шаблон сайта">
		<option disabled>Выберите основной шаблон сайта</option>
		$tmplist
	</select>
	<br>
	<strong>Управление доступностью сайта</strong>
	<br>
	<table width="90%" align="center">
		<tr>
			<td width="20%">
				Состояние сайта:
			</td>
			<td width="80%">
				<label><input type="checkbox" name="sen" value="1"$ses>Активен</label>
			</td>
		</tr>
	</table>
	<div class="left_text">Текст при отключенном сайте:</div>
	<textarea name="istext" placeholder="Обращение к пользователям, если сайт отключен. Не более 500 символов." style="width: 90%; height: 70px; resize: vertical;" title="Текст отключенного сайта" maxlength="500">$istext</textarea>
	<br><br>
	<button type="submit" name="done">Сохранить</button>
</form>
</center>
HERE;
	}
	else
	{
		T_GetData("UPDATE `settings` SET `site_name` = '$nname', `site_delimiter` = '$ndel', `date_format` = $dformat, `site_template` = $curr_tmp, `site_enabled` = $sen, `inactive_message` = '$istext'", "", 1);

		echo '<meta http-equiv="refresh" content="3;?settings"><center><strong>Настройки сохранены.</strong></center>';
	}