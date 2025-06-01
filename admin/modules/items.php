<?php
	$pagecount = T_GetData("SELECT COUNT(`id`) AS `cnt` FROM `shop_items`", "cnt");
	$pagecount = ceil($pagecount / 10);

	if (isset($_GET['zero']))
	{
		$q = T_GetData("SELECT `id` FROM `shop_items`");
		if (empty($q))
		{
			T_GetData("ALTER TABLE `shop_items` AUTO_INCREMENT = 1", "", 1);
			echo '<meta http-equiv="refresh" content="3;?items"><center><strong>AUTO_INCREMENT таблицы "shop_items" теперь равен 1.</strong></center>';
		}
		else
			echo '<center><font color="red"><strong>Таблица товаров не пуста!</strong></font></center>';

		require_once("template/footer.php");
		return;
	}

	// Вывод

	if (!isset($_GET['drop']) && !isset($_GET['edit']) && !isset($_GET['add']) && empty($id))
	{
		echo '<center><div class="add" onclick="location.href='."'".'?items&add'."'".'" title="Добавить">+</div></center>';
		$q = T_GetData("SELECT `id` FROM `shop_items`");
		if (empty($q))
		{
			echo '<br><center><strong>Нет товаров.</strong><br><br><a href="?items&zero" onclick="return confirm(\'Вы уверены?\')">Обнулить инкремент</a></center>';
		}
		else
		{
			echo '<br><table width="95%" align="center" valign="top"><tr><td width="5%" align="center"><strong>ID</strong></td><td width="10%" align="center"><strong>Вид</strong></td><td width="45%" align="center"><strong>Заголовок</strong></td><td width="10%" align="center"><strong>Цена</strong></td><td width="10%" align="center"><strong>Наличие</strong></td><td width="10%" align="center"><strong>Публичен?</strong></td><td width="10%" align="center"><strong>Управление</strong></td></tr>';
	
			$q = T_GetData("SELECT `id`, `name`, `price`, `count`, `is_public` FROM `shop_items` ORDER BY `id` DESC LIMIT $pagedb, 10", "", 1);
			foreach ($q as $qq)
			{
				$nid      = $qq['id'];
				$ntitle   = $qq['name'];
				$nprice   = $qq['price'];
				$ncount   = $qq['count'];
				$is_pub   = (int) $qq['is_public'];

				$template = T_Template();

				$pub = "Нет";
				if ($is_pub)
					$pub = "Да";

				$image = T_GetData("SELECT `file_name`, `hash` FROM `shop_items_images` WHERE `item_id` = $nid AND `is_default` = 1 LIMIT 1");

				if (!empty($image))
				{
					$dname  = StrToLower(MB_SubStr($image['file_name'], 0, 1, 'UTF-8'));
					$dhname = StrToLower(MB_SubStr($image['hash'], 0, 2, 'UTF-8'));
					$image  = "../files/shop/" . $dname . "/" . $dhname . "/" . $image['file_name'];
				}
				else
					$image = "../templates/" . T_Template() . "/assets/img/no_image.png";

				$semantic = $nid . '-' . T_Semantic($ntitle);

				PRINT <<<HERE
<tr><td>$nid</td><td align="center"><img src="$image" alt="$ntitle" width="50px" height="50px"></td><td><a href="/shop/item/$semantic" target="_blank">$ntitle</a></td><td align="center">$nprice ₽</td><td align="center">$ncount шт.</td><td align="center">$pub</td><td align="center"><a href="?items&edit&id=$nid" title="Редактировать"><img src="../templates/$template/assets/img/edit.png" alt="Редактировать"></a> <a href="?items&drop&id=$nid" title="Удалить"><img src="../templates/$template/assets/img/drop.png" alt="Удалить"></a></td></tr>
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
						echo '| <a href="?items&page='.$i.'">'.$i.'</a> ';
					}
				}
				echo "|</center><br>";
			}
		}
	}
	else if (isset($_GET['drop']) && !empty($id))
	{
		if (!isset($_POST['yes']))
		{
			echo '<center>Вы действительно хотите удалить этот товар? Это действие необратимо.<br><br><form action="?items&drop&id='.$id.'" method="POST"><button type="submit" name="yes">Да</button></form></center>';
		}
		else
		{			
			$image = T_GetData("SELECT `file_name`, `hash` FROM `shop_items_images` WHERE `item_id` = $id AND `is_default` = 1 LIMIT 1");

			$q = T_GetData("DELETE FROM `shop_items` WHERE `id` = $id", "", 1);
			if ($q)
			{
				if (!empty($image['file_name']))
				{
					$dname  = StrToLower(MB_SubStr($image['file_name'], 0, 1, 'UTF-8'));
					$dhname = StrToLower(MB_SubStr($image['hash'], 0, 2, 'UTF-8'));
					$image  = "../files/shop/" . $dname . "/" . $dhname . "/" . $image['file_name'];

					if (!UnLink($image))
						echo "<center>Ошибка удаления фотографии товара!</center>";
				}

				echo '<meta http-equiv="refresh" content="3;?items"><center>Товар успешно удалён.</center>';
			}
			else
				echo "<center>Ошибка удаления товара!</center>";
		}
	}
	else if (isset($_GET['edit']) && !empty($id))	// Редактирование
	{
		$q = T_GetData("SELECT * FROM `shop_items` WHERE `id` = $id");

		if ((empty($_POST['title']) || empty($_POST['about']) || empty($_POST['price'])) && isset($_POST['done']))
			echo '<center><font color="red"><strong>Заполнению подлежат <u>ВСЕ</u> поля, '.$_SESSION[login].'!</strong></font></center>';
		
		$ntitle   = $_POST['title'];
		$nabout   = $_POST['about'];
		$nprice   = $_POST['price'];
		$cat      = (int) $_POST['cat'];
		$scat     = (int) $_POST['scat'];
		$ncount   = (int) $_POST['count'];
		$is_pub   = (int) $_POST['is_pub'];

		$cover    = T_GetData("SELECT `file_name`, `hash`, `height`, `width` FROM `shop_items_images` WHERE `item_id` = $id AND `is_default` = 1 LIMIT 1");
		$c_path   = "";

		if (!empty($cover))
		{
			$c_hash = $cover['hash'];
			$c_name = $cover['file_name'];

			$c_s_name = StrToLower(MB_SubStr($c_name, 0, 1, 'UTF-8'));
			$c_s_hash = StrToLower(MB_SubStr($c_hash, 0, 2, 'UTF-8'));

			$c_path = "../files/shop/$c_s_name/$c_s_hash/$c_name";
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

					$_adir = $_adir . 'shop/';

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
							$q = T_GetData("UPDATE `shop_items_images` SET `file_name` = '$fname', `size` = $fsize, `hash` = '$fhash', `height` = $height, `width` = $width WHERE `item_id` = $id AND `is_default` = 1", "", 1);
							if ($q)
							{
								unlink($c_path);
							}
							else
								unlink($path);
						}
						else
							T_GetData("INSERT INTO `shop_items_images` (`item_id`, `date`, `user`, `file_name`, `size`, `hash`, `height`, `width`, `is_default`) VALUES ($id, NOW(), $_userID, '$fname', $fsize, '$fhash', $height, $width, 1)", "", 1);
					}
				}
			}
		}
		else
		{
			echo '<center><strong>Файл слишком большой!</strong></center>';
		}

		if (!isset($_POST['done']) || (isset($_POST['done']) && (empty($_POST['title']) || empty($_POST['about']))))
		{
			$ndate    = $q['date'];
			$ntitle   = $q['name'];
			$nabout   = $q['description'];
			$nprice   = $q['price'];
			$cat      = $q['category'];
			$scat     = $q['subcategory'];
			$ncount   = $q['count'];
			$is_pub   = (int) $q['is_public'];

			$checked = "";
			if ($is_pub)
				$checked = " checked";

			$cats      = T_GetData("SELECT `id`, `name` FROM `shop_categories`", "", 1);
			$cats_list = "";
			foreach ($cats as $cat_)
			{
				$selected = "";
				if ($cat_['id'] == $cat)
					$selected = " selected";
				
				$cats_list .= '<option value="' . $cat_['id'] . '"' . $selected . '>' . $cat_['name'] . '</option>';
			}

			if (empty($cats_list))
				$cats_list = '<option value="0" disabled selected>Нет категорий</option>';

			$scats      = T_GetData("SELECT `id`, `name` FROM `shop_subcategories`", "", 1);
			$scats_list = "";
			foreach ($scats as $scat_)
			{
				$selected = "";
				if ($scat_['id'] == $cat)
					$selected = " selected";
				
				$scats_list .= '<option value="' . $scat_['id'] . '"' . $selected . '>' . $scat_['name'] . '</option>';
			}

			if (empty($scats_list))
				$scats_list = '<option value="0" disabled selected>Нет подкатегорий</option>';

			PRINT <<<HERE
<center>
<form enctype="multipart/form-data" action="?items&edit&id=$id" method="POST">
<strong>Редактирование товара</strong>
<br><br>
<strong>Наименование:</strong>
<br>
<input type="text" name="title" placeholder="Введите наименование товара..." style="width: 90%;" value='$ntitle' title="Наименование" maxlength="100">
<br>
<strong>Описание:</strong>
<br>
<textarea name="about" placeholder="Введите описание товара..." style="width: 90%; height: 150px; resize: vertical;" title="Описание">$nabout</textarea>
<br>
<strong>Категория:</strong>
<br>
<select name="cat" style="width: 90%;">
	$cats_list
</select>
<br>
<strong>Подкатегория:</strong>
<br>
<select name="scat" style="width: 90%;">
	$scats_list
</select>
<br>
<strong>Изображение:</strong>
<br>
<img src="$c_path" width="150px">
<input type="file" name="content" accept="image/jpeg,image/png" title="Загрузить изображение вещи" style="width: 90%; font-weight: bold;">
<br>
<strong>Цена:</strong>
<br>
<input type="number" step="0.01" name="price" placeholder="Введите цену товара..." style="width: 90%;" value='$nprice' title="Цена" maxlength="100">
<br>
<strong>Количество:</strong>
<br>
<input type="number" name="count" placeholder="Введите количество товара..." style="width: 90%;" value='$ncount' title="Количество" maxlength="10">
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
			if ($cat > 0 && $scat > 0)
			{
				T_GetData("UPDATE `shop_items` SET `category` = $cat, `subcategory` = $scat, `name` = '$ntitle', `description` = '$nabout', `price` = '$nprice', `count` = $ncount, `is_public` = $is_pub WHERE `id` = $id", "", 1);
				echo '<meta http-equiv="refresh" content="3;?items"><center><strong>Товар изменён.</strong></center>';
			}
			else
				echo '<center><font color="red"><strong>Вначале создайте хоть одну категорию и подкатегорию!</strong></font></center>';
		}
	}
	else if (isset($_GET['add']) && empty($id))	// Добавление
	{
		$ntitle   = $_POST['title'];
		$nabout   = $_POST['about'];
		$nprice   = $_POST['price'];
		$cat      = (int) $_POST['cat'];
		$scat     = (int) $_POST['scat'];
		$ncount   = (int) $_POST['count'];
		$is_pub   = (int) $_POST['is_pub'];

		$checked = "";
		if ($is_pub)
			$checked = " checked";

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

					$_adir = $_adir . 'shop/';

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

		if ((empty($_POST['title']) || empty($_POST['about']) || empty($_POST['price']) || empty($_POST['count'])) && isset($_POST['done']))
			echo '<center><font color="red"><strong>Заполнению подлежат <u>ВСЕ</u> поля, '.$_SESSION[login].'!</strong></font></center>';
		
		if (!isset($_POST['done']) || (isset($_POST['done']) && (empty($_POST['title']) || empty($_POST['about']) || empty($_POST['price']) || empty($_POST['count']))))
		{
			$cats      = T_GetData("SELECT `id`, `name` FROM `shop_categories`", "", 1);
			$cats_list = "";
			foreach ($cats as $cat_)
			{
				$selected = "";
				if ($cat_['id'] == $cat)
					$selected = " selected";
				
				$cats_list .= '<option value="' . $cat_['id'] . '"' . $selected . '>' . $cat_['name'] . '</option>';
			}

			if (empty($cats_list))
				$cats_list = '<option value="0" disabled selected>Нет категорий</option>';

			$scats      = T_GetData("SELECT `id`, `name` FROM `shop_subcategories`", "", 1);
			$scats_list = "";
			foreach ($scats as $scat_)
			{
				$selected = "";
				if ($scat_['id'] == $cat)
					$selected = " selected";
				
				$scats_list .= '<option value="' . $scat_['id'] . '"' . $selected . '>' . $scat_['name'] . '</option>';
			}

			if (empty($scats_list))
				$scats_list = '<option value="0" disabled selected>Нет подкатегорий</option>';

			PRINT <<<HERE
<center>
<form enctype="multipart/form-data" action="?items&add" method="POST">
<strong>Добавление товара</strong>
<br><br>
<strong>Наименование:</strong>
<br>
<input type="text" name="title" placeholder="Введите наименование товара..." style="width: 90%;" value='$ntitle' title="Наименование" maxlength="100">
<br>
<strong>Описание:</strong>
<br>
<textarea name="about" placeholder="Введите описание товара..." title="Описание">$nabout</textarea>
<br>
<strong>Категория:</strong>
<br>
<select name="cat" style="width: 90%;">
	$cats_list
</select>
<br>
<strong>Подкатегория:</strong>
<br>
<select name="scat" style="width: 90%;">
	$scats_list
</select>
<br>
<strong>Изображение:</strong>
<br>
<input type="file" name="content" accept="image/jpeg,image/png" title="Загрузить изображение вещи" style="width: 90%; font-weight: bold;">
<br>
<strong>Цена:</strong>
<br>
<input type="number" step="0.01" name="price" placeholder="Введите цену товара..." style="width: 90%;" value='$nprice' title="Цена" maxlength="100">
<br>
<strong>Количество:</strong>
<br>
<input type="number" name="count" placeholder="Введите количество товара..." style="width: 90%;" value='$ncount' title="Количество" maxlength="10">
<br>
<label><input type="checkbox" name="is_pub" value="1"$checked> Опубликовать</label>
<br><br>
<button type="submit" name="done">Сохранить</button>
</form>
</center>
HERE;
		}
		else
		{
			if ($cat > 0 && $scat > 0)
			{
				$q = T_GetData("INSERT INTO `shop_items` (`date`, `user`, `category`, `subcategory`, `name`, `description`, `price`, `count`, `is_public`) VALUES (NOW(), $_userID, $cat, $scat, '$ntitle', '$nabout', '$nprice', $ncount, $is_pub)", "", 1);
				if ($q)
				{
					$q = (int) T_GetData("SELECT MAX(`id`) AS `max` FROM `shop_items` WHERE `user` = $_userID", "max");

					T_GetData("INSERT INTO `shop_items_images` (`item_id`, `date`, `user`, `file_name`, `size`, `hash`, `height`, `width`, `is_default`) VALUES ($q, NOW(), $_userID, '$fname', $fsize, '$fhash', $height, $width, 1)", "", 1);

					echo '<meta http-equiv="refresh" content="3;?items"><center><strong>Товар добавлен.</strong></center>';
				}
				else
					echo '<meta http-equiv="refresh" content="3;?items"><center><strong>Товар не был добавлен!</strong></center>';
			}
			else
				echo '<center><font color="red"><strong>Вначале создайте хотя бы по одной категориии и подкатегории!</strong></font></center>';
		}
	}
	else
		echo '<center><font color="red"><strong>Выполнен неподдерживаемый запрос. Необходима руковыпрямляющая машина.<br><p align="right"><img src="style/img/hand_notline.png" alt="Юзер детектед"><img src="style/img/hand_line.png" alt="Руковыпрямляющая машина" height="230px"></p></strong></font></center>';