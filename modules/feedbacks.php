<?php
	$content    = "";

	$mark     = 0;
	$feedtext = "";

	if (isset($_POST['mark']) && isset($_POST['text']))
	{
		$item = 0;

		if (CURRENT_ACTION == "item")
			$item = (int) CURRENT_PARAM;

		$_SiteTitle = "Добавление отзыва";

		if (!empty($item))
		{
			$item_i = T_GetData("SELECT `name`, `is_public` FROM `shop_items` WHERE `id` = $id");

			if (!empty($item_i) && ($item_i['is_public'] == 1 || T_Admin()))
			{
				// Валидация семантической ссылки
				$full_param = CURRENT_PARAM;
				$real_param = $item . "-" . T_Semantic($item_i['name']);

				// Если ссылка правильно составлена - выводим данные
				if ($full_param != $real_param)
				{
					$_SiteTitle = "Ошибка!";
					$content    = '<div class="center">Такого товара не существует!</div>';
					require("templates/" . T_Template() . "/layouts/header.php");
					echo "<div class=\"content\">$content</div>";
					return;
				}
			}
			else
			{
				$_SiteTitle = "Ошибка!";
				$content    = '<div class="center">Такого товара не существует!</div>';
				require("templates/" . T_Template() . "/layouts/header.php");
				echo "<div class=\"content\">$content</div>";
				return;
			}
		}

		$mark     = (int) $_POST['mark'];
		$feedtext = T_SafeText(Trim($_POST['text']));

		if ($mark > 0 && $mark < 6 && !empty($feedtext))
		{
			$q = false;

			if (empty($item))
				$q = T_GetData("INSERT INTO `feedbacks` (`date`, `user`, `text`, `mark`) VALUES (NOW(), $_userID, '$feedtext', $mark)", "", 1);
			else
				$q = T_GetData("INSERT INTO `feedbacks` (`date`, `user`, `item`, `text`, `mark`) VALUES (NOW(), $_userID, $item, '$feedtext', $mark)", "", 1);

			if ($q)
			{
				$content = '<div class="center">Отзыв успешно добавлен! Он появится на сайте после проверки администратором.</div>';
				require("templates/" . T_Template() . "/layouts/header.php");
				echo "<div class=\"content\">$content</div>";
			}
			else
			{
				$content = '<div class="center">Ошибка добавления отзыва в БД!</div>';
				require("templates/" . T_Template() . "/layouts/header.php");
				echo "<div class=\"content\">$content</div>";
			}
			return;
		}
		else
		{
			$content = '<div class="center">Некорректный отзыв!</div>';
			require("templates/" . T_Template() . "/layouts/header.php");
			echo "<div class=\"content\">$content</div>";
			return;
		}
	}

	$add_form = "
<form name=\"texthere\" method=\"POST\" name=\"feed\">
<div class=\"feedbacks-form\">
<div class=\"feedbacks-mark\">
Оценка<br>
<div class=\"feedbacks-mark-div\"><div class=\"mark\"><div class=\"mark_action\">
</div>
</div>
</div>
	<input name=\"mark\" type=\"hidden\" value=\"$mark\">
</div>
<div class=\"feedbacks-text-div\">
Отзыв<br>
<textarea class=\"feedbacks-text\" name=\"text\" maxlength=\"1000\" placeholder=\"Введите текст своего отзыва...\">$text</textarea>
	<div class=\"smiles-button\"><img src=\"/templates/" . T_Template() . "/assets/img/smiles/0.png\" alt=\":)\">
		<div class=\"smiles\">
			<img class=\"smile\" src=\"/templates/" . T_Template() . "/assets/img/smiles/0.png\" alt=\":)\" onclick=\"AddSmile(':)')\">
			<img class=\"smile\" src=\"/templates/" . T_Template() . "/assets/img/smiles/1.png\" alt=\":]\" onclick=\"AddSmile(':]')\">
			<img class=\"smile\" src=\"/templates/" . T_Template() . "/assets/img/smiles/2.png\" alt=\";)\" onclick=\"AddSmile(';)')\">
			<img class=\"smile\" src=\"/templates/" . T_Template() . "/assets/img/smiles/3.png\" alt=\":D\" onclick=\"AddSmile(':D')\">
			<img class=\"smile\" src=\"/templates/" . T_Template() . "/assets/img/smiles/5.png\" alt=\"O:}\" onclick=\"AddSmile('O:}')\">
			<img class=\"smile\" src=\"/templates/" . T_Template() . "/assets/img/smiles/4.png\" alt=\":p\" onclick=\"AddSmile(':p')\">
			<img class=\"smile\" src=\"/templates/" . T_Template() . "/assets/img/smiles/9.png\" alt=\":-/\" onclick=\"AddSmile(':-/')\">
			<img class=\"smile\" src=\"/templates/" . T_Template() . "/assets/img/smiles/10.png\" alt=\":\\\" onclick=\"AddSmile(':\\\')\">
			<img class=\"smile\" src=\"/templates/" . T_Template() . "/assets/img/smiles/11.png\" alt=\"o_O\" onclick=\"AddSmile('o_O')\">
			<img class=\"smile\" src=\"/templates/" . T_Template() . "/assets/img/smiles/7.png\" alt=\":o\" onclick=\"AddSmile(':o')\">
			<img class=\"smile\" src=\"/templates/" . T_Template() . "/assets/img/smiles/8.png\" alt=\":|\" onclick=\"AddSmile(':|')\">
			<img class=\"smile\" src=\"/templates/" . T_Template() . "/assets/img/smiles/6.png\" alt=\":(\" onclick=\"AddSmile(':(')\">
			<img class=\"smile\" src=\"/templates/" . T_Template() . "/assets/img/smiles/12.png\" alt=\":[\" onclick=\"AddSmile(':[')\">
			<img class=\"smile\" src=\"/templates/" . T_Template() . "/assets/img/smiles/13.png\" alt=\"X(\" onclick=\"AddSmile('X(')\">
			<img class=\"smile\" src=\"/templates/" . T_Template() . "/assets/img/smiles/14.png\" alt=\"]:->\" onclick=\"AddSmile(']:->')\">
		</div>
	</div>
</div>
<strong>Вы не сможете отредактировать или удалить свой отзыв. Пожалуйста, перед отправкой проверьте его ещё раз.</strong>
<br><br>
<button type=\"submit\">ОТПРАВИТЬ</button>
</div>
</form>
";

	switch (CURRENT_ACTION)
	{
		case "item":
			{
				// Отзывы о товаре

				$_SiteTitle = "Отзывы о товаре";
				$id			= (int) CURRENT_PARAM;
				$page		= (int) CURRENT_EXTRA1;

				if ($page == 0)
					$page = 1;

				$pageDB = ($page - 1) * 10;

				if (CURRENT_EXTRA == "page")
					if ($page > 1)
						$_SiteTitle .= ", страница " . $page;

				$item = T_GetData("SELECT `name`, `is_public` FROM `shop_items` WHERE `id` = $id");

				if (!empty($id))
				{
					if (!empty($item) && ($item['is_public'] == 1 || T_Admin()))
					{
						// Валидация семантической ссылки
						$full_param = CURRENT_PARAM;
						$real_param = $id . "-" . T_Semantic($item['name']);

						// Если ссылка правильно составлена - выводим данные
						if ($full_param != $real_param)
						{
							$_SiteTitle = "Ошибка!";
							$content    = '<div class="center">Такого товара не существует!</div>';
							break;
						}
					}
					else
					{
						$_SiteTitle = "Ошибка!";
						$content    = '<div class="center">Такого товара не существует!</div>';
						break;
					}
				}

				$item_name  = $item['name'];

				$item_image = T_GetData("SELECT `file_name`, `hash` FROM `shop_items_images` WHERE `item_id` = $id AND `is_default` = 1 LIMIT 1");

				if (!empty($item_image))
				{
					$item_dname  = StrToLower(MB_SubStr($item_image['file_name'], 0, 1, 'UTF-8'));
					$item_dhname = StrToLower(MB_SubStr($item_image['hash'], 0, 2, 'UTF-8'));
					$item_image  = "/files/shop/" . $item_dname . "/" . $item_dhname . "/" . $item_image['file_name'];
				}
				else
					$item_image = "/templates/" . T_Template() . "/assets/img/no_image.png";

				$content .= '<div class="feedback-item"><div class="feedback-item-name"><a href="/shop/item/' . $id . '-' . T_Semantic($item_name) . '">' . $item_name . '</a></div><img class="feedback-item-image" src="' . $item_image . '" alt="' . $item_name . '"></div><br><hr><br>';

				// Если авторизован
				if (T_Authorized())
				{
					// Проверка на существование отзыва от этого пользователя
					$q = T_GetData("SELECT `id` FROM `feedbacks` WHERE `user` = $_userID AND `item` = $id", "id");

					if (empty($q))
					{
						// Запрос статуса заказа, если такой был
						$q = T_GetData("SELECT `status` FROM `shop_orders` WHERE `id` = (SELECT `order_id` FROM `shop_orders_parts` WHERE `user` = $_userID AND `item_id` = $id)", "status");

						if ($q == 5)
							$content .= $add_form;
						else
							$content .= '<div class="center">Оставлять отзывы можно только о купленных Вами товарах!</div>';
					}
					else
						$content .= '<div class="center">Вы уже оставили свой отзыв!<br><br>Если он не отображается на сайте - значит, проходит модерацию.</div>';
				}
				else
					$content .= '<div class="center">Для  того, чтобы оставлять отзывы необходимо <a href="/auth">войти</a> или <a href="/reg">зарегистрироваться</a>.</div>';

				$content .= "<br><hr>";

				// Работа с выводом отзывов

				$all_cnt = T_GetData("SELECT COUNT(`id`) AS `cnt` FROM `feedbacks` WHERE `item` = $id AND `status` = 1", "cnt");

				if ($all_cnt > 0)
				{
					$q   = T_GetData("SELECT * FROM `feedbacks` WHERE `item` = $id AND `status` = 1 ORDER BY `id` DESC LIMIT $pageDB, 10", "", 1);
					$cnt = mysqli_num_rows($q);

					if ($cnt > 0)
					{
						$content .= '<div class="center">';

						foreach ($q as $feed)
						{
							// Данные отзыва

							$date = T_DateFormat($feed['date'], $site_df);
							$user = (int) $feed['user'];
							$mark = (int) $feed['mark'];
							$text = T_Smiles($feed['text']);

							$text = Str_Replace("\r\n", "<br>", $text);

							// Данные автора отзыва

							$user_i     = T_GetData("SELECT `login`, `status` FROM `users` WHERE `id` = $user");
							$user_sex   = (int) T_GetData("SELECT `sex` FROM `users_profiles` WHERE `user` = $user", "sex");
							$user_login = $user_i['login'];

							// Аватарка автора

							$user_ava   = "/templates/" . T_Template() . "/assets/img/avatars/";
							if ($user_i['status'] == 0)
								$user_ava .= "deactivated";
							else
							{
								switch($user_sex)
								{
									case 0: $user_ava .= "default";
										break;
									case 1: $user_ava .= "default_m";
										break;
									case 2: $user_ava .= "default_f";
										break;
								}
							}
							$user_ava .= ".png";

							// Звёздочки
							$stars = '<div class="feedback-stars">' . T_MarkOut($mark) .  '</div>';

							// Вывод

							$content .= '<div class="feedback"><div class="feedback-user"><img class="feedback-avatar" src="' . $user_ava . '"><br><strong><a href="/pa/profile/' . $user . '">' . $user_login . '</a></strong></div><div class="feedback-date">' . $date . '</div>' . $stars . '<div class="feedback-text">' . $text . '</div></div>';
						}

						$content .= '</div>';

						// Пагинация

						$page_cnt = ceil($all_cnt / 10);

						if ($page < $page_cnt)
							$content .= "<div class=\"news-page\" onclick=\"location.href='/feedbacks/item/" . $id . "/page/" . ($page + 1) . "'\">Далее</div>";

						if ($page > $page_cnt || $page > 1)
							$content .= "<div class=\"news-page\" onclick=\"location.href='/feedbacks/item/" . $id . "/page/" . ($page - 1) . "'\">Назад</div>";
					}
					else
						$content .= '<div class="center">Нет отзывов.</div>';
				}
				else
					$content .= '<div class="center">Нет отзывов.</div>';
			}
			break;
		default:
			{
				// Все отзывы

				$_SiteTitle = "Отзывы";
				$page       = (int) CURRENT_PARAM;

				if ($page == 0)
					$page = 1;

				$pageDB = ($page - 1) * 10;

				if (CURRENT_ACTION == "page")
					if ($page > 1)
						$_SiteTitle .= ", страница " . $page;

				if (T_Authorized())
				{
					$q = T_GetData("SELECT `id` FROM `feedbacks` WHERE `user` = $_userID AND `item` IS NULL", "id");

					if (empty($q))
						$content .= $add_form;
					else
						$content .= '<br><div class="center">Вы уже оставили свой отзыв!<br><br>Если он не отображается на сайте - значит, проходит модерацию.</div>';
				}
				else
					$content .= '<div class="center">Для  того, чтобы оставлять отзывы необходимо <a href="/auth">войти</a> или <a href="/reg">зарегистрироваться</a>.</div>';

				$content .= "<br><hr>";

				$all_cnt = T_GetData("SELECT COUNT(`id`) AS `cnt` FROM `feedbacks` WHERE `status` = 1", "cnt");

				if ($all_cnt > 0)
				{
					$q   = T_GetData("SELECT * FROM `feedbacks` WHERE `status` = 1 ORDER BY `id` DESC LIMIT $pageDB, 10", "", 1);
					$cnt = mysqli_num_rows($q);

					if ($cnt > 0)
					{
						$content .= '<div class="center">';

						foreach ($q as $feed)
						{
							// Данные отзыва

							$date = T_DateFormat($feed['date'], $site_df);
							$user = (int) $feed['user'];
							$item = (int) $feed['item'];
							$mark = (int) $feed['mark'];
							$text = T_Smiles($feed['text']);

							$text = Str_Replace("\r\n", "<br>", $text);

							// Данные автора отзыва

							$user_i     = T_GetData("SELECT `login`, `status` FROM `users` WHERE `id` = $user");
							$user_sex   = (int) T_GetData("SELECT `sex` FROM `users_profiles` WHERE `user` = $user", "sex");
							$user_login = $user_i['login'];

							// Аватарка автора

							$user_ava   = "/templates/" . T_Template() . "/assets/img/avatars/";
							if ($user_i['status'] == 0)
								$user_ava .= "deactivated";
							else
							{
								switch($user_sex)
								{
									case 0: $user_ava .= "default";
										break;
									case 1: $user_ava .= "default_m";
										break;
									case 2: $user_ava .= "default_f";
										break;
								}
							}
							$user_ava .= ".png";

							// Звёздочки
							$stars = '<div class="feedback-stars">' . T_MarkOut($mark) .  '</div>';

							// Товар

							$item_link = '';
							if (empty($item))
								$item_link = 'компании';
							else
							{
								$item_name = T_GetData("SELECT `name` FROM `shop_items` WHERE `id` = $item", "name");

								$item_link = '<a href="/shop/item/' . $item . '-' . T_Semantic($item_name) . '" target="_blank">товаре</a>';
							}

							// Вывод

							$content .= '<div class="feedback"><div class="feedback-user"><img class="feedback-avatar" src="' . $user_ava . '"><br><strong><a href="/pa/profile/' . $user . '">' . $user_login . '</a></strong></div><div class="feedback-date">' . $date . '</div>' . $stars . '<div class="feedback-about">Отзыв о ' . $item_link . '</div><div class="feedback-text">' . $text . '</div></div>';
						}

						$content .= '</div>';

						// Пагинация

						$page_cnt = ceil($all_cnt / 10);

						if ($page < $page_cnt)
							$content .= "<div class=\"news-page\" onclick=\"location.href='/feedbacks/page/" . ($page + 1) . "'\">Далее</div>";

						if ($page > $page_cnt || $page > 1)
							$content .= "<div class=\"news-page\" onclick=\"location.href='/feedbacks/page/" . ($page - 1) . "'\">Назад</div>";
					}
					else
						$content .= '<div class="center">Нет отзывов.</div>';
				}
				else
					$content .= '<div class="center">Нет отзывов.</div>';
			}
			break;
	}

	require("templates/" . T_Template() . "/layouts/header.php");
	echo "<div class=\"content\">$content</div>";