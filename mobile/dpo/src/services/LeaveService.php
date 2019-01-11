<?php

	namespace App\Service;

	use App\Model\LeaveNotification;
	use App\Model\LomsAccount;

	use Illuminate\Database\Capsule\Manager as DB;

	class LeaveService {

		public static function logon($Username, $Password){
			$model = LomsAccount::where('Username', $Username)
					->where('Password', $Password)
					->where('ActiveStatus', 'Y')
					->first();
			return $model;
		}

		public static function getNotification($email) {
	    	return LeaveNotification::where('Email', $email)
	    				->orderBy("CreateDateTime", "DESC")
	    				->get();
            
	    }

	    public static function countNotificationUnseen($email) {
	    	return count(LeaveNotification::where('Email', $email)
	    				->where("ViewStatus", 'unseen')
	    				->get()->toArray());
            
	    }

	    public static function putNotification($obj) {
	    	$obj['CreateDateTime'] = date('Y-m-d H:i:s');
	    	$model = LeaveNotification::create($obj);
            return $model->ID;
	    }

	    public static function updateSeenNotification($ID) {
	    	$model = LeaveNotification::find($ID);
	    	$model->ViewStatus = 'seen';
	    	return $model->save();
	    }
	}