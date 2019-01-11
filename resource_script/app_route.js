app.config(function($routeProvider) {
    $routeProvider
    .when("/", {
        templateUrl : "html/home.html",
        controller : "HomeController"
    })

    .when("/mobile", {
        templateUrl : "../html/login/mobile.html",
        controller : "LoginController"
    })

    .when("/docs/:doc_type", {
        templateUrl : "html/docs/docs.html",
        controller : "DocsController"
    })

    .when("/manage_docs/doc1", {
        templateUrl : "html/manage-docs/docs1.html",
        controller : "Docs1Controller"
    })

    .when("/manage_docs/doc2/:parent_id/:id?", {
        templateUrl : "html/manage-docs/docs2.html",
        controller : "Docs2Controller"
    })

    .when("/manage_docs/doc3/:parent_id/:id?", {
        templateUrl : "html/manage-docs/docs3.html",
        controller : "Docs3Controller"
    })

    .when("/manage_docs/rule1", {
        templateUrl : "html/manage-rules/rules1.html",
        controller : "Rules1Controller"
    })

    .when("/manage_docs/rule2", {
        templateUrl : "html/manage-rules/rules2.html",
        controller : "Rules2Controller"
    })

    .when("/manage_docs/rule3", {
        templateUrl : "html/manage-rules/rules3.html",
        controller : "Rules3Controller"
    })
    
	.when("/logon/:redirect_url?", {
        templateUrl : "login.html",
        controller : "LoginController"
	})
    
    .when("/forgotpassword", {
        templateUrl : "html/login/forgotpass.html",
        controller : "ForgotPasswordController"
    })

    .when("/roomconference", {
        templateUrl : "html/roomreservation/overview.html",
        controller : "RoomOverviewController"
    })

    .when("/room/monitor/:region_id", {
        templateUrl : "html/roomreservation/monitor.html",
        controller : "RoomMonitorController"
	})
    
    .when("/roombooking/:userID/:roomID/:startDateTime?/:roomReserveID?/:destinationRoomID?", {
        templateUrl : "html/roomreservation/roombooking.html",
        controller : "RoomBookingController"
	})

    .when("/search/:keyword?", {
        templateUrl : "html/news/search.html",
        controller : "SearchNewsController"
    })

    .when("/manage_news/:newsID?", {
        templateUrl : "html/manage/news.html",
        controller : "NewsController"
    })

    .when("/manage_room", {
        templateUrl : "html/manage/room.html",
        controller : "RoomController"
    })

    .when("/manage_food", {
        templateUrl : "html/manage/food.html",
        controller : "FoodController"
    })
    
    .when("/manage_device", {
        templateUrl : "html/manage/device.html",
        controller : "DeviceController"
    })

    .when("/manage_cartype", {
        templateUrl : "html/manage/cartype.html",
        controller : "CartypeController"
    })
    
    .when("/manage_car", {
        templateUrl : "html/manage/car.html",
        controller : "CarController"
    })

    .when("/manage_link", {
        templateUrl : "html/manage/link.html",
        controller : "LinkManageController"
    })

    .when("/manage_calendar", {
        templateUrl : "html/manage/calendar.html",
        controller : "CalendarManageController"
    })

    .when("/manage_repair", {
        templateUrl : "html/manage/repair_type.html",
        controller : "RepairManageController"
    })

    .when("/manage_repair_type/:RepairedTypeID?", {
        templateUrl : "html/manage/repair_type_update.html",
        controller : "RepairTypeManageController"
    })

    .when("/manage_repair_title/:RepairedTypeID/:RepairedTitleID?", {
        templateUrl : "html/manage/repair_title_update.html",
        controller : "RepairTitleManageController"
    })

    .when("/manage_repair_issue/:RepairedTypeID/:RepairedTitleID/:RepairedIssueID?", {
        templateUrl : "html/manage/repair_issue_update.html",
        controller : "RepairIssueManageController"
    })

    .when("/manage_repair_sub_issue/:RepairedTypeID/:RepairedTitleID/:RepairedIssueID/:RepairedSubIssueID?", {
        templateUrl : "html/manage/repair_sub_issue_update.html",
        controller : "RepairSubIssueManageController"
    })

    .when("/manage_telephonebook", {
        templateUrl : "html/manage/external_phonebook.html",
        controller : "ExternalPhoneBookManageController"
    })

    .when("/vehicles/:reserveCarID", {
        templateUrl : "html/carreservation/carbooking.html",
        controller : "CarBookingController"
    })

    .when("/news/:viewType?", {
        templateUrl : "html/news/news.html",
        controller : "NewsListController"
    })

    .when("/news_detail/:newsID?", {
        templateUrl : "html/news/news_detail.html",
        controller : "NewsDetailController"
    })

    .when("/link", {
        templateUrl : "html/link/link.html",
        controller : "LinkController"
    })

    .when("/notifications", {
        templateUrl : "html/notification/main.html",
        controller : "NotificationListController"
    })

    .when("/repair/:RepairedID?", {
        templateUrl : "html/repair/repair_desc.html",
        controller : "RepairDescController"
    })

    .when("/telephonebook_internal", {
        templateUrl : "html/phonebook/telephonebook.html",
        controller : "PhoneBookController"
    })

    .when("/telephonebook_external", {
        templateUrl : "html/phonebook/telephonebook_ex.html",
        controller : "PhoneBookEXController"
    })

    .when("/calendar", {
        templateUrl : "html/calendar/calendar.html",
        controller : "CalendarController"
    })

    .when("/manage_permission", {
        templateUrl : "html/manage/permission.html",
        controller : "PermissionController"
    })
    
    .when("/report_summary_room_conference", {
        templateUrl : "html/report/summary_room_conference.html",
        controller : "ReportSummaryRoomConferenceController"
    })

    .when("/report_summary_room", {
        templateUrl : "html/report/summary_room.html",
        controller : "ReportSummaryRoomController"
    })

    .when("/report_detail_room", {
        templateUrl : "html/report/detail_room.html",
        controller : "ReportDetailRoomController"
    })

    .when("/report_summary_car", {
        templateUrl : "html/report/summary_car.html",
        controller : "ReportSummaryCarController"
    })

    .when("/report_detail_car", {
        templateUrl : "html/report/detail_car.html",
        controller : "ReportDetailCarController"
    })

    .when("/report_summary_repair", {
        templateUrl : "html/report/summary_repair.html",
        controller : "ReportSummaryRepairController"
    })

    .when("/report_detail_repair", {
        templateUrl : "html/report/detail_repair.html",
        controller : "ReportDetailRepairController"
    })

    .when("/link_permission/:linkID", {
        templateUrl : "html/link/link_permission.html",
        controller : "LinkPermissionController"
    })

    .when("/manage_department", {
        templateUrl : "html/manage/department.html",
        controller : "DepartmentManageController"
    })

    .when("/update_phonebook_permission/:PhoneBookID", {
        templateUrl : "html/manage/phonebook_permission.html",
        controller : "PhoneBookPermissionController"
    })

    .when("/manage_log/", {
        templateUrl : "html/manage/log.html",
        controller : "LogManageController"
    })

    .when("/manage_setting/", {
        templateUrl : "html/manage/manage_setting.html",
        controller : "SettingManageController"
    })

    .when("/rule/", {
        templateUrl : "html/rule.html",
        controller : "RuleController"
    })

    .when("/manage_person_region/", {
        templateUrl : "html/manage/person_region.html",
        controller : "PersonRegionController"
    })

    ;
    
});