<?php
	switch (CURRENT_ACTION)
	{
		case "js":
			{
				// Проверка наличия заголовка JS-API-Client (защита от прямых запросов через браузер)
				$header = $_SERVER['HTTP_JS_API_CLIENT'];

				if (!empty($_userID) && !empty($header))
				{
					if (isset($_GET['order_by']))
					{
						$order = (int) $_GET['order_by'];

						if (!empty($order) && $order > 0 && $order < 5)
						{
							$_SESSION['order'] = $order;

							echo "OK";
						}
						else
							echo "ERR_INCORRECT_ORDER";
					}
				}
				else
					T_Error(400, "Auth error");
			}
			break;
		default:
			{
				T_Error(400, "Auth error");
			}
			break;
	}

	exit;