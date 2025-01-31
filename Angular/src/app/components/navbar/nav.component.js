"use strict";
angular.module("myApp").component("navbar", {
  templateUrl: "./app/components/navbar/navbar.html",
  controller: function ($scope, $timeout, $compile, UserService, $http) {
    $scope.isMenuOpen = false;

    $scope.toggleMenu = function () {
      $scope.isMenuOpen = !$scope.isMenuOpen;
    };
    $scope.isAuthenticated = UserService.isAuthenticated();
    $scope.userDetails = null;

    if ($scope.isAuthenticated) {
      UserService.fetchUserData().then(function (data) {
        if (data) {
          console.log("Inside navbar "+data.user.name);
          
          $scope.userDetails = data.user;
          console.log("19 "+$scope.userDetails.name);
          
        } else {
          console.log("User not authenticated or data not found.");
        }
      });
    }

    let typingTimeout;
    $scope.dummyData = [
      { name: "John Doe", userId: 1 },
      { name: "Jane Smith", userId: 2 },
      { name: "Michael Johnson", userId: 3 },
      { name: "Emily Davis", userId: 4 },
      { name: "Chris Brown", userId: 5 },
      { name: "Jessica Wilson", userId: 6 },
      { name: "Daniel Martinez", userId: 7 },
      { name: "Sophia Garcia", userId: 8 },
      { name: "David Anderson", userId: 9 },
      { name: "Emma Thomas", userId: 10 },
    ];

    $scope.searchQuery = "";

    $scope.openDialog = function () {
      const dialog = document.querySelector("#searchDialog");
      dialog.showModal();
    };

    $scope.closeDialog = function closeDialog() {
      var dialog = document.querySelector("#searchDialog");
      dialog.close();
      $scope.searchQuery = "";
    };
  },
});
