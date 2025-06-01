<?php
	$id   = (int) CURRENT_PARAM;
	$data = null;

	if (T_Admin())
		$data = T_GetData("SELECT `title`, `content` FROM `pages` WHERE `id` = $id");
	else
		$data = T_GetData("SELECT `title`, `content` FROM `pages` WHERE `id` = $id AND `is_public` = 1");

	if (!empty($data))
	{
		// Валидация семантической ссылки
		$full_param = CURRENT_PARAM;
		$real_param = $id . "-" . T_Semantic($data['title']);

		// Если ссылка правильно составлена - выводим страницу
		if ($full_param == $real_param)
		{
			$_SiteTitle = $data['title'];
			$content    = T_BBParse($data['content']);
		}
		else
		{
			$_SiteTitle = "Ошибка!";
			$content    = '<div class="center">Такой страницы не существует!</div>';
		}
	}
	else
	{
		$_SiteTitle = "Ошибка!";
		$content    = '<div class="center">Такой страницы не существует!</div>';
	}

	require("templates/" . T_Template() . "/layouts/header.php");
	//echo "<div class=\"content\"><pre wrap>$content</pre></div>";
?>
	<section class="wrapper">
        <div class="white-container">
            <h1><img src="/templates/<?=T_Template()?>/assets/icons/icon_for_title.svg" class="icon-for-title" alt="MTS_"><?=$_SiteTitle?></h1>
			<div class="content"><pre wrap><?=$content?></pre></div>
		</div>
    </section>
