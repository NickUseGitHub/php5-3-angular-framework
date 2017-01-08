(function(){
	'use strict';

	angular.module('app')
		.controller('BannersController', 
			['$scope', 'HttpService', 'ModalService'
			, function($scope, HttpService, ModalService){
			
			$scope.category_code = "BANNER";
			$scope.category_obj = null;

			$scope.search = "";
			$scope.page = 1;
			$scope.amount_per_page;
			$scope.total_page;

			$scope.list = [];

			$scope.init = function () {

				//set $scope.category_obj
				$scope.getCategoryId().then(function(category_obj){
					
					$scope.category_obj = category_obj;
					return $scope.getList();

				}, function(err){
					console.log("scope.getCategoryId()", err);
				}).then(function(data){
					
					$scope.list = data.list;
					$scope.total_page = data.total_page;

				}, function(err){
					console.log("init :: scope.getList()", err);
				});

			};

			$scope.getCategoryId = function(){

				var code = $scope.category_code;

				var called_url = site_url("cms/api/Category/" + code);
				var httpConfig = {
					 url: called_url
					,method: "GET"
				};

				return HttpService.callApi(httpConfig);

			};

			$scope.getList = function () {
				
				var category_id = is.empty($scope.category_obj)? "" : $scope.category_obj.id;
				var search = $scope.search;
				var page = $scope.page;
				var amount_per_page = $scope.amount_per_page;

				var called_url = site_url("cms/api/Content/getDataList");
				var httpConfig = {
					 url: called_url
					,method: "POST"
					,data: {
						category_id: category_id,
						search: search,
						page: page,
						amount_per_page:amount_per_page,
					}
				};

				return HttpService.callApi(httpConfig);

			}

			$scope.listMoved = function (list, index) {
				list.splice(index, 1);

				console.log("list", $scope.list);
			};

			$scope.filter = function () {
				$scope.getList().then(function(data){
					
					$scope.list = data.list;
					$scope.total_page = data.total_page;

				}, function(err){
					console.log("init :: scope.getList()", err);
				});
			};

			$scope.toggleStatus = function (item){

				var id = item.id;
				var called_url = site_url("cms/api/Content/toggleStatus");
				var httpConfig = {
					 url: called_url
					,method: "POST"
					,data: {
						id: id,
					}
				};

				HttpService.callApi(httpConfig).then(function(data){

					if(data.msg == "nok"){
						console.log("toggleStatus ::", data.error_msg);
						return;
					}

					item.status = item.status == 1? 0 : 1;

				}, function(err){
					console.log("toggleStatus ::", err);
				});

			};

			$scope.edit = function(id){

				var category_id = $scope.category_obj.id;

				var field = {
					id: id,
					category_id: category_id,
				};

				ModalService.showModal({
					templateUrl: "modal.html",
					controller: "BannerModalController",
					inputs:{params:{field:field},}
				}).then(function(modal) {
					// The modal object has the element built, if this is a bootstrap modal
					// you can call 'modal' to show it, if it's a custom modal just show or hide
					// it as you need to.
					modal.element.modal();
					modal.close.then(function() {
						
						//new get
						$scope.getList().then(function(data){
					
							$scope.list = data.list;
							$scope.total_page = data.total_page;

						}, function(err){
							console.log("init :: scope.getList()", err);
						});

					});
				});

			};

			$scope.delete = function (id){

				if (!confirm('Are you sure you want to delete this?'))
					return;

				var id = id;
				var called_url = site_url("cms/api/Content/delete");
				var httpConfig = {
					 url: called_url
					,method: "POST"
					,data: {
						id: id,
						path_file: "banner",
					}
				};

				HttpService.callApi(httpConfig).then(function(data){

					if(data.msg == "nok"){
						console.log("toggleStatus ::", data.error_msg);
						return;
					}

					return $scope.getList();

				}, function(err){
					console.log("toggleStatus ::", err);
				}).then(function(data){
					
					$scope.list = data.list;
					$scope.total_page = data.total_page;

				}, function(err){
					console.log("delete() -> scope.getList()", err);
				});

			};

			$scope.orderList = function(list){

				var page = $scope.page;
				var amount_per_page = $scope.amount_per_page;
				var list_for_order = [];

				angular.forEach(list, function(obj, key) {
					
					var id = obj.id;
					var temp_order = ((page - 1) * amount_per_page) + 1 + key;

					list_for_order.push({id:id, order_id: temp_order});

				});

				var called_url = site_url("cms/api/Content/order");
				var httpConfig = {
					 url: called_url
					,method: "POST"
					,data: {list_for_order:list_for_order}
				};

				HttpService.callApi(httpConfig).then(function(data){

					if(data.msg == "nok"){
						console.log("orderList - " + data.error_msg);
						return;
					}

					return $scope.getList();

				}, function(err){
					console.log("orderList()", err);
				}).then(function(data){
					
					$scope.list = data.list;
					$scope.total_page = data.total_page;

				}, function(err){
					console.log("orderList() -> scope.getList()", err);
				});

			};

			$scope.site_url = function(url){
				return site_url(url);
			};

			$scope.init();

		}]);

	angular.module('app')
		.controller('BannerModalController', 
			['$scope', '$q', '$parse', 'HttpService', 'FileUploadService', 'close', 'params'
			,function($scope, $q, $parse, HttpService, FileUploadService, close, params) {
 
 			$scope.id;
			$scope.field;

			$scope.init = function(){
			 	$scope.id = params.field.id;

			 	//binding data
			 	$scope.getData($scope.id).then(function(data){
			 		$scope.field = data;
			 	});

			};

			$scope.getData = function(id){
			 	var defered = $q.defer();

			 	if (is.empty(id) || is.null(id) ) {
			 		defered.reject(null);
			 		return defered.promise;
			 	}

			 	var called_url = site_url("cms/api/Content/" + id);
				var httpConfig = {
					 url: called_url
					,method: "GET"
				};

				HttpService.callApi(httpConfig).then(function(data){
					defered.resolve(data);

				}, function(err){
					defered.reject(data);
				});

			 	return defered.promise;

			};
			 
			$scope.save = function(field) {
				
				field.id = $scope.id;
				field.category_id = params.field.category_id;

				var called_url = site_url("cms/api/Content/createOrUpdate");
				var httpConfig = {
					 url: called_url
					,method: "POST"
					,data: field
				};

				HttpService.callApi(httpConfig).then(function(data){
			    	close(data, 500); // close, but give 200ms for bootstrap to animate
				}, function(err){
					$scope.message = err;
				});

			};

			$scope.uploadSingleFile = function function_name(file, errFiles, name_file) {
				
				var path_file = "banner";
				var file_name = $parse(name_file);
				file_name = file_name($scope);

				FileUploadService
					.removeFile(file_name, path_file).then(function(data){
						console.log("uploadSingleFile() -> removeFile(success)", data);
						return FileUploadService.uploadSingleFile(file, errFiles, path_file);
					}, function(err){
						console.log("uploadSingleFile() -> removeFile(failed)", err);
					}).then(function(data){
						
						var model = $parse(name_file);
						model.assign($scope, data.file_name);

					}, function(err){
						console.log("uploadSingleFile->FileUploadService.uploadSingleFile()", err);
					});

			};

			$scope.uploadSingleImageFile = function(file, errFiles, name_file) {
				
				var path_file = "banner";
				var file_name = $parse(name_file);
				file_name = file_name($scope);

				FileUploadService
					.removeFile(file_name, path_file).then(function(data){
						console.log("uploadSingleImageFile() -> removeFile(success)", data);
						return FileUploadService.uploadSingleImageFile(file, errFiles, path_file);
					}, function(err) {
						console.log("uploadSingleImageFile() -> removeFile(failed)", err);
					}).then(function(data){
						
						var model = $parse(name_file);
						model.assign($scope, data.file_name);

					}, function(err){
						console.log("uploadSingleImageFile->FileUploadService.uploadSingleImageFile()", err);
					});

			};

			$scope.close = function(field) {
				close(field, 500); // close, but give 500ms for bootstrap to animate
			};

			$scope.site_url = function(url){
				return site_url(url);
			};

			$scope.init();

		}]);

})();