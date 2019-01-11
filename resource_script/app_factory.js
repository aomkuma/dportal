function returnResponse(response){
   if(!tryParseJSON(response.data)){

      //the json is ok
        return response.data;
    }else{

      //the json is not ok
        console.log(response.data);
        return false;
    }
}

function returnErrorResponse(errResponse){
    var errorDesc = errResponse.status + ' : ' + errResponse.statusText ;
    
    if(errResponse.data.DATA != null){
        errorDesc += ':: Cause ' + errResponse.data.DATA.errorInfo[2];
        alert( errorDesc );
    }else{
        if(errResponse.status == 401){
            alert( 'ไม่มีสิทธิ์เข้าใช้งานในหน้านี้' );
        }else{
            alert( errorDesc );
        }
        
        window.location.replace("#/");
    }

    console.error('Error while fetching specific Item', errResponse);
    return errResponse;
}

function tryParseJSON (jsonString){
    try {
        var o = JSON.parse(jsonString);
        if (o && typeof o === "object") {
            return o;
        }
    }
    catch (e) { //alert(jsonString); 
    }

    return false;
};

app.factory('IndexOverlayFactory', function(){
	var indexVar = 
	{
		overlay : false,
		overlayHide : function() {this.overlay = false},
		overlayShow : function() {this.overlay = true}
	};	
	
	return indexVar;
});

app.factory('LoginFactory', ['$http', '$q', function($http, $q){
	 return {
         
    	login : function(obj_login) {
    		return $http.post(servicesUrl + '/dpo/public/login/',{"obj_login":obj_login})
	    		.then(
	    			function(response){
	                    
                        return returnResponse(response);
                        
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
	    		);
	    },
	    
	    logout :  function(obj_current_user) {
    		return $http.post(servicesUrl + '/dpo/public/login/',{"obj_current_user":obj_current_user})
	    		.then(
	    			function(response){
	                    // console.log(response);
                       return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
	    		);
	    },

        verifyUsername : function(username) {
            return $http.post(servicesUrl + '/dpo/public/verifyUsername/',{"username":username})
                .then(
                    function(response){
                        
                        return returnResponse(response);
                        
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        requestOTP : function(username) {
            return $http.post(servicesUrl + '/dpo/public/requestOTP/',{"username":username})
                .then(
                    function(response){
                        
                        return returnResponse(response);
                        
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        verifyOTP : function(otp) {
            return $http.post(servicesUrl + '/dpo/public/verifyOTP/',{"otp":otp})
                .then(
                    function(response){
                        
                        return returnResponse(response);
                        
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        changePassword : function(objChangePassword) {
            return $http.post(servicesUrl + '/dpo/public/changePassword/',{"objChangePassword":objChangePassword})
                .then(
                    function(response){
                        
                        return returnResponse(response);
                        
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },
        
	};
}]);

app.factory('HomeFactory', ['$http', '$q', function($http, $q){
    return {
        getNewsFeed : function(RegionID) {
    		return $http.get(servicesUrl + '/dpo/public/getNewsFeed/' + RegionID)
	    		.then(
	    			function(response){
	                    // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
	    		);
	    },

        getNotificationList : function(regionID, groupID, userID) {
            return $http.get(servicesUrl + '/dpo/public/getNotificationList/' + regionID + '/' + groupID + '/' + userID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return errResponse;//returnErrorResponse(errResponse);
                    }
                );
        },

        getCalendar : function() {
            return $http.get(servicesUrl + '/dpo/public/getHomePageCalendar/')
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },
    }
}]);

app.factory('LOMSFactory', ['$http', '$q', function($http, $q){
    return {
        getData : function(email) {
            return $http.get(LOMSUrl + email)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        getNotificationList : function(email, offset) {
            return $http.get(servicesUrl + '/dpo/public/leaves/notification/get/' + email)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        //return returnErrorResponse(errResponse);
                        return errResponse;
                    }
                );
        },

        updateNotificationStatus : function(ID, email) {           
            return $http.post(servicesUrl + '/dpo/public/leaves/notification/update/seen/' , 
                    {'ID':ID, 'email':email}
                )
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        }

    }
}]);

app.factory('NotificationFactory', ['$http', '$q', function($http, $q){
    return {
        getNotificationList : function(regionID, groupID, userID, offset) {
            return $http.get(servicesUrl + '/dpo/public/getNotificationList/' + regionID + '/' + groupID + '/' + userID + '/' + offset)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        //return returnErrorResponse(errResponse);
                        return errResponse;
                    }
                );
        },

        getNotificationListByCondition : function(notificationType, regionID, keyword, groupID, userID, offset) {
            // console.log(servicesUrl + '/dpo/public/getNotificationListByCondition/' + notificationType + '/' + regionID + '/' + groupID + '/' + userID + '/' + offset);
            return $http.get(servicesUrl + '/dpo/public/getNotificationListByCondition/' + notificationType + '/' + regionID + '/' + groupID + '/' + userID + '/' + offset + '/' + keyword)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        updateNotificationStatus : function(notification, userID, regionID, groupID) {           
            return $http.post(servicesUrl + '/dpo/public/updateNotificationStatus/' , 
                    {'Notification':notification
                    ,'userID':userID
                    ,'regionID':regionID
                    ,'groupID':groupID
                    }
                )
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        }
    }
}]);

app.factory('ReserveRoomFactory', ['$http', '$q', function($http, $q){
	 return {

        findBykeyword : function(keyword) {           
            return $http.get(servicesUrl + '/dpo/public/room-reserve/find-by-keyword/' + keyword)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },
         
    	getRoomReserveDetail : function(regionID, findDate) {           
    		return $http.get(servicesUrl + '/dpo/public/getRoomReserveDetail/' + regionID + '/' + findDate)
	    		.then(
	    			function(response){
	                    // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
	    		);
	    },
        
        getDefaultBookingRoomInfo : function(userID, roomID, reserveRoomID) {           
    		return $http.get(servicesUrl + '/dpo/public/getDefaultBookingRoomInfo/' + userID + '/' + roomID + '/' + reserveRoomID)
	    		.then(
	    			function(response){
	                    // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
	    		);
	    },
        
        updateReserveRoomInfo : function(ReserveRoomInfo
                                ,RoomInfo
                                ,reserveRoomID) {           
    		return $http.post(servicesUrl + '/dpo/public/updateReserveRoomInfo/' , 
                    {'ReserveRoomInfo':ReserveRoomInfo
                    ,'RoomInfo':RoomInfo
                    ,'reserveRoomID':reserveRoomID
                    }
                )
	    		.then(
	    			function(response){
	                    // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
	    		);
	    },

        markStatus : function(ReserveRoomInfo, RoomInfo,InternalAttendeeList
                                        ,ExternalAttendeeList
                                        ,DeviceList
                                        ,FoodList
                                        ,RequestUser
                                        , ReserveRoomID, ReserveStatus, AdminComment) {           
            return $http.post(servicesUrl + '/dpo/public/markStatus/' , 
                    {'ReserveRoomInfo':ReserveRoomInfo
                    ,'RoomInfo':RoomInfo
                    ,'InternalAttendeeList':InternalAttendeeList
                    ,'ExternalAttendeeList':ExternalAttendeeList
                    ,'DeviceList':DeviceList
                    ,'FoodList':FoodList
                    ,'RequestUser':RequestUser
                    ,'ReserveRoomID':ReserveRoomID
                    ,'ReserveStatus':ReserveStatus
                    ,'AdminComment':AdminComment
                    }
                )
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        markStatusRoomDestination : function(ReserveRoomInfo, RoomInfo, ReserveRoomID, ReserveStatus, AdminComment) {           
            return $http.post(servicesUrl + '/dpo/public/markStatusRoomDestination/' , 
                    {'ReserveRoomInfo':ReserveRoomInfo
                    ,'RoomInfo':RoomInfo
                    ,'ReserveRoomID':ReserveRoomID
                    ,'ReserveStatus':ReserveStatus
                    ,'AdminComment':AdminComment
                    }
                )
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },
        
        cancelRoom : function(reserveRoomID) {           
            return $http.post(servicesUrl + '/dpo/public/cancelRoom/' , 
                    {'reserveRoomID':reserveRoomID}
                )
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },
        
        requestReserveRoom : function(ReserveRoomInfo
                                    ,BookingDetail
                                    ,InternalAttendeeList
                                    ,ExternalAttendeeList
                                    ,DeviceList
                                    ,FoodList
                                    ,RoomDestinationList
                                    ,reserveRoomID) {           
            return $http.post(servicesUrl + '/dpo/public/requestReserveRoom/' , 
                    {'ReserveRoomInfo':ReserveRoomInfo
                    ,'RoomInfo':BookingDetail
                    ,'InternalAttendeeList':InternalAttendeeList
                    ,'ExternalAttendeeList':ExternalAttendeeList
                    ,'DeviceList':DeviceList
                    ,'FoodList':FoodList
                    ,'RoomDestinationList':RoomDestinationList
                    ,'reserveRoomID':reserveRoomID}
                )
                .then(
                    function(response){
                        // console.log(response.data);
                       return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        updateAttendee : function(Attendee) {           
    		return $http.post(servicesUrl + '/dpo/public/updateAttendee/',{'Attendee':Attendee})
	    		.then(
	    			function(response){
	                    // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
	    		);
	    },
        deleteAttendee  : function(UserID,ReserveRoomID) {           
    		return $http.delete(servicesUrl + '/dpo/public/deleteAttendee/' +UserID + '/' + ReserveRoomID  /*,{'UserID':UserID,'ReserveRoomID':ReserveRoomID}*/)
	    		.then(
	    			function(response){
	                    // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
	    		);
	    },
        
        updateExternalAttendee : function(ExternalAttendee) {           
    		return $http.post(servicesUrl + '/dpo/public/updateExternalAttendee/',{'ExternalAttendee':ExternalAttendee})
	    		.then(
	    			function(response){
	                    // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
	    		);
	    },
        deleteExternalAttendee  : function(ExternalID) {           
    		return $http.delete(servicesUrl + '/dpo/public/deleteExternalAttendee/' + ExternalID   /*,{'UserID':UserID,'ReserveRoomID':ReserveRoomID}*/)
	    		.then(
	    			function(response){
	                    // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
	    		);
	    },
        
        getDeviceList  : function(regionID, offset) {           
    		return $http.get(servicesUrl + '/dpo/public/getDeviceList/' + regionID + '/' + offset  /*,{'UserID':UserID,'ReserveRoomID':ReserveRoomID}*/)
	    		.then(
	    			function(response){
	                    // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
	    		);
	    },
        
        updateRoomDevice : function(device,reserveRoomID) {           
    		return $http.post(servicesUrl + '/dpo/public/updateRoomDevice/',{'Device':device, 'reserveRoomID':reserveRoomID})
	    		.then(
	    			function(response){
	                    // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
	    		);
	    },
        
        deleteRoomDevice  : function(deviceID, reserveRoomID) {           
    		return $http.delete(servicesUrl + '/dpo/public/deleteRoomDevice/' + deviceID + '/' + reserveRoomID   /*,{'UserID':UserID,'ReserveRoomID':ReserveRoomID}*/)
	    		.then(
	    			function(response){
	                    // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
	    		);
	    },
        
        getFoodList  : function(regionID, offset) {           
    		return $http.get(servicesUrl + '/dpo/public/getFoodList/' + regionID +'/' + offset   /*,{'UserID':UserID,'ReserveRoomID':ReserveRoomID}*/)
	    		.then(
	    			function(response){
	                    // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
	    		);
	    },
        
        updateRoomFood : function(food,reserveRoomID) {           
    		return $http.post(servicesUrl + '/dpo/public/updateRoomFood/',{'Food':food, 'reserveRoomID':reserveRoomID})
	    		.then(
	    			function(response){
	                    // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
	    		);
	    },
        
        deleteRoomFood  : function(foodID, reserveRoomID) {           
    		return $http.delete(servicesUrl + '/dpo/public/deleteRoomFood/' + foodID + '/' + reserveRoomID   /*,{'UserID':UserID,'ReserveRoomID':ReserveRoomID}*/)
	    		.then(
	    			function(response){
	                    // console.log(response.data);
                       return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
	    		);
	    },
        
        updateRoomDestinationStatus : function(Room,reserveRoomID, status) {           
    		return $http.post(servicesUrl + '/dpo/public/updateRoomDestinationStatus/',{'Room':Room, 'reserveRoomID':reserveRoomID, 'status':status})
	    		.then(
	    			function(response){
	                    // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
	    		);
	    }
	};
}]);

app.factory('RegionFactory', ['$http', '$q', function($http, $q){
	 return {
         
    	getAllRegion : function(regionID) {           
    		return $http.get(servicesUrl + '/dpo/public/allRegion/')
	    		.then(
	    			function(response){
	                    // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
	    		);
	    }
	};
}]);

app.factory('NewsFactory', ['$http', '$q', function($http, $q){
     return {
         
        getList : function(offset,RegionID, GlobalNews,HideNews,CurrentNews,WaitApprove, UserID) {           
            return $http.get(servicesUrl + '/dpo/public/getNewsList/' + offset +'/' + RegionID + '/' + GlobalNews +'/' + HideNews+'/' + CurrentNews+'/' + WaitApprove  + '/' + UserID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        getNewsList : function(offset,RegionID,GlobalNews) {           
            return $http.get(servicesUrl + '/dpo/public/getNewsListView/' + offset +'/' + RegionID+'/'+GlobalNews)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        deleteData : function(ID) {           
            return $http.delete(servicesUrl + '/dpo/public/deleteNewsData/' + ID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        getNewsTypeList : function() {           
            return $http.get(servicesUrl + '/dpo/public/getNewsTypeList/')
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        }, 

        deleteNewsPictureData : function(ID) {           
            return $http.delete(servicesUrl + '/dpo/public/deleteNewsPictureData/' + ID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        deleteNewsAttachFile  : function(ID) {           
            return $http.delete(servicesUrl + '/dpo/public/deleteNewsAttachFile/' + ID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        requestNews : function(News) {           
            return $http.post(servicesUrl + '/dpo/public/requestNews/',{'News':News})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        adminUpdateStatus : function(News) {           
            return $http.post(servicesUrl + '/dpo/public/adminUpdateNewsStatus/',{'News':News})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        getNewsByID : function(ID) {           
            return $http.get(servicesUrl + '/dpo/public/getNewsByID/' + ID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        }, 

        viewNews : function(ID) {           
            return $http.get(servicesUrl + '/dpo/public/viewNews/' + ID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        search : function(keyword) {           
            return $http.get(servicesUrl + '/dpo/public/searchNews/' + keyword)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },
    };
}]);

app.factory('RoomFactory', ['$http', '$q', function($http, $q){
     return {
         
        getList : function(offset, UserID) {           
            return $http.get(servicesUrl + '/dpo/public/getRoomList/' + offset + '/'  + UserID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        }, 
        deleteData : function(ID) {           
            return $http.delete(servicesUrl + '/dpo/public/deleteRoomData/' + ID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        }
    };
}]);

app.factory('FoodFactory', ['$http', '$q', function($http, $q){
     return {
         
        getList : function(offset) {           
            return $http.get(servicesUrl + '/dpo/public/getFoodList/' + offset)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        }, 
        deleteData : function(ID) {           
            return $http.delete(servicesUrl + '/dpo/public/deleteFoodData/' + ID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        }
    };
}]);

app.factory('DeviceFactory', ['$http', '$q', function($http, $q){
     return {
         
        getList : function(offset, UserID) {           
            return $http.get(servicesUrl + '/dpo/public/getDeviceManageList/' + offset + '/' + UserID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        }, 
        deleteData : function(ID) {           
            return $http.delete(servicesUrl + '/dpo/public/deleteDeviceData/' + ID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        }
    };
}]);


app.factory('ReserveCarFactory', ['$http', '$q', function($http, $q){
     return {
         
        getCarReserveDetail : function(reserveCarID) {           
            return $http.get(servicesUrl + '/dpo/public/getCarReserveDetail/' + reserveCarID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        getProvinceList : function() {           
            return $http.get(servicesUrl + '/dpo/public/getProvinceList/')
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        getMaxSeatAmount : function() {           
            return $http.get(servicesUrl + '/dpo/public/getMaxSeatAmount/')
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        updateReserveCarInfo : function(ReserveCarInfo,TravellerList, reserveCarID) {           
            return $http.post(servicesUrl + '/dpo/public/updateReserveCarInfo/'
                        ,{
                            'ReserveCarInfo' : ReserveCarInfo
                            ,'TravellerList' : TravellerList
                            ,'reserveCarID':reserveCarID
                        })
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        updateTraveller : function(UserID,ReserveCarID,CreateBy) {           
            return $http.post(servicesUrl + '/dpo/public/updateTraveller/'
                        ,{'UserID' : UserID
                        ,'ReserveCarID':ReserveCarID
                        ,'CreateBy':CreateBy})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        deleteTraveller : function(travellerID) {           
            return $http.delete(servicesUrl + '/dpo/public/deleteTraveller/' + travellerID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        cancelReserveCar : function(reserveCarID) {           
            return $http.post(servicesUrl + '/dpo/public/cancelReserveCar/',{'reserveCarID':reserveCarID})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        requestReserveCar : function(ReserveCarInfo, TravellerList, RequestUser) {           
            return $http.post(servicesUrl + '/dpo/public/requestReserveCar/',
                    {'ReserveCarInfo':ReserveCarInfo
                    ,'TravellerList':TravellerList
                    ,'RequestUser':RequestUser})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        adminUpdateCarStatus : function(ReserveCarInfo, TravellerList, InternalDriver, ExternalDriver) {           
            return $http.post(servicesUrl + '/dpo/public/adminUpdateCarStatus/',
                    {'ReserveCarInfo':ReserveCarInfo
                    ,'TravellerList':TravellerList
                    ,'InternalDriver':InternalDriver
                    ,'ExternalDriver':ExternalDriver})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        getReserveCartypeList : function() {           
            return $http.get(servicesUrl + '/dpo/public/getReserveCartypeList/')
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        getCarsInRegion : function(regionID,findDate) {           
            return $http.get(servicesUrl + '/dpo/public/getCarsInRegion/' + regionID + '/' + findDate)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        }
       
    };
}]);

app.factory('CartypeFactory', ['$http', '$q', function($http, $q){
     return {
        getList : function() {           
            return $http.get(servicesUrl + '/dpo/public/getCartypeList/')
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        getManageList : function(offset , UserID) {           
            return $http.get(servicesUrl + '/dpo/public/getCartypeManageList/' + offset + '/' + UserID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        }, 
        deleteData : function(ID) {           
            return $http.delete(servicesUrl + '/dpo/public/deleteCartypeData/' + ID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        }
    };
}]);

app.factory('CarFactory', ['$http', '$q', function($http, $q){
     return {
         
        getList : function(offset, UserID) {           
            return $http.get(servicesUrl + '/dpo/public/getCarList/' + offset + '/' + UserID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        }, 
        deleteData : function(ID) {           
            return $http.delete(servicesUrl + '/dpo/public/deleteCarData/' + ID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        }
    };
}]);

app.factory('ProvinceFactory', ['$http', '$q', function($http, $q){
     return {
         
        getAllProvince : function() {           
            return $http.get(servicesUrl + '/dpo/public/getProvinceList/' )
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        }, 
      
    };
}]);

app.factory('CalendarFactory', ['$http', '$q', function($http, $q){
     return {
         
        getList : function(mode) {           
            return $http.get(servicesUrl + '/dpo/public/getCalendarList/' + mode)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        getManageList : function(UserID) {           
            return $http.get(servicesUrl + '/dpo/public/getCalendarManageList/' + UserID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        updateData : function(Calendar) {           
            return $http.post(servicesUrl + '/dpo/public/updateCalendar/',{'Calendar' : Calendar})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        deleteData : function(ID) {           
            return $http.delete(servicesUrl + '/dpo/public/deleteCalendar/' + ID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        }
    };
}]);

app.factory('LinkFactory', ['$http', '$q', function($http, $q){
     return {
         
        getList : function(mode, userID) {           
            return $http.get(servicesUrl + '/dpo/public/getLinkList/' + mode + '/' + userID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        updateData : function(Link) {           
            return $http.post(servicesUrl + '/dpo/public/updateLink/',{'Link' : Link})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        deleteData : function(ID) {           
            return $http.delete(servicesUrl + '/dpo/public/deleteLink/' + ID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        getLinkPermission : function(offset, linkID, condition, userID) {           
            return $http.post(servicesUrl + '/dpo/public/getLinkPermission/'
                        ,{offset : offset, linkID : linkID, condition : condition , userID : userID})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        updatePermission : function(data, userID) {           
            return $http.post(servicesUrl + '/dpo/public/updateLinkPermission/'
                        ,{data : data , userID : userID})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        setAllLinkPermission : function(condition, linkID, updateType, userID) {           
            return $http.post(servicesUrl + '/dpo/public/setAllLinkPermission/'
                        ,{condition : condition 
                        , linkID : linkID
                        , updateType : updateType
                        , userID: userID})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        }
    };
}]);

app.factory('RepairFactory', ['$http', '$q', function($http, $q){
     return {
         
        getRepairTypeList : function(mode, UserID) {           
            return $http.get(servicesUrl + '/dpo/public/getRepairTypeList/' + mode + '/' + UserID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        getRepairType : function(ID) {           
            return $http.get(servicesUrl + '/dpo/public/getRepairType/' + ID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        updateRepairType : function(obj) {           
            return $http.post(servicesUrl + '/dpo/public/updateRepairType/',{'updateObj' : obj})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        deleteRepairType : function(ID) {           
            return $http.delete(servicesUrl + '/dpo/public/deleteRepairType/' + ID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        getRepairTitleList : function(mode, RepairedTypeID) {           
            return $http.get(servicesUrl + '/dpo/public/getRepairTitleList/' + mode + '/' + RepairedTypeID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        getRepairTitle : function(ID) {           
            return $http.get(servicesUrl + '/dpo/public/getRepairTitle/' + ID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        updateRepairTitle : function(obj) {           
            return $http.post(servicesUrl + '/dpo/public/updateRepairTitle/',{'updateObj' : obj})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        deleteRepairTitle : function(ID) {           
            return $http.delete(servicesUrl + '/dpo/public/deleteRepairTitle/' + ID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        getRepairIssueList : function(mode, RepairedTitleID) {           
            return $http.get(servicesUrl + '/dpo/public/getRepairIssueList/' + mode + '/' + RepairedTitleID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        getRepairIssue : function(ID) {           
            return $http.get(servicesUrl + '/dpo/public/getRepairIssue/' + ID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        updateRepairIssue : function(obj) {           
            return $http.post(servicesUrl + '/dpo/public/updateRepairIssue/',{'updateObj' : obj})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        deleteRepairIssue : function(ID) {           
            return $http.delete(servicesUrl + '/dpo/public/deleteRepairIssue/' + ID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        getRepairSubIssueList : function(mode, RepairedIssueID) {           
            return $http.get(servicesUrl + '/dpo/public/getRepairSubIssueList/' + mode + '/' + RepairedIssueID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        getRepairSubIssue : function(ID) {           
            return $http.get(servicesUrl + '/dpo/public/getRepairSubIssue/' + ID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        updateRepairSubIssue : function(obj) {           
            return $http.post(servicesUrl + '/dpo/public/updateRepairSubIssue/',{'updateObj' : obj})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        deleteRepairSubIssue : function(ID) {           
            return $http.delete(servicesUrl + '/dpo/public/deleteRepairSubIssue/' + ID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        getDepartmentList : function() {           
            return $http.get(servicesUrl + '/dpo/public/getDepartmentList/')
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        getRepair : function(RepairedID) {           
            return $http.get(servicesUrl + '/dpo/public/getRepair/' + RepairedID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        updateRepair : function(obj) {           
            return $http.post(servicesUrl + '/dpo/public/updateRepair/',{'updateObj' : obj})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        updateStatusRepair : function(obj, status) {           
            return $http.post(servicesUrl + '/dpo/public/updateStatusRepair/',{'updateObj' : obj, 'status' : status})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        updateAdminReceiveRepair : function(obj, AdminID) {           
            return $http.post(servicesUrl + '/dpo/public/updateAdminReceiveRepair/',{'updateObj' : obj, 'AdminID':AdminID})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        updateRepairAdmin : function(obj, RepairStatus) {           
            return $http.post(servicesUrl + '/dpo/public/updateRepairAdmin/',{'updateObj' : obj, 'RepairStatus' : RepairStatus})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },
    };
}]);

app.factory('DepartmentFactory', ['$http', '$q', function($http, $q){
    return {
        getDepartmentList : function() {           
            return $http.get(servicesUrl + '/dpo/public/getDepartmentList/')
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },
        getAllDepartmentList : function() {           
            return $http.get(servicesUrl + '/dpo/public/getAllDepartmentList/')
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        loadManageDepartmentList : function(condition) {           
            return $http.post(servicesUrl + '/dpo/public/loadManageDepartmentList/' , {condition : condition})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        updateRegionOfDepartment : function(groupID, orgID, RegionID) {           
            return $http.post(servicesUrl + '/dpo/public/updateRegionOfDepartment/' 
                        , {groupID : groupID
                            , orgID : orgID
                            , RegionID : RegionID})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },
    }
}]);

app.factory('PhoneBookFactory', ['$http', '$q', function($http, $q){
    return {
        getList : function(Group, Username, offset, Region, Department, LoginUserID) {           
            return $http.get(servicesUrl + '/dpo/public/getPhoneBookList/' + Group + '/' + Username + '/' + offset + '/' + Region + '/' + Department + '/' + LoginUserID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        addFavourite : function(UserFavouriteID, LoginUserID) {           
            return $http.post(servicesUrl + '/dpo/public/addFavouriteContact/', {'UserFavouriteID':UserFavouriteID, 'LoginUserID':LoginUserID})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        removeFavourite : function(FavouriteID, UserID) {           
            return $http.delete(servicesUrl + '/dpo/public/removeFavouriteContact/' + FavouriteID + '/' + UserID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        getUserContact : function(UserID) {           
            return $http.get(servicesUrl + '/dpo/public/getUserContact/' + UserID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        updateContact : function(Contact) {           
            return $http.post(servicesUrl + '/dpo/public/updatePhoneBookContact/', {'Contact':Contact})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        }

    };
}])

app.factory('PermissionFactory', ['$http', '$q', function($http, $q){
    return {
        getList : function(condition) {           
            return $http.post(servicesUrl + '/dpo/public/getPermissionList/',condition)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        updatePermission : function(data, permissionType) {           
            return $http.post(servicesUrl + '/dpo/public/updatePermission/',
                    {'data' : data
                    ,'permissionType' : permissionType})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },
    }
}]);

app.factory('ReportFactory', ['$http', '$q', function($http, $q){
    return {
        queryReport : function(condition) {           
            return $http.post(servicesUrl + '/dpo/public/queryReport/',condition)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        exportExcel : function(condition, data, summary) {           
            return $http.post(servicesUrl + '/dpo/public/exportExcel/'
                    ,{condition : condition, data : data, summary: summary})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        loadRoomListByRegion : function(regionID) {           
            return $http.get(servicesUrl + '/dpo/public/loadRoomListByRegion/' + regionID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        loadCarListByRegion : function(regionID) {           
            return $http.get(servicesUrl + '/dpo/public/loadCarListByRegion/' + regionID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        loadUserListByRegion : function(regionID) {           
            return $http.get(servicesUrl + '/dpo/public/loadUserListByRegion/' + regionID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },
        loadUserListByRegionAndRole : function(regionID) {           
            console.log('asdasd');
            return $http.get(servicesUrl + '/dpo/public/loadUserListByRegionAndRole/' + regionID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },
    }
}]);

app.factory('ExternalPhoneBookFactory', ['$http', '$q', function($http, $q){
    return {
        getManagePhoneBookList : function(offset, UserID, condition) {           
            return $http.post(servicesUrl + '/dpo/public/getManagePhoneBookList/'
                        , {'offset' : offset, UserID : UserID, 'condition' : condition})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        getExternalPhoneBookList : function(offset, condition) {           
            return $http.post(servicesUrl + '/dpo/public/getExternalPhoneBookList/'
                        , {'offset' : offset, 'condition' : condition})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        loadPhoneBookPermission : function(PhoneBookID, RegionID) {           
            return $http.get(servicesUrl + '/dpo/public/loadPhoneBookPermission/' + PhoneBookID + '/' + RegionID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        updateExPhoneBookPermission : function(updateObj) {           
            return $http.post(servicesUrl + '/dpo/public/updateExPhoneBookPermission/' , updateObj)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        addFavourite : function(UserFavouriteID, LoginUserID) {           
            return $http.post(servicesUrl + '/dpo/public/addFavouriteExContact/', {'UserFavouriteID':UserFavouriteID, 'LoginUserID':LoginUserID})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        removeFavourite : function(FavouriteID, UserID) {           
            return $http.delete(servicesUrl + '/dpo/public/removeFavouriteExContact/' + FavouriteID + '/' + UserID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        getUserContact : function(UserID) {           
            return $http.get(servicesUrl + '/dpo/public/getUserContact/' + UserID)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        updateContact : function(Contact) {           
            return $http.post(servicesUrl + '/dpo/public/updateExternalPhoneBook/', {'Contact':Contact})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        }

    };
}])

app.factory('LogFactory', ['$http', '$q', function($http, $q){
    return {
        getLogManageList : function() {           
            return $http.get(servicesUrl + '/dpo/public/getLogManageList/')
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        downloadLog : function(filename) {           
            return $http.post(servicesUrl + '/dpo/public/downloadLog/', {logName : filename})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },
    };
}])

app.factory('SettingFactory', ['$http', '$q', function($http, $q){
    return {
        getSettingManageList : function() {           
            return $http.get(servicesUrl + '/dpo/public/getSettingManageList/')
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        saveSettingManageList : function(data) {           
            return $http.post(servicesUrl + '/dpo/public/saveSettingManageList/', {data : data})
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

    };
}])

app.factory('HTTPFactory', ['$http', '$q', 'Upload', function($http, $q, Upload){
    return {

        clientRequest : function(action, data) {           
            return $http.post(servicesUrl + '/dpo/public/' + action + '/', data)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        getRequest : function(action, data) {           
            return $http.get(servicesUrl + '/dpo/public/' + action + '/' + data)
                .then(
                    function(response){
                        // console.log(response.data);
                        return returnResponse(response);
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        uploadRequest : function(action, obj) {
            return Upload.upload({
                url: servicesUrl + '/dpo/public/' + action + '/',
                data: obj
            }).then(
                function(response){
                    return returnResponse(response);                    
                }, 
                function(errResponse){
                    return returnErrorResponse(errResponse);
                }
            );
        }

    };
}])