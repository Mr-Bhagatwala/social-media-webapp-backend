"use strict";
angular.module("myApp").component("chat", {
  templateUrl: "./app/components/chat/chat.html",
  bindings: {
    onChatClick: "&",
  },
  controller: function ($scope, $http, UserService) {

    const userId = UserService.getUserData();
    $scope.searchs = "";
    $scope.chats = [];
    $scope.isThere = true;
    
    $scope.checkInput = function () {
      return $scope.searchs.trim() !== "";
    };
    const ctrl = this;

    $scope.fetchChats = function () {
      $scope.isThere = true;
      const url = `http://localhost/codeigniter/index.php/get-all-chats/${userId}?searchQuery=${encodeURIComponent($scope.searchs)}`;
      $http
          .get(url)
          .then(function (response) {
              if (response.data.status === "success") {
                  $scope.chats = response.data.data;
  
                  // Add hover and toggle to all chats
                  angular.forEach($scope.chats, function (chat) {
                      chat.hover = false;
                      chat.toggle = false;
                  });
              } else {
                $scope.isThere = false;
                // alert("No chats found");
              }
          })
          .catch(function (response) {
              alert("Error in fetching chats");
          });
    };

    $scope.$watch("searchs", function (newVal, oldVal) {
      // console.log($scope.searchs);
      
      if (newVal !== oldVal) {
        console.log("I got triggered");
        
        $scope.fetchChats();
      }
    });

    ctrl.toggleMenu = function ($event, chat) {
      $event.stopPropagation();
      console.log(chat.toggle);
      chat.toggle = !chat.toggle;
    };

    ctrl.muteNotifications = function (chat) {
      chat.is_muted = chat.is_muted == 1 ? 0 : 1;
      if (chat.is_muted) {
        $http
          .post(
            `http://localhost/codeigniter/index.php/mute-chat/${userId}/${chat.chat_id}`
          )
          .then(function (response) {
            alert(response.data.message);
          })
          .catch(function (error) {
            alert(error);
          });
      } else {
        $http
          .post(
            `http://localhost/codeigniter/index.php/unmute-chat/${userId}/${chat.chat_id}`
          )
          .then(function (response) {
            alert(response.data.message);
          })
          .catch(function (error) {
            alert(error);
          });
      }
      chat.toggle = false;
    };
    ctrl.pinChat = function (chat) {
      chat.pinned = chat.pinned == 1 ? 0 : 1;
      if (chat.pinned) {
        $http
          .post(
            `http://localhost/codeigniter/index.php/pin-chat/${chat.chat_id}`
          )
          .then(function (response) {
            alert(response.data.message);
            $http
              .get(
                `http://localhost/codeigniter/index.php/get-all-chats/${userId}`
              )
              .then(function (response) {
                console.log(response.data);
                $scope.chats = response.data.data;
                //add hover to all
                angular.forEach($scope.chats, function (chat) {
                  chat.hover = false;
                  chat.toggle = false;
                });
              })
              .catch(function (response) {
                alert("Error in fetching chats");
              });
          })
          .catch(function (error) {
            alert(error);
          });
      } else {
        $http
          .post(
            `http://localhost/codeigniter/index.php/unpin-chat/${chat.chat_id}`
          )
          .then(function (response) {
            alert(response.data.message);
            $http
              .get(
                `http://localhost/codeigniter/index.php/get-all-chats/${userId}`
              )
              .then(function (response) {
                console.log(response.data);
                $scope.chats = response.data.data;
                //add hover to all
                angular.forEach($scope.chats, function (chat) {
                  chat.hover = false;
                  chat.toggle = false;
                });
              })
              .catch(function (response) {
                alert("Error in fetching chats");
              });
          })
          .catch(function (error) {
            alert(error);
          });
      }
      chat.toggle = false;
    };
    ctrl.blockChat = function (chat) {
      chat.is_blocked = chat.is_blocked == 1 ? 0 : 1;
      console.log(chat.is_blocked);
      if (chat.is_blocked) {
        $http
          .post(
            `http://localhost/codeigniter/index.php/block-user/${userId}/${
              userId == chat.sender_id ? chat.receiver_id : chat.sender_id
            }`
          )
          .then(function (response) {
            alert(response.data.message);
          })
          .catch(function (error) {
            alert(error);
          });
      } else {
        $http
          .post(
            `http://localhost/codeigniter/index.php/unblock-user/${userId}/${
              userId == chat.sender_id ? chat.receiver_id : chat.sender_id
            }`
          )
          .then(function (response) {
            alert(response.data.message);
          })
          .catch(function (error) {
            alert(error);
          });
      }
      chat.toggle = false;
    };
    ctrl.deleteChat = function (chat) {
      $http
        .post(
          `http://localhost/codeigniter/index.php/delete-chat/${chat.chat_id}`
        )
        .then(function (response) {
          alert(response.data.message);
          $http
            .get(
              `http://localhost/codeigniter/index.php/get-all-chats/${userId}`
            )
            .then(function (response) {
              console.log(response.data);
              $scope.chats = response.data.data;
              //add hover to all
              angular.forEach($scope.chats, function (chat) {
                chat.hover = false;
                chat.toggle = false;
              });
            })
            .catch(function (response) {
              alert("Error in fetching chats");
            });
        })
        .catch(function (error) {
          alert(error);
        });
      chat.toggle = false;
    };
    $http
      .get(`http://localhost/codeigniter/index.php/get-all-chats/${userId}`)
      .then(function (response) {
        console.log(response.data);
        $scope.chats = response.data.data;
        //add hover to all
        angular.forEach($scope.chats, function (chat) {
          chat.hover = false;
          chat.toggle = false;
        });
      })
      .catch(function (response) {
        alert("Error in fetching chats");
      });

    console.log("I am inside chat hello");
    
    this.displayChatDetails = function (chat){
      this.onChatClick({chat: chat})
    }
  },
});
