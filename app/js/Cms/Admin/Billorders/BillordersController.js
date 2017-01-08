(function(){
	'use strict';

	angular.module('app')
		.controller('BillorderController', 
			['$scope', 'HttpService', 'ModalService'
			, function($scope, HttpService, ModalService){

				$scope.search = "";
				$scope.page = 1;
				$scope.amount_per_page = 15;
				$scope.total_page;

				//for list
				$scope.list = [];

				//private methods
				function getList() {
					var search = $scope.search;
					var page = $scope.page;
					var amount_per_page = $scope.amount_per_page;

					var called_url = site_url("cms/api/billorder/get");
					var httpConfig = {
						 url: called_url
						,method: "POST"
						,data: {
							search: search,
							page: page,
							amount_per_page:amount_per_page,
						}
					};

					return HttpService.callApi(httpConfig);
				}

				//methods
				$scope.init = function () {
					getList().then(function(data){
						$scope.list = data.list;
						$scope.total_page = data.total_page;
					});
				};

				$scope.listMoved = function (list, index) {
					list.splice(index, 1);

					console.log("list", $scope.list);
				};

				$scope.filter = function () {
					getList().then(function(data){
						
						$scope.list = data.list;
						$scope.total_page = data.total_page;

					}, function(err){
						console.log("init :: scope.getList()", err);
					});
				};

				$scope.togglePaidStatus = function (item){

					if (!confirm("Are you sure you want to change paid status for '" + item.code + "'")) {
						return;
					}

					var id = item.id;
					var called_url = site_url("cms/api/billorder/togglePaidStatus");
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

						item.paid_status = item.paid_status == 1? 0 : 1;

					}, function(err){
						console.log("toggleStatus ::", err);
					});

				};

				$scope.setShippingStatus = function(item, shipping_status) {

					if (!confirm("Are you sure you want to change shipping status for '" + item.code + "'")) {
						return;
					}

					var id = item.id;
					var called_url = site_url("cms/api/billorder/setShippingStatus");
					var httpConfig = {
						 url: called_url
						,method: "POST"
						,data: {
							id: id,
							shipping_status: shipping_status
						}
					};

					HttpService.callApi(httpConfig).then(function(data){

						if(data.msg == "nok"){
							console.log("toggleStatus ::", data.error_msg);
							return;
						}

						item.shipping_status = shipping_status;

					}, function(err){
						console.log("setShippingStatus ::", err);
					});

				}

				$scope.showItemsList = function(bill_id, code){
					
					var field = {
						bill_id: bill_id,
						code:code
					};

					ModalService.showModal({
						templateUrl: "billitems-modal.html",
						controller: "BillItemModalController",
						inputs:{params:{field:field},}
					}).then(function(modal) {
						// The modal object has the element built, if this is a bootstrap modal
						// you can call 'modal' to show it, if it's a custom modal just show or hide
						// it as you need to.
						modal.element.modal();
					});

				};

				$scope.site_url = function(url){
					return site_url(url);
				};

				$scope.init();

			}]);

	angular.module('app')
		.controller('BillItemModalController', 
			['$scope', '$q', '$parse', 'HttpService', 'close', 'params', 
			function($scope, $q, $parse, HttpService, close, params){
			
				$scope.bill = {};
				$scope.list;

				//private function
				function getItems(bill_id) {
					var called_url = site_url("cms/api/billitems/get");
					var httpConfig = {
						 url: called_url
						,method: "POST"
						,data: {
							bill_id: bill_id
						}
					};

					return HttpService.callApi(httpConfig);
				}

				$scope.init = function () {
					
					$scope.bill.bill_id = params.field.bill_id;
					$scope.bill.code = params.field.code;

					getItems($scope.bill.bill_id).then(function(data){
						$scope.list = data.list;
					});

				};

				$scope.ok = function(data) {
					close(null, 500);
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