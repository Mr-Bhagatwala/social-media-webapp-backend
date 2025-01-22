"use strict";

angular
  .module("myApp")
  .controller("FriendRequestController", function ($scope, $http, UserService) {
    // Initialize scope variables
    $scope.requests = [];
    // Fetch friend requests
    let id = UserService.getUserData();

    $http
      .get(`http://localhost/codeigniter/index.php/get-requests/${id}`)
      .then(function (response) {
        $scope.requests = response.data.data;
        console.log("friend request aaaaaa  ",response.data.data);
      });
      
    

    // Reject friend request
    $scope.rejectRequest = function (sender, receiver) {
      $http
        .post("http://localhost/codeigniter/index.php/rfr", {
          sender_id: sender,
          receiver_id: receiver,
          status: "rejected",
        })
        .then(function (response) {
          $http
            .get(`http://localhost/codeigniter/index.php/get-requests/${id}`)
            .then(function (response) {
              console.log(response.data.data);
              $scope.requests = response.data.data;
            });
        })
        .catch(function (err) {
          console.log(err);
        });
    };

    // Accept friend request
    $scope.acceptRequest = function (sender, receiver) {
      $http
        .post("http://localhost/codeigniter/index.php/rfr", {
          sender_id: sender,
          receiver_id: receiver,
          status: "accepted",
        })
        .then(function (response) {
          $scope.requests = $scope.requests.filter(function (request) {
            return request.sender_id !== response.data.id;
          });
        })
        .catch(function (err) {
          console.log(err);
        });
    };
  });
