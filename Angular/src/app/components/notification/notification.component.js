"use strict";

angular
  .module("myApp")
  .controller(
    "NotificationController",
    function ($scope, $http, $window, UserService) {
      let id = UserService.getUserData();

      $scope.notifications = [];
      $scope.offset = 0; // Starting offset
      $scope.limit = 2; // Number of notifications to load per request
      $scope.loading = false; // Prevent multiple requests
      $scope.allLoaded = false; // Flag to indicate if all notifications are loaded

      // Function to load notifications
      $scope.loadNotifications = function () {
        if ($scope.loading || $scope.allLoaded) return;
        $scope.loading = true;

        $http
          .get(
            `http://localhost/codeigniter/index.php/get-notifications/${id}?offset=${$scope.offset}&limit=${$scope.limit}`
          )
          .then(function (response) {
            if (response.data && response.data.status == "success") {
              const newNotifications = response.data.data;
              if (newNotifications && newNotifications.length > 0) {
                $scope.notifications =
                  $scope.notifications.concat(newNotifications);
                $scope.offset += $scope.limit; // Update offset
              } else {
                $scope.allLoaded = true; // No more notifications
              }
            }
          })
          .catch(function (err) {
            console.log("Error occurred: ", err.message);
          })
          .finally(function () {
            $scope.loading = false; // Reset loading state
          });
      };

      // Clear all notifications
      $scope.clearNotifications = function () {
        $scope.notifications = [];
      };

      // Initial load
      $scope.loadNotifications();

      // Infinite scroll logic using $window
      angular.element($window).bind("scroll", function () {
        const scrollTop =
          $window.pageYOffset || document.documentElement.scrollTop;
        const windowHeight = $window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;

        if (scrollTop + windowHeight >= documentHeight - 100) {
          $scope.loadNotifications();
        }
      });
    }
  );
