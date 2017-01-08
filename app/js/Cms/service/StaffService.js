(function(){
	'use strict';

	angular.module('app')
		.factory('StaffService', ['$q', 'HttpService', function($q, HttpService){
			
			var StaffService = {};

			StaffService.is_online = false;

			StaffService.logout = function(){
					
					var called_url = site_url("cms/api/Admin/logout");
					var httpConfig = {
						 url: called_url
						,method: "POST"
						,data: null
					};
					var defered = $q.defer();

					HttpService.callApi(httpConfig).then(function(res){


						if(is.empty(res) || is.empty(res.data.msg))
							defered.reject("HttpService failed");
						else if(res.data.msg == "nok")
							defered.reject(res.data.error_msg);
						else defered.resolve(true);

					}, function(err){
						defered.reject(err);
					});
					
					return defered.promise;
				};

			StaffService.ping = function(){

				var called_url = site_url("cms/api/Admin/pingToken");
				var httpConfig = {
					 url: called_url
					,method: "POST"
					,data: null
				};
				var defered = $q.defer();

				HttpService.callApi(httpConfig).then(function(res){

					if(is.empty(res)){
						StaffService.is_online = false;
						defered.reject("StaffService:: HttpService data return failed.");
					}
					else if(is.empty(res.data.msg) || res.data.msg == "nok"){
						console.log(res.data.error_msg);
						StaffService.is_online = false;
						defered.reject(res.data.error_msg);
					}
					else if(res.data.msg == "ok"){
						StaffService.is_online = true;
						defered.resolve(true);
					}
					else {
						StaffService.is_online = false;
						defered.reject(false);
					}

				}, function(err){
					defered.reject(err);
				});

				return defered.promise;
			};

			StaffService.getRoutes = function(){

				var called_url = site_url("cms/api/Admin/getRoutes");
					var httpConfig = {
						 url: called_url
						,method: "POST"
						,data: null
					};

			    	var defered = $q.defer();

			    	HttpService.callApi(httpConfig).then(function(res){
			    		defered.resolve(res.data);
			    	}, function(err){
			    		defered.reject(err);
			    	});

			    	return defered.promise;

			};

			StaffService.getMenus = function () {
			    	
			    	var called_url = site_url("cms/api/Admin/getMenus");
					var httpConfig = {
						 url: called_url
						,method: "POST"
						,data: null
					};

			    	var defered = $q.defer();

			    	HttpService.callApi(httpConfig).then(function(res){
			    		defered.resolve(res.data);
			    	}, function(err){
			    		defered.reject(err);
			    	});

			    	return defered.promise;

			    };

			return StaffService;

		}]);

})();