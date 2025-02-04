angular.module("myApp").component("manageNetwork", {
    templateUrl: "./app/components/manageNetwork/manageNetwork.component.html",
    controller: function ($scope, $http, UserService,$location) {
      const userId = UserService.getUserData();
  
      $scope.connections = null;
      $scope.contacts = null;
      $scope.posts = null
  
      $scope.fetchConnections = function () {
        $http
          .get(`http://localhost/codeigniter/index.php/get-friends/${userId}`)
          .then(function (response) {
            if (response.data.status === "success") {
              $scope.connections = response.data.data.length;
            } else {
              console.log("Error in fetching connections");
            }
          })
          .catch(function (error) {
            console.log("Error in fetching connections:", error);
          });
      };
      $scope.fetchConnections();
      $scope.goToUserFriends = function () {
        $location.path(`/friends/${userId}`);
    };




      $scope.fetchContacts = function(){
        const url = `http://localhost/codeigniter/index.php/get-all-chats/${userId}`;
        $http
            .get(url)
            .then(function (response) {
                if (response.data.status === "success") {
                    $scope.contacts = response.data.data.length
                } else {
                    console.warn("No chats found in  fetch chat function manage network component....");
                }
            })
            .catch(function (response) {
                console.error("Error in fetching chats:", response);
                alert("Error in fetching chats in manage network component");
            });
      }

      $scope.fetchContacts();

      $scope.goToContacts = function () {
        $location.path("/chat");
    };



      $scope.fetchPost = function () {
        const url = `http://localhost/codeigniter/index.php/posts/getAllPostOfUser`;
        const requestData = { user_id: userId }; // Replace 'userId' with actual variable
    
        $http
            .post(url, requestData) // Use POST instead of GET
            .then(function (response) {
                if (response.data.status === "success") {
                    $scope.posts = response.data.data.length; // Store all posts in $scope.posts
                    console.log("Fetched Posts:", $scope.posts);
                } else {
                    console.warn("No posts found in fetchPost function (manage network component).");
                }
            })
            .catch(function (response) {
                console.error("Error fetching posts:", response);
                alert("Error fetching posts in manage network component");
            });
    };
    
    $scope.fetchPost(); // Call the function

    $scope.goToUserPosts = function () {
        $location.path(`/user-post/${userId}`);
    };
    


    },
  });
  