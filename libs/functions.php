<?php
function limitStr($str,$lmt)
{
	if(strlen($str)>$lmt){
		$str=substr($str, 0,$lmt);
		return $str.="...";
	} else 
		return $str;
}






?>