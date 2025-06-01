<?php
	$pagecount = T_GetData("SELECT `id` FROM `pages`", "", 2);
	$pagecount = ceil($pagecount / 10);

	if (!isset($_GET['add']) && !isset($_GET['drop']) && !isset($_GET['edit']) && empty($id))
	{
		echo '<center><div class="add" onclick="location.href='."'".'?pages&add'."'".'" title="Добавить">+</div></center><br>';

		$q = T_GetData("SELECT `id` FROM `pages`");
		if (empty($q))
		{
			echo '<center><strong>Нет страниц.</strong></center>';
		}
		else
		{
			echo '<br><table width="95%" align="center" valign="top"><tr><td width="5%" align="center"><strong>ID</strong></td><td width="23%" align="center"><strong>Дата создания</strong></td><td width="55%" align="center"><strong>Заголовок</strong></td><td width="7%" align="center"><strong>Публично?</strong></td><td width="10%" align="center"><strong>Управление</strong></td></tr>';
		
			$q = T_GetData("SELECT * FROM `pages` ORDER BY `id` DESC LIMIT $pagedb, 10", "", 1);
			while ($qq = mysqli_fetch_assoc($q))
			{
				$nid      = (int) $qq['id'];
				$ndate    = T_DateFormat($qq['date'], $site_df);
				$ntitle   = $qq['title'];
				$semantic = $nid . "-" . T_Semantic($ntitle);
				$is_pub   = (int) $qq['is_public'];

				$template = T_Template();

				$pub = "Нет";
				if ($is_pub)
					$pub = "Да";

				PRINT <<<HERE
<tr><td><a href="/pages/show/$semantic" target="_blank">$nid</a></td><td>$ndate</td><td>$ntitle</td><td align="center">$pub</td><td align="center"><a href="?pages&edit&id=$nid" title="Редактировать"><img src="../templates/$template/assets/img/edit.png" alt="Редактировать"></a> <a href="?pages&drop&id=$nid" title="Удалить"><img src="../templates/$template/assets/img/drop.png" alt="Удалить"></a></td></tr>
HERE;
			}
			echo '</table><br>';

			if ($pagecount > 1)
			{
				echo '<center>Страницы:
';
				for ($i = 0; $i++ < $pagecount;)
				{
					if ($page == $i)
					{
						echo '| <b>'.$i.'</b> ';
					}
					else
					{
						echo '| <a href="?pages&page='.$i.'">'.$i.'</a> ';
					}
				}
				echo "|</center><br>";
			}
		}
	}
	else if (isset($_GET['edit']) && (!empty($id) || isset($_GET['id']))) //Редактирование
	{
		if ((empty($_POST['title']) || empty($_POST['text'])) && isset($_POST['done']))
			echo '<center><font color="red"><strong>Заполнению подлежат <u>ОБА</u> поля, ' . $_SESSION[login] . '!</strong></font></center>';

		$ntitle = $_POST['title'];
		$ntext  = $_POST['text'];
		$is_pub = (int) $_POST['is_pub'];

		if (!isset($_POST['done']) || (isset($_POST['done']) && (empty($_POST['title']) || empty($_POST['text']))))
		{
			$q = T_GetData("SELECT * FROM `pages` WHERE `id` = '$id'");
			$ndate  = T_DateFormat($q['date'], $site_df);
			$udate  = $q['u_date'];
			$ntitle = $q['title'];
			$ntext  = $q['content'];
			$is_pub = (int) $q['is_public'];

			$checked = "";
			if ($is_pub)
				$checked = " checked";

			$last_edit = "";
			if (!empty($udate))
				$last_edit = "Страница изменена: " .  T_DateFormat($udate, $site_df) . "<br>";

			PRINT <<<HERE
<center>
<form action="?pages&edit&id=$id" method="POST">
<strong>Редактирование страницы</strong>
<br><br>
Страница создана: $ndate
<br>
$last_edit
<br>
<strong>Заголовок:</strong><br>
<input type="text" name="title" placeholder="Введите заголовок страницы..." style="width: 90%;" value='$ntitle' title="Заголовок" maxlength="100">
<br><br>
<strong>Текст:</strong><br>
	<button class="format" title="Полужирный" type="button" onclick="AddBB('b');"><img src="style/img/bb/b.png" alt="Полужирный"></button>
	<button class="format" type="button" title="Курсивный" onclick="AddBB('i');"><img src="style/img/bb/i.png" alt="Курсивный"></button>
	<button class="format" title="Подчёркнутый" type="button" onclick="AddBB('u');"><img src="style/img/bb/u.png" alt="Подчёркнутый"></button>
	<button class="format" title="Зачёркнутый" type="button" onclick="AddBB('s');"><img src="style/img/bb/s.png" alt="Зачёркнутый"></button>
	<button class="format" title="Ссылка" type="button" onclick="AddBB('url');"><img src="style/img/bb/url.png" alt="Ссылка"></button>
	<button class="format" title="Цвет" type="button" onclick="AddBB('color');"><img src="style/img/bb/color.png" alt="Цвет"></button>
	<button class="format" title="По центру" type="button" onclick="AddBB('center');"><img src="style/img/bb/center.png" alt="По центру"></button>
	<button class="format" title="Отступ" type="button" onclick="AddSym('	');"><img src="style/img/bb/tab.png" alt="Отступ"></button><br><br>
<textarea name="text" id="text" placeholder="Введите текст страницы..." title="Текст">$ntext</textarea>
<br>
<label><input type="checkbox" name="is_pub" value="1"$checked> Опубликовать</label>
<br><br>
<button type="submit" name="done" style="width: 90%; height: 50px; font-weight: bold;">СОХРАНИТЬ</button>
</form>
</center>
HERE;
		}
		else
		{
			T_GetData("UPDATE `pages` SET `u_date` = NOW(), `title` = '$ntitle', `content` = '$ntext', `is_public` = $is_pub WHERE `id` = $id", "", 1);
			echo '<meta http-equiv="refresh" content="3;?pages"><center><strong>Страница изменена.</strong></center>';
		}
	}
	else if (isset($_GET['drop']) && !empty($id)) //Удаление
	{
		if (!isset($_POST['yes']))
		{
			echo '<center>Вы действительно хотите удалить эту страницу? Это действие необратимо.<br><br><form action="?pages&drop&id='.$id.'" method="POST"><button type="submit" name="yes">Да</button></form></center>';
		}
		else
		{
			T_GetData("DELETE FROM `pages` WHERE `id` = $id", "", 1);
			echo '<meta http-equiv="refresh" content="3;?pages"><center>Страница успешно удалена.</center>';
		}
	}
	else if (isset($_GET['add']) && empty($id))   //Добавление
	{
		if ((empty($_POST['title']) || empty($_POST['text'])) && isset($_POST['done']))
			echo '<center><font color="red"><strong>Заполнению подлежат <u>ОБА</u> поля, ' . $_SESSION[login] . '!</strong></font></center>';

		$ntitle = $_POST['title'];
		$ntext  = $_POST['text'];
		$is_pub = (int) $_POST['is_pub'];

		if (!isset($_POST['done']) || (isset($_POST['done']) && (empty($_POST['title']) || empty($_POST['text']))))
		{
			PRINT <<<HERE
<center>
<form action="?pages&add" method="POST">
<br>
<strong>Добавление страницы</strong>
<br><br>
<i>Обратите внимание: ссылка на созданную страницу НЕ появится на сайте автоматически.</i>
<br><br>
<strong>Заголовок:</strong><br>
<input type="text" name="title" placeholder="Введите заголовок страницы..." style="width: 90%;" value='$ntitle' title="Заголовок" maxlength="100">
<br><br>
<strong>Текст:</strong><br>
<button class="format" title="Полужирный" type="button" onclick="AddBB('b');"><img src="style/img/bb/b.png" alt="Полужирный"></button>
			<button class="format" type="button" title="Курсивный" onclick="AddBB('i');"><img src="style/img/bb/i.png" alt="Курсивный"></button>
			<button class="format" title="Подчёркнутый" type="button" onclick="AddBB('u');"><img src="style/img/bb/u.png" alt="Подчёркнутый"></button>
			<button class="format" title="Зачёркнутый" type="button" onclick="AddBB('s');"><img src="style/img/bb/s.png" alt="Зачёркнутый"></button>
			<button class="format" title="Ссылка" type="button" onclick="AddBB('url');"><img src="style/img/bb/url.png" alt="Ссылка"></button>
			<button class="format" title="Цвет" type="button" onclick="AddBB('color');"><img src="style/img/bb/color.png" alt="Цвет"></button>
			<button class="format" title="По центру" type="button" onclick="AddBB('center');"><img src="style/img/bb/center.png" alt="По центру"></button>
			<button class="format" title="Отступ" type="button" onclick="AddSym('	');"><img src="style/img/bb/tab.png" alt="Отступ"></button><br><br>
<textarea name="text" id="text" placeholder="Введите текст страницы..." style="width: 90%; height: 150px; resize: vertical;" title="Текст">$ntext</textarea>
<br>
<label><input type="checkbox" name="is_pub" value="1"$checked> Опубликовать</label>
<br><br>
<button type="submit" name="done" style="width: 90%; height: 50px; font-weight: bold;">СОХРАНИТЬ</button>
</form>
</center>
HERE;
		}
		else
		{
			T_GetData("INSERT INTO `pages` (`date`, `title`, `content`, `is_public`) VALUES (NOW(), '$ntitle', '$ntext', $is_pub)", "", 1);
			echo '<meta http-equiv="refresh" content="3;?pages"><center><strong>Страница добавлена.</strong></center>';
		}
	}
	else
		echo '<center><font color="red"><strong>Выполнен неподдерживаемый запрос. Необходима руковыпрямляющая машина.</strong><br><p align="right"><img src="style/img/hand_notline.png" alt="Юзер детектед"><img src="style/img/hand_line.png" alt="Руковыпрямляющая машина" height="230px"></p></font></center>';