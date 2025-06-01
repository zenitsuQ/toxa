<?php
	$pagecount = T_GetData("SELECT COUNT(`id`) AS `cnt` FROM `callbacks`", "cnt");
	$pagecount = ceil($pagecount / 10);

	if (isset($_GET['zero']))
	{
		$q = T_GetData("SELECT `id` FROM `callbacks`");
		if (empty($q))
		{
			T_GetData("ALTER TABLE `callbacks` AUTO_INCREMENT = 1", "", 1);
			echo '<meta http-equiv="refresh" content="3;?callback"><center><strong>AUTO_INCREMENT таблицы "callbacks" теперь равен 1.</strong></center>';
		}
		else
			echo '<center><font color="red"><strong>Таблица обратной связи не пуста!</strong></font></center>';

		require_once("template/footer.php");
		return;
	}

	if (!isset($_GET['accept']) && !isset($_GET['drop']) && empty($id))
	{
		$q = T_GetData("SELECT `id` FROM `callbacks`");
		if (empty($q))
		{
			echo '<center><strong>Нет обратной связи.</strong><br><br><a href="?callback&zero" onclick="return confirm(\'Вы уверены?\')">Обнулить инкремент</a></center>';
		}
		else
		{
			echo '<br><table width="95%" align="center" valign="top"><tr><td width="5%" align="center"><strong>ID</strong></td><td width="15%" align="center"><strong>Дата создания</strong></td><td width="15%" align="center"><strong>Имя</strong></td><td width="40%" align="center"><strong>Текст</strong></td><td width="10%" align="center"><strong>Управление</strong></td></tr>';
		
			$q = T_GetData("SELECT * FROM `callbacks` ORDER BY `id` DESC LIMIT $pagedb, 10", "", 1);
			foreach ($q as $qq)
			{
				$nid     = $qq['id'];
				$ndate   = T_DateFormat($qq['date'], $site_df);

				$text   = MB_SubStr($qq['message'], 0, 100, "UTF-8");
				if (MB_StrLen($qq['message'], "UTF-8") > 100)
					$text = $text . '...';

				$text = T_Smiles($text);

				//$city   = $qq['city'];
				$name   = $qq['name'];
				$naccept = $qq['is_checked'];

				$template = T_Template();

				if ($naccept == '1')
					$nbuttons = '<a href="?callback&drop&id='.$nid.'" title="Удалить"><img src="/templates/' . $template . '/assets/img/drop.png" alt="Удалить"></a>';
				else
					$nbuttons = '<a href="?callback&accept&id='.$nid.'" title="Обработать"><img src="/templates/' . $template . '/assets/img/accept.png" alt=""></a> <a href="?callback&drop&id='.$nid.'" title="Удалить"><img src="/templates/' . $template . '/assets/img/drop.png" alt="Удалить"></a>';
		
				PRINT <<<HERE
<tr><td><a href="?callback&id=$nid" title="Просмотр сообщения №$nid">$nid</a></td><td>$ndate</td><td>$name</td><td align="center">$text</td><td align="center">$nbuttons</td></tr>
HERE;
			}
			echo '</table><br>';

			if ($pagecount > 1)
			{
				echo '<center>Страницы:
';
				for ($i = 0; $i++ < $pagecount;)
				{
					if ($page == $i)
					{
						echo '| <b>'.$i.'</b> ';
					}
					else
					{
						echo '| <a href="?callback&page='.$i.'">'.$i.'</a> ';
					}
				}
				echo "|</center><br>";
			}
		}
	}
	else if (!isset($_GET['accept']) && !isset($_GET['drop']) && !empty($id))
	{
		$q = T_GetData("SELECT * FROM `callbacks` WHERE `id` = '$id'");

		$nid     = $q['id'];
		$ndate   = T_DateFormat($q['date'], $site_df);
		$nauthor = (int) $q['user'];

		$author = T_GetData("SELECT `login` FROM `users` WHERE `id` = $nauthor", "login");

		$ntext   = T_Smiles($q['message']);
		$ncity   = $q['city'];
		$nname   = $q['name'];
		$nphone  = $q['phone'];
		$nemail   = $q['email'];
		$naccept = $q['is_checked'];

		if ($naccept == 1)
			$naccept = "Обработано";
		else
			$naccept = "Ожидает ответа";

		PRINT <<<HERE
<center><strong>Сообщение обратной связи №$nid</strong></center><br>
<strong>Дата:</strong> $ndate<br>
<strong>Автор:</strong> $nauthor ($author)<br>
<strong>Имя:</strong> $nname<br>
<strong>Город:</strong> $ncity<br>
<strong>E-Mail:</strong> $nemail<br>
<strong>Телефон:</strong> $nphone<br>
<strong>Статус:</strong> $naccept<br>
<strong>Текст:</strong><br>
$ntext
HERE;
	}
	else if (isset($_GET['accept']) && !empty($id)) //Принять
	{
		if (!isset($_POST['yes']))
		{
			echo '<center>Вы действительно хотите пометить обработанным это сообщение?<br><br><form action="?callback&accept&id='.$id.'" method="POST"><button type="submit" name="yes">Да</button></form></center>';
		}
		else
		{
			T_GetData("UPDATE `callbacks` SET `is_checked` = 1 WHERE `id` = $id", "", 1);
			echo '<meta http-equiv="refresh" content="3;?callback"><center>Сообщение успешно обработано.</center>';
		}
	}
	else if (isset($_GET['drop']) && !empty($id))  //Удалить
	{
		if (!isset($_POST['yes']))
		{
			echo '<center>Вы действительно хотите удалить это сообщение? Это действие необратимо.<br><br><form action="?callback&drop&id='.$id.'" method="POST"><button type="submit" name="yes">Да</button></form></center>';
		}
		else
		{
			T_GetData("DELETE FROM `callbacks` WHERE `id` = $id", "", 1);
			echo '<meta http-equiv="refresh" content="3;?callback"><center>Сообщение успешно удалено.</center>';
		}
	}
		else
			echo '<center><font color="red"><strong>Выполнен неподдерживаемый запрос. Необходима руковыпрямляющая машина.<br><p align="right"><img src="style/img/hand_notline.png" alt="Юзер детектед"><img src="style/img/hand_line.png" alt="Руковыпрямляющая машина" height="230px"></p></strong></font></center>';