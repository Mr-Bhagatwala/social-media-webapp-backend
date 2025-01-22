// "use strict";

angular
  .module("myApp")
  .controller("NotificationController", function ($scope, $http, UserService) {
    let id = UserService.getUserData();
    console.log(" idd dee rha h    ", id);
    $scope.notifications = [];
    $http
      .get(`http://localhost/codeigniter/index.php/get-notifications/${id}`)
      .then(function (response) {
        if (response.data.status == "success") {
          $scope.notifications = response.data.data;
        }
      })

      .catch(function (err) {
        console.log("error ayi hai  ", err);
      });

    // Clear all notifications
    $scope.clearNotifications = function () {
      $scope.notifications = [];
    };
  });
