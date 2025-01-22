var app = angular.module("myApp", ["ngRoute"]);

app
  .config(function ($routeProvider) {
    $routeProvider
      .when("/home", {
        templateUrl: "./app/components/feed/feed.component.html",
        controller: "feedController",
      })
      .when("/profile", {
        templateUrl: "./app/components/profile/profile.component.html",
        controller: "profile",
      })
      .when("/contactDetails", {
        templateUrl:
          "./app/components/contactDetails/contactDetails.component.html",
        controller: "contactDetails1",
      })
      .when("/friend_search", {
        templateUrl: "./app/components/friend_search/fs.html",
        controller: "FriendSearchController",
      })
      .when("/friend_request", {
        templateUrl: "./app/components/friend_request/fr.html",
        controller: "FriendRequestController",
      })
      .when("/notification", {
        templateUrl: "./app/components/notification/notification.html",
        controller: "NotificationController",
      })
      .when("/signup", {
        templateUrl: "./app/components/signup/signup.component.html",
        controller: "signup",
      })
      .when("/login", {
        templateUrl: "./app/components/login/login.component.html",
        controller: "login",
      })
      .when("/user", {
        templateUrl: "./app/components/userProfile/user.component.html",
        controller: "user",
      })
      .when("/user-post/:uId", {
        templateUrl: "./app/components/userpost/userpost.component.html",
        controller: "user-feed",
      })
      .when("/friends/:uId", {
        templateUrl: "./app/components/userfriend/userfriend.component.html",
        controller: "userFriendsController",
      })
      .when("/:userName/:userId", {
        templateUrl: "./app/components/userProfile/user.component.html",
        controller: "user",
      })
      .when("/create-post", {
        templateUrl: "./app/components/createPost/createpost.component.html",
        controller: "createPost",
      })
      .when("/dashboard", {
        redirectTo: "/login",
      })
      .when("/chat", {
        templateUrl:
          "./app/components/chatApplication/chatApplication.component.html",
      })
      .otherwise({
        redirectTo: "/login",
      });
  })
  .run([
    "$rootScope",
    "$location",
    "UserService",
    function ($rootScope, $location, UserService) {
      $rootScope.$on("$routeChangeStart", function (event, next, current) {
        const allowedRoutes = ["/login", "/signup"];
        if (
          !UserService.isAuthenticated() &&
          !allowedRoutes.includes($location.path())
        ) {
          event.preventDefault();
          $location.path("/login");
        }
      });
    },
  ]);

app.controller("HomeController", function ($scope) {
  $scope.message = "Welcome to the Home Page!";
});

app.controller("MainController", function ($scope, $location) {
  $scope.isLoginOrSignupPage = function () {
    const currentPath = $location.path();
    return currentPath === "/login" || currentPath === "/signup";
  };
  //story should be display at home page
  $scope.isHomePage = function () {
    const currentPath = $location.path();
    return currentPath === "/home";
  };
});
