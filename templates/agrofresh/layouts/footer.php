
	<footer class="wrapper">
        <div class="footer-mts-desc">
            <p class="footer-mts-desc-title">АгроФреш — надёжные и свежие сельскохозяйственные продукты</p>
            <p class="footer-mts-desc-desc">Мы предлагаем свежие и качественные товары прямо от фермеров — овощи, фрукты, мясо, молоко и многое другое. Всё натуральное, без лишних добавок.</p>
			<br>
			<p>Разработка: Иванченков Д. А.</p>
        </div>

        <ul class="footer-menu">
            <li><a href="/">Главная</a></li>
            <li><a href="/pages/show/1-kontakty">Контакты</a></li>
            <li><a href="/news">Новости</a></li>
            <li><a href="/shop">Товары</a></li>
<?php if (T_Authorized()): ?>
			<li><a href="/shop/orders">Заказы</a></li>
			<li><a href="/shop/cart">Корзина</a></li>
			<li><a href="/pa/profile">Профиль</a></li>
<?php endif; ?>
            <li><a href="/callback">Обратная связь</a></li>
<?php if (T_Admin()): ?>
			<li><a href="/admin" target="_blank">Админ-панель</a></li>
<?php endif; ?>
        </ul>
	</footer>
</body>
</html>