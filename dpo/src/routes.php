<?php
// Routes

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//$app->get('/user/{id}', 'UserController:getUser');
$app->post('/user/', 'UserController:addUser');
// Login action
$app->post('/login/', 'LoginController:authenticate');
$app->post('/verifyUsername/', 'LoginController:verifyUsername');
$app->post('/requestOTP/', 'LoginController:requestOTP');
$app->post('/verifyOTP/', 'LoginController:verifyOTP');
$app->post('/changePassword/', 'LoginController:changePassword');
$app->post('/login/thirdparty/session/', 'LoginController:authenticateWithSession');
$app->post('/login/pin/', 'LoginController:authenticateWithPIN');
$app->post('/login/pin/setting/', 'LoginController:pinSetting');

// Phone Book action
$app->get('/getPhoneBookList/{Group}/{Username}/{offset}/{RegionID}/{DepartmentID}/{LoginUserID}', 'UserController:getPhoneBookList');
$app->post('/addFavouriteContact/', 'UserController:addFavouriteContact');
$app->delete('/removeFavouriteContact/{FavouriteID}/{UserID}', 'UserController:removeFavouriteContact');
$app->get('/getUserContact/{UserID}', 'UserController:getUserContact');
$app->post('/updatePhoneBookContact/', 'UserController:updatePhoneBookContact');

// External Phone Book action
$app->post('/getManagePhoneBookList/', 'ExternalPhoneBookController:getManagePhoneBookList');
$app->get('/loadPhoneBookPermission/{PhoneBookID}/{RegionID}', 'ExternalPhoneBookController:loadPhoneBookPermission');
$app->post('/updateExPhoneBookPermission/', 'ExternalPhoneBookController:updateExPhoneBookPermission');
$app->post('/updateExternalPhoneBook/', 'ExternalPhoneBookController:updateExternalPhoneBook');
$app->post('/getExternalPhoneBookList/', 'ExternalPhoneBookController:getExternalPhoneBookList');
$app->post('/addFavouriteExContact/', 'ExternalPhoneBookController:addFavouriteContact');
$app->delete('/removeFavouriteExContact/{FavouriteID}/{UserID}', 'ExternalPhoneBookController:removeFavouriteContact');

// News action
$app->get('/getNewsFeed/{RegionID}', 'NewsController:getNewsFeed');
$app->get('/getNewsList/{offset}/{RegionID}/{HideNews}/{CurrentNews}/{WaitApprove}/{UserID}', 'NewsController:getNewsList');
$app->get('/getNewsListView/{offset}/{RegionID}', 'NewsController:getNewsListView');
$app->get('/getNewsTypeList/', 'NewsController:getNewsTypeList');
$app->delete('/deleteNewsData/{ID}', 'NewsController:deleteData');
$app->post('/updateNewsData/', 'NewsController:updateData');
$app->delete('/deleteNewsPictureData/{ID}', 'NewsController:deleteNewsPictureData');
$app->delete('/deleteNewsAttachFile/{ID}', 'NewsController:deleteNewsAttachFile');
$app->post('/requestNews/', 'NewsController:requestNews');
$app->get('/getNewsByID/{newsID}', 'NewsController:getNewsByID');
$app->post('/adminUpdateNewsStatus/', 'NewsController:adminUpdateNewsStatus');
$app->get('/viewNews/{newsID}', 'NewsController:viewNews');
$app->get('/searchNews/{keyword}', 'NewsController:searchNews');

// Room action
$app->get('/getRoomList/{offset}/{UserID}', 'RoomController:getRoomList');
$app->delete('/deleteRoomData/{ID}', 'RoomController:deleteData');
$app->post('/updateRoomData/', 'RoomController:updateData');

// Food action
$app->get('/getFoodList/{offset}', 'FoodController:getFoodList');
$app->delete('/deleteFoodData/{ID}', 'FoodController:deleteData');
$app->post('/updateFoodData/', 'FoodController:updateData');

// Device action
$app->get('/getDeviceManageList/{offset}/{UserID}', 'DeviceController:getDeviceList');
$app->delete('/deleteDeviceData/{ID}', 'DeviceController:deleteData');
$app->post('/updateDeviceData/', 'DeviceController:updateData');

// Cartype action
$app->get('/getCartypeList/', 'CartypeController:getCartypeList');
$app->get('/getCartypeManageList/{offset}/{UserID}', 'CartypeController:getCartypeManageList');
$app->delete('/deleteCartypeData/{ID}', 'CartypeController:deleteData');
$app->post('/updateCartypeData/', 'CartypeController:updateData');

// Car action
$app->get('/getCarList/{offset}/{UserID}', 'CarController:getCarList');
$app->delete('/deleteCarData/{ID}', 'CarController:deleteData');
$app->post('/updateCarData/', 'CarController:updateData');

// Region action
$app->get('/allRegion/', 'RegionController:getRegionList');

// Province action
$app->get('/getProvinceList/', 'ProvinceController:getProvinceList');

// Calendar action
$app->get('/getCalendarList/{mode}', 'CalendarController:getCalendarList');
$app->get('/getCalendarManageList/{UserID}', 'CalendarController:getCalendarManageList');
$app->delete('/deleteCalendar/{ID}', 'CalendarController:deleteData');
$app->post('/updateCalendar/', 'CalendarController:updateData');
$app->get('/getHomePageCalendar/', 'CalendarController:getHomePageCalendar');

// Link action
$app->get('/getLinkList/{mode}/{userID}', 'LinkController:getLinkList');
$app->delete('/deleteLink/{ID}', 'LinkController:deleteData');
$app->post('/updateLink/', 'LinkController:updateData');
$app->post('/getLinkPermission/', 'LinkController:getLinkPermission');
$app->post('/updateLinkPermission/', 'LinkController:updateLinkPermission');
$app->post('/setAllLinkPermission/', 'LinkController:setAllLinkPermission');

// Repair action
$app->get('/getRepairTypeList/{mode}/{UserID}', 'RepairController:getRepairTypeList');
$app->delete('/deleteRepairType/{ID}', 'RepairController:deleteRepairType');
$app->post('/updateRepairType/', 'RepairController:updateRepairType');
$app->get('/getRepairType/{ID}', 'RepairController:getRepairType');
$app->get('/getRepairTitleList/{mode}/{RepairedTypeID}', 'RepairController:getRepairTitleList');
$app->get('/getRepairTitle/{ID}', 'RepairController:getRepairTitle');
$app->post('/updateRepairTitle/', 'RepairController:updateRepairTitle');
$app->delete('/deleteRepairTitle/{ID}', 'RepairController:deleteRepairTitle');
$app->get('/getRepairIssueList/{mode}/{RepairedTitleID}', 'RepairController:getRepairIssueList');
$app->get('/getRepairIssue/{ID}', 'RepairController:getRepairIssue');
$app->post('/updateRepairIssue/', 'RepairController:updateRepairIssue');
$app->delete('/deleteRepairIssue/{ID}', 'RepairController:deleteRepairSubIssue');
$app->get('/getRepairSubIssueList/{mode}/{RepairedIssueID}', 'RepairController:getRepairSubIssueList');
$app->get('/getRepairSubIssue/{ID}', 'RepairController:getRepairSubIssue');
$app->post('/updateRepairSubIssue/', 'RepairController:updateRepairSubIssue');
$app->delete('/deleteRepairSubIssue/{ID}', 'RepairController:deleteRepairSubIssue');
$app->get('/getRepair/{RepairedID}', 'RepairController:getRepair');
$app->post('/updateRepair/', 'RepairController:updateRepair');
$app->post('/updateStatusRepair/', 'RepairController:updateStatusRepair');
$app->post('/updateAdminReceiveRepair/', 'RepairController:updateAdminReceiveRepair');
$app->post('/updateRepairAdmin/', 'RepairController:updateRepairAdmin');
$app->get('/notify24Hours/', 'RepairController:notify24Hours');

// Autocomplete
$app->get('/autocomplete/{qtype}/{keyword}', 'AutocompleteController:getAutocomplete');

// Department action
$app->get('/getDepartmentList/', 'DepartmentController:getDepartmentList');
$app->get('/getAllDepartmentList/', 'DepartmentController:getAllDepartmentList');
$app->post('/loadManageDepartmentList/', 'DepartmentController:loadManageDepartmentList');
$app->post('/updateRegionOfDepartment/', 'DepartmentController:updateRegionOfDepartment');

// Notification action
$app->get('/getNotificationList/{regionID}/{adminGroup}/{userID}/{offset}', 'NotificationController:getNotificationList');
$app->get('/getNotificationListByCondition/{notificationType}/{regionID}/{adminGroup}/{userID}/{offset}/{keyword}', 'NotificationController:getNotificationListByCondition');
$app->post('/updateNotificationStatus/', 'NotificationController:updateNotificationStatus');

//  Room reserve action
$app->post('/room/monitor/', 'RoomReserveController:getRoomMonitor');
$app->get('/getRoomReserveDetail/{regionID}/{findDate}', 'RoomReserveController:getRoomBookingList');
$app->get('/getDefaultBookingRoomInfo/{userID}/{roomID}/{reserveRoomID}', 'RoomReserveController:getDefaultBookingRoomInfo');
$app->post('/updateReserveRoomInfo/', 'RoomReserveController:updateReserveRoomInfo');
$app->post('/updateAttendee/', 'RoomReserveController:updateAttendee');
$app->delete('/deleteAttendee/{UserID}/{ReserveRoomID}', 'RoomReserveController:deleteAttendee');
$app->post('/updateExternalAttendee/', 'RoomReserveController:updateExternalAttendee');
$app->delete('/deleteExternalAttendee/{AttendeeID}', 'RoomReserveController:deleteExternalAttendee');
$app->get('/getDeviceList/{regionID}/{offset}', 'RoomReserveController:getDeviceList');
$app->post('/updateRoomDevice/', 'RoomReserveController:updateRoomDevice');
$app->delete('/deleteRoomDevice/{deviceID}/{reserveRoomID}', 'RoomReserveController:deleteRoomDevice');
$app->get('/getFoodList/{regionID}/{offset}', 'RoomReserveController:getFoodList');
$app->post('/updateRoomFood/', 'RoomReserveController:updateRoomFood');
$app->delete('/deleteRoomFood/{foodID}/{reserveRoomID}', 'RoomReserveController:deleteRoomFood');
$app->post('/updateRoomDestinationStatus/', 'RoomReserveController:updateRoomDestinationStatus');
$app->post('/markStatus/', 'RoomReserveController:markStatus');
$app->post('/markStatusRoomDestination/', 'RoomReserveController:markStatusRoomDestination');
$app->post('/cancelRoom/', 'RoomReserveController:cancelRoom');
$app->post('/requestReserveRoom/', 'RoomReserveController:requestReserveRoom');

// Car Reserve action
//$app->get('/getProvinceList/', 'CarReserveController:getProvinceList');
$app->get('/getMaxSeatAmount/', 'CarReserveController:getMaxSeatAmount');
$app->post('/updateTraveller/', 'CarReserveController:updateTraveller');
$app->post('/updateReserveCarInfo/', 'CarReserveController:updateReserveCarInfo');
$app->get('/getCarReserveDetail/{reserveCarID}', 'CarReserveController:getCarReserveDetail');
$app->delete('/deleteTraveller/{travellerID}', 'CarReserveController:deleteTraveller');
$app->post('/cancelReserveCar/', 'CarReserveController:cancelReserveCar');
$app->post('/requestReserveCar/', 'CarReserveController:requestReserveCar');
$app->post('/adminUpdateCarStatus/', 'CarReserveController:adminUpdateCarStatus');
$app->get('/getReserveCartypeList/', 'CarReserveController:getReserveCartypeList');
$app->get('/getCarsInRegion/{regionID}/{findDate}', 'CarReserveController:getCarsInRegion');

// eHr action
$app->get('/eHrUpdateDepartment/', 'EHRController:eHrUpdateDepartment');
$app->get('/eHrUpdateOffice/', 'EHRController:eHrUpdateOffice');
$app->get('/eHrUpdateDivision/', 'EHRController:eHrUpdateDivision');
$app->get('/eHrUpdateStaff/', 'EHRController:eHrUpdateStaff');
$app->get('/testMail/', 'EHRController:testMail');

// Permission action
$app->post('/getPermissionList/', 'PermissionController:getPermissionList');
$app->post('/updatePermission/', 'PermissionController:updatePermission');

// Report action
$app->post('/queryReport/', 'ReportController:queryReport');
$app->post('/exportExcel/', 'ReportController:exportExcel');
$app->get('/loadRoomListByRegion/{regionID}', 'ReportController:loadRoomListByRegion');
$app->get('/loadCarListByRegion/{regionID}', 'ReportController:loadCarListByRegion');
$app->get('/loadUserListByRegion/{regionID}', 'ReportController:loadUserListByRegion');
$app->get('/loadUserListByRegionAndRole/{regionID}', 'ReportController:loadUserListByRegionAndRole');
$app->get('/loadPDF/{pdfName}', 'ReportController:loadPDF');

// System Manage action
$app->get('/getLogManageList/', 'SystemManageController:getLogManageList');
$app->post('/downloadLog/', 'SystemManageController:downloadLog');
$app->get('/getSettingManageList/', 'SystemManageController:getSettingManageList');
$app->post('/saveSettingManageList/', 'SystemManageController:saveSettingManageList');

$app->post('/person-region/get/', 'PersonRegionController:getPersonRegionList');
$app->post('/person-region/update/', 'PersonRegionController:updatePersonRegion');

// MIS Request
$app->post('/mis/list/user/', 'UserController:getMISUserInfoList');
$app->post('/mis/get/user/', 'UserController:getMISUserInfo');

// LOMS Request
$app->post('/leaves/notification/put/', 'LeaveController:putNotification');
$app->get('/leaves/notification/get/{email}', 'LeaveController:getNotification');
$app->post('/leaves/notification/update/seen/', 'LeaveController:updateSeenNotification');

// Default action
$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});
