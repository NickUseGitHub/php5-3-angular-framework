(function(){
	'use strict';

	/***
	 * this service require
	 * - assets/bower_components/crypto-js/crypto-js.js
	 * - assets/bower_components/angular-local-storage/dist/angular-local-storage.min.js
	 */

	angular.module('app')
		.factory('appLocalStorage', ['localStorageService', function(localStorageService){

			var appLocalStorage = {};

			var secretKey = "drawme-app";

			var isExpired = function (createDate, timeLimit) {
				var difference = new Date().getTime() - createDate;
				var secondsDifference = Math.floor(difference/1000);
				
				return secondsDifference > timeLimit;
			}

			appLocalStorage.defaultTime = 60*1;

			appLocalStorage.set = function (key, data, second) {
				var timeLimit = second ||appLocalStorage.defaultTime;

				var objStore = {
					 data: JSON.stringify(data)
					,timeLimit:timeLimit
					,createDate: new Date().getTime()
				};

				try {
					var dateForStore = CryptoJS.AES.encrypt(JSON.stringify(objStore), secretKey).toString();
					localStorageService.set(key, dateForStore);
				} catch (e) {
					console.log(e);
				}
			};

			appLocalStorage.get = function (key) {
				try {
					var encryptedValue = localStorageService.get(key);
					var dataRetrieve = CryptoJS.AES.decrypt(encryptedValue, secretKey).toString(CryptoJS.enc.Utf8)

					var objStore = JSON.parse(dataRetrieve);
					if (isExpired(objStore.createDate, objStore.timeLimit)) {
						appLocalStorage.remove(key);
						return null;
					}
					
					return JSON.parse(objStore.data);
				} catch (e) {
					console.log(e);
					return null;
				}
			};

			appLocalStorage.remove = function (key) {
				try {
					localStorageService.remove(key);
				} catch (e) {
					console.log(e);
				}
			};

			appLocalStorage.clear = function () {
				try {
					localStorageService.clearAll();
				} catch (e) {
					console.log(e);
				}
			};

			return appLocalStorage;

		}]);

})();