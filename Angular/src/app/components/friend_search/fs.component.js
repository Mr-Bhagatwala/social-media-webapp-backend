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

      $scope.loadProfiles = function () {
        if ($scope.loading || $scope.allLoaded) return;
        $scope.loading = true;

        $http
          .get(
            `http://localhost/codeigniter/index.php/getAll?offset=${$scope.offset}&limit=${$scope.limit}`
          )
          .then(function (response) {
            if (
              response.data &&
              response.data.data &&
              response.data.data.length > 0
            ) {
              console.log(response.data.data);
              $scope.profiles = $scope.profiles.concat(response.data.data);
              $scope.offset += $scope.limit;
            } else {
              $scope.allLoaded = true; // No more profiles to load
            }
            $scope.loading = false;
          })
          .catch(function (error) {
            console.error("Error fetching profiles:", error);
            $scope.loading = false;
            $scope.allLoaded = true; // Set to true on error to prevent further attempts
          });
      };

      $scope.filteredProfiles = function () {
        // If no profiles or empty array, return empty array
        if (!$scope.profiles || !$scope.profiles.length) {
          return [];
        }

        // If no search term, return all profiles
        if (!$scope.searchTerm) {
          return $scope.profiles;
        }

        const term = $scope.searchTerm.toLowerCase().trim();

        return $scope.profiles.filter(
          (profile) =>
            profile && profile.name && profile.name.toLowerCase().includes(term)
        );
      };

      // Handle profile connection (placeholder function)
      $scope.handleConnect = function (profileId) {
        console.log("I clicked " + profileId);

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
            console.log("Response from backend:", response.data);

            if (response.data.status === "success") {
              // Remove the phone from the list in the frontend
              alert(response.data.message);
            } else {
              alert(
                "Error while sending friend request: " + response.data.message
              );
            }
          })
          .catch(function (error) {
            console.error("Error while sending friend request:", error);
            if (error.data) {
              alert("Error: " + error.data.message);
            } else {
              alert("Error while sending friend request. Please try again.");
            }
          });
        console.log("I clicked " + profileId);
      };

      // Handle profile routing
      $scope.handleRoute = function (profileId, profileName) {
        console.log("I clicked " + profileId, profileName);
        const route = `/${profileName}/${profileId}`;
        console.log("Navigating to:", route);
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

      $scope.loadProfiles(); // Initial load
    }
  );
