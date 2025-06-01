<?php
	$content    = "";

	$_SiteTitle = "Магазин";

	switch (CURRENT_ACTION)
	{
		// Корзина
		case "cart":
			{
				if (!T_Authorized())
				{
					$content = '<div class="center">Для доступа к корзине необходимо <a href="/auth">авторизоваться</a>.</div>';
					break;
				}
				else
				{
					// Добавление в корзину
					switch (CURRENT_PARAM)
					{
						case "add":
							{
								$item_id  = (int) CURRENT_EXTRA;		// Товар
								$item_cnt = (int) CURRENT_EXTRA1;		// Количеество

								if ($item_id < 0 || $item_cnt < 0)		// Если значения отрицательные:
								{
									$_SiteTitle = "Ошибка!";
									$content = '<div class="center">Некорректные данные!</div>';
									break;
								}

								$_SiteTitle = "Добавление в корзину";

								// Секция проверок

								if (!empty($item_id))
								{
									// Проверка на количество добавляемого товара
									if (!empty($item_cnt))
									{
										// Проверка на наличие товара в корзине
										$q = T_GetData("SELECT `id` FROM `shop_cart` WHERE `user` = $_userID AND `item_id` = $item_id", "id");
										if (empty($q))
										{
											// Проверка на существование / наличие товара
											$q = T_GetData("SELECT `count` FROM `shop_items` WHERE `id` = $item_id", "count");
											if ($q > 0)
											{
												// Проверка на добавление разумного количества товара
												if ($item_cnt <= $q)
												{
													$q = T_GetData("INSERT INTO `shop_cart` (`date`, `user`, `item_id`, `count`) VALUES (NOW(), $_userID, $item_id, $item_cnt)", "", 1);
													if ($q)
													{
														$content = '<div class="center">Товар успешно добавлен в Вашу корзину!<br><br><a href="/shop/cart">Перейти к корзине</a></div>';
													}
													else
														$content = '<div class="center">Произошла ошибка при добавлении товара в корзину.<br><br>Если она повторится - пожалуйста, уведомите администратора.</div>';
												}
												else
													$content = '<div class="center">Нельзя добавить в корзину больше, чем есть в наличии.</div>';
											}
											else
												$content = '<div class="center">Нельзя добавить в корзину товар, которого нет в наличии или на сайте.</div>';
										}
										else
											$content = '<div class="center">Этот товар уже находится в Вашей корзине.<br><br>Вы в любой момент можете изменить желаемое количество товара перед заказом или удалить его из <a href="/shop/cart">корзины</a>.</div>';
									}
									else
										$content = '<div class="center">Нельзя добавить пустоту.</div>';
								}
								else
									$content = '<div class="center">Нечего добавлять.</div>';
							}
							break;
						// Удаление из корзины
						case "delete":
							{
								$_SiteTitle = "Удаление из корзины";

								$item_id  = (int) CURRENT_EXTRA;

								if (!empty($item_id))
								{
									// Если задан ID товара - удаляем и переадресуем назад
									$q = T_GetData("DELETE FROM `shop_cart` WHERE `user` = $_userID AND `item_id` = $item_id", "", 1);
									if ($q)
										header("Location: /shop/cart");
									else
										$content = '<div class="center">Ошибка удаления товара из корзины!</div>';
								}
								else
								{
									// Если НЕ задан ID товара - проверяем, не хотят ли удалить несколько товаров
									if (!empty($_SESSION['cart_for_delete']))
									{
										$items = $_SESSION['cart_for_delete'];

										$y_n  = (int) CURRENT_EXTRA1;

										// Если нет подтверждения очистки - выводим
										if (empty($y_n))
										{
											$content = '<div class="center">Вы уверены, что хотите удалить эти товары из корзины?<br><br>Это действие необратимо.<br><br><button onclick="location.href=\'/shop/cart/delete/0/1\'">Да</button><button onclick="location.href=\'/shop/cart\'">Нет</button></div>';
										}
										else
										{
											$end   = true;
											$items = $_SESSION['cart_for_delete'];

											for ($i = 0; $i < count($items); $i++)
											{
												$id  = (int) $items[$i];

												if ($id > 0)
												{
													$q = T_GetData("DELETE FROM `shop_cart` WHERE `user` = $_userID AND `item_id` = $id", "", 1);

													if (!$q)
														$end = false;
												}
											}

											if ($end)
											{
												unset($_SESSION['cart_for_delete']);

												header("Location: /shop/cart");
											}
											else
											{
												unset($_SESSION['cart_for_delete']);

												$content = '<div class="center">Произошла ошибка при удалении одного или нескольких товаров из корзины.<br><br>Если она повторится - пожалуйста, уведомите администратора.</div>';
											}
										}
									}
									else
										$content = '<div class="center">Нам непонятно что Вы хотите удалить и уверены ли Вы в этом.</div>';
								}
							}
							break;
						// Очистка корзины
						case "clear":
							{
								$_SiteTitle = "Очистка корзины";

								$y_n  = (int) CURRENT_EXTRA;

								// Если нет подтверждения очистки - выводим
								if (empty($y_n))
								{
									$content = '<div class="center">Вы уверены, что хотите очистить свою покупательскую корзину?<br><br>Это действие необратимо.<br><br><button onclick="location.href=\'/shop/cart/clear/1\'">Да</button><button onclick="location.href=\'/shop/cart\'">Нет</button></div>';
								}
								else
								{
									// Удаляем всё из корзины пользователя
									$q = T_GetData("DELETE FROM `shop_cart` WHERE `user` = $_userID", "", 1);
									if ($q)
										header("Location: /shop/cart");
									else
										$content = '<div class="center">Произошла ошибка при удалении товара из корзины.<br><br>Если она повторится - пожалуйста, уведомите администратора.</div>';
								}
							}
							break;
						// Просмотр корзины
						default:
							{
								$_SiteTitle = "Корзина";

								unset($_SESSION['cart_for_delete']);
								unset($_SESSION['cart_for_order_i']);
								unset($_SESSION['cart_for_order_c']);

								// Отправляем выбранные вещи на удаление
								if (isset($_POST['for_delete']))
								{
									$_SESSION['cart_for_delete'] = $_POST['items'];

									header("Location: /shop/cart/delete");

									break;
								}

								// Отправляем выбранные вещи на заказ
								if (isset($_POST['for_order']))
								{
									$_SESSION['cart_for_order_i'] = $_POST['items'];
									$_SESSION['cart_for_order_c'] = $_POST['counts'];

									header("Location: /shop/orders/add");

									break;
								}

								$page = (int) CURRENT_EXTRA;

								if ($page == 0)
									$page = 1;

								$pageDB = ($page - 1) * 20;

								if (CURRENT_PARAM == "page")
									if ($page > 1)
										$_SiteTitle .= ", страница " . $page;

								// Получаем список товаров в корзине и их количество
								$items_cnt = (int)T_GetData("SELECT COUNT(`id`) AS `cnt` FROM `shop_cart` WHERE `user` = $_userID", "cnt");

								if (!empty($items_cnt))
								{
									$q = T_GetData("SELECT `id`, `item_id`, `count` FROM `shop_cart` WHERE `user` = $_userID ORDER BY `id` DESC LIMIT $pageDB, 20", "", 1);
									$cnt = mysqli_num_rows($q);

									if ($cnt > 0)
									{
										$content = '<div class="center"><form nane="cart" method="POST">';

										foreach ($q as $part)
										{
											$id      = (int) $part['id'];
											$item_id = (int) $part['item_id'];
											$cnt     = (int) $part['count'];

											// Получаем инфу о товаре
											$item = T_GetData("SELECT `name`, `price`, `count` FROM `shop_items` WHERE `id` = $item_id");

											$item_name  = $item['name'];
											$item_price = $item['price'];
											$item_count = $item['count'];

											// Получение картинки товара
											$item_image = T_GetData("SELECT `file_name`, `hash` FROM `shop_items_images` WHERE `item_id` = $item_id AND `is_default` = 1 LIMIT 1");

											if (!empty($item_image))
											{
												$dname      = StrToLower(MB_SubStr($item_image['file_name'], 0, 1, 'UTF-8'));
												$dhname     = StrToLower(MB_SubStr($item_image['hash'], 0, 2, 'UTF-8'));
												$item_image = "/files/shop/" . $dname . "/" . $dhname . "/" . $item_image['file_name'];
											}
											else
												$item_image = "/templates/" . T_Template() . "/assets/img/no_image.png";

											// Вывод

											if ($item_count < $cnt)
												$cnt = $item_count;

											$cart_div   = "shop-cart-item";
											$cart_cnt   = '<div class="shop-item-minus" item="' . $id . '">-</div><input type="text" name="counts[' . $item_id . ']" class="shop-item-selector" value="' . $cnt . '" readonly><div class="shop-item-plus" item="' . $id . '" max="' . $item_count . '">+</div>';
											$cart_check = '<div class="checkbox"><input type="checkbox" id="cart_select' . $id . '" name="items[]" value="' . $item_id . '"><label for="cart_select' . $id . '"></label></div>';
											if (empty($item_count))
											{
												$cart_div   = "shop-cart-item-no";
												$cart_cnt   = "Нет в наличии";
												$cart_check = "";
											}

											$semantic = $item_id . '-' . T_Semantic($item_name);

											$content .= '<div class="' . $cart_div . '">
											<div class="shop-cart-item-check">' . $cart_check . '</div>
											<img src="' . $item_image . '" alt="' . $item_name . '" class="shop-cart-item-image">
											<div class="shop-cart-item-actions"><img src="/templates/' . T_Template() . '/assets/img/drop.png" onclick="location.href=\'/shop/cart/delete/' . $item_id . '\'" title="Удалить"></div>
											<div class="shop-cart-item-price">' . $item_price . ' ₽</div>
											<div class="shop-cart-item-name"><a href="/shop/item/' . $semantic . '">' . $item_name . '</a></div>
											<div class="shop-cart-item-count">' . $cart_cnt . '</div>
											</div>';
										}

										$content .= '<button type="submit" name="for_delete" value="1">Удалить выбранные</button><button type="submit" name="for_order" value="1">Оформить заказ</button></form>
										<button onclick="location.href=\'/shop/cart/clear\'">Очистить корзину</button></div>';
									}
									else
										$content = '<div class="center">Ваша козина пуста.<br><br>Наполните её понравившимися товарами из <a href="/shop">магазина</a>.</div>';

									// Пагинация

									$page_cnt = ceil($items_cnt / 20);

									if ($page < $page_cnt)
										$content .= "<div class=\"shop-link\" onclick=\"location.href='/shop/cart/page/" . ($page + 1) . "'\">Далее</div>";

									if ($page > $page_cnt || $page > 1)
										$content .= "<div class=\"shop-link\" onclick=\"location.href='/shop/cart/page/" . ($page - 1) . "'\">Назад</div>";
								}
								else
									$content = '<div class="center">Ваша козина пуста.<br><br>Наполните её понравившимися товарами из <a href="/shop">магазина</a>.</div>';
							}
							break;
					}
				}
			}
			break;
		// Заказы
		case "orders":
			{
				$_SiteTitle = "Заказы";

				/*
					Статусы заказов:

					0 - отменён
					1 - создан
					2 - подтверждён
					3 - обработан
					4 - готов / отправлен
					5 - выдан / доставлен

					Типы доставки:

					0 - самовывоз
					1 - курьером
					2 - почтой
				*/

				if (!T_Authorized())
				{
					$content = '<div class="center">Для доступа к заказам необходимо <a href="/auth">авторизоваться</a>.</div>';
					break;
				}
				else
				{
					switch (CURRENT_PARAM)
					{
						// Создание заказа
						case "add":
							{
								$_SiteTitle = "Добавление заказа";

								// Ищем товары для добавления в заказ
								if (!empty($_SESSION['cart_for_order_i']) && !empty($_SESSION['cart_for_order_c']))
								{
									$items  = $_SESSION['cart_for_order_i'];
									$counts = $_SESSION['cart_for_order_c'];

									// Создаём пустой заказ
									$q = T_GetData("INSERT INTO `shop_orders` (`date`, `user`) VALUES (NOW(), $_userID)", "", 1);
									if ($q)
									{
										$all_ok   = true;	// Всё добавилось

										// Получаем ID свежесозданного заказа
										$order_id = (int) T_GetData("SELECT MAX(`id`) AS `max` FROM `shop_orders` WHERE `user` = $_userID", "max");

										// Перебираем товары
										for ($i = 0; $i < count($items); $i++)
										{
											$id = (int) $items[$i];

											// Валидация ID
											if ($id > 0)
											{
												$count = (int) $counts[$id];

												// Валидация количества
												if ($count > 0)
												{
													// Получение количества товаров в наличии и проверка на существование
													$q = (int) T_GetData("SELECT `count` FROM `shop_items` WHERE `id` = $id", "count");

													if (!empty($q))
													{
														// Если желаемое количество больше, чем есть - ставим максимально доступное
														if ($count > $q)
															$count = $q;

														// Пытаемся добавить товар в заказ
														$q = T_GetData("INSERT INTO `shop_orders_parts` (`user`, `order_id`, `item_id`, `count`) VALUES ($_userID, $order_id, $id, $count)", "", 1);
														if ($q)
															T_GetData("DELETE FROM `shop_cart` WHERE `user` = $_userID AND `item_id` = $id", "", 1); // Удаляем товар из корзины
														else
														{
															$all_ok = false;
															continue;
														}
													}
													else
													{
														$all_ok = false;
														continue;
													}
												}
												else
												{
													$all_ok = false;
													continue;
												}
											}
										}

										if ($all_ok)
											header("Location: /shop/orders/show/" . $order_id);
										else
											$content = '<div class="center">Не удалось добавить в заказ один или несколько товаров.<br><br>Пожалуйста, тщательно проверьте свой <a href="/shop/orders/show/' . $order_id . '">заказ</a> перед подтверждением.</div>';
									}
									else
										$content = '<div class="center">Не удалось создать заказ!<br><br>Повторите попытку позже и при повторении ошибки, пожалуйста, сообщите о ней администратору.</div>';
								}
								else
									$content = '<div class="center">Нам непонятно что Вы хотите купить.</div>';
							}
							break;
						// Удаление заказа
						case "delete":
							{
								$_SiteTitle = "Удаление заказа";

								$id = (int) CURRENT_EXTRA;

								if ($id > 0)
								{
									// Проверяем владельца и статус
									$q = T_GetData("SELECT `user`, `status` FROM `shop_orders` WHERE `id` = $id");

									// Если владелец - разрешаем удаление
									if ($q['user'] == $_userID)
									{
										// Если статус "Отменён" или "Создан" - удаляем
										if ($q['status'] == 0 || $q['status'] == 1)
										{
											$y_n = (int) CURRENT_EXTRA1;

											if (empty($y_n))
												$content = '<div class="center">Вы уверены, что хотите удалить заказ?<br><br>Это действие необратимо.<br><br><button onclick="location.href=\'/shop/orders/delete/' . $id . '/1\'">Да</button><button onclick="location.href=\'/shop/orders/show/' . $id . '\'">Нет</button></div>';
											else
											{
												// Удаляем заказ
												$q = T_GetData("DELETE FROM `shop_orders` WHERE `id` = $id", "", 1);
												if ($q)
													header("Location: /shop/orders");
												else
													$content = '<div class="center">Ошибка удаления заказа!<br><br>Повторите попытку позже и при повторении ошибки, пожалуйста, сообщите о ней администратору.</div>';
											}
										}
										else
											$content = '<div class="center">Можно удалять только отменённые и не подтверждённые заказы!</div>';
									}
									else
										$content = '<div class="center">Нельзя удалять чужие или несуществующие заказы!</div>';
								}
								else
									$content = '<div class="center">Нам непонятно что Вы хотите удалить.</div>';
							}
							break;
						// Удаление товара из заказа
						case "drop":
							{
								$_SiteTitle = "Удаление товара из заказа";

								$order_id = (int) CURRENT_EXTRA;
								$item_id  = (int) CURRENT_EXTRA1;

								if ($order_id > 0 && $item_id > 0)
								{
									// Проверяем владельца и статус
									$q = T_GetData("SELECT `user`, `status` FROM `shop_orders` WHERE `id` = $order_id");

									// Если владелец - разрешаем удаление
									if ($q['user'] == $_userID)
									{
										// Если статус "Создан" - разрешаем удаление
										if ($q['status'] == 1)
										{
											// Проверяем на владельца и существование
											$q = (int) T_GetData("SELECT `user` FROM `shop_orders_parts` WHERE `order_id` = $order_id AND `item_id` = $item_id", "user");

											if ($q == $_userID)
											{
												// Удаляем заказ
												$q = T_GetData("DELETE FROM `shop_orders_parts` WHERE `order_id` = $order_id AND `item_id` = $item_id", "", 1);
												if ($q)
													header("Location: /shop/orders/show/" . $order_id);
												else
													$content = '<div class="center">Ошибка удаления товара из заказа!<br><br>Повторите попытку позже и при повторении ошибки, пожалуйста, сообщите о ней администратору.</div>';
											}
											else
												$content = '<div class="center">Нельзя удалять чужие или несуществующие товары из заказа!</div>';
										}
										else
											$content = '<div class="center">Можно удалять только отменённые и не подтверждённые заказы!</div>';
									}
									else
										$content = '<div class="center">Нельзя удалять чужие или несуществующие заказы!</div>';
								}
								else
									$content = '<div class="center">Нам непонятно что Вы хотите удалить.</div>';
							}
							break;
						// Подтверждение заказа / получения
						case "accept":
							{
								$_SiteTitle = "Подтверждение заказа";

								$id = (int) CURRENT_EXTRA;

								if ($id > 0)
								{
									// Проверяем владельца и статус
									$q = T_GetData("SELECT `user`, `status` FROM `shop_orders` WHERE `id` = $id");

									// Если владелец - разрешаем подтверждение
									if ($q['user'] == $_userID)
									{
										// Если статус "Создан" или "Готов" - разрешаем подтверждение
										if ($q['status'] == 1 || $q['status'] == 4)
										{
											if ($q['status'] == 1)
											{
												// Проверяем данные формы подтверждения
												if (isset($_POST['accept']))
												{
													$address = T_SafeText(Trim($_POST['address']));
													$phone   = T_SafeText(Trim($_POST['phone']));
													$type    = (int) $_POST['type'];

													if (!empty($phone) && $type >= 0)
													{
														if ($type != 0)
															if (empty($address))
															{
																$content = '<div class="center">Для способов получения "Курьером" и "Почтой" указание адреса обязательно!</div>';
																break;
															}

														// Подтверждаем заказ
														$q = T_GetData("UPDATE `shop_orders` SET `u_date` = NOW(), `address` = '$address', `phone` = '$phone', `delivery_type` = $type, `status` = 2 WHERE `id` = $id", "", 1);

														if ($q)
															$content = '<div class="center">Заказ подтверждён!<br><br>Пожалуйста, дождитесь окончания обработки. Это может занять несколько часов.</div>';
														else
															$content = '<div class="center">Не удалось подтвердить заказ!<br><br>Повторите попытку позже и при повторении ошибки, пожалуйста, сообщите о ней администратору.</div>';
													}
													else
														$content = '<div class="center">Ошибка подтверждения заказа!<br><br>Вы не указали номер телефона или передали некорректный тип доставки!</div>';
												}
												else
												{
													// Выводим форму подтверждения заказа
													$content = '<center>
													Пожалуйста заполните краткую форму, указав предпочтительный способ получения заказа.<br><br>
													Обратите внимание: оплата производится при получении товара.<br><br>
													<form name="accept" method="POST">
													Адрес:<br>
													<input type="text" class="def-input" name="address" minlength="5" maxlength="100" placeholder="Введите свой адрес с указанием индекса (необязательно при самовывозе)"><br>
													Телефон:<br>
													<input type="text" class="def-input" name="phone" minlength="10" maxlength="20" placeholder="Введите свой номер телефона в формате +7 (XXX) XXX-XX-XX" required><br>
													Способ получения:<br>
													<select name="type" class="def-input" required>
														<option value="0" selected>Самовывоз</option>
														<option value="1">Курьером</option>
														<option value="2">Почтой</option>
													</select><br><br>
													<button type="submit" name="accept">Подтвердить</button>
													</form></center>';
												}
											}
											else
											{
												// Завершаем заказ
												$q = T_GetData("UPDATE `shop_orders` SET `u_date` = NOW(), `status` = 5 WHERE `id` = $id", "", 1);

												if ($q)
													$content = '<div class="center">Заказ завершён!<br><br>Теперь Вы можете оставить отзывы к приобретённым товарам.</div>';
												else
													$content = '<div class="center">Не удалось завершить заказ!<br><br>Повторите попытку позже и при повторении ошибки, пожалуйста, сообщите о ней администратору.</div>';
											}
										}
										else
											$content = '<div class="center">Можно подтверждать только созданные и готовые заказы!</div>';
									}
									else
										$content = '<div class="center">Нельзя подтверждать чужие или несуществующие заказы!</div>';
								}
								else
									$content = '<div class="center">Нам непонятно что Вы хотите подтвердить.</div>';
							}
							break;
						// Отмена заказа
						case "cancel":
							{
								$_SiteTitle = "Отмена заказа";

								$id = (int) CURRENT_EXTRA;

								if ($id > 0)
								{
									// Проверяем владельца и статус
									$q = T_GetData("SELECT `user`, `status` FROM `shop_orders` WHERE `id` = $id");

									// Если владелец - разрешаем отмену
									if ($q['user'] == $_userID)
									{
										// Если статус "Создан" или "Подтверждён" - разрешаем отмену
										if ($q['status'] == 1 || $q['status'] == 2)
										{
											$y_n = (int) CURRENT_EXTRA1;

											if (empty($y_n))
												$content = '<div class="center">Вы уверены, что хотите отменить заказ?<br><br>Это действие необратимо.<br><br><button onclick="location.href=\'/shop/orders/cancel/' . $id . '/1\'">Да</button><button onclick="location.href=\'/shop/orders/show/' . $id . '\'">Нет</button></div>';
											else
											{
												$q = T_GetData("UPDATE `shop_orders` SET `u_date` = NOW(), `status` = 0 WHERE `id` = $id", "", 1);
												if ($q)
													header("Location: /shop/orders/show/" . $id);
												else
													$content = '<div class="center">Не удалось отменить заказ!<br><br>Повторите попытку позже и при повторении ошибки, пожалуйста, сообщите о ней администратору.</div>';
											}
										}
										else
											$content = '<div class="center">Можно отменять только созданные и подтверждённые заказы!</div>';
									}
									else
										$content = '<div class="center">Нельзя отменять чужие или несуществующие заказы!</div>';
								}
								else
									$content = '<div class="center">Нам непонятно что Вы хотите отменить.</div>';
							}
							break;
						// Просмотр подробностей заказа
						case "show":
							{
								$_SiteTitle = "Просмотр заказа";

								unset($_SESSION['cart_for_order_i']);
								unset($_SESSION['cart_for_order_c']);

								$id = (int) CURRENT_EXTRA;

								if ($id > 0)
								{
									// Проверяем владельца и получаем данные
									$q = T_GetData("SELECT * FROM `shop_orders` WHERE `id` = $id");

									// Если владелец - разрешаем просмотр
									if ($q['user'] == $_userID)
									{
										$order_date    = T_DateFormat($q['date'], $site_df);
										$order_udate   = $q['u_date'];
										$order_address = $q['address'];
										$order_phone   = $q['phone'];
										$order_track   = $q['post_track'];
										$order_type    = $q['delivery_type'];
										$order_status  = $q['status'];

										if (!empty($order_udate))
											$order_udate = T_DateFormat($order_udate, $site_df);

										// Тип доставки
										$type_text = "";
										switch ($order_type)
										{
											case 0: $type_text = "самовывоз"; break;
											case 1: $type_text = "курьером"; break;
											case 2: $type_text = "почтой"; break;
											default:
												$type_text = "неизвестно";
												break;
										}

										// Статус заказа
										$status_text = "";
										switch ($order_status)
										{
											case 0: $status_text = "отменён"; break;
											case 1: $status_text = "ожидает подтверждения"; break;
											case 2: $status_text = "подтверждён"; break;
											case 3: $status_text = "обработан"; break;
											case 4:
												{
													if ($order_type == 0)
														$status_text = "ожидает покупателя";
													else
														$status_text = "отправлен";
												}
												break;
											case 5:
												{
													if ($order_type == 0)
														$status_text = "выдан";
													else
														$status_text = "доставлен";
												}
												break;
											default:
												$status_text = "неизвестно";
												break;
										}

										$_SiteTitle = "Информация о заказе №$id";

										$content .= "<strong>Дата создания:</strong> $order_date<br>";

										if (!empty($order_udate))
											$content .= "<strong>Дата изменения:</strong> $order_udate<br>";

										$content .= "<strong>Тип доставки:</strong> $type_text<br>";

										if (!empty($order_phone))
											$content .= "<strong>Телефон:</strong> $order_phone<br>";

										if ($order_type != 0)
											$content .= "<strong>Адрес:</strong> $order_address<br>";

										if ($order_type == 2 && !empty($order_track))
											$content .= '<strong>Трек-номер:</strong> <a href="https://pochta.ru/tracking#' . $order_track . '" target="_blank">' . $order_track . '</a><br>';

										$content .= "<strong>Статус:</strong> $status_text<br>";

										$content .= "<strong>Список товаров:</strong> ";

										// Получение списка товаров в заказе
										$q     = T_GetData("SELECT `item_id`, `count` FROM `shop_orders_parts` WHERE `order_id` = $id", "", 1);
										$i_cnt = mysqli_num_rows($q);

										if ($i_cnt > 0)
										{
											$content   .= '<br><table width="95%" align="center"><tr>
											<td width="10%" align="center"><strong>Фото</strong></td>
											<td width="55%" align="center"><strong>Имя</strong></td>
											<td width="10%" align="center"><strong>Цена, шт.</strong></td>
											<td width="10%" align="center"><strong>Кол-во</strong></td>
											<td width="10%" align="center"><strong>Цена</strong></td>
											<td width="5%" align="center"></td></tr>';

											$full_price = 0;

											// Вывод товаров в заказе
											foreach ($q as $item)
											{
												$item_id  = (int) $item['item_id'];
												$item_cnt = (int) $item['count'];

												$it = T_GetData("SELECT `name`, `price` FROM `shop_items` WHERE `id` = $item_id");
												if (!empty($it))
												{
													$item_name  = $it['name'];
													$item_price = $it['price'];

													$end_price   = $item_price * $item_cnt;	// Цена в заказе
													$full_price += $end_price;				// Прибавляем к цене заказа

													$item_image = T_GetData("SELECT `file_name`, `hash` FROM `shop_items_images` WHERE `item_id` = $item_id AND `is_default` = 1 LIMIT 1");

													if (!empty($item_image))
													{
														$dname  = StrToLower(MB_SubStr($item_image['file_name'], 0, 1, 'UTF-8'));
														$dhname = StrToLower(MB_SubStr($item_image['hash'], 0, 2, 'UTF-8'));
														$item_image  = "/files/shop/" . $dname . "/" . $dhname . "/" . $item_image['file_name'];
													}
													else
														$item_image = "/templates/" . T_Template() . "/assets/img/no_image.png";

													// Вывод

													$semantic = $item_id . '-' . T_Semantic($item_name);

													$delete_item = "";
													if ($order_status == 1)
														$delete_item = '<img src="/templates/' . T_Template() . '/assets/img/drop.png" onclick="location.href=\'/shop/orders/drop/' . $id . '/' . $item_id . '\'" title="Удалить" class="button-image">';

													$content .= '<tr>
													<td align="center"><img src="' . $item_image . '" class="shop-order-image"></td>
													<td><a href="/shop/item/' . $semantic . '">' . $item_name . '</a></td>
													<td align="center">' . $item_price . ' ₽</td>
													<td align="center">' . $item_cnt . ' шт.</td>
													<td align="center">' . $end_price . ' ₽</td>
													<td align="center">' . $delete_item . '</td></tr>';
												}
												else
												{
													$content .= '<tr><td colspan="6">Товар не найден.</td></tr>';
													continue;
												}
											}

											$content .= '<tr><td><strong>ИТОГО:</strong></td><td colspan="5" align="right">' . $full_price . ' ₽</tr></table>';

											if ($order_status == 0)
												$content .= '<div class="center"><button onclick="location.href=\'/shop/orders/delete/' . $id . '\'">Удалить</button></div>';

											if ($order_status == 1)
												$content .= '<div class="center"><button onclick="location.href=\'/shop/orders/accept/' . $id . '\'">Подтвердить</button><button onclick="location.href=\'/shop/orders/cancel/' . $id . '\'">Отменить</button><button onclick="location.href=\'/shop/orders/delete/' . $id . '\'">Удалить</button></div>';

											if ($order_status == 2)
												$content .= '<div class="center"><button onclick="location.href=\'/shop/orders/cancel/' . $id . '\'">Отменить</button></div>';

											if ($order_status == 4)
												$content .= '<div class="center"><button onclick="location.href=\'/shop/orders/accept/' . $id . '\'">Завершить</button></div>';
										}
										else
											$content .= "нет товаров<br>";
									}
									else
										$content = '<div class="center">Нельзя просматривать чужие или несуществующие заказы!</div>';
								}
								else
									$content = '<div class="center">Нам непонятно что Вы хотите просмотреть.</div>';
							}
							break;
						// Просмотр списка заказов
						default:
							{
								$_SiteTitle = "Заказы";

								unset($_SESSION['cart_for_order_i']);
								unset($_SESSION['cart_for_order_c']);

								$page = (int) CURRENT_EXTRA;

								if ($page == 0)
									$page = 1;

								$pageDB = ($page - 1) * 20;

								if (CURRENT_PARAM == "page")
									if ($page > 1)
										$_SiteTitle .= ", страница " . $page;

								$orders_cnt = (int) T_GetData("SELECT COUNT(`id`) AS `cnt` FROM `shop_orders` WHERE `user` = $_userID", "cnt");			// Количество заказов
								if (!empty($orders_cnt))
								{
									// Выводим заказы на странице
									$q   = T_GetData("SELECT `id`, `date`, `status` FROM `shop_orders` WHERE `user` = $_userID ORDER BY `id` DESC LIMIT $pageDB, 20", "", 1);
									$cnt = mysqli_num_rows($q);

									if ($cnt > 0)
									{
										// Вывод списка заказов
										$content .= '<table align="center" width="95%"><tr>
										<td width="15%" align="center"><strong>Дата</strong></td>
										<td width="30%" align="center"><strong>Имя</strong></td>
										<td width="15%" align="center"><strong>Статус</strong></td>
										<td width="10%" align="center"><strong>Кол-во</strong></td>
										<td width="10%" align="center"><strong>Сумма</strong></td>
										<td width="20%" align="center"><strong>Действия</strong></td></tr>';

										foreach ($q as $order)
										{
											$order_id     = $order['id'];
											$order_date   = T_DateFormat($order['date'], $site_df);
											$order_status = $order['status'];

											// Статус заказа
											$status_text = "";
											switch ($order_status)
											{
												case 0: $status_text = "отменён"; break;
												case 1: $status_text = "ожидает подтверждения"; break;
												case 2: $status_text = "подтверждён"; break;
												case 3: $status_text = "обработан"; break;
												case 4:
													{
														if ($order_type == 0)
															$status_text = "ожидает покупателя";
														else
															$status_text = "отправлен";
													}
													break;
												case 5:
													{
														if ($order_type == 0)
															$status_text = "выдан";
														else
															$status_text = "доставлен";
													}
													break;
												default:
													$status_text = "неизвестно";
													break;
											}

											$buttons = "";
											if ($order_status == 0)
												$buttons = '<img src="/templates/' . T_Template() . '/assets/img/drop.png" class="button-image" onclick="location.href=\'/shop/orders/delete/' . $order_id . '\'" title="Удалить">';

											if ($order_status == 1)
												$buttons = '<img src="/templates/' . T_Template() . '/assets/img/accept.png" class="button-image" onclick="location.href=\'/shop/orders/accept/' . $order_id . '\'" title="Подтвердить"><img src="/templates/' . T_Template() . '/assets/img/cancel.png" class="button-image" onclick="location.href=\'/shop/orders/cancel/' . $order_id . '\'" title="Отменить"><img src="/templates/' . T_Template() . '/assets/img/drop.png" class="button-image" onclick="location.href=\'/shop/orders/delete/' . $order_id . '\'" title="Удалить">';

											if ($order_status == 2)
												$buttons = '<img src="/templates/' . T_Template() . '/assets/img/cancel.png" class="button-image" onclick="location.href=\'/shop/orders/cancel/' . $order_id . '\'" title="Отменить">';

											if ($order_status == 4)
												$buttons = '<img src="/templates/' . T_Template() . '/assets/img/accept.png" class="button-image" onclick="location.href=\'/shop/orders/accept/' . $order_id . '\'" title="Подтвердить">';

											$items_count = 0;
											$order_price = 0;

											$q           = T_GetData("SELECT `item_id`, `count` FROM `shop_orders_parts` WHERE `order_id` = $order_id", "", 1);
											$items_count = mysqli_num_rows($q);

											foreach ($q as $item)
											{
												$item_id    = $item['item_id'];
												$item_count = $item['count'];

												$item_price = T_GetData("SELECT `price` FROM `shop_items` WHERE `id` = $item_id", "price");

												$order_price += $item_price * $item_count;
											}

											$content .= '<tr><td align="center">' . $order_date . '</td>
											<td><a href="/shop/orders/show/' . $order_id . '">Заказ №' . $order_id . '</a></td>
											<td align="center">' . $status_text . '</td>
											<td align="center">' . $items_count . '</td>
											<td align="center">' . $order_price . ' ₽</td>
											<td align="center">' . $buttons . '</td></tr>';
										}

										$content .= '</table>';
									}
									else
										$content = '<div class="center">Вы ещё ничего у нас не заказывали.</div>';

									// Пагинация

									$page_cnt = ceil($orders_cnt / 20);

									if ($page < $page_cnt)
										$content .= "<div class=\"shop-link\" onclick=\"location.href='/shop/orders/page/" . ($page + 1) . "'\">Далее</div>";

									if ($page > $page_cnt || $page > 1)
										$content .= "<div class=\"shop-link\" onclick=\"location.href='/shop/orders/page/" . ($page - 1) . "'\">Назад</div>";
								}
								else
									$content = '<div class="center">Вы ещё ничего у нас не заказывали.</div>';
							}
							break;
					}
				}
			}
			break;
		// Просмотр категорий
		case "category":
			{
				$id  = (int) CURRENT_PARAM;

				$cat = T_GetData("SELECT `name`, `description`, `is_public` FROM `shop_categories` WHERE `id` = $id");

				if (!empty($cat) && ($cat['is_public'] == 1 || T_Admin()))
				{
					// Валидация семантической ссылки
					$full_param = CURRENT_PARAM;
					$real_param = $id . "-" . T_Semantic($cat['name']);

					// Если ссылка правильно составлена - выводим данные
					if ($full_param == $real_param)
						$_SiteTitle = T_CutText($cat['name'], 50);
					else
					{
						$_SiteTitle = "Ошибка!";
						$content    = '<div class="center">Нет такой категории.</div>';
						break;
					}
				}
				else
				{
					$_SiteTitle = "Ошибка!";
					$content    = '<div class="center">Нет такой категории.</div>';
					break;
				}

				// Получение списка подкатегорий в категории и её описания

				$descr   = $cat['description'];
				$content = '<pre wrap>' . T_BBParse($descr) . '</pre><br>';

				$q   = T_GetData("SELECT `id`, `name` FROM `shop_subcategories` WHERE `cat_id` = $id AND `is_public` = 1", "", 1);
				$cnt = mysqli_num_rows($q);

				if ($cnt > 0)
				{
					foreach ($q as $cat)
					{
						$id        = (int) $cat['id'];
						$name      = $cat['name'];

						$items_cnt = T_GetData("SELECT COUNT(`id`) AS `cnt` FROM `shop_items` WHERE `subcategory` = $id AND `is_public` = 1 ORDER BY `name`", "cnt");

						// Вывод

						$content   .= '<div class="shop-link" onclick="location.href=\'/shop/subcategory/' . $id . '-' . T_Semantic($name) . '\'">' . $name . ' (' . $items_cnt . ')</div>';
					}
				}
				else
					$content .= '<div class="center"><strong>Нет подкатегорий.</strong></div>';
			}
			break;
		// Просмотр подкатегорий
		case "subcategory":
			{
				$id  = (int) CURRENT_PARAM;

				$cat = T_GetData("SELECT `name`, `description`, `is_public` FROM `shop_subcategories` WHERE `id` = $id");

				if (!empty($cat) && ($cat['is_public'] == 1 || T_Admin()))
				{
					// Валидация семантической ссылки
					$full_param = CURRENT_PARAM;
					$real_param = $id . "-" . T_Semantic($cat['name']);

					// Если ссылка правильно составлена - выводим данные
					if ($full_param == $real_param)
						$_SiteTitle = T_CutText($cat['name'], 50);
					else
					{
						$_SiteTitle = "Ошибка!";
						$content    = '<div class="center">Нет такой подкатегории.</div>';
						break;
					}
				}
				else
				{
					$_SiteTitle = "Ошибка!";
					$content    = '<div class="center">Нет такой подкатегории.</div>';
					break;
				}

				$page = (int) CURRENT_EXTRA1;

				if ($page == 0)
					$page = 1;

				$pageDB = ($page - 1) * 20;

				if (CURRENT_EXTRA == "page")
					if ($page > 1)
						$_SiteTitle .= ", страница " . $page;

				// Получение списка товаров в подкатегории и её описания

				$descr   = $cat['description'];
				$content = '<pre wrap>' . T_BBParse($descr) . '</pre>';

				// Сортировка товаров

				$sort   = (int) $_SESSION['order'];
				$order  = "";
				$o_alph = "";
				$o_new  = "";

				// ˅˄↓↑

				switch ($sort)
				{
					case 1:
						{
							$order  = "`name`";
							$o_alph = '<div class="sort-param-current" onclick="SortBy(2)" title="От А до Я">алфавиту ˅</div>';
							$o_new  = '<div class="sort-param" onclick="SortBy(3)">новизне</div></div>';
						}
						break;
					case 2:
						{
							$order  = "`name` DESC";
							$o_alph = '<div class="sort-param-current" onclick="SortBy(1)" title="От Я до А">алфавиту ˄</div>';
							$o_new  = '<div class="sort-param" onclick="SortBy(3)">новизне</div></div>';
						}
						break;
					case 3:
						{
							$order  = "`id`";
							$o_alph = '<div class="sort-param" onclick="SortBy(1)">алфавиту</div>';
							$o_new  = '<div class="sort-param-current" onclick="SortBy(4)" title="От старых к новым">новизне ˅</div></div>';
						}
						break;
					case 4:
						{
							$order  = "`id` DESC";
							$o_alph = '<div class="sort-param" onclick="SortBy(1)">алфавиту</div>';
							$o_new  = '<div class="sort-param-current" onclick="SortBy(3)" title="От новых к старым">новизне ˄</div></div>';
						}
						break;
					default:
						{
							$_SESSION['order'] = 1;

							$order  = "`name`";
							$o_alph = '<div class="sort-param-current" onclick="SortBy(2)" title="По возрастанию">алфавиту ˅</div>';
							$o_new  = '<div class="sort-param" onclick="SortBy(3)">новизне</div></div>';
						}
						break;
				}

				$content .= '<div class="sort">Сортировать по:'. $o_alph . $o_new .'</div><br>';

				$items_cnt = T_GetData("SELECT COUNT(`id`) AS `cnt` FROM `shop_items` WHERE `subcategory` = $id AND `is_public` = 1", "cnt");

				if ($items_cnt > 0)
				{
					$q   = T_GetData("SELECT `id`, `name`, `description`, `price` FROM `shop_items` WHERE `subcategory` = $id AND `is_public` = 1 ORDER BY $order LIMIT $pageDB, 20", "", 1);
					$cnt = mysqli_num_rows($q);
	
					if ($cnt > 0)
					{
						$content   .= '<div class="center"><div class="card-list">';
						foreach ($q as $item)
						{
							$id    = (int) $item['id'];
							$name  = T_CutText($item['name'], 40);
							$descr = T_CutText(T_AntiBBParse(T_ClearSmiles($item['description'])), 108);
							$price = $item['price'];

							// Получение картинки товара

							$image = T_GetData("SELECT `file_name`, `hash` FROM `shop_items_images` WHERE `item_id` = $id AND `is_default` = 1 LIMIT 1");

							if (!empty($image))
							{
								$dname  = StrToLower(MB_SubStr($image['file_name'], 0, 1, 'UTF-8'));
								$dhname = StrToLower(MB_SubStr($image['hash'], 0, 2, 'UTF-8'));
								$image  = "/files/shop/" . $dname . "/" . $dhname . "/" . $image['file_name'];
							}
							else
								$image = "/templates/" . T_Template() . "/assets/img/no_image.png";

							// Вывод

							$semantic = $id . '-' . T_Semantic($item['name']);

							//$content   .= '<div class="shop-items" onclick="location.href=\'/shop/item/' . $semantic . '\'"><img class="shop-items-image" src="' . $image . '" alt="' . $name . '"><div class="shop-items-name">' . $name . '</div><div class="shop-items-price">' . $price . ' ₽</div></div>';

							$content   .= '<div class="card" onclick="location.href=\'/shop/item/' . $semantic . '\'"><img class="card-cover" src="' . $image . '" alt="' . $name . '"><p class="card-title">' . $name . '</p><p class="card-description">' . $descr . '</p><p class="card-price">' . $price . ' ₽</p></div>';
						}
						$content   .= '</div></div><br>';
					}
					else
						$content .= '<div class="center"><strong>Нет товаров.</strong></div>';

					// Пагинация

					$page_cnt = ceil($items_cnt / 20);

					if ($page < $page_cnt)
						$content .= "<div class=\"shop-link\" onclick=\"location.href='/shop/subcategory/" . $full_param . "/page/" . ($page + 1) . "'\">Далее</div>";

					if ($page > $page_cnt || $page > 1)
						$content .= "<div class=\"shop-link\" onclick=\"location.href='/shop/subcategory/" . $full_param . "/page/" . ($page - 1) . "'\">Назад</div>";
				}
				else
					$content .= '<div class="center"><strong>Нет товаров.</strong></div>';
			}
			break;
		// Просмотр товаров
		case "item":
			{
				$id  = (int) CURRENT_PARAM;

				$item = T_GetData("SELECT * FROM `shop_items` WHERE `id` = $id");

				if (!empty($item) && ($item['is_public'] == 1 || T_Admin()))
				{
					// Валидация семантической ссылки
					$full_param = CURRENT_PARAM;
					$real_param = $id . "-" . T_Semantic($item['name']);

					// Если ссылка правильно составлена - выводим данные
					if ($full_param == $real_param)
						$_SiteTitle = T_CutText($item['name'], 50);
					else
					{
						$_SiteTitle = "Ошибка!";
						$content    = '<div class="center">Такого товара не существует!</div>';
						break;
					}
				}
				else
				{
					$_SiteTitle = "Ошибка!";
					$content    = '<div class="center">Такого товара не существует!</div>';
					break;
				}

				// Информация о товаре

				$name           = $item['name'];
				$count          = (int) $item['count'];
				$price          = $item['price'];
				$is_pub         = (int) $item['is_public'];
				$description    = T_BBParse($item['description']);
				$category_id    = (int) $item['category'];
				$subcategory_id = (int) $item['subcategory'];

				// Получение изображения

				$image = T_GetData("SELECT `file_name`, `hash` FROM `shop_items_images` WHERE `item_id` = $id AND `is_default` = 1 LIMIT 1");

				if (!empty($image))
				{
					$dname  = StrToLower(MB_SubStr($image['file_name'], 0, 1, 'UTF-8'));
					$dhname = StrToLower(MB_SubStr($image['hash'], 0, 2, 'UTF-8'));
					$image  = "/files/shop/" . $dname . "/" . $dhname . "/" . $image['file_name'];
				}
				else
					$image = "/templates/" . T_Template() . "/assets/img/no_image.png";

				// Работа с категориями

				$category    = T_GetData("SELECT `name` FROM `shop_categories` WHERE `id` = $category_id", "name");
				$subcategory = T_GetData("SELECT `name` FROM `shop_subcategories` WHERE `id` = $subcategory_id", "name");

				// Вывод статуса публикации администраторам

				$is_public = "";
				if (T_Admin())
				{
					if ($is_pub)
						$is_public = "<strong>Статус:</strong> опубликован<br>";
					else
						$is_public = "<strong>Статус:</strong> не опубликован<br>";
				}

				// Работа с отзывами и средней оценкой

				/*$feed_cnt    = (int) T_GetData("SELECT COUNT(`id`) AS `cnt` FROM `feedbacks` WHERE `item` = $id AND `status` = 1", "cnt");

				$mid_mark       = 0;
				if (!empty($feed_cnt))
				{
					$q = T_GetData("SELECT `mark` FROM `feedbacks` WHERE `item` = $id AND `status` = 1", "", 1);

					foreach ($q as $feed)
						$mid_mark += $feed['mark'];

					$mid_mark     /= $feed_cnt;
				}*/

				// Вывод звёздочек
				//$stars = '<div class="shop-item-feed-stars">' . T_MarkOut($mid_mark) . '</div>';

				// Работа с наличием, выбором количества и добавлением в корзину

				$selector = "";

				if (T_Authorized())
				{
					$add_cart = "";
					$q = T_GetData("SELECT `id` FROM `shop_cart` WHERE `user` = $_userID AND `item_id` = $id", "id");
					if (empty($q))
						$add_cart = '<div id="shop-cart-add-link" onclick="location.href=\'/shop/cart/add/' . $id . '/1\'" title="Добавить в корзину" class="shop-cart-add"><img src="/templates/' . T_Template() . '/assets/img/cart.png" alt="Корзина"><div class="shop-cart-add-text">В КОРЗИНУ</div></div>';
					else
						$add_cart = '<div id="shop-cart-add-link" class="shop-cart-add-full"><img src="/templates/' . T_Template() . '/assets/img/cart.png" alt="Корзина"><div class="shop-cart-add-text">В КОРЗИНЕ</div></div>';

					if ($count > 0)
						$selector = '<div class="shop-item-plus-minus"><div class="shop-item-minus" item="' . $id . '">-</div><input type="text" class="shop-item-selector" value="1" readonly><div class="shop-item-plus" item="' . $id . '" max="' . $count . '">+</div></div><strong>В наличии:</strong> ' . $count . ' шт.<br>' . $add_cart;
					else
						$selector = '<div class="shop-item-no">Нет в наличии</div>';
				}
				else
				{
					if ($count > 0)
						$selector = '<div class="shop-item-plus-minus">' . $count . ' шт.</div><a href="/auth">Авторизуйтесь</a>, чтобы купить этот товар.';
					else
						$selector = '<div class="shop-item-no">Нет в наличии</div>';
				}

				// Вывод

				$item_semantic = $id . '-' . T_Semantic($name);
				$cat_semantic  = $category_id . '-' . T_Semantic($category);
				$scat_semantic = $subcategory_id . '-' . T_Semantic($subcategory);

				$content = '<div class="shop-item-head"><center><img class="pruduct-card-cover" src="' . $image . '" alt="' . $name . '" onclick="ShowFull($(this).attr(\'src\'))" title="Приблизить"></center><div class="pruduct-card-section-2"><p class="pruduct-card-price">' . $price . ' ₽</p><input class="pruduct-card-btn-buy" type="button" value="Купить" onclick="location.href=\'/shop/cart/add/' . $id . '/1\'"></div><div class="pruduct-card-characteristic"><p class="pruduct-card-characteristic-title">Описание:</p><pre class="pruduct-card-characteristic-desc">' . $description . '</pre></div></div><br><div>' . $is_public . '</div></div><div class="shop-item-description"><strong>Категория:</strong> <a href="/shop/category/' . $cat_semantic . '">' . $category . '</a><br><strong>Подкатегория:</strong> <a href="/shop/subcategory/' . $scat_semantic . '">' . $subcategory . '</a></div><strong>В наличии:</strong> ' . $count . ' шт.<br><div id="shop-image-show-full"><img src="/templates/' . T_Template() . '/assets/img/cancel.png" alt="Закрыть" title="Закрыть" onclick="CloseFull()" id="shop-image-show-full-close"></div>';
			}
			break;
		default:
			{
				// Главная страница магазина

				$content = '<div class="center">Добро пожаловать в наш Магазин!<br><br>Чтобы приступить к ознакомлению с каталогом товаров выберите в одну из категорий:<br><br></div>';

				// Получение и вывод категорий (при наличии)

				$q    = T_GetData("SELECT `id`, `name` FROM `shop_categories` WHERE `is_public` = 1", "", 1);
				$cnt  = mysqli_num_rows($q);
				$cats = "";

				if ($cnt > 0)
				{
					foreach ($q as $cat)
					{
						$id      = (int) $cat['id'];
						$name    = $cat['name'];

						$sub_cnt = T_GetData("SELECT COUNT(`id`) AS `cnt` FROM `shop_subcategories` WHERE `cat_id` = $id AND `is_public` = 1 ORDER BY `name`", "cnt");

						$content .= '<div class="shop-link" onclick="location.href=\'/shop/category/' . $id . '-' . T_Semantic($name) . '\'">' . $name . ' (' . $sub_cnt . ')</div>';
					}
				}
				else
					$content .= '<div class="center"><strong>Нет категорий.</strong></div>';
			}
			break;
	}

	require("templates/" . T_Template() . "/layouts/header.php");
	//echo "<div class=\"content\">$content</div>";
?>
	<section class="wrapper">
        <div class="white-container">
            <h1><img src="/templates/<?=T_Template()?>/assets/icons/icon_for_title.svg" class="icon-for-title" alt="MTS_"><?=$_SiteTitle?></h1>
			<div class="content"><?=$content?></div>
		</div>
    </section>
