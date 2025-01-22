"use strict"

angular.module('myApp').controller("ParentController", function ($scope){
    $scope.selectedChat = null; //initially no data

    $scope.setSelectedChat = function (chat) {
        $scope.selectedChat = chat;
    };

})