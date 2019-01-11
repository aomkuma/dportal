<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    $logger->pushHandler(new Monolog\Handler\RotatingFileHandler($settings['path'], $settings['maxFiles'], $settings['level']));
    return $logger;
};

$container['db'] = function ($c) {
    $settings = $c->get('settings')['db'];
    $capsule = new Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($settings);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
    return $capsule;
};

$container['sms'] = function ($c) {
    $settings = $c->get('settings')['sms'];
    return $settings;
};

$container['ldap'] = function ($c) {
    $settings = $c->get('settings')['ldap'];
    return $settings;
};

$container['UserController'] = function ($c) {
    return new \App\Controller\UserController($c->get('logger'), $c->get('db'));
};

$container['LoginController'] = function ($c) {
    return new \App\Controller\LoginController($c->get('logger'), $c->get('sms'), $c->get('db'),  $c->get('ldap'));
};

$container['RegionController'] = function ($c) {
    return new \App\Controller\RegionController($c->get('logger'), $c->get('db'));
};

$container['RoomReserveController'] = function ($c) {
    return new \App\Controller\RoomReserveController($c->get('logger'), $c->get('sms'), $c->get('db'));
};

$container['AutocompleteController'] = function ($c) {
    return new \App\Controller\AutocompleteController($c->get('logger'), $c->get('db'));
};

$container['NewsController'] = function ($c) {
    return new \App\Controller\NewsController($c->get('logger'), $c->get('db'));
};

$container['NotificationController'] = function ($c) {
    return new \App\Controller\NotificationController($c->get('logger'), $c->get('db'));
};

$container['RoomController'] = function ($c) {
    return new \App\Controller\RoomController($c->get('logger'), $c->get('db'));
};

$container['FoodController'] = function ($c) {
    return new \App\Controller\FoodController($c->get('logger'), $c->get('db'));
};

$container['DeviceController'] = function ($c) {
    return new \App\Controller\DeviceController($c->get('logger'), $c->get('db'));
};

$container['CartypeController'] = function ($c) {
    return new \App\Controller\CartypeController($c->get('logger'), $c->get('db'));
};

$container['CarController'] = function ($c) {
    return new \App\Controller\CarController($c->get('logger'), $c->get('db'));
};

$container['ProvinceController'] = function ($c) {
    return new \App\Controller\ProvinceController($c->get('logger'), $c->get('db'));
};

$container['CarReserveController'] = function ($c) {
    return new \App\Controller\CarReserveController($c->get('logger'), $c->get('db'));
};

$container['LinkController'] = function ($c) {
    return new \App\Controller\LinkController($c->get('logger'), $c->get('db'));
};

$container['RepairController'] = function ($c) {
    return new \App\Controller\RepairController($c->get('logger'), $c->get('db'));
};

$container['DepartmentController'] = function ($c) {
    return new \App\Controller\DepartmentController($c->get('logger'), $c->get('db'));
};

$container['CalendarController'] = function ($c) {
    return new \App\Controller\CalendarController($c->get('logger'), $c->get('db'));
};

$container['Mailer'] = function ($c) {
    return new \App\Controller\Mailer($c->get('logger'));
};

$container['SMSController'] = function ($c) {
    return new \App\Controller\SMSController($c->get('logger'), $c->get('sms'));
};

$container['EHRController'] = function ($c) {
    return new \App\Controller\EHRController($c->get('logger'), $c->get('db'));
};

$container['PermissionController'] = function ($c) {
    return new \App\Controller\PermissionController($c->get('logger'), $c->get('db'));
};

$container['ReportController'] = function ($c) {
    return new \App\Controller\ReportController($c->get('logger'), $c->get('db'));
};

$container['ExternalPhoneBookController'] = function ($c) {
    return new \App\Controller\ExternalPhoneBookController($c->get('logger'), $c->get('db'));
};

$container['SystemManageController'] = function ($c) {
    return new \App\Controller\SystemManageController($c->get('logger'), $c->get('db'));
};

$container['PersonRegionController'] = function ($c) {
    return new \App\Controller\PersonRegionController($c->get('logger'), $c->get('db'));
};

$container['LeaveController'] = function ($c) {
    return new \App\Controller\LeaveController($c->get('logger'), $c->get('db'));
};