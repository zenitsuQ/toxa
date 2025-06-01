<?php
	if (!T_Authorized())
	{
		$_SiteTitle = "Ошибка!";
		require("templates/" . T_Template() . "/layouts/header.php");

		echo "Для доступа к этому разделу необходимо быть авторизованным!";
		return;
	}

	switch(CURRENT_ACTION)
	{
		case "profile":
		{
			switch(CURRENT_PARAM)
			{
				case "edit":
				{
					$_userID = (int) $_SESSION['id'];

					$_SiteTitle = "Редактирование профиля";
					require("templates/" . T_Template() . "/layouts/header.php");

					if (!isset($_POST['save']))
					{
						$profile = T_GetData("SELECT * FROM `users_profiles` WHERE `user` = $_userID");

						$lastname   = $profile['lastname'];
						$name       = $profile['name'];
						$patronymic = $profile['patronymic'];
						$city       = $profile['city'];
						$about      = $profile['about'];
						$birthday   = $profile['birthday'];
						$sex        = $profile['sex'];
						$s_year     = $profile['conf_for_consult'];
					}
					else
					{
						$lastname   = T_SafeText(Trim($_POST['lastname']));
						$name       = T_SafeText(Trim($_POST['name']));
						$patronymic = T_SafeText(Trim($_POST['patronymic']));
						$city       = T_SafeText(Trim($_POST['city']));
						$about      = T_SafeText(Trim($_POST['about']));
						$birthday   = T_SafeText(Trim($_POST['birthday']));
						$sex        = (int) $_POST['sex'];
						$s_year     = (int) $_POST['conf_for_consult'];
					}

					$csy = "";
					if ($s_year)
						$csy = " checked";

					$sex_op = "";
					$arr[] = "Не указан";
					$arr[] = "Мужской";
					$arr[] = "Женский";

					for ($i = 0; $i < 3; $i++)
					{
						$ckd = "";
						if ($sex == $i)
							$ckd = " selected";

						$sex_op .= '<option value="'.$i.'"'.$ckd.'>'.$arr[$i].'</option>';
					}

					PRINT <<<HERE
		<section class="wrapper">
        <div class="white-container">
            <h1><img src="/templates/agrofresh/assets/icons/icon_for_title.svg" class="icon-for-title" alt="MTS_">$_SiteTitle</h1>
			<center>
				<form name="edit" method="POST">
					<table width="70%" align="center">
					<tr>
						<td width="30%">
							Фамилия:
						</td>
						<td width="70%">
							<input class="def-input" type="text" name="lastname" maxlength="100" placeholder="Введите фамилию..." value="$lastname">
						</td>
					</tr>
					<tr>
						<td width="30%">
							Имя:
						</td>
						<td width="70%">
							<input class="def-input" type="text" name="name" maxlength="100" placeholder="Введите имя..." value="$name">
						</td>
					</tr>
					<tr>
						<td width="30%">
							Отчество:
						</td>
						<td width="70%">
							<input class="def-input" type="text" name="patronymic" maxlength="100" placeholder="Введите отчество..." value="$patronymic">
						</td>
					</tr>
					<tr>
						<td width="30%">
							Город:
						</td>
						<td width="70%">
							<input class="def-input" type="text" name="city" maxlength="100" placeholder="Введите город..." value="$city">
						</td>
					</tr>
					<tr>
						<td width="30%">
							Дата рождения:
						</td>
						<td width="70%">
							<input class="def-input" type="date" name="birthday" maxlength="100" placeholder="Введите дату рождения..." value="$birthday">
							<div class="checkbox">
								<input type="checkbox" id="conf_for_consult" name="conf_for_consult" value="1"$csy>
								<label for="conf_for_consult">Показывать год</label>
							</div>
						</td>
					</tr>
					<tr>
						<td width="30%">
							Пол:
						</td>
						<td width="70%">
							<select name="sex" class="def-input">
								$sex_op
							</select>
						</td>
					</tr>
					<tr>
						<td width="30%">
							О себе:
						</td>
						<td width="70%">
							<textarea class="def-input" name="about" placeholder="Расскажите о себе..." maxlength="1000">$about</textarea>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center">
							<input name="save" type="submit" value="Сохранить">
						</td>
					</tr>
					</table>
				</form>
HERE;

					if (isset($_POST['save']))
					{
						if (MB_StrLen($lastname, "UTF-8") > 100)
						{
							echo '<div class="error">Длина фамилии должна быть не более 100 символов!</div>';
							return;
						}
						if (MB_StrLen($name, "UTF-8") > 100)
						{
							echo '<div class="error">Длина имени должна быть не более 100 символов!</div>';
							return;
						}
						if (MB_StrLen($patronymic, "UTF-8") > 100)
						{
							echo '<div class="error">Длина отчества должна быть не более 100 символов!</div>';
							return;
						}
						if (MB_StrLen($city, "UTF-8") > 100)
						{
							echo '<div class="error">Длина города должна быть не более 100 символов!</div>';
							return;
						}
						if (MB_StrLen($about, "UTF-8") > 1000)
						{
							echo '<div class="error">Длина информации о себе должна быть не более 1000 символов!</div>';
							return;
						}
						if ($sex < 0 || $sex > 2)
						{
							echo '<div class="error">Некорректное значение поля "Пол"!</div>';
							return;
						}
						if ($s_year < 0 || $s_year > 1)
						{
							echo '<div class="error">Некорректное значение поля "Показывать год"!</div>';
							return;
						}

						$upd = T_GetData("UPDATE `users_profiles` SET `lastname` = '$lastname', `name` = '$name', `patronymic` = '$patronymic', `city` = '$city', `birthday` = '$birthday', `sex` = $sex, `s_year` = '$s_year', `about` = '$about' WHERE `user` = $_userID", "", 1);
						if ($upd)
						{
							echo '<meta http-equiv="refresh" content="2;/pa/profile">Данные изменены!';

							$_SESSION['sex'] = $sex;
						}
						else
							echo '<div class="error">Ошибка редактирования анкеты!</div>';
					}

					echo "</center></section>";
				}
				break;
				default:
				{
					$pid = (int) CURRENT_PARAM;

					if (empty($pid))
						$pid = (int) $_SESSION['id'];

					if ($pid == $_SESSION['id'])
						$_SiteTitle = "Мой профиль";
					else
					{
						$user_login = T_GetData("SELECT `login` FROM `users` WHERE `id` = $pid", "login");
						$_SiteTitle = "Профиль пользователя " . $user_login;
					}

					require("templates/" . T_Template() . "/layouts/header.php");

					$user     = T_GetData("SELECT `date`, `l_date`, `login`, `deleted`, `status` FROM `users` WHERE `id` = $pid");
					$user_sex = (int) T_GetData("SELECT `sex` FROM `users_profiles` WHERE `user` = $pid", "sex");

					$ava_path = "/templates/" . T_Template() . "/assets/img/avatars/";
					if ($user['status'] == 0)
						$ava_path .= "deactivated";
					else
					{
						switch($user_sex)
						{
							case 0: $ava_path .= "default";
								break;
							case 1: $ava_path .= "default_m";
								break;
							case 2: $ava_path .= "default_f";
								break;
						}
					}
					$ava_path .= ".png";

					$reg_date  = T_DateFormat($user['date'], $site_df);
					$last_date = T_DateFormat($user['l_date'], $site_df);

					$roles = T_GetData("
						SELECT `roles_list`.`role`, `roles`.`name`
						FROM `roles_list`
						LEFT JOIN `roles` ON `roles`.`id` = `roles_list`.`role`
						WHERE `roles_list`.`user` = $pid
						ORDER BY `roles_list`.`role` DESC", "", 1);

					$roles_list = "";
					foreach ($roles as $role)
					{
						/*$sel = "";
						if ($role['role'] == 1)
						{
							$sel = " selected";
						}*/

						$roles_list .= '<option value="' . $role['role'] . '">' . $role['name'] . '</option>';
					}

					$profile = T_GetData("SELECT * FROM `users_profiles` WHERE `user` = $pid");

					$profile_lastname   = $profile['lastname'];
					$profile_name       = $profile['name'];
					$profile_patronymic = $profile['patronymic'];
					$profile_city       = $profile['city'];
					$profile_about      = $profile['about'];
					$profile_birthday   = $profile['birthday'];
					$profile_sex        = $profile['sex'];
					$profile_s_year     = $profile['s_year'];

					switch($profile_sex)
					{
						case 1: $profile_sex = "мужской";
							break;
						case 2: $profile_sex = "женский";
							break;
						default: $profile_sex = "не задан";
							break;
					}

					$profile_birthday   = T_DateFormat($profile_birthday, 4, $profile_s_year);

					$_profile_lastname = !empty($profile_lastname) ? "<strong>Фамилия: </strong> " . $profile_lastname . "<br>" : "";
					$_profile_name = !empty($profile_name) ? "<strong>Имя: </strong> " . $profile_name . "<br>" : "";
					$_profile_patronymic = !empty($profile_patronymic) ? "<strong>Отчество: </strong> " . $profile_patronymic . "<br>" : "";
					$_profile_city = !empty($profile_city) ? "<strong>Город: </strong> " . $profile_city . "<br>" : "";
					$_profile_about = !empty($profile_about) ? "<strong>О себе: </strong> " . $profile_about . "<br>" : "";
					$_profile_birthday = !empty($profile_birthday) ? "<strong>Дата рождения: </strong> " . $profile_birthday . "<br>" : "";
					$_profile_sex = "<strong>Пол: </strong> " . $profile_sex . "<br>";

			PRINT <<<HERE
<section class="wrapper">
        <div class="white-container">
            <h1><img src="/templates/agrofresh/assets/icons/icon_for_title.svg" class="icon-for-title" alt="MTS_">$_SiteTitle</h1>
<div id="pa-content">
	<div id="profile-left">
		<img src="$ava_path" alt="Аватар"><br>
		Дата регистрации: $reg_date<br>
		Последний визит: $last_date<br>
		<select class="def-input">
			$roles_list
		</select>
	</div>
	<div id="profile-content">
		$_profile_lastname
		$_profile_name
		$_profile_patronymic
		$_profile_city
		$_profile_sex
		$_profile_birthday
		$_profile_about
	</div>
HERE;

			if ($pid == $_SESSION['id'])
			{
				PRINT <<<HERE
	<div id="profile-actions"><a href="/pa/profile/edit" title="Редактировать"><img src="/templates/agrofresh/assets/icons/icon_edit.svg"></a></div>
HERE;
			}

			PRINT <<<HERE
	</div>
</div>
</div>
</section>
HERE;
				}
				break;
			}
		}
		break;
		case "avatar1":
			{
				switch (CURRENT_PARAM)
				{
					case "load":
					{
						$_SiteTitle = "Загрузка аватара";
						require("templates/" . T_Template() . "/layouts/header.php");

						$upload_base = 'files/';
						$upload_ava  = 'avatars/';
						$upload = $upload_base . $upload_ava;

						ClearStatCache();

						if (!is_dir($upload_base))
							mkdir($upload_base, 0755);

						if (!is_dir($upload))
							mkdir($upload, 0755);

						$files = $_FILES;

						foreach ($files as $file)
						{
							$format        = MB_StrToLower(end(explode(".", $file['name'])), "UTF-8");

							$new_file_name = T_RSGen(64) . "." . $format;

							$fsize    = $file['size'];
							$currfile = $file['tmp_name'];
							$fhash    = hash_file("sha512", $currfile);

							if ($format != "jpg" && $format != "png" && $format != "gif")
							{
								echo "Некорректное расширение файла!";
								return;
							}

							$mime = MIME_Content_Type($currfile);

							if ($mime != "image/png" && $mime != "image/gif" && $mime != "image/jpeg")
							{
								echo "Смена расширения не сделает этот файл картинкой!";
								return;
							}

							if ($fsize <= 10485760) //Если файл меньше или равен 10 МБ - продолжаем
							{
								$q = T_GetData("SELECT `id`, `user` FROM `users_avatars` WHERE `hash` = '$fhash'");

								if (!empty($q))
								{
									$q_id   = (int) $q['id'];
									$q_user = (int) $q['user'];

									if ($q_user == $_SESSION['id'])
									{
										T_GetData("UPDATE `users_avatars` SET `is_active` = 0 WHERE `user` = $q_user", "", 1);

										T_GetData("UPDATE `users_avatars` SET `is_active` = 1 WHERE `id` = $q_id", "", 1);
									}
									else
									{
										$q = T_GetData("SELECT * FROM `users_avatars` WHERE `id` = $q_id");
									}
								}
								else
								{
									
								}

								while (file_exists($uploaddir . $new_file_name))
								{
									$new_file_name = T_RSGen(64) . "." . $format;
								}

								/*if (!empty($q['file_name']))
								{
									unlink($uploaddir . $q['file_name']);
								}*/
							}
							else
								echo "Файл слишком большой! Максимальный размер - 10 МБ.";
						}
					}
					break;
					default:
					{
						$pid = (int) CURRENT_PARAM;

						if (empty($pid))
							$pid = (int) $_SESSION['id'];

						if ($pid == $_SESSION['id'])
							$_SiteTitle = "Просмотр аватаров";
						else
						{
							$user_login = T_GetData("SELECT `login` FROM `users` WHERE `id` = $pid", "login");
							$_SiteTitle = "Просмотр аватаров пользователя " . $user_login;
						}
						require("templates/" . T_Template() . "/layouts/header.php");

						$ava_path = "";

						$def_or_cust = T_GetData("SELECT `id`, `is_active` FROM `users_avatars` WHERE `user` = $pid AND `is_active` = 1");

						if (empty($def_or_cust))
						{
							$sex = T_GetData("SELECT `sex` FROM `users_profiles` WHERE `user` = $pid", "sex");

							$ava_path = "/templates/" . T_Template() . "/assets/img/avatars/";

							switch($sex)
							{
								case 1: $ava_path .= "default_m.png";
									break;
								case 2: $ava_path .= "default_f.png";
									break;
								default: $ava_path .= "default.png";
									break;
							}
						}
						else
						{
							$ava_path = "/api/avatar/get/" . $def_or_cust['id'];
						}

						PRINT <<<HERE
	<div id="ava-left"></div><div id="ava"><img src="$ava_path" alt="Аватар"></div><div id="ava-right"></div>
HERE;

						if ($pid == $_SESSION['id'])
						{
							if (!empty($def_or_cust))
							{
								$aid = $def_or_cust['id'];

								echo '<div id="ava-actions">';

								if ($def_or_cust['is_active'] != 1)
									echo "<a href=\"/pa/avatar/$pid/main/$aid\">Сделать главным</a>";

								PRINT <<<HERE
						<a href="/pa/avatar/$pid/remove/$aid">Удалить</a>
						</div><br>
HERE;
							}

							PRINT <<<HERE
						<div id="ava-upload">
							<form action="/pa/avatar/load" method="POST" enctype="multipart/form-data">
								<input type="file" name="ava" multiple accept=".png, .jpg, .gif">
								<button name="avatar" type="submit">Загрузить</button>
							</form>
						</div>
HERE;
						}
					}
				}
			}
			break;
		case "account":
		{
			switch(CURRENT_PARAM)
			{
				case "security":
				{
					$_userID = (int) $_SESSION['id'];

					$_SiteTitle = "Безопасность";
					require("templates/" . T_Template() . "/layouts/header.php");

					$data = T_GetData("SELECT `login`, `email`, `pass` FROM `users` WHERE `id` = $_userID");
					$pass = $data['pass'];

					if (!isset($_POST['save']))
					{
						$login = $data['login'];
						$email = $data['email'];
					}
					else
					{
						$login  = T_SafeText(Trim($_POST['login']));
						$email  = T_SafeText(Trim($_POST['email']));
						$c_pass = T_SafeText(Trim($_POST['current_pass']));
						$n_pass = T_SafeText(Trim($_POST['new_pass']));
						$r_pass = T_SafeText(Trim($_POST['repeat_pass']));
					}

					PRINT <<<HERE
<section class="wrapper">
        <div class="white-container">
            <h1><img src="/templates/agrofresh/assets/icons/icon_for_title.svg" class="icon-for-title" alt="MTS_">$_SiteTitle</h1>
			<center>
<div id="pa-content">
	Вы можете в любой момент изменить свой логин, адрес электронной почты или пароль.<br>
	Если не хотите менять пароль - оставьте поля "Новый пароль" и "Повтор пароля" пустыми.<br><br>
	<form name="edit" method="POST">
		<table width="70%" align="center">
			<tr>
				<td width="30%">
					Логин:
				</td>
				<td width="70%">
					<input class="def-input" type="text" name="login" minlength="1" maxlength="100" placeholder="Введите логин..." value="$login" required>
				</td>
			</tr>
			<tr>
				<td width="30%">
					E-Mail:
				</td>
				<td width="70%">
					<input class="def-input" type="email" name="email" minlength="6" maxlength="100" placeholder="Введите E-Mail..." value="$email" required>
				</td>
			</tr>
			<tr>
				<td width="30%">
					Текущий пароль:
				</td>
				<td width="70%">
					<input class="def-input" type="password" name="current_pass" minlength="10" maxlength="100" placeholder="Введите текущий пароль..." required>
				</td>
			</tr>
			<tr>
				<td width="30%">
					Новый пароль:
				</td>
				<td width="70%">
					<input class="def-input" type="password" name="new_pass" minlength="10" maxlength="100" placeholder="Введите новый пароль...">
				</td>
			</tr>
			<tr>
				<td width="30%">
					Повтор пароля:
				</td>
				<td width="70%">
					<input class="def-input" type="password" name="repeat_pass" minlength="10" maxlength="100" placeholder="Повторите новый пароль...">
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input name="save" type="submit" value="Сохранить">
				</td>
			</tr>
		</table>
	</form>
HERE;

					if (isset($_POST['save']))
					{
						if (password_verify($c_pass, $pass))
						{
							if (empty($login) || MB_StrLen($login, "UTF-8") > 100)
							{
								echo '<strong><font color="red">Некорректно заполнен логин!</font></strong>';
								return;
							}

							if (empty($email) || (MB_StrLen($email, "UTF-8") < 6 || MB_StrLen($email, "UTF-8") > 100))
							{
								echo '<strong><font color="red">Некорректно заполнен E-Mail!</font></strong>';
								return;
							}

							if (empty($c_pass) || (MB_StrLen($c_pass, "UTF-8") < 10 || MB_StrLen($c_pass, "UTF-8") > 100))
							{
								echo '<strong><font color="red">Некорректный пароль!</font></strong>';
								return;
							}

							if (empty($n_pass) && empty($r_pass))
							{
								$q = T_GetData("UPDATE `users` SET `login` = '$login', `email` = '$email' WHERE `id` = $_userID", "", 1);

								if ($q)
								{
									$_SESSION['login'] = $login;

									echo '<meta http-equiv="refresh" content="3;/pa/account/security"><strong>Данные изменены.</strong>';
								}
								else
									echo '<strong><font color="red">Ошибка сохранения данных!</font></strong>';
							}
							else
							{
								if (empty($n_pass) || (MB_StrLen($n_pass, "UTF-8") < 10 || MB_StrLen($n_pass, "UTF-8") > 100))
								{
									echo '<strong><font color="red">Некорректный новый пароль!</font></strong>';
									return;
								}

								if (empty($r_pass) || (MB_StrLen($r_pass, "UTF-8") < 10 || MB_StrLen($r_pass, "UTF-8") > 100))
								{
									echo '<strong><font color="red">Некорректный повтор пароля!</font></strong>';
									return;
								}

								if ($n_pass == $r_pass)
								{
									if (!password_verify($n_pass, $pass))
									{
										$new_pass = password_hash($n_pass, PASSWORD_BCRYPT);

										$q = T_GetData("UPDATE `users` SET `login` = '$login', `email` = '$email', `pass` = '$new_pass' WHERE `id` = $_userID", "", 1);

										if ($q)
										{
											$_SESSION['login'] = $login;
											$_SESSION['pass']  = $new_pass;

											echo '<meta http-equiv="refresh" content="3;/pa/account/security"><strong>Данные изменены.</strong>';
										}
										else
											echo '<strong><font color="red">Ошибка сохранения данных!</font></strong>';
									}
									else
										echo '<strong><font color="red">Новый пароль не может совпадать с текущим!</font></strong>';
								}
								else
									echo '<strong><font color="red">Введённые пароли не совпадают!</font></strong>';
							}
						}
						else
							echo '<strong><font color="red">Текущий пароль введён неверно.</font></strong>';
					}

					echo '</div></center></div></section>';
				}
				break;
				case "sessions":
				{
					$_SiteTitle = "Сеансы";
					require("templates/" . T_Template() . "/layouts/header.php");

					PRINT <<<HERE
<div id="pa-content">
	Здесь выводятся все активные сеансы для данной учётной записи. В случае обнаружения подозрительной активности Вы можете завершить любой из них. Возможно, после этого Вам придётся заново авторизоваться.
	<br><br>
	<div class="center"><strong>Сайт</strong></div>
	<br>
	<table width="90%" align="center" class="table">
		<tr>
			<th align="center" width="5%">
				Система
			</th>
			<th align="center" width="15%">
				IP-адрес
			</th>
			<th align="center" width="15%">
				Дата
			</th>
			<th align="center" width="15%">
				Браузер
			</th>
			<th align="center" width="45%">
				User-Agent
			</th>
			<th width="5%">
				
			</th>
		</tr>
		<tr>
			<td align="center" colspan="6">
				Нет активных сеансов.
			</td>
		</tr>
	</table>
	<br>
	<div class="center"><strong>Клиенты</strong></div>
	<br>
	<table width="90%" align="center" class="table">
		<tr>
			<th align="center" width="5%">
				Система
			</th>
			<th align="center" width="15%">
				IP-адрес
			</th>
			<th align="center" width="15%">
				Дата
			</th>
			<th align="center" width="15%">
				Клиент
			</th>
			<th align="center" width="45%">
				User-Agent
			</th>
			<th width="5%">
				
			</th>
		</tr>
		<tr>
			<td align="center" colspan="6">
				Нет активных сеансов.
			</td>
		</tr>
	</table>
</div>
HERE;
				}
				break;
				default:
				{
					T_Error(404, "Такого раздела или файла не существует!");
				}
				break;
			}
		}
		break;
		default:
		{
			$_SiteTitle = "Личный кабинет";
			require("templates/" . T_Template() . "/layouts/header.php");

			PRINT <<<HERE
<div id="pa-content">
	Добро пожаловать в Личный кабинет, $_SESSION[login]!
</div>
HERE;
		}
		break;
	}