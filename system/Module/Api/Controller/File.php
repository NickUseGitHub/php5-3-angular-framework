<?php 

namespace Application\Module\Api\Controller;

use Application\Module\Api\Controller\BaseController;
use Application\Helper\UrlHelper;
use Application\Library\ImageRender;

/**
* File
*/
class File extends BaseController
{

    public function saveImageFromCanvas($param){

		$path_file = $param['path_file'];
		$partialName = $param['partialName'];

		$upload_dir = DATA_PATH."uploads/{$path_file}/";  //implement this function yourself
		$img = $param['image'];
		$img = str_replace('data:image/png;base64,', '', $img);
		$img = str_replace(' ', '+', $img);
		$data = base64_decode($img);
		$file_name = "test";
		$file_name = md5(date('Y_m_d_H_i_s') . $file_name);
		$extension_file = ".png";
		$file = $upload_dir.$file_name . $extension_file;
		$success = file_put_contents($file, $data);
		
		$msg = "";
		$error_msg = "";
		$response = new \stdClass();
		if($success){
			$msg = "ok";
			ImageRender::changeImageSizeFromBase64($file);
		}else {
			$msg = "nok";
			$error_msg = "save image failed";
		}

		$file_obj = new \stdClass();
		$file_obj->file_name = $file_name . $extension_file;
		$file_obj->partialName = $partialName;

		$response->msg = $msg;
		$response->file_obj = $file_obj;
		$response->error_msg = $error_msg;
		return $response;

	}

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

	public function removeFiles($param)
	{
		$files = $param['files'];
		$path_file = $param['path_file'];

		$response = new \stdClass();
		if (empty($files) || !is_array($files) || count($files) === 0) {

			$response->msg = "nok";
			$response->error_msg = "files is null or empty.";
			return $response;

		}

		if(empty($path_file)){

			$response->msg = "nok";
			$response->error_msg = "path_file is null or empty.";
			return $response;

		}

		foreach ($files as $file_name) {
			$target = DATA_PATH."uploads/{$path_file}/{$file_name}";

			if(!file_exists($target)){
				continue;
			}

			@unlink($target);
		}

		$response->msg = "ok";
		$response->error_msg = "remove success";

		return $response;

	}

}