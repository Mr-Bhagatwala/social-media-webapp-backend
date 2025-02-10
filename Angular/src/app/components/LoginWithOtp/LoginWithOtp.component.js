"use strict";

angular
  .module("myApp")
  .controller("LoginWithOtp", function ($rootScope, $scope, $http, $location, $timeout) {
    // Initialize variables
    $scope.otpEmail = "";
    $scope.randomOtp = "";
    $scope.message = ""; // For success/error messages
    $scope.toggleOtp = false;

    // Function to generate OTP
    $scope.generateOtp = function () {
      if (!$scope.otpEmail) {
        $scope.message = "Please enter a valid email.";
        return;
      }
      

      const postData = {
        email: $scope.otpEmail
      };

      // Send the request to generate OTP
      $http
        .post("http://localhost/codeigniter/index.php/generate-otp", postData)
        .then(function (response) {
          if (response.data.status === "success") {
            $scope.message = "OTP sent successfully!";
            // alert($scope.message + "please check your mail")
          } else {
            $scope.message = response.message || "Failed to send OTP.";
            alert(response.data.message + "with this email ID please register first" )
          }
        })
        .catch(function (error) {
          $scope.message = "Error: " + error.message;
        });
    };

  

    // Function to verify OTP
    $scope.verifyOtp = function () {
      if (!$scope.randomOtp) {
        $scope.message = "Please enter the OTP.";
        return;
      }

      const postData = {
        email: $scope.otpEmail,
        otp: $scope.randomOtp
      };

      // Send the request to verify OTP
      $http
        .post("http://localhost/codeigniter/index.php/verify-otp", postData)
        .then(function (response) {
          if (response.data.status === "success") {
            alert("Login successful!");
            localStorage.setItem("user_id", response.data.user.id);
            $rootScope.userData = response.data.user.id;
            $location.path("/home"); // Redirect to home page
          } else {
            $scope.message = response.message || "Invalid OTP.";
            alert($scope.message)
          }
        })
        .catch(function (error) {
          $scope.message = "Error: " + error.message;
        });
    };


    $scope.toggleOtpBtn = function(){
        console.log("object")
        $scope.toggleOtp = !$scope.toggleOtp;
    }
  });
