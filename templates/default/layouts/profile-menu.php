<?php
		switch (CURRENT_ACTION)
		{
			case "profile":
				{
					$_currentS  = 1;
				}
				break;
			case "account":
				{
					switch (CURRENT_PARAM)
					{
						case "security":
							$_currentS  = 2;
							break;
						case "sessions":
							$_currentS  = 3;
							break;
					}
				}
				break;
			default:
				{
					$_currentS  = 0;
				}
				break;
		}
?>
		<div id="pa-menu">
			<div class="pa-menu-title">Профиль</div>
			<div class="pa-menu-item<?=($_currentS == 1 ? "-s" : "\" onclick=\"location.href='/pa/profile'")?>">Профиль</div>
			<div class="pa-menu-title">Учётная запись</div>
			<div class="pa-menu-item<?=($_currentS == 2 ? "-s" : "\" onclick=\"location.href='/pa/account/security'")?>">Безопасность</div>
			<div class="pa-menu-item<?=($_currentS == 3 ? "-s" : "\" onclick=\"location.href='/pa/account/sessions'")?>">Сеансы</div>
		</div>