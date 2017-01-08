(function(){
	'use strict';

	angular.module('app')
		.factory('HttpService', ['$q', '$http', function($q, $http){
			var HttpService = {};

			HttpService.callApi = function(httpConfig){
				
				var defered = $q.defer();

				$http(httpConfig).then(function(data){
					defered.resolve(data);
				});

				return defered.promise;

			};

			return HttpService;
		}]);

})();