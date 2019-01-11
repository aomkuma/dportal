'use strict';

var app = angular.module('app',['ngRoute', 'as.sortable','ui.bootstrap']);
app.config(function($routeProvider) {
    $routeProvider
    .when("/", {
        templateUrl : "dashboard",
        controller : "DashboardCtrl"
    })
    .when("/create_proj", {
        templateUrl : "create_proj",
        controller : "CreateProjCtrl"
    })
    .when("/analysis_proj/:proj_id", {
        templateUrl : "analysis_proj",
        controller : "AnalysisProjCtrl"
        ,resolve: {
        	projectGeneral : ['ProjectService','$route', function(ProjectService , $route) {
            	console.log($route.current.params.proj_id);
                return ProjectService.getProjectGeneral($route.current.params.proj_id);
            }],
            projectDetail : ['ProjectService','$route', function(ProjectService , $route) {
            	console.log($route.current.params.proj_id);
                return ProjectService.getProjectDetail($route.current.params.proj_id);
            }],
            projectTeamMember : ['ProjectService','$route', function(ProjectService , $route) {
            	console.log($route.current.params.proj_id);
                return ProjectService.getProjectTeamMember($route.current.params.proj_id);
            }]
        }
    })
    
});
