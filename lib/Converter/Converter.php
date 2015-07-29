<?php
/*
 * Created on 27.06.2006
 *
 * Author: Mats
 * 
 * File: Converter.php
 * 
 */
class Converter
{
	function Converter()
	{
		
	}
	
	function brtonl($text)
	{
		return str_replace("<br>","\r\n",$text);
	}
	
	function nltobr($text)
	{
		return str_replace("\r\n","<br>",$text);
	}
	
	public static function number_format($number, $language, $nachkommastellen=2)
	{
		$newNumber=0;
		switch ($language)
		{
			case 1:
				$newNumber = number_format($number,$nachkommastellen,",",".");
				break;
				
		}
		return $newNumber;
	}
}
?>
