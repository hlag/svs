<?php
/*
 * Created on 08.11.2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class Checker
{
	public function checkEmail($email)
	{
		$regex = '/^[a-z][a-z0-9]*((-|_|\.)[a-z0-9]+)*@[a-z0-9]+((-|_|\.)[a-z0-9]+)*\.[a-z]{2,4}$/i';
		if(preg_match($regex, $email))
			return TRUE;
		else
			return FALSE;
	}
}
?>
