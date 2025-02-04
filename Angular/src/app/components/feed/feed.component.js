"use strict";

angular
  .module("myApp")
  .controller(
    "feedController",
    function ($scope, $http, $window, UserService, $location) {
      $scope.feeds = [];
      const userId = UserService.getUserData(); // Replace with dynamic user_id if available
      $scope.sortCriteria = "recent"; // Default sorting
      $scope.offset = 0; // Starting offset
      $scope.limit = 3; // Number of posts to load per request
      $scope.loading = false;
      $scope.allLoaded = false;

      // using loading for elminating multiple request
      // $scope.loadFeeds = function () {
      //   if ($scope.loading || $scope.allLoaded) return;
      //   $scope.loading = true;

      //   const sortParam = $scope.sortCriteria;
      //   console.log("sortParam", sortParam);
      //   $http
      //     .get(
      //       `http://localhost/codeigniter/index.php/posts/feed?user_id=${userId}&sort=${sortParam}&offset=${$scope.offset}&limit=${$scope.limit}`
      //     )
      //     .then(function (response) {
      //       if (response.data && response.data.length > 0) {
      //         console.log(response.data);
      //         const newFeeds = response.data.map((feed) => {
      //           feed.mediaFiles = feed.media ? feed.media.split(",") : [];
      //           feed.currentIndex = 0;
      //           feed.showComments = false;
      //           feed.comments = [];
      //           feed.replyingTo = null;
      //           feed.newComment = "";
      //           return feed;
      //         });
      //         $scope.feeds = $scope.feeds.concat(newFeeds);
      //         $scope.offset += $scope.limit;
      //       } else {
      //         $scope.allLoaded = true; // No more posts to load
      //       }
      //       $scope.loading = false;
      //     })
      //     .catch(function (error) {
      //       console.error("Error fetching feeds:", error);
      //       $scope.loading = false;
      //     });
      // };
      $scope.loadFeeds = function () {
        // Prevent multiple requests or loading beyond available data
        if ($scope.loading || $scope.allLoaded) return;
        $scope.loading = true;

        // Construct API URL with dynamic parameters
        const sortParam = $scope.sortCriteria;
        const apiUrl = `http://localhost/codeigniter/index.php/posts/feed?user_id=${userId}&sort=${sortParam}&offset=${$scope.offset}&limit=${$scope.limit}`;

        $http
          .get(apiUrl)
          .then(function (response) {
            if (response.data && response.data.status === "success") {
              const feeds = response.data.data;

              if (feeds && feeds.length > 0) {
                // Process and append new feeds
                const newFeeds = feeds.map((feed) => {
                  feed.mediaFiles = feed.media ? feed.media : [];
                  feed.currentIndex = 0;
                  feed.showComments = false;
                  feed.comments = [];
                  feed.replyingTo = null;
                  feed.newComment = "";
                  return feed;
                });
                $scope.feeds = $scope.feeds.concat(newFeeds);
                $scope.offset += $scope.limit;
              } else {
                // No more posts to load
                $scope.allLoaded = true;
              }
            } else {
              // Handle error from backend response
              console.error("Error from backend:", response.data.message);
              // alert(
              //   response.data.message ||
              //     "Unable to fetch feeds. Please try again."
              // );
            }
          })
          .catch(function (error) {
            // Handle HTTP or network errors
            console.error("HTTP Error:", error);
            alert(
              "An error occurred while fetching feeds. Please check your connection."
            );
          })
          .finally(function () {
            $scope.loading = false; // Reset loading state
          });
      };

      // Detect when user scrolls to the bottom of the page
      angular.element($window).bind("scroll", function () {
        const scrollTop =
          $window.pageYOffset || document.documentElement.scrollTop;
        const windowHeight = $window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;

        if (scrollTop + windowHeight >= documentHeight - 100) {
          // Load more feeds when close to the bottom
          $scope.loadFeeds();
        }
      });

      $scope.filterChange = function () {
        $scope.offset = 0;
        $scope.feeds = []; // Clear the existing feed list to prevent duplicates

        $scope.loadFeeds();
      };

      const getComments = function (feed) {
        $http
          .get(
            `http://localhost/codeigniter/index.php/posts/getcomments/${feed.post_id}`
          )
          .then(function (response) {
            if (response.data && response.data.status == "success") {
              feed.comments = response.data.data;
            } else {
              alert("Error:  Unable to fetch Comments of the post");
            }
          })
          .catch(function (error) {
            console.error("Error fetching comments:", error);
          });
      };
      $scope.toggleComments = function (feed) {
        feed.showComments = !feed.showComments;

        if (feed.showComments && feed.comments.length === 0) {
          getComments(feed);
        }
      };

      $scope.toggleLike = function (feed) {
        const initialLikeStatus = feed.islike;
        const initialLikeCount = feed.likesCount || 0;
        console.log("initial like sattus ", initialLikeStatus);
        feed.islike = !feed.islike;
        feed.likesCount = initialLikeStatus
          ? initialLikeCount - 1
          : initialLikeCount + 1;

        $http
          .post("http://localhost/codeigniter/index.php/posts/toggle_like", {
            post_id: feed.post_id,
            user_id: userId,
          })
          .then(
            function (response) {
              if (response.data.status === "success") {
                feed.likesCount = response.data.likes_count;
                feed.islike = response.data.action === "added";
              } else {
                feed.islike = initialLikeStatus;
                feed.likesCount = initialLikeCount;
                console.error("Unexpected response:", response.data);
              }
              console.log("in if , ", feed.islike);
            },
            function (error) {
              feed.islike = initialLikeStatus;
              feed.likesCount = initialLikeCount;
              console.error("Error toggling like:", error);
            }
          );
      };

      $scope.submitComment = function (
        postId,
        content,
        parentCommentId,
        authorId
      ) {
        const payload = {
          user_id: userId, // Replace with dynamic user_id
          content: content,
          parent_comment_id: parentCommentId,
          author_id: authorId,
        };

        $http
          .post(
            `http://localhost/codeigniter/index.php/posts/comment/${postId}`,
            payload
          )
          .then(function (response) {
            console.log("Comment posted successfully", response);
            const feed = $scope.feeds.find((f) => f.post_id === postId);
            if (feed) {
              // $scope.toggleComments(feed); // Reload comments
              getComments(feed);
              feed.commentCount++;
              if (parentCommentId === null) {
                // Clear the comment input for the post
                feed.newComment = "";
              } else {
                // Clear the reply input for a specific comment
                feed.replyContent = "";
              }
              console.log("mai andr huu if k andr");
            }
            console.log("feed is ", feed);
          })
          .catch(function (error) {
            console.error("Error posting comment:", error);
          });
      };

  
      $scope.loadFeeds(); // Initial load





      $scope.openDialog = function () {
        const dialog = document.querySelector("#postDialog");
        dialog.showModal();
      }
      $scope.closeDialog = function () {
        $scope.loadFeeds();
        setTimeout(function() {
          $window.location.reload();
      }, 300);
        const dialog = document.querySelector("#postDialog");
        dialog.close();
      };

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

        const userId = UserService.getUserData();
        const formData = new FormData();
        formData.append("user_id", userId);
        formData.append("content", $scope.post.content);

        $scope.post.media.forEach((file) => {
          formData.append("media[]", file);
        });

        $http
          .post(
            "http://localhost/codeigniter/index.php/posts/create",
            formData,
            {
              headers: { "Content-Type": undefined },
              transformRequest: angular.identity,
            }
          )
          .then(function (response) {
            if (response.data.status == "success") {
              alert("Post created successfully!");
              $scope.post = { content: "", media: [] };
              $scope.mediaPreview = [];
              document.getElementById("mediaFiles").value = ""; // Reset file input
              $scope.closeDialog();
              $scope.loadFeeds();
            } else {
              alert("Error in backend: ".response.data.message);
            }
          })
          .catch(function (error) {
            console.error("Error creating post:", error);
            alert("Error creating post. Please try again.");
          });
      };
    }
  );
