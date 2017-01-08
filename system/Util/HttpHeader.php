<?php

namespace Application\Util;

/**
* 
*/
class HttpHeader
{
	public static function setHeader($options)
	{
		if(!is_array($options)){
			return;
		}
		if(count($options) < 1){
			return;
		}

		foreach ($options as $header => $value) {
			if(!empty($value)){
				$value = ": {$value}";
			}

			header("{$header}{$value}");
		}

	}	
}