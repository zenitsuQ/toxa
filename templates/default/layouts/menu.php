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
			default:
				{
					$_currentS  = 0;
				}
				break;
		}
?>
		<center class="menu-center">
		<div class="container">
            <ul id="nav">
                <li<?=($_currentS == 0) ? ' class="active"' : ""?>><a href="/">Главная</a></li>
<?php if (T_Authorized()): ?>
				<li<?=($_currentS == 1) ? ' class="active"' : ""?>><a href="/pa">Личный кабинет</a>
					<span id="s1"></span>
					<ul class="subs">
						<li><a href="javascript:void(0)">Личный кабинет</a>
							<ul>
								<li><a href="/pa/profile">Профиль</a></li>
								<li><a href="/pa/account/security">Учётные данные</a></li>
							</ul>
						</li>
					</ul>
				</li>
<?php endif ?>
				<li<?=($_currentS == 6) ? ' class="active"' : ""?>><a href="/news">Новости</a></li>
				<li<?=($_currentS == 4) ? ' class="active"' : ""?>><a href="/feedbacks">Отзывы</a></li>
				<li<?=($_currentS == 3) ? ' class="active"' : ""?>><a href="/search">Поиск</a></li>
				<li<?=($_currentS == 2) ? ' class="active"' : ""?>><a href="/shop">Магазин</a>
					<span id="s1"></span>
					<ul class="subs">
						<li>
							<ul>
								<li><a href="/shop">Магазин</a></li>
<?php if (T_Authorized()): ?>
								<li><a href="/shop/cart">Корзина</a></li>
								<li><a href="/shop/orders">Заказы</a></li>
<?php endif ?>
							</ul>
						</li>
					</ul>
				</li>
				<li<?=($_currentS == 5) ? ' class="active"' : ""?>><a href="/pages/show/1-kontakty">Контакты</a>
					<span id="s1"></span>
					<ul class="subs">
						<li>
							<ul>
								<li><a href="/pages/show/1-kontakty">Контакты</a></li>
<?php if (T_Authorized()): ?>
								<li><a href="/callback">Обратная связь</a></li>
<?php endif ?>
							</ul>
						</li>
					</ul>
				</li>
<?php
	if (T_Admin())
		echo '				<li><a href="/admin" target="_blank">Админ-панель</a></li>';
?>
            </ul>
        </div>
		</center>