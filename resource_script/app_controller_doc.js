app.controller('Docs1Controller', function($cookies, $scope, $http, $uibModal, $routeParams, HTTPFactory, IndexOverlayFactory) {
    $scope.$parent.menu_selected = 'manage_doc';
    IndexOverlayFactory.overlayShow();
    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
        window.location.replace('#/logon/manage-docs/doc');
    }
    IndexOverlayFactory.overlayHide();

    
    $scope.loadList = function(){

        var params = {'doc_type' : 'doc', 'doc_level' : 1, 'condition' : $scope.condition};
        HTTPFactory.clientRequest('docs/list', params).then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.DataList = result.data.DATA.List;
            }
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.loadSubList = function(parent_id){

        var params = {'doc_type' : 'doc', 'doc_level' : 2, 'parent_id' : parent_id};
        HTTPFactory.clientRequest('docs/list', params).then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.SubDataList = result.data.DATA.List;
            }
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.add = function(){
        $scope.Data = {id:'','doc_type':'doc', 'actives':'Y'};
        $scope.PAGE = 'UPDATE';
    }

    $scope.update = function(id){
        var params = {'id' : id, 'doc_type':'doc', 'doc_level' : 1};
        HTTPFactory.clientRequest('docs/get', params).then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.Data = result.data.DATA;

                $scope.loadSubList($scope.Data.id);

                $scope.PAGE = 'UPDATE';
            }
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.saveData = function(Data){
        var params = {'Data' : Data, 'doc_level' : 1};
        HTTPFactory.clientRequest('docs/update', params).then(function (result) {
            if (result.data.STATUS == 'OK') {
                alert('บันทึกสำเร็จ');
                $scope.Data.id = result.data.DATA.id;
                $scope.update(result.data.DATA.id);
            }
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.cancelUpdate = function(page){
        $scope.PAGE = page;
        $scope.loadList();
    }

    $scope.PAGE = 'MAIN';
    $scope.loadList();

    
});

app.controller('Docs2Controller', function($cookies, $scope, $http, $uibModal, $routeParams, HTTPFactory, IndexOverlayFactory) {
    $scope.$parent.menu_selected = 'manage';
    IndexOverlayFactory.overlayShow();
    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
        window.location.replace('#/logon/manage-docs/doc');
    }
    IndexOverlayFactory.overlayHide();

    $scope.getData = function(id){
        var params = {'id' : id, 'doc_level' : 2};
        HTTPFactory.clientRequest('docs/get', params).then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.Data = result.data.DATA;

                $scope.loadSubList($scope.Data.id);

                $scope.PAGE = 'UPDATE';
            }
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.loadSubList = function(parent_id){

        var params = {'doc_type' : 'doc', 'doc_level' : 3, 'parent_id' : parent_id};
        HTTPFactory.clientRequest('docs/list', params).then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.SubDataList = result.data.DATA.List;
            }
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.saveData = function(Data){
        var params = {'Data' : Data, 'doc_level' : 2};
        HTTPFactory.clientRequest('docs/update', params).then(function (result) {
            if (result.data.STATUS == 'OK') {
                alert('บันทึกสำเร็จ');
                $scope.Data.id = result.data.DATA.id;
                $scope.getData(result.data.DATA.id);
            }
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.cancelUpdate = function(page){
        // window.location. = '#/manage_docs/doc1';
        history.back();
    }

    $scope.parent_id = $routeParams.parent_id;
    $scope.id = $routeParams.id;

    $scope.Data = {'id':'', 'parent_id':$scope.parent_id, 'actives':'Y'};

    if($scope.id != undefined && $scope.id != null && $scope.id != ''){
        $scope.getData($scope.id);
    }
    
});

app.controller('Docs3Controller', function($cookies, $scope, $http, $uibModal, $routeParams, HTTPFactory, IndexOverlayFactory) {
    $scope.$parent.menu_selected = 'manage';
    IndexOverlayFactory.overlayShow();
    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
        window.location.replace('#/logon/manage-docs/doc');
    }
    IndexOverlayFactory.overlayHide();

    $scope.getData = function(id){
        var params = {'id' : id, 'doc_level' : 3};
        HTTPFactory.clientRequest('docs/get', params).then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.Data = result.data.DATA;
                $scope.PAGE = 'UPDATE';
            }
            IndexOverlayFactory.overlayHide();
        });
    }


    $scope.saveData = function(Data, AttachFile){
        var params = {'Data' : Data, 'AttachFile': AttachFile, 'doc_level' : 3};
        HTTPFactory.uploadRequest('docs/update', params).then(function (result) {
            if (result.data.STATUS == 'OK') {
                alert('บันทึกสำเร็จ');
                $scope.Data.id = result.data.DATA.id;
                $scope.getData(result.data.DATA.id);
            }
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.cancelUpdate = function(page){
        // window.location. = '#/manage_docs/doc1';
        history.back();
    }

    $scope.parent_id = $routeParams.parent_id;
    $scope.id = $routeParams.id;

    $scope.Data = {'id':'', 'parent_id':$scope.parent_id, 'file_type': null, 'actives':'Y'};

    if($scope.id != undefined && $scope.id != null && $scope.id != ''){
        $scope.getData($scope.id);
    }
    
});

app.controller('DocsController', function($cookies, $scope, $http, $uibModal, $routeParams, HTTPFactory, IndexOverlayFactory) {
    $scope.$parent.menu_selected = $routeParams.doc_type;
    IndexOverlayFactory.overlayShow();
    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
        window.location.replace('#/logon/doc/' + $routeParams.doc_type);
    }
    IndexOverlayFactory.overlayHide();

    $scope.loadList = function(doc_type){
        var params = {'doc_type' : doc_type};
        HTTPFactory.clientRequest('docs/list/view', params).then(function (result) {
            
            if (result.data.STATUS == 'OK') {
                $scope.DataList = result.data.DATA.List;
            }

            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.doc_type = $routeParams.doc_type;

    $scope.loadList($routeParams.doc_type);
});