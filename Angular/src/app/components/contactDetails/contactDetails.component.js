"use strict";

angular
  .module("myApp")
  .controller(
    "contactDetails1",
    function ($scope, $http, $location, UserService) {
      // Initialize form fields

      $scope.primaryEmail = "";
      $scope.primaryPhone = "";
      $scope.alternativeEmail = "";
      $scope.alternativePhone = "";
      $scope.linkedinUrl = "";

      $scope.isAuthenticated = UserService.isAuthenticated();
      $scope.userDetails = null;

      if ($scope.isAuthenticated) {
        UserService.fetchUserData().then(function (user) {
          if (user) {
            $scope.userDetails = user;
            $scope.primaryEmail = $scope.userDetails[0].email;
          } else {
            console.log("User not authenticated or data not found.");
          }
        });
      }

      $scope.onSubmit = function (contactDetails) {
        const user_id = localStorage.getItem("user_id");

        console.log("LL " + $scope.linkedinUrl);

        if (contactDetails.$valid) {
          // Prepare data for basic details
          const contactDetailsData = {
            primary_phone: $scope.primaryPhone,
            linkedin_url: $scope.linkedinUrl,
            user_id: user_id,
          };
          
          const emailDetailData = {
            pEmail: $scope.primaryEmail,
            user_id: user_id,
          };

          if ($scope.primaryEmail) {
            $http
              .post(
                "http://localhost/codeigniter/index.php/updateEmail",
                emailDetailData
              )
              .then(function (response) {
                if (response.data.status === "success") {
                  console.log("Updated email ", response.data);
                } else {
                  console.log("Not done email updation ", response.data);
                }
              })
              .catch(function (error) {
                console.log(error);
                alert(
                  "An error occurred while updating basic details. Please try again."
                );
              });
          }

          // Send POST request to update basic details
          $http
            .post(
              "http://localhost/codeigniter/index.php/contact",
              contactDetailsData
            )
            .then(function (response) {
              if (response.data.status === "success") {
                console.log(
                  "Response from backend (contact Details):",
                  response.data
                );
              } else {
                console.log(
                  "Response from backend (contact Details):",
                  response.data
                );
              }
            })
            .catch(function (error) {
              console.error("Error updating basic details:", error);
              alert(
                "An error occurred while updating basic details. Please try again."
              );
            });

          // Conditionally send alternate email details if data exists
          if ($scope.alternativeEmail) {
            const alternateEmailData = {
              alternative_email: $scope.alternativeEmail,
              user_id: user_id,
            };

            $http
              .post(
                "http://localhost/codeigniter/index.php/alternativeEmail",
                alternateEmailData
              )
              .then(function (response) {
                if (response.data.status === "success") {
                  console.log(
                    "Response from backend (alternate Email Details):",
                    response.data
                  );
                } else {
                  console.log(
                    "Response from backend (alternate Email Details):",
                    response.data
                  );
                }
              })
              .catch(function (error) {
                console.error(
                  "Error updating contact in alternative email details:",
                  error
                );
                alert(
                  "An error occurred while updating alternate email details. Please try again."
                );
              });
          }

          // Conditionally send alternate phone details if data exists
          if ($scope.alternativePhone) {
            const alternatePhoneData = {
              alternative_phones: $scope.alternativePhone,
              user_id: user_id,
            };

            $http
              .post(
                "http://localhost/codeigniter/index.php/alternativePhone",
                alternatePhoneData
              )
              .then(function (response) {
                if (response.data.status === "success") {
                  console.log(
                    "Response from backend (alternate Phone Details):",
                    response.data
                  );
                } else {
                  console.log(
                    "Response from backend (alternate Phone Details):",
                    response.data
                  );
                }
              })
              .catch(function (error) {
                console.error(
                  "Error updating contact in alternative phone details:",
                  error
                );
                alert(
                  "An error occurred while updating alternate phone details. Please try again."
                );
              });
          }

          // Redirect to home
          $location.path("/home");
        } else {
          console.log("Invalid form");
        }
      };
    }
  );
