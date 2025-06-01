<?php
		switch (CURRENT_SECTION)
		{
			case "pa":
				{
					$_currentS  = 1;
				}
				break;
			case "shop":
				{
					$_currentS  = 2;

					if (CURRENT_ACTION == "orders")
						$_currentS  = 8;

					if (CURRENT_ACTION == "cart")
						$_currentS  = 99;
				}
				break;
			case "search":
				{
					$_currentS  = 3;
				}
				break;
			case "feedbacks":
				{
					$_currentS  = 4;
				}
				break;
			case "news":
				{
					$_currentS  = 6;
				}
				break;
			case "pages":
				{
					if (CURRENT_PARAM == "1-kontakty")
						$_currentS  = 5;
					else
						$_currentS  = 99;
				}
				break;
			case "callback":
				{
					$_currentS  = 5;
				}
				break;
			case "index":
				{
					$_currentS  = 7;
				}
				break;
			default:
				{
					$_currentS  = 0;
				}
				break;
		}
?>
		<nav>
            <div style="display:inline-block; vertical-align: middle;">
				<img src="/templates/<?=T_Template()?>/assets/img/favicon.png" alt="Logo" height="30" class="header-logo" style="display:inline-block; vertical-align: middle; margin-right: 10px;">
				<p style="display:inline-block; vertical-align: middle; margin-right: 20px; font-size: 14pt; color: #F9BC1F; font-weight: bold;">АгроФреш</p>
			</div>
            <ul class="header-menu">
                <li><a href="/"<?=($_currentS == 7) ? ' class="active"' : ""?>>Главная</a></li>
                <!--<li><a href="https://kursk.mts.ru/personal/mobilnaya-svyaz/tarifi/vse-tarifi" target="_blank">Купить SIM</a></li>-->
                <li><a href="/shop"<?=($_currentS == 2) ? ' class="active"' : ""?>>Товары</a></li>
<?php if (T_Authorized()): ?>
				<li><a href="/shop/orders"<?=($_currentS == 8) ? ' class="active"' : ""?>>Заказы</a></li>
<?php endif; ?>
                <li><a href="/news"<?=($_currentS == 6) ? ' class="active"' : ""?>>Новости</a></li>
                <li><a href="/pages/show/1-kontakty"<?=($_currentS == 5) ? ' class="active"' : ""?>>Контакты</a></li>
<?php if (T_Admin()): ?>
				<li><a href="/admin" target="_blank">Админ-панель</a></li>
<?php endif; ?>
            </ul>
        </nav>
<?php if (!T_Authorized()): ?>
        <ul>
            <li><a href="/reg" class="btn-register">Регистрация</a></li>
            <li><a href="/auth" class="btn-login">Войти</a></li>
        </ul>
<?php else: ?>
		<ul>
            <li><a href="/shop/cart" class="btn-cart"><img src="/templates/<?=T_Template()?>/assets/icons/cart.svg" alt=""><?php
			$_cartCount = (int) T_GetData("SELECT COUNT(`id`) AS `cnt` FROM `shop_cart` WHERE `user` = $_userID", "cnt");

			if ($_cartCount > 0): ?> <div id="cart_count"><?=$_cartCount?></div><?php endif; ?></a></li>
            
            <li class="btn-profile-container">
                <label for="cb_btn_view_profile" class="btn-view-profile">
<?php
	$ava_show = "";

	switch($_SESSION['sex'])
	{
		case 0: $ava_show = "default";
			break;
		case 1: $ava_show = "default_m";
			break;
		case 2: $ava_show = "default_f";
			break;
	}
?>
                    <img id="user_photo" src="/templates/<?=T_Template()?>/assets/img/avatars/<?=$ava_show?>.png" alt="" width="32" height="32">
                    <span id="user_full_name"><?=$_SESSION['login']?></span>
                    <img class="icon_arrow_down" src="/templates/<?=T_Template()?>/assets/icons/icons_arrow_down.svg" alt="">
                </label>

                <input type="checkbox" name="cb_btn_view_profile" id="cb_btn_view_profile">

                <div class="user-dropdown-profile-menu">
                    <div><a href="/pa/profile">Профиль</a></div>
					<div><a href="/pa/account/security">Безопасность</a></div>
                    <div><a href="/exit">Выйти</a></div>
                </div>
                
            </li>
        </ul>
<?php endif; ?>