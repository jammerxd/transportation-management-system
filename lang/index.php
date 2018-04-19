<?php
if(!isset($lang))
{
	if(!isset($_COOKIE['lang']))
	{
		setcookie('lang',"en",time() + (86400 * 30), "/",true);
		$langShort = 'en';
	}
	else
	{
		$langShort = $_COOKIE['lang'];
	}
}
require($langShort . ".php");
?>