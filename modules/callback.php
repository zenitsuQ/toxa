<?php
	$content    = "";

	$_SiteTitle = "Обратная связь";

	$name    = T_SafeText(Trim($_POST['name']));
	$phone   = T_SafeText(Trim($_POST['phone']));
	$email   = T_SafeText(Trim($_POST['email']));
	//$city    = T_SafeText(Trim($_POST['city']));
	$message = T_SafeText($_POST['text']);

	if (!empty($name) && !empty($phone) && !empty($email) && !empty($message))
	{
		if (T_Authorized())
		{
			$result = T_GetData("INSERT INTO `callbacks` (`date`, `user`, `name`, `phone`, `email`, `message`) VALUES (NOW(), $_userID, '$name', '$phone', '$email', '$message')", "", 1);

			if ($result)
				$content .= '<center>Ваше обращение сохранено! После его обработки с Вами свяжется наш менеджер.</center>';
		}
		else
			$content .= '<center>Обратная связь доступна только авторизованным пользователям!</center>';
	}
	else
	{
		$content = '
		<center>
			<form method="POST">
				<input class="def-input" type="text" name="name" required placeholder="Как Вас зовут?" maxlength="50"><br>
				<input class="def-input" type="text" name="phone" required placeholder="Введите Ваш номер телефона..." maxlength="20"><br>
				<input class="def-input" type="email" name="email" required placeholder="Введите Ваш адрес электронной почты..." maxlength="100"><br>
				<textarea class="def-input" name="text" placeholder="Введите Ваше сообщение..." required></textarea><br>
				Нажимая на кнопку "Отправить", вы соглашаетесь на обработку персональных данных и с политикой конфиденциальности нашего сайта.
				<br>
				<input type="submit" value="ОТПРАВИТЬ">
			</form>
		</center>';
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