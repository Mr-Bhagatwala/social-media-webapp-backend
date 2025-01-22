// "use strict";

// angular
//   .module("myApp")
//   .controller("user-feed", function ($scope, $http, $routeParams) {
//     $scope.feeds = [];

//     const loadFeeds = function () {
//       $http
//         .get(
//           `http://localhost/codeigniter/index.php/posts/post-by-user?userId=${userId}`
//         )
//         .then(function (response) {
//           if (response.data) {
//             $scope.feeds = response.data.map((feed) => {
//               feed.mediaFiles = feed.media ? feed.media.split(",") : [];
//               feed.currentIndex = 0;
//               feed.showComments = false;
//               feed.comments = [];
//               feed.replyingTo = null;
//               feed.newComment = "";
//               return feed;
//             });
//             console.log(
//               "Feeds loaded successfully in the froneend:",
//               $scope.feeds
//             );
//           } else {
//             console.error("Invalid response data", response);
//           }
//         })
//         .catch(function (error) {
//           console.error("Error fetching feeds:", error);
//         });
//     };

//     $scope.toggleComments = function (feed) {
//       feed.showComments = !feed.showComments;

//       if (feed.showComments && feed.comments.length === 0) {
//         $http
//           .get(
//             `http://localhost/codeigniter/index.php/posts/getcomments/${feed.post_id}`
//           )
//           .then(function (response) {
//             feed.comments = response.data;
//           })
//           .catch(function (error) {
//             console.error("Error fetching comments:", error);
//           });
//       }
//     };

//     $scope.deletePost = function (postId) {
//       $http
//         .post(`http://localhost/codeigniter/index.php/posts/delete/${postId}`)
//         .then(function (reponse) {
//           if (reponse) {
//             alert("Post deleted Succesfully");
//           }
//         })
//         .catch(function (error) {
//           console.log("Error is deleting ", error);
//         });
//     };

//     $scope.submitComment = function (postId, content, parentCommentId) {
//       const payload = {
//         user_id: 1, // Replace with dynamic user_id
//         content: content,
//         parent_comment_id: parentCommentId,
//       };

//       $http
//         .post(
//           `http://localhost/codeigniter/index.php/posts/comment/${postId}`,
//           payload
//         )
//         .then(function () {
//           console.log("Comment posted successfully");
//           const feed = $scope.feeds.find((f) => f.post_id === postId);
//           if (feed) {
//             $scope.toggleComments(feed); // Reload comments
//           }
//         })
//         .catch(function (error) {
//           console.error("Error posting comment:", error);
//         });
//     };

//     loadFeeds();
//   });

"use strict";

angular
  .module("myApp")
  .controller("user-feed", function ($scope, $http, $routeParams, UserService) {
    $scope.feeds = [];
    const userId = UserService.getUserData(); // Replace with dynamic user_id if available
    const uId = $routeParams.uId;

    $scope.canDelete = userId == uId ? true : false;
    const loadFeeds = function () {
      $http
        .get(`http://localhost/codeigniter/index.php/posts/post-by-user`, {
          params: { uId: uId, user_id: userId },
        })
        .then(function (response) {
          if (response.data && response.data.status == "success") {
            console.log(response.data);
            $scope.feeds = response.data.data.map((feed) => {
              feed.mediaFiles = feed.media ? feed.media : [];
              feed.currentIndex = 0;
              feed.showComments = false;
              feed.comments = [];
              feed.replyingTo = null;
              feed.newComment = "";
              //  feed.islike = feed.islike || false;
              return feed;
            });
            console.log(
              "Feeds loaded successfully in the frontend:",
              $scope.feeds
            );
          } else {
            console.error("Invalid response data", response);
          }
        })
        .catch(function (error) {
          console.error("Error fetching feeds:", error);
        });
    };

    $scope.deletePost = function (postId) {
      if (userId == uId) {
        $http
          .post(`http://localhost/codeigniter/index.php/posts/delete/${postId}`)
          .then(function (response) {
            if (response.data.status == "success") {
              alert("Post deleted Succesfully");
              $scope.feeds = $scope.feeds.filter(function (feed) {
                return feed.post_id !== postId; // Fixed typo and use proper comparison
              });
            }
            loadFeeds();
          })
          .catch(function (error) {
            console.log("Error is deleting ", error);
          });
      }
    };

    $scope.toggleComments = function (feed) {
      feed.showComments = !feed.showComments;

      if (feed.showComments && feed.comments.length === 0) {
        $http
          .get(
            `http://localhost/codeigniter/index.php/posts/getcomments/${feed.post_id}`
          )
          .then(function (response) {
            feed.comments = response.data;
          })
          .catch(function (error) {
            console.error("Error fetching comments:", error);
          });
      }
    };

    // $scope.toggleLike = function (feed) {
    //   // Update the like state optimistically
    //   const initialLikeStatus = feed.islike;
    //   const initialLikeCount = feed.likesCount || 0;

    //   feed.islike = !feed.islike;
    //   feed.likesCount = initialLikeStatus
    //     ? initialLikeCount - 1
    //     : initialLikeCount + 1;

    //   $http
    //     .post("http://localhost/codeigniter/index.php/posts/toggle_like", {
    //       post_id: feed.post_id,
    //       user_id: userId,
    //     })
    //     .then(
    //       function (response) {
    //         if (response.data.status === "success") {
    //           // Confirm the like/dislike status and count from the server
    //           feed.likesCount = response.data.likes_count;
    //           feed.islike = response.data.action === "added";
    //         } else {
    //           // Revert changes in case of an unexpected response
    //           feed.islike = initialLikeStatus;
    //           feed.likesCount = initialLikeCount;
    //           console.error("Unexpected response:", response.data);
    //         }
    //       },
    //       function (error) {
    //         // Handle the error and revert changes
    //         feed.islike = initialLikeStatus;
    //         feed.likesCount = initialLikeCount;
    //         console.error("Error toggling like:", error);
    //       }
    //     );
    // };

    // $scope.submitComment = function (postId, content, parentCommentId) {
    //   const payload = {
    //     user_id: 1, // Replace with dynamic user_id
    //     content: content,
    //     parent_comment_id: parentCommentId,
    //   };

    //   $http
    //     .post(
    //       `http://localhost/codeigniter/index.php/posts/comment/${postId}`,
    //       payload
    //     )
    //     .then(function () {
    //       console.log("Comment posted successfully");
    //       const feed = $scope.feeds.find((f) => f.post_id === postId);
    //       if (feed) {
    //         $scope.toggleComments(feed); // Reload comments
    //       }
    //     })
    //     .catch(function (error) {
    //       console.error("Error posting comment:", error);
    //     });
    // };

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
          if (response.data.status == "success") {
            console.log("Comment posted successfully", response);
            const feed = $scope.feeds.find((f) => f.post_id === postId);
            if (feed) {
              // $scope.toggleComments(feed); // Reload comments
              feed.commentCount++;
              getComments(feed);
              if (parentCommentId === null) {
                // Clear the comment input for the post
                feed.newComment = "";
              } else {
                // Clear the reply input for a specific comment
                feed.replyContent = "";
              }
              // console.log("mai andr huu if k andr");
            }
            // console.log("feed is ", feed);
          }
        })
        .catch(function (error) {
          console.error("Error posting comment:", error);
        });
    };
    loadFeeds();
  });
