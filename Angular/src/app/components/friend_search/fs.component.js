
"use strict";

angular
  .module("myApp")
  .controller(
    "FriendSearchController",
    function ($scope, $http, $location, $window) {
      $scope.searchTerm = "";
      $scope.profiles = [];
      $scope.offset = 0; // Starting offset
      $scope.limit = 10; // Number of profiles per batch
      $scope.loading = false;
      $scope.allLoaded = false;

      // Function to load profiles with the current search term
      $scope.loadProfiles = function () {
        if ($scope.loading || $scope.allLoaded) return;
        $scope.loading = true;
      
        $http
          .get(
            `http://localhost/codeigniter/index.php/getAll?offset=${$scope.offset}&limit=${$scope.limit}&search=${encodeURIComponent(
              $scope.searchTerm
            )}`
          )
          .then(function (response) {
            if (response.data.success) {
              if (response.data.data && response.data.data.length > 0) {
                console.log(response.data.data);
                $scope.profiles = $scope.profiles.concat(response.data.data);
                $scope.offset += $scope.limit;
              } else {
                // No more users found
                $scope.allLoaded = true;
              }
            } else {
              // If the success field is false, set allLoaded to true
              console.warn("No more users found:", response.data.message);
              $scope.allLoaded = true;
            }
          })
          .catch(function (error) {
            console.error("Error fetching profiles:", error);
            $scope.allLoaded = true; // Prevent further attempts on error
          })
          .finally(function () {
            $scope.loading = false;
          });
      };
      

      // Function to handle search input changes
      $scope.searchProfiles = function () {
        // Reset state for a fresh search
        $scope.profiles = [];
        $scope.offset = 0;
        $scope.allLoaded = false;
        $scope.loadProfiles(); // Trigger profile loading
      };

      // Handle profile connection (placeholder function)
      $scope.handleConnect = function (profileId) {
        const requestData = {
          sender_id: user_id,
          receiver_id: profileId,
        };

        $http
          .post(
            "http://localhost/codeigniter/index.php/send-friend-request",
            requestData
          )
          .then(function (response) {
            if (response.data.status === "success") {
              alert(response.data.message);
            } else {
              alert(
                "Error while sending friend request: " + response.data.message
              );
            }
          })
          .catch(function (error) {
            console.error("Error sending friend request:", error);
            alert("Error while sending friend request. Please try again.");
          });
      };

      // Handle profile routing
      $scope.handleRoute = function (profileId, profileName) {
        const route = `/${profileName}/${profileId}`;
        $location.path(route);
      };

      // Improved infinite scrolling with scope apply
      angular.element($window).bind("scroll", function () {
        const scrollTop =
          $window.pageYOffset || document.documentElement.scrollTop;
        const windowHeight = $window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;

        if (scrollTop + windowHeight >= documentHeight - 100) {
          $scope.$apply(function () {
            $scope.loadProfiles();
          });
        }
      });

      // Cleanup event listener when scope is destroyed
      $scope.$on("$destroy", function () {
        angular.element($window).unbind("scroll");
      });

      $scope.loadProfiles(); 
    }
);
