<?php 

namespace Application\Module\CmsApi\Controller;

use Application\Module\CmsApi\Controller\BaseController;
use Application\Library\Proxy\Admin as AdminProx;
use Application\Helper\UrlHelper;

/**
* Test
*/
class Admin extends BaseController
{

	public function create($param){

		$this->di->add("admin", new \Application\Module\CmsApi\Model\Admin());
		
		$data = array();
		$data['username'] = "admin";
		$data['name'] = "Super Admin";
		$data['password'] = "1234";
		$data['status'] = 1;
		$data['type'] = AdminProx::getTypeSuperAdmin();
		AdminProx::addAdmin($this->di->get("admin"), $data);

		$responseMsg = array();
		$responseMsg['msg'] = $message;
		return $responseMsg;

	}

	public function pingToken(){

		$token = $this->token;
		$this->di->add("admin", new \Application\Module\CmsApi\Model\Admin());
		$response = array();

		if(empty($token)){
			$response['msg'] = "nok";
			$response['error_msg'] = "cannot get token";
			return $response;
		}

		$user_obj = AdminProx::getUserFromToken($this->di->get("admin"), $token);
		if(empty($user_obj)){
			$response['msg'] = "nok";
			$response['error_msg'] = "token is timeout.";
			return $response;
		}

		$response['msg'] = "ok";
		$response['error_msg'] = "";
		return $response;

	}

	public function getRoutes($param)
	{
		$routes = array(
			// "Dashboard" => array(
			// 	"path_url" => "/dashboard",
			// 	"templateUrl" => UrlHelper::static_site_url("assets/js/Cms/Admin/Dashboard/template/dashboard.html"),
			// 	"controller" => "DashboardController",
			// 	"ctrDependencies" => UrlHelper::static_site_url("assets/js/Cms/Admin/Dashboard/DashboardController.js"),
			// ),
			// "Banners" => array(
			// 	"path_url" => "/banners",
			// 	"templateUrl" => UrlHelper::static_site_url("assets/js/Cms/Admin/Banners/template/banners.html"),
			// 	"controller" => "BannersController",
			// 	"ctrDependencies" => UrlHelper::static_site_url("assets/js/Cms/Admin/Banners/BannersController.js"),
			// ),
			// "Products" => array(
			// 	"path_url" => "/products",
			// 	"templateUrl" => UrlHelper::static_site_url("assets/js/Cms/Admin/Products/template/products.html"),
			// 	"controller" => "ProductsController",
			// 	"ctrDependencies" => UrlHelper::static_site_url("assets/js/Cms/Admin/Products/ProductsController.js"),
			// ),
			// "BillOrders" => array(
			// 	"path_url" => "/billorders",
			// 	"templateUrl" => UrlHelper::static_site_url("assets/js/Cms/Admin/Billorders/template/billorders.html"),
			// 	"controller" => "BillorderController",
			// 	"ctrDependencies" => UrlHelper::static_site_url("assets/js/Cms/Admin/Billorders/BillordersController.js"),
			// ),
			"Members" => array(
				"path_url" => "/members",
				"templateUrl" => UrlHelper::static_site_url("dist/js/Cms/Admin/Members/template/members.html"),
				"controller" => "MemberController",
				"ctrDependencies" => UrlHelper::static_site_url("dist/js/Cms/Admin/Members/MemberController.js"),
			),
		);

		return $routes;
	}

	public function getMenus($param)
	{
		$menus = array(
			// "Dashboard" => array(
			// 	"icon" => "fa-dashboard",
			// 	"name" => "Dashboard",
			// 	"path" => "#/dashboard",
			// 	"children" => array(
			// 		"DashboardLV2" => array(
			// 			"icon" => "fa-dashboard",
			// 			"name" => "Dashboard LV2",
			// 			"path" => "#/dashboardLV2",
			// 			"children" => array(
			// 				"DashboardLV3" => array(
			// 					"icon" => "fa-dashboard",
			// 					"name" => "Dashboard LV3",
			// 					"path" => "#/dashboardLV3",
			// 				),
			// 			),
			// 		),
			// 	),
			// ),
			// "Banners" => array(
			// 	"icon" => "fa-picture-o",
			// 	"name" => "Banners",
			// 	"path" => "#/banners",
			// ),
			// "Products" => array(
			// 	"icon" => "fa-shirtsinbulk",
			// 	"name" => "Products",
			// 	"path" => "#/products",
			// ),
			// "Billorders" => array(
			// 	"icon" => "fa-usd",
			// 	"name" => "Bill & Orders",
			// 	"path" => "#/billorders",
			// ),
			"Members" => array(
				"icon" => "fa-user",
				"name" => "Members",
				"path" => "#/members",
			),
		);

		return $menus;
	}

	public function logout($param)
	{
		AdminProx::logout();

		$responseMsg = array();
		$responseMsg['msg'] = "ok";
		return $responseMsg;
	}

}