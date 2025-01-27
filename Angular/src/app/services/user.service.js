angular.module("myApp").service("UserService", [
  "$rootScope",
  "$window",
  "$http",
  function ($rootScope, $window, $http) {
    $rootScope.userData = localStorage.getItem("user_id");
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

    this.fetchUserData = async function () {
      if (cachedUserData) {
        return cachedUserData;
      }

      if (!$rootScope.userData) {
        console.warn("No user ID found in local storage.");
        return null;
      }

      try {
        const response = await $http.post(
          `http://localhost/codeigniter/index.php/getUser?id=${$rootScope.userData}`
        );

        if (response.data.status === "success") {
          cachedUserData = response.data.user;
          return cachedUserData;
        } else {
          console.error("Error fetching user data:", response.data.message);
          return null;
        }
      } catch (error) {
        console.error("Error fetching user data:", error);
        return null;
      }
    };

    this.isAuthenticated = function () {
      return !!localStorage.getItem("user_id");
    };
  },
]);
