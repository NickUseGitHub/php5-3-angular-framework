<?php 

namespace Application\Module\CmsApi\Controller;

use Application\Module\CmsApi\Controller\BaseController;
use Application\Helper\UrlHelper;

/**
* File
*/
class File extends BaseController
{

    public function uploadPic($param)
	{
		$path_file = $_POST['path_file'];

		if(empty($path_file)){
			$response = new \stdClass();
			$response->msg = "nok";
			$response->error_msg = "path_file is null or empty.";

			return $response;
		}


		ini_set('display_errors',1);

		$target_dir = DATA_PATH."uploads/{$path_file}/";

		if(isset($_FILES['file'])){

			$errors= array();
			$file_size = $_FILES['file']['size'];
			$file_tmp = $_FILES['file']['tmp_name'];
			$file_type = $_FILES['file']['type'];
			$file_ext=strtolower(end(explode('.',$_FILES['file']['name'])));
			$file_name = md5(date('Y_m_d_H_i_s') . basename($_FILES["file"]["name"]));
			$target_file = $target_dir . "{$file_name}.{$file_ext}";

			$expensions= array("jpeg","jpg","png");

			if(in_array($file_ext,$expensions)=== false){
				$errors[]="extension not allowed, please choose a JPEG or PNG file.";
			}

			if(empty($errors)==true) {
				if(move_uploaded_file($file_tmp, $target_file)){
					$response = new \stdClass();
					$response->file_name = "{$file_name}.{$file_ext}";
					$response->msg = "ok";
					$response->error_msg = "";

					return $response;
				}else{

					error_reporting(E_ALL);

					$response = new \stdClass();
					$response->msg = "nok";
					$response->error_msg = "cannot uploaded";
					return $response;
				}

				

			}else{
				$response = new \stdClass();
				$response->msg = "nok";
				$response->error_msg = "cannot uploaded";

				return $response;
			}
		}

		$response = new \stdClass();
		$response->msg = "nok";
		$response->error_msg = "cannot uploaded";
		return $response;
	}

	public function uploadFile($param)
	{
		$path_file = $_POST['path_file'];

		if(empty($path_file)){
			$response = new \stdClass();
			$response->msg = "nok";
			$response->error_msg = "path_file is null or empty.";

			return $response;
		}


		ini_set('display_errors',1);

		$target_dir = DATA_PATH."uploads/{$path_file}/";

		if(isset($_FILES['file'])){

			$errors= array();
			$file_size = $_FILES['file']['size'];
			$file_tmp = $_FILES['file']['tmp_name'];
			$file_type = $_FILES['file']['type'];
			$file_ext=strtolower(end(explode('.',$_FILES['file']['name'])));
			$file_name = md5(date('Y_m_d_H_i_s') . basename($_FILES["file"]["name"]));
			$target_file = $target_dir . "{$file_name}.{$file_ext}";

			$expensions= array("pdf","zip");

			if(in_array($file_ext,$expensions)=== false){
				$errors[]="extension not allowed, please choose a JPEG or PNG file.";
			}

			if(empty($errors)==true) {
				if(move_uploaded_file($file_tmp, $target_file)){
					$response = new \stdClass();
					$response->file_name = "{$file_name}.{$file_ext}";
					$response->msg = "ok";
					$response->error_msg = "";

					return $response;
				}else{

					error_reporting(E_ALL);

					$response = new \stdClass();
					$response->msg = "nok";
					$response->error_msg = "cannot uploaded";
					return $response;
				}

				

			}else{
				$response = new \stdClass();
				$response->msg = "nok";
				$response->error_msg = "cannot uploaded";

				return $response;
			}
		}

		$response = new \stdClass();
		$response->msg = "nok";
		$response->error_msg = "cannot uploaded";
		return $response;
	}

	public function removeFile($param)
	{
		$file_name = $param['file_name'];
		$path_file = $param['path_file'];

		$response = new \stdClass();
		if(empty($file_name)){

			$response->msg = "nok";
			$response->error_msg = "file_name is null or empty.";
			return $response;

		}

		if(empty($path_file)){

			$response->msg = "nok";
			$response->error_msg = "path_file is null or empty.";
			return $response;

		}

		$target = DATA_PATH."uploads/{$path_file}/{$file_name}";

		if(!file_exists($target)){
			
			$response->msg = "nok";
			$response->error_msg = "file does not exist";
			return $response;

		}

		@unlink($target);

		$response->msg = "ok";
		$response->error_msg = "remove success";

		return $response;

	}
    
}