(function(){
	'use strict';

	angular.module('app')
		.factory('CookieService', ['$cookies', function($cookies){
			
			var CookieService = {};

			CookieService.put = function (key, value, minute) {
				
				minute = minute || 5;

				var dateExp = new Date();
				dateExp.setMinutes(dateExp.getMinutes() + minute);

				$cookies.put(key, value, {expires:dateExp})

			};

			return CookieService;

		}]);

})();