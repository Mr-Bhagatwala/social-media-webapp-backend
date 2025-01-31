"use strict"

angular.module('myApp').controller("ParentController", function ($scope, $http, $rootScope){
    $scope.selectedChat = null; 
    // let urlName = undefined;
    let urlName = $rootScope.urlName;
    const user_id = localStorage.getItem("user_id");

    if (urlName != undefined) {
        console.log("Called"); 
        $scope.fetchChats = function () {
            const url = `http://localhost/codeigniter/index.php/get-all-chats/${user_id}?searchQuery=${urlName}`;
            $http
                .get(url)
                .then(function (response) {
                    if (response.data.status === "success") {
                        $scope.selectedChat = response.data.data[0];
                        console.log(response.data.data); 
                    } else {
                        console.warn("No chats found");
                    }
                })
                .catch(function (response) {
                    console.error("Error in fetching chats:", response);
                    alert("Error in fetching chats");
                });
        };
        $scope.fetchChats();

    }else{
        console.log("Not called");
    }

    $scope.setSelectedChat = function (chat) {
        $scope.selectedChat = chat;
    };
    $scope.goBackToChats = function () {
        $scope.selectedChat = null; // Reset the selected chat to null
    };
    
    
})