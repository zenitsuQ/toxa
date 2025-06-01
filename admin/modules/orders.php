<?php
	$pagecount = T_GetData("SELECT COUNT(`id`) AS `cnt` FROM `shop_orders` WHERE `status` <> 0 AND `status` <> 1", "cnt");
	$pagecount = ceil($pagecount / 10);

	// Обнуление

	if (isset($_GET['zero']))
	{
		$q = T_GetData("SELECT `id` FROM `shop_orders`");
		if (empty($q))
		{
			mysqli_query($db, "ALTER TABLE `shop_orders` AUTO_INCREMENT = 1");
			echo '<meta http-equiv="refresh" content="3;?cats"><center><strong>AUTO_INCREMENT таблицы "shop_orders" теперь равен 1.</strong></center>';
		}
		else
			echo '<center><font color="red"><strong>Таблица заказов не пуста!</strong></font></center>';

		require_once("template/footer.php");
		return;
	}

	// Список

	if (!isset($_GET['edit']) && empty($id))
	{
		$q = T_GetData("SELECT `id` FROM `shop_orders`");
		if (empty($q))
		{
			echo '<center><strong>Нет заказов.</strong><br><br><a href="?orders&zero" onclick="return confirm(\'Вы уверены?\')">Обнулить инкремент</a></center>';
		}
		else
		{
			echo '<br><table width="95%" align="center" valign="top"><tr><td width="5%" align="center"><strong>ID</strong></td><td width="15%" align="center"><strong>Дата создания</strong></td><td width="40%" align="center"><strong>Телефон</strong></td><td width="20%" align="center"><strong>Способ</strong></td><td width="20%" align="center"><strong>Статус</strong></td></tr>';
	
			$q = T_GetData("SELECT `id`, `date`, `phone`, `delivery_type`, `status` FROM `shop_orders` ORDER BY `id` DESC LIMIT $pagedb, 10", "", 1); // `status` <> 0 AND `status` <> 1
			foreach ($q as $qq)
			{
				$nid      = $qq['id'];
				$ndate    = T_DateFormat($qq['date'], $site_df);
				$phone    = $qq['phone'];
				$type     = $qq['delivery_type'];
				$status   = $qq['status'];

				// Тип доставки
				$type_text = "";
				switch ($type)
				{
					case 0: $type_text = "Самовывоз"; break;
					case 1: $type_text = "Курьером"; break;
					case 2: $type_text = "Почтой"; break;
					default:
						$type_text = "Неизвестно";
						break;
				}

				// Статус заказа
				$status_text = "";
				switch ($status)
				{
					case 0: $status_text = "Отменён"; break;
					case 1: $status_text = "Ожидает подтверждения"; break;
					case 2: $status_text = "Подтверждён"; break;
					case 3: $status_text = "Обработан"; break;
					case 4:
						{
							if ($type == 0)
								$status_text = "Ожидает покупателя";
							else
								$status_text = "Отправлен";
						}
						break;
					case 5:
						{
							if ($type == 0)
								$status_text = "Выдан";
							else
								$status_text = "Доставлен";
						}
						break;
					default:
						$status_text = "Неизвестно";
						break;
				}

				PRINT <<<HERE
<tr><td><a href="?orders&id=$nid" title="Просмотр заказа №$nid">$nid</a></td><td>$ndate</td><td>$phone</td><td align="center">$type_text</td><td align="center">$status_text</td></tr>
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
						echo '| <a href="?orders&page='.$i.'">'.$i.'</a> ';
					}
				}
				echo "|</center><br>";
			}
		}
	}
	else if (!isset($_GET['edit']) && !empty($id))	// Просмотр
	{
		$qq = T_GetData("SELECT * FROM `shop_orders` WHERE `id` = $id");

		$nid      = $qq['id'];
		$ndate    = T_DateFormat($qq['date'], $site_df);
		$udate    = $qq['u_date'];
		$nuser    = $qq['user'];
		$naddress = $qq['address'];
		$phone    = $qq['phone'];
		$ntrack   = $qq['post_track'];

		$type     = $qq['delivery_type'];
		$status   = $qq['status'];

		$edit_date = "";
		if (!empty($udate))
		{
			$udate     = T_DateFormat($udate, $site_df);
			$edit_date = "<strong>Изменён:</strong> $udate<br>";
		}

		$u_login  = T_GetData("SELECT `login` FROM `users` WHERE `id` = $nuser", "login");

		// Тип доставки
		$type_text = "";
		switch ($type)
		{
			case 0: $type_text = "самовывоз"; break;
			case 1: $type_text = "курьером"; break;
			case 2: $type_text = "почтой"; break;
			default:
				$type_text = "Неизвестно";
				break;
		}

		// Статус заказа
		$status_text = "";
		switch ($status)
		{
			case 0: $status_text = "отменён"; break;
			case 1: $status_text = "ожидает подтверждения"; break;
			case 2: $status_text = "подтверждён"; break;
			case 3: $status_text = "обработан"; break;
			case 4:
				{
					if ($type == 0)
						$status_text = "ожидает покупателя";
					else
						$status_text = "отправлен";
				}
				break;
			case 5:
				{
					if ($type == 0)
						$status_text = "выдан";
					else
						$status_text = "доставлен";
				}
				break;
			default:
				$status_text = "неизвестно";
				break;
		}

		$full_price = 0;

		$q = T_GetData("SELECT `item_id`, `count` FROM `shop_orders_parts` WHERE `order_id` = $nid", "", 1);
		foreach ($q as $qfa)
		{
			$oi_item    = $qfa['item_id'];
			$oi_item_c  = $qfa['count'];

			$qi         = T_GetData("SELECT `category`, `subcategory`, `name`, `price`, `count` FROM `shop_items` WHERE `id` = $oi_item");
			$oi_cat     = $qi['cat'];
			$oi_scat    = $qi['scat'];
			$oi_name    = $qi['name'];
			$oi_price   = $qi['price'];
			$oi_count   = $qi['count'];

			$cover    = T_GetData("SELECT `file_name`, `hash`, `height`, `width` FROM `shop_items_images` WHERE `item_id` = $oi_item AND `is_default` = 1 LIMIT 1");
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

			$semantic   = $oi_item . '-' . T_Semantic($oi_name);

			$full_price += $oi_price * $oi_item_c;
	
			$order_item_list .= '<tr><td align="center"><img src="' . $c_path . '" width="50px" height="50px"></td><td><a href="/shop/item/' . $semantic . '" target="_blank">'.$oi_name.'</a></td><td align="center">'.$oi_item_c.' шт.</td><td align="center">'.$oi_price.' ₽</td><td align="center">'.$oi_count.' шт.</td></tr>';
		}

		if (empty($naddress))
			$naddress = "не указан";

		if (empty($ntrack))
			$ntrack   = "не задан";

		PRINT <<<HERE
<center><strong>Заказ №$nid</strong></center><br>
<strong>Дата:</strong> $ndate<br>
$edit_date
<strong>Адрес:</strong> $naddress<br>
<strong>Статус:</strong> $status_text<br>
<strong>Телефон:</strong> $phone<br>
<strong>Заказчик:</strong> $nuser ($u_login)<br>
<strong>Трек-номер:</strong> $ntrack<br>
<strong>Способ доставки:</strong> $type_text<br>
<table align="center" width="95%">
<tr><td width="10%" align="center"><strong>Вид</strong></td><td width="55%" align="center"><strong>Наименование</strong></td><td width="15%" align="center"><strong>Кол-во</strong></td><td width="15%" align="center"><strong>Цена</strong></td><td width="10%" align="center"><strong>Наличие</strong></td></tr>
$order_item_list
<tr><td><strong>ИТОГО:</strong></td><td colspan="4" align="right"><strong>$full_price ₽</strong></td></tr>
</table>
<center><a href="?orders&edit&id=$nid">Изменить заказ</a></center>
HERE;
	}
	else if (isset($_GET['edit']) && !empty($id)) //Редактирование
	{
		$q = T_GetData("SELECT `post_track`, `delivery_type`, `status` FROM `shop_orders` WHERE `id` = $id");

		$type    = $q['delivery_type'];
		$nstatus = $q['status'];
		$ntrack  = $q['post_track'];

		if (!isset($_POST['done']))
		{
			$statuses[0] = "Отменён";
			$statuses[1] = "Ожидает подтверждения";
			$statuses[2] = "Подтверждён";
			$statuses[3] = "Обработан";

			if ($type == 0)
				$statuses[4] = "Ожидает покупателя";
			else
				$statuses[4] = "Отправлен";

			if ($type == 0)
				$statuses[5] = "Выдан";
			else
				$statuses[5] = "Доставлен";

			$statuses_list = "";
			for ($i = 0; $i < count($statuses); $i++)
			{
				$selected = "";
				if ($i == $nstatus)
					$selected = " selected";
				
				$statuses_list .= '<option value="' . $i . '"' . $selected . '>' . $statuses[$i] . '</option>';
			}

			PRINT <<<HERE
<center>
<form action="?orders&edit&id=$id" method="POST">
<strong>Редактирование заказа №$id</strong>
<br><br>
<strong>Трек-номер:</strong><br>
<input type="text" name="track" placeholder="Введите трек-номер (необязательно)" style="width: 90%;" value="$ntrack" title="Трек-номер" maxlength="20">
<br>
<strong>Статус:</strong><br>
<select name="status" style="width: 90%;">
	$statuses_list
</select>
<br><br>
<button type="submit" name="done" style="width: 90%; height: 50px; font-weight: bold;">СОХРАНИТЬ</button>
</form>
</center>
HERE;
		}
		else
		{
			$track  = $_POST['track'];
			$status = (int) $_POST['status'];

			// При отправке товара уменьшаем его количество в БД
			if ($nstatus == 3 && $status == 4)
			{
				$q = T_GetData("SELECT `item_id`, `count` FROM `shop_orders_parts` WHERE `order_id` = $id", "", 1);
				foreach ($q as $item)
				{
					$item_id  = $item['item_id'];
					$item_cnt = $item['count'];

					T_GetData("UPDATE `shop_items` SET `count` = `count` - $item_cnt WHERE `id` = $item_id");
				}
			}

			// При отмене заказа возвращаем зарезервированные товары
			if ($nstatus == 4 && $status == 0)
			{
				$q = T_GetData("SELECT `item_id`, `count` FROM `shop_orders_parts` WHERE `order_id` = $id", "", 1);
				foreach ($q as $item)
				{
					$item_id  = $item['item_id'];
					$item_cnt = $item['count'];

					T_GetData("UPDATE `shop_items` SET `count` = `count` + $item_cnt WHERE `id` = $item_id");
				}
			}

			$q = T_GetData("UPDATE `shop_orders` SET `post_track` = '$track', `status` = $status WHERE `id` = $id", "", 1);

			if ($q)
				echo '<meta http-equiv="refresh" content="3;?orders"><center><strong>Заказ изменён.</strong></center>';
			else
				echo '<meta http-equiv="refresh" content="3;?orders"><center><strong>Ошибка изменения заказа!</strong></center>';
		}
	}
	else
		echo '<center><font color="red"><strong>Выполнен неподдерживаемый запрос. Необходима руковыпрямляющая машина.<br><p align="right"><img src="style/img/hand_notline.png" alt="Юзер детектед"><img src="style/img/hand_line.png" alt="Руковыпрямляющая машина" height="230px"></p></strong></font></center>';