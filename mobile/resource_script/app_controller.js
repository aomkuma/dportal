app.controller('AppController', ['$cookies','$scope', '$filter', '$uibModal','IndexOverlayFactory', 'PhoneBookFactory', 'LOMSFactory', function($cookies, $scope, $filter, $uibModal, IndexOverlayFactory, PhoneBookFactory, LOMSFactory) {
	$scope.overlay = IndexOverlayFactory;
	$scope.overlayShow = false;
	$scope.currentUser = null;
    $scope.TotalLogin = 0;
    $scope.menu_selected = '';

    setTimeout(function(){
        $(document).ready(function(){
        
            $("div.menu-navbar").css('height', $(window).height());
            // alert($("div.menu-navbar").height());
        });
    },1000);
    
    
    $scope.logout = function(){
        sessionStorage.removeItem('user_session');
        sessionStorage.removeItem('TotalLogin');
        sessionStorage.removeItem('MenuList');
        $scope.currentUser = null;
        window.location.replace('#/logon');
    }

    $scope.editProfile = function(UserLoginID){
        // console.log(UserLoginID);
        // return;
        IndexOverlayFactory.overlayShow();
        PhoneBookFactory.getUserContact(UserLoginID).then(function (result) {
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.Contact = result.data.DATA;

                // get Leave Data
                $scope.getLeaveData($scope.Contact.Email);

                var modalInstance = $uibModal.open({
                    animation: true,
                    templateUrl: 'update_contact.html',
                    size: 'md',
                    scope: $scope,
                    backdrop: 'static',
                    controller: 'ModalDialogReturnFromOKBtnCtrl',
                    resolve: {
                        params: function () {
                            return {};
                        }
                    },
                });
                modalInstance.result.then(function (valResult) {
                    $scope.updateProfile(valResult);
                });
            }else{
                alert('ไม่พบข้อมูล');
            }
        });

        $scope.updateProfile = function (Contact){
            IndexOverlayFactory.overlayShow();
            PhoneBookFactory.updateContact(Contact).then(function (result) {
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS == 'OK'){
                    $scope.dataOffset = 0;
                    $scope.tableLoad = false;
                    $scope.continueLoad = true;
                    $scope.DataList = [];
                    
                    $scope.loadList();
                }
            });
        }
    }

    $scope.getLeaveData = function(email){
        LOMSFactory.getData(email).then(function (result) {
            console.log(result.items);
            $scope.LeaveList = result.items;
        });
    }

    $scope.searchNews = function(keyword){
        window.location.href = '#/search/' + keyword;
    }

    $scope.filterMenu = function(MenuID){
        //console.log('MenuID = ', MenuID);
        $scope.MenuList = angular.fromJson(sessionStorage.getItem('MenuList'));
        var result = false;
        if(MenuID == '5' || MenuID == '6'){
            result = $filter('MenuFilter')($scope.MenuList, 6);     
            if(!result){
                result = $filter('MenuFilter')($scope.MenuList, 5);    
                return result;
            }else{
                return result;
            }
            
        }else{
            result = $filter('MenuFilter')($scope.MenuList, MenuID);
            return result;
        }
        
        
    }

    $scope.filterHeadMenu = function(MenuIDList){
        //console.log('MenuID = ', MenuID);
        var result = false;
        $scope.MenuList = angular.fromJson(sessionStorage.getItem('MenuList'));
        if(MenuIDList != ''){
            var menuIdList = MenuIDList.split(',');
            for(var i = 0; i < menuIdList.length; i++){
                result = $filter('MenuFilter')($scope.MenuList, menuIdList[i]);   
                if(result){
                    return result;
                }
            }
        }else{
            result = $filter('MenuFilter')($scope.MenuList, 1);   
            if(result){
                return result;
            }
            result = $filter('MenuFilter')($scope.MenuList, 2);   
            if(result){
                return result;
            }
            result = $filter('MenuFilter')($scope.MenuList, 3);   
            if(result){
                return result;
            }
            result = $filter('MenuFilter')($scope.MenuList, 4);   
            if(result){
                return result;
            }
            result = $filter('MenuFilter')($scope.MenuList, 5);   
            if(result){
                return result;
            }
            result = $filter('MenuFilter')($scope.MenuList, 6);   
            if(result){
                return result;
            }
            result = $filter('MenuFilter')($scope.MenuList, 7);   
            if(result){
                return result;
            }
            result = $filter('MenuFilter')($scope.MenuList, 8);   
            if(result){
                return result;
            }
            result = $filter('MenuFilter')($scope.MenuList, 9);   
            if(result){
                return result;
            }
            result = $filter('MenuFilter')($scope.MenuList, 10);   
            if(result){
                return result;
            }
        }

        return result;
    }
    //console.log('AppController ',$scope.currentUser);

    $scope.goToPage = function(page){
        window.location = '#/' + page;
    }
}]);

app.controller('NotificationController', function($scope, NotificationFactory, IndexOverlayFactory, LOMSFactory) {
    // Notifications
    $scope.totalNewNotifications = 0;
    $scope.NotificationList = [];
    $scope.continueLoad = true;
    $scope.tableLoad = false;
    $scope.offset = 0;
    $scope.showNotificationBox = false;
    $scope.showLeaveNotificationBox = false;

    $scope.getNotifications = function (regionID, groupID, userID, interval) {

        if($scope.continueLoad){
            $scope.tableLoad = true;
            NotificationFactory.getNotificationList(regionID, groupID, userID, $scope.offset).then(function(result){
                
                if(result.data.STATUS == 'OK'){
                    $scope.offset = result.data.DATA.offset;
                    $scope.continueLoad = result.data.DATA.continueLoad;
                    $scope.totalNewNotifications = result.data.DATA.totalNewNotifications;
                    
                    if(interval){
                        $scope.NotificationList = result.data.DATA.NotificationList;
                    }else{
                        for(var i = 0; i < result.data.DATA.NotificationList.length; i++){
                            $scope.NotificationList.push(result.data.DATA.NotificationList[i]);    
                        }
                    }               
                }

            });
        }
    }

    $scope.getLeaveNotifications = function (email, interval) {
        LOMSFactory.getNotificationList(email).then(function(result){
            $scope.totalNewLeaveNotifications = result.data.DATA.totalNewNotifications;
            $scope.LeaveNotificationList = result.data.DATA.NotificationList;
        });
    }

    $scope.reFormatDate = function (d) {
        return convertDateToFullThaiDate(makeDate(d));
        //return '9 July 2017';
    }

    $scope.updateAndGotoPage = function (index,notify) {
        IndexOverlayFactory.overlayShow();
        NotificationFactory.updateNotificationStatus(notify
                            ,$scope.$parent.currentUser.UserID
                            ,$scope.$parent.currentUser.RegionID
                            ,$scope.$parent.currentUser.GroupID).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.NotificationList[index].NotificationStatus = 'Seen';
                $scope.totalNewNotifications = result.data.DATA.totalNewNotifications;
                window.location.href = notify.NotificationUrl;
            }else{
                alert(result.DATA.DATA);
            }
        });

    }

    $scope.updateAndGotoLeavePage = function (index,notify) {
        if(notify.ViewStatus == 'unseen'){
            IndexOverlayFactory.overlayShow();
            LOMSFactory.updateNotificationStatus(notify.ID, notify.Email).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS == 'OK'){
                    $scope.LeaveNotificationList[index].ViewStatus = 'seen';
                    $scope.totalNewLeaveNotifications = result.data.DATA.totalNewNotifications;
                    // console.log(notify.ReturnLink);
                    //window.open(notify.ReturnLink, '_blank');
                    // window.location.href = notify.ReturnLink;

                }else{
                    alert(result.DATA.DATA);
                }
            });
        }
    }

    $scope.checkNotificationBoxStatus = function () {
        if($scope.showNotificationBox){
            $scope.showNotificationBox = false;
        }else{
            $scope.showNotificationBox = true;
        }
    }

    $scope.checkLeaveNotificationBoxStatus = function () {
        if($scope.showLeaveNotificationBox){
            $scope.showLeaveNotificationBox = false;
        }else{
            $scope.showLeaveNotificationBox = true;
        }
    }

    $scope.getLeaveNotifications($scope.$parent.currentUser.Email);
    $scope.getNotifications($scope.$parent.currentUser.RegionID, $scope.$parent.currentUser.GroupID, $scope.$parent.currentUser.UserID, false);

    // Loop for check new notification
    setInterval(function(){
        //console.log('begin interval check' );
        $scope.continueLoad = true;
        //if(!$scope.showNotificationBox){
            $scope.offset = 0;
        //}
        $scope.getNotifications($scope.$parent.currentUser.RegionID, $scope.$parent.currentUser.GroupID, $scope.$parent.currentUser.UserID, true);
        //console.log('complete interval check' );
    },30000);
});

app.controller('NotificationListController', function($scope, NotificationFactory, RegionFactory, IndexOverlayFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.$parent.menu_selected = '';
    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon');
    }

    // Notifications
    $scope.totalNewNotifications = 0;
    $scope.NotificationList = [];
    $scope.continueLoad = true;
    $scope.tableLoad = false;
    $scope.offset = 0;
    $scope.keyword = '';
    $scope.showNotificationBox = false;
    $scope.notificationType = 'ALL';

    RegionFactory.getAllRegion().then(function (obj){
        //console.log(obj);
        IndexOverlayFactory.overlayHide();
        $scope.RegionList = obj.data.DATA;
    });

    $scope.getNotifications = function (type, regionID, keyword, groupID, userID, interval) {
        keyword = keyword=='' || keyword == null?'-':keyword;
        if($scope.continueLoad){
            IndexOverlayFactory.overlayShow();
            $scope.tableLoad = true;
            NotificationFactory.getNotificationListByCondition(type, regionID, keyword, groupID, userID, $scope.offset).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS == 'OK'){
                    $scope.offset = result.data.DATA.offset;
                    $scope.continueLoad = result.data.DATA.continueLoad;
                    $scope.totalNewNotifications = result.data.DATA.totalNewNotifications;
                    
                    if(interval){
                        $scope.NotificationList = result.data.DATA.NotificationList;
                    }else{
                        for(var i = 0; i < result.data.DATA.NotificationList.length; i++){
                            $scope.NotificationList.push(result.data.DATA.NotificationList[i]);    
                        }
                    }               
                }

            });
        }
    }

    $scope.reFormatDate = function (d) {
        return convertDateToFullThaiDate(makeDate(d));
        //return '9 July 2017';
    }

    $scope.updateAndGotoPage = function (index,notify) {
        IndexOverlayFactory.overlayShow();
        NotificationFactory.updateNotificationStatus(notify
                            ,$scope.$parent.currentUser.UserID
                            ,$scope.$parent.currentUser.RegionID
                            ,$scope.$parent.currentUser.GroupID).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.NotificationList[index].NotificationStatus = 'Seen';
                $scope.totalNewNotifications = result.data.DATA.totalNewNotifications;
                window.location.href = notify.NotificationUrl;
            }else{
                alert(result.DATA.DATA);
            }
        });

    }

    $scope.checkNotificationBoxStatus = function () {
        if($scope.showNotificationBox){
            $scope.showNotificationBox = false;
        }else{
            $scope.showNotificationBox = true;
        }
    }

    $scope.convertStatus = function (word){
        switch(word){
            case 'Finish' : return 'เสร็จสิ้น';
            case 'Approve' : return 'อนุมัติ';
            case 'Reject' : return 'ปฏิเสธ';
            case 'Cancel' : return 'ยกเลิก';
            case 'Receive' : return 'กำลังดำเนินการ';
            case 'Request' : return 'รอการพิจารณา';
            case 'Suspend' : return 'ยกเลิกชั่วคราว';
            default : return '';
        }
    }

    $scope.setCondition = function(type){
        $scope.totalNewNotifications = 0;
        $scope.NotificationList = [];
        $scope.continueLoad = true;
        $scope.tableLoad = false;
        $scope.offset = 0;
        $scope.showNotificationBox = false;
        $scope.getNotifications($scope.notificationType, $scope.RegionID, $scope.keyword, $scope.currentUser.GroupID, $scope.currentUser.UserID, false);
    }

    $scope.RegionID = $scope.currentUser.RegionID;
    $scope.getNotifications($scope.notificationType, $scope.RegionID, $scope.keyword, $scope.currentUser.GroupID, $scope.currentUser.UserID, false);
    
    // // Loop for check new notification
    // setInterval(function(){
    //     //console.log('begin interval check' );
    //     $scope.continueLoad = true;
    //     //if(!$scope.showNotificationBox){
    //         $scope.offset = 0;
    //     //}
    //     $scope.getNotifications($scope.$parent.currentUser.RegionID, $scope.$parent.currentUser.GroupID, $scope.$parent.currentUser.UserID, true);
    //     //console.log('complete interval check' );
    // },15000);
});

app.controller('SearchNewsController', function($scope, $routeParams, NewsFactory, IndexOverlayFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.$parent.menu_selected = '';
    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon');
    }

    $scope.$parent.news_keyword = $routeParams.keyword;
    NewsFactory.search($routeParams.keyword).then(function(result){
        IndexOverlayFactory.overlayHide();
        if(result.data.STATUS == 'OK'){
            $scope.NewsList = result.data.DATA.NewsList;
            $scope.AttachFileList = result.data.DATA.AttachFileList;
        }

    });
    
    $scope.convertDateToThai = function(date){
        return convertDateToFullThaiDateIgnoreTime( convertDateToSQLString(date));
    }
});

app.controller('HomeController', function($scope, $location, $sce, IndexOverlayFactory, HomeFactory, NewsFactory) {
	//sessionStorage.removeItem('user_session');
    $scope.$parent.menu_selected = '';
    var $user_session = sessionStorage.getItem('user_session');
    $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
    
    if($user_session != null){
		$scope.$parent.currentUser = angular.fromJson($user_session);
        
	}else{
	   window.location.replace('#/logon');
       return;
	}

    $scope.loadNewsList = function () {
        if($scope.continueLoad){
            $scope.tableLoad = true;
            NewsFactory.getNewsList($scope.dataOffset
                                ,$scope.condition.RegionID 
                                ,$scope.condition.HideNews
                                ,$scope.condition.CurrentNews
                                ,$scope.condition.WaitApprove).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS == 'OK'){
                    $scope.tableLoad = false;
                    $scope.dataOffset =  result.data.DATA.offset;
                    $scope.continueLoad = result.data.DATA.continueLoad;
                    for(var i = 0; i < result.data.DATA.DataList.length; i++){

                        result.data.DATA.DataList[i].NewsDateTimeFormat = convertDateToFullThaiDateIgnoreTime( convertDateToSQLString(result.data.DATA.DataList[i].NewsDateTimeFormat) );
                        //console.log(result.data.DATA.DataList[i].NewsDateTimeFormat);
                        $scope.DataList.push(result.data.DATA.DataList[i]);   

                    }
                }
            });
        }
    }

    $scope.setCondition = function (){
        if($scope.condition.RegionID ==null){
            $scope.condition.RegionID = '0';
        }
        $scope.DataList = [];
        $scope.dataOffset = 0;
        $scope.tableLoad = false;
        $scope.continueLoad = true;
        $scope.loadList();
    }

    $scope.showListPage = function (){
        $scope.showPage = 'MAIN';
    }

    $scope.RegionList = [];
    $scope.showPage = '';
    $scope.dataOffset = 0;
    $scope.tableLoad = false;
    $scope.continueLoad = true;
    $scope.DataList = [];
    $scope.condition ={'RegionID':'0','HideNews':'0','CurrentNews':'0','WaitApprove':'0'};

    $scope.loadNewsList();

});

app.controller('LoginController',function($scope, $routeParams, LoginFactory, IndexOverlayFactory){
	
    
	$scope.user = {'Username':'','Password':''};

    var reDirect = '';
    if($routeParams.redirect_url !== undefined){
        reDirect = $routeParams.redirect_url;
        console.log(reDirect);
    }
	$scope.showError = false; // set Error flag
	$scope.showSuccess = false; // set Success Flag
    $scope.showUserPass = false;
    IndexOverlayFactory.overlayHide();
	//------- Authenticate function
	$scope.authenticate = function (confirm, event){
        
        if(confirm=='login' || event.keyCode == '13'){
            if($scope.user.Username != '' && $scope.user.Username != null && $scope.user.Password != '' && $scope.user.Password != null){
        		var flag= false;
                $scope.showUserPass = false;
                $scope.showError = false;
                $scope.showSuccess = false;
                IndexOverlayFactory.overlayShow();
        		LoginFactory.login($scope.user).then(function (user) {
                    IndexOverlayFactory.overlayHide();
            		//-------- set error or success flags
            		if(user.data.STATUS == 'OK'){
            			$scope.showError = false;
                        $scope.showUserPass = false;
            			$scope.showSuccess = true;
                        $scope.$parent.TotalLogin = user.data.DATA.TotalLogin;
                        sessionStorage.setItem('user_session' , JSON.stringify(user.data.DATA.UserData));
                        sessionStorage.setItem('TotalLogin' , $scope.$parent.TotalLogin);
                        sessionStorage.setItem('MenuList' , JSON.stringify(user.data.DATA.MenuList));
                        setTimeout(function(){
                            window.location.replace('#/' + reDirect);    
                        }, 1000);
                        
            		}
            		else{
                        $scope.errorMsg = user.data.DATA; //'Invalid username or password';
            			$scope.showError = true;
            			$scope.showSuccess = false;
            		}
                    
                });
            }else{
                $scope.showUserPass = true;
            }
        }
	}

});

app.controller('ForgotPasswordController',function($scope, $routeParams, LoginFactory, IndexOverlayFactory){
    
    $scope.ForgotPassword = {'Username':'', 'OTP':'', 'NewPassword':'', 'ConfirmNewPassword':''};
    $scope.step = 1;

    $scope.verifyUsername = function (username){
        var flag= false;
        IndexOverlayFactory.overlayShow();
        LoginFactory.verifyUsername(username).then(function (user) {
            IndexOverlayFactory.overlayHide();
            //-------- set error or success flags
            if(user.data.STATUS == 'OK' && username == user.data.DATA){
                $scope.showUsernameError = false;
                $scope.step = 2;
            }
            else{
                $scope.showUsernameError = true;
            }
            
        });
    }

    $scope.requestOTP = function(username){
        IndexOverlayFactory.overlayShow();
        LoginFactory.requestOTP(username).then(function (user) {
            IndexOverlayFactory.overlayHide();
            if(user.data.STATUS == 'OK'){
                $scope.showOTPNotify = true;
                $scope.showOTPError = false;
            }
            else{
                $scope.showOTPError = true;
                $scope.showOTPNotify = false;
            }
        });
    }

    $scope.verifyOTP = function (otp){
        $scope.showOTPNotify = false;
        $scope.showOTPError = false;
        
        IndexOverlayFactory.overlayShow();
        LoginFactory.verifyOTP(otp).then(function (user) {
            IndexOverlayFactory.overlayHide();
            //-------- set error or success flags
            if(user.data.STATUS == 'OK'){
                $scope.showOTPNotfound = false;
                $scope.step = 3;
            }
            else{
                $scope.showOTPNotfound = true;
            }
            
        });
    }

    $scope.changePassword = function (objChangePassword){
        var flag= false;
        IndexOverlayFactory.overlayShow();
        LoginFactory.changePassword(objChangePassword).then(function (user) {
            IndexOverlayFactory.overlayHide();
            //-------- set error or success flags
            if(user.data.STATUS == 'OK'){
                $scope.showChangePassSuccess = true;
                setTimeout(function(){
                    window.location.replace('#/logon');    
                }, 1000);
                
            }
            else{
                $scope.showChangePassSuccess = false;
            }
            
        });

    }

    $scope.showUsernameError = false;
    $scope.showOTPNotify = false;
    $scope.showOTPError = false;
    $scope.showOTPNotfound = false;
    $scope.showChangePassSuccess = false;

});

app.controller('RoomOverviewController', function($scope, $location, $compile, $routeParams, IndexOverlayFactory, ReserveRoomFactory, RegionFactory) {
	var $user_session = sessionStorage.getItem('user_session');
    // alert($user_session);
    // console.log($user_session);
    
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
    

    
    // Load Region List
    $scope.loadRegionList = function(){
        RegionFactory.getAllRegion().then(function (obj){
            //console.log(obj);
            IndexOverlayFactory.overlayHide();
            $scope.RegionList = obj.data.DATA;
        });
    }
    // End Load Region List
    
    // Accordion Zone
    $scope.oneAtATime = true;
    
    $scope.status = {
        isCustomHeaderOpen: false,
        isFirstOpen: true,
        isFirstDisabled: false
    };
	// End Accordion Zone
    
    // Generate Date Range
    $scope.setDateRange = function(index){
        //console.log('$scope.regionSelected', $scope.regionSelected);
        if($scope.regionSelected == ''){
            $scope.regionSelected = $scope.RegionList[0].RegionID;
            //console.log('$scope.regionSelected', $scope.regionSelected);
        }
        $scope.dateSelected = $scope.dateRange[index];
        $scope.generateDateRange($scope.dateRange[index]);
        $scope.loadReserveList($scope.regionSelected);
    }
    
    $scope.generateDateRange = function(selectedDate){
        
        $scope.dateRange = [];
        $scope.dateRangeDisplay = [];
        
        var dateArr = selectedDate.split('-');
        var mm = parseInt(dateArr[1]) - 1;
        
        //console.log('selectedDate' , selectedDate);
        var beginDate = -3;
        for(var i = 0; i < 7; i++){
            
            var dd = new Date();
            dd.setYear(dateArr[0]);
            dd.setMonth(mm,parseInt(dateArr[2]));
            // dd.setDate(dateArr[2]);  
            
            var t = addDays(dd, beginDate);
            var month = (parseInt(t.getMonth()) + 1);
            month = month.toString().length==1?'0'+month:month;
            var date = t.getDate().length==1?'0'+t.getDate():t.getDate();
            newDate = t.getFullYear() + '-' + month + '-' + date;
            $scope.dateRange.push(newDate);
            
            // Date to Display
            $scope.dateRangeDisplay.push(convertDateToThai(t));
            
            beginDate++;
            
        }
    }
    
    // Load Reserve List
    $scope.loadReserveList = function(regionID){
        
        if(regionID !== undefined && regionID !== null && regionID !== ''){
            $scope.regionSelected = regionID;
        }
        
        // IndexOverlayFactory.overlayShow();
        ReserveRoomFactory.getRoomReserveDetail(regionID, $scope.dateSelected).then(function(obj) {
            //console.log(obj);
            IndexOverlayFactory.overlayHide();
            if(obj.data.STATUS=='OK'){
                $scope.ReserveList = obj.data.DATA;
                var ownerID = $scope.currentUser.UserID;
                for(var i = 0; i < $scope.ReserveList.length; i++){
                    //console.log($scope.ReserveList[i].ReserveList);
                    var timeList = [
                            {time:'07:00:00.000',status:''}
                            ,{time:'08:00:00.000',status:''}
                            ,{time:'09:00:00.000',status:''}
                            ,{time:'10:00:00.000',status:''}
                            ,{time:'11:00:00.000',status:''}
                            ,{time:'12:00:00.000',status:''}
                            ,{time:'13:00:00.000',status:''}
                            ,{time:'14:00:00.000',status:''}
                            ,{time:'15:00:00.000',status:''}
                            ,{time:'16:00:00.000',status:''}
                            ,{time:'17:00:00.000',status:''}
                            ,{time:'18:00:00.000',status:''}
                            ,{time:'19:00:00.000',status:''}
                            ,{time:'20:00:00.000',status:''}];
                    var ReserveList = $scope.ReserveList[i].ReserveList;
                    for(var j = 0; j < ReserveList.length; j++){  
                        var reserveStartDate = makeDate(ReserveList[j].StartDateTime);
                        var reserveEndDate = makeDate(ReserveList[j].EndDateTime);
                        var cntTimeIndex = 0;

                        for(var k = 7; k <= 20; k++){
                            
                            var hour = k + ':00:00.000';
                            if(k < 10){
                                hour = '0' + hour;
                            }
                            
                            var curDateTime = makeDate($scope.dateSelected + ' ' + hour);
                            var statusTxt = '';
                            if((reserveStartDate.getTime() == curDateTime.getTime()) 
                                || (reserveEndDate.getTime() > curDateTime.getTime() 
                                    && curDateTime.getTime() > reserveStartDate.getTime())){
                                if(ownerID == ReserveList[j].CreateBy && ReserveList[j].ReserveStatus == 'Approve'){
                                    //console.log('Approve');
                                    timeList[cntTimeIndex].status = 'reserve_approved';
                                }else if(ownerID == ReserveList[j].CreateBy && (ReserveList[j].ReserveStatus == 'Request' || ReserveList[j].ReserveStatus == '')){
                                    timeList[cntTimeIndex].status = 'reserve_waiting';
                                }else if(ownerID != ReserveList[j].CreateBy  || ReserveList[j].ReserveStatus == 'Reject'){
                                    //console.log('reserve_unvailable', ownerID +'!='+ ReserveList[i].CreateBy);
                                    timeList[cntTimeIndex].status = 'reserve_unvailable';
                                }
                                
                            }

                            cntTimeIndex++;
                            //timeList.push({time : hour,status : statusTxt});
                        }

                        /*  
                        
                        */
                    }
                    $scope.ReserveList[i].timeList = timeList;
                }
            }else{
                alert(obj.data.DATA);
            }
            
        });
    }
    
    // End Load Reserve List
    
    // Generate Reserve List                     
    $scope.getStatus = function(currentTime, ReserveList, ownerID){
        var curDateTime = makeDate($scope.dateSelected + ' ' + currentTime);
        for(var i=0; i < ReserveList.length; i++){
            
            var reserveStartDate = makeDate(ReserveList[i].StartDateTime);
            var reserveEndDate = makeDate(ReserveList[i].EndDateTime);
            
            if((reserveStartDate.getTime() == curDateTime.getTime()) || (reserveEndDate.getTime() >= curDateTime.getTime() && curDateTime.getTime() > reserveStartDate.getTime())){
                if(ownerID == ReserveList[i].CreateBy && ReserveList[i].ReserveStatus == 'Approve'){
                    return 'reserve_approved';
                }else if(ownerID == ReserveList[i].CreateBy && (ReserveList[i].ReserveStatus == 'Request' || ReserveList[i].ReserveStatus == '')){
                    return 'reserve_waiting';
                }else if(ownerID != ReserveList[i].CreateBy  || ReserveList[i].ReserveStatus == 'Reject'){
                    return 'reserve_unvailable';
                }
                
            }
        }
        
    }
    
    $scope.getReserveInfo = function (currentTime, ReserveList, ownerID) {
        //console.log('getReserveInfo', ReserveList);
        var curDateTime = makeDate($scope.dateSelected + ' ' + currentTime);
        for(var i=0; i < ReserveList.length; i++){
                
            var reserveStartDate = makeDate(ReserveList[i].StartDateTime);
            var reserveEndDate = makeDate(ReserveList[i].EndDateTime);
            
            if((reserveStartDate.getTime() == curDateTime.getTime()) || (reserveEndDate.getTime() >= curDateTime.getTime() && curDateTime.getTime() > reserveStartDate.getTime())){
                
                return ReserveList[i].TopicConference;
                
            }
        }
    }
    
    $scope.getReserveInfoView = function (currentTime, ReserveList, ownerID) {
        //console.log('getReserveInfo', ReserveList);
        var curDateTime = makeDate($scope.dateSelected + ' ' + currentTime);
        for(var i=0; i < ReserveList.length; i++){
                
            var reserveStartDate = makeDate(ReserveList[i].StartDateTime);
            var reserveEndDate = makeDate(ReserveList[i].EndDateTime);
            
            if((reserveStartDate.getTime() == curDateTime.getTime()) || (reserveEndDate.getTime() >= curDateTime.getTime() && curDateTime.getTime() > reserveStartDate.getTime())){
                
                return 'View';
                
            }
        }
    }
    
    // End Generate Reserve List
    //console.log($scope.$parent.currentUser);
    $scope.gotoBookingRoom = function (userID, roomID, currentTime, ReserveList) {
        // IndexOverlayFactory.overlayShow();
        //console.log(userID, roomID);
        if(currentTime!== undefined && ReserveList !== undefined){
            var curDateTime = makeDate($scope.dateSelected + ' ' + currentTime);
            var create_bool = true;
            for(var i=0; i < ReserveList.length; i++){
                    
                var reserveStartDate = makeDate(ReserveList[i].StartDateTime);
                var reserveEndDate = makeDate(ReserveList[i].EndDateTime);
                
                if((reserveStartDate.getTime() == curDateTime.getTime()) || (reserveEndDate.getTime() > curDateTime.getTime() && curDateTime.getTime() > reserveStartDate.getTime())){
                    create_bool = false;
                    window.location.href = '#/roombooking/' + userID + '/' + roomID +'/' + null + '/' + ReserveList[i].ReserveRoomID ;
                    
                }
            }

            if(create_bool){
                console.log('create page');
                window.location.href = '#/roombooking/' + userID + '/' + roomID + '/' + $scope.dateSelected + ' ' + currentTime + '/-1//'+$routeParams.user_session;        
            }
            
        }else{
            window.location.href = '#/roombooking/' + userID + '/' + roomID +'/' + null + '/-1//'+$routeParams.user_session;    
        }
        
    }

    // All variables
    $scope.regionSelected = '';
    $scope.RegionList = [];
    $scope.ReserveList = [];
    var curDate = new Date();
    var curMonth = (parseInt(curDate.getMonth()) + 1);
    curMonth = curMonth.toString().length==1?'0'+curMonth:curMonth;
    var curDay = curDate.getDate().length==1?'0'+curDate.getDate():curDate.getDate();
    $scope.dateSelected = curDate.getFullYear() + '-' + curMonth + '-' + curDay;
    //console.log($scope.dateSelected);
    $scope.dateRange = [];
    $scope.dateRangeDisplay = [];
    $scope.timeList = ['07:00:00.000','08:00:00.000','09:00:00.000','10:00:00.000','11:00:00.000','12:00:00.000'
                        ,'13:00:00.000','14:00:00.000','15:00:00.000','16:00:00.000','17:00:00.000','18:00:00.000'
                        ,'19:00:00.000','20:00:00.000'];  
    $scope.isFirstOpen = true;                    
    
    // End All variables
    // Set default date range
    $scope.loadRegionList();                    
    $scope.generateDateRange($scope.dateSelected);
    setTimeout(function(){
        $scope.loadReserveList($scope.currentUser.RegionID);
    },300);
});

app.controller('RoomBookingController', function($scope, $location, $http, $filter, $uibModal, $routeParams, IndexOverlayFactory, ReserveRoomFactory, $routeParams) {
	//console.log(BookingRoomInfo.data.DATA);
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
    
    // Variables zone
    $scope.dateOptions1 = {
        minDate: new Date(),
        showWeeks: true
      };

    $scope.dateOptions2 = {
        minDate: new Date(),
        showWeeks: true
      };
    $scope.popup1 = {
        opened: false
    };

    $scope.popup2 = {
        opened: false
    };
    $scope.open1 = function() {
        $scope.popup1.opened = true;
    };

    $scope.open2 = function() {
        $scope.dateOptions2.minDate = $scope.ReserveRoomInfo.StartDate==null?new Date():$scope.ReserveRoomInfo.StartDate;
        $scope.popup2.opened = true;
    };

    $scope.checkBetweenDate = function(changeType){
        if($scope.ReserveRoomInfo.StartDate != undefined && $scope.ReserveRoomInfo.EndDate != undefined 
            && ($scope.ReserveRoomInfo.StartTime != undefined &&$scope.ReserveRoomInfo.StartTime != '') 
            && ($scope.ReserveRoomInfo.EndTime != undefined &&$scope.ReserveRoomInfo.EndTime != '')){
            var startTime = ($scope.ReserveRoomInfo.StartTime==''?'00:00':$scope.ReserveRoomInfo.StartTime);
            var endTime = ($scope.ReserveRoomInfo.EndTime==''?'00:00':$scope.ReserveRoomInfo.EndTime);
            var start = concatDateTime($scope.ReserveRoomInfo.StartDate, startTime);
            var end = concatDateTime($scope.ReserveRoomInfo.EndDate, endTime);
            if(start > end){
                alert('เวลาเริ่มต้นมากกว่าเวลาสิ้นสุด');
                if(changeType == 'StartDate'){
                    $scope.ReserveRoomInfo.StartDate = null;
                }else if(changeType == 'StartTime'){
                    $scope.ReserveRoomInfo.StartTime = '';
                }else if(changeType == 'EndDate'){
                    $scope.ReserveRoomInfo.EndDate = null;
                }else if(changeType == 'EndTime'){
                    $scope.ReserveRoomInfo.EndTime = '';
                }
            }
        }
    }

    $scope.destinationRoomID = $routeParams.destinationRoomID;

    $scope.BookingDetail = {};
    
    $scope.StartTimeList = ['07:00','08:00','09:00','10:00','11:00','12:00'
                        ,'13:00','14:00','15:00','16:00','17:00','18:00'
                        ,'19:00','20:00','21:00'];  
    $scope.EndTimeList = ['07:00','08:00','09:00','10:00','11:00','12:00'
                        ,'13:00','14:00','15:00','16:00','17:00','18:00'
                        ,'19:00','20:00','21:00'];  
    
    $scope.ReserveRoomInfo = {'AdminComment':''
                            ,'CreateBy':$scope.$parent.currentUser.UserID
                            ,'CreateDateTime':""
                            ,'EndDateTime':""
                            ,'Remark':''
                            ,'ReserveRoomID':""
                            ,'ReserveStatus':""
                            ,'RoomID':$routeParams.roomID
                            ,'SnackStatus':''
                            ,'StartDateTime':""
                            ,'StartTime':""
                            ,'TopicConference':""};

    if($routeParams.roomReserveID == -1 && $routeParams.startDateTime != 'null'){
        $scope.ReserveRoomInfo.StartDate = convertDateToSQLString($routeParams.startDateTime);
        $scope.ReserveRoomInfo.StartTime = convertTimeToSQLString($routeParams.startDateTime);
    }
                //console.log($scope.ReserveRoomInfo);
    $scope.InternalAttendeeList = [];
    $scope.ExternalAttendeeList = [];
    $scope.DeviceList = [];
    $scope.FoodList = [];
    $scope.RequestUser = [];
    $scope.VerifyUser = [];

    ReserveRoomFactory.getDefaultBookingRoomInfo($routeParams.userID, $routeParams.roomID, $routeParams.roomReserveID).then(function(BookingRoomInfo){
        IndexOverlayFactory.overlayHide();   
        if(BookingRoomInfo.data.STATUS == 'OK'){
            $scope.BookingDetail = BookingRoomInfo.data.DATA.RoomInfo;
            if(BookingRoomInfo.data.DATA.reserveRoomInfo !== null){
                    $scope.ReserveRoomInfo = BookingRoomInfo.data.DATA.reserveRoomInfo;

                    if($scope.currentUser.UserID == BookingRoomInfo.data.DATA.reserveRoomInfo.AdminID){
                        $scope.AdminStatus = '';
                        //$scope.AdminComment = '';
                    }
                    
                    $scope.ReserveRoomInfo.StartDate = convertDateToSQLString($scope.ReserveRoomInfo.StartDateTime);
                    $scope.ReserveRoomInfo.StartTime = convertTimeToSQLString($scope.ReserveRoomInfo.StartDateTime);
                    $scope.ReserveRoomInfo.EndDate = convertDateToSQLString($scope.ReserveRoomInfo.EndDateTime);
                    $scope.ReserveRoomInfo.EndTime = convertTimeToSQLString($scope.ReserveRoomInfo.EndDateTime);
                    
                    if($scope.ReserveRoomInfo.SnackStatus == 'Breakfast'){
                        $scope.ReserveRoomInfo.Breakfast = true;
                    }
                    else if($scope.ReserveRoomInfo.SnackStatus == 'Lunch'){
                        $scope.ReserveRoomInfo.Lunch = true;
                    }
                    else if($scope.ReserveRoomInfo.SnackStatus == 'Both'){
                        $scope.ReserveRoomInfo.Breakfast = true;
                        $scope.ReserveRoomInfo.Lunch = true;
                    }
                    
                $scope.InternalAttendeeList = BookingRoomInfo.data.DATA.internalAttendeeList;
                $scope.ExternalAttendeeList = BookingRoomInfo.data.DATA.externalAttendeeList;
                $scope.DeviceList = BookingRoomInfo.data.DATA.deviceList;
                $scope.FoodList = BookingRoomInfo.data.DATA.foodList;
                $scope.RequestUser = BookingRoomInfo.data.DATA.RequestUser;
                $scope.VerifyUser = BookingRoomInfo.data.DATA.VerifyUser;
            }
            $scope.RoomDestinationList = BookingRoomInfo.data.DATA.roomDestinationList;
            $scope.RoomDestinationAdminIndex = -1;
            if($scope.RoomDestinationList != null){
                for(var i = 0; i < $scope.RoomDestinationList.length; i++){
                    if($scope.RoomDestinationList[i].ReserveStatus != '' && $scope.RoomDestinationList[i].ReserveStatus != null 
                        && $scope.RoomDestinationList[i].ReserveRoomID == $scope.ReserveRoomInfo.ReserveRoomID){
                        $scope.RoomDestinationList[i].selected_room = true;
                    }else{
                        $scope.RoomDestinationList[i].selected_room = false;
                    }

                    if($scope.destinationRoomID == $scope.RoomDestinationList[i].DestinationRoomID){
                        $scope.RoomDestinationAdminIndex = i;
                    }
                }
            }
            //console.log($scope.destinationRoomID);
            if($scope.destinationRoomID != undefined && $scope.RoomDestinationAdminIndex != -1){
                if($scope.currentUser.UserID == $scope.RoomDestinationList[$scope.RoomDestinationAdminIndex].VerifyBy){
                    $scope.ReserveRoomDestination = $scope.RoomDestinationList[$scope.RoomDestinationAdminIndex];
                    //$scope.AdminStatus = 'Reject';
                        //$scope.AdminComment = '';
                }
                
            }

            if($scope.ReserveRoomInfo.ReserveRoomID != '' && $scope.ReserveRoomInfo.ReserveStatus == ''){
                timeoutReserve = setTimeout(function(){
                    ReserveRoomFactory.cancelRoom($scope.ReserveRoomInfo.ReserveRoomID).then(function(result){
                        // IndexOverlayFactory.overlayHide();
                        if(result.data.STATUS == 'OK'){
                            $scope.alertMessage = '<b>SessionTimeout</b><br>การทำรายการจองห้องประชุมถูกยกเลิกเนื่องจากเกินเวลา 5 นาที';
                            var modalInstance = $uibModal.open({
                                animation : true,
                                templateUrl : 'html/custom_alert.html',
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
                                //console.log(valResult);
                                window.location.replace('#/');
                            }, function () {});
                            
                        }
                    });
                    //alert('Session timeout!');
                }, 300000);
            }

        }else{
            alert(BookingRoomInfo.data.DATA);
            history.back();
        }

    });    
    // End Variables zone

    $scope.addExternalAdtendee = function (obj) {
        var res = $scope.ExternalAttendeeList.forEach(function(data) {
            console.log(data.AttendeeName , obj.Name);
            if(data.AttendeeName == obj.Name){
                alert('Duplicate name');
                return false;
            }
        });
        if(!res){
            $scope.ExternalAttendeeList.push(obj);
        }
    }
    
    $scope.saveDraft = function (){
        IndexOverlayFactory.overlayShow();
        if($scope.ReserveRoomInfo.Breakfast && $scope.ReserveRoomInfo.Lunch){
            $scope.ReserveRoomInfo.SnackStatus = 'Both';
        }else if($scope.ReserveRoomInfo.Breakfast){
            $scope.ReserveRoomInfo.SnackStatus = 'Breakfast';
        }else if($scope.ReserveRoomInfo.Lunch){
            $scope.ReserveRoomInfo.SnackStatus = 'Lunch';
        }else{
            $scope.ReserveRoomInfo.SnackStatus = '';
        }
        
        $scope.ReserveRoomInfo.StartDateTime = concatDateTimeSQL($scope.ReserveRoomInfo.StartDate, $scope.ReserveRoomInfo.StartTime);
        $scope.ReserveRoomInfo.EndDateTime = concatDateTimeSQL($scope.ReserveRoomInfo.EndDate, $scope.ReserveRoomInfo.EndTime);
        
        // console.log($scope.ReserveRoomInfo);return;
        ReserveRoomFactory.updateReserveRoomInfo($scope.ReserveRoomInfo
                                                ,$scope.BookingDetail
                                                ,$routeParams.roomReserveID).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS=='OK'){
                //console.log(result);
                if(parseInt(result.data.DATA.ReserveRoomID) > 0 && $routeParams.roomReserveID == '-1'){
                    window.location.replace('#/roombooking/' + $routeParams.userID + '/' + $routeParams.roomID + '/' + $routeParams.startDateTime + '/' + result.data.DATA.ReserveRoomID);
                }
                $scope.addAlert('บันทึกสำเร็จ','success');
            }else{
                alert(result.data.DATA.MSG);
            }            
            
        });
    }

    $scope.markStatus = function(ReserveRoomID, ReserveStatus, AdminComment){
        $scope.alertMessage = 'ต้องการ '+(ReserveStatus=='Approve'?'อนุมัติ':'ไม่อนุมัติ')+' การจองห้องประชุมนี้ ใช่หรือไม่ ?';
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
            ReserveRoomFactory.markStatus($scope.ReserveRoomInfo 
                                        ,$scope.BookingDetail
                                        ,$scope.InternalAttendeeList
                                        ,$scope.ExternalAttendeeList
                                        ,$scope.DeviceList
                                        ,$scope.FoodList
                                        ,$scope.RequestUser
                                        , ReserveRoomID
                                        , ReserveStatus
                                        , AdminComment).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS=='OK'){
                    
                    $scope.addAlert('บันทึกสำเร็จ','success');
                    window.location.replace('#/');
                }else if(result.data.STATUS == 'ERROR'){
                    $scope.alertMessage = result.data.DATA.MSG;
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
                    window.location.replace('#/');
                }else{
                    $scope.addAlert('เกิดข้อผิดพลาดขณะทำงาน','danger');
                }
            });
        });
    }

    $scope.markStatusRoomDestination = function(ReserveRoomID, ReserveStatus, AdminComment){
        $scope.alertMessage = 'ต้องการ '+(ReserveStatus=='Approve'?'อนุมัติ':'ไม่อนุมัติ')+' การจองห้องประชุมนี้ ใช่หรือไม่ ?';
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
            ReserveRoomFactory.markStatusRoomDestination($scope.ReserveRoomInfo ,$scope.ReserveRoomDestination, ReserveRoomID, ReserveStatus, AdminComment).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS=='OK'){
                    $scope.addAlert('บันทึกสำเร็จ','success');
                    window.location.replace('#/');
                }else{
                    $scope.addAlert('เกิดข้อผิดพลาดขณะทำงาน','danger');
                }
            });
        });
    }
    
    $autocompleteUserResult = [];
    $scope.searchUserAutoComplete = function (val, qtype){
		val = encodeURIComponent(val);
		return $http.get(servicesUrl + "/dpo/public/autocomplete/" + qtype + "/" + val).then(function(response){
		  
          $autocompleteUserResult = response.data.data.DATA;
          var loop = $autocompleteUserResult.length;
          //console.log($autocompleteUserResult);
          if(loop > 0){
              var objList = [];
              for(var i = 0; i < loop; i++){
                objList.push((i + 1) + ' : ' + $autocompleteUserResult[i].FirstName + ' ' + $autocompleteUserResult[i].LastName);
              }
              return objList;
          }else{
            return null;
          }
	      
	    });
	};
    $scope.autocompleteUserSelected = function ($item, $model, $label){
        
        IndexOverlayFactory.overlayShow();
        var itemSplit = $item.split(':');
        var index = parseInt(itemSplit[0].trim()) - 1;
        //console.log('index', index);
        
        // Check UserID already exist
        var offset = $filter('FindUserID')($scope.InternalAttendeeList, $autocompleteUserResult[index].UserID);
        //console.log('offset', offset, 'index' , index);
        if(offset == -1){
            // Update attendee data
            ReserveRoomFactory.updateAttendee(
            {
                'UserID' : $autocompleteUserResult[index].UserID
                ,'ReserveRoomID':$routeParams.roomReserveID
                ,'CreateBy':$scope.$parent.currentUser.UserID
            }).then(function(result){
                IndexOverlayFactory.overlayHide();
                $scope.InternalAttendeeList.push({
                    'UserID' : $autocompleteUserResult[index].UserID
                    ,'ReserveRoomID':$routeParams.roomReserveID
                    ,'FirstName':$autocompleteUserResult[index].FirstName
                    ,'LastName':$autocompleteUserResult[index].LastName
                });
            });
            
            
        }else{
            alert('รายชื่อดังกล่าวได้ถูกเลือกไว้แล้ว');
            IndexOverlayFactory.overlayHide();
        }
        $scope.BookingDetail.attendee = '';
        
    }

    $scope.autocompleteExUserSelected = function ($item, $model, $label){
        console.log($item);
        //IndexOverlayFactory.overlayShow();
        var itemSplit = $item.split(':');
        var index = parseInt(itemSplit[0].trim()) - 1;
        //console.log('index', index);
        
        // Check UserID already exist
        var offset = $filter('FindExUserID')($scope.ExternalAttendeeList, $autocompleteUserResult[index].PhoneBookID);
        //console.log('offset', offset, 'index' , index);
        if(offset == -1){
            // Update attendee data
            $scope.AttendeeExt.Name = itemSplit[1];
            $scope.AttendeeExt.Email = $autocompleteUserResult[index].Email;
            $scope.AttendeeExt.Mobile = $autocompleteUserResult[index].Mobile;
        }
        //$scope.AttendeeExt = {'Name':'','':'Email','Mobile':''};
        
    }
    
    $scope.deleteInternalAttendee = function (index, userID, reserveRoomID){
        //console.log(userID, reserveRoomID);
        IndexOverlayFactory.overlayShow();
        ReserveRoomFactory.deleteAttendee(userID,reserveRoomID).then(function(result){
            IndexOverlayFactory.overlayHide();
            $scope.InternalAttendeeList.splice(index, 1);
            $scope.addAlert('ลบผู้เข้าร่วมประชุมแล้ว','success');
        });
    }
    
    $scope.addExternalAdtendee = function (AttendeeExt){
        //console.log(AttendeeExt);
        if(AttendeeExt == undefined || AttendeeExt.Name == ''){
            //alert('กรุณากรอกชื่อผู้เข้าร่วมประชุม');
            $scope.alertMessage = 'กรุณากรอกชื่อผู้เข้าร่วมประชุม';
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
            
            return;
        }else{

            var res = $filter('FindExUserName')($scope.ExternalAttendeeList, AttendeeExt.Name);

            if(res != -1){
                alert('รายชื่อบุคคลภายนอก (ชื่อ : '+ AttendeeExt.Name +') ได้ถูกเลือกไว้แล้ว');
                return false;
            }

            IndexOverlayFactory.overlayShow();
            ReserveRoomFactory.updateExternalAttendee(
                {
                    'ReserveRoomID':$routeParams.roomReserveID
                    ,'AttendeeName':AttendeeExt.Name
                    ,'Email':AttendeeExt.Email
                    ,'Mobile':AttendeeExt.Mobile
                    ,'CreateBy':$scope.$parent.currentUser.UserID
                }).then(function(result){
                    IndexOverlayFactory.overlayHide();
                    if(result.data.STATUS == 'OK'){
                        $scope.ExternalAttendeeList.push({
                            'AttendeeID':result.data.DATA
                            ,'ReserveRoomID':$routeParams.roomReserveID
                            ,'AttendeeName':AttendeeExt.Name
                            ,'Email':AttendeeExt.Email
                            ,'Mobile':AttendeeExt.Mobile
                        });
                        $scope.AttendeeExt = {'Name':'','':'Email','Mobile':''};
                    }else {
                        alert(result.data.DATA);
                    }
                    
            });
        }
    }
    
    $scope.deleteExternalAttendee = function (index , AttendeeID){
        IndexOverlayFactory.overlayShow();
        ReserveRoomFactory.deleteExternalAttendee(AttendeeID).then(function(result){
            IndexOverlayFactory.overlayHide();
            $scope.ExternalAttendeeList.splice(index, 1);
            $scope.addAlert('ลบผู้เข้าร่วมประชุมภายนอกแล้ว','success');
        });
    }
    
    // Device zone
    $scope.checkDisabledBtn = function(val, maxVal){
        // console.log(val, maxVal);
        if(parseInt(val) > parseInt(maxVal)){
            return true;
        }else{
            return false;
        }
    }

    $scope.chooseDevice = function (){
        $scope.tableLoad = false;
        $scope.continueLoad = true;
        $scope.deviceOffset = 0;
        ReserveRoomFactory.getDeviceList($scope.BookingDetail.RegionID, $scope.deviceOffset).then(function(result){
            if(result.data.STATUS == 'OK'){
                $scope.AllDeviceList = result.data.DATA.DeviceList;
                $scope.deviceOffset = result.data.DATA.offset;
                
                $scope.selectDevice = function (objSelected){
                    
                    if(objSelected.Amount === undefined || objSelected.Amount === null || objSelected.Amount == ''  || parseFloat(objSelected.Amount) == 0){
                        alert('กรุณากรอกจำนวนอุปกรณ์');
                    }else{
                        var offset = $filter('FindDevice')($scope.DeviceList, objSelected.DeviceID);
                        //console.log('offset', offset, 'index' , index);
                        if(offset == -1){
                            ReserveRoomFactory.updateRoomDevice(objSelected, $routeParams.roomReserveID).then(function(obj){
                                if(obj.data.STATUS == 'OK'){
                                    objSelected.ReserveRoomID = $routeParams.roomReserveID;
                                    $scope.DeviceList.push(angular.copy(objSelected));
                                }    
                            })
                        }else{
                            alert('อุปกรณ์นี้ได้ถูกเลือกแล้ว');
                        }
                    }    
                }
                
                var modalInstance = $uibModal.open({
        			animation : true,
        			templateUrl : 'choose_device.html',
        			size : 'lg',
        			scope : $scope,
                    backdrop : 'static',
        			controller : 'ModalDialogReturnFromOKBtnCtrl',
        			resolve : {
        				params : function() {
        					return {};
        				} 
        			},
        		});
        		
        		modalInstance.result.then(function (valResult) {
        			//console.log(valResult);
        	    }, function () {});
             }else {
                alert('Could not load device list');
             }
        });
    }
    
    $scope.deleteRoomDevice = function (index, deviceID, reserveRoomID) {
        IndexOverlayFactory.overlayShow();
        ReserveRoomFactory.deleteRoomDevice(deviceID, reserveRoomID).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.DeviceList.splice(index , 1);
            }
            
        });
    }
    
    $scope.showMoreDevice = function(){
        if($scope.continueLoad){
            $scope.tableLoad = true;
            ReserveRoomFactory.getDeviceList($scope.BookingDetail.RegionID, $scope.deviceOffset).then(function(result){
                if(result.data.STATUS == 'OK'){
                    $scope.tableLoad = false;
                    $scope.deviceOffset =  result.data.DATA.offset;
                    for(var i = 0; i < result.data.DATA.DeviceList.length; i++){
                        $scope.AllDeviceList.push(result.data.DATA.DeviceList[i]);    
                    }
                    $scope.continueLoad = result.data.DATA.continueLoad;
                }
            });
        }
    }
    
    // End Device zone
    
    // Food zone
    $scope.chooseFood = function (){
        $scope.tableLoad = false;
        $scope.continueLoad = true;
        $scope.offset = 0;
        ReserveRoomFactory.getFoodList($scope.BookingDetail.RegionID, $scope.offset).then(function(result){
            if(result.data.STATUS == 'OK'){
                $scope.AllFoodList = result.data.DATA.FoodList;
                $scope.offset = result.data.DATA.offset;
                
                $scope.selectFood = function (objSelected){
                    
                    if(objSelected.Amount === undefined || objSelected.Amount == ''){
                        alert('กรุณากรอกจำนวนอาหาร');
                    }else{
                        var offset = $filter('FindFood')($scope.FoodList, objSelected.FoodID);
                        //console.log('offset', offset, 'index' , index);
                        if(offset == -1){
                            ReserveRoomFactory.updateRoomFood(objSelected, $routeParams.roomReserveID).then(function(obj){
                                if(obj.data.STATUS == 'OK'){
                                    objSelected.ReserveRoomID = $routeParams.roomReserveID;
                                    $scope.FoodList.push(angular.copy(objSelected));
                                }    
                            })
                        }else{
                            alert('อาหารชนิดนี้ถูกเลือกแล้ว');
                        }
                    }    
                }
                
                var modalInstance = $uibModal.open({
        			animation : true,
        			templateUrl : 'choose_food.html',
        			size : 'lg',
        			scope : $scope,
                    backdrop : 'static',
        			controller : 'ModalDialogReturnFromOKBtnCtrl',
        			resolve : {
        				params : function() {
        					return {};
        				} 
        			},
        		});
        		
        		modalInstance.result.then(function (valResult) {
        			//console.log(valResult);
        	    }, function () {});
             }else {
                alert('Could not load food list');
             }
        });
    }
    
    $scope.deleteRoomFood = function (index, foodID, reserveRoomID) {
        ReserveRoomFactory.deleteRoomFood(foodID, reserveRoomID).then(function(result){
            if(result.data.STATUS == 'OK'){
                $scope.FoodList.splice(index , 1);
            }
            
        });
    }
    
    $scope.showMoreFood = function(){
        if($scope.continueLoad){
            $scope.tableLoad = true;
            ReserveRoomFactory.getFoodList($scope.BookingDetail.RegionID, $scope.offset).then(function(result){
                if(result.data.STATUS == 'OK'){
                    $scope.tableLoad = false;
                    $scope.offset =  result.data.DATA.offset;
                    for(var i = 0; i < result.data.DATA.FoodList.length; i++){
                        $scope.AllFoodList.push(result.data.DATA.FoodList[i]);    
                    }
                    $scope.continueLoad = result.data.DATA.continueLoad;
                }
            });
        }
    }
    // End Food zone
    
    // Room Destination zone
    $scope.roomDestEvent = function (index, room){
        //console.log(room.selected_room);
        var status = room.selected_room?'เลือก':'ยกเลิก';
        if(confirm('คุณต้องการ' + status + 'ห้องประชุมปลายทางนี้ ใช่หรือไม่ ?')){
            IndexOverlayFactory.overlayShow();
            ReserveRoomFactory.updateRoomDestinationStatus(room, $routeParams.roomReserveID, room.selected_room).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS == 'OK'){
                    $scope.RoomDestinationList[index].DestinationRoomID = result.data.DATA.DestinationRoomID;
                    $scope.RoomDestinationList[index].ReserveStatus = result.data.DATA.ReserveStatus;
                }
                else{
                    alert(result.data.DATA.MSG);
                    $scope.RoomDestinationList[index].ReserveStatus = result.data.DATA.ReserveStatus;
                    $scope.RoomDestinationList[index].selected_room = false;
                }
                //console.log($scope.RoomDestinationList[index]);
            });    
        }else{
            if(room.selected_room){
                $scope.RoomDestinationList[index].selected_room = false;
            }else{
                $scope.RoomDestinationList[index].selected_room = true;
            }
            return false;
        }
        
    }

    $scope.cancelRoom = function (reserveRoomID){
        $scope.alertMessage = 'ต้องการยกเลิกการจองห้องประชุมนี้ ใช่หรือไม่ ?';
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
            ReserveRoomFactory.cancelRoom(reserveRoomID).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS == 'OK'){
                    $scope.alertMessage = 'ยกเลิกการจองเรียบร้อยแล้ว';
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
                        window.location.replace('#/');
                    }, function () {});
                    
                }
            });
        }, function () {});
            
    }

    $scope.requestReserveRoom = function (reserveRoomID){
        $scope.alertMessage = 'ต้องการส่งคำขอการจองห้องประชุมนี้ ใช่หรือไม่ ?';
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

            if($scope.ReserveRoomInfo.Breakfast && $scope.ReserveRoomInfo.Lunch){
                $scope.ReserveRoomInfo.SnackStatus = 'Both';
            }else if($scope.ReserveRoomInfo.Breakfast){
                $scope.ReserveRoomInfo.SnackStatus = 'Breakfast';
            }else if($scope.ReserveRoomInfo.Lunch){
                $scope.ReserveRoomInfo.SnackStatus = 'Lunch';
            }else{
                $scope.ReserveRoomInfo.SnackStatus = '';
            }
            
            $scope.ReserveRoomInfo.StartDateTime = concatDateTimeSQL($scope.ReserveRoomInfo.StartDate, $scope.ReserveRoomInfo.StartTime);
            $scope.ReserveRoomInfo.EndDateTime = concatDateTimeSQL($scope.ReserveRoomInfo.EndDate, $scope.ReserveRoomInfo.EndTime);
            
            window.clearTimeout(timeoutReserve);
            ReserveRoomFactory.requestReserveRoom($scope.ReserveRoomInfo
                                                ,$scope.BookingDetail
                                                ,$scope.InternalAttendeeList
                                                ,$scope.ExternalAttendeeList
                                                ,$scope.DeviceList
                                                ,$scope.FoodList
                                                ,$scope.RoomDestinationList
                                                ,$routeParams.roomReserveID).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS == 'OK'){
                    $scope.ReserveRoomInfo.ReserveStatus = result.data.DATA;
                    $scope.alertMessage = 'ส่งคำขอเรียบร้อยแล้ว';
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
                        window.location.replace('#/');
                    }, function () {});
                    
                }else{
                    alert(result.data.DATA.MSG);
                }
            });
        }, function () {});
            
    }

    // Alert zone
    $scope.alerts = [];
    $scope.addAlert = function(msg, type) {
      $scope.alerts.push({
        msg: msg,
        type: type
      });
    };
    // End Alert zone
    
});



app.controller('NewsDetailController', function($cookies, $scope, $uibModal, $routeParams, $timeout, $interval, IndexOverlayFactory, NewsFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'news';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }

    $scope.getNewsByID = function(newsID){
        NewsFactory.viewNews(newsID).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.News = result.data.DATA;
                $scope.images.push({'id':1
                                    ,'thumbUrl':$scope.News.NewsPicture
                                    ,'url':$scope.News.NewsPicture
                                    });
                var imgLoop = $scope.News.pictures.length;
                for(var i = 0; i < imgLoop; i++){
                    console.log($scope.News.pictures[i].PicturePath);
                    $scope.images.push({'id': (i + 2)
                                    ,'thumbUrl':$scope.News.pictures[i].PicturePath
                                    ,'url':$scope.News.pictures[i].PicturePath
                                    });
                }
                
            }
        });
    }

    $scope.getNewsByID($routeParams.newsID);

    $scope.images = [];

    $scope.conf = {
        imgAnim : 'slide'
    };
    /*****************************************************/
    
    // Thumbnails
    $scope.thumbnails = true;
    $scope.toggleThumbnails = function(){
        $scope.thumbnails = !$scope.thumbnails;
    }
    // Inline
    $scope.inline = true;
    $scope.toggleInline = function(){
        $scope.inline = !$scope.inline;
    }
    // Bubbles
    $scope.bubbles = true;
    $scope.toggleBubbles = function(){
        $scope.bubbles = !$scope.bubbles;
    }
    // Image bubbles
    $scope.imgBubbles = true;
    $scope.toggleImgBubbles = function(){
        $scope.imgBubbles = !$scope.imgBubbles;
    }
    // Background close
    $scope.bgClose = false;
    $scope.closeOnBackground = function(){
        $scope.bgClose = !$scope.bgClose;
    }
    // Gallery methods gateway
    $scope.methods = {};
    $scope.openGallery = function(){
        $scope.methods.open();
    };
    // Gallery callbacks
    $scope.opened = function(){
        console.info('Gallery opened!');
    }
    $scope.closed = function(){
        console.warn('Gallery closed!');
    }
    $scope.delete = function(img, cb){
        cb();
    }
});

app.controller('NewsListController', function($cookies, $scope, $uibModal, $routeParams, IndexOverlayFactory, NewsFactory, RegionFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'news';

    var $user_session = sessionStorage.getItem('user_session');
    // alert($user_session);
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
        $user_session = decodeURIComponent(window.atob($routeParams.user_session));
        // alert($user_session);
        sessionStorage.setItem('user_session', $user_session);
        $scope.$parent.currentUser = angular.fromJson($user_session);
        // alert($user_session);
        //
       // window.location.replace('#/logon/' + $scope.menu_selected);
    }

    $scope.loadRegionList = function(){
        RegionFactory.getAllRegion().then(function(result){
            if(result.data.STATUS == 'OK'){
                $scope.RegionList = result.data.DATA;
            }
        });
    }

    $scope.loadList = function () {
        if($scope.continueLoad){
            $scope.tableLoad = true;
            NewsFactory.getNewsList($scope.dataOffset
                                ,$scope.condition.RegionID 
                                ,$scope.condition.HideNews
                                ,$scope.condition.CurrentNews
                                ,$scope.condition.WaitApprove).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS == 'OK'){
                    $scope.tableLoad = false;
                    $scope.dataOffset =  result.data.DATA.offset;
                    $scope.continueLoad = result.data.DATA.continueLoad;
                    for(var i = 0; i < result.data.DATA.DataList.length; i++){

                        result.data.DATA.DataList[i].NewsDateTimeFormat = convertDateToFullThaiDateIgnoreTime( convertDateToSQLString(result.data.DATA.DataList[i].NewsDateTimeFormat) );
                        //console.log(result.data.DATA.DataList[i].NewsDateTimeFormat);
                        $scope.DataList.push(result.data.DATA.DataList[i]);   

                    }
                }
            });
        }
    }

    $scope.setCondition = function (){
        if($scope.condition.RegionID ==null){
            $scope.condition.RegionID = '0';
        }
        $scope.DataList = [];
        $scope.dataOffset = 0;
        $scope.tableLoad = false;
        $scope.continueLoad = true;
        $scope.loadList();
    }

    $scope.showListPage = function (){
        $scope.showPage = 'MAIN';
    }

    $scope.RegionList = [];
    $scope.showPage = '';
    $scope.dataOffset = 0;
    $scope.tableLoad = false;
    $scope.continueLoad = true;
    $scope.DataList = [];
    $scope.condition ={'RegionID':'0','HideNews':'0','CurrentNews':'0','WaitApprove':'0'};

    
    $scope.showListPage();
    $scope.loadList();
    $scope.loadRegionList();
    

});

app.controller('CalendarController', function($cookies, $scope, $uibModal, $routeParams, $sce, IndexOverlayFactory, CalendarFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'calendar';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }

    $scope.DataList = [/*{"LinkID":"1","LinkTopic":"การสร้างอาชีพ","LinkUrl":"curreer.co.th/works","LinkIcon":"img/link/ring.gif","LinkStatus":'Y'},
                    {"LinkID":"2","LinkTopic":"การสร้างอาชีพ 1","LinkUrl":"curreer1.co.th/works","LinkIcon":"img/link/spin.gif","LinkStatus":'N'}*/];

    $scope.loadList = function (mode){
        CalendarFactory.getList(mode).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.DataList = result.data.DATA.DataList;
                for(var i = 0 ; i < $scope.DataList.length; i++){
                    $scope.DataList[i].CalendarUrl = $sce.trustAsResourceUrl($scope.DataList[i].CalendarUrl);
                }
            }
        });
    }

    $scope.loadList('view');
});

app.controller('CalendarManageController', function($cookies, $scope, $uibModal, $routeParams, IndexOverlayFactory, CalendarFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'manage_calendar';

    var $user_session = sessionStorage.getItem('user_session');
    $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }

    $scope.DataList = [/*{"LinkID":"1","LinkTopic":"การสร้างอาชีพ","LinkUrl":"curreer.co.th/works","LinkIcon":"img/link/ring.gif","LinkStatus":'Y'},
                    {"LinkID":"2","LinkTopic":"การสร้างอาชีพ 1","LinkUrl":"curreer1.co.th/works","LinkIcon":"img/link/spin.gif","LinkStatus":'N'}*/];

    $scope.loadList = function (mode){
        CalendarFactory.getManageList($scope.currentUser.UserID).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.DataList = result.data.DATA.DataList;
            }
        });
    }

    $scope.newCalendar = function (){
        $scope.Calendar = {"CalendarID":"","CalendarName":"","CalendarUrl":"","IsFirstPage":"N","ActiveStatus":'Y', "CreateBy":$scope.currentUser.UserID};
        $scope.DataList.push($scope.Calendar);
    }
    $scope.updateCalendar = function (index, Calendar){
        $scope.alertMessage = 'ต้องการบันทึกปฏิทินนี้ ใช่หรือไม่ ?';
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
            CalendarFactory.updateData(Calendar).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS == 'OK' && result.data.DATA){
                    $scope.loadList('manage');
                }
            });
        });
    }
    $scope.removeCalendar = function (index, ID){
        if(ID == ''){
            $scope.DataList.splice(index, 1);
        }else{
            $scope.alertMessage = 'ต้องการลบปฏิทินนี้ ใช่หรือไม่ ?';
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
                CalendarFactory.deleteData(ID).then(function(result){
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

app.controller('LogManageController', function($cookies, $scope, $filter, $uibModal, $routeParams, IndexOverlayFactory, LogFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'manage_log';

    var $user_session = sessionStorage.getItem('user_session');
    $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }

    // Validate zone
    $scope.filterMenu = function(MenuID){
        //console.log('MenuID = ', MenuID);
        $scope.MenuList = angular.fromJson(sessionStorage.getItem('MenuList'));
        var result = false;
        result = $filter('MenuFilter')($scope.MenuList, MenuID);
        
        return result;
    }
    if(!$scope.filterMenu(0)){
        alert('ไม่มีสิทธิ์เข้าใช้งานในหน้านี้');
        window.location.replace('#/');
    }

    $scope.loadList = function (){
        IndexOverlayFactory.overlayShow();
        LogFactory.getLogManageList().then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.DataList = result.data.DATA.DataList;
            }
        });
    }

    $scope.downloadLog = function(filename){
        IndexOverlayFactory.overlayShow();
        LogFactory.downloadLog(filename).then(function(result){
            IndexOverlayFactory.overlayHide();
            var logContent = atob(result.data.DATA);
            console.log(logContent);
            generateFile(filename, logContent);
        });

        function generateFile(filename, text) {
          var element = document.createElement('a');
          element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
          element.setAttribute('download', filename);

          element.style.display = 'none';
          document.body.appendChild(element);

          element.click();

          document.body.removeChild(element);
        }
    }

    $scope.loadList();

});

app.controller('SettingManageController', function($cookies, $scope, $filter, $uibModal, $routeParams, IndexOverlayFactory, SettingFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'manage_setting';

    var $user_session = sessionStorage.getItem('user_session');
    $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }

    // Validate zone
    $scope.filterMenu = function(MenuID){
        //console.log('MenuID = ', MenuID);
        $scope.MenuList = angular.fromJson(sessionStorage.getItem('MenuList'));
        var result = false;
        result = $filter('MenuFilter')($scope.MenuList, MenuID);
        
        return result;
    }
    if(!$scope.filterMenu(0)){
        alert('ไม่มีสิทธิ์เข้าใช้งานในหน้านี้');
        window.location.replace('#/');
    }

    $scope.loadList = function (){
        IndexOverlayFactory.overlayShow();
        SettingFactory.getSettingManageList().then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.Data = result.data.DATA;
            }
        });
    }

    $scope.saveSettings = function(data){
        IndexOverlayFactory.overlayShow();
        SettingFactory.saveSettingManageList(data).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                //$scope.Data = result.data.DATA;
                alert('Update success');
            }
        });
    }

    $scope.loadList();

});

app.controller('PermissionController', function($cookies, $scope, $uibModal, $routeParams, IndexOverlayFactory, PermissionFactory, RegionFactory, DepartmentFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'manage_permission';

    var $user_session = sessionStorage.getItem('user_session');
    $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }

    $scope.DataList = [];

    $scope.loadList = function (condition){
        IndexOverlayFactory.overlayShow();
        PermissionFactory.getList(condition).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.DataList = result.data.DATA.DataList;
                //console.log($scope.DataList);
            }
        });
    }

    $scope.loadRegionList = function(){
        IndexOverlayFactory.overlayShow();
        RegionFactory.getAllRegion().then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.RegionList = result.data.DATA;
            }
        });
    }

    $scope.loadDepartmentList = function(){
        DepartmentFactory.getAllDepartmentList().then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.DepartmentList = result.data.DATA;
            }
        });
    }

    $scope.setRegionName = function(region){
        $scope.RegionName = region.RegionName;
    }

    $scope.setDepartmentName = function(dep){
        $scope.DepartmentName = dep.OrgName;
    }

    $scope.setPermission = function(index, data, permissionType){
        console.log(permissionType);
        if(permissionType == 'SuperAdmin'){
            $scope.setAllPermission(index, data, data.SuperAdmin, permissionType);
        }else{
            $scope.updatePermission(data, permissionType);
        }
    }

    $scope.setAllPermission = function(index, data, adminGroupID, permissionType){
        console.log(adminGroupID);
        if(adminGroupID != -1){
            $scope.DataList[index].PermissionAdmin = 1;
            $scope.DataList[index].RoomAdmin = 2;
            $scope.DataList[index].CarAdmin = 3;
            $scope.DataList[index].DeviceAdmin = 4;
            $scope.DataList[index].NewsAdmin = 5;
            $scope.DataList[index].NewsApproveAdmin = 6;
            $scope.DataList[index].RepairAdmin = 7;
            $scope.DataList[index].LinkAdmin = 8;
            $scope.DataList[index].ExPhoneBookAdmin = 9;
            $scope.DataList[index].CalendarAdmin = 10;
        }else{
            $scope.DataList[index].PermissionAdmin = -1;
            $scope.DataList[index].RoomAdmin = -1;
            $scope.DataList[index].CarAdmin = -1;
            $scope.DataList[index].DeviceAdmin = -1;
            $scope.DataList[index].NewsAdmin = -1;
            $scope.DataList[index].NewsApproveAdmin = -1;
            $scope.DataList[index].RepairAdmin = -1;
            $scope.DataList[index].LinkAdmin = -1;
            $scope.DataList[index].ExPhoneBookAdmin = -1;
            $scope.DataList[index].CalendarAdmin = -1;
        }

        $scope.updatePermission(data, permissionType);

    }

    $scope.updatePermission = function(data, permissionType){
        IndexOverlayFactory.overlayShow();
        PermissionFactory.updatePermission(data, permissionType).then(function(result){
            IndexOverlayFactory.overlayHide();
            // if(result.data.STATUS == 'OK'){
            //     $scope.DataList = result.data.DATA.DataList;
            //     console.log($scope.DataList);
            // }
        });
    }

    $scope.RegionName = '';
    $scope.DepartmentName = '';
    $scope.loadRegionList();
    $scope.loadDepartmentList();
    $scope.condition = {Region:2/*parseInt($scope.currentUser.RegionID)*/ , Group:'' , Username:'', UserID:$scope.currentUser.UserID};
    console.log($scope.currentUser);
    $scope.loadList($scope.condition);

});

app.controller('LinkManageController', function($cookies, $scope, $uibModal, $routeParams, $timeout, $interval, Upload, IndexOverlayFactory, LinkFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'manage_link';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }

    $scope.DataList = [/*{"LinkID":"1","LinkTopic":"การสร้างอาชีพ","LinkUrl":"curreer.co.th/works","LinkIcon":"img/link/ring.gif","LinkStatus":'Y'},
                    {"LinkID":"2","LinkTopic":"การสร้างอาชีพ 1","LinkUrl":"curreer1.co.th/works","LinkIcon":"img/link/spin.gif","LinkStatus":'N'}*/];

    $scope.loadList = function (mode){
        LinkFactory.getList(mode, $scope.currentUser.UserID).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.DataList = result.data.DATA.DataList;
            }
        });
    }

    $scope.newLink = function (){
        $scope.Link = {"LinkID":"","LinkTopic":"","LinkUrl":"","LinkIcon":"","LinkStatus":'Y', "CreateBy":$scope.currentUser.UserID,"UploadFile":null};
        //$scope.DataList.push($scope.Link);
        $scope.DataList.unshift($scope.Link);
    }
    $scope.updateLink = function (index, link, fileimg){

        IndexOverlayFactory.overlayShow();
        Upload.upload({
            url: servicesUrl + '/dpo/public/updateLink/',
            data: {'fileimg' : link.UploadFile
                , 'updateObj': link
            }
        }).then(function (resp) {
            //IndexOverlayFactory.overlayHide();
            $scope.loadList('manage');
        }, function (resp) {
            console.log('Error status: ' + resp.status);
        }, function (evt) {
        });
        
    }
    $scope.removeLink = function (index, ID){
        if(ID == ''){
            $scope.DataList.splice(index, 1);
        }else{
            $scope.alertMessage = 'ต้องการลบการลิ้งค์นี้ ใช่หรือไม่ ?';
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
                LinkFactory.deleteData(ID).then(function(result){
                    IndexOverlayFactory.overlayHide();
                    if(result.data.STATUS == 'OK' && result.data.DATA){
                        $scope.DataList.splice(index, 1);
                    }
                });
            });
        }
    }

    $scope.goLinkPermission = function(linkID){
        window.location.href="#/link_permission/"+linkID;
    }

    $scope.loadList('manage');

});

app.controller('LinkController', function($cookies, $scope, $uibModal, $routeParams, $timeout, $interval, Upload, IndexOverlayFactory, LinkFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'link';

    var $user_session = sessionStorage.getItem('user_session');
    console.log($user_session);
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }

    $scope.loadList = function (mode){
        LinkFactory.getList(mode, $scope.currentUser.UserID).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.DataList = result.data.DATA.DataList;
                $scope.DataList.push({
                    "LinkUrl": "http://172.23.10.224/MIS/web/#/thirdparty/authen/" + $scope.$parent.currentUser.Username + "/" + $scope.$parent.currentUser.LoginSession,
                    "LinkIcon": "img/link/logo_mis.png",
                    "LinkStatus": "Y",
                    "CreateBy": 1442,
                    "CreateDateTime": "2018-05-31 15:23:31",
                    "LinkTopic": "ระบบ MIS (ใหม่)"
                });
                
                if(result.data.Permission != null){
                    console.log("asdasd");
                    $scope.Permission = true;
                }
            }
        });
    }

    $scope.Permission = false;

    $scope.loadList('view');

});

app.controller('LinkPermissionController', function($cookies, $scope, $uibModal, $routeParams, IndexOverlayFactory, LinkFactory, RegionFactory, DepartmentFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'link';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }

    $scope.getLinkPermission = function (linkID, condition){
        //IndexOverlayFactory.overlayShow();
        if($scope.continueLoad){
            
            $scope.tableLoad = true;
            LinkFactory.getLinkPermission($scope.dataOffset, linkID, condition, $scope.currentUser.UserID).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS == 'OK'){
                    if(result.data.Permission != null){
                        $scope.Link = result.data.Link;
                        $scope.tableLoad = false;
                        $scope.dataOffset =  result.data.DATA.offset;
                        $scope.continueLoad = result.data.DATA.continueLoad;
                        for(var i = 0; i < result.data.DATA.DataList.length; i++){
                            $scope.LinkPermission.push(result.data.DATA.DataList[i]);    
                        }
                    }else{
                        alert('Permission Denied!');
                        window.location.replace("#/link");
                    }
                    $scope.disabled_search = false;
                }
            });
        }
    }

    $scope.loadRegionList = function(){
        IndexOverlayFactory.overlayShow();
        RegionFactory.getAllRegion().then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.RegionList = result.data.DATA;
            }
        });
    }

    $scope.loadDepartmentList = function(){
        DepartmentFactory.getAllDepartmentList().then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.DepartmentList = result.data.DATA;
            }
        });
    }

    $scope.search = function(condition){
        $scope.disabled_search = true;
        $scope.dataOffset = 0;
        $scope.tableLoad = false;
        $scope.continueLoad = true;
        $scope.LinkPermission = [];
        $scope.getLinkPermission($routeParams.linkID, condition);
    }

    $scope.updatePermission = function(index, data){
        IndexOverlayFactory.overlayShow();
        LinkFactory.updatePermission(data, $scope.currentUser.UserID).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.LinkPermission[index].LinkPermissionID = result.data.DATA;    
            }
            
            // if(result.data.STATUS == 'OK'){
            //     $scope.DataList = result.data.DATA.DataList;
            //     console.log($scope.DataList);
            // }
        });
    }

    $scope.setAllLinkPermission = function(condition, linkID, updateType){
        IndexOverlayFactory.overlayShow();
        LinkFactory.setAllLinkPermission(condition, linkID, updateType, $scope.currentUser.UserID).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.search(condition);
            }
            
            // if(result.data.STATUS == 'OK'){
            //     $scope.DataList = result.data.DATA.DataList;
            //     console.log($scope.DataList);
            // }
        });
    }

    $scope.loadDepartmentList();
    $scope.disabled_search = false;
    $scope.Permission = false;
    $scope.LinkID = $routeParams.linkID;
    $scope.condition = {Region:parseInt($scope.currentUser.RegionID) , Group:parseInt($scope.currentUser.OrgID) , Username:''};
    $scope.dataOffset = 0;
    $scope.tableLoad = false;
    $scope.continueLoad = true;
    $scope.LinkPermission = [];

    $scope.loadRegionList();
    
    $scope.getLinkPermission($routeParams.linkID, $scope.condition);

});

app.controller('DepartmentManageController', function($cookies, $scope, $uibModal, $routeParams, IndexOverlayFactory, RegionFactory, DepartmentFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'manage_department';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }

    $scope.loadRegionList = function(){
        IndexOverlayFactory.overlayShow();
        RegionFactory.getAllRegion().then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.RegionList = result.data.DATA;
            }
        });
    }

    $scope.loadDepartmentList = function(){
        DepartmentFactory.getAllDepartmentList().then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.DepartmentList = result.data.DATA;
            }
        });
    }

    $scope.loadManageDepartmentList = function(condition){
        IndexOverlayFactory.overlayShow();
        DepartmentFactory.loadManageDepartmentList(condition).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.ManageDepartmentList = result.data.DATA;
            }
        });
    }

    $scope.search = function(condition){
        $scope.ManageDepartmentList = [];
        $scope.loadManageDepartmentList(condition);
    }

    $scope.updateRegionOfDepartment = function(groupID, orgID, RegionID){
        IndexOverlayFactory.overlayShow();
        DepartmentFactory.updateRegionOfDepartment(groupID, orgID, RegionID).then(function(result){
            IndexOverlayFactory.overlayHide();
        });
    }
    $scope.condition = {Region:'' , Group:''};
    $scope.LinkPermission = [];

    $scope.loadRegionList();
    $scope.loadDepartmentList();
    $scope.loadManageDepartmentList($scope.condition);

});

app.controller('NewsController', function($cookies, $scope, $uibModal, $routeParams, IndexOverlayFactory, NewsFactory, RegionFactory, Upload) {
    
    IndexOverlayFactory.overlayShow();

    if($routeParams.viewType !== undefined){
        $scope.menu_selected = 'news';
    }else{
        $scope.menu_selected = 'manage_news';    
    }
    

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }
    IndexOverlayFactory.overlayHide();

    

    $scope.loadRegionList = function(){
        RegionFactory.getAllRegion().then(function(result){
            if(result.data.STATUS == 'OK'){
                $scope.RegionList = result.data.DATA;
            }
        });
    }

    $scope.loadNewsTypeList = function(){
        NewsFactory.getNewsTypeList().then(function(result){
            if(result.data.STATUS == 'OK'){
                $scope.NewsTypeList = result.data.DATA;
            }
        });
    }

    $scope.showUpdatePage = function (index, obj){
        if(index != -1 && obj != null){
            $scope.setUpdateModelValue(obj);
        }else{
            $scope.setDefaultModelValue();
        }
        $scope.showPage = 'UPDATE';
        $scope.setImgList();
        $scope.setAttachFileList();
        
        
        // editor title
        if (CKEDITOR.instances.editor_title1) CKEDITOR.instances.editor_title1.destroy();
        CKEDITOR.config.toolbar = [
            { name: 'tools', items: [ 'Maximize' ] },
            { name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source' ] },
            { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Strike', '-', 'RemoveFormat' ] },
            { name: 'styles', items: [ 'Styles', 'Format' ] },
            { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
            { name: 'colors', items: [ 'TextColor','BGColor' ] }
        ];
        CKEDITOR.config.extraPlugins = 'colorbutton';
        // CKEDITOR.config.colorButton_colors = 'CF5D4E,454545,FFF,CCC,DDD,CCEAEE,66AB16';
        CKEDITOR.config.colorButton_enableAutomatic = false;
        // CKEDITOR.config.allowedContent = true;
        // CKEDITOR.config.requiredContent = true;
        CKEDITOR.replace( 'editor_title1' );

        // editor content
        if (CKEDITOR.instances.editor1) CKEDITOR.instances.editor1.destroy();
        CKEDITOR.config.toolbar = [
            { name: 'tools', items: [ 'Maximize' ] },
            { name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source' ] },
            { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Strike', '-', 'RemoveFormat' ] },
            { name: 'styles', items: [ 'Styles', 'Format' ] },
            { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
            { name: 'colors', items: [ 'TextColor','BGColor' ] }
        ];
        // CKEDITOR.config.colorButton_colors = 'CF5D4E,454545,FFF,CCC,DDD,CCEAEE,66AB16';
        CKEDITOR.config.colorButton_enableAutomatic = false;

        // CKEDITOR.config.allowedContent = true;
        // CKEDITOR.config.requiredContent = true;
        CKEDITOR.replace( 'editor1' );

        /*
        $scope.checkNewsTitleLength = function(title){
            var regex = /(<([^>]+)>)/ig
                ,   body = title

                ,   result = body.replace(regex, "");
            result = result.replace('&nbsp;', ' ');
            console.log(result.length);
            if(result.length > 120){
                
                alert('หัวข้อข่าวต้องมีความยาวไม่เกิน 120 ตัวอักษร');
                return false;
            }else{
                return true;
            }
        }
    
        CKEDITOR.instances.editor_title1.on('blur', function(e) {
            console.log('onkeyup fired');
            var content = CKEDITOR.instances.editor_title1.getData();
            var regex = /(<([^>]+)>)/ig
                ,   body = content
                ,   result = body.replace(regex, "");
            result = result.replace('&nbsp;', ' ');
            console.log(result.length);
            if(result.length > 120){
                
                alert('หัวข้อข่าวต้องมีความยาวไม่เกิน 120 ตัวอักษร');
                return false;
            }else{
                return true;
            }
        });
        */
        // Update Zone
        $scope.upload = function (fileimg) {
            /*
            var validFilename = /^[a-z0-9_.@()-]+\.[^.]+$/i;
            if($scope.fileimg != null && !validFilename.test($scope.fileimg.name)){ // check main picture
               alert('ระบบไม่รองรับชื่อไฟล์รูปภาพหลักที่เป็นภาษาไทยและอักขระพิเศษ');
               return false;
            }
            

            for(var i =0; i < fileimg.length; i++){ // check sub pictures
                
                if(fileimg[i].fileimg != null && !validFilename.test(fileimg[i].fileimg.name)){ // check main picture
                   alert('ระบบไม่รองรับชื่อไฟล์รูปภาพประกอบที่ '+(i + 1)+' ที่เป็นภาษาไทยและอักขระพิเศษ');
                   return false;
                }
            }

            for(var i =0; i < $scope.AttachFileList.length; i++){ // check file list
                if($scope.AttachFileList[i].attachFile != null && !validFilename.test($scope.AttachFileList[i].attachFile.name)){ // check main picture
                   alert('ระบบไม่รองรับชื่อไฟล์แนบที่ '+(i + 1)+' ที่เป็นภาษาไทยและอักขระพิเศษ');
                   return false;
                }
            }
            */
            if(CKEDITOR.instances.editor_title1.getData() == '' || CKEDITOR.instances.editor1.getData() == ''){
               alert('กรุณากรอกหัวข้อข่าวและเนื้อหาข่าว');
                return false; 
            }
            var regex = /(<([^>]+)>)/ig
                ,   body = CKEDITOR.instances.editor_title1.getData()
                ,   result = body.replace(regex, "");
            result = result.replace('&nbsp;', ' ');

            if(result.length > 120){
                console.log(result.length);
                alert('หัวข้อข่าวต้องมีความยาวไม่เกิน 120 ตัวอักษร');
                return false;
            }else{            

                $scope.alertMessage = 'ต้องการบันทึกและส่งคำขอสร้างข่าว ใช่หรือไม่ ?';
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
                    $scope.News.NewsTitle = CKEDITOR.instances.editor_title1.getData();
                    $scope.News.NewsContent = CKEDITOR.instances.editor1.getData();

                    if($scope.News.LimitDisplay == 'N'){
                         $scope.News.NewsStartDateTime = '';
                         $scope.News.NewsEndDateTime = '';
                    }else{
                        if($scope.News.StartDate != null && $scope.News.StartTime != null){
                            $scope.News.NewsStartDateTime = concatDateTime($scope.News.StartDate, $scope.News.StartTime);
                        }
                        if($scope.News.EndDate != null && $scope.News.EndTime != null){
                            $scope.News.NewsEndDateTime = concatDateTime($scope.News.EndDate, $scope.News.EndTime);
                        }
                    }
                    if($scope.News.NewsDateTime != undefined && $scope.News.NewsDateTime != null && $scope.News.NewsDateTime != ''){
                        $scope.News.NewsDateTime = makeSQLDate($scope.News.NewsDateTime);
                    }

                    IndexOverlayFactory.overlayShow();
                    Upload.upload({
                        
                        url: servicesUrl + '/dpo/public/updateNewsData/',
                        data: {'img' : $scope.fileimg
                            , 'fileimg' : fileimg
                            , 'attachFile' : $scope.AttachFileList
                            , 'updateObj': $scope.News
                        }
                    }).then(function (resp) {
                        
                        //console.log('Success ' + resp.config.data.fileimg.name + 'uploaded. Response: ' + resp.data);
                        console.log(resp.data.data.DATA);
                        //$scope.DataList[index] = resp.data.data.DATA;
                        if($scope.News.ActiveStatus == 'Y'){
                            $scope.News.NewsID = resp.data.data.DATA.NewsID;
                            $scope.requestNews();
                        }
                        IndexOverlayFactory.overlayHide();
                        $scope.showListPage();
                        $scope.DataList = [];
                        $scope.dataOffset = 0;
                        $scope.tableLoad = false;
                        $scope.continueLoad = true;
                        $scope.loadList();
                    }, function (resp) {
                        console.log('Error status: ' + resp.status);
                    }, function (evt) {
                        //var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
                        //console.log('progress: ' + progressPercentage + '% ' + evt.config.data.fileimg.name);
                    });
                });
            }
        };

        $scope.requestNews = function(){
            // $scope.alertMessage = 'ต้องการบันทึกและส่งคำขอสร้างข่าว ใช่หรือไม่ ?';
            // var modalInstance = $uibModal.open({
            //     animation : true,
            //     templateUrl : 'html/dialog_confirm.html',
            //     size : 'sm',
            //     scope : $scope,
            //     backdrop : 'static',
            //     controller : 'ModalDialogCtrl',
            //     resolve : {
            //         params : function() {
            //             return {};
            //         } 
            //     },
            // });
            // modalInstance.result.then(function (valResult) {

            //     IndexOverlayFactory.overlayShow();

                // $scope.News.NewsTitle = CKEDITOR.instances.editor_title1.getData();
                // $scope.News.NewsContent = CKEDITOR.instances.editor1.getData();
                // if($scope.News.LimitDisplay == 'N'){
                //      $scope.News.NewsStartDateTime = '';
                //      $scope.News.NewsEndDateTime = '';
                // }else{
                //     if($scope.News.StartDate != null && $scope.News.StartTime != null){
                //         $scope.News.NewsStartDateTime = concatDateTime($scope.News.StartDate, $scope.News.StartTime);
                //     }
                //     if($scope.News.EndDate != null && $scope.News.EndTime != null){
                //         $scope.News.NewsEndDateTime = concatDateTime($scope.News.EndDate, $scope.News.EndTime);
                //     }
                // }
                // if($scope.News.NewsDateTime != null){
                //     $scope.News.NewsDateTime = makeSQLDate($scope.News.NewsDateTime);
                // }

                NewsFactory.requestNews($scope.News).then(function(result){
                    IndexOverlayFactory.overlayHide();
                });
            // });
        }

    }

    $scope.loadList = function () {
        if($scope.continueLoad){
            $scope.tableLoad = true;
            NewsFactory.getList($scope.dataOffset
                                ,$scope.condition.RegionID 
                                ,$scope.condition.HideNews
                                ,$scope.condition.CurrentNews
                                ,$scope.condition.WaitApprove
                                ,$scope.currentUser.UserID).then(function(result){
                if(result.data.STATUS == 'OK'){
                    $scope.tableLoad = false;
                    $scope.dataOffset =  result.data.DATA.offset;
                    $scope.continueLoad = result.data.DATA.continueLoad;
                    for(var i = 0; i < result.data.DATA.DataList.length; i++){

                        result.data.DATA.DataList[i].NewsDateTimeFormat = convertDateToFullThaiDateIgnoreTime( convertDateToSQLString(result.data.DATA.DataList[i].NewsDateTimeFormat) );
                        //console.log(result.data.DATA.DataList[i].NewsDateTimeFormat);
                        $scope.DataList.push(result.data.DATA.DataList[i]);   

                    }
                }
            });
        }
    }

    $scope.getNewsByID = function(newsID){
        NewsFactory.getNewsByID(newsID).then(function(result){
            if(result.data.STATUS == 'OK'){
                $scope.showUpdatePage(0, result.data.DATA);
            }
        });
    }

    $scope.confirmDelete = function (index, ID) {
        $scope.alertMessage = 'ต้องการลบข่าวนี้ ใช่หรือไม่ ?';
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
            NewsFactory.deleteData(ID).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS == 'OK' && result.data.DATA){
                    $scope.DataList.splice(index, 1);
                }
            });
        });
    }

    

    $scope.showListPage = function (){
        $scope.showPage = 'MAIN';
    }

    $scope.setDefaultModelValue = function (){
        $scope.News = {'NewsID':''
                    ,'NewsTitle':''
                    ,'NewsContent':''
                    ,'NewsPicture':''
                    ,'VerifyBy':''
                    ,'VerifyDate':''
                    ,'NewsStatus':''
                    ,'NewsRegionID':$scope.currentUser.RegionID
                    ,'NewsType':''
                    ,'LimitDisplay':'N'
                    ,'NewsDateTime':''
                    ,'NewsStartDateTime':''
                    ,'NewsEndDateTime':''
                    ,'ActiveStatus':'Y'
                    ,'CreateBy':$scope.currentUser.UserID
                    ,'CreateDateTime':''
                    ,'UpdateBy':$scope.currentUser.UserID
                    ,'UpdateDateTime':''
                };
        $scope.fileimg = null;
    }

    $scope.setUpdateModelValue = function (obj){


        $scope.News = {'NewsID':obj.NewsID
                    ,'NewsTitle':obj.NewsTitle
                    ,'NewsContent':obj.NewsContent
                    ,'NewsPicture':obj.NewsPicture
                    ,'VerifyBy':obj.VerifyBy
                    ,'VerifyDate':obj.VerifyDate
                    ,'NewsStatus':obj.NewsStatus
                    ,'NewsRegionID':obj.NewsRegionID
                    ,'NewsType':obj.NewsType
                    ,'LimitDisplay':obj.LimitDisplay
                    ,'NewsDateTime':obj.NewsDateTime != null?convertDateToSQLString(obj.NewsDateTime):obj.NewsDateTime
                    // ,'NewsStartDateTime':obj.NewsStartDateTime!=null?convertDateToSQLString(obj.NewsStartDateTime):obj.NewsStartDateTime
                    // ,'NewsEndDateTime':obj.NewsEndDateTime != null?convertDateToSQLString(obj.NewsEndDateTime):obj.NewsEndDateTime
                    ,'ActiveStatus':obj.ActiveStatus
                    ,'StartDate':''
                    ,'StartTime':''
                    ,'EndDate':''
                    ,'EndTime':''
                    ,'NewsEnd':obj.NewsEndDateTime!=null?true:false
                    ,'CreateBy':obj.CreateBy
                    ,'CreateDateTime':obj.CreateDateTime
                    ,'UpdateBy':$scope.currentUser.UserID
                    ,'UpdateDateTime':obj.UpdateDateTime
                    ,'GlobalVisible':obj.GlobalVisible==null?'Y':obj.GlobalVisible
                    ,'ShowNewsFeed':obj.ShowNewsFeed==null?'N':obj.ShowNewsFeed
                    ,'RegionName':obj.RegionName
                    ,'pictures':obj.pictures
                    ,'attachFiles':obj.attach_files

                };
        //console.log($scope.News);
        $scope.fileimg = null;

        if(obj.NewsStartDateTime != null){
            $scope.News.StartDate = convertDateToSQLString(obj.NewsStartDateTime);
            $scope.News.StartTime = convertTimeToSQLString(obj.NewsStartDateTime);
            
        }

        if(obj.NewsEndDateTime != null){
            $scope.News.EndDate = convertDateToSQLString(obj.NewsEndDateTime);
            $scope.News.EndTime = convertTimeToSQLString(obj.NewsEndDateTime);
        }
    }

    $scope.checkAdminStatus = function (){
        if($scope.News.NewsStatus == 'Reject'){
            $scope.News.GlobalVisible = 'Y';
            $scope.News.ShowNewsFeed = 'N';
        }
    }

    $scope.adminUpdateStatus = function (){
        var ApproveWord = 'อนุมัติ';
        if($scope.News.NewsStatus == 'Reject'){
            ApproveWord = 'ไม่' + ApproveWord;
        }
        $scope.alertMessage = 'ต้องการ' + ApproveWord + 'คำขอสร้างข่าว ใช่หรือไม่ ?';
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

            $scope.News.NewsTitle = CKEDITOR.instances.editor_title1.getData();
            $scope.News.NewsContent = CKEDITOR.instances.editor1.getData();
            if($scope.News.LimitDisplay == 'N'){
                 $scope.News.NewsStartDateTime = '';
                 $scope.News.NewsEndDateTime = '';
            }else{
                if($scope.News.StartDate != null && $scope.News.StartTime != null){
                    $scope.News.NewsStartDateTime = concatDateTime($scope.News.StartDate, $scope.News.StartTime);
                }
                if($scope.News.EndDate != null && $scope.News.EndTime != null){
                    $scope.News.NewsEndDateTime = concatDateTime($scope.News.EndDate, $scope.News.EndTime);
                }
            }
            if($scope.News.NewsDateTime != null){
                $scope.News.NewsDateTime = makeSQLDate($scope.News.NewsDateTime);
            }

            NewsFactory.adminUpdateStatus($scope.News).then(function(result){
                IndexOverlayFactory.overlayHide();
            });
        });
    }

    $scope.setImgList = function () {
        $scope.ImgList = [{'fileimg':null},{'fileimg':null},{'fileimg':null},{'fileimg':null},{'fileimg':null}
                ,{'fileimg':null},{'fileimg':null}];
    }

    $scope.setAttachFileList = function (){
        $scope.AttachFileList = [{'attachFile':null},{'attachFile':null},{'attachFile':null},{'attachFile':null},{'attachFile':null}
                ,{'attachFile':null},{'attachFile':null}];
    }

    $scope.checkClearEndTime = function () {
        if(!$scope.News.NewsEnd){
            $scope.News.EndDate = null;
            $scope.News.EndTime = null;
        }
    }

    $scope.checkLimitDisplay = function () {
        if($scope.News.LimitDisplay == 'N'){
            $scope.News.StartDate = null;
            $scope.News.StartTime = null;
            $scope.News.NewsEnd = false;
            $scope.News.EndDate = null;
            $scope.News.EndTime = null;
        }
    }

    $scope.deleteNewsPicture = function (index, ID){
        IndexOverlayFactory.overlayShow();
        NewsFactory.deleteNewsPictureData(ID).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK' && result.data.DATA){
                $scope.News.pictures.splice(index, 1);
            }
        });
    }

    $scope.deleteAttachFile = function (index, ID){
        IndexOverlayFactory.overlayShow();
        NewsFactory.deleteNewsAttachFile(ID).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK' && result.data.DATA){
                $scope.News.attactFiles.splice(index, 1);
            }
        });
    }

    $scope.setCondition = function (){
        if($scope.condition.RegionID ==null){
            $scope.condition.RegionID = '0';
        }
        $scope.DataList = [];
        $scope.dataOffset = 0;
        $scope.tableLoad = false;
        $scope.continueLoad = true;
        $scope.loadList();
    }

    $scope.goBackMain = function () {
        $scope.loadList();
        $scope.showPage = 'MAIN';
        // if($scope.DataList.length == 0){
        //     // $scope.loadList();
        //     $scope.showPage = 'MAIN';
        // }else{
        //     $scope.showPage = 'MAIN';
        // }
        
    }

    $scope.popup1 = {
        opened: false
    };

    $scope.popup2 = {
        opened: false
    };

    $scope.popup3 = {
        opened: false
    };

    $scope.open1 = function() {
        $scope.popup1.opened = true;
    };

    $scope.open2 = function() {
        $scope.popup2.opened = true;
    };

    $scope.open3 = function() {
        $scope.popup3.opened = true;
    };

    $scope.timeList = ['00:00','01:00','02:00','03:00','04:00','05:00','06:00'
                        ,'07:00','08:00','09:00','10:00','11:00','12:00'
                        ,'13:00','14:00','15:00','16:00','17:00','18:00'
                        ,'19:00','20:00','21:00','22:00','23:00'];

    
    // Variables
    $scope.RegionList = [];
    $scope.showPage = '';
    $scope.dataOffset = 0;
    $scope.tableLoad = false;
    $scope.continueLoad = true;
    $scope.DataList = [];
    $scope.condition ={'RegionID':'0','HideNews':'0','CurrentNews':'0','WaitApprove':'0'};

    if($routeParams.newsID !== undefined){

        $scope.loadRegionList();
        $scope.loadNewsTypeList();
        $scope.getNewsByID($routeParams.newsID);
        
    }else{
        
        // Initial page
        $scope.showListPage();
        $scope.loadList();
        $scope.loadRegionList();
        $scope.loadNewsTypeList();
    }
});

app.controller('RoomController', function($cookies, $scope, $http, $uibModal, IndexOverlayFactory, RoomFactory, RegionFactory, Upload) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'manage_room';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }
    IndexOverlayFactory.overlayHide();

    $scope.loadRegionList = function(){
        RegionFactory.getAllRegion().then(function(result){
            if(result.data.STATUS == 'OK'){
                $scope.RegionList = result.data.DATA;
            }
        });
    }

    $scope.loadList = function () {
        if($scope.continueLoad && !$scope.tableLoad){
            $scope.tableLoad = true;
            RoomFactory.getList($scope.dataOffset, $scope.currentUser.UserID).then(function(result){
                if(result.data.STATUS == 'OK'){
                    $scope.tableLoad = false;
                    $scope.dataOffset =  result.data.DATA.offset;
                    $scope.continueLoad = result.data.DATA.continueLoad;
                    for(var i = 0; i < result.data.DATA.DataList.length; i++){
                        $scope.DataList.push(result.data.DATA.DataList[i]);    
                    }
                }
            });
        }
    }

    $scope.confirmDelete = function (index, ID) {
        $scope.alertMessage = 'ต้องการลบห้องนี้ ใช่หรือไม่ ?';
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
            RoomFactory.deleteData(ID).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS == 'OK' && result.data.DATA){
                    $scope.DataList.splice(index, 1);
                }
            });
        });
    }

    $scope.showUpdatePage = function (index, obj){
        $scope.setDefaultModelValue();
        if(index != -1 && obj != null){
            $scope.setUpdateModelValue(obj);
        }
        $scope.showPage = 'UPDATE';

        // Update Zone
        $scope.upload = function (fileimg) {
            // var validFilename = /^[a-z0-9_.@()-]+\.[^.]+$/i;
            // if(fileimg != null && !validFilename.test(fileimg.name)){ // check main picture
            //    alert('ระบบไม่รองรับชื่อไฟล์รูปภาพภาษาไทยและอักขระพิเศษ');
            //    return false;
            // }
            IndexOverlayFactory.overlayShow();
            Upload.upload({
                url: servicesUrl + '/dpo/public/updateRoomData/',
                data: {fileimg: fileimg, 'updateObj': $scope.Room}
            }).then(function (resp) {
                IndexOverlayFactory.overlayHide();
                //console.log('Success ' + resp.config.data.fileimg.name + 'uploaded. Response: ' + resp.data);
                console.log(resp.data.data.DATA);
                //$scope.DataList[index] = resp.data.data.DATA;
                $scope.showListPage();
                $scope.dataOffset = 0;
                $scope.tableLoad = false;
                $scope.continueLoad = true;
                $scope.DataList = [];

                $scope.loadList();
            }, function (resp) {
                console.log('Error status: ' + resp.status);
            }, function (evt) {
                //var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
                //console.log('progress: ' + progressPercentage + '% ' + evt.config.data.fileimg.name);
            });
        };

    }

    $scope.showListPage = function (){
        $scope.showPage = 'MAIN';
        $scope.setDefaultModelValue();
    }

    $scope.setDefaultModelValue = function (){

        $scope.Room = {'ActiveStatus':"Y"
                        ,'ConferenceType':"N"
                        ,'CreateBy':$scope.currentUser.UserID
                        ,'CreateDateTime':""
                        ,'DeviceAdminID':""
                        ,'FoodAdminID':""
                        ,'RegionID':$scope.currentUser.RegionID
                        ,'RegionName':""
                        ,'RoomAdminID':$scope.currentUser.UserID
                        ,'RoomDescription':""
                        ,'RoomID':""
                        ,'RoomName':""
                        ,'RoomPicture':""
                        ,'SeatAmount':""
                        ,'UpdateBy':""
                        ,'UpdateDateTime':""
                        ,'device_admin_Mobile':""
                        ,'device_admin_email':""
                        ,'device_admin_name':""
                        ,'food_admin_Mobile':""
                        ,'food_admin_email':""
                        ,'food_admin_name':""
                        ,'room_admin_Mobile':$scope.currentUser.Mobile
                        ,'room_admin_email':$scope.currentUser.Email
                        ,'room_admin_name':$scope.currentUser.FirstName + ' ' + $scope.currentUser.LastName
                    }
        $scope.fileimg = null;
        //console.log('default $scope.Room : ', $scope.Room);
    }

    $scope.setUpdateModelValue = function (obj){
        $scope.Room = {'ActiveStatus':obj.ActiveStatus
                        ,'ConferenceType':obj.ConferenceType
                        ,'CreateBy':obj.CreateBy
                        ,'CreateDateTime':obj.CreateDateTime
                        ,'DeviceAdminID':obj.DeviceAdminID
                        ,'FoodAdminID':obj.FoodAdminID
                        ,'RegionID':parseInt(obj.RegionID)
                        ,'RegionName':obj.RegionName
                        ,'RoomAdminID':obj.RoomAdminID
                        ,'RoomDescription':obj.RoomDescription
                        ,'RoomID':obj.RoomID
                        ,'RoomName':obj.RoomName
                        ,'RoomPicture':obj.RoomPicture
                        ,'SeatAmount':parseInt(obj.SeatAmount)
                        ,'UpdateBy':$scope.currentUser.UserID
                        ,'UpdateDateTime':obj.UpdateDateTime
                        ,'device_admin_Mobile':obj.device_admin_Mobile
                        ,'device_admin_email':obj.device_admin_email
                        ,'device_admin_name':obj.device_admin_firstname + ' ' + obj.device_admin_lastname
                        ,'food_admin_Mobile':obj.food_admin_Mobile
                        ,'food_admin_email':obj.food_admin_email
                        ,'food_admin_name':obj.food_admin_firstname + ' ' + obj.food_admin_lastname
                        ,'room_admin_Mobile':obj.room_admin_Mobile
                        ,'room_admin_email':obj.room_admin_email
                        ,'room_admin_name':obj.room_admin_firstname + ' ' + obj.room_admin_lastname
                    }
        $scope.fileimg = null;
        $scope.room_admin_name = obj.room_admin_firstname + ' ' + obj.room_admin_lastname;
        $scope.room_device_name = obj.device_admin_firstname + ' ' + obj.device_admin_lastname;
        $scope.room_food_name = obj.food_admin_firstname + ' ' + obj.food_admin_lastname;
    }

    $autocompleteUserResult = [];
    $scope.searchUserAutoComplete = function (val, qtype){
        val = encodeURIComponent(val);
        return $http.get(servicesUrl + "/dpo/public/autocomplete/" + qtype + "/" + val).then(function(response){
          
          $autocompleteUserResult = response.data.data.DATA;
          var loop = $autocompleteUserResult.length;
          //console.log($autocompleteUserResult);
          if(loop > 0){
              var objList = [];
              for(var i = 0; i < loop; i++){
                objList.push((i + 1) + ' : ' + $autocompleteUserResult[i].FirstName + ' ' + $autocompleteUserResult[i].LastName);
              }
              return objList;
          }else{
            return null;
          }
          
        });
    };
    $scope.autocompleteUserSelected = function ($item, $model, $label, $type){
        
        //IndexOverlayFactory.overlayShow();
        var itemSplit = $item.split(':');
        var index = parseInt(itemSplit[0].trim()) - 1;
        //console.log('index', index);
        
        // Check UserID already exist
        //var offset = $filter('FindUserID')($scope.InternalAttendeeList, $autocompleteUserResult[index].UserID);
        //console.log('offset', offset, 'index' , index);
        if($type == 'ROOM'){
            $scope.room_admin_name = $autocompleteUserResult[index].FirstName + ' ' + $autocompleteUserResult[index].LastName;
            $scope.Room.RoomAdminID = $autocompleteUserResult[index].UserID;
            $scope.Room.room_admin_name = $autocompleteUserResult[index].FirstName + ' ' + $autocompleteUserResult[index].LastName;
            $scope.Room.room_admin_email = $autocompleteUserResult[index].Email;
            $scope.Room.room_admin_Mobile = $autocompleteUserResult[index].Mobile;
        }else if($type == 'DEVICE'){
            $scope.room_device_name = $autocompleteUserResult[index].FirstName + ' ' + $autocompleteUserResult[index].LastName;
            $scope.Room.DeviceAdminID = $autocompleteUserResult[index].UserID;
            $scope.Room.device_admin_name = $autocompleteUserResult[index].FirstName + ' ' + $autocompleteUserResult[index].LastName;
            $scope.Room.device_admin_email = $autocompleteUserResult[index].Email;
            $scope.Room.device_admin_Mobile = $autocompleteUserResult[index].Mobile;
        }else {
            $scope.room_food_name = $autocompleteUserResult[index].FirstName + ' ' + $autocompleteUserResult[index].LastName;
            $scope.Room.FoodAdminID = $autocompleteUserResult[index].UserID;
            $scope.Room.food_admin_name = $autocompleteUserResult[index].FirstName + ' ' + $autocompleteUserResult[index].LastName;
            $scope.Room.food_admin_email = $autocompleteUserResult[index].Email;
            $scope.Room.food_admin_Mobile = $autocompleteUserResult[index].Mobile;
        }
    }

    $scope.checkEmptyField = function($type, val){
        if($type == 'ROOM'){
            console.log(val , $scope.room_admin_name);
            if(val == null || (val != $scope.room_admin_name && $scope.Room.RoomAdminID != '')){
                $scope.Room.RoomAdminID = '';
                $scope.Room.room_admin_name = '';
                $scope.Room.room_admin_email = '';
                $scope.Room.room_admin_Mobile = '';
            }
        }else if($type == 'DEVICE'){
            console.log(val , $scope.room_device_name);
            if(val == null || (val != $scope.room_device_name && $scope.Room.DeviceAdminID != '')){
                $scope.Room.DeviceAdminID = '';
                $scope.Room.device_admin_name = '';
                $scope.Room.device_admin_email = '';
                $scope.Room.device_admin_Mobile = '';    
            }
            
        }else if($type == 'FOOD'){
            if(val == null || (val != $scope.room_food_name && $scope.Room.FoodAdminID != '')){
                $scope.Room.FoodAdminID = '';
                $scope.Room.food_admin_name = '';
                $scope.Room.food_admin_email = '';
                $scope.Room.food_admin_Mobile = '';
            }
        }
    }

    // Variables
    $scope.RegionList = [];
    $scope.showPage = '';
    $scope.dataOffset = 0;
    $scope.tableLoad = false;
    $scope.continueLoad = true;
    $scope.DataList = [];

    // Initial page
    $scope.showListPage();
    $scope.loadList();
    $scope.loadRegionList();

});

app.controller('FoodController', function($cookies, $scope, $http, $uibModal, IndexOverlayFactory, FoodFactory, RegionFactory, Upload) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'manage_food';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }
    IndexOverlayFactory.overlayHide();

    $scope.loadRegionList = function(){
        RegionFactory.getAllRegion().then(function(result){
            if(result.data.STATUS == 'OK'){
                $scope.RegionList = result.data.DATA;
            }
        });
    }

    $scope.loadList = function () {
        if($scope.continueLoad && !$scope.tableLoad){
            $scope.tableLoad = true;
            FoodFactory.getList($scope.dataOffset).then(function(result){
                if(result.data.STATUS == 'OK'){
                    $scope.tableLoad = false;
                    $scope.dataOffset =  result.data.DATA.offset;
                    $scope.continueLoad = result.data.DATA.continueLoad;
                    for(var i = 0; i < result.data.DATA.DataList.length; i++){
                        $scope.DataList.push(result.data.DATA.DataList[i]);    
                    }
                }
            });
        }
    }

    $scope.confirmDelete = function (index, ID) {
        $scope.alertMessage = 'ต้องการลบอาหารนี้ ใช่หรือไม่ ?';
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
            FoodFactory.deleteData(ID).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS == 'OK' && result.data.DATA){
                    $scope.DataList.splice(index, 1);
                }
            });
        });
    }

    $scope.showUpdatePage = function (index, obj){
        $scope.setDefaultModelValue();
        if(index != -1 && obj != null){
            $scope.setUpdateModelValue(obj);
        }
        $scope.showPage = 'UPDATE';

        // Update Zone
        $scope.upload = function (fileimg) {
            // var validFilename = /^[a-z0-9_.@()-]+\.[^.]+$/i;
            // if(fileimg != null && !validFilename.test(fileimg.name)){ // check main picture
            //    alert('ระบบไม่รองรับชื่อไฟล์รูปภาพภาษาไทยและอักขระพิเศษ');
            //    return false;
            // }
            IndexOverlayFactory.overlayShow();
            Upload.upload({
                url: servicesUrl + '/dpo/public/updateFoodData/',
                data: {fileimg: fileimg, 'updateObj': $scope.Food}
            }).then(function (resp) {
                IndexOverlayFactory.overlayHide();
                //console.log('Success ' + resp.config.data.fileimg.name + 'uploaded. Response: ' + resp.data);
                console.log(resp.data.data.DATA);
                //$scope.DataList[index] = resp.data.data.DATA;
                $scope.showListPage();
                $scope.dataOffset = 0;
                $scope.tableLoad = false;
                $scope.continueLoad = true;
                $scope.DataList = [];

                $scope.loadList();
            }, function (resp) {
                console.log('Error status: ' + resp.status);
            }, function (evt) {
                //var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
                //console.log('progress: ' + progressPercentage + '% ' + evt.config.data.fileimg.name);
            });
        };

    }

    $scope.showListPage = function (){
        $scope.showPage = 'MAIN';
        $scope.setDefaultModelValue();
    }

    $scope.setDefaultModelValue = function (){

        $scope.Food = {'FoodID':''
                        ,'RegionID':""
                        ,'FoodName':""
                        ,'FoodPicture':""
                        ,'FoodDescription':""
                        ,'ActiveStatus':"Y"
                        ,'UpdateBy':""
                        ,'UpdateDateTime':""
                        ,'CreateBy':$scope.currentUser.UserID
                        ,'CreateDateTime':""
                        
                    }
        $scope.fileimg = null;
    }

    $scope.setUpdateModelValue = function (obj){
        $scope.Food = {'FoodID':obj.FoodID
                        ,'RegionID':obj.RegionID
                        ,'FoodName':obj.FoodName
                        ,'FoodPicture':obj.FoodPicture
                        ,'FoodDescription':obj.FoodDescription
                        ,'ActiveStatus':obj.ActiveStatus
                        ,'UpdateBy':$scope.currentUser.UserID
                        ,'UpdateDateTime':obj.UpdateDateTime
                        ,'CreateBy':obj.CreateBy
                        ,'CreateDateTime':obj.CreateDateTime
                    }
        $scope.fileimg = null;
    }

    // Variables
    $scope.RegionList = [];
    $scope.showPage = '';
    $scope.dataOffset = 0;
    $scope.tableLoad = false;
    $scope.continueLoad = true;
    $scope.DataList = [];

    // Initial page
    $scope.showListPage();
    $scope.loadList();
    $scope.loadRegionList();

});

app.controller('DeviceController', function($cookies, $scope, $http, $uibModal, IndexOverlayFactory, DeviceFactory, RegionFactory, Upload) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'manage_device';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }
    IndexOverlayFactory.overlayHide();

    $scope.loadRegionList = function(){
        RegionFactory.getAllRegion().then(function(result){
            if(result.data.STATUS == 'OK'){
                $scope.RegionList = result.data.DATA;
            }
        });
    }

    $scope.loadList = function () {
        if($scope.continueLoad && !$scope.tableLoad){
            $scope.tableLoad = true;
            DeviceFactory.getList($scope.dataOffset, $scope.currentUser.UserID).then(function(result){
                if(result.data.STATUS == 'OK'){
                    $scope.tableLoad = false;
                    $scope.dataOffset =  result.data.DATA.offset;
                    $scope.continueLoad = result.data.DATA.continueLoad;
                    for(var i = 0; i < result.data.DATA.DataList.length; i++){
                        $scope.DataList.push(result.data.DATA.DataList[i]);    
                    }
                }
            });
        }
    }

    $scope.confirmDelete = function (index, ID) {
        $scope.alertMessage = 'ต้องการลบอุปกรณ์นี้ ใช่หรือไม่ ?';
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
            DeviceFactory.deleteData(ID).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS == 'OK' && result.data.DATA){
                    $scope.DataList.splice(index, 1);
                }
            });
        });
    }

    $scope.showUpdatePage = function (index, obj){
        $scope.setDefaultModelValue();
        if(index != -1 && obj != null){
            $scope.setUpdateModelValue(obj);
        }
        $scope.showPage = 'UPDATE';

        // Update Zone
        $scope.upload = function (fileimg) {
            // var validFilename = /^[a-z0-9_.@()-]+\.[^.]+$/i;
            // if(fileimg != null && !validFilename.test(fileimg.name)){ // check main picture
            //    alert('ระบบไม่รองรับชื่อไฟล์รูปภาพภาษาไทยและอักขระพิเศษ');
            //    return false;
            // }
            IndexOverlayFactory.overlayShow();
            Upload.upload({
                url: servicesUrl + '/dpo/public/updateDeviceData/',
                data: {fileimg: fileimg, 'updateObj': $scope.Device}
            }).then(function (resp) {
                IndexOverlayFactory.overlayHide();
                //console.log('Success ' + resp.config.data.fileimg.name + 'uploaded. Response: ' + resp.data);
                console.log(resp.data.data.DATA);
                //$scope.DataList[index] = resp.data.data.DATA;
                $scope.showListPage();
                $scope.dataOffset = 0;
                $scope.tableLoad = false;
                $scope.continueLoad = true;
                $scope.DataList = [];

                $scope.loadList();
            }, function (resp) {
                console.log('Error status: ' + resp.status);
            }, function (evt) {
                //var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
                //console.log('progress: ' + progressPercentage + '% ' + evt.config.data.fileimg.name);
            });
        };

    }

    $scope.showListPage = function (){
        $scope.showPage = 'MAIN';
        $scope.setDefaultModelValue();
    }

    $scope.setDefaultModelValue = function (){

        $scope.Device = {'DeviceID':''
                        ,'RegionID':""
                        ,'DeviceName':""
                        ,'DevicePicture':""
                        ,'DeviceDescription':""
                        ,'DeviceAmount':""
                        ,'ActiveStatus':"Y"
                        ,'UpdateBy':""
                        ,'UpdateDateTime':""
                        ,'CreateBy':$scope.currentUser.UserID
                        ,'CreateDateTime':""
                        
                    }
        $scope.fileimg = null;
    }

    $scope.setUpdateModelValue = function (obj){
        $scope.Device = {'DeviceID':obj.DeviceID
                        ,'RegionID':obj.RegionID
                        ,'DeviceName':obj.DeviceName
                        ,'DevicePicture':obj.DevicePicture
                        ,'DeviceDescription':obj.DeviceDescription
                        ,'DeviceAmount':obj.DeviceAmount
                        ,'ActiveStatus':obj.ActiveStatus
                        ,'UpdateBy':$scope.currentUser.UserID
                        ,'UpdateDateTime':obj.UpdateDateTime
                        ,'CreateBy':obj.CreateBy
                        ,'CreateDateTime':obj.CreateDateTime
                    }
        $scope.fileimg = null;

    }

    // Variables
    $scope.RegionList = [];
    $scope.showPage = '';
    $scope.dataOffset = 0;
    $scope.tableLoad = false;
    $scope.continueLoad = true;
    $scope.DataList = [];

    // Initial page
    $scope.showListPage();
    $scope.loadList();
    $scope.loadRegionList();



});

app.controller('PhoneBookController', function($cookies, $scope, $http, $uibModal, IndexOverlayFactory, PhoneBookFactory, RegionFactory, DepartmentFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'telephonebook_internal';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }
    IndexOverlayFactory.overlayHide();

    $scope.loadRegionList = function(){
        RegionFactory.getAllRegion().then(function(result){
            if(result.data.STATUS == 'OK'){
                $scope.RegionList = result.data.DATA;
            }
        });
    }

    $scope.loadDepartmentList = function(){
        DepartmentFactory.getAllDepartmentList().then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.DepartmentList = result.data.DATA;
            }
        });
    }

    $scope.loadList = function () {

        if($scope.continueLoad){
            $scope.tableLoad = true;
            PhoneBookFactory.getList($scope.Group==''?'ALL':$scope.Group
                                    , $scope.Username==''?'-':$scope.Username
                                    , $scope.dataOffset, $scope.ConRegion, $scope.ConDepartment, $scope.currentUser.UserID).then(function(result){
                if(result.data.STATUS == 'OK'){
                    $scope.tableLoad = false;
                    $scope.dataOffset =  result.data.DATA.offset;
                    $scope.continueLoad = result.data.DATA.continueLoad;
                    for(var i = 0; i < result.data.DATA.DataList.length; i++){
                        $scope.DataList.push(result.data.DATA.DataList[i]);    
                    }
                }
                $scope.disabled_search = false;
            });
        }
    }

    $scope.viewContact = function (index, data){
        $scope.Contact = data;
        $scope.DataIndex = index;
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'view_contact.html',
            size: 'md',
            scope: $scope,
            backdrop: 'static',
            controller: 'ModalDialogCtrl',
            resolve: {
                params: function () {
                    return {};
                }
            },
        });

        modalInstance.result.then(function (valResult) {

        });

        $scope.addFavourite = function(UserFavouriteID){
            IndexOverlayFactory.overlayShow();
            PhoneBookFactory.addFavourite(UserFavouriteID, $scope.currentUser.UserID).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS == 'OK'){
                    console.log(result.data.DATA);
                    $scope.DataList[$scope.DataIndex] = result.data.DATA;
                    $scope.Contact = result.data.DATA;
                }else{
                    alert(result.data.DATA);
                }
            });
        }

        $scope.removeFavourite = function (FavouriteID, UserID){
            IndexOverlayFactory.overlayShow();
            PhoneBookFactory.removeFavourite(FavouriteID, UserID).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS == 'OK'){
                    console.log(result.data.DATA);
                    $scope.DataList[$scope.DataIndex] = result.data.DATA;
                    $scope.Contact = result.data.DATA;
                }else{
                    alert(result.data.DATA);
                }
            });
        }
    }

    $scope.editProfile = function(UserLoginID){
        IndexOverlayFactory.overlayShow();
        PhoneBookFactory.getUserContact(UserLoginID).then(function (result) {
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.Contact = result.data.DATA;
                var modalInstance = $uibModal.open({
                    animation: true,
                    templateUrl: 'update_contact.html',
                    size: 'md',
                    scope: $scope,
                    backdrop: 'static',
                    controller: 'ModalDialogReturnFromOKBtnCtrl',
                    resolve: {
                        params: function () {
                            return {};
                        }
                    },
                });
                modalInstance.result.then(function (valResult) {
                    $scope.updateProfile(valResult);
                });
            }else{
                alert('ไม่พบข้อมูล');
            }
        });

        $scope.updateProfile = function (Contact){
            IndexOverlayFactory.overlayShow();
            PhoneBookFactory.updateContact(Contact).then(function (result) {
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS == 'OK'){
                    $scope.dataOffset = 0;
                    $scope.tableLoad = false;
                    $scope.continueLoad = true;
                    $scope.DataList = [];
                    
                    $scope.loadList();
                }
            });
        }
    }

    $scope.search = function(){
        $scope.disabled_search = true;
        $scope.dataOffset = 0;
        $scope.tableLoad = false;
        $scope.continueLoad = true;
        $scope.DataList = [];
        $scope.loadList();
    }

    $scope.disabled_search = false;
    $scope.RegionList = [];
    $scope.dataOffset = 0;
    $scope.tableLoad = false;
    $scope.continueLoad = true;
    $scope.DataList = [];
    $scope.DepartmentListList = [];
    $scope.ConRegion = '1';
    $scope.ConDepartment = '1';
    $scope.Group = '';
    $scope.Username = '';
    $scope.loadList();
    $scope.loadDepartmentList();
    $scope.loadRegionList();

});


app.controller('ExternalPhoneBookManageController', function($cookies, $scope, $http, $uibModal, Upload, IndexOverlayFactory, ExternalPhoneBookFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'manage_telephonebook';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }
    IndexOverlayFactory.overlayHide();

    $scope.loadList = function () {
        if($scope.continueLoad){
            $scope.tableLoad = true;
            ExternalPhoneBookFactory.getManagePhoneBookList($scope.dataOffset, $scope.currentUser.UserID, $scope.condition).then(function(result){
                if(result.data.STATUS == 'OK'){
                    $scope.tableLoad = false;
                    $scope.dataOffset =  result.data.DATA.offset;
                    $scope.continueLoad = result.data.DATA.continueLoad;
                    for(var i = 0; i < result.data.DATA.DataList.length; i++){
                        $scope.DataList.push(result.data.DATA.DataList[i]);    
                    }
                }
                $scope.disabled_search = false;
            });
        }
    }

    $scope.search = function(){
        $scope.disabled_search = true;
        $scope.dataOffset = 0;
        $scope.tableLoad = false;
        $scope.continueLoad = true;
        $scope.DataList = [];
        $scope.loadList();
    }

    $scope.updateProfileDialog = function(index, data){
        
        if(index == -1){
            $scope.Contact = {PhoneBookID:'', ActiveStatus:'Y', Picture:'', CreateBy:$scope.currentUser.UserID, fileimg:null};    
        }else{
            $scope.Contact = data;
            $scope.Contact.UpdateBy = $scope.currentUser.UserID;
        }
        
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'update_ex_contact.html',
            size: 'md',
            scope: $scope,
            backdrop: 'static',
            controller: 'ModalDialogReturnFromOKBtnCtrl',
            resolve: {
                params: function () {
                    return {};
                }
            },
        });
        modalInstance.result.then(function (valResult) {
            $scope.updateProfile(valResult);
        });

        $scope.updateProfile = function (Contact){
            IndexOverlayFactory.overlayShow();
            // var validFilename = /^[a-z0-9_.@()-]+\.[^.]+$/i;
            // if(Contact.fileimg != null && !validFilename.test(Contact.fileimg.name)){ // check main picture
            //    alert('ระบบไม่รองรับชื่อไฟล์รูปภาพภาษาไทยและอักขระพิเศษ');
            //    return false;
            // }
            Upload.upload({
                url: servicesUrl + '/dpo/public/updateExternalPhoneBook/',
                data: {'fileimg' : Contact.fileimg
                    , 'Contact': Contact}
            }).then(function (resp) {
                IndexOverlayFactory.overlayHide();
                //console.log(resp.data.data.DATA);
                if(result.data.STATUS == 'OK'){
                    $scope.dataOffset = 0;
                    $scope.tableLoad = false;
                    $scope.continueLoad = true;
                    $scope.DataList = [];
                    $scope.fileimg = null;
                    $scope.loadList();
                }else{
                    alert();
                }
            }, function (resp) {
                console.log('Error status: ' + resp.status);
            }, function (evt) {
                //var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
                //console.log('progress: ' + progressPercentage + '% ' + evt.config.data.fileimg.name);
            });
        }
    }

    $scope.goUpdatePermission = function(ID){
        window.location.href = '#/update_phonebook_permission/' + ID;
    }

    $scope.disabled_search = false;
    $scope.dataOffset = 0;
    $scope.tableLoad = false;
    $scope.continueLoad = true;
    $scope.DataList = [];
    $scope.condition = {'keyword':'', 'activeStatus':''};
    
    $scope.loadList();

});

app.controller('PhoneBookPermissionController', function($cookies, $scope, $http, $uibModal, $routeParams, IndexOverlayFactory, ExternalPhoneBookFactory, RegionFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'telephonebook_external';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }
    IndexOverlayFactory.overlayHide();

    $scope.loadRegionList = function () {
        RegionFactory.getAllRegion().then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.RegionList = result.data.DATA;
            }
        });
    }

    $scope.loadPhoneBookPermission = function (PhoneBookID, RegionID) {
        IndexOverlayFactory.overlayShow();
        ExternalPhoneBookFactory.loadPhoneBookPermission(PhoneBookID, RegionID).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.DataList = result.data.DATA.DataList;   
                $scope.PhoneBook = result.data.DATA.PhoneBook;
            }
        });
        
    }

    $scope.search = function(RegionID){
        // console.log(RegionID);
        RegionID = RegionID==''|| RegionID==null?'ALL':RegionID;
        $scope.loadPhoneBookPermission($routeParams.PhoneBookID, RegionID);
    }

    $scope.updatePermission = function(RegionID, UpdateType, GroupID, OrgID, AllowStatus){
        IndexOverlayFactory.overlayShow();
        RegionID = (RegionID == undefined || RegionID == null || RegionID==''?'ALL':RegionID);
        var updateObj = {
                            PhoneBookID : $routeParams.PhoneBookID
                            ,RegionID : RegionID
                            ,UpdateType : UpdateType
                            ,GroupID : GroupID
                            ,OrgID : OrgID
                            ,UpdateBy : $scope.currentUser.UserID
                            ,AllowStatus : AllowStatus
                        };
        ExternalPhoneBookFactory.updateExPhoneBookPermission(updateObj).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                //if(UpdateType == 'SELECT_ALL' || UpdateType == 'UNSELECT_ALL'){
                    $scope.search(RegionID);
                //}
            }
        });
    }

    $scope.condition = {RegionID:''};
    if($routeParams.PhoneBookID != undefined){
        $scope.loadRegionList();
        $scope.loadPhoneBookPermission($routeParams.PhoneBookID, 'ALL');
    }

});

app.controller('PhoneBookEXController', function($cookies, $scope, $http, $uibModal, Upload, IndexOverlayFactory, ExternalPhoneBookFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'telephonebook_external';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }
    IndexOverlayFactory.overlayHide();

    $scope.loadList = function () {
        if($scope.continueLoad){
            $scope.tableLoad = true;
            ExternalPhoneBookFactory.getExternalPhoneBookList($scope.dataOffset, $scope.condition).then(function(result){
                if(result.data.STATUS == 'OK'){
                    $scope.tableLoad = false;
                    $scope.dataOffset =  result.data.DATA.offset;
                    $scope.continueLoad = result.data.DATA.continueLoad;
                    for(var i = 0; i < result.data.DATA.DataList.length; i++){
                        $scope.DataList.push(result.data.DATA.DataList[i]);    
                    }
                    $scope.disabled_search = false;
                }else{
                    $scope.disabled_search = false;
                }


            });
        }
    }

    $scope.search = function(){
        $scope.disabled_search = true;
        $scope.dataOffset = 0;
        $scope.tableLoad = false;
        $scope.continueLoad = true;
        $scope.DataList = [];
        $scope.loadList();
    }

    $scope.viewProfileDialog = function(index, data){
        
        
        $scope.Contact = data;
        
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'view_contact.html',
            size: 'lg',
            scope: $scope,
            backdrop: 'static',
            controller: 'ModalDialogReturnFromOKBtnCtrl',
            resolve: {
                params: function () {
                    return {};
                }
            },
        });
        modalInstance.result.then(function (valResult) {
            
        });

        $scope.addFavourite = function(UserFavouriteID){
            IndexOverlayFactory.overlayShow();
            ExternalPhoneBookFactory.addFavourite(UserFavouriteID, $scope.currentUser.UserID).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS == 'OK'){
                    console.log(result.data.DATA);
                    $scope.DataList[index] = result.data.DATA;
                    $scope.Contact = result.data.DATA;
                }
            });
        }

        $scope.removeFavourite = function (FavouriteID, UserID){
            IndexOverlayFactory.overlayShow();
            ExternalPhoneBookFactory.removeFavourite(FavouriteID, UserID).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS == 'OK'){
                    console.log(result.data.DATA);
                    $scope.DataList[index] = result.data.DATA;
                    $scope.Contact = result.data.DATA;
                }
            });
        }
    }

    $scope.disabled_search = false;
    $scope.dataOffset = 0;
    $scope.tableLoad = false;
    $scope.continueLoad = true;
    $scope.DataList = [];
    $scope.condition = {'keyword':'', 'activeStatus':'Y', 'LoginUserID':$scope.currentUser.UserID};
    
    $scope.loadList();

});


app.controller('RuleController', function($cookies, $scope, $http, $uibModal, Upload, IndexOverlayFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'rule';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }
    IndexOverlayFactory.overlayHide();

    $scope.Rule = {
        'Title' : 'กฎ ระเบียบ ข้อบังคับ'
        ,'RuleList' : [
            {
                'RuleTitle' : 'ข้อบังคับการแบ่งส่วนงาน'
                ,'RuleContent' : 
                [
                    {'Content' : 'ข้อบังคับแบ่งส่วนงาน ฉบับสมบูรณ์ (2560)','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=170'}
                    ,{'Content' : 'ข้อบังคับ อสค การแบ่งส่วนงานและการกำหนดอำนาจหน้าที่ของส่วนงาน พ.ศ.2555','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=100'}
                    ,{'Content' : 'ข้อบังคับ อสค การแบ่งส่วนงานและการกำหนดอำนาจหน้าที่ของส่วนงาน พ.ศ.2555 ฉบับเพิ่มเติมครั้งที่ 1 พ.ศ. 2556','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=101'}
                    ,{'Content' : 'ข้อบังคับ อสค การแบ่งส่วนงานและการกำหนดอำนาจหน้าที่ของส่วนงาน พ.ศ.2555 ฉบับเพิ่มเติมครั้งที่ 2 พ.ศ. 2556','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=102'}
                    ,{'Content' : 'ข้อบังคับ อสค การแบ่งส่วนงานและการกำหนดอำนาจหน้าที่ของส่วนงาน พ.ศ.2555 ฉบับเพิ่มเติมครั้งที่ 3 พ.ศ. 2556','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=103'}
                    ,{'Content' : 'ข้อบังคับ อสค การแบ่งส่วนงานและการกำหนดอำนาจหน้าที่ของส่วนงาน พ.ศ.2555 ฉบับเพิ่มเติมครั้งที่ 4 พ.ศ. 2559','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=104'}
                    ,{'Content' : 'ข้อบังคับ อสค การแบ่งส่วนงานและการกำหนดอำนาจหน้าที่ของส่วนงาน พ.ศ.2555 ฉบับเพิ่มเติมครั้งที่ 5 พ.ศ. 2559','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=105'}
                ]
            }
            ,{
                'RuleTitle' : 'ข้อบังคับการพนักงาน'
                ,'RuleContent' : 
                [
                    {'Content' : 'ข้อบังคับ อ.ส.ค.ว่าด้วยค่าใช้จ่ายในการเดินทาง พ.ศ. 2561','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=212'}
                    ,{'Content' : 'ข้อบังคับว่าด้วยค่าใช้จ่ายในการเดินทางไปปฏิบัติงาน ฉบับสมบูรณ์ (ล่าสุด 2560)','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=167'}
                    ,{'Content' : 'ข้อบังคับว่าด้วยการสงเคราะห์เกี่ยวกับการรักษาพยาบาล ฉบับสมบูรณ์ (ล่าสุด 2560)','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=166'}
                    ,{'Content' : 'ข้อบังคับว่าด้วยการพนักงาน ฉบับสมบูรณ์ (ล่าสุด 2560)','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=164'}
                    ,{'Content' : 'ข้อบังคับการพนักงาน ฉบับที่ 2 พ.ศ. 2555','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=8'}
                    ,{'Content' : 'ข้อบังคับการพนักงาน 2555','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=9'}
                    ,{'Content' : 'ข้อบังคับการพนักงาน ฉบับที่ 3 พ.ศ. 2556','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=10'}
                    ,{'Content' : 'ข้อบังคับการพนักงาน ฉบับที่ 4 พ.ศ. 2558','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=11'}
                    ,{'Content' : 'ข้อบังคับการพนักงาน ฉบับที่ 5 พ.ศ. 2558','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=12'}
                    ,{'Content' : 'สวัสดิการผู้ปฏิบัติงาน อ.ส.ค','Url' : 'https://sites.google.com/view/dpowelfare'}
                ]
            }
            ,{
                'RuleTitle' : 'การสงเคราะห์เกี่ยวกับการรักษาพยาบาล'
                ,'RuleContent' : 
                [
                    {'Content' : 'การสงเคราะห์เกี่ยวกับการรักษาพยาบาล พ.ศ. 2523','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=16'}
                    ,{'Content' : 'การสงเคราะห์เกี่ยวกับการรักษาพยาบาล ฉบับที่ 2 พ.ศ. 2531','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=17'}
                    ,{'Content' : 'การสงเคราะห์เกี่ยวกับการรักษาพยาบาล ฉบับที่ 4 พ.ศ. 2540','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=18'}
                    ,{'Content' : 'การสงเคราะห์เกี่ยวกับการรักษาพยาบาล ฉบับที่ 5 พ.ศ. 2555','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=19'}
                ]
            }
            ,{
                'RuleTitle' : 'ข้อบังคับกองทุนสำรองเลี้ยงชีพฯ'
                ,'RuleContent' : 
                [
                    {'Content' : 'ข้อบังคับกองทุนสำรองเลี้ยงชีพฯ ฉบับแก้ไขเพิ่มเติม 2558','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=30'}
                    ,{'Content' : 'ข้อบังคับกองทุนสำรองเลี้ยงชีพฯ ฉบับแก้ไขเพิ่มเติม 2552','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=29'}
                ]
            }
            ,{
                'RuleTitle' : 'ระเบียบจัดซื้อจัดจ้าง'
                ,'RuleContent' : 
                [
                    {'Content' : 'พระราชบัญญัติการจัดซื้อจัดจ้างและการบริหารพัสดุภาครัฐ','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=156'}
                ]
            }
            ,{
                'RuleTitle' : 'นโยบายด้านสารสนเทศ'
                ,'RuleContent' : 
                [
                    {'Content' : 'นโยบายและแนวปฏิบัติ การรักษาความมั่นคงปลอดภัยสารสนเทศ สิงหาคม 2561','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=209'}
                ]   
            }
            ,{
                'RuleTitle' : 'คู่มือ อ.ส.ค.'
                ,'RuleContent' : 
                [
                    {'Content' : 'การพัฒนาวัฒนธรรมองค์กรสู่ความเป็นเลิศ DPO Excellence Culture ประจำปี 2561','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=210'}
                ]   
            }
            ,{
                'RuleTitle' : 'นโยบายด้านทรัพยากรบุคคล'
                ,'RuleContent' : 
                [
                    {'Content' : 'แผนแม่บทด้านทรัพยากรบุคคลประจำปี 2560-2564 ฉบับทบทวน ปี 2560 และแผนปฏิบัติการฯ ปี 2560','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=120'}
                    ,{'Content' : 'แผนแม่บทด้านทรัพยากรบุคคล ประจำปี 2560-2564 ฉบับทบทวน ปี 2561 และ แผนปฏิบัติการฯ ปี 2561','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=198'}
                    ,{'Content' : 'แผนแม่บทด้านทรัพยากรบุคคล ประจำปี 2560-2564 ฉบับทบทวน ปี 2562 และ แผนปฏิบัติการฯ ปี 2562','Url' : 'http://dp0rtal.dpo.go.th/index.php?option=com_attachments&task=download&id=211'}
                ]   
            }
            ,{
                'RuleTitle' : 'คู่มือการบริหารความเสี่ยง'
                ,'RuleContent' : 
                [
                    {'Content' : 'คู่มือการบริหารความเสี่ยง ประจำปี พ.ศ. 2562','Url' : 'http://www.dpo.go.th/wp-content/uploads/2018/11/คู่มือการบริหารความเสี่ยง-ประจา-ปี-2562-.pdf'}
                    ,{'Content' : 'แผนบริหารรความต่อเนื่องทางธุรกิจ (Business Continuity Plan BCP ) และแผนฟื้นฟูภัยพิบัติ(Disaster Recovery Plan  DRP) พ.ศ. 2561-2562','Url' : 'http://www.dpo.go.th/wp-content/uploads/2018/11/%E0%B9%81%E0%B8%9C%E0%B8%99%E0%B8%9A%E0%B8%A3%E0%B8%AB%E0%B8%B4%E0%B8%B2%E0%B8%A3%E0%B8%84%E0%B8%A7%E0%B8%B2%E0%B8%A1%E0%B8%95%E0%B9%88%E0%B8%AD%E0%B9%80%E0%B8%99%E0%B8%B7%E0%B9%88%E0%B8%AD%E0%B8%87%E0%B8%97%E0%B8%B2%E0%B8%87%E0%B8%98%E0%B8%B8%E0%B8%A3%E0%B8%81%E0%B8%B4%E0%B8%88-Business-Continuity-Plan-BCP-%E0%B9%81%E0%B8%A5%E0%B8%B0%E0%B9%81%E0%B8%9C%E0%B8%99%E0%B8%9F%E0%B8%B7%E0%B9%89%E0%B8%99%E0%B8%9F%E0%B8%B9%E0%B8%A0%E0%B8%B1%E0%B8%A2%E0%B8%9E%E0%B8%B4%E0%B8%9A%E0%B8%B1%E0%B8%95%E0%B8%B4Disaster-Recovery-Plan-DRP2561-2562.pdf'}
                ]   
            }
        ]
    };
});

app.controller('PersonRegionController', function($cookies, $scope, $http, $uibModal, RegionFactory, HTTPFactory, IndexOverlayFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'manage_person_region';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }

    $scope.loadPersonRegionList = function(action, data){
        var params = {'Username' : data};
        HTTPFactory.clientRequest(action, params).then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.PersonRegionList = result.data.DATA.PersonRegionList;
                console.log($scope.PersonRegionList.length);
            }
        });
    }

    $scope.loadRegionList = function () {
        RegionFactory.getAllRegion().then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.RegionList = result.data.DATA;
                $scope.loadPersonRegionList('person-region/get', null);
            }
        });
    }

    $scope.search = function(condition){
        $scope.loadPersonRegionList('person-region/get', condition.Username);
    }

    $scope.setPersonRegion = function(UserID, RegionID){
        var params = {'UserID' : UserID, 'RegionID' : RegionID};
        HTTPFactory.clientRequest('person-region/update', params).then(function (result) {
            if (result.data.STATUS == 'OK') {
                console.log('Update success');
            }
        });
    }

    $scope.condition = {'Username':''};
    $scope.loadRegionList();


    IndexOverlayFactory.overlayHide();
});


app.controller('UserProfileController', function($cookies, $scope, $http, $uibModal, $routeParams, RegionFactory, PhoneBookFactory, IndexOverlayFactory) {
sessionStorage.removeItem('user_session')
    var $user_session = sessionStorage.getItem('user_session');
    // alert($user_session);
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
        $user_session = decodeURIComponent(window.atob($routeParams.user_session));
        // alert($user_session);
        sessionStorage.setItem('user_session', $user_session);
        $scope.$parent.currentUser = angular.fromJson($user_session);
        // alert($user_session);
        //
       // window.location.replace('#/logon/' + $scope.menu_selected);
    }

    $scope.loadProfile = function(UserLoginID){
        // console.log(UserLoginID);
        // return;
        IndexOverlayFactory.overlayShow();
        PhoneBookFactory.getUserContact(UserLoginID).then(function (result) {
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.Contact = result.data.DATA;

                // // get Leave Data
                // $scope.getLeaveData($scope.Contact.Email);

                // var modalInstance = $uibModal.open({
                //     animation: true,
                //     templateUrl: 'update_contact.html',
                //     size: 'md',
                //     scope: $scope,
                //     backdrop: 'static',
                //     controller: 'ModalDialogReturnFromOKBtnCtrl',
                //     resolve: {
                //         params: function () {
                //             return {};
                //         }
                //     },
                // });
                // modalInstance.result.then(function (valResult) {
                //     $scope.updateProfile(valResult);
                // });
            }else{
                alert('ไม่พบข้อมูล');
            }
        });

        
    }

    $scope.updateProfile = function (Contact){
        IndexOverlayFactory.overlayShow();
        PhoneBookFactory.updateContact(Contact).then(function (result) {
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                alert('บันทึกสำเร็จ');
                window.close();
            //     $scope.dataOffset = 0;
            //     $scope.tableLoad = false;
            //     $scope.continueLoad = true;
            //     $scope.DataList = [];
                
            //     $scope.loadList();
            }
        });
    }

    $scope.loadProfile($scope.$parent.currentUser.UserID);
});
