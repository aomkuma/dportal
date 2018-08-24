<?php

    namespace App\Controller;
    
    use App\Model\User;
    use App\Service\UserService;
    
    class UserController extends Controller {
        
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
        }
       
        public function getUser($request, $response, $args){
            try{
                $id = filter_var($request->getAttribute('id'), FILTER_SANITIZE_NUMBER_INT);
                $this->logger->info('Find by id : '.$id);
                $user = UserService::getUser($id);
                return $this->returnResponse(200, $user, $response, false);
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $e, $response);
            }
            
        }
        
        public function addUser($request, $response, $args){
            try{
                $parsedBody = $request->getParsedBody();
                //$user = (User)$parsedBody;
                //die();
                $user = new User;
                $user = $user->setValues($user, $parsedBody);
                $insert_id = UserService::addUser($user);
                return $this->returnResponse(200, $insert_id, $response);
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $e, $response);
            }
        }

        public function getPhoneBookList($request, $response, $args){
            try{
                $Group = filter_var($request->getAttribute('Group'), FILTER_SANITIZE_NUMBER_INT);
                $Username = $request->getAttribute('Username');
                $offset = filter_var($request->getAttribute('offset'), FILTER_SANITIZE_NUMBER_INT);
                $RegionID = filter_var($request->getAttribute('RegionID'), FILTER_SANITIZE_NUMBER_INT);
                $DepartmentID = filter_var($request->getAttribute('DepartmentID'), FILTER_SANITIZE_NUMBER_INT);
                $LoginUserID = filter_var($request->getAttribute('LoginUserID'), FILTER_SANITIZE_NUMBER_INT);

                $Username = $Username == '-'?'':$Username;
                $Group  = $Group == 'ALL'?'':$Group;

                //$this->logger->info('Find by id : '.$id);
                $DataList = UserService::getPhoneBookList($Group, $Username, $offset, $RegionID, $DepartmentID, $LoginUserID);
                $this->data_result['DATA'] = $DataList;

                return $this->returnResponse(200, $this->data_result, $response, false);

            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }

        public function addFavouriteContact($request, $response, $args){
            try{

                $parsedBody = $request->getParsedBody();
                $UserFavouriteID = filter_var($parsedBody['UserFavouriteID'], FILTER_SANITIZE_NUMBER_INT);
                $LoginUserID = filter_var($parsedBody['LoginUserID'], FILTER_SANITIZE_NUMBER_INT);

                //$this->logger->info('Find by id : '.$id);
                $insertID = UserService::addFavouriteContact($LoginUserID, $UserFavouriteID);
                if(intval($insertID) > 0){
                    $this->data_result['DATA'] = UserService::getFavouriteContact($UserFavouriteID);
                }else{
                    $this->data_result['STATUS'] = 'ERROR';
                    $this->data_result['DATA'] = 'Cannot add favourite contact';
                }
                

                return $this->returnResponse(200, $this->data_result, $response);

            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }

        public function removeFavouriteContact($request, $response, $args){
            try{
    //             error_reporting(E_ERROR);
    // error_reporting(E_ALL);
    // ini_set('display_errors','On');
                $FavouriteID = filter_var($request->getAttribute('FavouriteID'), FILTER_SANITIZE_NUMBER_INT);
                $UserID = filter_var($request->getAttribute('UserID'), FILTER_SANITIZE_NUMBER_INT);

                if(UserService::removeFavouriteContact($FavouriteID)){
                    $this->data_result['DATA'] = UserService::getContact($UserID);
                }else{
                    $this->data_result['STATUS'] = 'ERROR';
                    $this->data_result['DATA'] = 'Cannot remove favourite contact';
                }
                

                return $this->returnResponse(200, $this->data_result, $response);

            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }

        public function getUserContact($request, $response, $args){
            try{
                $UserID = filter_var($request->getAttribute('UserID'), FILTER_SANITIZE_NUMBER_INT);
                //$this->logger->info('Find by id : '.$id);
                $user = UserService::getContact($UserID);
                $this->data_result['DATA'] = $user;
                return $this->returnResponse(200, $this->data_result, $response, false);
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }
        public function updatePhoneBookContact($request, $response, $args){
            try{
                $parsedBody = $request->getParsedBody();
                $parsedBody = $parsedBody['Contact'];
                //$this->logger->info('Find by id : '.$id);
                $user = UserService::updatePhoneBookContact($parsedBody);
                $this->data_result['DATA'] = $user;
                return $this->returnResponse(200, $this->data_result, $response);
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }
        
    }

?>