app.controller('RepairManageController', function($cookies, $scope, $uibModal, $routeParams, $timeout, $interval, Upload, IndexOverlayFactory, RepairFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'manage_repair';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }

    $scope.loadList = function (mode){
        RepairFactory.getRepairTypeList(mode, $scope.currentUser.UserID).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.DataList = result.data.DATA.DataList;
            }
        });
    }

    $scope.confirmDelete = function (index, ID){
        if(ID == ''){
            $scope.DataList.splice(index, 1);
        }else{
            $scope.alertMessage = 'ต้องการลบ "ประเภทงานซ่อม" นี้ ใช่หรือไม่ ?';
            var modalInstance = $uibModal.open({
                animation : true,
                templateUrl : 'html/dialog_confirm.html',
                size : 'sm',
                scope : $scope,
                backdrop : 'static',
                controller : 'ModalDialogCtrl',
                resolve : {
                    params : function() {
                        return {};
                    } 
                },
            });

            modalInstance.result.then(function (valResult) {
                IndexOverlayFactory.overlayShow();
                RepairFactory.deleteRepairType(ID).then(function(result){
                    IndexOverlayFactory.overlayHide();
                    if(result.data.STATUS == 'OK' && result.data.DATA){
                        $scope.DataList.splice(index, 1);
                    }
                });
            });
        }
    }

    $scope.loadList('manage');

});

app.controller('RepairTypeManageController', function($cookies, $scope, $uibModal, $routeParams, $timeout, $interval, Upload, IndexOverlayFactory, RepairFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'manage_repair';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }

    $scope.loadRepairType = function (ID){
        RepairFactory.getRepairType(ID).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.Data = result.data.DATA;
            }
        });
    }

    $scope.updateData = function (data){
        IndexOverlayFactory.overlayShow();
        RepairFactory.updateRepairType(data).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                //$scope.loadRepairType(result.data.DATA.RepairedTypeID);
                if($scope.Data.RepairedTypeID ==''){
                    $scope.Data = result.data.DATA;
                    window.location.href = '#/manage_repair_type/' + result.data.DATA.RepairedTypeID;
                }
            }
        });
    }

    $scope.confirmDelete = function (index, ID){
        if(ID == ''){
            $scope.DataList.splice(index, 1);
        }else{
            $scope.alertMessage = 'ต้องการลบ "หัวข้องานซ่อม" นี้ ใช่หรือไม่ ?';
            var modalInstance = $uibModal.open({
                animation : true,
                templateUrl : 'html/dialog_confirm.html',
                size : 'sm',
                scope : $scope,
                backdrop : 'static',
                controller : 'ModalDialogCtrl',
                resolve : {
                    params : function() {
                        return {};
                    } 
                },
            });

            modalInstance.result.then(function (valResult) {
                IndexOverlayFactory.overlayShow();
                RepairFactory.deleteRepairTitle(ID).then(function(result){
                    IndexOverlayFactory.overlayHide();
                    if(result.data.STATUS == 'OK' && result.data.DATA){
                        $scope.Data.repair_title.splice(index, 1);
                    }
                });
            });
        }
    }

    $scope.Data = {'RepairedTypeID':''
                    ,'RepairedTypeName':''
                    ,'ActiveStatus':'Y'
                    ,'CreateBy':$scope.currentUser.UserID
                    ,'CreateDateTime':''};
    
    if($routeParams.RepairedTypeID != undefined || $routeParams.RepairedTypeID != null){
        $scope.loadRepairType($routeParams.RepairedTypeID);
    }else{
        IndexOverlayFactory.overlayHide();
    }
    

});

app.controller('RepairTitleManageController', function($cookies, $scope, $uibModal, $routeParams, $timeout, $interval, Upload, IndexOverlayFactory, RepairFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'manage_repair';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }

    $scope.loadRepairTitle = function (ID){
        RepairFactory.getRepairTitle(ID).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.Data = result.data.DATA;
            }
        });
    }

    $scope.loadDepartment = function (){
        RepairFactory.getDepartmentList().then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.DepartmentList = result.data.DATA;
            }
        });
    }

    $scope.updateData = function (data){
        IndexOverlayFactory.overlayShow();
        RepairFactory.updateRepairTitle(data).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                if($scope.Data.RepairedTitleID ==''){
                    $scope.Data = result.data.DATA;
                    window.location.href = '#/manage_repair_title/' + $routeParams.RepairedTypeID + '/' + result.data.DATA.RepairedTitleID;
                }
            }
        });
    }

    $scope.confirmDelete = function (index, ID){
        if(ID == ''){
            $scope.DataList.splice(index, 1);
        }else{
            $scope.alertMessage = 'ต้องการลบ "ปัญหางานซ่อม" นี้ ใช่หรือไม่ ?';
            var modalInstance = $uibModal.open({
                animation : true,
                templateUrl : 'html/dialog_confirm.html',
                size : 'sm',
                scope : $scope,
                backdrop : 'static',
                controller : 'ModalDialogCtrl',
                resolve : {
                    params : function() {
                        return {};
                    } 
                },
            });

            modalInstance.result.then(function (valResult) {
                IndexOverlayFactory.overlayShow();
                RepairFactory.deleteRepairIssue(ID).then(function(result){
                    IndexOverlayFactory.overlayHide();
                    if(result.data.STATUS == 'OK' && result.data.DATA){
                        $scope.Data.repair_issue.splice(index, 1);
                    }
                });
            });
        }
    }

    $scope.Data = {'RepairedTitleID':''
                    ,'RepairedTypeID':$routeParams.RepairedTypeID
                    ,'RepairedTitleName':''
                    ,'DepartmentID':''
                    ,'RepairedTitleCode' : ''
                    ,'ActiveStatus':'Y'
                    ,'CreateBy':$scope.currentUser.UserID
                    ,'CreateDateTime':''};
    console.log($scope.Data);
    if($routeParams.RepairedTitleID != undefined || $routeParams.RepairedTitleID != null){
        $scope.loadRepairTitle($routeParams.RepairedTitleID);
        
    }else{
        IndexOverlayFactory.overlayHide();
    }
    $scope.loadDepartment();

});

app.controller('RepairIssueManageController', function($cookies, $scope, $uibModal, $routeParams, $timeout, $interval, Upload, IndexOverlayFactory, RepairFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'manage_repair';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }

    $scope.loadRepairIssue = function (ID){
        RepairFactory.getRepairIssue(ID).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.Data = result.data.DATA;
            }
        });
    }

    $scope.updateData = function (data){
        IndexOverlayFactory.overlayShow();
        RepairFactory.updateRepairIssue(data).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                //$scope.loadRepairIssue(result.data.DATA.RepairedTypeID);
                if($scope.Data.RepairedIssueID ==''){
                    $scope.Data = result.data.DATA;
                    window.location.href = '#/manage_repair_issue/' + $routeParams.RepairedTypeID + '/' + $routeParams.RepairedTitleID + '/' + result.data.DATA.RepairedIssueID;
                }
            }
        });
    }

    $scope.confirmDelete = function (index, ID){
        if(ID == ''){
            $scope.DataList.splice(index, 1);
        }else{
            $scope.alertMessage = 'ต้องการลบ "ปัญหาย่อยานซ่อม" นี้ ใช่หรือไม่ ?';
            var modalInstance = $uibModal.open({
                animation : true,
                templateUrl : 'html/dialog_confirm.html',
                size : 'sm',
                scope : $scope,
                backdrop : 'static',
                controller : 'ModalDialogCtrl',
                resolve : {
                    params : function() {
                        return {};
                    } 
                },
            });

            modalInstance.result.then(function (valResult) {
                IndexOverlayFactory.overlayShow();
                RepairFactory.deleteRepairSubIssue(ID).then(function(result){
                    IndexOverlayFactory.overlayHide();
                    if(result.data.STATUS == 'OK' && result.data.DATA){
                        $scope.Data.repair_sub_issue.splice(index, 1);
                    }
                });
            });
        }
    }

    $scope.Data = {'RepairedIssueID':''
                    ,'RepairedTitleID':$routeParams.RepairedTitleID
                    ,'RepairedIssueName':''
                    ,'ActiveStatus':'Y'
                    ,'CreateBy':$scope.currentUser.UserID
                    ,'CreateDateTime':''};
    
    if($routeParams.RepairedIssueID != undefined || $routeParams.RepairedIssueID != null){
        $scope.loadRepairIssue($routeParams.RepairedIssueID);
    }else{
        IndexOverlayFactory.overlayHide();
    }
    
    $scope.urlRepairedTypeID = $routeParams.RepairedTypeID;

});

app.controller('RepairSubIssueManageController', function($cookies, $scope, $uibModal, $routeParams, $timeout, $interval, Upload, IndexOverlayFactory, RepairFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'manage_repair';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }

    $scope.loadRepairSubIssue = function (ID){
        RepairFactory.getRepairSubIssue(ID).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.Data = result.data.DATA;
            }
        });
    }

    $scope.updateData = function (data){
        IndexOverlayFactory.overlayShow();
        RepairFactory.updateRepairSubIssue(data).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                if($scope.Data.RepairedSubIssueID ==''){
                    $scope.Data = result.data.DATA;
                    //window.location.href = '#/manage_repair_sub_issue/' + $routeParams.RepairedTypeID + '/' + $routeParams.RepairedTitleID + '/' + $routeParams.RepairedIssueID + '/' + result.data.DATA.RepairedSubIssueID;

                }
                window.location.href = '#/';
            }
        });
    }

    $scope.confirmDelete = function (index, ID){
        if(ID == ''){
            $scope.DataList.splice(index, 1);
        }else{
            $scope.alertMessage = 'ต้องการลบ "ปัญหาย่อยงานซ่อม" นี้ ใช่หรือไม่ ?';
            var modalInstance = $uibModal.open({
                animation : true,
                templateUrl : 'html/dialog_confirm.html',
                size : 'sm',
                scope : $scope,
                backdrop : 'static',
                controller : 'ModalDialogCtrl',
                resolve : {
                    params : function() {
                        return {};
                    } 
                },
            });

            modalInstance.result.then(function (valResult) {
                IndexOverlayFactory.overlayShow();
                RepairFactory.deleteRepairSubIssue(ID).then(function(result){
                    IndexOverlayFactory.overlayHide();
                    if(result.data.STATUS == 'OK' && result.data.DATA){
                        $scope.Data.repair_sub_issue.splice(index, 1);
                    }
                });
            });
        }
    }

    $scope.Data = {'RepairedSubIssueID':''
                    ,'RepairedIssueID':$routeParams.RepairedIssueID
                    ,'RepairedSubIssueName':''
                    ,'SLA':''
                    ,'ActiveStatus':'Y'
                    ,'CreateBy':$scope.currentUser.UserID
                    ,'CreateDateTime':''};
    
    if($routeParams.RepairedSubIssueID != undefined || $routeParams.RepairedSubIssueID != null){
        $scope.loadRepairSubIssue($routeParams.RepairedSubIssueID);
    }else{
        IndexOverlayFactory.overlayHide();
    }
    
    $scope.urlRepairedTypeID = $routeParams.RepairedTypeID;
    $scope.urlRepairedTitleID = $routeParams.RepairedTitleID;
    $scope.urlRepairedIssueID = $routeParams.RepairedIssueID;

});


app.controller('RepairDescController', function($cookies, $scope, $uibModal, $routeParams, IndexOverlayFactory, RepairFactory, RegionFactory) {
    IndexOverlayFactory.overlayShow();
    var $user_session = sessionStorage.getItem('user_session');
    // alert($user_session);
    console.log($user_session);
    $user_session = window.atob(($routeParams.user_session));
        console.log($user_session);
        $user_session = decodeURIComponent($user_session);
        console.log($user_session);
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{

        $user_session = window.atob(($routeParams.user_session));
        console.log($user_session);
        $user_session = decodeURIComponent($user_session);
        console.log($user_session);
        // alert($user_session);
        sessionStorage.setItem('user_session', $user_session);
        $scope.$parent.currentUser = angular.fromJson($user_session);
        // alert($user_session);
        //
       // window.location.replace('#/logon/' + $scope.menu_selected);
    }

    IndexOverlayFactory.overlayHide();

    $scope.getRepair = function(RepairedID){
        IndexOverlayFactory.overlayShow();
        RepairFactory.getRepair(RepairedID).then(function (obj){
            IndexOverlayFactory.overlayHide();
            $scope.Repair = obj.data.DATA;
            console.log($scope.Repair);
            // load all requires
            $scope.getRepairType();
            $scope.getRepairTitle($scope.Repair.RepairedTypeID);
            $scope.getRepairIssue($scope.Repair.RepairedTitleID);
            $scope.getRepairSubIssue($scope.Repair.RepairedIssueID);
            $scope.getRegion();

            if($scope.Repair.ReceiveDateTime != null){
                $scope.AdminReceiveDate = convertDateToFullThaiDateIgnoreTime( convertDateToSQLString($scope.Repair.ReceiveDateTime) );
            }
            if($scope.Repair.RepairedStatus == 'Finish' || $scope.Repair.RepairedStatus == 'Suspend' || $scope.Repair.RepairedStatus == 'Cancel'){
                $scope.RepairStatus = $scope.Repair.RepairedStatus;
            }
        });
    }

    $scope.getRegion = function (){
        IndexOverlayFactory.overlayShow();
        RegionFactory.getAllRegion().then(function (obj){
            //console.log(obj);
            IndexOverlayFactory.overlayHide();
            $scope.RegionList = obj.data.DATA;
        });
    }

    $scope.getRepairType = function(){
        IndexOverlayFactory.overlayShow();
        RepairFactory.getRepairTypeList('view', $scope.currentUser.UserID).then(function (obj){
            IndexOverlayFactory.overlayHide();
            $scope.RepairTypeList = obj.data.DATA.DataList;
        });
    }

    $scope.getRepairTitle = function(RepairedTypeID){
        IndexOverlayFactory.overlayShow();
        RepairFactory.getRepairTitleList('view', RepairedTypeID).then(function (obj){
            IndexOverlayFactory.overlayHide();
            $scope.RepairTitleList = obj.data.DATA.DataList;
        });
    }

    $scope.getRepairIssue = function(RepairedTitleID){
        IndexOverlayFactory.overlayShow();
        RepairFactory.getRepairIssueList('view', RepairedTitleID).then(function (obj){
            IndexOverlayFactory.overlayHide();
            $scope.RepairIssueList = obj.data.DATA.DataList;
        });
    }

    $scope.getRepairSubIssue = function(RepairedIssueID){

        IndexOverlayFactory.overlayShow();
        RepairFactory.getRepairSubIssueList('view', RepairedIssueID).then(function (obj){
            IndexOverlayFactory.overlayHide();
            $scope.RepairSubIssueList = obj.data.DATA.DataList;
        });
    }

    $scope.updateRepair = function(Repair){
        $scope.alertMessage = 'ต้องการบันทึกข้อมูลการแจ้งซ่อม ใช่หรือไม่ ?';
        var modalInstance = $uibModal.open({
            animation : true,
            templateUrl : 'html/dialog_confirm.html',
            size : 'sm',
            scope : $scope,
            backdrop : 'static',
            controller : 'ModalDialogCtrl',
            resolve : {
                params : function() {
                    return {};
                } 
            },
        });

        modalInstance.result.then(function (valResult) {
            IndexOverlayFactory.overlayShow();
            RepairFactory.updateRepair(Repair).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS == 'OK'){
                    if(parseInt(result.data.DATA.RepairedID) > 0 && $routeParams.RepairedID == undefined){
                        $scope.alertMessage = 'ส่งคำร้องแจ้งซ่อมสำเร็จแล้ว';
                        var modalInstance = $uibModal.open({
                            animation : true,
                            templateUrl : 'html/custom_alert.html',
                            size : 'sm',
                            scope : $scope,
                            backdrop : 'static',
                            controller : 'ModalDialogCtrl',
                            resolve : {
                                params : function() {
                                    return {};
                                } 
                            },
                        });
                        
                        modalInstance.result.then(function (valResult) {
                            //console.log(valResult);
                            window.location.replace('#/news');
                            // window.location.replace('#/repair/' + result.data.DATA.RepairedID);
                        }, function () {});

                    }else{
                        $scope.addAlert('บันทึกสำเร็จ','success');
                    }
                }
            });
        });
    }

    $scope.updateStatusRepair = function(Repair, Status){
        var statusTxt = '';
        if(Status == 'Request'){
            statusTxt = 'ส่งคำขอแจ้งซ่อม';
        }else{
            statusTxt = 'ยกเลิกการแจ้งซ่อม';
        }

        $scope.alertMessage = 'ต้องการ' + statusTxt + ' ใช่หรือไม่ ?';
        var modalInstance = $uibModal.open({
            animation : true,
            templateUrl : 'html/dialog_confirm.html',
            size : 'sm',
            scope : $scope,
            backdrop : 'static',
            controller : 'ModalDialogCtrl',
            resolve : {
                params : function() {
                    return {};
                } 
            },
        });

        modalInstance.result.then(function (valResult) {
            IndexOverlayFactory.overlayShow();
            RepairFactory.updateStatusRepair(Repair, Status).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS == 'OK'){
                    if(parseInt(result.data.DATA.RepairedID) > 0 && $routeParams.RepairedID == undefined){
                        $scope.alertMessage = 'ส่งคำร้องแจ้งซ่อมสำเร็จแล้ว';
                        var modalInstance = $uibModal.open({
                            animation : true,
                            templateUrl : 'html/custom_alert.html',
                            size : 'sm',
                            scope : $scope,
                            backdrop : 'static',
                            controller : 'ModalDialogCtrl',
                            resolve : {
                                params : function() {
                                    return {};
                                } 
                            },
                        });
                        
                        modalInstance.result.then(function (valResult) {
                            //console.log(valResult);
                            // window.location.replace('#/');
                            window.location.replace('#/repair/' + result.data.DATA.RepairedID);
                        }, function () {});
                    }else{
                        $scope.addAlert(statusTxt + ' สำเร็จ','success');
                    }
                }
            });
        });
    }

    $scope.updateAdminReceiveRepair = function (Repair){
        $scope.alertMessage = 'ต้องการกดรับเพื่อเริ่มดำเนินการซ่อม ใช่หรือไม่ ?';
        var modalInstance = $uibModal.open({
            animation : true,
            templateUrl : 'html/dialog_confirm.html',
            size : 'sm',
            scope : $scope,
            backdrop : 'static',
            controller : 'ModalDialogCtrl',
            resolve : {
                params : function() {
                    return {};
                } 
            },
        });

        modalInstance.result.then(function (valResult) {
            IndexOverlayFactory.overlayShow();
            RepairFactory.updateAdminReceiveRepair(Repair, $scope.currentUser.UserID).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS == 'OK'){
                    $scope.AdminReceiveDate = convertDateToFullThaiDateIgnoreTime( convertDateToSQLString(result.data.DATA.ReceiveDateTime) );
                }
            });
        });
    }

    $scope.updateRepairAdmin = function(Repair, RepairStatus){
        $scope.alertMessage = 'กรณีเลือกสถานะเป็น "เสร็จสิ้น หรือ ยกเลิก" จะไม่สามารถแก้ไขข้อมูลใดๆ ได้อีก ต้องการบันทึกข้อมูลเพื่ออัพเดทสถานะ ใช่หรือไม่ ?';
        var modalInstance = $uibModal.open({
            animation : true,
            templateUrl : 'html/dialog_confirm.html',
            size : 'md',
            scope : $scope,
            backdrop : 'static',
            controller : 'ModalDialogCtrl',
            resolve : {
                params : function() {
                    return {};
                } 
            },
        });

        modalInstance.result.then(function (valResult) {
            IndexOverlayFactory.overlayShow();
            RepairFactory.updateRepairAdmin(Repair, RepairStatus).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS == 'OK'){
                    $scope.Repair = result.data.DATA;
                    $scope.addAlert('บันทึกสำเร็จ','success');
                }
            });
        });
    }

    $scope.setRepairType = function(RepairedTypeID){
        $scope.RepairTititleList = [];
        $scope.RepairIssueList = [];
        $scope.RepairSubIssueList = [];
        // load Title
        if(RepairedTypeID != null){
            $scope.getRepairTitle(RepairedTypeID);
        }   
    }

    $scope.setRepairTitle = function(RepairedTitleID){
        $scope.RepairIssueList = [];
        $scope.RepairSubIssueList = [];
        // load Issue
        if(RepairedTitleID != null){
            $scope.getRepairIssue(RepairedTitleID);
        }
    }

    $scope.setRepairIssue = function(RepairedIssueID){
        $scope.RepairSubIssueList = [];
        // load Sub Issue
        if(RepairedIssueID != null){
            $scope.getRepairSubIssue(RepairedIssueID);
        }
    }

    $scope.checkAdminStatus = function(type){   
        if(type == 'Finish'){
            $scope.Repair.SuspenedComment = '';
            $scope.Repair.CancelComment = '';
        }else if(type == 'Suspend'){
            $scope.Repair.FinishComment = '';
            $scope.Repair.CancelComment = '';            
        }else if(type == 'Cancel'){
            $scope.Repair.FinishComment = '';
            $scope.Repair.SuspenedComment = '';
        }
    }

    if($routeParams.RepairedID == undefined){
        $scope.getRegion();
        $scope.getRepairType();
    }else{
        $scope.getRepair($routeParams.RepairedID);
    }
    

    $scope.Repair = {'RepairedID':''
                    ,'RegionID':parseInt($scope.currentUser.RegionID)
                    ,'RepairedTypeID':''
                    ,'RepairedTitleID':''
                    ,'RepairedIssueID':''
                    ,'RepairedSubIssueID':''
                    ,'RepairedDetail':''
                    ,'RepairedStatus':''
                    ,'CreateBy':$scope.currentUser.UserID
                    };

    console.log($scope.Repair);
    $scope.RepairTypeList = [];
    $scope.RepairTititleList = [];
    $scope.RepairIssueList = [];
    $scope.RepairSubIssueList = [];
    $scope.RepairStatus = '';
    // Alert zone
    $scope.alerts = [];
    $scope.addAlert = function(msg, type) {
      $scope.alerts.push({
        msg: msg,
        type: type
      });
    };
});