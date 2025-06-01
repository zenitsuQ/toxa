<?php
	$content    = "";
	$results    = "";

	$_SiteTitle = "Поиск";

	$page		= (int) CURRENT_PARAM;

	if ($page == 0)
		$page = 1;

	$pageDB = ($page - 1) * 10;

	if (CURRENT_ACTION == "page")
		if ($page > 1)
			$_SiteTitle .= ", страница " . $page;

	$search    = "";
	$where     = -1;
	$only_name = -1;

	// Логика поиска

	if ((isset($_POST['search_field']) && isset($_POST['where'])) || $page > 1)
	{
		if (!empty($_SESSION['search-text']) && !empty($_SESSION['search-where']) && $page > 1)
		{
			$search    = $_SESSION['search-text'];
			$where     = (int) $_SESSION['search-where'];
			$only_name = (int) $_SESSION['search-on'];
		}
		else
		{
			$search    = T_SafeText(Trim($_POST['search_field']));
			$where     = (int) $_POST['where'];
			$only_name = (int) $_POST['only_name'];
		}

		if (MB_StrLen($search, "UTF-8") < 3 || MB_StrLen($search, "UTF-8") > 50)
			$results = '<div class="center">Минимальная длина поискового запроса - 3 символа, максимальная - 50 символов.</div>';
		else
		{
			if ($where < 0 || $where > 2)
				$results = '<div class="center">Некорректный раздел для поиска!</div>';
			else
			{
				if ($only_name < 0 || $only_name > 1)
					$results = '<div class="center">Некорректный параметр поиска!</div>';
				else
				{
					// Поиск

					$_SESSION['search-text']  = $search;
                    $_SESSION['search-where'] = $where;
					$_SESSION['search-on']    = $only_name;

					$query    = "";
					$result_q = "";

					switch ($where)
					{
						// Страницы
						case 0:
							{
								if (empty($only_name))
								{
									$query = "SELECT `id`, `title`
											FROM `pages`
											WHERE (`title` LIKE '%$search%' OR `content` LIKE '%$search%') AND `is_public` = 1
											ORDER BY `id` DESC
											LIMIT $pageDB, 10";

									$result_q = "SELECT COUNT(`id`) AS `cnt`
												FROM `pages`
												WHERE (`title` LIKE '%$search%' OR `content` LIKE '%$search%') AND `is_public` = 1";
								}
								else
								{
									$query = "SELECT `id`, `title`
											FROM `pages`
											WHERE `title` LIKE '%$search%' AND `is_public` = 1
											ORDER BY `id` DESC
											LIMIT $pageDB, 10";

									$result_q = "SELECT COUNT(`id`) AS `cnt`
												FROM `pages`
												WHERE `title` LIKE '%$search%' AND `is_public` = 1";
								}
							}
							break;
						// Новости
						case 1:
							{
								if (empty($only_name))
								{
									$query = "SELECT `id`, `title`
											FROM `news`
											WHERE (`title` LIKE '%$search%' OR `text` LIKE '%$search%') AND `is_public` = 1
											ORDER BY `id` DESC
											LIMIT $pageDB, 10";

									$result_q = "SELECT COUNT(`id`) AS `cnt`
												FROM `news`
												WHERE (`title` LIKE '%$search%' OR `text` LIKE '%$search%') AND `is_public` = 1";
								}
								else
								{
									$query = "SELECT `id`, `title`
											FROM `news`
											WHERE `title` LIKE '%$search%' AND `is_public` = 1
											ORDER BY `id` DESC
											LIMIT $pageDB, 10";

									$result_q = "SELECT COUNT(`id`) AS `cnt`
												FROM `news`
												WHERE `title` LIKE '%$search%' AND `is_public` = 1";
								}
							}
							break;
						// Товары
						case 2:
							{
								if (empty($only_name))
								{
									$query = "SELECT `id`, `name`
											FROM `shop_items`
											WHERE (`name` LIKE '%$search%' OR `description` LIKE '%$search%') AND `is_public` = 1
											ORDER BY `id` DESC
											LIMIT $pageDB, 10";

									$result_q = "SELECT COUNT(`id`) AS `cnt`
												FROM `shop_items`
												WHERE (`name` LIKE '%$search%' OR `description` LIKE '%$search%') AND `is_public` = 1";
								}
								else
								{
									$query = "SELECT `id`, `name`
											FROM `shop_items`
											WHERE `name` LIKE '%$search%' AND `is_public` = 1
											ORDER BY `id` DESC
											LIMIT $pageDB, 10";

									$result_q = "SELECT COUNT(`id`) AS `cnt`
												FROM `shop_items`
												WHERE `name` LIKE '%$search%' AND `is_public` = 1";
								}
							}
							break;
					}

					$result_cnt = (int) T_GetData($result_q, "cnt");	// Количество найденного контента

					if (!empty($result_cnt))
					{
						$q     = T_GetData($query, "", 1);				// Запрос на данные
						$q_cnt = mysqli_num_rows($q);					// Количество результатов на странице

						$results .= '<div id="search-results-count">Нашлось результатов: ' . $result_cnt . '.</div>';

						if (!empty($q_cnt))
						{
							foreach ($q as $find)
							{
								$id   = (int) $find['id'];
								$name = "";
								$link = "";
								$type = "";

								switch ($where)
								{
									case 0:
										{
											$name = T_CutText($find['title'], 70);
											$link = "/pages/show/" . $id . "-" . T_Semantic($find['title']);
											$type = "Страница";
										}
										break;
									case 1:
										{
											$name = T_CutText($find['title'], 70);
											$link = "/news/show/" . $id . "-" . T_Semantic($find['title']);
											$type = "Новость";
										}
										break;
									case 2:
										{
											$name = T_CutText($find['name'], 70);
											$link = "/shop/item/" . $id . "-" . T_Semantic($find['name']);
											$type = "Товар";
										}
										break;
								}

								$results .= '<div class="search-result"><div class="search-result-type">' . $type . '</div><div class="search-result-title"><a href="' . $link . '" target="_blank">' . $name . '</a></div></div>';
							}

							// Пагинация

							$page_cnt = ceil($result_cnt / 10);	// Количество страниц

							if ($page <= $page_cnt)
								$results .= '<div class="search-result-page">Стр. ' . $page . ' / ' . $page_cnt . '</div>';

							if ($page < $page_cnt)
								$results .= "<div class=\"news-page\" onclick=\"location.href='/search/page/" . ($page + 1) . "'\">Далее</div>";

							if ($page > $page_cnt || $page > 1)
								$results .= "<div class=\"news-page\" onclick=\"location.href='/search/page/" . ($page - 1) . "'\">Назад</div>";
						}
						else
							$results = '<div class="center">Нет результатов.</div>';
					}
					else
						$results = '<div class="center">Нет результатов.</div>';
				}
			}
		}
	}

	$on_checked = "";
	if ($only_name == 1)
		$on_checked = " checked";

	$wh_0 = "";
	$wh_1 = "";
	$wh_2 = "";
	switch ($where)
	{
		case 0: $wh_0 = " selected"; break;
		case 1: $wh_1 = " selected"; break;
		case 2: $wh_2 = " selected"; break;
	}

	$content = '
		<div class="center">
			<form name="search" method="POST">
				<div id="search-zone">
					<input type="text" name="search_field" minlength="3" maxlength="50" placeholder="Введите поисковый запрос..." value="' . $search . '" required>
					<button class="btn">ИСКАТЬ</button>
				</div>
				<div id="search-params">
					<strong>Параметры поиска</strong>
					<br>
					Где искать:
					<select name="where" id="search-where">
						<option value="0"' . $wh_0 . '>В страницах</option>
						<option value="1"' . $wh_1 . '>В новостях</option>
						<option value="2"' . $wh_2 . '>В товарах</option>
					</select>
					<div class="checkbox">
						<input type="checkbox" id="isName" name="only_name" value="1"' . $on_checked . '>
						<label for="isName">Только в имени</label>
					</div>
				</div>
			</form>
			<div id="search-results">' . $results . '</div>
		</div>';

	require("templates/" . T_Template() . "/layouts/header.php");
	echo "<div class=\"content\">$content</div>";