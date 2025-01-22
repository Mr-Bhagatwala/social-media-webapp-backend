"use strict";

angular
  .module("myApp")
  .controller("createPost", function ($scope, $http, $window, UserService) {
    $scope.post = {
      content: "",
      media: [],
    };

    $scope.mediaPreview = [];

    // Handle file uploads
    $scope.handleFileUpload = function (input) {
      const files = input.files;
      if (files && files.length > 0) {
        for (let i = 0; i < files.length; i++) {
          const file = files[i];
          const reader = new FileReader();
          reader.onload = function (e) {
            $scope.$apply(function () {
              $scope.mediaPreview.push({
                url: e.target.result,
                file: file,
                type: file.type,
              });
            });
          };
          reader.readAsDataURL(file);
          $scope.post.media.push(file);
        }
      }
    };

    // Remove media from preview and list
    $scope.removeMedia = function (index) {
      $scope.mediaPreview.splice(index, 1);
      $scope.post.media.splice(index, 1);
    };

    // Submit post
    $scope.submitPost = function (form) {
      // if (form.$valid) {
      //   alert("Username submitted: " + $scope.post.content);
      // } else {
      //   alert("Please correct the errors in the form.");
      // }
      // return;
      const userId = UserService.getUserData();
      const formData = new FormData();
      formData.append("user_id", userId);
      formData.append("content", $scope.post.content);

      $scope.post.media.forEach((file) => {
        formData.append("media[]", file);
      });

      $http
        .post("http://localhost/codeigniter/index.php/posts/create", formData, {
          headers: { "Content-Type": undefined },
          transformRequest: angular.identity,
        })
        .then(function (response) {
          if (response.data.status == "success") {
            alert("Post created successfully!");
            $scope.post = { content: "", media: [] };
            $scope.mediaPreview = [];
            document.getElementById("mediaFiles").value = ""; // Reset file input
            $window.history.back();
          } else {
            alert("Error in backend: ".response.data.message);
          }
        })
        .catch(function (error) {
          console.error("Error creating post:", error);
          alert("Error creating post. Please try again.");
        });
    };

    $scope.goBack = function () {
      // Using AngularJS's $window to navigate to the previous page
      $window.history.back();
    };
  });
