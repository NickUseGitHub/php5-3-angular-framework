(function(){
	'use strict';

	angular.module('app')
		.controller('Login_Controller', ['$scope', '$window', 'HttpService', function($scope, $window, HttpService){
			
			$scope.username = "";
			$scope.password = "";
			$scope.message = "";

			$scope.init = function(){
			};

			$scope.login = function(){
				if(!$scope.formField.$valid)
					return;

				var called_url = site_url("cms/login");
				var param = {
								username: $scope.username,
								password: $scope.password,
							};

				var httpConfig = {
					 url: called_url
					,method: "POST"
					,data: param
				};

				HttpService.callApi(httpConfig).then(function(res){
					if(is.empty(res.data) || is.empty(res.data.msg)){
						$scope.message = "HttpService failed";
						return;
					}

					if(res.data.msg == "nok"){
						$scope.message = res.data.error_msg;
						return;
					}

					$scope.gotoCmsIndex();

				}, function(err){
					console.log(err);
				});

			};

			$scope.gotoCmsIndex = function(){
				var url = site_url("cms/Admin");
				$window.location = url;
			};

			$scope.init();

		}]);

	angular.module('app')
		.directive('userPassword', ['$rootScope', function($rootScope){
			// Runs during compile
			return {
				restrict: 'A', // E = Element, A = Attribute, C = Class, M = Comment
				link: function(scope, element, attrs, controller) {
					
					element.bind("keypress", function (event) {
			            if(event.which === 13) {
			                scope.login();

			                event.preventDefault();
			            }
			        });

				}
			};
		}]);

	angular.module('app')
		.directive('focus',
			function($timeout) {
				return {
					scope : {
						trigger : '@focus'
					},
					link : function(scope, element) {
						scope.$watch('trigger', function(value) {
							if (value === "true") {
								$timeout(function() {
									element[0].focus();
								});
							}
						});
					}
				};
			}); 

})();