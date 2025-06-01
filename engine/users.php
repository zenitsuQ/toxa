<?php
	switch(CURRENT_SECTION)
	{
		// ======================= Авторизация =======================
		case "auth":
		{
			$_SiteTitle = "Авторизация";
			require("templates/" . T_Template() . "/layouts/header.php");

			PRINT <<<HERE
				<section class="login-page">
					<img src="/templates/agrofresh/assets/icons/icon_for_title.svg" class="icon-for-title" alt="AgroFresh" height="60px">
						<form name="auth" action="/auth" method="POST">
							<input class="def-input" type="text" name="login" id="login" minlength="1" maxlength="100" placeholder="Логин" required>
							<input class="def-input" type="password" name="pass" id="password" minlength="10" maxlength="72" placeholder="Пароль" required>
							<script defer src="https://www.google.com/recaptcha/api.js?hl=ru"></script>
							<center><div class="g-recaptcha" data-sitekey="$rc_key" data-theme="light"></div></center>
							<input name="auth" type="submit" value="Войти">
						</form><br>
						<a href="/restore" title="Восстановление пароля">Забыли пароль?</a>
						<a href="/reg" title="Зарегистрироваться">Нет учётной записи?</a>
				</section>
HERE;

			if (isset($_POST['auth']))
			{
				$login = T_SafeText($_POST['login']);
				$pass  = T_SafeText(Trim($_POST['pass']));

				if ($rc_response != null && $rc_response->success)
				{
					$r = T_GetData("SELECT `pass` FROM `users` WHERE `login` = '$login'", "pass");

					if (!empty($r))
					{
						if (password_verify($pass, $r))
						{
							$q = T_GetData("SELECT `users`.`id` AS `user_id`, `users`.`status` AS `user_status`, `users_avatars`.`id` AS `avatar_id`, `users_profiles`.`sex` AS `sex`
							FROM `users`
							LEFT JOIN `users_avatars` ON `users_avatars`.`user` = `users`.`id`
                            LEFT JOIN `users_profiles` ON `users_profiles`.`user` = `users`.`id`
							WHERE `users`.`login` = '$login'");

							if ($q['user_status'] == 1)
							{
								$_userID = $q['user_id'];
	
								$_SESSION['id']    = $_userID;
								$_SESSION['login'] = $login;
								$_SESSION['pass']  = $r;
	
								$_SESSION['sex'] = $q['sex'];
	
								if (empty($q['avatar_id']))
									$_SESSION['avatar'] = 0;
								else
								{
									$ava = T_GetData("SELECT `id` FROM `users_avatars` WHERE `user` = $_userID AND `is_active` = 1 AND `is_deleted` = 0", "id");
	
									if (!empty($ava))
										$_SESSION['avatar'] = $ava;
									else
										$_SESSION['avatar'] = 0;
								}

								T_GetData("UPDATE `users` SET `l_date` = NOW() WHERE `id` = $_userID", "", 1);

								echo '<meta http-equiv="refresh" content="0;/"><center>Вы успешно авторизовались!</center>';
							}
							else
								echo '<meta http-equiv="refresh" content="5;/"><center>Ваша учётная запись отключена. Обратитесь к администраторам для выяснения причины.</center>';
						}
						else
							echo "<div class=\"error\">Неверный логин или пароль!</div>";
					}
					else
						echo "<div class=\"error\">Неверный логин или пароль!</div>";
				}
				else
					echo "<div class=\"error\">Вы не прошли анти-спам проверку!</div>";
			}

			echo "			</div>";
		}
		break;
		// ======================= Регистрация =======================
		case "reg":
		{
			$_SiteTitle = "Регистрация";
			require("templates/" . T_Template() . "/layouts/header.php");

			PRINT <<<HERE
			<section class="login-page">
				<img src="/templates/agrofresh/assets/icons/icon_for_title.svg" class="icon-for-title" alt="AgroFresh" height="60px">
			<div class="content">
				<form name="auth" action="/reg" method="POST">
							<input type="text" class="def-input" name="login" minlength="1" maxlength="100" placeholder="Придумайте логин..." required>
							<input type="email" class="def-input" name="email" minlength="10" maxlength="100" placeholder="Введите свой E-Mail..." required>
							<center><script defer src="https://www.google.com/recaptcha/api.js?hl=ru"></script>
							<div class="g-recaptcha" data-sitekey="$rc_key" data-theme="light"></div>
							<input name="reg" type="submit" value="Зарегистрироваться">
				</form>
				<a href="/restore" title="Восстановление пароля">Забыли пароль?</a><br>
				<a href="/auth" title="Зарегистрироваться">Уже зарегистрированы?</a></center>
HERE;

			if (isset($_POST['reg']))
			{
				$login = T_SafeText(Trim($_POST['login']));
				$email = T_SafeText(Trim($_POST['email']));

				if ($rc_response != null && $rc_response->success)
				{
					$t_email = T_GetData("SELECT `id` FROM `users` WHERE `email` = '$email'", "id");
					$t_login = T_GetData("SELECT `id` FROM `users` WHERE `login` = '$login'", "id");

					if (!empty($t_email))
					{
						echo "<div class=\"error\">Пользователь с таким адресом электронной почты уже существует!</div>";
					}

					if (!empty($t_login))
					{
						echo "<div class=\"error\">Пользователь с таким логином уже существует!</div>";
					}

					if (empty($t_login) && empty($t_email))
					{
						$password = T_RSGen(rand(10, 20));
						$enc_pass = password_hash($password, PASSWORD_BCRYPT);

						$add = T_GetData("INSERT INTO `users` (`date`, `email`, `login`, `pass`) VALUES (NOW(), '$email', '$login', '$enc_pass')", "", 1);

						if ($add)
						{
							$id = (int) T_GetData("SELECT `id` FROM `users` WHERE `login` = '$login' AND `email` = '$email'", "id");

							$ank = T_GetData("INSERT INTO `users_profiles` (`user`) VALUES ($id)", "", 1);
							if ($ank)
							{
								$role = T_GetData("INSERT INTO `roles_list` (`user`) VALUES ($id)", "", 1);
								if ($role)
								{
									// Отправляем E-Mail
									$MailTitle = "Регистрация на сайте";
									$etext     = "Благодарим за регистрацию на сайте!<br><br>Ваш логин: $login<br>Ваш пароль: $password<br><br><a href=\"http://" . SITE_HOST . "\" target=\"_blank\">$site_name</a>";

									T_SendMail($email, $MailTitle, $etext);

									echo '<meta http-equiv="refresh" content="3;/"><center>Регистрация прошла успешно! Вам отправлено письмо с паролем.</center>';
								}
							}
							else
								echo "<div class=\"error\">Ошибка регистрации!</div>";
						}
						else
							echo "<div class=\"error\">Ошибка регистрации!</div>";
					}
				}
				else
					echo "<div class=\"error\">Вы не прошли анти-спам проверку!</div>";
			}
			echo "</section>";
		}
		break;
		// ======================= Восстановление пароля =======================
		case "restore":
		{
			switch(CURRENT_ACTION)
			{
				// Подтверждение сброса пароля
				case "accept":
				{
					$_SiteTitle = "Восстановление пароля";
					require("templates/" . T_Template() . "/layouts/header.php");

					print <<<HERE
		<section class="wrapper">
        <div class="white-container">
            <h1><img src="/templates/agrofresh/assets/icons/icon_for_title.svg" class="icon-for-title" alt="AgroFresh_">$_SiteTitle</h1>
HERE;

					$rkey = T_SafeText(Trim(CURRENT_PARAM));

					$query = T_GetData("
							SELECT `users_restores`.`id`, `users_restores`.`user`, `users`.`login`
							FROM `users_restores`
							LEFT JOIN `users` ON `users`.`id` = `users_restores`.`user`
							WHERE `users_restores`.`restore_key` = '$rkey' AND `users_restores`.`is_active` = 1");

					if (!empty($query))
					{
						$id   = (int) $query['id'];
						$user = (int) $query['user'];
						$login = $query['login'];

						$email    = T_GetData("SELECT `email` FROM `users` WHERE `id` = $user", "email");
						$password = T_RSGen(rand(10, 20));
						$enc_pass = password_hash($password, PASSWORD_BCRYPT);

						$upd = T_GetData("UPDATE `users` SET `pass` = '$enc_pass' WHERE `id` = $user", "", 1);

						if ($upd)
						{
							$MailTitle = "Восстановление пароля";
							$etext     = "Заявка на восстановление пароля принята.<br><br>Ваш логин: $login<br>Ваш новый пароль: $password<br><br><a href=\"http://" . SITE_HOST . "\" target=\"_blank\">$site_name</a>";

							T_SendMail($email, $MailTitle, $etext);

							echo '<meta http-equiv="refresh" content="3;/"><center>Восстановление пароля прошло успешно! Вам отправлено электронное письмо с новым паролем.</center>';

							T_GetData("UPDATE `users_restores` SET `is_active` = 0 WHERE `id` = $id", "", 1);
						}
						else
							echo "<div class=\"error\">Ошибка восстановления пароля!</div>";
					}
					else
						echo "<div class=\"error\">Ошибка восстановления пароля!</div>";
					
					echo "</div></section>";
				}
				break;
				// Отклонение сброса пароля
				case "decline":
				{
					$_SiteTitle = "Отклонение восстановления пароля";
					require("templates/" . T_Template() . "/layouts/header.php");

					print <<<HERE
		<section class="wrapper">
        <div class="white-container">
            <h1><img src="/templates/agrofresh/assets/icons/icon_for_title.svg" class="icon-for-title" alt="AgroFresh_">$_SiteTitle</h1>
HERE;

					$rkey = T_SafeText(Trim(CURRENT_PARAM));

					$id = (int) T_GetData("SELECT `id` FROM `users_restores` WHERE `restore_key` = '$rkey' AND `is_active` = 1", "id");

					if (!empty($id))
					{
						$q = T_GetData("UPDATE `users_restores` SET `is_active` = 0 WHERE `id` = $id", "", 1);

						if ($q)
						{
							echo '<meta http-equiv="refresh" content="3;/"><center>Запрос на восстановление пароля успешно отклонён.</center>';
						}
						else
							echo "<div class=\"error\">Ошибка отклонения запроса!</div>";
					}
					else
						echo "<div class=\"error\">Ошибка отклонения запроса!</div>";
					
					echo "</div></section>";
				}
				break;
				// Заявка на сброс пароля
				default:
				{
					$_SiteTitle = "Восстановление пароля";
					require("templates/" . T_Template() . "/layouts/header.php");

					PRINT <<<HERE
			<section class="login-page">
				<img src="/templates/agrofresh/assets/icons/icon_for_title.svg" class="icon-for-title" alt="AgroFresh" height="60px">
			<div class="content">
				<form name="auth" action="/restore" method="POST">
							<input type="email" class="def-input" name="email" minlength="10" maxlength="100" placeholder="Введите свой E-Mail..." required>
							<center><script defer src="https://www.google.com/recaptcha/api.js?hl=ru"></script>
							<div class="g-recaptcha" data-sitekey="$rc_key" data-theme="light"></div>
							<input name="restore" type="submit" value="Восстановить пароль">
				</form>
				<br>
				<a href="/reg" title="Зарегистрироваться">Нет учётной записи?</a><br>
				<a href="/auth" title="Зарегистрироваться">Уже зарегистрированы?</a>
HERE;

					if (isset($_POST['restore']))
					{
						$email = T_SafeText(Trim($_POST['email']));

						if ($rc_response != null && $rc_response->success)
						{
							$t_user = T_GetData("SELECT `id`, `login` FROM `users` WHERE `email` = '$email'");

							if (empty($t_user))
							{
								echo "<div class=\"error\">Пользователя с таким адресом электронной почты не существует!</div>";
							}
							else
							{
								$id    = (int) $t_user['id'];
								$login = $t_user['login'];

								$q    = T_GetData("SELECT `id`, `restore_key`, `send_count` FROM `users_restores` WHERE `user` = $id AND `is_active` = 1");

								if (!empty($q))
								{
									$rid    = (int) $q['id'];
									$rkey   = $q['restore_key'];
									$scount = (int) $q['send_count'];

									if ($scount < 3)
									{
										$scount++;

										$upd = T_GetData("UPDATE `users_restores` SET `send_count` = $scount WHERE `id` = $rid", "", 1);

										if ($upd)
										{
											$MailTitle = "Заявка на восстановление пароля";

											$etext     = "Здравствуйте, $login!<br><br>Вы получили это письмо, потому что кто-то запросил восстановление пароля на Вашу учётную запись. Данный запрос был отправлен: $scount раза.<br><br>Чтобы сбросить пароль, нажмите <a href=\"http://" . SITE_HOST . "/restore/accept/$rkey\" target=\"_blank\">сюда</a>.<br>Если Вы не запрашивали сброс пароля - нажмите <a href=\"http://" . SITE_HOST . "/restore/decline/$rkey\" target=\"_blank\">сюда</a>.";

											T_SendMail($email, $MailTitle, $etext);

											echo '<meta http-equiv="refresh" content="3;/"><center>Инструкция по восстановлению пароля была выслана Вам на E-Mail! Если письмо не приходит - проверьте папку "Спам".</center>';
										}
										else
											echo '<div class=\"error\">Ошибка восстановления пароля!</div>';
									}
									else
										echo '<div class=\"error\">Вы исчерпали количество отправок запросов на восстановление пароля!</div>';
								}
								else
								{
									$rkey = T_RSGen(64);
									$ip   = $_SERVER['REMOTE_ADDR'];
									$ua   = T_SafeText(Trim($_SERVER['HTTP_USER_AGENT']));

									$q = T_GetData("SELECT `id` FROM `users_restores` WHERE `restore_key` = '$rkey'", "id");
									while(!empty($q))
									{
										$rkey = T_RSGen(64);

										$q = T_GetData("SELECT `id` FROM `users_restores` WHERE `restore_key` = '$rkey'", "id");
									}

									$q = T_GetData("INSERT INTO `users_restores` (`ip`, `user`, `date`, `restore_key`, `user_agent`) VALUES ('$ip', $id, NOW(), '$rkey', '$ua')", "", 1);

									if ($q)
									{
										$MailTitle  = "Заявка на восстановление пароля";

										$etext   = "Здравствуйте, $login!<br><br>Вы получили это письмо, потому что кто-то запросил восстановление пароля на Вашу учётную запись.<br><br>Чтобы сбросить пароль, нажмите <a href=\"http://" . SITE_HOST . "/restore/accept/$rkey\" target=\"_blank\">сюда</a>.<br>Если Вы не запрашивали сброс пароля - нажмите <a href=\"http://" . SITE_HOST . "/restore/decline/$rkey\" target=\"_blank\">сюда</a>.";

										T_SendMail($email, $MailTitle, $etext);

										echo '<meta http-equiv="refresh" content="3;/"><center>Инструкция по восстановлению пароля была выслана Вам на E-Mail! Если письмо не приходит - проверьте папку "Спам".</center>';
									}
									else
										echo "<div class=\"error\">Ошибка восстановления пароля!</div>";
								}
							}
						}
						else
							echo "<div class=\"error\">Вы не прошли анти-спам проверку!</div>";
					}
					echo "</center></section>";
				}
				break;
			}
		}
		break;
		// ======================= Выход =======================
		case "exit":
		{
			T_DropSession(T_GetSessionID(session_id()));

			session_unset();
			session_destroy();

			SetCookie('userID');
			SetCookie('login');
			SetCookie('role');
			SetCookie('remember');
			SetCookie('key');
			SetCookie('sid');

			SetCookie(session_name());

			$_SiteTitle = "Выход";
			require("templates/" . T_Template() . "/layouts/header.php");

			PRINT <<<HERE
	<section class="wrapper">
        <div class="white-container">
            <h1><img src="/templates/agrofresh/assets/icons/icon_for_title.svg" class="icon-for-title" alt="AgroFresh_">$_SiteTitle</h1>
			<meta http-equiv="refresh" content="3;/index"><div class="content"><center>Вы успешно вышли.</center></div>
		</div>
	</section>
HERE;
		}
		break;
	}