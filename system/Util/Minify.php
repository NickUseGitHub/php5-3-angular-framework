<?php 
namespace Application\Util;

use MatthiasMullie\Minify\JS;
use MatthiasMullie\Minify\CSS;

/**
* Minify
*/
class Minify
{
	
	public static function minifyJS($src, $des)
	{

		if(empty($src) || !is_array($src) || empty($des))
			return false;

		$minifier_js = new JS();

		foreach ($src as $name => $src_file) {
			$minifier_js->add($src_file);
		}
		
		$minifier_js->minify($des);

		$minifier_js = null;

		return true;

	}

	public static function minifyCss($src, $des)
	{

		if(empty($src) || !is_array($src) || empty($des))
			return false;

		$minifier_css = new CSS();

		foreach ($src as $name => $src_file) {
			$minifier_css->add($src_file);
		}
		
		$minifier_css->minify($des);

		$minifier_css = null;

		return true;

	}

}