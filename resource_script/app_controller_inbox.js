app.controller('InboxManageController', function($cookies, $scope, $http, $uibModal, $routeParams, $http, HTTPFactory, IndexOverlayFactory, DepartmentFactory) {
    $scope.$parent.menu_selected = 'manage_inbox';
    IndexOverlayFactory.overlayShow();
    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
        window.location.replace('#/logon/manage_inbox');
    }
    IndexOverlayFactory.overlayHide();

    $scope.loadDepartmentList = function(){
        DepartmentFactory.getAllDepartmentList().then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.DepartmentList = result.data.DATA;
            }
        });
    }

    $scope.loadList = function(){
        if($scope.continueLoad){
            $scope.tableLoad = true;
            var params = {'condition' : $scope.condition, 'offset' : $scope.dataOffset};
            HTTPFactory.clientRequest('inbox/list/manage', params).then(function (result) {
                if (result.data.STATUS == 'OK') {
                    $scope.tableLoad = false;
                    $scope.dataOffset =  result.data.DATA.offset;
                    $scope.continueLoad = result.data.DATA.continueLoad;
                    $scope.DataList = result.data.DATA.DataList;
                    console.log($scope.DataList);
                }
                IndexOverlayFactory.overlayHide();
            });
        }
    }

    $scope.add = function(){
        $scope.Inbox = {'InboxID':'', 'ActiveStatus':'Y', 'CreateBy':$scope.currentUser.UserID};
        $scope.prepareEditor();
        $scope.setImgList();
        $scope.setAttachFileList();
        $scope.PAGE = 'UPDATE';
    }

    $scope.update = function(data){
        $scope.Inbox = angular.copy(data);
        $scope.Inbox.InboxDateTime = $scope.Inbox.InboxDateTime != null?convertDateToSQLString($scope.Inbox.InboxDateTime):$scope.Inbox.InboxDateTime
        $scope.prepareEditor();
        $scope.setImgList();
        $scope.setAttachFileList();

        $scope.PAGE = 'UPDATE';
    }

    $scope.saveData = function(DataObj, fileimg, ImgList, AttachFileList){

        var Data = angular.copy(DataObj);
        
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

                $scope.alertMessage = 'ต้องการบันทึกข่าว ใช่หรือไม่ ?';
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

                    Data.InboxTitle = CKEDITOR.instances.editor_title1.getData();
                    Data.InboxContent = CKEDITOR.instances.editor1.getData();

                    if(Data.InboxDateTime != undefined && Data.InboxDateTime != null && Data.InboxDateTime != ''){
                        Data.InboxDateTime = makeSQLDate(Data.InboxDateTime);
                    }

                    var params = {'img' : fileimg
                            , 'fileimg' : ImgList
                            , 'attachFile' : AttachFileList
                            , 'updateObj': Data
                        };
                    // var params = {'Data' : Data};
                    HTTPFactory.uploadRequest('inbox/update', params).then(function (result) {
                        if (result.data.STATUS == 'OK') {
                            alert('บันทึกสำเร็จ');
                            $scope.Data.InboxID = result.data.DATA.InboxID;
                            $scope.update(result.data.DATA.InboxID);
                        }
                        IndexOverlayFactory.overlayHide();
                    });
                });
            }
        }

    

    $scope.sentNotification = function(Data){
        var params = {'Data' : Data};
        HTTPFactory.clientRequest('inbox/sent-notification', params).then(function (result) {
            if (result.data.STATUS == 'OK') {
                alert('ส่งการแจ้งเตือนสำเร็จ');
            }
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.cancelUpdate = function(page){
        $scope.PAGE = page;
        $scope.loadList();
    }

    $scope.prepareEditor = function(){
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
    }

    $scope.setImgList = function () {
        $scope.ImgList = [{'fileimg':null},{'fileimg':null},{'fileimg':null},{'fileimg':null},{'fileimg':null}
                ,{'fileimg':null},{'fileimg':null}];
    }

    $scope.setAttachFileList = function (){
        $scope.AttachFileList = [{'attachFile':null},{'attachFile':null},{'attachFile':null},{'attachFile':null},{'attachFile':null}
                ,{'attachFile':null},{'attachFile':null}];
    }

    $scope.deleteInboxPicture = function (index, ID){
        IndexOverlayFactory.overlayShow();
        var params = {'ID' : ID};
        HTTPFactory.clientRequest('inbox/picture/delete', params).then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.Inbox.pictures.splice(index, 1);
            }
            IndexOverlayFactory.overlayHide();
        });
        
    }

    $scope.deleteAttachFile = function (index, ID){
        IndexOverlayFactory.overlayShow();
        var params = {'ID' : ID};
        HTTPFactory.clientRequest('inbox/attach-file/delete', params).then(function (result) {
            if (result.data.STATUS == 'OK') {
                $scope.Inbox.attach_files.splice(index, 1);
            }
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.selectPushNotification = function(){
        $scope.loadDepartmentList();
        $scope.Notify = {'Group':'', 'UserList' : []};
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'inbox_notification.html',
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
            $scope.pushNotification(valResult);
        });
    }

    $scope.pushNotification = function(Data){
        IndexOverlayFactory.overlayShow();
        var params = {'InboxID' : $scope.Inbox.InboxID, 'Data' : Data};
        HTTPFactory.clientRequest('inbox/notification/push', params).then(function (result) {
            if (result.data.STATUS == 'OK') {
                // $scope.Inbox.attach_files.splice(index, 1);
                alert('ส่งการแจ้งเตือนสำเร็จ');
            }
            IndexOverlayFactory.overlayHide();
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

    $scope.autocompleteUserSelected = function ($item, $model, $label, $type){
        
        //IndexOverlayFactory.overlayShow();
        var itemSplit = $item.split(':');
        var index = parseInt(itemSplit[0].trim()) - 1;

        $scope.Notify.UserList.push({'UserID' : $autocompleteUserResult[index].UserID
                                    , 'PersonName' : $autocompleteUserResult[index].FirstName + ' ' + $autocompleteUserResult[index].LastName
                                    });
        $scope.Username = null;
    };

    $scope.removePerson = function(index){
        $scope.Notify.UserList.splice(index, 1);
    }

    $scope.popup3 = {
        opened: false
    };

    $scope.open3 = function() {
        $scope.popup3.opened = true;
    };

    $scope.dataOffset = 0;
    $scope.tableLoad = false;
    $scope.continueLoad = true;
    $scope.PAGE = 'MAIN';
    $scope.loadList();

    
});

app.controller('InboxController', function($cookies, $scope, $http, $uibModal, $routeParams, $http, HTTPFactory, IndexOverlayFactory, DepartmentFactory) {
    $scope.$parent.menu_selected = 'inbox';
    IndexOverlayFactory.overlayShow();
    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
        window.location.replace('#/logon/inbox');
    }
    IndexOverlayFactory.overlayHide();

    $scope.loadList = function(){
        if($scope.continueLoad){
            $scope.tableLoad = true;
            var params = {'user_session' : $scope.currentUser, 'offset' : $scope.dataOffset};
            HTTPFactory.clientRequest('inbox/list', params).then(function (result) {
                if (result.data.STATUS == 'OK') {
                    $scope.tableLoad = false;
                    $scope.dataOffset =  result.data.DATA.offset;
                    $scope.continueLoad = result.data.DATA.continueLoad;
                    $scope.DataList = result.data.DATA.DataList;
                    console.log($scope.DataList);
                }
                IndexOverlayFactory.overlayHide();
            });
        }
    }

    $scope.dataOffset = 0;
    $scope.tableLoad = false;
    $scope.continueLoad = true;

    $scope.loadList();

});

app.controller('InboxDetailController', function($cookies, $scope, $uibModal, $routeParams, $timeout, $interval, IndexOverlayFactory, HTTPFactory) {
    IndexOverlayFactory.overlayShow();
    $scope.menu_selected = 'news';

    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        $scope.$parent.TotalLogin = sessionStorage.getItem('TotalLogin');
        
    }else{
       window.location.replace('#/logon/' + $scope.menu_selected);
    }

    $scope.getInboxByID = function(newsID){
        var params = {'InboxID' : newsID, 'UserID' : $scope.currentUser.UserID, 'OrgID' : $scope.currentUser.OrgID};
        console.log(params);
        HTTPFactory.clientRequest('inbox/get', params).then(function(result){
            IndexOverlayFactory.overlayHide();
            if(result.data.STATUS == 'OK'){
                $scope.Inbox = result.data.DATA;
                $scope.$parent.countUnseenInbox = result.data.DATA.Unseen;
                $scope.images.push({'id':1
                                    ,'thumbUrl':$scope.Inbox.InboxPicture
                                    ,'url':$scope.Inbox.InboxPicture
                                    });
                var imgLoop = $scope.Inbox.pictures.length;
                for(var i = 0; i < imgLoop; i++){
                    console.log($scope.Inbox.pictures[i].PicturePath);
                    $scope.images.push({'id': (i + 2)
                                    ,'thumbUrl':$scope.Inbox.pictures[i].PicturePath
                                    ,'url':$scope.Inbox.pictures[i].PicturePath
                                    });
                }
                
            }
        });
    }

    $scope.getInboxByID($routeParams.InboxID);

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