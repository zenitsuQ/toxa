<?php
	$_SiteTitle = "Главная";
	require("templates/" . T_Template() . "/layouts/header.php");
?>
	<section class="banner-section">
        <div class="block-banner wrapper">
            <div class="banner-card">
                <p class="banner-card-title">АгроФреш</p> 
                <p class="banner-card-text">Свежие продукты от местных фермеров</p>

                <a href="/pages/show/2-o-nas" class="banner-card-btn">Подробнее</a>
            </div>
        </div>
    </section>

    <section class="wrapper" id="block_tariffs">
        <div class="white-container">
            <h1><img src="/templates/<?=T_Template()?>/assets/icons/icon_for_title.svg" class="icon-for-title" alt="MTS_">Новости</h1>
            
            <div class="card-list" style="justify-content: center;">
<?php
	$news_cnt = T_GetData("SELECT COUNT(`id`) AS `cnt` FROM `news` WHERE `is_public` = 1 ORDER BY `id` DESC LIMIT 8", "cnt");

	if ($news_cnt != 0)
	{
		$q = T_GetData("SELECT `id`, `title`, `text` FROM `news` WHERE `is_public` = 1 ORDER BY `id` DESC LIMIT 8", "", 1);

		foreach ($q as $new)
		{
			$new_id   = (int) $new['id'];
			$new_name = T_CutText($new['title'], 50);
			$new_text = T_CutText($new['text'], 140);

			$news_link = "/news/show/" . $new_id . "-" . T_Semantic($new['title']);

			$cover = T_GetData("SELECT `file_name`, `hash`, `height`, `width` FROM `news_images` WHERE `news_id` = $new_id AND `is_cover` = 1 LIMIT 1");
			$cover_path = "";

			if (!empty($cover))
			{
				$c_hash = $cover['hash'];
				$c_name = $cover['file_name'];

				$c_s_name = StrToLower(MB_SubStr($c_name, 0, 1, 'UTF-8'));
				$c_s_hash = StrToLower(MB_SubStr($c_hash, 0, 2, 'UTF-8'));

				$cover_path = "/files/news/$c_s_name/$c_s_hash/$c_name";
			}
			else
				$cover_path = "/templates/" . T_Template() . "/assets/images/no_image.png";

			PRINT <<<HERE
					<div class="card" onclick="location.href='$news_link'">
						<img src="$cover_path" alt="Photo" class="card-cover">
						<p class="card-title">$new_name</p>
						<p class="card-description">$new_text</p>
					</div>
HERE;
		}
	}
	else
		echo "Нет новостей.";
?>
            </div>
        </div>
    </section>

    <section class="wrapper" id="block_support">
        <div class="white-container">
            <h1><img src="/templates/<?=T_Template()?>/assets/icons/icon_for_title.svg" class="icon-for-title" alt="MTS_">Обратная связь</h1>
            
            <form action="/callback" method="POST">
                <input class="def-input" type="text" name="name" required placeholder="Как Вас зовут?" maxlength="50"><br>
				<input class="def-input" type="text" name="phone" required placeholder="Введите Ваш номер телефона..." maxlength="20"><br>
				<input class="def-input" type="email" name="email" required placeholder="Введите Ваш адрес электронной почты..." maxlength="100"><br>
				<textarea class="def-input" name="text" placeholder="Введите Ваше сообщение..." required></textarea>

                <input type="submit" value="Отправить">
            </form>
        </div>
    </section>