<?php
namespace WilokeListGoFunctionality\Frontend;

class ConvertTime{
	public static function toTimestamp($str){
		return strtotime($str);
	}
}