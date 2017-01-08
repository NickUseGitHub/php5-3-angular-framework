<?php  
namespace Application\Library;

use Gregwar\Image\Image;

class ImageRender {

	public function __construct(){}

	public static function changeImageSizeFromBase64($file){

		try { // Error handling

			Image::open($file)
			     ->scaleResize(2480, 3508)
				 ->brighness(-255)
				 ->contrast(-255)
			     // ->negate()
			     ->save($file);
			     return false;

		} catch(\Exception $e) {
		    // echo 'Error: ' . $e->getMessage();
		    return false;
		}

	}

}