"use strict";
angular.module("myApp").component("navbar", {
  templateUrl: "./app/components/navbar/navbar.html",
  controller: function ($scope, $timeout, $compile, UserService, $http) {
    $scope.isMenuOpen = false;

    $scope.toggleMenu = function () {
      $scope.isMenuOpen = !$scope.isMenuOpen;
    };
    $scope.isAuthenticated = UserService.isAuthenticated();
    $scope.userDetails = null;

    if ($scope.isAuthenticated) {
      UserService.fetchUserData().then(function (user) {
        if (user) {
          $scope.userDetails = user;
          console.log("Inside userDetails " + $scope.userDetails);
        } else {
          console.log("User not authenticated or data not found.");
        }
      });
    }

    let typingTimeout;
    $scope.dummyData = [
      { name: "John Doe", userId: 1 },
      { name: "Jane Smith", userId: 2 },
      { name: "Michael Johnson", userId: 3 },
      { name: "Emily Davis", userId: 4 },
      { name: "Chris Brown", userId: 5 },
      { name: "Jessica Wilson", userId: 6 },
      { name: "Daniel Martinez", userId: 7 },
      { name: "Sophia Garcia", userId: 8 },
      { name: "David Anderson", userId: 9 },
      { name: "Emma Thomas", userId: 10 },
    ];

    $scope.searchQuery = "";

    $scope.openDialog = function () {
      const dialog = document.querySelector("#searchDialog");
      dialog.showModal();
    };

    $scope.closeDialog = function closeDialog() {
      var dialog = document.querySelector("#searchDialog");
      dialog.close();
      $scope.searchQuery = "";
    };

    $scope.onSearchInput = function (query) {
      const sdialog = document.querySelector("#searchDialog");

      if (typingTimeout) {
        $timeout.cancel(typingTimeout);
      }

      typingTimeout = $timeout(function () {
        if (query && query.trim().length > 0) {
          if (sdialog && !sdialog.open) {
            sdialog.showModal();
          }

          //
          // const filteredData = $scope.dummyData.filter((user) =>
          //   user.name.toLowerCase().includes(query.toLowerCase())
          // );
          $http
            .get(
              `http://localhost/codeigniter/index.php/search/users/${query}`,
              {}
            )
            .then(function (response) {
              const filteredData = response.data;

              const searchResults = document.querySelector("#searchResults");
              searchResults.innerHTML = ""; // Clear previous results

              if (filteredData.length > 0) {
                const compiledHTML = $compile(
                  filteredData
                    .map(
                      (user) =>
                        `<li><a href="#/${user.name.replace(/\s+/g, "")}/${
                          user.userId
                        }" ng-click="closeDialog()">${user.name}</a></li>`
                    )
                    .join("")
                )($scope);

                angular.element(searchResults).append(compiledHTML);
              } else {
                searchResults.innerHTML = `<li>No results found for "${query}"</li>`;
              }
            })
            .catch(function (error) {
              console.error("Error fetching search results:", error);
              const searchResults = document.querySelector("#searchResults");
              searchResults.innerHTML = `<li>Error fetching results. Please try again.</li>`;
            });

          // const searchResults = document.querySelector("#searchResults");
          // if (filteredData.length > 0) {
          //   const compiledHTML = $compile(
          //     filteredData
          //       .map(
          //         (user) =>
          //           `<li><a href="#/${user.name.replace(/\s+/g, "")}/${
          //             user.userId
          //           }" ng-click="closeDialog()">${user.name}</a></li>`
          //       )
          //       .join("")
          //   )($scope);

          //   angular.element(searchResults).append(compiledHTML);
          // } else {
          //   searchResults.innerHTML = `<li>No results found for "${query}"</li>`;
          // }
        } else {
          if (sdialog) {
            sdialog.close();
          }
        }
      }, 800);
    };
  },
});
