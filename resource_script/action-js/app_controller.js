app.controller('AppCtrl', function($scope, $http, $location) {
	$scope.username = username;
	console.log("This is AppCtrl", $scope.username);
	$scope.go = function ( path ) {
		console.log('Go to '+path);
		$location.path( path );
	};

});

app.controller('DashboardCtrl', function($scope, $http, $location) {
	console.log("This is DashboardCtrl"+$scope.username);
	

});

app.controller('CreateProjCtrl', function($scope, $http, $location) {
	console.log("This is CreateProjCtrl");
	$scope.addProject = function(){
		$http({
			url : 'add_project',
			method : "POST",
			data : $scope.projectName
		}).success(function(response) {
			console.log(response);
			$location.path( 'analysis_proj/1' );
		});
	};
});

app.controller('AnalysisProjCtrl', ['projectGeneral', 'projectDetail', 'projectTeamMember','BoardService', 'BoardDataFactory', '$scope', '$http', '$modal', '$filter', 
                                    function(projectGeneral, projectDetail, projectTeamMember, BoardService, BoardDataFactory, $scope, $http, $modal, $filter) {
	console.log("This is AnalysisProjCtrl");
	$scope.data = {};
	$scope.programmingBaseLangList = [{'lang':'JAVA'}
									,{'lang':'PHP'}];
	
	$scope.frameworkList = [{'type':'JAVA','framework':'Spring + Hibernate'}
							,{'type':'JAVA','framework':'Spring + JDBC'}
							,{'type':'PHP','framework':'PHP + IE5DEV'}];
	
	$scope.timelineList = [
	                       {
	                    	'timelineName':'Getting Requirement'
	                    	,'timelimePlandateStart':'2016-09-01'
	                    	,'timelimePlandateEnd':'2016-09-09'
	                    	,'devStartDate':''
	                    	,'devEndDate':''
	                    	,'qaStartDate':''
	                    	,'qaEndDate':''
	                    	,'timelimeProgress':'0'
	                    	}
						
						];
	// Prepare data value
	$scope.project = projectGeneral;
	$scope.moduleList = projectDetail;
	$scope.teamList = projectTeamMember;
	
	console.log($scope.moduleList);
	
	$scope.onSelect = function ($item) {
		$scope.personName = $item;
	};
	
	$scope.addTeamMember = function($item){
		var result = $item.split(' : ');
		var personName = result[0];
		var personPosition = result[1];
		$scope.teamList.push({'personName':personName,'personPosition':personPosition});
		$scope.data.personName = '';
		$item = '';
	};
	
	
	$scope.addModule = function(){
		$scope.moduleList.push({'module':[{"moduleId":null,"createDate":null,"moduleComment":"Test Login comment","moduleDevOwnerId":0,"moduleDevOwnerName":null,"moduleDevOwnerPoint":null,"moduleName":$scope.data.moduleName,"moduleProgress":null,"moduleQaOwnerId":0,"moduleQaOwnerName":null,"moduleQaOwnerPoint":null,"moduleStatus":null,"peojectId":0}]
								,'functions':[]}); //{"projectModuleFunctionId":null,"createDate":null,"estimateDevDate":null,"estimateQaDate":null,"finishDevDate":null,"finishQaDate":null,"functionComment":null,"functionDevOwnerId":0,"functionDevOwnerName":null,"functionDevOwnerPoint":null,"functionName":null,"functionNameDescription":null,"functionQaOwnerId":0,"functionQaOwnerName":null,"functionQaOwnerPoint":null,"functionStatus":null,"projectId":0,"projectModuleId":0}
		$scope.data.moduleName = '';
	};
	
	
	$scope.viewFunctionDetail = function(functionObj){
		var modalInstance = $modal.open({
			backdrop: 'static',
		    keyboard: false,
			animation: true,
			templateUrl : 'functionDetail.html',
			controller : 'ModalUpdateDataCtrl',
			scope : $scope
//			,size : 'sm'
			,resolve : {
				params : function() {
					var vars = [];
					vars['functionObj']=functionObj;
					return vars;
				} 
			}
		});
	};
	
	$scope.searchAutoComplete = function (val, qtype){
		return autoComplete($http, $scope, val, qtype);
	};
	
	$scope.searchAutoCompleteClient = function(val, dataSource, fieldName){
		return $filter('AutoCompleteClient')(val, dataSource, fieldName);
	};
	
	$scope.kanbanBoard = BoardService.kanbanBoard(BoardDataFactory.kanban);

	  $scope.kanbanSortOptions = {

	    //restrict move across columns. move only within column.
	    /*accept: function (sourceItemHandleScope, destSortableScope) {
	     return sourceItemHandleScope.itemScope.sortableScope.$id === destSortableScope.$id;
	     },*/
	    itemMoved: function (event) {
	      event.source.itemScope.modelValue.status = event.dest.sortableScope.$parent.column.name;
	    },
	    orderChanged: function (event) {
	    },
	    containment: '#board'
	  };

	  $scope.removeCard = function (column, card) {
	    BoardService.removeCard($scope.kanbanBoard, column, card);
	  };

	  $scope.addNewCard = function (column) {
	    BoardService.addNewCard($scope.kanbanBoard, column);
	  }
	  
}]);

app.filter('AutoCompleteClient', function() {
  return function(val, dataSource, fieldName) {
    var i=0, len=dataSource.length;
    var arrResult = [];
    for (; i<len; i++) {
    	eval('if (dataSource[i].'+fieldName+'.toLowerCase().indexOf(val.toLowerCase()) !== -1 ) { arrResult.push(dataSource[i].'+fieldName+'); }');
    }
    return arrResult;
  };
});

app.controller('ModalUpdateDataCtrl', function ($scope, $http, $modalInstance, params) {
	console.log("ModalUpdateDataCtrl", params.functionObj);
	$scope.data = {};
	if(params.functionObj != null){
		$scope.data = params.functionObj;
		$scope.data.functionNameDescription = 'Verify permission of user include logging on status, password expire date and etc.';
		$scope.data.functionDevOwnerName = 'Aom U-koskit';
		$scope.data.functionDevOwnerEsimateTime = 180;
		$scope.data.functionDevOwnerPoint = 25;
	}
    $disabledOK = false;
    $scope.ok = function () {
        $scope.disabledOK = true;
        $http({
            url: '../data_defaulttemplate.php',
            method: "POST",
            data: params
        }).success(function (response) {
            //alert(response.results.msg);
            $modalInstance.close(response.result);
        });
    };
    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
});

function autoComplete($http, $scope, val, qtype){
	$scope.searchAutoComplete = function (val, qtype){
		val = encodeURIComponent(val);
		return $http.get("autoComplete/"+qtype+"/"+val).then(function(response){
			console.log(response);
	      	return response.data;
	    });
	};
}