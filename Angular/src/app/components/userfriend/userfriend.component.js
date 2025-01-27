
"use strict";

angular
  .module("myApp")
  .controller(
    "userFriendsController",
    function ($scope, $http, $window, UserService, $location) {
      $scope.searchTerm = "";
      $scope.page = 1;
      $scope.limit = 6;
      $scope.isLoading = false;
      $scope.hasMore = true;
      $scope.friends = [];
      const user_id = UserService.getUserData();
      $scope.loadFriends = function () {
        if ($scope.isLoading || !$scope.hasMore) return;
        
        console.log("term is ", $scope.searchTerm);
        const searchQuery = encodeURIComponent($scope.searchTerm);
        console.log("searchQuery is ", searchQuery);
        $scope.isLoading = true;
        const url = `http://localhost/codeigniter/index.php/get-friends/${user_id}?page=${$scope.page}&limit=${$scope.limit}&search=${searchQuery}`;
        
        $http
          .get(url)
          .then(
            function (response) {
              if (response.data.status === "success") {
                const newFriends = response.data.data;
                console.log(newFriends);
                // Append new friends to the list
                $scope.friends = $scope.friends.concat(newFriends);
                
                // Check if there are more friends to load
                $scope.hasMore = newFriends.length === $scope.limit;
                
                // Increment page for the next load
                $scope.page += 1;
              }
            },
            function (error) {
              console.error("Error fetching friends:", error);
            }
          )
          .finally(function () {
            $scope.isLoading = false;
          });
      };

      $scope.searchFriends = function () {
        $scope.page = 1;
        $scope.friends = [];
        $scope.hasMore = true;
        $scope.loadFriends();
      };

      // Infinite scrolling
      window.onscroll = function () {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight) {
          $scope.loadFriends();
        }
      };

      $scope.viewProfile = function (name, id) {
        const route = `/${name}/${id}`;
        console.log("Navigating to:", route);
        $location.path(route); // Navigate to the route
      };


      // Initial call to load friends
      $scope.loadFriends();
    }
  );
