<?php

namespace Application\Util;

/**
* 
*/
class Regex
{
	
	public static function match($find, $pattern)
	{
		$matches = null;
		if(empty($find)){
			return $matches;
		}

		preg_match($pattern, $find, $matches);
		
		return $matches;
	}
	
}