(function(){
	'use strict';

	angular.module('app')
		.factory('FileUploadService', 
			['$q', 'Upload', 'HttpService'
			, function($q, Upload, HttpService){
			var FileUploadService = {};

			FileUploadService.uploadSingleImageFile = function(file, errFiles, path_file) {
				
				var defered = $q.defer();

				if (file && is.not.empty(path_file)) {

					var called_url = site_url("api/files/uploadPic");
					file.upload = Upload.upload({
						url: called_url,
						data: {file: file, path_file: path_file}
					});

					file.upload.then(function (response) {
						defered.resolve(response.data);
					}, function (response) {
						defered.reject("Error");
					}, function (evt) {
						// file.progress = Math.min(100, parseInt(100.0 * 
						// 	evt.loaded / evt.total));
					});
				}else defered.reject("file or path_file is null or empty.");

				return defered.promise;  

			};


			FileUploadService.uploadSingleFile = function(file, errFiles, path_file) {
				
				var defered = $q.defer();

				if (file && is.not.empty(path_file)) {

					var called_url = site_url("api/files/uploadFile");
					file.upload = Upload.upload({
						url: called_url,
						data: {file: file, path_file: path_file}
					});

					file.upload.then(function (response) {
						defered.resolve(response.data);
					}, function (response) {
						defered.reject("Error");
					}, function (evt) {
						// file.progress = Math.min(100, parseInt(100.0 * 
						// 	evt.loaded / evt.total));
					});
				}else defered.reject("file or path_file is null or empty.");

				return defered.promise;  

			};

			FileUploadService.removeSingleFile = function(file_name, path_file){
				var files = [];
				files.push(file_name);

				return FileUploadService.removeFiles(files, path_file);
			};

			FileUploadService.removeFiles = function(files, path_file){

				var called_url = site_url("api/files/remove");
				var httpConfig = {
					 url: called_url
					,method: "POST"
					,data: {
						path_file: path_file,
                        files: files	
					}
				};

				return HttpService.callApi(httpConfig);

			};

			return FileUploadService;
		}]);

})();