"use strict";
angular.module("myApp").component("chat", {
  templateUrl: "./app/components/chat/chat.html",
  bindings: {
    onChatClick: "&",
  },
  controller: function ($scope, $http, UserService) {

    const userId = UserService.getUserData();
    $scope.searchs = "";
    // Function to check if input is empty
    $scope.checkInput = function () {
      return $scope.searchs.trim() !== "";
    };
    const ctrl = this;

    // this.displayChatDetails = function (chat){
    //   console.log("Clicked chat ", chat);
    // }

    ctrl.toggleMenu = function ($event, chat) {
      $event.stopPropagation();
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

      this.displayChatDetails = function (chat){
        this.onChatClick({chat: chat})
      }
  },
});
