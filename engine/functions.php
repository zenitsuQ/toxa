<?php
/*
	* Файл functions.php
	* Team-Tech Web Engine 1.4 beta
	* Префикс функций движка: T_
	* Приватный билд
	* Автор: Кизилов Владимир Владимирович
	* Team-Tech.ru
*/
	define("ENGINE", "Team-Tech Web Engine");
	define("ENGINE_SITE", "Team-Tech.ru");
	define("ENGINE_AUTHOR", "Kizilov Vladimir");
	define("ENGINE_VERSION", "1.4 beta");

	header("X-Powered-By: " . ENGINE);

	require_once("db.php");													//Подключение конфигурации
	require_once("recaptcha.php");											//Подключение Google-капчи

	$_userID    = (int) $_SESSION['id'];									//Выносим переменные сессии для удобного обращения к ним
	$_userLogin = $_SESSION['login'];
	$_userRole  = $_SESSION['role'];

	$site_df   	= T_GetData("SELECT `date_format` FROM `settings`", "date_format");  //Получаем формат даты
	$site_md	= T_GetData("SELECT `site_name`, `site_delimiter` FROM `settings`"); //Получаем имя сайта и разделитель заголовка
	$site_td    = $site_md['site_delimiter'];
	$site_name  = $site_md['site_name'];
	unset($site_md);

	define("SITE_HOST", $_SERVER['HTTP_HOST']);
	define("SITE_NAME", $site_name);

/*
	* Работа с графическим отображением оценок в отзывах
	*
	* @param float $mark - Оценка от 0 до шкалы
	* @param int $scale  - Шкала оценки. По умолчанию пятибалльная
*/
function T_MarkOut($mark, int $scale = 5)
{
	if ($mark < 0)
		return "WTF?!";

	if ($mark > $scale)
		return "WAT?!";

	$result     = "";

	$mark_floor = floor($mark);			// В меньшую сторону
	$mark_round = round($mark, 0);		// Арифметически
	$mark_empty = $scale - $mark_floor;	// Количество пустых

	// Выводим целые
	for ($i = 0; $i < $mark_floor; $i++)
		$result .= '<img class="feedback-mark" src="/templates/' . T_Template() . '/assets/img/star_full.png" alt="Оценка">';

	// Выводим половинку
	if ($mark_round != $mark_floor)
	{
		$result .= '<img class="feedback-mark" src="/templates/' . T_Template() . '/assets/img/star_half.png" alt="Оценка">';
		$mark_empty--;
	}

	// Выводим пустые
	if ($mark_floor < $scale)
		for ($i = 0; $i < $mark_empty; $i++)
			$result .= '<img class="feedback-mark" src="/templates/' . T_Template() . '/assets/img/star_empty.png" alt="Оценка">';

	return $result;
}

/*
	* Работа с иллюстрациями к страницам и новостям
	*
	* @param string $text - Текст объекта
	* @param int $type    - Тип объекта: 1 - страница, 2 - новость
	* @param int $max     - Максимальный размер изображения по одной из сторон в пикселях
*/
function T_PicWorks($text, int $type, int $max)
{
	$p_text  = $text;	// Вводим переменную для парсинга
	$section = "";		// Переменная для типа объекта

	switch ($type)
	{
		case 1: $section = "pages"; break;
		case 2: $section = "news";  break;
		default:
			return "ERR_INCORRECT_OBJECT";
			break;
	}

	// Парсинг картинок в тегах [photo]
	while (MB_StrStr($p_text, '[photo]'))
	{
		$p_id     = MB_SubStr($p_text, MB_StrPos($p_text, '[photo]') + 7, 20, 'UTF-8');
		$p_id     = (int) MB_SubStr($p_id, 0, MB_StrPos($p_id, '[/photo]'), 'UTF-8');	// Получаем ID иллюстрации
		$r_str    = '[photo]' . $p_id . '[/photo]';										// Строка для перезаписи

		$pic      = T_GetData("SELECT `file_name`, `hash`, `height`, `width`, `description` FROM `" . $section . "_images` WHERE `id` = $p_id");

		$pic_h    = (int) $pic['height'];	// Высота
		$pic_w    = (int) $pic['width'];	// Ширина
		$pic_name = $pic['file_name'];		// Имя
		$pic_hash = $pic['hash'];			// Хэш
		$pic_desc = $pic['description'];	// Описание

		if (empty($pic_desc))
			$pic_desc = "Иллюстрация №$p_id";

		$c_s_name = StrToLower(MB_SubStr($pic_name, 0, 1, 'UTF-8'));
		$c_s_hash = StrToLower(MB_SubStr($pic_hash, 0, 2, 'UTF-8'));

		$pic_url  = "/files/$section/$c_s_name/$c_s_hash/$pic_name";		// Путь

		$n_str = T_PicAutoScale($pic_h, $pic_w, $max, $pic_url, $pic_desc);	// Новая строка

		$text    = Str_Replace($r_str, $n_str, $text);

		$p_text  = MB_SubStr($p_text, MB_StrPos($p_text, '[photo]') + 7, 17000000, 'UTF-8');
	}

	return $text;
}

/*
	* Функция, автоматически масштабирующая самую большую сторону картинки до необходимого размера
	*
	* @param int $h - Высота
	* @param int $w - Ширина
	* @param int $max     - Максимальный размер изображения по одной из сторон в пикселях
	* @param string $path - Путь до картинки
	* @param string $alt  - Альтернативная подпись для картинки
*/
function T_PicAutoScale(int $h, int $w, int $max, $path, $alt = "Иллюстрация")
{
	$result = "";

	//list($width, $height) = GetImageSize($path); // Получить размеры картинки из файла

	if (($w > $h || $w == $h) && $w > $max)
		$result = '<div class="news-picture"><img src="' . $path . '" alt="' . $alt . '" width="' . $max . 'px" onclick="window.open(\'' . $path . '\', \'_blank\')" title="Посмотреть в полном размере"></div>';

	if ($h > $w && $h > $max)
		$result = '<div class="news-picture"><img src="' . $path . '" alt="' . $alt . '" height="' . $max . 'px" onclick="window.open(\'' . $path . '\', \'_blank\')" title="Посмотреть в полном размере"></div>';

	if ($h < $max && $w < $max)
		$result = '<div class="news-picture"><img src="' . $path . '" alt="' . $alt . '" onclick="window.open(\'' . $path . '\', \'_blank\')" title="Посмотреть в полном размере"></div>';

	return $result;
}

/*
	* Функция отправки электронного сообщения
	*
	* @param string $arrdess - кому отправлять
	* @param string $title   - заголовок сообщения
	* @param string $text    - текст сообщения
	* @param int $type       - тип сообщения: 0 - автоматическое, 1 - сформированное вручную
*/
function T_SendMail($address, $title, $text, $type = 0)
{
	$SiteName   = T_GetData("SELECT `site_name` FROM `settings`", "site_name");//SITE_NAME;
	$SiteName   = MB_Encode_MIMEHeader($SiteName, "UTF-8", "B");
	$SiteEmail  = "noreply@" . $_SERVER['HTTP_HOST'];

	// Кодируем текст в MIME-формат с помощью Base64 (B) / Quoted-Printable (Q)
	$MailFrom   = "$SiteName <$SiteEmail>";//MB_Encode_MIMEHeader("$SiteName <$SiteEmail>", "UTF-8", "B");//"$SiteName <$SiteEmail>";//
	$MailTitle  = MB_Encode_MIMEHeader($title, "UTF-8", "B");
	$MailText   = "";

	$MailHeaders = "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\nFrom: $MailFrom\r\nX-Mailer: " . ENGINE . "\r\n";

	if ($type == 0)
		$MailText = "$text<br><hr><em>Это сообщение отправлено автоматически. Отвечать на него не нужно.</em>";
	else
		$MailText = $text;

	$result = Mail($address, $MailTitle, Base64_Encode($MailText), $MailHeaders, "-f$SiteEmail");

	return $result;
}
/*function T_SendMail($address, $title, $text, $type = 0)
{
	$SiteName   = SITE_NAME;
	$SiteEmail  = "noreply@" . SITE_HOST;

	// Кодируем текст в MIME-формат с помощью Base64 (B) / Quoted-Printable (Q)
	$MailFrom   = MB_Encode_MIMEHeader("$SiteName <$SiteEmail>", "UTF-8", "B");
	$MailTitle  = MB_Encode_MIMEHeader($title, "UTF-8", "B");
	$MailText   = "";

	$MailHeaders = "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\nFrom: $MailFrom\r\nX-Mailer: " . ENGINE . "\r\n";

	if ($type == 0)
		$MailText = "$text<br><hr><em>Это сообщение отправлено автоматически. Отвечать на него не нужно.</em>";
	else
		$MailText = $text;

	$result = Mail($address, $MailTitle, Base64_Encode($MailText), $MailHeaders, "-f$SiteEmail");

	return $result;
}*/

// Ограничение авторизаций
function T_AuthValidate()
{
	$result = false;

	$ip  = $_SERVER['REMOTE_ADDR'];
	$u_a = SafeText(Trim($_SERVER['HTTP_USER_AGENT']));

	$end_date = date('Y-m-d H:i:s', StrToTime('-10 minutes'));
	$q = DB("
			SELECT `id`, `try_count` 
			FROM `attempts` 
			WHERE ((`date` BETWEEN '$end_date' AND NOW()) OR (`l_date` BETWEEN '$end_date' AND NOW())) AND `ip` = '$ip' AND `u_agent` = '$u_a'");

	if (empty($q))
	{
		$q = DB("INSERT INTO `attempts` (`date`, `ip`, `u_agent`) VALUES (NOW(), '$ip', '$u_a')", "", 1);
		if (!$q)
			$result = false;
		else
			$result = true;
	}
	else
	{
		$id  = (int) $q['id'];
		$cnt = (int) $q['try_count'];

		if ($cnt == 3)
		{
			DB("UPDATE `attempts` SET `l_date` = NOW() WHERE `id` = $id", "", 1);
			$result = false;
		}
		else
		{
			DB("UPDATE `attempts` SET `l_date` = NOW(), `try_count` = `try_count` + 1 WHERE `id` = $id", "", 1);
			$result = true;
		}
	}

	return $result;
}

//================== СЕССИИ ==================

/*
	* Проверка сессии на существование в БД и актуальность, если существует
	*
	* @param string $sid - SID сессии из Cookies
*/
function T_CheckSession($sid)
{
	$sid    = T_SafeText($sid);
	$result = 0;

	$q = T_GetData("SELECT `id`, `is_active` FROM `users_sessions` WHERE `sid` = '$sid'", "", 1);

	if (mysqli_num_rows($q) == 0)		//Если сессии не существует - создаём её в БД
	{
		if (T_NewSession())
			$result = 2;				//Успех
		else
			$result = 3;				//Не добавилось в БД
	}
	else
	{
		$q = mysqli_fetch_assoc($q);	//Если существует - проверяем на активность

		$id = (int) $q['id'];
		$st = (int) $q['is_active'];

		if ($st == 0)					//Если статус 0 (неактивна) - убиваем сессию у юзверя и сносим печеньку
		{
			session_unset();
			session_destroy();

			SetCookie(session_name());

			$result = 0;
		}
		else
			$result = 1;				//Иначе - пускаем юзверя
	}

	return $result;
}

/*
	* Получить ID сессии в БД по SID
	*
	* @param string $sid - SID сессии из Cookies
*/
function T_GetSessionID($sid)
{
	$id  = 0;
	$sid = T_SafeText($sid);

	$q = T_GetData("SELECT `id` FROM `users_sessions` WHERE `sid` = '$sid'", "id");

	if (!empty($q))
		$id = (int) $q;

	if (empty($id))
		$id = 0;

	return $id;
}

/*
	* Создать новую сессию в БД
*/
function T_NewSession()
{
	$usr_a  = $_SERVER['HTTP_USER_AGENT'];
	$usr_ip = $_SERVER['REMOTE_ADDR'];
	$sid    = session_id();

	$id     = $_SESSION['id'];
	$sid    = T_SafeText($sid);

	$q = T_GetData("INSERT INTO `users_sessions` (`date`, `user`, `ip`, `sid`, `user_agent`) VALUES (NOW(), $id, '$usr_ip', '$sid', '$usr_a')", "", 1);

	if (!$q)
		return false;

	return true;
}

/*
	* Деактивировать сессию в БД
	*
	* @param int $id - ID сессии в БД
*/
function T_DropSession(int $id)
{
	$q = T_GetData("UPDATE `users_sessions` SET `date_end` = NOW(), `is_active` = 0 WHERE `id` = $id", "", 1);

	if ($q)
		return true;
	else
		return false;
}

//=============================================

/*
	* Функция получения текущего шаблона сайта
*/
function T_Template()
{
	$template      = (int) T_GetData("SELECT `site_template` FROM `settings`", "site_template");
	$template_name = T_GetData("SELECT `pointer` FROM `templates` WHERE `id` = $template", "pointer");
	
	return $template_name;
}

/*
	* Функция автоматического удаления черновиков
*/
function T_RemoveDrafts()
{
	$r_date = date('Y-m-d H:i:s', StrToTime('-1 days'));

	$drafts = T_GetData("SELECT `id` FROM `pm` WHERE `datetime` < '$r_date' AND `name` = 'Заголовок' AND `comment` IS NULL", "", 1);

	if (!empty($drafts))
	{
		foreach ($drafts as $draft)
		{
			$d_id = (int) $draft['id'];
			T_GetData("DELETE FROM `pm` WHERE `id` = $d_id", "", 1);
		}

		return true;
	}
	else
		return true;
}

/*
	* Функция форматирования вида версий
	*
	* @param int $version - Версия вида xxxxxxxx
*/
function T_VerFormat($version)
{
	if (MB_StrLen($version, "UTF-8") == 7)
	{
		$version = "0" . $version;
	}

	sscanf($version, "%2d%2d%2d%2d", $major, $mid, $minor, $build);

	$result = $major . "." . $mid . "." . $minor . "." . $build;

	return $result;
}

/*
	* Функция получения и/или генерации токенов
	*
	* @param string $ip      - IP
	* @param int $userID     - ID пользователя
	* @param int $c_type     - тип клиента (PC / Android / Прочее)
	* @param string $client  - имя клиента
	* @param string $u_agent - User-Agent
*/
function T_GetToken(string $ip, int $userID, int $c_type, string $client, string $u_agent)
{
	$token = T_GetData("
			SELECT `token`
			FROM `users_tokens`
			WHERE `ip` = '$ip' AND `user` = $userID AND `client` = '$client' AND `client_type` = $c_type AND `useragent` = '$u_agent' AND `status` = 1
			ORDER BY `id` DESC
			LIMIT 1", "token");

	if (empty($token))
	{
		$token = StrRev(hash("sha256", T_PGen(50)));
		$ftoken = StrRev(hash("sha256", T_PGen(50)));

		while (!empty(T_GetData("SELECT `id` FROM `users_tokens` WHERE `token` = '$token' OR `f_token` = '$ftoken'", "id")))
		{
			$token = StrRev(hash("sha256", T_PGen(50)));
			$ftoken = StrRev(hash("sha256", T_PGen(50)));
		}

		T_GetData("
				INSERT INTO `users_tokens`
				(`ip`, `date`, `user`, `client`, `client_type`, `useragent`, `token`, `f_token`)
				VALUES
				('$ip', NOW(), $userID, '$client', $c_type, '$u_agent', '$token', '$ftoken')", "", 1);

		T_GetData("
				INSERT INTO `users_log`
				(`ip`, `date`, `user`, `auth_type`)
				VALUES
				('$ip', NOW(), $userID, 'API Token')", "", 1);
	}

	return $token;
}

//Формирование семантического адреса
function T_Semantic(string $text)
{
	$text = Trim(preg_replace('/[^a-zA-Zа-яА-ЯёЁ0-9 -]/ui', '', $text)); //,.-
	$text = Str_Replace("  ", " ", $text);
	$text = Str_Replace(" - ", " ", $text);
	$text = Str_Replace(" ", "-", $text);	// _
	$text = Str_Replace("--", "-", $text);
	$text = MB_StrToLower($text, "UTF-8");
	$text = T_Translit($text);

	return $text;
}

//Проверка авторизации
function T_Authorized()
{
	if (!empty($_SESSION['id']) && !empty($_SESSION['pass']))
		return true;
	else
		return false;
}

//Получить ID пользователя
function T_UserID()
{
	return $_SESSION['id'];
}

//Проверка прав
function T_Admin()
{
	$uid = isset($_SESSION['id']) ? $_SESSION['id'] : 0;

	if (empty($uid))
		return false;

	$q = T_GetData("SELECT `id` FROM `roles_list` WHERE `user` = $uid AND `role` = 3", "id");

	if (!empty($q))
		return true;
	else
		return false;
}

//Текущая страница
function T_CurrentPage($page_name)
{
	if ($page_name == CURRENT_PAGE)
		return "menu_current";
	else
		return "menu_item";
}

//Текущий раздел
function T_CurrentSection($_name)
{
	$result = "";

	if ($_name == CURRENT_SECTION)
	{
		$result = "current-menu-item ";
	}
	else
		$result = "";

	if ($_name == "pages")
	{
		if (CURRENT_PARAM == "1-kontakty")
			$result = "current-menu-item ";
		else
			$result = "";
	}

	return $result;
}

//Проверка разрешён ли доступ к сайту
function T_SiteActive()
{
	$result = (bool) T_GetData("SELECT `site_enabled` FROM `settings`", "site_enabled");

	return $result;
}

// Парсер смайлов
function T_Smiles($text)
{
	$smiles = array( 
		':)' => "<img src='/templates/" . T_Template() . "/assets/img/smiles/0.png' alt=':)'>",
		':]' => "<img src='/templates/" . T_Template() . "/assets/img/smiles/1.png' alt=':]'>",
		';)' => "<img src='/templates/" . T_Template() . "/assets/img/smiles/2.png' alt=';)'>",
		':D' => "<img src='/templates/" . T_Template() . "/assets/img/smiles/3.png' alt=':D'>",
		':p' => "<img src='/templates/" . T_Template() . "/assets/img/smiles/4.png' alt=':p'>",
		'O:}' => "<img src='/templates/" . T_Template() . "/assets/img/smiles/5.png' alt='O:}'>",
		':(' => "<img src='/templates/" . T_Template() . "/assets/img/smiles/6.png' alt=':('>",
		':o' => "<img src='/templates/" . T_Template() . "/assets/img/smiles/7.png' alt=':o'>",
		':|' => "<img src='/templates/" . T_Template() . "/assets/img/smiles/8.png' alt=':|'>",
		':-/' => "<img src='/templates/" . T_Template() . "/assets/img/smiles/9.png' alt=':-/'>",
		':\\' => "<img src='/templates/" . T_Template() . "/assets/img/smiles/10.png' alt=':\\'>",
		'o_O' => "<img src='/templates/" . T_Template() . "/assets/img/smiles/11.png' alt='o_O'>",
		':[' => "<img src='/templates/" . T_Template() . "/assets/img/smiles/12.png' alt=':['>",
		'X(' => "<img src='/templates/" . T_Template() . "/assets/img/smiles/13.png' alt='X('>",
		']:->;' => "<img src='/templates/" . T_Template() . "/assets/img/smiles/14.png' alt=']:->'>");

	$text = StrTr($text, $smiles);
	return Str_Replace("\\", "", $text);
}

// Зачистка смайлов
function T_ClearSmiles($text)
{
	$smiles = array( 
		':)' => "",
		':]' => "",
		';)' => "",
		':D' => "",
		':p' => "",
		'O:}' => "",
		':(' => "",
		':o' => "",
		':|' => "",
		':-/' => "",
		':\\' => "",
		'o_O' => "",
		':[' => "",
		'X(' => "",
		']:->;' => "");

	$text = StrTr($text, $smiles);
	return Str_Replace("\\", "", $text);
}

/*
	* Функция, рекурсивно удаляющая директорию со всем её содержимым
	*
	* @param string $dir - Путь до директории
*/
function T_RemoveDir($dir)
{
	$c_dir = new RecursiveDirectoryIterator($dir);
	$files = new RecursiveIteratorIterator($c_dir, RecursiveIteratorIterator::CHILD_FIRST);

	foreach($files as $file)
	{
		if ($file->getFilename() === '.' || $file->getFilename() === '..')
			continue;

		if ($file->isDir())
			RmDir($file->getRealPath());
		else
			UnLink($file->getRealPath());
	}

	return RmDir($dir);
}

/*
	* Функция, превращающая русский текст в транслит
	*
	* @param string $text - Текст на русском
*/
function T_Translit($text)
{
	$rus = array(
		'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й',
		'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф',
		'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я',
		'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й',
		'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф',
		'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я',
		' ');

	$translit = array(
		'A', 'B', 'V', 'G', 'D', 'E', 'YO', 'ZH', 'Z', 'I', 'IY',
		'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F',
		'H', 'C', 'CH', 'SH', 'SHCH', '', 'Y', '', 'E', 'IU', 'IA',
		'a', 'b', 'v', 'g', 'd', 'e', 'yo', 'zh', 'z', 'i', 'iy',
		'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f',
		'h', 'c', 'ch', 'sh', 'shch', '', 'y', '', 'e', 'iu', 'ia',
		'_');

	return str_replace($rus, $translit, $text);
}

/*
	* Функция экспорта базы данных в файл (резервное копирование)
	* (!) Только для Debian Linux и MariaDB
*/
function T_BackupDB()
{
	require_once("db.php");
	$date = date("d.m.Y_His") . "_" . T_PGen(10);
	$path = "backups";

	if (!is_dir($path))
	{
		mkdir($path, 0755);

		$text = "Deny from All";
		$fp   = fopen("$path/.htaccess", "w");
		fwrite($fp, $text);
		fclose($fp);
	}

	return shell_exec("mysqldump -u $db_user -p$db_pass $db_name | gzip > /var/www/html/$path/db_$date.sql.gz");
}

/*
	* Функция, переворачивающая UTF-8 строку
	*
	* @param string $str - Исходная строка текста
*/
function T_StrRev($str)
{
    $rev = "";

    for ($i = MB_StrLen($str, "UTF-8"); $i >= 0; $i--)
	{
        $rev .= MB_SubStr($str, $i, 1, "UTF-8");
    }

    return $rev;
}

/*
	* Функция, возвращающая ответ в JSON-формате
	*
	* @param array $data  - массив данных
	* @param string $ver  - Версия API
*/
function T_JSONThrow($data, $ver)
{
	$result = array(
			"r"    => "ok",
			"data" => $data,
			"v"    => $ver);
	$result = json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); // | JSON_NUMERIC_CHECK
	die($result);

	return;
}

/*
	* Функция, возвращающая ошибки в JSON-формате
	*
	* @param string $text - Описание ошибки
	* @param int $code    - Код ошибки
	* @param string $ver  - Версия API
*/
function T_JSONError($text, int $code, string $ver)
{
	$err = array(
			"r"    => "ok",
			"data" => array(
							"error"      => $text,
							"error_code" => $code),
			"v"    => $ver);
	$err = json_encode($err);
	die($err);

	return;
}

/*
	* Функция, обрезающая текст до заданного количества символов
	*
	* @param string $text - Входные данные
	* @param int $length  - Желаемая длина текста на выходе
*/
function T_CutText(string $text, int $length)
{
	/*
		Так как данная функция подразумевается для использования в предпросмотре текста - вывод чистится от пробелов вокруг с помощью Trim()
	*/

	if (MB_StrLen($text, "UTF-8") > $length)
	{
		$result = Trim(MB_SubStr($text, 0, $length, "UTF-8"))."...";
	}
	else
		$result = Trim($text);

	return $result;
}

/*
	* Функция, выкидывающая ошибки по шаблону с генерацией соответствующего ответа сервера.
	* По умолчанию - ошибка 500.
	* Назначение: быстрое формирование вывода ошибок и прекращение выполнения скрипта.
	*
	* @param int $error   - Код ошибки
	* @param string $text - Текст ошибки
	* @param int $type    - Тип вывода. 0 - с HTML-разметкой; 1 - без разметки
*/
function T_Error(int $error = 500, string $text = "", int $type = 0)
{
	switch ($error)
	{
		case 200:
			$error_descr = "OK";
			break;
		case 301:
			$error_descr = "Moved Permanently";
			break;
		case 302:
			$error_descr = "Moved Temporarily";
			break;
		case 400:
			$error_descr = "Bad Request";
			break;
		case 401:
			$error_descr = "Unauthorized";
			break;
		case 403:
			$error_descr = "Forbidden";
			break;
		case 404:
			$error_descr = "Not Found";
			break;
		case 405:
			$error_descr = "Method Not Allowed";
			break;
		case 429:
			$error_descr = "Too Many Requests";
			break;
		case 502:
			$error_descr = "Bad Gateway";
			break;
		case 503:
			$error_descr = "Service Unavailable";
			break;
		case 504:
			$error_descr = "Gateway Timeout";
			break;
		default: {
			$error = 500;
			$error_descr = "Internal Server Error";
		}
	}

	$error_text  = $error." ".$error_descr;

	if (empty($text))
		$text = $error_text;

	if ($type == 0)
	{
		$die = '<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>'.$error_text.'</title>
		<style>
			a {
				color: blue;
				text-decoration: none;
			}
			a:hover {
				text-decoration: underline;
			}

			h1 {
				font-size: 36pt;
			}

			h2 {
				color: #FF0000;
				font-size: 26pt;
				font-weight: bold;
			}

			hr {
				color: blue;
				border: none;
				border-bottom: 1px solid #000;
				background: blue;
			}

			body {
				font-size: 14pt;
				text-align: center;
			}
		</style>
	</head>
	<body>
		<h1>'.$error_text.'</h1>
		<h2>
			'.$text.'
		</h2>
		<hr>
		Powered by <a href="https://Team-Tech.ru/webengine/" target="_blank">Team-Tech Web Engine</a>
	</body>
</html>';
	}
	else
		$die = $text;

	header("HTTP/1.1 ".$error_text);
	die($die);

	return;
}

/*
	* Парсер BB-кодов (безопасная альтернатива HTML-разметке)
	*
	* @param string $text - Текст для преобразования
*/
function T_BBParse($text)
{
	$bbcodes = array(
		"#\[b\](.+?)\[\/b\]#is", 
		"#\[u\](.+?)\[\/u\]#is",
		"#\[i\](.+?)\[\/i\]#is",
		"#\[s\](.+?)\[\/s\]#is",
		"#\[code\](.+?)\[\/code\]#is",
		"#\[big\](.+?)\[\/big\]#is",
		"#\[sub\](.+?)\[\/sub\]#is",
		"#\[sup\](.+?)\[\/sup\]#is",
		"#\[small\](.+?)\[\/small\]#is",
		"#\[center\](.+?)\[\/center\]#is",
		"#\[url=(.+?)\](.+?)\[\/url\]#is",
		"#\[url\](.+?)\[\/url\]#is",
		"#\[color=(.+?)\](.+?)\[\/color\]#is",
		"#\[img\](.+?)\[\/img\]#is",
		"#\[img=(.+?),(.+?)\](.+?)\[\/img\]#is",
		"#\%year%#is",
		"#\%today%#is",
		"#\%login%#is",
		"#\%username%#is");

	$bbrep = array(
		"<strong>\\1</strong>",
		"<ins>\\1</ins>",
		"<em>\\1</em>",
		"<del>\\1</del>",
		"<code>\\1</code>",
		"<big>\\1</big>",
		"<sub>\\1</sub>",
		"<sup>\\1</sup>",
		"<small>\\1</small>",
		"<center>\\1</center>",
		"<a href=\"\\1\" title=\"\\2\">\\2</a>",
		"<a href=\"\\1\">\\1</a>",
		"<span style=\"color: \\1\">\\2</span>",
		"<img src=\"\\1\" alt=\"Изображение\">",
		"<img src=\"\\3\" alt=\"Изображение\" width=\"\\1\" height=\"\\2\">",
		date("Y"),
		date("d.m.Y"),
		$_SESSION['login'],
		$_SESSION['firstname'] . ' ' . $_SESSION['middlename']);

	return preg_replace($bbcodes, $bbrep, $text);
}

/*
	* Удаляет BB-коды из текста
	*
	* @param string $text - Текст для преобразования
*/
function T_AntiBBParse($text)
{
	$bbcodes = array(
		"#\[b\](.+?)\[\/b\]#is", 
		"#\[u\](.+?)\[\/u\]#is",
		"#\[i\](.+?)\[\/i\]#is",
		"#\[s\](.+?)\[\/s\]#is",
		"#\[code\](.+?)\[\/code\]#is",
		"#\[big\](.+?)\[\/big\]#is",
		"#\[sub\](.+?)\[\/sub\]#is",
		"#\[sup\](.+?)\[\/sup\]#is",
		"#\[small\](.+?)\[\/small\]#is",
		"#\[center\](.+?)\[\/center\]#is",
		"#\[url=(.+?)\](.+?)\[\/url\]#is",
		"#\[url\](.+?)\[\/url\]#is",
		"#\[color=(.+?)\](.+?)\[\/color\]#is",
		"#\[img\](.+?)\[\/img\]#is",
		"#\[img=(.+?),(.+?)\](.+?)\[\/img\]#is",
		"#\%year%#is",
		"#\%today%#is",
		"#\%login%#is",
		"#\%username%#is");

	$bbrep = array(
		"\\1",
		"\\1",
		"\\1",
		"\\1",
		"\\1",
		"\\1",
		"\\1",
		"\\1",
		"\\1</small>",
		"\\1</center>",
		"\\2: \\1",
		"\\1",
		"\\2",
		"Изображение",
		"Изображение",
		date("Y"),
		date("d.m.Y"),
		$_SESSION['login'],
		$_SESSION['firstname'] . ' ' . $_SESSION['middlename']);

	return preg_replace($bbcodes, $bbrep, $text);
}

/*
	* Обработчик входного текста, защита от SQL-инъекций
	*
	* @param string $text - Текст для преобразования
*/
function T_SafeText($text)
{
	require("db.php");
	$result = HTMLSpecialChars($text, ENT_NOQUOTES, "UTF-8");
	$result = mysqli_real_escape_string($db, $result);

	return $result;
}

/*
	* Обработчик SQL-запросов
	*
	* @param string $query - Запрос
	* @param string $qvar  - Столбец для возвращения значения
	* @param int $qtype    - Тип запроса. Указывает, что возвращает функция. 0 - готовый массив; 1 - сырой ответ MySQL; 2 - количество строк в ответе на запрос
*/
function T_GetData(string $query, string $qvar = "", int $qtype = 0)
{
	require("db.php");

	switch($qtype)
	{
		case 0: { 										//Получить готовый массив данных
			if (!empty($qvar)) 							//Получить значение одного параметра из запроса
			{
				$result = mysqli_query($db, $query);

				if (!empty($result))
				{
					$result = mysqli_fetch_assoc($result);
					$result = $result[$qvar];
				}
				else
					$result = null;
			}
			else 										//Получить массив из запроса
			{
				$result = mysqli_query($db, $query);

				if (!empty($result))
				{
					$result = mysqli_fetch_assoc($result);
				}
				else
					$result = null;
			}

			mysqli_close($db);
		}
		break;
		case 1: { 										//Получить сырые данные
			$result = mysqli_query($db, $query);
			mysqli_close($db);
		}
		break;
		case 2: { 										//Получить количество строк
			$result = mysqli_query($db, $query);

			if (!empty($result))
			{
				$result = mysqli_num_rows($result);
			}
			else
				$result = 0;

			mysqli_close($db);
		}
		break;
		default:
			$result = "Неверное обращение к функции!";
	}
	return $result;
}

/*
	* Форматирование величин информации
	*
	* @param int $data - размер файла в байтах
*/
function T_FormatFileSize($data)
{
	// Байты
	if ($data < 1024 )
	{
		$sw = SubStr($data, -1);

		$end = "";

		if (($sw == 2 || $sw == 3 || $sw == 4) && $data < 100)
			$end = "а";

		return $data . " Байт" . $end;
	}
	// Килобайты
	else if ($data < 1048576)
	{
		return round(($data / 1024), 2) . " КБ";
	}
	// Мегабайты
	else if ($data < 1073741824)
	{
		return round(($data / 1048576), 2) . " МБ";
	}
	// Гигабайты
	else if ($data < 1099511627776)
	{
		return round(($data / 1073741824), 2) . " ГБ";
	}
	// Терабайты
	else
	{
		return round(($data / 1099511627776), 2) . " ТБ";
	}
}

/*
	* Форматирование даты
	*
	* @param string $date_f - Дата в формате базы данных
	* @param int $date_t    - Формат вывода даты. 1 - 01.01.2018, 00:00; 2 - 1 января 2017 г., 00:00; 3 - 01.01.2018; 4 - 1 января 2018 г.
	* @param int $show_y    - Показывать ли год (для формата 4)
*/
function T_DateFormat($date_f, $date_t, $show_y = 1)
{
	$parsedate = Date_Parse($date_f);
	$monthint  = $parsedate['month'];

	//Замена числового кода месяца на слово
	switch ($monthint)
	{
		case '01': $month_word = 'января';   break;
		case '02': $month_word = 'февраля';  break;
		case '03': $month_word = 'марта';    break;
		case '04': $month_word = 'апреля';   break;
		case '05': $month_word = 'мая';      break;
		case '06': $month_word = 'июня';     break;
		case '07': $month_word = 'июля';     break;
		case '08': $month_word = 'августа';  break;
		case '09': $month_word = 'сентября'; break;
		case '10': $month_word = 'октября';  break;
		case '11': $month_word = 'ноября';   break;
		case '12': $month_word = 'декабря';  break;
		default:
			$month_word = 'не определён';
	}

	if (StrLen($parsedate['day']) == 1)
		$parsed_day = '0'.$parsedate['day'];
	else
		$parsed_day = $parsedate['day'];

	if (StrLen($parsedate['month']) == 1)
		$parsed_month = '0'.$parsedate['month'];
	else
		$parsed_month = $parsedate['month'];

	if (StrLen($parsedate['hour']) == 1)
		$parsed_hour = '0'.$parsedate['hour'];
	else
		$parsed_hour = $parsedate['hour'];

	if (StrLen($parsedate['minute']) == 1)
		$parsed_minute = '0'.$parsedate['minute'];
	else
		$parsed_minute = $parsedate['minute'];

	if (StrLen($parsedate['second']) == 1)
		$parsed_second = '0'.$parsedate['second'];
	else
		$parsed_second = $parsedate['second'];

	switch ($date_t)
	{
		//Формат: 01.01.2018, 00:00
		case 1:
			$result_date = $parsed_day.'.'.$parsed_month.'.'.$parsedate['year'].', '.$parsed_hour.':'.$parsed_minute;
			break;

		//Формат:
		//Для прошлого года: 1 января 2017 г., 00:00
		//Для текущего года: 1 января, 00:00
		case 2:
		{
			if ($parsedate['year'] != date("Y"))
				$result_date = $parsedate['day'].' '.$month_word.' '.$parsedate['year'].' г., '.$parsedate['hour'].':'.$parsed_minute;
			else
				$result_date = $parsedate['day'].' '.$month_word.', '.$parsedate['hour'].':'.$parsed_minute;
		}
			break;

		//Формат: 01.01.2018  - используется если не нужно время
		case 3:
			$result_date = $parsed_day.'.'.$parsed_month.'.'.$parsedate['year'];
			break;

		//Формат: 1 января 2018 г. или просто 1 января - используется если не нужно время
		case 4:
		{
			if ($parsedate['year'] != 0 && $show_y == 1) //И если не скрыт год
				$result_date = $parsedate['day'].' '.$month_word.' '.$parsedate['year'].' г.';
			else
				$result_date = $parsedate['day'].' '.$month_word;
			
			if ($parsedate['day'] == 0 && $parsedate['month'] == 0 && $parsedate['year'] == 0)
				$result_date = "";
		}
			break;
	}

	return $result_date;
}

/*
	* Генератор псевдослучайных строк
	*
	* @param int $length - Желаемая длина генерируемой строки
*/
function T_RSGen($length)
{
	$rsa = array(
		'a', 'b', 'c', 'd', 'e', 'f', 'g',
		'h', 'i', 'j', 'k', 'l', 'm', 'n',
		'o', 'p', 'q', 'r', 's', 't', 'u',
		'v', 'w', 'x', 'y', 'z',
		'A', 'B', 'C', 'D', 'E', 'F', 'G',
		'H', 'I', 'J', 'K', 'L', 'M', 'N',
		'O', 'P', 'Q', 'R', 'S', 'T', 'U',
		'V', 'W', 'X', 'Y', 'Z',
		'1', '2', '3', '4', '5', '6', '7',
		'8', '9', '0', '_', '-');

	$result = "";

	for ($i = 0; $i < $length; $i++)
	{
		$index   = rand(0, count($rsa) - 1);
		$result .= $rsa[$index];
	}

	return $result;
}