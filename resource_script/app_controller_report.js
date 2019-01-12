app.controller('ReportSummaryRepairController', function($scope, $filter, IndexOverlayFactory, ReportFactory, RepairFactory) {
	IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'report_summary_repair';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
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

    // Validate zone
    $scope.filterMenu = function(MenuID){
        //console.log('MenuID = ', MenuID);
        $scope.MenuList = angular.fromJson(sessionStorage.getItem('MenuList'));
        var result = false;
        if(MenuID == '6'){
            result = $filter('MenuFilter')($scope.MenuList, 6);     
            result = $filter('MenuFilter')($scope.MenuList, 7);     
        }else{
            result = $filter('MenuFilter')($scope.MenuList, MenuID);
        }
        
        return result;
    }
    if(!$scope.filterMenu(7)){
        alert('ไม่มีสิทธิ์เข้าใช้งานในหน้านี้');
        window.location.replace('#/');
    }


    $scope.refresh = function(){
        $scope.condition = {'report_type':'summary_repair','Year':null};

        $scope.getRepairType();
        $scope.getRepairTitle();
        $scope.getRepairIssue();
        $scope.getRepairSubIssue();
        $scope.DataList = null;
        $scope.Summary = null;
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

    $scope.setRepairType = function(RepairedTypeID){
        console.log(RepairedTypeID);
        $scope.RepairTititleList = [];
        $scope.RepairIssueList = [];
        $scope.RepairSubIssueList = [];
        // load Title
        if(RepairedTypeID != null){
            $scope.getRepairTitle(RepairedTypeID);
        }   
    }

    $scope.setRepairTitle = function(RepairedTitleID){
        console.log(RepairedTitleID);
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
    $scope.queryReport = function (condition){
    	IndexOverlayFactory.overlayShow();
        var con = angular.copy(condition);
        if(con.StartDate != null && con.StartDate != undefined && con.StartDate != ''){
            con.StartDate = makeSQLDate(con.StartDate);
        }
        if(con.EndDate != null && con.EndDate != undefined && con.EndDate != ''){
            con.EndDate = makeSQLDate(con.EndDate);
        }
        ReportFactory.queryReport(con).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.DataList = result.data.DATA.result;
                $scope.Summary = result.data.DATA.summary;
            }
        });
    }

    $scope.exportToExcel = function (condition, data, summary){
        IndexOverlayFactory.overlayShow();
    	ReportFactory.exportExcel(condition, data, summary).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
    			window.location.href="downloads/" + result.data.DATA;
            }
        });
    }

    $scope.exportToPDF = function(condition, data, summary) {
        IndexOverlayFactory.overlayShow();
    	var data_detail = [];
    	var headerRow = [];
    	// set header 
        headerRow.push({text: 'ประเภทงานซ่อม', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'หัวข้องานซ่อม', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'ปัญหางานซ่อม', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});                
    	headerRow.push({text: 'ปัญหาย่อยงานซ่อม', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
		headerRow.push({text: 'จำนวนที่รับแจ้ง', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});	
		headerRow.push({text: 'ซ่อมเสร็จสิ้น', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
		headerRow.push({text: 'ระงับชั่วคราว', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
		headerRow.push({text: 'ยกเลิกงานซ่อม', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
		headerRow.push({text: 'ผ่าน SLA', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
		headerRow.push({text: 'ไม่ผ่าน SLA', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
		data_detail.push(headerRow)

		// set detail
		data.forEach(function(sourceRow) {
		  var dataRow = [];
          dataRow.push(sourceRow.RepairedTypeName);
          dataRow.push(sourceRow.RepairedTitleName);
          dataRow.push(sourceRow.RepairedIssueName);
		  dataRow.push(sourceRow.RepairedSubIssueName);
		  dataRow.push(sourceRow.countTotal);
		  dataRow.push(sourceRow.countFinish);
		  dataRow.push(sourceRow.countHold);
		  dataRow.push(sourceRow.countCancel);
		  dataRow.push(sourceRow.countSLAPass);
		  dataRow.push(sourceRow.countSLAFailed);
		  data_detail.push(dataRow)
		});

		// set summary
		var summaryRow = [];
		summaryRow.push({text: summary.name ,alignment: 'center', colSpan: 4});
        // summaryRow.push(summary.name);
        summaryRow.push('');
        summaryRow.push('');
        summaryRow.push('');
		summaryRow.push(summary.sumTotal);
		summaryRow.push(summary.sumFinish);
		summaryRow.push(summary.sumHold);
		summaryRow.push(summary.sumCancel);
		summaryRow.push(summary.sumPassSLA);
		summaryRow.push(summary.sumFailSLA);
		data_detail.push(summaryRow);

    	//return ;
    	pdfMake.fonts = {
            SriSuriwongse: {
                
                normal: 'SRISURYWONGSE.ttf'
                ,bold: 'SRISURYWONGSE-Bold.ttf'
            }
        };
        
        var dd = {
		    content: [
		    	{text: 'สรุปการแจ้งซ่อม ประจำปีงบประมาณ ' + (condition.Year + 543), style: 'header', alignment:'center',margin: [0,10,0,0]},
		        {
		             table: {
		             	headerRows: 1,
				        widths: [ 80,80,80,80,70,70,70,70, 50,50],
		                body: data_detail
		            }
		        }
		    ],
            styles: {
                header: {
                    bold: true,
                    fontSize: 18
                }
            },
            defaultStyle: {
                fontSize: 12,
                font:'SriSuriwongse'
            },
            pageOrientation: 'landscape'
		}

		 pdfMake.createPdf(dd).download('summary_repair.pdf');
         IndexOverlayFactory.overlayHide();
    }

    // Prepare variable
    $scope.YearList = getYearList(100);
    var d = new Date();
    //console.log(d.getFullYear());
    $scope.refresh();

    IndexOverlayFactory.overlayHide();

});

app.controller('ReportSummaryRepairCaseController', function($scope, $filter, IndexOverlayFactory, ReportFactory, RepairFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'report_summary_repair_case';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
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

    // Validate zone
    $scope.filterMenu = function(MenuID){
        //console.log('MenuID = ', MenuID);
        $scope.MenuList = angular.fromJson(sessionStorage.getItem('MenuList'));
        var result = false;
        if(MenuID == '6'){
            result = $filter('MenuFilter')($scope.MenuList, 6);     
            result = $filter('MenuFilter')($scope.MenuList, 7);     
        }else{
            result = $filter('MenuFilter')($scope.MenuList, MenuID);
        }
        
        return result;
    }
    if(!$scope.filterMenu(7)){
        alert('ไม่มีสิทธิ์เข้าใช้งานในหน้านี้');
        window.location.replace('#/');
    }


    $scope.refresh = function(){
        $scope.condition = {'report_type':'summary_repair_case','Year':null};

        $scope.getRepairType();
        $scope.getRepairTitle();
        $scope.getRepairIssue();
        $scope.getRepairSubIssue();
        $scope.DataList = null;
        $scope.Summary = null;
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

    $scope.setRepairType = function(RepairedTypeID){
        console.log(RepairedTypeID);
        $scope.RepairTititleList = [];
        $scope.RepairIssueList = [];
        $scope.RepairSubIssueList = [];
        // load Title
        if(RepairedTypeID != null){
            $scope.getRepairTitle(RepairedTypeID);
        }   
    }

    $scope.setRepairTitle = function(RepairedTitleID){
        console.log(RepairedTitleID);
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
    $scope.queryReport = function (condition){
        IndexOverlayFactory.overlayShow();
        var con = angular.copy(condition);
        if(con.StartDate != null && con.StartDate != undefined && con.StartDate != ''){
            con.StartDate = makeSQLDate(con.StartDate);
        }
        if(con.EndDate != null && con.EndDate != undefined && con.EndDate != ''){
            con.EndDate = makeSQLDate(con.EndDate);
        }
        ReportFactory.queryReport(con).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.DataList = result.data.DATA.result;
                for(var i = 0; i < $scope.DataList.length; i++){
                    $scope.DataList[i].RepairedStatus = $scope.getRepairedStatusTH($scope.DataList[i].RepairedStatus);
                    $scope.DataList[i].SLAStatus = $scope.getSLAStatusTH($scope.DataList[i].SLAStatus);
                }
            }
        });
    }

    $scope.getRepairedStatusTH = function(status){
        if(status == 'Suspend'){
            return 'ระงับการซ่อม';
        }else if(status == 'Finish'){
            return 'ซ่อมเสร็จสิ้น';
        }else if(status == 'Cancel'){
            return 'ยกเลิกการซ่อม';
        }

    }

    $scope.getSLAStatusTH = function(status){
        if(status == 1){
            return 'ผ่าน';
        }else if(status == 0){
            return 'ไม่ผ่าน';
        }else{
            return '';
        }
    }

    $scope.exportToExcel = function (condition, data, summary){
        IndexOverlayFactory.overlayShow();
        ReportFactory.exportExcel(condition, data, summary).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                window.location.href="downloads/" + result.data.DATA;
            }
        });
    }

    $scope.exportToPDF = function(condition, data) {

        var dateRange = convertDateToFullThaiDateIgnoreTime(condition.StartDate) + ' ถึง ' + convertDateToFullThaiDateIgnoreTime(condition.EndDate);

        IndexOverlayFactory.overlayShow();
        var data_detail = [];
        var headerRow = [];
        // set header 
        headerRow.push({text: 'วันที่', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'รหัสแจ้งซ่อม', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'ประเภทงานซ่อม', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'หัวข้องานซ่อม', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'ปัญหางานซ่อม', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});                
        headerRow.push({text: 'ปัญหาย่อยงานซ่อม', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'สถานะงานซ่อม', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true}); 
        headerRow.push({text: 'ผ่าน / ไม่ผ่าน SLA', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'ผู้อนุมัติ', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true}); 
        data_detail.push(headerRow)

        // set detail
        data.forEach(function(sourceRow) {
          var dataRow = [];
          dataRow.push(convertSQLDateTimeToReportDate(sourceRow.CreateDateTime));
          dataRow.push(sourceRow.RepairedCode);
          dataRow.push(sourceRow.RepairedTypeName);
          dataRow.push(sourceRow.RepairedTitleName);
          dataRow.push(sourceRow.RepairedIssueName);
          dataRow.push(sourceRow.RepairedSubIssueName);
          dataRow.push(sourceRow.RepairedStatus);
          dataRow.push(sourceRow.SLAStatus==null?'':sourceRow.SLAStatus);
          dataRow.push(sourceRow.ApproveName);
          data_detail.push(dataRow)
        });
        console.log(data_detail);
        
        //return ;
        pdfMake.fonts = {
            SriSuriwongse: {
                
                normal: 'SRISURYWONGSE.ttf'
                ,bold: 'SRISURYWONGSE-Bold.ttf'
            }
        };
        
        var dd = {
            content: [
                {text: 'สรุปการแจ้งซ่อม ช่วงวันที่ ' + dateRange , style: 'header', alignment:'center',margin: [0,10,0,0]},
                {
                     table: {
                        headerRows: 1,
                        widths: [70,70, 80,80, 80, 80,70,70, 60],
                        body: data_detail
                    }
                }
            ],
            styles: {
                header: {
                    bold: true,
                    fontSize: 18
                }
            },
            defaultStyle: {
                fontSize: 12,
                font:'SriSuriwongse'
            },
            pageOrientation: 'landscape'
        }

         pdfMake.createPdf(dd).download('summary_repair_case.pdf');
         IndexOverlayFactory.overlayHide();
    }

    // Prepare variable
    $scope.YearList = getYearList(100);
    var d = new Date();
    //console.log(d.getFullYear());
    $scope.refresh();

    IndexOverlayFactory.overlayHide();

});

app.controller('ReportDetailRoomController', function($scope, $filter, IndexOverlayFactory, ReportFactory, RegionFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'report_detail_room';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }

    // Validate zone
    $scope.filterMenu = function(MenuID){
        //console.log('MenuID = ', MenuID);
        $scope.MenuList = angular.fromJson(sessionStorage.getItem('MenuList'));
        var result = false;
        if(MenuID == '6'){
            result = $filter('MenuFilter')($scope.MenuList, 6);     
            result = $filter('MenuFilter')($scope.MenuList, 7);     
        }else{
            result = $filter('MenuFilter')($scope.MenuList, MenuID);
        }
        
        return result;
    }
    if(!$scope.filterMenu(2)){
        alert('ไม่มีสิทธิ์เข้าใช้งานในหน้านี้');
        window.location.replace('#/');
    }

    $scope.changeRegion = function(region){
        $scope.loadRoomListByRegion(region.RegionID);
    }


    $scope.loadRegionList = function () {
        RegionFactory.getAllRegion().then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.RegionList = result.data.DATA;
            }
        });
    }

    $scope.loadRoomListByRegion = function (regionID) {
        ReportFactory.loadRoomListByRegion(regionID).then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.RoomList = result.data.DATA;
            }
        });
    }

    $scope.queryReport = function (condition){
        IndexOverlayFactory.overlayShow();
        ReportFactory.queryReport(condition).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.DataList = result.data.DATA;
                for(var i=0 ; i < $scope.DataList.length; i++){
                    $scope.DataList[i].StartDateTime = convertSQLDateTimeToReportDateTime($scope.DataList[i].StartDateTime);
                    $scope.DataList[i].EndDateTime = convertSQLDateTimeToReportDateTime($scope.DataList[i].EndDateTime);
                }
            }
        });
    }

    $scope.exportToExcel = function (condition, data, summary){
        IndexOverlayFactory.overlayShow();
        ReportFactory.exportExcel(condition, data, summary).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                window.location.href="downloads/" + result.data.DATA;
            }
        });
    }

    $scope.exportToPDF = function(condition, data, summary) {

        function getFoodText(data){
            var str = '';
            for(var i = 0; i < data.length; i++){
                str += data[i].FoodName + '       ' + data[i].Amount + '\n';
            }
            return str;
        }

        function getDeviceText(data){
            var str = '';
            for(var i = 0; i < data.length; i++){
                str += data[i].DeviceName + '       ' + data[i].Amount + '\n';
            }
            return str;
        }

        IndexOverlayFactory.overlayShow();
        var data_detail = [];
        var headerRow = [];
        // set header
        //headerRow.push('ห้องประชุม');
        //headerRow.push({text: 'ห้องประชุม', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        //headerRow.push({text: 'พื้นที่', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});  
        headerRow.push({text: 'ลำดับ', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});  
        headerRow.push({text: 'ผู้ทำการจอง', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});  
        headerRow.push({text: 'เริ่มประชุม\nวัน / เวลา', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'สิ้นสุดประชุม\nวัน / เวลา', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'อุปกรณ์', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'อาหาร', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'หัวข้อการประชุม', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        data_detail.push(headerRow);
        
        // set detail
        var row_index = 1;
        data.forEach(function(sourceRow) {
          var dataRow = [];
          //dataRow.push(sourceRow.RoomName);
          //dataRow.push(sourceRow.RegionName);
          dataRow.push(row_index);
          dataRow.push(sourceRow.FirstName + ' ' + sourceRow.LastName);
          dataRow.push({text: sourceRow.StartDateTime, alignment: 'center'});
          dataRow.push({text: sourceRow.EndDateTime, alignment: 'center'});
          dataRow.push({text: getDeviceText(sourceRow.device), alignment: 'left'});
          dataRow.push({text: getFoodText(sourceRow.food), alignment: 'left'});
          dataRow.push(sourceRow.TopicConference);
          data_detail.push(dataRow);
          row_index++;
        });

        //return ;
        pdfMake.fonts = {
            SriSuriwongse: {
                
                normal: 'SRISURYWONGSE.ttf'
                ,bold: 'SRISURYWONGSE-Bold.ttf'
            }
        };
        
        var dd = {
            content: [
                {text: 'ตารางการใช้งานห้องประชุม ', style: 'header', alignment:'center',margin: [0,10,0,0]},
                {text: 'ห้องประชุม ' + (condition.Room.RoomName) + ' พื้นที่ ' + (condition.Region.RegionName), style: 'header', alignment:'center',margin: [0,10,0,0]},
                {
                     table: {
                        headerRows: 1,
                        widths: [30, 110,90,90, 110, 110,150],
                        body: data_detail
                    }
                }
            ],
            styles: {
                header: {
                    bold: true,
                    fontSize: 18
                }
            },
            defaultStyle: {
                fontSize: 12,
                font:'SriSuriwongse'
            },pageOrientation: 'landscape'
        }

         pdfMake.createPdf(dd).download('detail_room.pdf');
         IndexOverlayFactory.overlayHide();
    }

    // Prepare variable
    $scope.loadRegionList();
    $scope.MonthList = getThaiMonth();
    $scope.YearList = getYearList(100);
    var d = new Date();
    $scope.condition = {'report_type':'detail_room', 'RegionID':null, 'RoomID':null, 'Month':null, 'Year':null, 'RegionName':null, 'RoomName':null, 'MonthName':null, 'YearName':null};

    IndexOverlayFactory.overlayHide();



});

app.controller('ReportSummaryRoomController', function($scope, $filter, $uibModal, IndexOverlayFactory, ReportFactory, RegionFactory, HTTPFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'report_summary_room';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
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

    $scope.getTotalUse = function(){
        var totalUse = 0;
        for(var i = 0; i < $scope.DataList.length; i++){
            totalUse += $scope.DataList[i].CountUseRoom;
        }
        return totalUse;
    }

    // Validate zone
    $scope.filterMenu = function(MenuID){
        //console.log('MenuID = ', MenuID);
        $scope.MenuList = angular.fromJson(sessionStorage.getItem('MenuList'));
        var result = false;
        if(MenuID == '2'){
            result = $filter('MenuFilter')($scope.MenuList, 6);     
            result = $filter('MenuFilter')($scope.MenuList, 7);     
        }else{
            result = $filter('MenuFilter')($scope.MenuList, MenuID);
        }
        
        return result;
    }
    if(!$scope.filterMenu(7)){
        alert('ไม่มีสิทธิ์เข้าใช้งานในหน้านี้');
        window.location.replace('#/');
    }

    $scope.loadRegionList = function () {
        RegionFactory.getAllRegion().then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.RegionList = result.data.DATA;
            }
        });
    }

    $scope.queryReport = function (condition){
        //IndexOverlayFactory.overlayShow();
        var con = angular.copy(condition);
        if(con.StartDate != null && con.StartDate != undefined && con.StartDate != ''){
            con.StartDate = makeSQLDate(con.StartDate);
        }
        if(con.EndDate != null && con.EndDate != undefined && con.EndDate != ''){
            con.EndDate = makeSQLDate(con.EndDate);
        }
        ReportFactory.queryReport(con).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.DataList = result.data.DATA;
            }
        });
    }

    $scope.exportToExcel = function (condition, data, summary){
        IndexOverlayFactory.overlayShow();
        ReportFactory.exportExcel(condition, data, summary).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                window.location.href="downloads/" + result.data.DATA;
            }
        });
    }

    $scope.exportToPDF = function(condition, data, summary) {
        IndexOverlayFactory.overlayShow();
        var data_detail = [];
        var headerRow = [];
        // set header
        //headerRow.push('ห้องประชุม');
        headerRow.push({text: 'ห้องประชุม', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'พื้นที่', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});  
        headerRow.push({text: 'จำนวนครั้งที่ใช้งาน', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        // headerRow.push({text: 'ปี', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        data_detail.push(headerRow);
        
        // set detail
        var row_index = 1;
        data.forEach(function(sourceRow) {
          var dataRow = [];
          dataRow.push(sourceRow.RoomName);
          dataRow.push(sourceRow.RegionName);
          dataRow.push({text: sourceRow.CountUseRoom, alignment: 'center'});
          // dataRow.push({text: sourceRow.ReportYear, alignment: 'center'});
          data_detail.push(dataRow);
          row_index++;
        });

        //return ;
        pdfMake.fonts = {
            SriSuriwongse: {
                
                normal: 'SRISURYWONGSE.ttf'
                ,bold: 'SRISURYWONGSE-Bold.ttf'
            }
        };
        
        var dd = {
            content: [
                {text: 'สรุปการใช้ห้องประชุมประจำปี ', style: 'header', alignment:'center',margin: [0,10,0,0]},
                {
                     table: {
                        headerRows: 1,
                        widths: [170,150,100],
                        body: data_detail
                    }
                }
            ],
            styles: {
                header: {
                    bold: true,
                    fontSize: 18
                }
            },
            defaultStyle: {
                fontSize: 12,
                font:'SriSuriwongse'
            }
        }

         pdfMake.createPdf(dd).download('summary_room.pdf');
         IndexOverlayFactory.overlayHide();
    }

    // Prepare variable
    $scope.loadRegionList();
    $scope.MonthList = getThaiMonth();
    $scope.YearList = getYearList(100);
    var d = new Date();
    $scope.DataList = [];
    $scope.condition = {'report_type':'summary_room', 'RegionID':null, 'RoomID':null, 'Month':null, 'Year':null, 'RegionName':null, 'RoomName':null, 'MonthName':null, 'YearName':null};

    IndexOverlayFactory.overlayHide();

    $scope.viewDetail = function(RoomID, RoomName, TotalUse, condition){
        $scope.RoomName = RoomName;
        $scope.TotalUse = TotalUse;
        var con = angular.copy(condition);
        if(con.StartDate != null && con.StartDate != undefined && con.StartDate != ''){
            con.StartDate = makeSQLDate(con.StartDate);
        }
        if(con.EndDate != null && con.EndDate != undefined && con.EndDate != ''){
            con.EndDate = makeSQLDate(con.EndDate);
        }
        
        var params = {'RoomID' : RoomID, 'condition' : con};
        HTTPFactory.clientRequest('report/room/detail', params).then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.RoomDetail = result.data.DATA.List;

                var modalInstance = $uibModal.open({
                    animation: true,
                    templateUrl: 'room_detail.html',
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
                    // $scope.updateProfile(valResult);
                });

            }
        });
    }

});

app.controller('ReportDetailCarController', function($scope, $filter, IndexOverlayFactory, ReportFactory, RegionFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'report_detail_car';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }

    // Validate zone
    $scope.filterMenu = function(MenuID){
        //console.log('MenuID = ', MenuID);
        $scope.MenuList = angular.fromJson(sessionStorage.getItem('MenuList'));
        var result = false;
        if(MenuID == '6'){
            result = $filter('MenuFilter')($scope.MenuList, 6);     
            result = $filter('MenuFilter')($scope.MenuList, 7);     
        }else{
            result = $filter('MenuFilter')($scope.MenuList, MenuID);
        }
        
        return result;
    }
    if(!$scope.filterMenu(3)){
        alert('ไม่มีสิทธิ์เข้าใช้งานในหน้านี้');
        window.location.replace('#/');
    }

    $scope.changeRegion = function(region){
        $scope.loadCarListByRegion(region.RegionID);
    }


    $scope.loadRegionList = function () {
        RegionFactory.getAllRegion().then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.RegionList = result.data.DATA;
            }
        });
    }

    $scope.loadCarListByRegion = function (regionID) {
        ReportFactory.loadCarListByRegion(regionID).then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.CarList = result.data.DATA;
            }
        });
    }

    $scope.queryReport = function (condition){
        //IndexOverlayFactory.overlayShow();
        ReportFactory.queryReport(condition).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.DataList = result.data.DATA;
                for(var i=0 ; i < $scope.DataList.length; i++){
                    $scope.DataList[i].StartDateTime = convertSQLDateTimeToReportDateTime($scope.DataList[i].StartDateTime);
                    $scope.DataList[i].EndDateTime = convertSQLDateTimeToReportDateTime($scope.DataList[i].EndDateTime);
                }
            }
        });
    }

    $scope.exportToExcel = function (condition, data, summary){
        IndexOverlayFactory.overlayShow();
        ReportFactory.exportExcel(condition, data, summary).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                window.location.href="downloads/" + result.data.DATA;
            }
        });
    }

    $scope.exportToPDF = function(condition, data, summary) {
        IndexOverlayFactory.overlayShow();
        var data_detail = [];
        var headerRow = [];
        // set header
        headerRow.push({text: 'ลำดับ', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});  
        headerRow.push({text: 'เดินทางไป\nวัน / เวลา', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'ผู้ใช้รถ', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});  
        headerRow.push({text: 'สถานที่ไป', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'เดินทางกลับ\nวัน / เวลา', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'พนักงานขับรถ', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'หมายเหตุ', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        data_detail.push(headerRow);
        
        // set detail
        var row_index = 1;
        data.forEach(function(sourceRow) {
          var dataRow = [];
          dataRow.push(row_index);
          dataRow.push({text: sourceRow.StartDateTime, alignment: 'center'});
          dataRow.push(sourceRow.FirstName + ' ' + sourceRow.LastName);
          dataRow.push(sourceRow.Destination);
          dataRow.push({text: sourceRow.EndDateTime, alignment: 'center'});
          dataRow.push(sourceRow.DriverType == 'Internal'? sourceRow.DriverFirstName + ' ' + sourceRow.DriverLastName : sourceRow.DriverName  );
          dataRow.push(sourceRow.Remark);
          data_detail.push(dataRow);
          row_index++;
        });

        //return ;
        pdfMake.fonts = {
            SriSuriwongse: {
                
                normal: 'SRISURYWONGSE.ttf'
                ,bold: 'SRISURYWONGSE-Bold.ttf'
            }
        };
        
        var dd = {
            content: [
                {text: 'ตารางบันทึกการใช้ยานพาหนะ', style: 'header', alignment:'center',margin: [0,10,0,0]},
                {text: 'รถหมายเลขทะเบียน ' + (condition.Car.License), style: 'header', alignment:'center',margin: [0,10,0,0]},
                {
                     table: {
                        headerRows: 1,
                        widths: [25, 50, 80, 80, 50, 80, 80],
                        body: data_detail
                    }
                }
            ],
            styles: {
                header: {
                    bold: true,
                    fontSize: 18
                }
            },
            defaultStyle: {
                fontSize: 12,
                font:'SriSuriwongse'
            }
        }

         pdfMake.createPdf(dd).download('detail_car.pdf');
         IndexOverlayFactory.overlayHide();
    }

    // Prepare variable
    $scope.loadRegionList();
    $scope.MonthList = getThaiMonth();
    $scope.YearList = getYearList(100);
    var d = new Date();
    $scope.condition = {'report_type':'detail_car', 'RegionID':null, 'RoomID':null, 'Month':null, 'Year':null, 'RegionName':null, 'RoomName':null, 'MonthName':null, 'YearName':null};

    IndexOverlayFactory.overlayHide();

});

app.controller('ReportSummaryCarController', function($scope, $filter, IndexOverlayFactory, ReportFactory, RegionFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'report_summary_car';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }

    // Validate zone
    $scope.filterMenu = function(MenuID){
        //console.log('MenuID = ', MenuID);
        $scope.MenuList = angular.fromJson(sessionStorage.getItem('MenuList'));
        var result = false;
        if(MenuID == '6'){
            result = $filter('MenuFilter')($scope.MenuList, 6);     
            result = $filter('MenuFilter')($scope.MenuList, 7);     
        }else{
            result = $filter('MenuFilter')($scope.MenuList, MenuID);
        }
        
        return result;
    }
    if(!$scope.filterMenu(3)){
        alert('ไม่มีสิทธิ์เข้าใช้งานในหน้านี้');
        window.location.replace('#/');
    }

    $scope.loadRegionList = function () {
        RegionFactory.getAllRegion().then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.RegionList = result.data.DATA;
            }
        });
    }

    $scope.queryReport = function (condition){
        //IndexOverlayFactory.overlayShow();
        ReportFactory.queryReport(condition).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.DataList = result.data.DATA;
            }
        });
    }

    $scope.exportToExcel = function (condition, data, summary){
        IndexOverlayFactory.overlayShow();
        ReportFactory.exportExcel(condition, data, summary).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                window.location.href="downloads/" + result.data.DATA;
            }
        });
    }

    $scope.exportToPDF = function(condition, data, summary) {
        IndexOverlayFactory.overlayShow();
        var data_detail = [];
        var headerRow = [];
        // set header
        headerRow.push({text: 'ลำดับ', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'ยานพาหนะ', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'ทะเบียนรถ', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});  
        headerRow.push({text: 'พื้นที่', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});  
        headerRow.push({text: 'จำนวนครั้งที่ใช้งาน', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'ปี', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        data_detail.push(headerRow);
        
        // set detail
        var row_index = 1;
        data.forEach(function(sourceRow) {
          var dataRow = [];
          dataRow.push(row_index);
          dataRow.push(sourceRow.CarName);
          dataRow.push(sourceRow.License);
          dataRow.push(sourceRow.RegionName);
          dataRow.push({text: sourceRow.CountUseCar, alignment: 'center'});
          dataRow.push({text: sourceRow.ReportYear, alignment: 'center'});
          data_detail.push(dataRow);
          row_index++;
        });

        //return ;
        pdfMake.fonts = {
            SriSuriwongse: {
                
                normal: 'SRISURYWONGSE.ttf'
                ,bold: 'SRISURYWONGSE-Bold.ttf'
            }
        };
        
        var dd = {
            content: [
                {text: 'สรุปการใช้ยานพาหนะประจำปี ', style: 'header', alignment:'center',margin: [0,10,0,0]},
                {
                     table: {
                        headerRows: 1,
                        widths: [30, 120, 60, 130, 70, 50],
                        body: data_detail
                    }
                }
            ],
            styles: {
                header: {
                    bold: true,
                    fontSize: 18
                }
            },
            defaultStyle: {
                fontSize: 12,
                font:'SriSuriwongse'
            }
        }

         pdfMake.createPdf(dd).download('summary_car.pdf');
         IndexOverlayFactory.overlayHide();
    }

    // Prepare variable
    $scope.loadRegionList();
    $scope.MonthList = getThaiMonth();
    $scope.YearList = getYearList(100);
    var d = new Date();
    $scope.condition = {'report_type':'summary_car', 'RegionID':null, 'RoomID':null, 'Month':null, 'Year':null, 'RegionName':null, 'RoomName':null, 'MonthName':null, 'YearName':null};

    IndexOverlayFactory.overlayHide();

});

app.controller('ReportDetailRepairController', function($scope, $uibModal, $filter, IndexOverlayFactory, ReportFactory, RegionFactory, DepartmentFactory, RepairFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'report_detail_repair';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }

    // Validate zone
    $scope.filterMenu = function(MenuID){
        //console.log('MenuID = ', MenuID);
        $scope.MenuList = angular.fromJson(sessionStorage.getItem('MenuList'));
        var result = false;
        if(MenuID == '6'){
            result = $filter('MenuFilter')($scope.MenuList, 6);     
            result = $filter('MenuFilter')($scope.MenuList, 7);     
        }else{
            result = $filter('MenuFilter')($scope.MenuList, MenuID);
        }
        
        return result;
    }
    if(!$scope.filterMenu(7)){
        alert('ไม่มีสิทธิ์เข้าใช้งานในหน้านี้');
        window.location.replace('#/');
    }

    $scope.changeRegion = function(region){
        $scope.loadUserListByRegionAndRole(region.RegionID);
    }

    $scope.loadRegionList = function () {
        RegionFactory.getAllRegion().then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.RegionList = result.data.DATA;
            }
        });
    }

    $scope.loadRepairedTypeList = function () {
        RepairFactory.getRepairTypeList('view', $scope.currentUser.UserID).then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.RepairedTypeList = result.data.DATA.DataList;
            }
        });
    }

    $scope.loadUserListByRegionAndRole = function (regionID) {
        ReportFactory.loadUserListByRegionAndRole(regionID).then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.UserList = result.data.DATA;
            }
        });
    }

    $scope.queryReport = function (condition){
        // IndexOverlayFactory.overlayShow();
        ReportFactory.queryReport(condition).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.DataList = result.data.DATA;
                for(var i=0 ; i < $scope.DataList.length; i++){
                    $scope.DataList[i].RepairedStatus = $scope.getStatusName($scope.DataList[i].RepairedStatus);
                    $scope.DataList[i].CreateDateTime = convertSQLDateTimeToReportDateTime($scope.DataList[i].CreateDateTime);
                    if($scope.DataList[i].ReceiveDateTime != null){
                        $scope.DataList[i].ReceiveDateTime = convertSQLDateTimeToReportDateTime($scope.DataList[i].ReceiveDateTime);
                    }
                }
            }
        });
    }

    $scope.viewDetail = function (data){
        $scope.Repair = data;
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'view_repair.html',
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
    };

    $scope.getStatusName = function(statusName){
        switch(statusName){
            case 'Request' : return 'รอการดำเนินการ';
            case 'Process' : return 'กำลังดำเนินการ';
            case 'Cancel' : return 'ยกเลิก';
            case 'Finish' : return 'เสร็จสิ้น';
            case 'Suspend' : return 'ระงับชั่วคราว';
            default : return '';
        }
    }

    $scope.exportToExcel = function (condition, data, summary){
        IndexOverlayFactory.overlayShow();
        ReportFactory.exportExcel(condition, data, summary).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                window.location.href="downloads/" + result.data.DATA;
            }
        });
    }

    $scope.exportToPDF = function(condition, data, summary) {
        IndexOverlayFactory.overlayShow();
        var data_detail = [];
        var headerRow = [];
        // set header
        //headerRow.push('ห้องประชุม');
        //headerRow.push({text: 'ห้องประชุม', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        //headerRow.push({text: 'พื้นที่', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});  
        //headerRow.push({text: 'ลำดับ', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});  
        headerRow.push({text: 'รหัสแจ้งซ่อม', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});  
        headerRow.push({text: 'ปัญหาย่อยงานซ่อม', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'หน่วยงาน', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'วันที่แจ้ง', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'ผู้รับเรื่อง', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'SLA', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'สถานะ', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        data_detail.push(headerRow);
        
        // set detail
        var row_index = 1;
        data.forEach(function(sourceRow) {
          var dataRow = [];
          console.log(sourceRow.RecieverFirstName);
          //dataRow.push(sourceRow.RoomName);
          //dataRow.push(sourceRow.RegionName);
          // dataRow.push(row_index);
          var recvname = sourceRow.RecieverFirstName + ' ' + sourceRow.RecieverLastName;
          dataRow.push(sourceRow.RepairedCode);
          dataRow.push(sourceRow.RepairedSubIssueName);
          dataRow.push(sourceRow.GroupName);
          dataRow.push({text: sourceRow.CreateDateTime, alignment: 'center'});
          dataRow.push(recvname);//sourceRow.RecieverFirstName + ' ' + sourceRow.RecieverLastName==null?'':sourceRow.RecieverLastName);
          dataRow.push(sourceRow.RepairedStatus == 'เสร็จสิ้น'?sourceRow.SLAStatus == 1?'ผ่าน':'ไม่ผ่าน':'-');
          dataRow.push(sourceRow.RepairedStatus);
          data_detail.push(dataRow);
          row_index++;
        });

        //return ;
        pdfMake.fonts = {
            SriSuriwongse: {
                
                normal: 'SRISURYWONGSE.ttf'
                ,bold: 'SRISURYWONGSE-Bold.ttf'
            }
        };
        
        
        var textHeader = '';
        if($scope.condition.Month != null &&  $scope.condition.Day != null){
            var reportDate = $scope.condition.Year + '-' + $scope.condition.Month.monthText + '-' + $scope.condition.Day.dayText;
            textHeader = 'ประจำวันที่ ' + convertSQLDateTimeToReportDate(reportDate);
        }
        else if($scope.condition.Month != null &&  $scope.condition.Day == null){
            textHeader = $scope.condition.Month.monthText + '/' + (parseInt($scope.condition.Year) + 543);
        }
        else{
            textHeader = 'ปี ' + (parseInt($scope.condition.Year) + 543);
        }
        var dd = {
            content: [
                {text: 'ตารางการแจ้งซ่อม ', style: 'header', alignment:'center',margin: [0,10,0,0]},
                {text: textHeader, style: 'header', alignment:'center',margin: [0,10,0,0]},
                {
                     table: {
                        headerRows: 1,
                        widths: [60, 75,80,55,65,40,65],
                        body: data_detail
                    }
                }
            ],
            styles: {
                header: {
                    bold: true,
                    fontSize: 18
                }
            },
            defaultStyle: {
                fontSize: 12,
                font:'SriSuriwongse'
            }
        }

         pdfMake.createPdf(dd).download('detail_room.pdf');
         IndexOverlayFactory.overlayHide();
    }

    // Prepare variable
    $scope.loadRepairedTypeList();
    $scope.loadRegionList();
    $scope.DayList = getTotalDayInMonth(0,0);
    $scope.MonthList = getThaiMonth();
    $scope.YearList = getYearList(100);
    var d = new Date();
    $scope.condition = {'report_type':'detail_repair', 'RegionID':null, 'RepairedtTypeID':null ,'UserID':null, 'Day':null, 'Month':null, 'Year':null, 'RegionName':null, 'RoomName':null, 'MonthName':null, 'YearName':null};

    IndexOverlayFactory.overlayHide();

});

app.controller('ReportSummaryRoomConferenceController', function($scope, $filter, IndexOverlayFactory, ReportFactory, RegionFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'report_summary_room_conference';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }

    // Validate zone
    $scope.filterMenu = function(MenuID){
        //console.log('MenuID = ', MenuID);
        $scope.MenuList = angular.fromJson(sessionStorage.getItem('MenuList'));
        var result = false;
        if(MenuID == '2'){
            result = $filter('MenuFilter')($scope.MenuList, 6);     
            result = $filter('MenuFilter')($scope.MenuList, 7);     
        }else{
            result = $filter('MenuFilter')($scope.MenuList, MenuID);
        }
        
        return result;
    }
    if(!$scope.filterMenu(7)){
        alert('ไม่มีสิทธิ์เข้าใช้งานในหน้านี้');
        window.location.replace('#/');
    }

    $scope.loadRegionList = function () {
        RegionFactory.getAllRegion().then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.RegionList = result.data.DATA;
            }
        });
    }

    $scope.loadRoomListByRegion = function (regionID) {
        console.log(regionID);
        ReportFactory.loadRoomListByRegion(regionID).then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.RoomList = result.data.DATA;
            }
        });
    }

    $scope.changeRegion = function(region){
        $scope.loadRoomListByRegion(region.RegionID)
    }

    $scope.queryReport = function (condition){
        //IndexOverlayFactory.overlayShow();
        var con = angular.copy(condition);
        if(con.StartDate != null && con.StartDate != undefined && con.StartDate != ''){
            con.StartDate = makeSQLDate(con.StartDate);
        }
        if(con.EndDate != null && con.EndDate != undefined && con.EndDate != ''){
            con.EndDate = makeSQLDate(con.EndDate);
        }
        ReportFactory.queryReport(con).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.DataList = result.data.DATA;
            }
        });
    }

    $scope.getTotalUse = function(){
        return $scope.DataList.length;
    }

    $scope.exportToExcel = function (condition, data, summary){
        IndexOverlayFactory.overlayShow();
        ReportFactory.exportExcel(condition, data, summary).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                window.location.href="downloads/" + result.data.DATA;
            }
        });
    }

    $scope.exportToPDF = function(condition, data, summary) {
        IndexOverlayFactory.overlayShow();
        var data_detail = [];
        var headerRow = [];
        // set header
        //headerRow.push('ห้องประชุม');
        headerRow.push({text: 'พื้นที่', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});  
        headerRow.push({text: 'ห้องประชุม', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'เริ่มประชุม', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'สิ้นสุดประชุม', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'หัวข้อการประชุม', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'ผู้ทำการจอง', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'ห้องประชุมพื้นที่เชื่อมโยง', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        headerRow.push({text: 'พื้นที่เชื่อมโยง', style: 'tableHeader', /*rowSpan: 2,*/ alignment: 'center', fontSize: 12, bold: true});
        data_detail.push(headerRow);
        
        // set detail
        var row_index = 1;
        data.forEach(function(sourceRow) {
          var dataRow = [];
          var ConRoom = '';
          var ConRegion = '';
          for(var i = 0; i < sourceRow.ConferenceList.length; i++){
            ConRoom += "- " + sourceRow.ConferenceList[i].RoomName + "\n";
            ConRegion += "- " + sourceRow.ConferenceList[i].RegionName + "\n";
          }
          dataRow.push(sourceRow.RegionName);
          dataRow.push(sourceRow.RoomName);
          dataRow.push(sourceRow.StartDateTime);
          dataRow.push(sourceRow.EndDateTime);
          dataRow.push(sourceRow.TopicConference);
          dataRow.push(sourceRow.FirstName + ' ' + sourceRow.LastName);
          dataRow.push(ConRoom);
          dataRow.push(ConRegion);
          data_detail.push(dataRow);
          row_index++;
        });

        //return ;
        pdfMake.fonts = {
            SriSuriwongse: {
                
                normal: 'SRISURYWONGSE.ttf'
                ,bold: 'SRISURYWONGSE-Bold.ttf'
            }
        };
        
        var dd = {
            content: [
                {text: 'รายงานสรุปการใช้งาน ประชุมด้วย Video Conference ', style: 'header', alignment:'center',margin: [0,10,0,0]},
                {
                     table: {
                        headerRows: 1,
                        widths: [100,90,50,50, 100, 50, 100, 90],
                        body: data_detail
                    }
                }
            ],
            styles: {
                header: {
                    bold: true,
                    fontSize: 18
                }
            },
            defaultStyle: {
                fontSize: 12,
                font:'SriSuriwongse'
            },
            pageOrientation: 'landscape' 
        }

         pdfMake.createPdf(dd).download('summary_room_conference.pdf');
         IndexOverlayFactory.overlayHide();
    }

    // Prepare variable
    $scope.loadRegionList();
    $scope.MonthList = getThaiMonth();
    $scope.YearList = getYearList(100);
    var d = new Date();
    $scope.condition = {'report_type':'summary_room_conference'};

    IndexOverlayFactory.overlayHide();

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

});