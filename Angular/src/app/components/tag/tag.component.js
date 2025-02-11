angular.module("myApp").component("tag", {
  templateUrl: "./app/components/tag/tag.component.html",
  controller: function (
    $scope,
    $http,
    UserService,
    $location,
    $timeout,
    TagService
  ) {
    $scope.tagSearchTerm = "";
    const user_id = UserService.getUserData();
    $scope.taggedUsers = [];
    $scope.tagFriends = []; // All fetched friends
    $scope.displayedTagFriends = []; // Friends to display (2 at a time)
    $scope.page = 1;
    $scope.limit = 2; // Load only 2 friends at a time
    $scope.isLoading = false;
    $scope.hasMore = true;
    $scope.searchClicked = false;

    $scope.taggedUsers = TagService.getTaggedUsers();

    console.log("Tagged users in tag component:", $scope.taggedUsers);

    $scope.loadFriendsForTag = function () {
      if ($scope.isLoading || !$scope.hasMore) return;

      $scope.isLoading = true;
      const searchQuery = encodeURIComponent($scope.tagSearchTerm);
      const url = `http://localhost/codeigniter/index.php/get-friends/${user_id}?page=${$scope.page}&limit=${$scope.limit}&search=${searchQuery}`;

      $http
        .get(url)
        .then(function (response) {
          if (response.data.status === "success") {
            console.log("Response:", response);
            const newTagFriends = response.data.data;

            // Append new friends to both tagFriends and displayedTagFriends
            $scope.tagFriends = $scope.tagFriends.concat(newTagFriends);
            $scope.displayedTagFriends =
              $scope.displayedTagFriends.concat(newTagFriends);

            // Check if more friends are available
            $scope.hasMore = newTagFriends.length === $scope.limit;
            $scope.page += 1;
          }
        })
        .catch(function (error) {
          console.error("Error fetching friends:", error);
        })
        .finally(function () {
          $scope.isLoading = false;
        });
    };

    $scope.searchFriends = function () {
      $scope.page = 1;
      $scope.tagFriends = [];
      $scope.displayedTagFriends = [];
      $scope.hasMore = true;
      $scope.loadFriendsForTag();
    };

    // Function to load more friends when scrolling
    $scope.loadMoreFriends = function () {
      if ($scope.isLoading || !$scope.hasMore) return;

      $scope.isLoading = true;
      $timeout(function () {
        $scope.loadFriendsForTag();
        $scope.$apply(); // Ensure AngularJS detects scope changes
      }, 100);
    };

    // Attach scroll event listener
    window.onscroll = function () {
      if (window.innerHeight + window.scrollY >= document.body.offsetHeight) {
        $scope.loadMoreFriends();
      }
    };

    // Function to tag a friend
    $scope.tagFriendFun = function (id, name, profile_photo) {
      const user = { id, name, profile_photo };
      TagService.addTaggedUser(user);
      $scope.taggedUsers = TagService.getTaggedUsers(); // Update scope
      console.log("Tagged Users:", $scope.taggedUsers);
    };

    // Untag a friend and update the service
    $scope.untagFriendFun = function (id) {
      TagService.removeTaggedUser(id);
      $scope.taggedUsers = TagService.getTaggedUsers(); // Update scope
      console.log("Updated Tagged Users:", $scope.taggedUsers);
    };

    // Open search results
    $scope.openDialog = function () {
      $scope.searchClicked = !$scope.searchClicked;
    };

    // Initial load
    $scope.loadFriendsForTag();
  },
});
