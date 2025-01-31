angular.module("myApp").service("UserService", [
  "$rootScope",
  "$window",
  "$http",
  function ($rootScope, $window, $http) {
    $rootScope.userData = localStorage.getItem("user_id");
    $rootScope.userName = "";
    let cachedUserData = null;

    this.getUserData = function () {
      return $rootScope.userData;
    };

    this.setUserData = function (userId) {
      localStorage.setItem("user_id", userId);
      $rootScope.userData = userId;
    };

    this.removeUserData = function () {
      localStorage.removeItem("user_id");
      $rootScope.userData = null;
      cachedUserData = null;
    };

    this.fetchUserData = function () {
      if (cachedUserData) {
        return Promise.resolve(cachedUserData); 
      }
    
      if (!$rootScope.userData) {
        console.warn("No user ID found in local storage.");
        return Promise.resolve(null); // Return null as a resolved promise
      }
    
      return $http
        .post(`http://localhost/codeigniter/index.php/getUser?id=${$rootScope.userData}`)
        .then(function (response) {
          if (response.data.status === "success") {
            cachedUserData = response.data.data; // Cache the user data
            return cachedUserData; // Return the cached data
          } else {
            console.error("Error fetching user data:", response.data.message);
            return null; // Return null if the response is not successful
          }
        })
        .catch(function (error) {
          console.error("Error fetching user data:", error);
          return null; // Return null in case of an error
        });
    };

    this.isAuthenticated = function () {
      return !!localStorage.getItem("user_id");
    };
  },
]);
