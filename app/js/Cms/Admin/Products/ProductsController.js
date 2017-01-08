(function(){
	'use strict';

	angular.module('app')
		.controller('ProductsController', 
			['$scope', 'HttpService', 'ModalService'
			, function($scope, HttpService, ModalService){

				$scope.search = "";
				$scope.page = 1;
				$scope.amount_per_page;
				$scope.total_page;

				$scope.product_category_id;

				//for product list
				$scope.list = [];

				$scope.init = function () {
				};

				$scope.$on('call-products', function(event, args){ 
					$scope.product_category_id = args.product_category_id;
					$scope.filter();
				});

				$scope.listMoved = function (list, index) {
					list.splice(index, 1);

					console.log("list", $scope.list);
				};

				$scope.getList = function () {
					
					if(is.empty(product_category_id))
						return null;

					var product_category_id = $scope.product_category_id;
					var search = $scope.search;
					var page = $scope.page;
					var amount_per_page = $scope.amount_per_page;

					var called_url = site_url("cms/api/Product/getDataList");
					var httpConfig = {
						 url: called_url
						,method: "POST"
						,data: {
							product_category_id: product_category_id,
							search: search,
							page: page,
							amount_per_page:amount_per_page,
						}
					};

					return HttpService.callApi(httpConfig);

				}

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
					var called_url = site_url("cms/api/Product/toggleStatus");
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
					
					var product_category_id = $scope.product_category_id;

					var field = {
						id: id,
						product_category_id: product_category_id,
					};

					ModalService.showModal({
						templateUrl: "product-modal.html",
						controller: "ProductModalController",
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
								console.log("editProduct() -> scope.getList()", err);
							});

						});
					});

				};

				$scope.delete = function(id){
					
					if (!confirm('Are you sure you want to delete this?'))
						return;

					var id = id;
					var called_url = site_url("cms/api/Product/delete");
					var httpConfig = {
						 url: called_url
						,method: "POST"
						,data: {
							id: id,
							path_file: "product",
						}
					};

					HttpService.callApi(httpConfig).then(function(data){

						if(data.msg == "nok"){
							console.log("deleteProduct ::", data.error_msg);
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

					console.log("list_for_order", list_for_order);

					var called_url = site_url("cms/api/Product/order");
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
		.controller('ProductModalController', 
			['$scope', '$q', '$parse', 'HttpService', 'FileUploadService', 'close', 'params', 
			function($scope, $q, $parse, HttpService, FileUploadService, close, params){
			
				$scope.id;
				$scope.field;

				$scope.init = function () {
					
					$scope.id = params.field.id;

				 	//binding data
				 	$scope.getData($scope.id).then(function(data){
				 		
				 		$scope.field = data;

				 	});

				};

				$scope.getData = function(id){

					var defered = $q.defer();

				 	if(is.empty(id)){
				 		defered.reject(null);
				 		return defered.promise;
				 	}

				 	var called_url = site_url("cms/api/Product/getData");
					var httpConfig = {
						 url: called_url
						,method: "POST"
						,data: {id:id}
					};

					HttpService.callApi(httpConfig).then(function(data){

						//change string into float
						data.price = is.empty(data.price)? null : parseFloat(data.price);
						defered.resolve(data);

					}, function(err){
						defered.reject(data);
					});

				 	return defered.promise;

				};

				$scope.save = function(field) {

					field.id = $scope.id;
					field.product_category_id = is.empty(params.field.product_category_id) || is.undefined(params.field.product_category_id)? "" : params.field.product_category_id;

					var called_url = site_url("cms/api/Product/createOrUpdate");
					var httpConfig = {
						 url: called_url
						,method: "POST"
						,data: {field:field}
					};

					HttpService.callApi(httpConfig).then(function(data){
				    	close(data, 500); // close, but give 200ms for bootstrap to animate
					}, function(err){
						$scope.message = err;
					});

				};

				$scope.uploadSingleImageFile = function(file, errFiles, name_file) {
					
					var path_file = "product";
					var file_name = $parse(name_file);
					file_name = file_name($scope);

					console.log(file_name);

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
					close(null, 500); // close, but give 500ms for bootstrap to animate
				};

				$scope.site_url = function(url){
					return site_url(url);
				};

				$scope.init();

			}]);

	angular.module('app')
		.directive('productCategory', [function(){
			// Runs during compile
			return {
				scope: {},
				controller: 
				['$scope', '$rootScope', 'HttpService', 'ModalService',
				function($scope, $rootScope, HttpService, ModalService) {

					$scope.parent_id;
					$scope.product_category_list = [];
					$scope.sub_product_category_list = [];

					$scope.init = function(){

						$scope.getSubProductCategory($scope.parent_id).then(function(data){
							$scope.sub_product_category_list = data.list;
						}, function (err) {
							console.log("productCategory:: init() -> getSubProductCategory() - ", err);
						});

					};

					$scope.subProductCategoryListMoved = function (sub_product_category_list, index) {
						sub_product_category_list.splice(index, 1);
					};

					$scope.callProductCategoryNoParent = function () {
						$scope.product_category_list = [];
						$scope.parent_id = '';
						$scope.callProducts('');

						$scope.getSubProductCategory('').then(function(data){
							$scope.sub_product_category_list = data.list;
						}, function (err) {
							console.log("productCategory:: callProductCategoryNoParent() - ", err);
						});
					};

					$scope.callSubProductCategoryList = function(product_category, index){

						$scope.parent_id = product_category.id;
						$scope.callProducts(product_category.id);

						index++;
						if(index == $scope.product_category_list.length)
							return;

						var how_many = $scope.product_category_list.length - index;

						$scope.product_category_list.splice(index, how_many);

						$scope.getSubProductCategory(product_category.id).then(function(data){
							$scope.sub_product_category_list = data.list;
						}, function (err) {
							console.log("productCategory:: init() -> getSubProductCategory() - ", err);
						});

					};

					$scope.addIntoProductCategoryList = function (sub_product_category, index) {
						
						$scope.sub_product_category_list = [];

						$scope.product_category_list.push({
							id: sub_product_category.id,
							name:sub_product_category.name,
						});

						$scope.parent_id = sub_product_category.id;
						$scope.callProducts(sub_product_category.id);
						$scope.getSubProductCategory($scope.parent_id).then(function(data){
							$scope.sub_product_category_list = data.list;
						}, function (err) {
							console.log("productCategory::addIntoProductCategoryList(101) - ", err);
						});

					}

					$scope.getSubProductCategory = function(parent_id){

						var called_url = site_url("cms/api/ProductCategory/getDataList");
						var httpConfig = {
							 url: called_url
							,method: "POST"
							,data: {parent_id:parent_id}
						};

						return HttpService.callApi(httpConfig);

					};

					$scope.subProductCategoryListMoved = function (sub_product_category_list, index) {
						sub_product_category_list.splice(index, 1);
					};

					$scope.callProducts = function(product_category_id){
						$rootScope.$broadcast('call-products', { product_category_id: product_category_id });
					};

					$scope.editSub = function(id){
						var parent_id = $scope.parent_id;
						var field = {
							id: id,
							parent_id: parent_id,
						};

						ModalService.showModal({
							templateUrl: "sub-product-category-modal.html",
							controller: "SubProductCategoryModalController",
							inputs:{params:{field:field},}
						}).then(function(modal) {
							// The modal object has the element built, if this is a bootstrap modal
							// you can call 'modal' to show it, if it's a custom modal just show or hide
							// it as you need to.
							modal.element.modal();
							modal.close.then(function(field) {
								
								if(is.empty(field)||is.undefined(field))
									return;

								$scope.getSubProductCategory($scope.parent_id).then(function(data){
									$scope.sub_product_category_list = data.list;
								}, function (err) {
									console.log("productCategory::modal close(156) when save - ", err);
								});

							});
						});

					};

					$scope.toggleSubProductCategoryStatus = function(sub_product_category){
						
						var id = sub_product_category.id;
						var called_url = site_url("cms/api/ProductCategory/toggleStatus");
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

							sub_product_category.status = sub_product_category.status == 1? 0 : 1;

						}, function(err){
							console.log("toggleStatus ::", err);
						});

					};

					$scope.delete = function(id){

						if (!confirm('Are you sure you want to delete this?'))
							return;

						$scope.deleteProductByProductCategoryId(id).then(function(){
							$scope.deleteSub(id);
						});

					};

					$scope.deleteSub = function(id){

						var path_file = "productCategory";
						var called_url = site_url("cms/api/ProductCategory/delete");
						var httpConfig = {
							 url: called_url
							,method: "POST"
							,data: {id:id, path_file:path_file}
						};

						HttpService.callApi(httpConfig).then(function(data){

							if(data.msg == "nok"){
								console.log("deleteSub - " + data.error_msg);
								return;
							}

							return $scope.getSubProductCategory($scope.parent_id);

						}, function(err){
							console.log("deleteSub() -> getSubProductCategory()", err);
						}).then(function(data){
							$scope.sub_product_category_list = data.list;
						}, function (err) {
							console.log("productCategory::deleteSub() -> getSubProductCategory() - ", err);
						});

					};

					$scope.deleteProductByProductCategoryId = function(product_category_id){

						if(is.empty(product_category_id))
							return;

						var called_url = site_url("cms/api/Product/deleteProductByProductCategoryId");
						var httpConfig = {
							 url: called_url
							,method: "POST"
							,data: {product_category_id:product_category_id}
						};

						return HttpService.callApi(httpConfig);

					};

					$scope.orderSubList = function(list){
						
						if(is.empty(list))
							return;

						var page = 1;
						var amount_per_page = 30;
						var list_for_order = [];

						angular.forEach(list, function(obj, key) {
							
							var id = obj.id;
							var temp_order = ((page - 1) * amount_per_page) + 1 + key;

							list_for_order.push({id:id, order_id: temp_order});

						});

						console.log("list_for_order", list_for_order);

						var called_url = site_url("cms/api/ProductCategory/order");
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

							return $scope.getSubProductCategory($scope.parent_id);

						}, function(err){
							console.log("orderSubList()", err);
						}).then(function(data){
							$scope.sub_product_category_list = data.list;
						}, function (err) {
							console.log("productCategory::orderSubList() -> getSubProductCategory() - ", err);
						});

					};

					$scope.init();

				}],
				restrict: 'E', // E = Element, A = Attribute, C = Class, M = Comment
				templateUrl: site_url("assets/js/Cms/Admin/Products/template/subProducts.html")
			};
		}]);

	angular.module('app')
		.controller('SubProductCategoryModalController', 
			['$scope', '$q', '$parse', 'HttpService', 'FileUploadService', 'close', 'params',
			function($scope, $q, $parse, HttpService, FileUploadService, close, params){
			
			$scope.id;
			$scope.field;

			$scope.init = function () {
				
				$scope.id = params.field.id;

			 	//binding data
			 	$scope.getData($scope.id).then(function(data){
			 		
			 		$scope.field = data;

			 	});

			};

			$scope.getData = function(id){

				var defered = $q.defer();

			 	if(is.empty(id)){
			 		defered.reject(null);
			 		return defered.promise;
			 	}

			 	var called_url = site_url("cms/api/ProductCategory/getData");
				var httpConfig = {
					 url: called_url
					,method: "POST"
					,data: {id:id}
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
				field.parent_id = is.empty(params.field.parent_id) || is.undefined(params.field.parent_id)? "" : params.field.parent_id;

				var called_url = site_url("cms/api/ProductCategory/createOrUpdate");
				var httpConfig = {
					 url: called_url
					,method: "POST"
					,data: {field:field}
				};

				HttpService.callApi(httpConfig).then(function(data){
			    	close(data, 500); // close, but give 200ms for bootstrap to animate
				}, function(err){
					$scope.message = err;
				});

			};

			$scope.uploadSingleImageFile = function(file, errFiles, name_file) {
				
				var path_file = "productCategory";
				var file_name = $parse(name_file);
				file_name = file_name($scope);

				console.log(file_name);

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
				close(null, 500); // close, but give 500ms for bootstrap to animate
			};

			$scope.site_url = function(url){
				return site_url(url);
			};

			$scope.init();

		}]);

})();