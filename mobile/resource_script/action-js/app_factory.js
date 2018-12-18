app.factory('ProjectService', ['$http', '$q', function($http, $q){
	 
    return {
         
    	getProjectGeneral: function(proj_id) {
            return $http.get('http://localhost:8080/requirementtools/analysisProj/'+proj_id)
	            .then(
	                    function(response){
//	                    	console.log(response);
	                        return response.data;
	                    }, 
	                    function(errResponse){
	                        console.error('Error while fetching specific Item');
	                        return $q.reject(errResponse);
	                    }
	            );
        }
    
	    ,getProjectDetail: function(proj_id) {
	        return $http.get('http://localhost:8080/requirementtools/analysisProjDetails/'+proj_id)
	            .then(
	                    function(response){
//	                    	console.log(response);
	                        return response.data;
	                    }, 
	                    function(errResponse){
	                        console.error('Error while fetching specific Item');
	                        return $q.reject(errResponse);
	                    }
	            );
	    }
	    
	    ,getProjectTeamMember: function(proj_id) {
	        return $http.get('http://localhost:8080/requirementtools/analysisProjTeamMember/'+proj_id)
	            .then(
	                    function(response){
//	                    	console.log(response);
	                        return response.data;
	                    }, 
	                    function(errResponse){
	                        console.error('Error while fetching specific Item');
	                        return $q.reject(errResponse);
	                    }
	            );
	    }
    };
 
}]);
