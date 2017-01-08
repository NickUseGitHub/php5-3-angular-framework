(function(){
	'use strict';

	angular.module('app', [
		'ngRoute',
		'ngCookies',
		'angularModalService',
		'ngFileUpload',
		'dndLists',
		'textAngular'
	]);

	angular.module('app')
		.config(['$routeProvider', '$compileProvider', '$controllerProvider', '$provide', function($routeProvider, $compileProvider, $controllerProvider, $provide) {
			
			angular.module('app').routeProvider = $routeProvider;

			angular.module('app')._factory = angular.module('app').factory;
			angular.module('app')._controller = angular.module('app').controller;
			angular.module('app')._directive = angular.module('app').directive;

			// Provider-based factory.
            angular.module('app').factory = function( name, factory ) {
                    $provide.factory( name, factory );
                    return( this );
                };


			//controller
			angular.module('app').controller = function(name, constructor){
				$controllerProvider.register(name, constructor);
				return (this);
			};

			//directive
			angular.module('app').directive = function( name, factory ) {
                $compileProvider.directive( name, factory );
                return (this);
            };

		}]);

	angular.module('app')
		.factory('LazyLoadModule', ['$q', '$rootScope', function($q, $rootScope){
			var LazyLoadModule = {};

			LazyLoadModule.load = function (deps) {

				if(deps == undefined || deps == null || deps == '')
					failedCallback();

				var defered = $q.defer();

	            $script(deps, function()
	            {
	                // all dependencies have now been loaded by $script.js so resolve the promise
	                $rootScope.$apply(function()
	                {
	                    defered.resolve();
	                });
	            });

				return defered.promise;

			};

			return LazyLoadModule;
		}]);

	angular.module('app')
		.controller('MainController', [
			'$scope', '$q', '$interval', '$window', 'HttpService', 'StaffService', 'LazyLoadModule', 
			function($scope, $q, $interval, $window, HttpService, StaffService, LazyLoadModule){
			
				$scope.originalPath = location.hash;

				$scope.StaffService = StaffService;
				$scope.menus = null;
				$scope.interval = null;

				$scope.init = function(){

					location.hash = '';

					$scope.pingUser();

					$scope.initController().then(function(){
						return $scope.getMenus();
					}, function (err) {
						console.log(err);
					}).then(function(data){
						
						$scope.menus = data;
						location.hash = $scope.originalPath;

					}, function (err) {
						console.log(err);
					});

					$scope.start();

				};

				$scope.initController = function(){

					var defered = $q.defer();

					StaffService.getRoutes().then(function(routes){

						if(is.empty(routes)){
							defered.reject("routes is null or empty.");
						}else{

							angular.forEach(routes, function(route, key) {
							  	
								var path_url = route.path_url;
								var templateUrl = route.templateUrl;
								var controller = route.controller;
								var ctrDependencies = route.ctrDependencies; 

								angular.module('app').routeProvider
									.when(path_url, {
					                    templateUrl : templateUrl,
										controller : controller,
										resolve:{
											deps:function()
				        					{	
									         	return LazyLoadModule.load(ctrDependencies);
						                	}
						                }
						           	})

							});

							defered.resolve();

						}

					}, function(err){
						defered.reject(err);
					});

					return defered.promise;

				};

				$scope.logout = function(){
					
					StaffService.logout().then(function(data){
						
						if(is.not.boolean(data) || !data){
							console.log("main.js - logout error.");
							return;
						}

						$scope.stop();
						$scope.gotoLoginPage();

					}, function(err){
						console.log(err);
					});

				};

				$scope.gotoLoginPage = function(){
					var url = site_url("cms/Login");
					$window.location = url;
				};

				$scope.pingUser = function(){
					
					StaffService.ping().then(function(data){

						if(is.not.boolean(data) || data)
							return;

						$scope.stop();
						$scope.gotoLoginPage();

					}, function(err){
						console.log(err);
					});

				};

				$scope.start = function(){
			        $scope.stop();

			        $scope.interval = $interval(function () {
			            $scope.pingUser();
			        }, 5 * 60 * 1000);

			    };

			    $scope.stop = function(){
			        $interval.cancel($scope.interval);
			    };

			    $scope.$on('$destroy', function() {
			        $scope.stop();
			    });

			    $scope.getMenus = function () {

			    	return StaffService.getMenus();

			    };

				$scope.init();

			}]);
	
	//filters
	angular.module('app')
		.filter('dateToISO', function() {
			return function(input) {
				input = new Date(input).toISOString();
				return input;
			};
		});

})();