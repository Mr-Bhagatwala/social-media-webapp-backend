"use strict";

angular
  .module("myApp")
  .controller("login", function ($rootScope, $scope, $http, $location) {
    $scope.email = "";
    $scope.password = "";
    
    
    // Submit handler
    $scope.onSubmit = function (loginForm) {
      if (loginForm.$valid) {
        const postData = {
          email: $scope.email,
          password: $scope.password,
        };

        $http
          .post("http://localhost/codeigniter/index.php/login", postData)
          .then(function (response) {
            console.log("Response from backend:", response.data);
            if (response.data.status == "success") {
              alert("login successful!");
              console.log(response);
              localStorage.setItem("user_id", response.data.user.id);
              $rootScope.userData = response.data.user.id;
              $location.path("/home");
            } else {
              alert("Error: " + response.data.message);
            }
          })
          .catch(function (error) {
            console.error("Error connecting to backend:", error);
            alert("An error occurred while login. Please try again.");
          });
        loginForm.$setPristine();
        loginForm.$setUntouched();
      } else {
        console.log("Invalid Form");
      }
    };
  });
