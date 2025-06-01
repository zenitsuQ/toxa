<?php
	$content = "";

	switch (CURRENT_ACTION)
	{
		case "show":
			{
				// ========== Секция заголовка ==========

				$id = (int) CURRENT_PARAM;

				if ($id <= 0)
				{
					$_SiteTitle = "Ошибка!";
					$content    = "<div class=\"news\">Такой новости не существует!</div><div class=\"news-page\" onclick=\"location.href='/news'\">К новостям</div><div class=\"news-page\" onclick=\"javascript:history.back();\">Назад</div>";
					break;
				}
				else
				{
					$news = T_GetData("SELECT `title`, `is_public` FROM `news` WHERE `id` = $id");

					if (!empty($news) && ($news['is_public'] == 1 || T_Admin()))
					{
						// Валидация семантической ссылки
						$full_param = CURRENT_PARAM;
						$real_param = $id . "-" . T_Semantic($news['title']);

						// Если ссылка правильно составлена - выводим данные
						if ($full_param == $real_param)
							$_SiteTitle = $news['title'];
						else
						{
							$_SiteTitle = "Ошибка!";
							$content    = "<div class=\"news\">Такой новости не существует!</div><div class=\"news-page\" onclick=\"location.href='/news'\">К новостям</div><div class=\"news-page\" onclick=\"javascript:history.back();\">Назад</div>";
							break;
						}
					}
					else
					{
						$_SiteTitle = "Ошибка!";
						$content    = "<div class=\"news\">Такой новости не существует!</div><div class=\"news-page\" onclick=\"location.href='/news'\">К новостям</div><div class=\"news-page\" onclick=\"javascript:history.back();\">Назад</div>";
						break;
					}
				}

				// ========== Секция вывода ==========

				$news = T_GetData("SELECT * FROM `news` WHERE `id` = $id");

				$date   = T_DateFormat($news['date'], $site_df);
				$u_date = $news['u_date'];
				$if_upd = "";

				// Если статья редактировалась - выводим дату правки
				if (!empty($u_date))
				{
					$u_date = T_DateFormat($news['u_date'], $site_df);
					$if_upd = " <strong>Изменено:</strong> " . $u_date . " |";
				}

				$text   = $news['text'];
				$author = (int) $news['user'];
				$views  = ((int) $news['views']) + 1;
				$is_pub = $news['is_public'];

				if ($is_pub == 1)
					$is_pub = "опубликована";
				else
					$is_pub = "не опубликована";

				$if_admin = "";

				// Если админ - выводить больше информации
				if (T_Admin())
				{
					$login    = T_GetData("SELECT `login` FROM `users` WHERE `id` = $author", "login");
					$if_admin = "<br><br><strong>Дата:</strong> $date |$if_upd <strong>Просмотров:</strong> $views | <strong>Автор:</strong> " . $login . " | <strong>Статус:</strong> " . $is_pub . "<br><br>";
				}

				// Работа с обложкой статьи
				$cover  = T_GetData("SELECT `id` FROM `news_images` WHERE `news_id` = $id AND `is_cover` = 1 LIMIT 1", "id");
				$c_path = "";

				if (!empty($cover))
					$text = "[photo]" . $cover . "[/photo]" . $text;
				else
				{
					$c_path = "/templates/" . T_Template() . "/assets/img/no_image.png";

					$text = "<div class=\"news-picture\"><img src=\"$c_path\" alt=\"$n_title\"></div>" . $text;
				}

				$text     = T_PicWorks($text, 2, 1024);	// Подстановка картинок
				$text     = T_BBParse($text);
				$text     = T_Smiles($text);

				// Вывод содержимого статьи

				$content = "$text $if_admin";

				$content = "<pre wrap>" . $content . "</pre>";									// Делаем текст предварительно отформатированным

				$content .= "<div class=\"news-page\" onclick=\"location.href='/news'\">К новостям</div><div class=\"news-page\" onclick=\"javascript:history.back();\">Назад</div>";

				T_GetData("UPDATE `news` SET `views` = `views` + 1 WHERE `id` = $id", "", 1);	// Обновляем просмотры
			}
			break;
		default:
			{
				// ========== Секция заголовка ==========

				$_SiteTitle = "Новости";

				$page = (int) CURRENT_PARAM;

				if ($page == 0)
					$page = 1;

				$pagedb = ($page - 1) * 10;

				if (CURRENT_ACTION == "page")
					if ($page > 1)
						$_SiteTitle .= ", страница " . $page;

				// ========== Секция вывода ==========

				$news_cnt = (int) T_GetData("SELECT COUNT(`id`) AS `cnt` FROM `news` WHERE `is_public` = 1", "cnt");
				$page_cnt = ceil($news_cnt / 10);

				if ($news_cnt != 0 && $page <= $page_cnt)
				{
					$news      = T_GetData("SELECT * FROM `news` WHERE `is_public` = 1 ORDER BY `id` DESC LIMIT $pagedb, 10", "", 1);

					$content .= '<div class="card-list">';

					// Выводим все новости на страницу
					foreach ($news as $new)
					{
						$n_id    = (int) $new['id'];
						$n_title = T_CutText($new['title'], 55);
						$n_text  = T_CutText(T_AntiBBParse(T_ClearSmiles($new['text'])), 150);
						$n_date  = T_DateFormat($new['date'], $site_df);
						$n_views = (int) $new['views'];
						$n_semantic = T_Semantic($new['title']);

						// Работа с обложкой
						$cover  = T_GetData("SELECT `file_name`, `hash`, `height`, `width` FROM `news_images` WHERE `news_id` = $n_id AND `is_cover` = 1 LIMIT 1");
						$news_cover = "";

						if (!empty($cover))
						{
							$c_hash = $cover['hash'];
							$c_name = $cover['file_name'];

							$c_s_name = StrToLower(MB_SubStr($c_name, 0, 1, 'UTF-8'));
							$c_s_hash = StrToLower(MB_SubStr($c_hash, 0, 2, 'UTF-8'));

							$c_path = "/files/news/$c_s_name/$c_s_hash/$c_name";

							$h   = (int) $cover['height'];
							$w   = (int) $cover['width'];
							$max = 256;

							if (($w > $h || $w == $h) && $w > $max)
								$news_cover = '<img src="' . $c_path . '" alt="' . $n_title . '" class="card-cover">'; // width="' . $max . 'px"

							if ($h > $w && $h > $max)
								$news_cover = '<img src="' . $c_path . '" alt="' . $n_title . '" class="card-cover">'; // height="' . $max . 'px"

							if ($h < $max && $w < $max)
								$news_cover = '<img src="' . $c_path . '" alt="' . $n_title . '" class="card-cover">';
						}
						else
						{
							$c_path = "/templates/" . T_Template() . "/assets/img/no_image.png";

							$news_cover = '<img src="' . $c_path . '" alt="' . $n_title . '" class="card-cover">';
						}

						// Вывод содержимого
						$content .= "<div class=\"card\" onclick=\"location.href='/news/show/$n_id-$n_semantic'\">$news_cover<div class=\"news-content\"><p class=\"card-title\">$n_title</p><p class=\"card-description\">$n_text</p><div class=\"news-for-button\"></div></div></div>";
					}
					$content .= '</div>';
				}
				else
					$content = "<div class=\"news\">Нет новостей.</div>";

				// Пагинация
				if ($page < $page_cnt)
					$content .= "<div class=\"news-page\" onclick=\"location.href='/news/page/" . ($page + 1) . "'\">Далее</div>";

				if ($page > $page_cnt && $page > 1 && $page != 1)
					$content .= "<div class=\"news-page\" onclick=\"location.href='/news/page/" . ($page - 1) . "'\">Назад</div>";
			}
			break;
	}

	require("templates/" . T_Template() . "/layouts/header.php");
	//echo "<div class=\"content\">$content</div>";
?>
	<section class="wrapper">
        <div class="white-container">
            <h1><img src="/templates/<?=T_Template()?>/assets/icons/icon_for_title.svg" class="icon-for-title" alt="MTS_"><?=$_SiteTitle?></h1>
			<?=$content?>
		</div>
	</section>