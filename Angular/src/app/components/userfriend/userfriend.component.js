// "use strict";

// angular
//   .module("myApp")
//   .controller(
//     "userFriendsController",
//     function ($scope, $http, $window, UserService, $location) {
//       $scope.searchTerm = "";
//       $scope.friends = [];

//       const currentUser = UserService.getUserData();

//       const loadFriends = function () {
//         $http
//           .get(
//             `http://localhost/codeigniter/index.php/get-friends/${currentUser}`
//           )
//           .then(function (response) {
//             if (response.data.status === "success") {
//               $scope.friends = response.data.data;
//               console.log("friends ka data is ", $scope.friends);
//             } else {
//               console.error("Failed to fetch friends data", response);
//             }
//           })
//           .catch(function (error) {
//             console.error("Error fetching friends data:", error);
//           });
//         //   $scope.friends = [
//         //     {
//         //       friend_id: "1",
//         //       name: "Alice Johnson",
//         //       profile_photo:
//         //         "http://localhost/codeigniter/assets/profile_pictures/pp.jpg",
//         //     },
//         //     {
//         //       friend_id: "2",
//         //       name: "Bob Brown",
//         //       profile_photo:
//         //         "http://localhost/codeigniter/assets/profile_pictures/pp.jpg",
//         //     },
//         //     {
//         //       friend_id: "3",
//         //       name: "Charlie Davis",
//         //       profile_photo:
//         //         "http://localhost/codeigniter/assets/profile_pictures/pp.jpg",
//         //     },
//         //     {
//         //       friend_id: "4",
//         //       name: "Diana Evans",
//         //       profile_photo:
//         //         "http://localhost/codeigniter/assets/profile_pictures/pp.jpg",
//         //     },
//         //     {
//         //       friend_id: "5",
//         //       name: "Ethan Foster",
//         //       profile_photo:
//         //         "http://localhost/codeigniter/assets/profile_pictures/pp.jpg",
//         //     },
//         //     {
//         //       friend_id: "6",
//         //       name: "Fiona Green",
//         //       profile_photo:
//         //         "http://localhost/codeigniter/assets/profile_pictures/pp.jpg",
//         //     },
//         //     {
//         //       friend_id: "7",
//         //       name: "George Harris",
//         //       profile_photo:
//         //         "http://localhost/codeigniter/assets/profile_pictures/pp.jpg",
//         //     },
//         //     {
//         //       friend_id: "8",
//         //       name: "Hannah Ivers",
//         //       profile_photo:
//         //         "http://localhost/codeigniter/assets/profile_pictures/pp.jpg",
//         //     },
//         //     {
//         //       friend_id: "9",
//         //       name: "Ian Johnson",
//         //       profile_photo:
//         //         "http://localhost/codeigniter/assets/profile_pictures/pp.jpg",
//         //     },
//         //     {
//         //       friend_id: "10",
//         //       name: "Jasmine King",
//         //       profile_photo:
//         //         "http://localhost/codeigniter/assets/profile_pictures/pp.jpg",
//         //     },
//         //   ];
//       };

//       $scope.filterFriends = function () {
//         const term = $scope.searchTerm.toLowerCase();
//         return $scope.friends.filter((friend) =>
//           friend.name.toLowerCase().includes(term)
//         );
//       };

//       $scope.viewProfile = function (name, id) {
//         const route = `/${name}/${id}`;
//         console.log("Navigating to:", route);
//         $location.path(route); // Navigate to the route
//       };

//       // angular.element($window).bind("scroll", function () {
//       //   const scrollTop =
//       //     $window.pageYOffset || document.documentElement.scrollTop;
//       //   const windowHeight = $window.innerHeight;
//       //   const documentHeight = document.documentElement.scrollHeight;

//       //   if (scrollTop + windowHeight >= documentHeight - 100) {
//       //     // Load more feeds when close to the bottom
//       //     $scope.loadFriends();
//       //   }
//       // });
//       // Initial call to load friends
//       loadFriends();
//     }
//   );

"use strict";

angular
  .module("myApp")
  .controller(
    "userFriendsController",
    function ($scope, $http, $window, UserService, $location) {
      // $scope.searchTerm = "";
      // $scope.friends = [];
      // $scope.page = 1;
      // $scope.limit = 10;
      // $scope.loading = false;
      // $scope.allLoaded = false;

      // const user_id = UserService.getUserData();

      // const loadFriends = function () {
      //   if ($scope.loading || $scope.allLoaded) return;

      //   $scope.loading = true;
      // console.log("term is ", $scope.searchTerm);
      // const searchQuery = encodeURIComponent($scope.searchTerm);
      // console.log("searchQuery is ", searchQuery);
      //   $http
      //     .get(
      //       `http://localhost/codeigniter/index.php/get-friends/${user_id}?page=${$scope.page}&limit=${$scope.limit}&search=${searchQuery}`
      //     )
      //     .then(function (response) {
      //       if (response.data.status === "success") {
      //         const newFriends = response.data.data;

      //         if (newFriends.length === 0) {
      //           $scope.allLoaded = true; // No more data to load
      //         } else {
      //           $scope.friends =
      //             $scope.page === 1
      //               ? newFriends
      //               : $scope.friends.concat(newFriends);
      //           $scope.page++;
      //         }
      //       } else {
      //         console.error("Failed to fetch friends data", response);
      //       }
      //     })
      //     .catch(function (error) {
      //       console.error("Error fetching friends data:", error);
      //     })
      //     .finally(function () {
      //       $scope.loading = false;
      //     });
      // };

      // $scope.searchFriends = function () {
      //   $scope.page = 1; // Reset to first page for a new search
      //   $scope.allLoaded = false; // Allow loading new data
      //   $scope.friends = []; // Clear current friends list
      //   loadFriends();
      // };

      // angular.element($window).bind("scroll", function () {
      //   const scrollTop =
      //     $window.pageYOffset || document.documentElement.scrollTop;
      //   const windowHeight = $window.innerHeight;
      //   const documentHeight = document.documentElement.scrollHeight;

      //   if (scrollTop + windowHeight >= documentHeight - 100) {
      //     // Load more friends when close to the bottom
      //     loadFriends();
      //   }
      // });
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
