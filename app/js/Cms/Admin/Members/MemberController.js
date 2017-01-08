(function(){
	'use strict';

	angular.module('app')
		.controller('MemberController', 
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

					var called_url = site_url("cms/api/clients/get");
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
					alert("Hello world");
					// getList().then(function(data){
					// 	$scope.list = data.list;
					// });
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

				$scope.showDetail = function(member_id){
					
					var field = {
						member_id: member_id
					};

					ModalService.showModal({
						templateUrl: "client-detail-modal.html",
						controller: "ClientdetailController",
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
		.controller('ClientdetailController', 
			['$scope', '$q', '$parse', 'HttpService', 'close', 'params', 
			function($scope, $q, $parse, HttpService, close, params){
			
				$scope.member_id;
				$scope.details;

				//private function
				var getDetail = function(member_id) {
					var called_url = site_url("cms/api/clients/getDetail");
					var httpConfig = {
						 url: called_url
						,method: "POST"
						,data: {
							member_id: member_id
						}
					};

					return HttpService.callApi(httpConfig);
				}

				$scope.init = function () {
					$scope.member_id = params.field.member_id;

					getDetail($scope.member_id).then(function(data){
						$scope.details = data.user_detail;
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