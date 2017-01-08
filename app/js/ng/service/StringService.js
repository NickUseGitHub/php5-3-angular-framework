(function(){
	'use strict';

	angular.module('app')
		.factory('StringService', [function(){
			var StringService = {};

			StringService.randomString = function(length) {
				if(is.empty(length))
					return;

			    var text = "";
			    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
			    for(var i = 0; i < length; i++) {
					text += possible.charAt(Math.floor(Math.random() * possible.length));
			    }
			    return text;
			};
			StringService.randomStringLeadWithChar = function(length) {
				if(is.empty(length))
					return;

			    var text = "";
			    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
			    var firstPossible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
			    for(var i = 0; i < length; i++) {
					if(i == 0)		    	
						text += firstPossible.charAt(Math.floor(Math.random() * firstPossible.length));
			        else text += possible.charAt(Math.floor(Math.random() * possible.length));
			    }
			    return text;
			};

			return StringService;
		}]);

})();