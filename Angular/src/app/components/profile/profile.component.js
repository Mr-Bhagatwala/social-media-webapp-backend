"use strict";

angular
  .module("myApp")
  .controller("profile", function ($scope, $http, $location, UserService) {
    // Initialize form fields
    $scope.name = "";
    $scope.gender = "";
    $scope.marital = "";
    $scope.date = "";
    $scope.currentCity = "";
    $scope.homeTown = "";
    
    $scope.isAuthenticated = UserService.isAuthenticated();
    $scope.userDetails = null;
    
    if ($scope.isAuthenticated) {
      UserService.fetchUserData().then(function (user) {
        if (user) {
          $scope.userDetails = user;
          console.log("Inside userDetails "+$scope.userDetails[0].name);
          $scope.name = $scope.userDetails[0].name;
          console.log("Inside Details name"+$scope.name);
          
        } else {
          console.log("User not authenticated or data not found.");
        }
      });
    }
    
    $scope.onSubmit = function (profileForm) {
      const user_id = localStorage.getItem("user_id");
      if (profileForm.$valid) {
        // Prepare data for basic details
        const basicDetailsData = {
          name: $scope.name,
          gender: $scope.gender,
          marital_status: $scope.marital,
          date_of_birth: $scope.date,
          current_city: $scope.currentCity,
          hometown: $scope.homeTown,
          user_id: user_id,
        };
        // Send POST request to update basic details
        $http
          .post(
            "http://localhost/codeigniter/index.php/details",
            basicDetailsData
          )
          .then(function (response) {
            console.log(
              "Response from backend (Basic Details):",
              response.data
            );
            if (response.data.status === "success") {
              alert("Profile updated successfully!");
              $location.path("/contactDetails");
            } else {
              alert("Error updating basic details: " + response.data.message);
            }
          })
          .catch(function (error) {
            console.error("Error updating basic details:", error);
            // Handle network errors or backend errors more explicitly
            if (error.data) {
              alert("Error: " + error.data.message);
            } else {
              alert("An error occurred. Please try again.");
            }
          });
      } else {
        console.log("Invalid form");
      }
    };
  });
