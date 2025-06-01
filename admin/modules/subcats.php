<?php
	$pagecount = T_GetData("SELECT COUNT(`id`) AS `cnt` FROM `shop_subcategories`", "cnt");
	$pagecount = ceil($pagecount / 10);

	if (isset($_GET['zero']))
	{
		$q = T_GetData("SELECT `id` FROM `shop_subcategories`");
		if (empty($q))
		{
			T_GetData("ALTER TABLE `shop_subcategories` AUTO_INCREMENT = 1", "", 1);
			echo '<meta http-equiv="refresh" content="3;?subcats"><center><strong>AUTO_INCREMENT таблицы "shop_subcategories" теперь равен 1.</strong></center>';
		}
		else
			echo '<center><font color="red"><strong>Таблица категорий не пуста!</strong></font></center>';

		require_once("template/footer.php");
		return;
	}

	// Просмотр

	if (!isset($_GET['add']) && !isset($_GET['drop']) && !isset($_GET['edit']) && empty($id))
	{
		echo '<center><div class="add" onclick="location.href='."'".'?subcats&add'."'".'" title="Добавить">+</div></center><br>';

		$q = T_GetData("SELECT `id` FROM `shop_subcategories`");
		if (empty($q))
		{
			echo '<center><strong>Нет категорий.</strong></center>';
		}
		else
		{
			echo '<br><table width="95%" align="center" valign="top"><tr><td width="5%" align="center"><strong>ID</strong></td><td width="23%" align="center"><strong>Дата создания</strong></td><td width="55%" align="center"><strong>Заголовок</strong></td><td width="7%" align="center"><strong>Публично?</strong></td><td width="10%" align="center"><strong>Управление</strong></td></tr>';
		
			$q = T_GetData("SELECT * FROM `shop_subcategories` ORDER BY `id` DESC LIMIT $pagedb, 10", "", 1);
			foreach ($q as $qq)
			{
				$nid      = (int) $qq['id'];
				$ndate    = T_DateFormat($qq['date'], $site_df);
				$ntitle   = $qq['name'];
				$semantic = $nid . "-" . T_Semantic($ntitle);
				$is_pub   = (int) $qq['is_public'];

				$template = T_Template();

				$pub = "Нет";
				if ($is_pub)
					$pub = "Да";

				PRINT <<<HERE
<tr><td><a href="/shop/subcategory/$semantic" target="_blank">$nid</a></td><td>$ndate</td><td>$ntitle</td><td align="center">$pub</td><td align="center"><a href="?subcats&edit&id=$nid" title="Редактировать"><img src="../templates/$template/assets/img/edit.png" alt="Редактировать"></a> <a href="?subcats&drop&id=$nid" title="Удалить"><img src="../templates/$template/assets/img/drop.png" alt="Удалить"></a></td></tr>
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
						echo '| <a href="?subcats&page='.$i.'">'.$i.'</a> ';
					}
				}
				echo "|</center><br>";
			}
		}
	}
	else if (isset($_GET['edit']) && (!empty($id) || isset($_GET['id']))) // Редактирование
	{
		if ((empty($_POST['title']) || empty($_POST['text'])) && isset($_POST['done']))
			echo '<center><font color="red"><strong>Заполнению подлежат <u>ОБА</u> поля, ' . $_SESSION[login] . '!</strong></font></center>';

		$ntitle = $_POST['title'];
		$ntext  = $_POST['text'];
		$ncat   = (int) $_POST['cat'];
		$is_pub = (int) $_POST['is_pub'];

		if (!isset($_POST['done']) || (isset($_POST['done']) && (empty($_POST['title']) || empty($_POST['text']))))
		{
			$q = T_GetData("SELECT * FROM `shop_subcategories` WHERE `id` = '$id'");
			$ndate  = T_DateFormat($q['date'], $site_df);
			$ntitle = $q['name'];
			$ntext  = $q['description'];
			$ncat   = $q['cat_id'];
			$is_pub = (int) $q['is_public'];

			$checked = "";
			if ($is_pub)
				$checked = " checked";

			$cats      = T_GetData("SELECT `id`, `name` FROM `shop_categories`", "", 1);
			$cats_list = "";
			foreach ($cats as $cat)
			{
				$selected = "";
				if ($cat['id'] == $ncat)
					$selected = " selected";
				
				$cats_list .= '<option value="' . $cat['id'] . '"' . $selected . '>' . $cat['name'] . '</option>';
			}

			PRINT <<<HERE
<center>
<form action="?subcats&edit&id=$id" method="POST">
<strong>Редактирование подкатегории</strong>
<br><br>
Подкатегория создана: $ndate
<br>
$last_edit
<br>
<strong>Имя:</strong><br>
<input type="text" name="title" placeholder="Введите заголовок страницы..." style="width: 90%;" value='$ntitle' title="Заголовок" maxlength="100">
<br><br>
<strong>Описание:</strong><br>
	<button class="format" title="Полужирный" type="button" onclick="AddBB('b');"><img src="style/img/bb/b.png" alt="Полужирный"></button>
	<button class="format" type="button" title="Курсивный" onclick="AddBB('i');"><img src="style/img/bb/i.png" alt="Курсивный"></button>
	<button class="format" title="Подчёркнутый" type="button" onclick="AddBB('u');"><img src="style/img/bb/u.png" alt="Подчёркнутый"></button>
	<button class="format" title="Зачёркнутый" type="button" onclick="AddBB('s');"><img src="style/img/bb/s.png" alt="Зачёркнутый"></button>
	<button class="format" title="Ссылка" type="button" onclick="AddBB('url');"><img src="style/img/bb/url.png" alt="Ссылка"></button>
	<button class="format" title="Цвет" type="button" onclick="AddBB('color');"><img src="style/img/bb/color.png" alt="Цвет"></button>
	<button class="format" title="По центру" type="button" onclick="AddBB('center');"><img src="style/img/bb/center.png" alt="По центру"></button>
	<button class="format" title="Отступ" type="button" onclick="AddSym('	');"><img src="style/img/bb/tab.png" alt="Отступ"></button><br><br>
<textarea name="text" id="text" placeholder="Введите текст страницы..." title="Текст">$ntext</textarea>
<br><strong>Категория:</strong><br>
<select name="cat" style="width: 90%;">
	$cats_list
</select>
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
			T_GetData("UPDATE `shop_subcategories` SET `cat_id` = $ncat, `name` = '$ntitle', `description` = '$ntext', `is_public` = $is_pub WHERE `id` = $id", "", 1);
			echo '<meta http-equiv="refresh" content="3;?subcats"><center><strong>Подкатегория изменена.</strong></center>';
		}
	}
	else if (isset($_GET['drop']) && !empty($id)) //Удаление
	{
		if (!isset($_POST['yes']))
		{
			echo '<center>Вы действительно хотите удалить эту подкатегорию?<br><br>Это действие необратимо и приведёт к удалению всех находящихся в ней товаров.<br><br><form action="?subcats&drop&id='.$id.'" method="POST"><button type="submit" name="yes">Да</button></form></center>';
		}
		else
		{
			T_GetData("DELETE FROM `shop_subcategories` WHERE `id` = $id", "", 1);
			echo '<meta http-equiv="refresh" content="3;?subcats"><center>Подкатегория успешно удалена.</center>';
		}
	}
	else if (isset($_GET['add']) && empty($id))   //Добавление
	{
		if ((empty($_POST['title']) || empty($_POST['text'])) && isset($_POST['done']))
			echo '<center><font color="red"><strong>Заполнению подлежат <u>ОБА</u> поля, ' . $_SESSION[login] . '!</strong></font></center>';

		$ntitle = $_POST['title'];
		$ntext  = $_POST['text'];
		$ncat   = $_POST['cat'];
		$is_pub = (int) $_POST['is_pub'];

		$cats      = T_GetData("SELECT `id`, `name` FROM `shop_categories`", "", 1);
		$cats_list = "";
		foreach ($cats as $cat)
		{
			$selected = "";
			if ($cat['id'] == $ncat)
				$selected = " selected";
				
			$cats_list .= '<option value="' . $cat['id'] . '"' . $selected . '>' . $cat['name'] . '</option>';
		}

		if (empty($cats_list))
			$cats_list = '<option value="0" disabled selected>Нет категорий</option>';

		if (!isset($_POST['done']) || (isset($_POST['done']) && (empty($_POST['title']) || empty($_POST['text']))))
		{
			PRINT <<<HERE
<center>
<form action="?subcats&add" method="POST">
<strong>Добавление подкатегории</strong>
<br><br>
<strong>Имя:</strong><br>
<input type="text" name="title" placeholder="Введите заголовок страницы..." style="width: 90%;" value='$ntitle' title="Заголовок" maxlength="100">
<br><br>
<strong>Описание:</strong><br>
<button class="format" title="Полужирный" type="button" onclick="AddBB('b');"><img src="style/img/bb/b.png" alt="Полужирный"></button>
			<button class="format" type="button" title="Курсивный" onclick="AddBB('i');"><img src="style/img/bb/i.png" alt="Курсивный"></button>
			<button class="format" title="Подчёркнутый" type="button" onclick="AddBB('u');"><img src="style/img/bb/u.png" alt="Подчёркнутый"></button>
			<button class="format" title="Зачёркнутый" type="button" onclick="AddBB('s');"><img src="style/img/bb/s.png" alt="Зачёркнутый"></button>
			<button class="format" title="Ссылка" type="button" onclick="AddBB('url');"><img src="style/img/bb/url.png" alt="Ссылка"></button>
			<button class="format" title="Цвет" type="button" onclick="AddBB('color');"><img src="style/img/bb/color.png" alt="Цвет"></button>
			<button class="format" title="По центру" type="button" onclick="AddBB('center');"><img src="style/img/bb/center.png" alt="По центру"></button>
			<button class="format" title="Отступ" type="button" onclick="AddSym('	');"><img src="style/img/bb/tab.png" alt="Отступ"></button><br><br>
<textarea name="text" id="text" placeholder="Введите текст страницы..." style="width: 90%; height: 150px; resize: vertical;" title="Текст">$ntext</textarea>
<br><strong>Категория:</strong><br>
<select name="cat">
	$cats_list
</select>
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
			T_GetData("INSERT INTO `shop_subcategories` (`date`, `user`, `cat_id`, `name`, `description`, `is_public`) VALUES (NOW(), $_userID, $ncat, '$ntitle', '$ntext', $is_pub)", "", 1);
			echo '<meta http-equiv="refresh" content="3;?subcats"><center><strong>Подкатегория добавлена.</strong></center>';
		}
	}
	else
		echo '<center><font color="red"><strong>Выполнен неподдерживаемый запрос. Необходима руковыпрямляющая машина.</strong><br><p align="right"><img src="style/img/hand_notline.png" alt="Юзер детектед"><img src="style/img/hand_line.png" alt="Руковыпрямляющая машина" height="230px"></p></font></center>';