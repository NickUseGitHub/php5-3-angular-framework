(function(){
	'use strict';

	angular.module('app')
		.factory('SessionStorage', [function(){
			
			var SessionStorage = {};

			SessionStorage.set = function (key, data) {
		        try {
		            sessionStorage.setItem(key, JSON.stringify(data));
		        } catch (e) {
		            console.log(e);
		        }
		    };

		    SessionStorage.get = function (key) {
		        try {
		            return JSON.parse(sessionStorage.getItem(key));
		        } catch (e) {
		            console.log(e);
		            return false;
		        }
		    };

		    SessionStorage.remove = function (key) {
		        try {
		            sessionStorage.removeItem(key);
		        } catch (e) {
		            console.log(e);
		        }
		    };

		    SessionStorage.clear = function () {
		        try {
		            sessionStorage.clear();
		        } catch (e) {
		            console.log(e);
		        }
		    };

			return SessionStorage;

		}]);
})();