(function(){
	'use strict';

	angular.module('app', []);

	angular.module('app')
		.config(['$compileProvider', '$controllerProvider', '$provide', function($compileProvider, $controllerProvider, $provide) {
			
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

})();