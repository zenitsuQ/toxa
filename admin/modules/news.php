<?php
	$pagecount = T_GetData("SELECT COUNT(`id`) AS `cnt` FROM `news`", "cnt");
	$pagecount = ceil($pagecount / 10);

	if (isset($_GET['zero']))
	{
		$q = T_GetData("SELECT `id` FROM `news`");
		if (empty($q))
		{
			T_GetData("ALTER TABLE `news` AUTO_INCREMENT = 1", "", 1);
			echo '<meta http-equiv="refresh" content="3;?news"><center><strong>AUTO_INCREMENT таблицы "news" теперь равен 1.</strong></center>';
		}
		else
			echo '<center><font color="red"><strong>Таблица новостей не пуста!</strong></font></center>';

		require_once("template/footer.php");
		return;
	}

	if (!isset($_GET['add']) && !isset($_GET['drop']) && !isset($_GET['edit']) && empty($id))
	{
		echo '<center><div class="add" onclick="location.href='."'".'?news&add'."'".'" title="Добавить">+</div></center>';

		$q = T_GetData("SELECT `id` FROM `news`");
		if (empty($q))
		{
			echo '<br><center><strong>Нет новостей.</strong><br><br><a href="?news&zero" onclick="return confirm(\'Вы уверены?\')">Обнулить инкремент</a></center>';
		}
		else
		{
			echo '<br><table width="95%" align="center" valign="top"><tr><td width="5%" align="center"><strong>ID</strong></td><td width="10%" align="center"><strong>Картинка</strong></td><td width="20%" align="center"><strong>Дата создания</strong></td><td width="50%" align="center"><strong>Заголовок</strong></td><td width="5%" align="center"><strong>Публично?</strong></td><td width="10%" align="center"><strong>Управление</strong></td></tr>';
	
			$q = T_GetData("SELECT * FROM `news` ORDER BY `id` DESC LIMIT $pagedb, 10", "", 1);
			foreach ($q as $qq)
			{
				$nid      = $qq['id'];
				$ndate    = T_DateFormat($qq['date'], $site_df);
				$ntitle   = $qq['title'];
				$is_pub   = (int) $qq['is_public'];

				$semantic = $nid . "-" . T_Semantic($ntitle);

				$cover    = T_GetData("SELECT `file_name`, `hash`, `height`, `width` FROM `news_images` WHERE `news_id` = $nid AND `is_cover` = 1 LIMIT 1");
				$c_path   = "";

				if (!empty($cover))
				{
					$c_hash = $cover['hash'];
					$c_name = $cover['file_name'];

					$c_s_name = StrToLower(MB_SubStr($c_name, 0, 1, 'UTF-8'));
					$c_s_hash = StrToLower(MB_SubStr($c_hash, 0, 2, 'UTF-8'));

					$c_path = "../files/news/$c_s_name/$c_s_hash/$c_name";
				}
				else
					$c_path = "../templates/" . T_Template() . "/assets/img/no_image.png";

				$template = T_Template();

				$pub = "Нет";
				if ($is_pub)
					$pub = "Да";

				PRINT <<<HERE
<tr><td><a href="/news/show/$semantic" target="_blank">$nid</a></td><td><img src="$c_path" style="max-height: 70px; max-width: 70px;"></td><td>$ndate</td><td>$ntitle</td><td align="center">$pub</td><td align="center"><a href="?news&edit&id=$nid" title="Редактировать"><img src="/templates/$template/assets/img/edit.png" alt="Редактировать"></a> <a href="?news&drop&id=$nid" title="Удалить"><img src="/templates/$template/assets/img/drop.png" alt="Удалить"></a></td></tr>
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
						echo '| <a href="?news&page='.$i.'">'.$i.'</a> ';
					}
				}
				echo "|</center><br>";
			}
		}
	}
	else if (isset($_GET['edit']) && !empty($id)) //Редактирование
	{
		$q = T_GetData("SELECT * FROM `news` WHERE `id` = $id");

		if ((empty($_POST['title']) || empty($_POST['text'])) && isset($_POST['done']))
			echo '<center><font color="red"><strong>Заполнению подлежат <u>ОБА</u> поля, '.$_SESSION[login].'!</strong></font></center>';

		$ntitle   = $_POST['title'];
		$ntext    = $_POST['text'];
		$is_pub   = (int) $_POST['is_pub'];

		$cover    = T_GetData("SELECT `file_name`, `hash`, `height`, `width` FROM `news_images` WHERE `news_id` = $id AND `is_cover` = 1 LIMIT 1");
		$c_path   = "";

		if (!empty($cover))
		{
			$c_hash = $cover['hash'];
			$c_name = $cover['file_name'];

			$c_s_name = StrToLower(MB_SubStr($c_name, 0, 1, 'UTF-8'));
			$c_s_hash = StrToLower(MB_SubStr($c_hash, 0, 2, 'UTF-8'));

			$c_path = "../files/news/$c_s_name/$c_s_hash/$c_name";
		}
		else
			$c_path = "../templates/" . T_Template() . "/assets/img/no_image.png";

		// Файл

		if ($_FILES['content']['size'] <= 5242880) //Если файл меньше или равен 5 МБ - продолжаем
		{
			if (isset($_FILES['content']['name'])) //отправлялась ли переменная
			{
				if (!empty($_FILES['content']['name']))
				{
					$_adir = '../files/';

					if (!is_dir($_adir))
						MkDir($_adir, 0755);

					$_adir = $_adir . 'news/';

					if (!is_dir($_adir))
						MkDir($_adir, 0755);

					if (preg_match('/[.](JPG)|(jpg)|(jpeg)|(JPEG)|(png)|(PNG)$/', $_FILES['content']['name'])) //проверка формата исходного изображения
					{
						$filename = $_FILES['content']['name'];
						$source   = $_FILES['content']['tmp_name'];
						$fsize    = $_FILES['content']['size'];

						$fhash    = hash_file("sha512", $source);

						$path   = "";
						$dname  = "";
						$dhname = "";

						do
						{
							$fname = T_RSGen(30) . '.' . end(explode(".", $filename));

							$dname  = StrToLower(MB_SubStr($fname, 0, 1, 'UTF-8'));
							$dhname = StrToLower(MB_SubStr($fhash, 0, 2, 'UTF-8'));

							$mom_dir = $_adir . $dname . "/";
							if (!is_dir($mom_dir))
								MkDir($mom_dir, 0755);

							$end_dir = $mom_dir . $dhname . "/";
							if (!is_dir($end_dir))
								MkDir($end_dir, 0755);

							$path = $end_dir . $fname;
						}
						while (file_exists($path));

						list($width, $height) = GetImageSize($source);

						move_uploaded_file($source, $path);

						if (!empty($cover))
						{
							$q = T_GetData("UPDATE `news_images` SET `file_name` = '$fname', `size` = $fsize, `hash` = '$fhash', `height` = $height, `width` = $width WHERE `news_id` = $id AND `is_cover` = 1", "", 1);
							if ($q)
								unlink($c_path);
							else
								unlink($path);
						}
						else
							T_GetData("INSERT INTO `news_images` (`news_id`, `user`, `file_name`, `size`, `hash`, `height`, `width`, `description`, `is_cover`) VALUES ($id, $_userID, '$fname', $fsize, '$fhash', $height, $width, 'Нет описания', 1)", "", 1);
					}
				}
			}
		}
		else
		{
			echo '<center><strong>Файл слишком большой!</strong></center>';
		}

		if (!isset($_POST['done']) || (isset($_POST['done']) && (empty($_POST['title']) || empty($_POST['text']))))
		{
			$ndate  = T_DateFormat($q['date'], $site_df);
			$ntitle = $q['title'];
			$ntext  = $q['text'];
			$udate  = $q['u_date'];
			$is_pub = (int) $q['is_public'];

			$checked = "";
			if ($is_pub)
				$checked = " checked";

			$last_edit = "";
			if (!empty($udate))
				$last_edit = "Новость изменена: " .  T_DateFormat($udate, $site_df) . "<br>";

			$cover    = T_GetData("SELECT `file_name`, `hash`, `height`, `width` FROM `news_images` WHERE `news_id` = $id AND `is_cover` = 1 LIMIT 1");
			$c_path   = "";

			if (!empty($cover))
			{
				$c_hash = $cover['hash'];
				$c_name = $cover['file_name'];

				$c_s_name = StrToLower(MB_SubStr($c_name, 0, 1, 'UTF-8'));
				$c_s_hash = StrToLower(MB_SubStr($c_hash, 0, 2, 'UTF-8'));

				$c_path = "../files/news/$c_s_name/$c_s_hash/$c_name";
			}
			else
				$c_path = "../templates/" . T_Template() . "/assets/img/no_image.png";

			PRINT <<<HERE
<center>
<form enctype="multipart/form-data" action="?news&edit&id=$id" method="POST">
<strong>Редактирование новости</strong>
<br><br>
Новость добавлена: $ndate
<br>
$last_edit
<br>
<strong>Заголовок:</strong><br>
<input type="text" name="title" placeholder="Введите заголовок новости..." style="width: 90%;" value='$ntitle' title="Заголовок" maxlength="100">
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
<textarea name="text" id="text" placeholder="Введите текст новости..." style="width: 90%; height: 150px; resize: vertical;" title="Текст">$ntext</textarea>
<br>
<label><input type="checkbox" name="is_pub" value="1"$checked> Опубликовать</label>
<br><br>
<strong>Изображение:</strong>
<br>
<img src="$c_path" style="max-height: 150px; max-width: 150px;">
<input type="file" name="content" accept="image/jpeg,image/png" title="Загрузить изображение вещи" style="width: 90%; font-weight: bold;">
<br><br>
<button type="submit" name="done" style="width: 90%; height: 50px; font-weight: bold;">СОХРАНИТЬ</button>
</form>
</center>
HERE;
		}
		else
		{
			T_GetData("UPDATE `news` SET `u_date` = NOW(), `title` = '$ntitle', `text` = '$ntext', `is_public` = $is_pub WHERE `id` = $id", "", 1);
			echo '<meta http-equiv="refresh" content="3;?news"><center><strong>Новость изменена.</strong></center>';
		}
	}
	else if (isset($_GET['drop']) && !empty($id)) //Удаление
	{
		if (!isset($_POST['yes']))
		{
			echo '<center>Вы действительно хотите удалить эту новость? Это действие необратимо.<br><br><form action="?news&drop&id='.$id.'" method="POST"><button type="submit" name="yes">Да</button></form></center>';
		}
		else
		{
			$cover    = T_GetData("SELECT `file_name`, `hash`, `height`, `width` FROM `news_images` WHERE `news_id` = $id AND `is_cover` = 1 LIMIT 1");
			$c_path   = "";

			if (!empty($cover))
			{
				$c_hash = $cover['hash'];
				$c_name = $cover['file_name'];

				$c_s_name = StrToLower(MB_SubStr($c_name, 0, 1, 'UTF-8'));
				$c_s_hash = StrToLower(MB_SubStr($c_hash, 0, 2, 'UTF-8'));

				$c_path = "../files/news/$c_s_name/$c_s_hash/$c_name";
			}

			if (!empty($c_path))
				unlink($c_path);

			T_GetData("DELETE FROM `news` WHERE `id` = $id", "", 1);
			echo '<meta http-equiv="refresh" content="3;?news"><center>Новость успешно удалена.</center>';
		}
	}
	else if (isset($_GET['add']) && empty($id))   //Добавление
	{
		if ((empty($_POST['title']) || empty($_POST['text'])) && isset($_POST['done']))
			echo '<center><font color="red"><strong>Заполнению подлежат <u>ОБА</u> поля, ' . $_SESSION[login] . '!</strong></font></center>';
		
		$ntitle = $_POST['title'];
		$ntext  = $_POST['text'];
		$is_pub = (int) $_POST['is_pub'];

		if ($_FILES['content']['size'] <= 5242880) //Если файл меньше или равен 5 МБ - продолжаем
		{
			if (isset($_FILES['content']['name'])) //отправлялась ли переменная
			{
				if (!empty($_FILES['content']['name']))
				{
					$_adir = '../files/';

					if (!is_dir($_adir))
						MkDir($_adir, 0755);

					$_adir = $_adir . 'news/';

					if (!is_dir($_adir))
						MkDir($_adir, 0755);

					if (preg_match('/[.](JPG)|(jpg)|(jpeg)|(JPEG)|(png)|(PNG)$/', $_FILES['content']['name'])) //проверка формата исходного изображения
					{
						$filename = $_FILES['content']['name'];
						$source   = $_FILES['content']['tmp_name'];
						$fsize    = $_FILES['content']['size'];

						$fhash    = hash_file("sha512", $source);

						$path   = "";
						$dname  = "";
						$dhname = "";

						do
						{
							$fname = T_RSGen(30) . '.' . end(explode(".", $filename));

							$dname  = StrToLower(MB_SubStr($fname, 0, 1, 'UTF-8'));
							$dhname = StrToLower(MB_SubStr($fhash, 0, 2, 'UTF-8'));

							$mom_dir = $_adir . $dname . "/";
							if (!is_dir($mom_dir))
								MkDir($mom_dir, 0755);

							$end_dir = $mom_dir . $dhname . "/";
							if (!is_dir($end_dir))
								MkDir($end_dir, 0755);

							$path = $end_dir . $fname;
						}
						while (file_exists($path));

						list($width, $height) = GetImageSize($source);

						move_uploaded_file($source, $path);
					}
				}
			}
		}
		else
		{
			echo '<center><strong>Файл слишком большой!</strong></center>';
		}
		
		if (!isset($_POST['done']) || (isset($_POST['done']) && (empty($_POST['title']) || empty($_POST['text']))))
		{
			PRINT <<<HERE
<center>
<form enctype="multipart/form-data" action="?news&add" method="POST">
<strong>Добавление новости</strong>
<br><br>
<strong>Заголовок:</strong><br>
<input type="text" name="title" placeholder="Введите заголовок новости..." style="width: 90%;" value='$ntitle' title="Заголовок" maxlength="100">
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
<textarea name="text" id="text" placeholder="Введите описание новости..." style="width: 90%; height: 150px; resize: vertical;" title="Текст">$ntext</textarea>
<br>
<label><input type="checkbox" name="is_pub" value="1"$checked> Опубликовать</label>
<br><br>
<strong>Изображение:</strong>
<br>
<input type="file" name="content" accept="image/jpeg,image/png" title="Загрузить картинку" style="width: 90%; font-weight: bold;">
<br><br>
<button type="submit" name="done" style="width: 90%; height: 50px; font-weight: bold;">СОХРАНИТЬ</button>
</form>
</center>
HERE;
		}
		else
		{
			$q = T_GetData("INSERT INTO `news` (`date`, `user`, `title`, `text`, `is_public`) VALUES (NOW(), $_userID, '$ntitle', '$ntext', $is_pub)", "", 1);
			if ($q)
			{
				$q = (int) T_GetData("SELECT MAX(`id`) AS `max` FROM `news` WHERE `user` = $_userID", "max");

				T_GetData("INSERT INTO `news_images` (`news_id`, `user`, `file_name`, `size`, `hash`, `height`, `width`, `description`, `is_cover`) VALUES ($q, $_userID, '$fname', $fsize, '$fhash', $height, $width, 'Нет описания', 1)", "", 1);

				echo '<meta http-equiv="refresh" content="3;?news"><center><strong>Новость добавлена.</strong></center>';
			}
			else
				echo '<meta http-equiv="refresh" content="3;?news"><center><strong>Ошибка!</strong></center>';
		}
	}
	else
		echo '<center><font color="red"><strong>Выполнен неподдерживаемый запрос. Необходима руковыпрямляющая машина.</strong><br><p align="right"><img src="style/img/hand_notline.png" alt="Юзер детектед"><img src="style/img/hand_line.png" alt="Руковыпрямляющая машина" height="230px"></p></font></center>';