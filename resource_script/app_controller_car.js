app.controller('CarBookingController', function($scope, $location, $http, $filter, $uibModal, $routeParams, IndexOverlayFactory, ReserveCarFactory, RegionFactory, $routeParams) {
	//console.log(BookingRoomInfo.data.DATA);
    $scope.$parent.menu_selected = 'vehicles';
    IndexOverlayFactory.overlayShow();
    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
		$scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
	}else{
        window.location.replace('#/logon/vehicles');
	}
    IndexOverlayFactory.overlayHide();

    RegionFactory.getAllRegion().then(function (obj){
        //console.log(obj);
        IndexOverlayFactory.overlayHide();
        $scope.RegionList = obj.data.DATA;
    });

    $scope.MaxSeatAmount = 0;
    $scope.getMaxSeatAmount = function (){
        IndexOverlayFactory.overlayShow();
        ReserveCarFactory.getMaxSeatAmount().then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.MaxSeatAmount = result.data.DATA.SeatAmount;
                $scope.setSeatAmount($scope.ReserveCarInfo.DriverType);
            }
        });
    }

    $scope.setSeatAmount = function(hasDriver){
        var seatAmount = $scope.MaxSeatAmount;
        // if(hasDriver == 'Y'){
        //     seatAmount = seatAmount - 1;   
        // }
        $scope.SeatAmountList = [];

        for(var i = 0; i < seatAmount; i++){
            $scope.SeatAmountList.push({'SeatAmount':(i+1)});
        }

        if($scope.ReserveCarInfo.TravelerAmount > $scope.SeatAmountList.length){
            $scope.ReserveCarInfo.TravelerAmount = 0;
        }
    }

    $scope.setDriver = function(DriverType){
        $scope.setSeatAmount(DriverType);
    }

    $scope.getProvinceList = function (){
        IndexOverlayFactory.overlayShow();
        ReserveCarFactory.getProvinceList().then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.ProvinceList = result.data.DATA;
            }
        });
    }

    $scope.getReserveCarInfo = function(){
        IndexOverlayFactory.overlayShow();
        ReserveCarFactory.getCarReserveDetail($routeParams.reserveCarID).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS=='OK'){
                //alert(result.data.DATA.ReserveCarInfo.TravelerAmount);
                $scope.CarDetail = result.data.DATA.CarInfo;
                $scope.ReserveCarInfo = {
                    ReserveCarID : result.data.DATA.ReserveCarInfo.ReserveCarID
                    ,RegionID : parseInt(result.data.DATA.ReserveCarInfo.RegionID)
                    ,ProvinceID : parseInt(result.data.DATA.ReserveCarInfo.ProvinceID)
                    ,CarID : result.data.DATA.ReserveCarInfo.CarID
                    ,StartDateTime : result.data.DATA.ReserveCarInfo.StartDateTime
                    ,EndDateTime : result.data.DATA.ReserveCarInfo.EndDateTime
                    ,Destination : result.data.DATA.ReserveCarInfo.Destination
                    ,Mission : result.data.DATA.ReserveCarInfo.Mission
                    ,TravelerAmount : parseInt(result.data.DATA.ReserveCarInfo.TravelerAmount)//(result.data.DATA.ReserveCarInfo.TravelerAmount==0?$scope.SeatAmountList[0].SeatAmount:result.data.DATA.ReserveCarInfo.TravelerAmount)
                    ,DriverType : result.data.DATA.ReserveCarInfo.DriverType
                    ,Remark : result.data.DATA.ReserveCarInfo.Remark
                    ,ReserveStatus : result.data.DATA.ReserveCarInfo.ReserveStatus
                    ,AdminComment : result.data.DATA.ReserveCarInfo.AdminComment
                    ,AdminID : result.data.DATA.ReserveCarInfo.AdminID
                    ,CreateBy : result.data.DATA.ReserveCarInfo.CreateBy
                    ,CreateDateTime : result.data.DATA.ReserveCarInfo.CreateDateTime
                    ,UpdateBy : result.data.DATA.ReserveCarInfo.UpdateBy
                    ,UpdateDateTime : result.data.DATA.ReserveCarInfo.UpdateDateTime
                    ,RegionName : $scope.$parent.currentUser.RegionName
                    ,ProvinceName : result.data.DATA.ReserveCarInfo.ProvinceName
                };

                $scope.DriverType = $scope.ReserveCarInfo.DriverType;
                $scope.ReserveCarInfo.StartDate = convertDateToSQLString(result.data.DATA.ReserveCarInfo.StartDateTime);
                $scope.ReserveCarInfo.StartTime = convertTimeToSQLString(result.data.DATA.ReserveCarInfo.StartDateTime);
                $scope.ReserveCarInfo.EndDate = convertDateToSQLString(result.data.DATA.ReserveCarInfo.EndDateTime);
                $scope.ReserveCarInfo.EndTime = convertTimeToSQLString(result.data.DATA.ReserveCarInfo.EndDateTime);
                if(result.data.DATA.TravellerList != null){
                    $scope.TravellerList = result.data.DATA.TravellerList;
                }

                if(result.data.DATA.InternalDriver != null){
                    $scope.InternalDriver = result.data.DATA.InternalDriver;
                    $scope.InternalDriver.Name =  $scope.InternalDriver.FirstName + ' ' +  $scope.InternalDriver.LastName;
                }
                if(result.data.DATA.ExternalDriver != null){
                    $scope.ExternalDriver = result.data.DATA.ExternalDriver;
                }      
                $scope.RequestUser = result.data.DATA.RequestUser;
                $scope.VerifyUser = result.data.DATA.VerifyUser;

                $scope.oldValueTravelerAmount = $scope.ReserveCarInfo.TravelerAmount;

                // Admin setting zone
                if($scope.ReserveCarInfo.AdminID == $scope.currentUser.UserID){
                    if($scope.ReserveCarInfo.ReserveStatus == 'Request'){
                        if($scope.ReserveCarInfo.DriverType == 'Y'){
                            $scope.ReserveCarInfo.DriverType = 'Internal';
                        }
                    }
                    
                }

            }
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
        var totalTraveller = $scope.TravellerList.length;
        // if($scope.ReserveCarInfo.DriverType == 'Y'){
        //     totalTraveller += 1;
        // }
        //alert(totalTraveller + ' ' + $scope.ReserveCarInfo.TravelerAmount);return;
        if(totalTraveller < $scope.ReserveCarInfo.TravelerAmount){
            //IndexOverlayFactory.overlayShow();
            var itemSplit = $item.split(':');
            var index = parseInt(itemSplit[0].trim()) - 1;
            //console.log('index', index);
            
            // Check UserID already exist
            var offset = $filter('FindUserID')($scope.TravellerList, $autocompleteUserResult[index].UserID);
            //console.log('offset', offset, 'index' , index);
            if(offset == -1){
                // Update attendee data
                /*
                ReserveCarFactory.updateTraveller(
                {
                    'UserID' : $autocompleteUserResult[index].UserID
                    ,'ReserveCarID':$routeParams.reserveCarID
                    ,'CreateBy':$scope.$parent.currentUser.UserID
                }).then(function(result){
                    IndexOverlayFactory.overlayHide();
                    $scope.TravellerList.push({
                        'UserID' : $autocompleteUserResult[index].UserID
                        ,'ReserveCarID':$routeParams.reserveCarID
                        ,'FirstName':$autocompleteUserResult[index].FirstName
                        ,'LastName':$autocompleteUserResult[index].LastName
                    });
                });
                */
                $scope.TravellerList.push({
                    'travellerID':''
                    ,'UserID' : $autocompleteUserResult[index].UserID
                    ,'ReserveCarID':''
                    ,'FirstName':$autocompleteUserResult[index].FirstName
                    ,'LastName':$autocompleteUserResult[index].LastName
                    ,'CreateBy':$scope.currentUser.UserID
                });
                
            }else{
                IndexOverlayFactory.overlayHide();
                $scope.alertMessage = 'รายชื่อดังกล่าวได้ถูกเพิ่มไว้แล้ว';
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
            }
            
        }else{
            console.log($scope.TravellerList);
            $scope.alertMessage = 'ไม่สามารถเพิ่มผู้เดินทางได้เกินกว่าจำนวนผู้เดินทางที่เลือกไว้';
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
        }

        $scope.Traveller.Name = '';
    }

    $scope.searchDriverAutoComplete = function (val, qtype){
        val = encodeURIComponent(val);
        return $http.get(servicesUrl + "/dpo/public/autocomplete/" + qtype + "/" + val).then(function(response){
          
          $autocompleteUserResult = response.data.data.DATA;
          var loop = $autocompleteUserResult.length;
          //console.log($autocompleteUserResult);
          if(loop > 0){
              var objList = [];
              for(var i = 0; i < loop; i++){
                objList.push($autocompleteUserResult[i].FirstName + ' ' + $autocompleteUserResult[i].LastName);
              }
              return objList;
          }else{
            return null;
          }
          
        });
    };

    $scope.autocompleteDriverSelected = function ($item, $model, $label){
        var nameObj = $item.split(' ');
        var offset = $filter('FindUserByName')($autocompleteUserResult, nameObj[0], nameObj[1]);
        console.log('offset', offset);
        if(offset != -1){
            $scope.InternalDriver.UserID = $autocompleteUserResult[offset].UserID;
            $scope.InternalDriver.UserID = $autocompleteUserResult[offset].UserID;
        }
    }

    $scope.saveDraft = function (){
        IndexOverlayFactory.overlayShow();
        
        $scope.ReserveCarInfo.StartDateTime = concatDateTimeSQL($scope.ReserveCarInfo.StartDate, $scope.ReserveCarInfo.StartTime);
        $scope.ReserveCarInfo.EndDateTime = concatDateTimeSQL($scope.ReserveCarInfo.EndDate, $scope.ReserveCarInfo.EndTime);
        

        ReserveCarFactory.updateReserveCarInfo($scope.ReserveCarInfo
                                                ,$scope.TravellerList
                                                ,$routeParams.reserveCarID).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS=='OK'){
                //console.log(result);
                if(parseInt(result.data.DATA.ReserveCarID) > 0 && $routeParams.reserveCarID == '-1'){
                    window.location.replace('#/vehicles/' + result.data.DATA.ReserveCarID);
                }
            }else{
                alert(result.data.DATA.MSG);
            }            
            
            $scope.addAlert('บันทึกสำเร็จ','success');
        });
    }

    $scope.deleteTraveller = function (index, travellerID){
        //console.log(userID, reserveRoomID);
        //IndexOverlayFactory.overlayShow();
        $scope.TravellerList.splice(index, 1);
        // ReserveCarFactory.deleteTraveller(travellerID).then(function(result){
        //     IndexOverlayFactory.overlayHide();
        //     if(result.data.DATA == 1){
        //         $scope.TravellerList.splice(index, 1);
        //         console.log($scope.TravellerList);
        //         $scope.addAlert('ลบผู้เดินทางแล้ว','success');
        //     }
        // });
    }

    $scope.cancelReserveCar = function (reserveCarID){
        $scope.alertMessage = 'ต้องการยกเลิกการจองพาหนะ ใช่หรือไม่ ?';
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
            ReserveCarFactory.cancelReserveCar(reserveCarID).then(function(result){
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

    $scope.requestReserveCar = function (){

        if(parseInt($scope.ReserveCarInfo.TravelerAmount) > $scope.TravellerList.length){
            alert('จำนวนผู้เดินทางมากกว่ารายชื่อที่ถูกเพิ่มไว้');
            return false;
        }

        $scope.alertMessage = 'ต้องการส่งคำขอการจองพาหนะ ใช่หรือไม่ ?';
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
            
            $scope.ReserveCarInfo.StartDateTime = concatDateTimeSQL($scope.ReserveCarInfo.StartDate, $scope.ReserveCarInfo.StartTime);
            $scope.ReserveCarInfo.EndDateTime = concatDateTimeSQL($scope.ReserveCarInfo.EndDate, $scope.ReserveCarInfo.EndTime);
        
         //console.log($scope.ReserveCarInfo);return;
            ReserveCarFactory.requestReserveCar($scope.ReserveCarInfo
                                                ,$scope.TravellerList
                                                ,$scope.RequestUser).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS == 'OK'){
                    $scope.ReserveCarInfo.ReserveStatus = result.data.DATA;
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
                    $scope.alertMessage = result.data.DATA;
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
                }
            });
        }, function () {});
            
    }

    $scope.checkSelectDateTime = function (startDate, startTime, endDate, endTime){
        //console.log(startDate, startTime, endDate, endTime);
        if(startDate != null 
                && endDate != null 
                && (startTime != undefined && startTime != '') 
                && (endTime != undefined && endTime != '')){
            var timeArr = startTime.split(':');
            startDate.setHours(timeArr[0]);
            startDate.setMinutes(timeArr[1]);
            startDate.setSeconds(0);

            timeArr = endTime.split(':');
            endDate.setHours(timeArr[0]);
            endDate.setMinutes(timeArr[1]);
            endDate.setSeconds(0);

            //console.log(startDate > endDate);
            if(startDate > endDate){
                //alert('วันที่เดินทางไปน้อยกว่าวันที่เดินทางกลับ');
                $scope.alertMessage = 'วันที่เดินทางไปน้อยกว่าวันที่เดินทางกลับ';
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
                    $scope.ReserveCarInfo.EndDate = null;
                    $scope.ReserveCarInfo.EndTime = '';
                }, function () {});
                
            }
        }
    }

    $scope.checkReserveStatus = function (type){
        if(type == 'Reject'){
            $scope.ExternalDriver.DriverName = '';
            $scope.ExternalDriver.Mobile = '';
            $scope.InternalDriver.Name = '';
        }else{
            $scope.ReserveCarInfo.AdminComment = '';
        }
    }

    $scope.checkDriverType = function (type){
        if(type == 'Internal'){
            $scope.ExternalDriver.DriverName = '';
            $scope.ExternalDriver.Mobile = '';
        }else{
            $scope.InternalDriver.Name = '';
        }
    }

    $scope.checkTraveller = function (){
        var totalTraveller = $scope.TravellerList.length;
        // if($scope.ReserveCarInfo.DriverType == 'Y'){
        //     totalTraveller += 1;
        // }
        if($scope.ReserveCarInfo.TravelerAmount < totalTraveller){
            //alert('วันที่เดินทางไปน้อยกว่าวันที่เดินทางกลับ');
            $scope.alertMessage = 'จำนวนผู้เดินทางที่เลือกน้อยกว่าจำนวนผู้เดินทางที่เพิ่มไว้';
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
                $scope.ReserveCarInfo.TravelerAmount = $scope.oldValueTravelerAmount;   
                }, function () {});
            
        }else{
            $scope.oldValueTravelerAmount = $scope.ReserveCarInfo.TravelerAmount;
        }
    }

    $scope.checkInternalDriverCondition = function (){
        return ($scope.ReserveCarInfo.DriverType == 'Internal' && ($scope.InternalDriver.Name == '' || $scope.InternalDriver.Name == null));
    }

    $scope.adminUpdateCarStatus = function (){
        $scope.alertMessage = 'ต้องการ '+($scope.ReserveCarInfo.ReserveStatus=='Approve'?'อนุมัติ':'ไม่อนุมัติ')+' การจองพาหนะนี้ ใช่หรือไม่ ?';
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
            ReserveCarFactory.adminUpdateCarStatus($scope.ReserveCarInfo
                                                , $scope.TravellerList
                                                ,$scope.InternalDriver
                                                ,$scope.ExternalDriver).then(function(result){
                IndexOverlayFactory.overlayHide();
                if(result.data.STATUS=='OK'){
                    
                    //$scope.addAlert('บันทึกสำเร็จ','success');
                    //window.location.replace('#/vehicles/' + $routeParams.reserveCarID);
                    window.location.reload();
                }else{
                    alert(result.data.DATA.MSG);
                    $scope.addAlert('เกิดข้อผิดพลาดขณะทำงาน','danger');
                }
            });
        });
    }

    
    $scope.showSelectCar = function(){

        $scope.dateRange = [];
        $scope.dateRangeDisplay = [];
        $scope.dateSelected = $scope.ReserveCarInfo.StartDate.getFullYear() 
                            + '-' + ($scope.ReserveCarInfo.StartDate.getMonth() < 10?'0'+($scope.ReserveCarInfo.StartDate.getMonth() + 1):$scope.ReserveCarInfo.StartDate.getMonth() + 1) 
                            + '-' + $scope.ReserveCarInfo.StartDate.getDate();
        //console.log($scope.dateSelected);
        // Generate Date Range
        $scope.setDateRange = function(index){
            //console.log('$scope.regionSelected', $scope.regionSelected);
            if($scope.regionSelected == ''){
                $scope.regionSelected = $scope.RegionList[0].RegionID;
                //console.log('$scope.regionSelected', $scope.regionSelected);
            }
            $scope.dateSelected = $scope.dateRange[index];
            $scope.generateDateRange($scope.dateRange[index]);
            $scope.getCarsInRegion($scope.ReserveCarInfo.RegionID, $scope.dateSelected);
            //$scope.loadReserveList($scope.regionSelected);
        }
        
        $scope.generateDateRange = function(selectedDate){
            
            $scope.dateRange = [];
            $scope.dateRangeDisplay = [];
            //$scope.SelectedCarDetail = {Brand:'ยังไม่ได้เลือก'};
            
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

        // Generate Reserve List                     
        $scope.getStatus = function(currentTime, ReserveList){
            var curDateTime = makeDate($scope.dateSelected + ' ' + currentTime);
            //console.log($scope.dateSelected + ' ' + currentTime);
            for(var i=0; i < ReserveList.length; i++){
                
                var reserveStartDate = makeDate(ReserveList[i].StartDateTime);
                var reserveEndDate = makeDate(ReserveList[i].EndDateTime);
                
                if((reserveStartDate.getTime() == curDateTime.getTime()) || (reserveEndDate.getTime() > curDateTime.getTime() && curDateTime.getTime() > reserveStartDate.getTime())){
                    
                    return 'reserve_unvailable';
                
                }
            }
            
        }

        $scope.setDraftSelectedCar = function(car){
            $scope.alertMessage = 'ต้องการเลือกรถ '+ car.Brand + ' ' + car.Model + ' ทะเบียน ' + car.License + ' ' + car.LicenseProvinceName +' ใช่หรือไม่ ?';
            var modalInstance = $uibModal.open({
                animation : false,
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
                $scope.SelectedCarDetail = car;
            });
        }
        
        $scope.getCarsInRegion = function(regionID, findDate){
            IndexOverlayFactory.overlayShow();
            ReserveCarFactory.getCarsInRegion(regionID,findDate).then(function(result){
                if(result.data.STATUS == 'OK'){
                    IndexOverlayFactory.overlayHide();
                    $scope.CarList = result.data.DATA;
                    console.log($scope.CarList);
                    // return
                    var ownerID = $scope.currentUser.UserID;
                    for(var i = 0; i < $scope.CarList.length; i++){
                        var timeList = [
                            {time:'00:00:00.000',status:''}
                            ,{time:'01:00:00.000',status:''}
                            ,{time:'02:00:00.000',status:''}
                            ,{time:'03:00:00.000',status:''}
                            ,{time:'04:00:00.000',status:''}
                            ,{time:'05:00:00.000',status:''}
                            ,{time:'06:00:00.000',status:''}
                            ,{time:'07:00:00.000',status:''}
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
                            ,{time:'20:00:00.000',status:''}
                            ,{time:'21:00:00.000',status:''}
                            ,{time:'22:00:00.000',status:''}
                            ,{time:'23:00:00.000',status:''}
                            ];
                        var ReserveList = $scope.CarList[i].reserve_cars;
                        for(var j = 0; j < ReserveList.length; j++){  
                            var reserveStartDate = makeDate(ReserveList[j].StartDateTime);
                            var reserveEndDate = makeDate(ReserveList[j].EndDateTime);
                            var cntTimeIndex = 0;

                            for(var k = 0; k <= 23; k++){
                                
                                var hour = k + ':00:00.000';
                                if(k < 10){
                                    hour = '0' + hour;
                                }
                                
                                var curDateTime = makeDate($scope.dateSelected + ' ' + hour);
                                var statusTxt = '';
                                if((reserveStartDate.getTime() == curDateTime.getTime()) || (reserveEndDate.getTime() > curDateTime.getTime() && curDateTime.getTime() > reserveStartDate.getTime())){
                                    if(ownerID == ReserveList[j].CreateBy && ReserveList[j].ReserveStatus == 'Approve'){
                                        console.log('Approve');
                                        timeList[cntTimeIndex].status = 'reserve_approved';
                                    }else if(ownerID == ReserveList[j].CreateBy && (ReserveList[j].ReserveStatus == 'Request' || ReserveList[j].ReserveStatus == '')){
                                        timeList[cntTimeIndex].status = 'reserve_waiting';
                                    }else if(ownerID != ReserveList[j].CreateBy  || ReserveList[j].ReserveStatus == 'Reject'){
                                        // console.log(cntTimeIndex+ ' reserve_unvailable', ownerID +'!='+ ReserveList[i].CreateBy);
                                        timeList[cntTimeIndex].status = 'reserve_unvailable';
                                    }
                                    
                                }
                                cntTimeIndex++;
                                //timeList.push({time : hour,status : statusTxt});
                            }

                            /*  
                            
                            */
                        }
                        $scope.CarList[i].timeList = timeList;
                        //console.log($scope.CarList[i]);
                    }
                    console.log($scope.CarList);
                }
            });
        }

        $scope.getCarsInRegion($scope.ReserveCarInfo.RegionID, $scope.dateSelected);
        
        ReserveCarFactory.getReserveCartypeList().then(function (result) {
            if (result.data.STATUS == 'OK') {
                
                $scope.Cartype = result.data.DATA;

                // Set default date range
                $scope.generateDateRange($scope.dateSelected);

                $scope.FromDate = convertDateToFullThaiDateIgnoreTime($scope.ReserveCarInfo.StartDate) + ' ' + $scope.ReserveCarInfo.StartTime;
                $scope.ToDate = convertDateToFullThaiDateIgnoreTime($scope.ReserveCarInfo.EndDate) + ' ' + $scope.ReserveCarInfo.EndTime;
                $scope.TotalTravellers = $scope.TravellerList.length;
                if($scope.DriverType == 'Y'){
                    $scope.TotalTravellers += 1;
                }
                var modalInstance = $uibModal.open({
                    animation : true,
                    templateUrl : 'choose_car.html',
                    size : 'lg',
                    scope : $scope,
                    backdrop : 'static',
                    controller : 'ModalDialogReturnFromOKBtnCtrl',
                    windowClass: 'app-modal-window',
                    resolve : {
                        params : function() {
                            return {};
                        } 
                    },
                });
                modalInstance.result.then(function (valResult) {
                    $scope.CarDetail = valResult;
                    $scope.ReserveCarInfo.CarID = $scope.CarDetail.CarID;
                });
            }
        });

    }

    $scope.printReserveCar = function(){

        var data1 = formatted_string(' ', $scope.RequestUser.FirstName + ' ' + $scope.RequestUser.LastName, '', 55 , '');
        var data2 = formatted_string(' ', $scope.RequestUser.PositionName, '', 55 , '');
        var data3 = formatted_string(' ', $scope.RequestUser.OrgName, '', 54 , '');
        var data4 = formatted_string(' ', $scope.RequestUser.UpperOrgName, '', 54 , '');
        var data5 = formatted_string(' ', $scope.ReserveCarInfo.Destination, '', 80 , '');
        var data6 = formatted_string(' ', $scope.ReserveCarInfo.ProvinceName, '', 55 , '');

        var startDate = convertDateToReportDate($scope.ReserveCarInfo.StartDate);
        var data7 = formatted_string(' ', startDate[0], '', 20 , '');
        var data8 = formatted_string(' ', startDate[1], '', 35 , '');
        var data9 = formatted_string(' ', startDate[2], '', 20 , '');
        var data10 = formatted_string(' ', $scope.ReserveCarInfo.StartTime, '', 15 , '');
        
        var endDate = convertDateToReportDate($scope.ReserveCarInfo.EndDate);
        var data11 = formatted_string(' ', endDate[0], '', 20 , '');
        var data12 = formatted_string(' ', endDate[1], '', 35 , '');
        var data13 = formatted_string(' ', endDate[2], '', 20 , '');

        var data14 = $scope.TravellerList.length;
        if($scope.ReserveCarInfo.DriverType == 'Y'){
            data14 += 1;
        }
        data14 = formatted_string(' ', data14, '', 25 , '');

        var data15 = formatted_string(' ', $scope.RequestUser.FirstName + ' ' + $scope.RequestUser.LastName, '', 46 , '');

        var Remark = $scope.ReserveCarInfo.Remark == ''?'-':$scope.ReserveCarInfo.Remark;
        
        var data16length = Remark.length < 145?145:(145*2);
        var data16 = formatted_string(' ', Remark, '', data16length);

        var AdminComment = $scope.ReserveCarInfo.AdminComment == ''?'-':$scope.ReserveCarInfo.AdminComment;
        var data17length = AdminComment.length < 70?70:(70*2);
        var data17 = formatted_string(' ', AdminComment, '', data17length);

        var data18 = formatted_string(' ', $scope.VerifyUser.FirstName + ' ' + $scope.VerifyUser.LastName, '', 46 , '');
        var data19 = formatted_string(' ', $scope.CarDetail.CarType, '', 46 , '');

        var printDate = convertDateToReportDate(new Date());
        pdfMake.fonts = {
            SriSuriwongse: {
                
                normal: 'SRISURYWONGSE.ttf'
                ,bold: 'SRISURYWONGSE-Bold.ttf'
            }
        };

        var pdfForm = {
            content: [
                {text: 'แบบขอใช้ยานพาหนะ อ.ส.ค.', style: 'header', alignment:'center'},
                {text: ' '},
                {text: [
                        {text : ' วันที่ '}
                        ,{text : '    ' + printDate[0] + '    ', decoration : 'underline', decorationStyle: 'dashed'}
                        ,{text : ' เดือน '}
                        ,{text : '    ' + printDate[1] + '    ', decoration : 'underline', decorationStyle: 'dashed'}
                        ,{text : ' พ.ศ. '}
                        ,{text : '    ' + printDate[2] + '    ', decoration : 'underline', decorationStyle: 'dashed'}
                        ]
                        ,alignment:'right'
                },
                {text: 'เรื่อง ขอใช้ยานพาหนะ อ.ส.ค.',margin: [0,10,0,0]},
                {text: 'เสนอ แผนกยานพาหนะ ฝ่ายจัดซื้อและบริการ',margin: [0,10,0,0]},
                {text: [{text : ' ด้วย ข้าพเจ้า '} 
                        ,{text : data1, decoration : 'underline', decorationStyle: 'dashed'}
                        ,{text : 'ตำแหน่ง '}
                        ,{text : data2, decoration : 'underline', decorationStyle: 'dashed'}
                        ], margin: [60,10,0,0]},
                {text : [{text : 'แผนก / ศูนย์ '}
                        ,{text : data3, decoration : 'underline', decorationStyle: 'dashed'}
                        ,{text : ' ฝ่าย / สำนัก / สำนักงาน / สถาบัน '}
                        ,{text : data4, decoration : 'underline', decorationStyle: 'dashed'}
                        ],margin: [0,10,0,0]
                },
                {text : [{text:'มีความประสงค์จะขอใช้ยานพาหนะ อ.ส.ค. ประเภท '}
                    ,{text:data19, decoration : 'underline', decorationStyle: 'dashed'}
                    ],margin: [0,10,0,0]},
                
                {text: [
                    {text:'เพื่อจะเดินทางไป '},
                    {text:data5, decoration : 'underline', decorationStyle: 'dashed'},
                    {text:' จังหวัด '},
                    {text:data6, decoration : 'underline', decorationStyle: 'dashed'},
                ],margin: [0,10,0,0]},
                {text: [
                    {text:'โดยจะออกเดินทางในวันที่ '},
                    {text:data7, decoration : 'underline', decorationStyle: 'dashed'},
                    {text:' เดือน '},
                    {text:data8, decoration : 'underline', decorationStyle: 'dashed'},
                    {text:' พ.ศ. '},
                    {text:data9, decoration : 'underline', decorationStyle: 'dashed'},
                    {text:' เวลา '},
                    {text:data10, decoration : 'underline', decorationStyle: 'dashed'},
                    {text:' น.'},
                ],margin: [0,10,0,0]},
                {text: [
                    {text:'เดินทางกลับ ในวันที่ '},
                    {text:data11, decoration : 'underline', decorationStyle: 'dashed'},
                    {text:' เดือน '},
                    {text:data12, decoration : 'underline', decorationStyle: 'dashed'},
                    {text:' พ.ศ. '},
                    {text:data13, decoration : 'underline', decorationStyle: 'dashed'},
                    {text:'และมีผู้ร่วมเดินทางไปด้วย'}
                ],margin: [0,10,0,0]},
                {text: [
                    {text:'จำนวน '},
                    {text:data14, decoration : 'underline', decorationStyle: 'dashed'},
                    {text:' คน'},
                ],margin: [0,10,0,0]},
                {text: [{text : ' หมายเหตุ ',bold: true}
                    ,{text : data16,decoration : 'underline', decorationStyle: 'dashed'}],margin: [0,10,0,0]},
                {text: [
                        {text : ' (ลงชื่อ) '}
                        ,{text : '                                                       ', decoration : 'underline', decorationStyle: 'dashed'}
                        ,{text : ' ผู้ขอใช้บริการ '}
                    ]
                    ,margin: [220,30,0,0]
                },
                {text: [
                        {text : ' ( '}
                        ,{text : data15, decoration : 'underline', decorationStyle: 'dashed'}
                        ,{text : ' )'}
                    ]
                    ,margin: [248,10,0,0]
                },
                {text: [
                        {text : ' (ลงชื่อ) '}
                        ,{text : '                                                       ', decoration : 'underline', decorationStyle: 'dashed'}
                        ,{text : ' ผู้ควบคุมการให้บริการ '}
                    ]
                    ,margin: [220,20,0,0]
                },
                {text: [
                        {text : ' ( '}
                        ,{text : data18, decoration : 'underline', decorationStyle: 'dashed',preserveLeadingSpaces: true}
                        ,{text : ' )'}
                    ]
                    ,margin: [248,10,0,0]
                }
                /*,
                {
                    style: 'tableExample',
                    table: {
                        widths: [270,230],
                        body: [
                            [{text : ' ความเห็นของแผนกยานพาหนะ '
                                ,decoration : 'underline'
                                ,bold: true
                                ,margin: [0,10,0,0]
                                }
                                , ''],
                            [{text : data17
                                //, decoration : 'underline', decorationStyle: 'dashed'
                            }
                            ,{
                                style: 'tableExample',
                                table: {
                                    widths: [20,100,20,100],
                                    body: [
                                            [{image: ($scope.ReserveCarInfo.ReserveStatus=='Approve'?'checkedbox.png':'uncheckbox.jpg'),width: 20, height: 20}
                                            , 'อนุมัติ'
                                            ,{image: ($scope.ReserveCarInfo.ReserveStatus=='Reject'?'checkedbox.png':'uncheckbox.jpg'),width: 16, height: 16}
                                            ,'ไม่อนุมัติ']
                                        ]
                                },layout: 'noBorders',margin: [0,10,0,0]
                            }]
                        ]
                    },layout: 'noBorders',margin: [0,10,0,0]
                },
                {text: [
                        {text : ' (ลงชื่อ) '}
                        ,{text : '                                                       ', decoration : 'underline', decorationStyle: 'dashed'}
                        ,{text : ' ผู้ควบคุมการให้บริการ '}
                    ]
                    ,margin: [10,20,0,0]
                },
                {text: [
                        {text : ' ( '}
                        ,{text : data18, decoration : 'underline', decorationStyle: 'dashed',preserveLeadingSpaces: true}
                        ,{text : ' )'}
                    ]
                    ,margin: [35,10,0,0]
                }
                */
            ],
            styles: {
                header: {
                    bold: true,
                    fontSize: 24
                }
            },
            defaultStyle: {
                fontSize: 14,
                font:'SriSuriwongse'
            }
        };

        //pdfMake.createPdf(pdfForm).download($scope.RequestUser.FirstName + '_Reserve_Car.pdf');
        pdfMake.createPdf(pdfForm).open();
        
        function formatted_string(pad, user_str, pad_pos, pad_length, alignment)
        {
          if (typeof user_str === 'undefined') 
            return pad;
           if(alignment == 'center'){
                var avialableSpace = pad_length - user_str.length;
                console.log(user_str.length, pad_length);
                var pading = Math.ceil( avialableSpace / 2);
                console.log(pading);
                var string_left = (pad + user_str).slice(-pading);
                var string_right = '';
                for(var i = 0; i < pading; i++){
                    string_right += pad;
                }
                console.log(string_left + string_right);
                return string_left + string_right;
           } else{
              if (pad_pos == 'l')
                 {
                 return (pad + user_str).slice(-pad_length);
                 }
              else 
                {
                    
                    var str_pad = '';
                    for(var i = 0; i < pad_length; i++){
                        str_pad += pad;
                    }
                    //console.log(user_str + str_pad);
                return (user_str + str_pad).substring(0, pad_length);
                }
            }
        }
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
        $scope.popup2.opened = true;
    };

    $scope.open2 = function() {
        $scope.dateOptions2.minDate = $scope.ReserveCarInfo.StartDate==null?new Date():$scope.ReserveCarInfo.StartDate;
        $scope.popup2.opened = true;
    };

    $scope.checkBetweenDate = function(){
        if($scope.ReserveCarInfo.StartDate > $scope.ReserveCarInfo.EndDate){
            $scope.ReserveCarInfo.EndDate = null;
        }
    }

    $scope.CarTimeList = ['00:00:00','01:00:00','02:00:00','03:00:00','04:00:00','05:00:00'
                        ,'06:00:00','07:00:00','08:00:00','09:00:00','10:00:00','11:00:00','12:00:00'
                        ,'13:00:00','14:00:00','15:00:00','16:00:00','17:00:00','18:00:00'
                        ,'19:00:00','20:00:00','21:00:00','22:00:00','23:00:00']; 
    $scope.StartTimeList = ['00:00','01:00','02:00','03:00','04:00','05:00'
                        ,'06:00','07:00','08:00','09:00','10:00','11:00','12:00'
                        ,'13:00','14:00','15:00','16:00','17:00','18:00'
                        ,'19:00','20:00','21:00','22:00','23:00'];  
    $scope.EndTimeList = ['00:00','01:00','02:00','03:00','04:00','05:00'
                        ,'06:00','07:00','08:00','09:00','10:00','11:00','12:00'
                        ,'13:00','14:00','15:00','16:00','17:00','18:00'
                        ,'19:00','20:00','21:00','22:00','23:00'];  
    
    $scope.ReserveCarInfo = {
                        ReserveCarID : ''
                        ,RegionID : $scope.$parent.currentUser.RegionID
                        ,ProvinceID : ''
                        ,CarID : ''
                        ,StartDateTime : ''
                        ,EndDateTime : ''
                        ,Destination : ''
                        ,Mission : ''
                        ,TravelerAmount : 0
                        ,DriverType : 'Y'
                        ,Remark : ''
                        ,ReserveStatus : ''
                        ,AdminComment : ''
                        ,AdminID : ''
                        ,CreateBy : $scope.$parent.currentUser.UserID
                        ,CreateDateTime : ''
                        ,UpdateBy : $scope.$parent.currentUser.UserID
                        ,UpdateDateTime : ''
                        ,RegionName : $scope.$parent.currentUser.RegionName
    };

    $scope.Traveller = [];
    $scope.ProvinceList = [];
    $scope.SeatAmountList = [];
    $scope.TravellerList = [];
    $scope.InternalDriver = {'UserID' : '', 'Name':''};
    $scope.ExternalDriver = {'DriverName' : '', 'Mobile':''};
    $scope.oldValueTravelerAmount = $scope.ReserveCarInfo.TravelerAmount;

    if($routeParams.reserveCarID != '-1'){
        $scope.getReserveCarInfo();
    }

    $scope.getMaxSeatAmount();
    $scope.getProvinceList();

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

app.controller('CarController', function ($cookies, $scope, $http, $uibModal, IndexOverlayFactory, CarFactory,CartypeFactory, RegionFactory,ProvinceFactory, Upload) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'manage_car';

    var $user_session = sessionStorage.getItem('user_session');

    if ($user_session != null) {
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');

    } else {
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
     $scope.loadProvinceList = function () {
        ProvinceFactory.getAllProvince().then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.ProvinceList = result.data.DATA;
                
            }
        });
    }
     $scope.loadCartypeList = function () {
        CartypeFactory.getList().then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.Cartype = result.data.DATA.DataList;
               
            }
        });
    }

    $scope.loadList = function () {
        if ($scope.continueLoad && !$scope.tableLoad) {
            $scope.tableLoad = true;
            CarFactory.getList($scope.dataOffset, $scope.currentUser.UserID).then(function (result) {
                if (result.data.STATUS == 'OK') {
                    $scope.tableLoad = false;
                    $scope.dataOffset = result.data.DATA.offset;
                    $scope.continueLoad = result.data.DATA.continueLoad;
                    for (var i = 0; i < result.data.DATA.DataList.length; i++) {
                        $scope.DataList.push(result.data.DATA.DataList[i]);
                    }
                }
            });
        }
    }

    $scope.confirmDelete = function (index, ID) {
        $scope.alertMessage = 'ต้องการลบอุปกรณ์นี้ ใช่หรือไม่ ?';
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'html/dialog_confirm.html',
            size: 'sm',
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
            IndexOverlayFactory.overlayShow();
            CarFactory.deleteData(ID).then(function (result) {
                IndexOverlayFactory.overlayHide();
                if (result.data.STATUS == 'OK' && result.data.DATA) {
                    $scope.DataList.splice(index, 1);
                }
            });
        });
    }

    $scope.showUpdatePage = function (index, obj) {
        $scope.setDefaultModelValue();
        console.log(index, obj);
        if (index != -1 && obj != null) {
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
                url: servicesUrl + '/dpo/public/updateCarData/',
                data: {'updateObj': $scope.Car,'fileimg': fileimg}
            }).then(function (resp) {
                IndexOverlayFactory.overlayHide();
                //console.log('Success ' + resp.config.data.fileimg.name + 'uploaded. Response: ' + resp.data);
              //  console.log(resp.data.data.DATA);
               
                //$scope.DataList[index] = resp.data.data.DATA;
                if(resp.data.data.DATA=='a'){
                    alert('หมายเลขทะเบียนนี้ซ้ำ');
                     console.log('if');
                }else{
                    console.log('else');
                    $scope.showListPage();
                $scope.dataOffset = 0;
                $scope.tableLoad = false;
                $scope.continueLoad = true;
                $scope.DataList = [];

                $scope.loadList();
                }
                
            }, function (resp) {
             
            }, function (evt) {
               
            });
        };

    }

    $scope.showListPage = function () {
        $scope.showPage = 'MAIN';
        $scope.setDefaultModelValue();
    }

    $scope.setDefaultModelValue = function () {

        $scope.Car = {'CarID': ''
            , 'RegionID': parseInt($scope.currentUser.RegionID)
            , 'CarTypeID': ""
            , 'Brand': ""
            , 'Model': ""
            , 'License': ""
            , 'LicenceProvince': ""
            , 'Description': ""
            , 'CarPicture': ""
            , 'ActiveStatus': "Y"
            , 'UpdateBy': ""
            , 'UpdateDateTime': ""
            , 'CreateBy': $scope.currentUser.UserID
            , 'CreateDateTime': ""
            , 'car_admin_Mobile': $scope.currentUser.Mobile
            , 'car_admin_email': $scope.currentUser.Email
            , 'car_admin_name':$scope.currentUser.FirstName + ' ' + $scope.currentUser.LastName
            , 'CarAdminID':$scope.currentUser.UserID

        }
        $scope.fileimg = null;
    }

    $scope.setUpdateModelValue = function (obj) {
        $scope.Car = {'CarID': obj.CarID
            , 'RegionID': parseInt(obj.RegionID)
            , 'CarTypeID': parseInt(obj.CarTypeID)
            , 'Brand': obj.Brand
            , 'Model': obj.Model
            , 'License': obj.License
            , 'LicenceProvince': parseInt(obj.LicenceProvince)
            , 'Description': obj.Description
            , 'CarPicture': obj.CarPicture
            , 'ActiveStatus': obj.ActiveStatus
            , 'UpdateBy': $scope.currentUser.UserID
            , 'UpdateDateTime': obj.UpdateDateTime
            , 'CreateBy': obj.CreateBy
            , 'CreateDateTime': obj.CreateDateTime
            , 'car_admin_Mobile': obj.car_admin_Mobile
            , 'car_admin_email': obj.car_admin_email
            , 'car_admin_name': obj.car_admin_firstname + ' ' + obj.car_admin_lastname
             , 'CarAdminID':obj.CarAdminID
        }
        $scope.fileimg = null;
        $scope.car_admin_name = obj.car_admin_firstname + ' ' + obj.car_admin_lastname
    }

    // Variables
    $scope.RegionList = [];
    $scope.showPage = '';
    $scope.dataOffset = 0;
    $scope.tableLoad = false;
    $scope.continueLoad = true;
    $scope.DataList = [];
     $scope.ProvinceList = [];
    

    // Initial page
    $scope.loadProvinceList();
    $scope.showListPage();
    $scope.loadList();
    $scope.loadCartypeList();
    // $scope.loadRegionList();

    RegionFactory.getAllRegion().then(function (obj) {
        //console.log(obj);
        IndexOverlayFactory.overlayHide();
        $scope.RegionList = obj.data.DATA;
    });

   $autocompleteUserResult = [];
    $scope.searchUserAutoComplete = function (val, qtype) {
        val = encodeURIComponent(val);
        return $http.get(servicesUrl + "/dpo/public/autocomplete/" + qtype + "/" + val).then(function (response) {

            $autocompleteUserResult = response.data.data.DATA;
            var loop = $autocompleteUserResult.length;
            //console.log($autocompleteUserResult);
            if (loop > 0) {
                var objList = [];
                for (var i = 0; i < loop; i++) {
                    objList.push((i + 1) + ' : ' + $autocompleteUserResult[i].FirstName + ' ' + $autocompleteUserResult[i].LastName);
                }
                return objList;
            } else {
                return null;
            }

        });
    };
    $scope.autocompleteUserSelected = function ($item, $model, $label, $type) {

        //IndexOverlayFactory.overlayShow();
        var itemSplit = $item.split(':');
        var index = parseInt(itemSplit[0].trim()) - 1;
        //console.log('index', index);

        // Check UserID already exist
        //var offset = $filter('FindUserID')($scope.InternalAttendeeList, $autocompleteUserResult[index].UserID);
        //console.log('offset', offset, 'index' , index);
            $scope.car_admin_name = $autocompleteUserResult[index].FirstName + ' ' + $autocompleteUserResult[index].LastName;
            $scope.Car.CarAdminID = $autocompleteUserResult[index].UserID;
            $scope.Car.car_admin_name = $autocompleteUserResult[index].FirstName + ' ' + $autocompleteUserResult[index].LastName;
            $scope.Car.car_admin_email = $autocompleteUserResult[index].Email;
            $scope.Car.car_admin_Mobile = $autocompleteUserResult[index].Mobile;
         
    }

    $scope.checkEmptyField = function ($type, val) {
        
        if(val == null || (val != $scope.car_admin_name && $scope.Car.CarAdminID != '')){
            $scope.Car.CarAdminID = '';
            $scope.Car.car_admin_name = '';
            $scope.Car.car_admin_email = '';
            $scope.Car.car_admin_Mobile = '';
        }
        
    }


});
app.controller('CartypeController', function ($cookies, $scope, $http, $uibModal, IndexOverlayFactory, CartypeFactory, RegionFactory, Upload) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'manage_cartype';

    var $user_session = sessionStorage.getItem('user_session');

    if ($user_session != null) {
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');

    } else {
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

    $scope.loadList = function () {
        if ($scope.continueLoad && !$scope.tableLoad) {
            $scope.tableLoad = true;
            CartypeFactory.getManageList($scope.dataOffset, $scope.currentUser.UserID).then(function (result) {
                if (result.data.STATUS == 'OK') {
                    $scope.tableLoad = false;
                    $scope.dataOffset = result.data.DATA.offset;
                    $scope.continueLoad = result.data.DATA.continueLoad;
                    for (var i = 0; i < result.data.DATA.DataList.length; i++) {
                        $scope.DataList.push(result.data.DATA.DataList[i]);
                    }
                }
            });
        }
    }

    $scope.confirmDelete = function (index, ID) {
        $scope.alertMessage = 'ต้องการลบอุปกรณ์นี้ ใช่หรือไม่ ?';
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'html/dialog_confirm.html',
            size: 'sm',
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
            IndexOverlayFactory.overlayShow();
            CartypeFactory.deleteData(ID).then(function (result) {
                IndexOverlayFactory.overlayHide();
                if (result.data.STATUS == 'OK' && result.data.DATA) {
                    $scope.DataList.splice(index, 1);
                }
            });
        });
    }

    $scope.showUpdatePage = function (index, obj) {
        $scope.setDefaultModelValue();
        if (index != -1 && obj != null) {
            $scope.setUpdateModelValue(obj);
        }
        $scope.showPage = 'UPDATE';

        // Update Zone
        $scope.upload = function (fileimg) {
            IndexOverlayFactory.overlayShow();
            Upload.upload({
                url: servicesUrl + '/dpo/public/updateCartypeData/',
                data: {'updateObj': $scope.Cartype}
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

    $scope.showListPage = function () {
        $scope.showPage = 'MAIN';
        $scope.setDefaultModelValue();
    }

    $scope.setDefaultModelValue = function () {

        $scope.Cartype = {'CarTypeID': ''
             , 'CarType':""
            , 'SeatAmount': ""
            , 'ActiveStatus': "Y"
            , 'UpdateBy': ""
            , 'UpdateDateTime': ""
            , 'CreateBy': $scope.currentUser.UserID
            , 'CreateDateTime': ""

        }
        $scope.fileimg = null;
    }

    $scope.setUpdateModelValue = function (obj) {
        $scope.Cartype = {'CarTypeID': obj.CarTypeID
            , 'CarType': obj.CarType
            , 'SeatAmount': obj.SeatAmount
            , 'ActiveStatus': obj.ActiveStatus
            , 'UpdateBy': $scope.currentUser.UserID
            , 'UpdateDateTime': obj.UpdateDateTime
            , 'CreateBy': obj.CreateBy
            , 'CreateDateTime': obj.CreateDateTime
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
    // $scope.loadRegionList();



});

app.controller('CarReserveDetailController', function($cookies, $scope, $http, $uibModal, $routeParams, HTTPFactory, RegionFactory, IndexOverlayFactory) {
    $scope.$parent.menu_selected = '';
    IndexOverlayFactory.overlayShow();
    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
        window.location.replace('#/logon/doc/' + $routeParams.doc_type);
    }
    IndexOverlayFactory.overlayHide();

    $scope.loadList = function(){
        var con = angular.copy($scope.condition);
        if(con.StartDate != null && con.StartDate != undefined && con.StartDate != ''){
            con.StartDate = makeSQLDate(con.StartDate);
        }
        if(con.EndDate != null && con.EndDate != undefined && con.EndDate != ''){
            con.EndDate = makeSQLDate(con.EndDate);
        }
        var params = {'condition' : con};
        HTTPFactory.clientRequest('carreserve/list/detail', params).then(function (result) {
            
            if (result.data.STATUS == 'OK') {
                $scope.DataList = result.data.DATA.List;
            }

            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.loadRegionList = function () {
        RegionFactory.getAllRegion().then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.RegionList = result.data.DATA;
            }
        });
    }

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
        
        $scope.popup2.opened = true;
    };

    $scope.loadRegionList();

    // $scope.loadList();
});

app.filter('FindUserByName', function () {
    return function (input, FirstName, LastName) {
        if (input !== undefined && input !== null) {
            var i = 0, len = input.length;
            for (; i < len; i++) {
                //console.log(input[i].UserID, '==' ,val);
                if (input[i].FirstName == FirstName && input[i].LastName == LastName) {
                    return i;
                }
            }
            return -1;
        } else {
            return -1;
        }
    };
});