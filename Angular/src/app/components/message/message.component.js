"use strict";
angular.module("myApp").component("message", {
  templateUrl: "./app/components/message/message.html",
  bindings: {
    selectedChat: "<",
  },
  controller: function ($scope, $timeout, $compile, UserService, $http) {
    $scope.userDetails = UserService.getUserData();
    $scope.selectedFile = null;
    $scope.message = "";
    $scope.chatData = null;
    
    this.$onChanges = function (changes) {
      if (changes.selectedChat) {
        $scope.chatData = changes.selectedChat.currentValue;
        console.log($scope.chatData);
      }
    }

    $scope.getLastSeenMessage = function (lastMessage) {
      if (!lastMessage) {
        return "No messages yet";
      }
    
      const messageDate = new Date(lastMessage);
      const today = new Date();
      
      const isToday =
        messageDate.toDateString() === today.toDateString(); // Compare only the date part
    
      if (isToday) {
        const hours = messageDate.getHours();
        const minutes = messageDate.getMinutes().toString().padStart(2, '0');
        const ampm = hours >= 12 ? 'pm' : 'am';
        const formattedTime = `${hours % 12 || 12}:${minutes} ${ampm}`;
        return `Last seen today at ${formattedTime}`;
      } else {
        const formattedDate = messageDate.toLocaleDateString();
        const formattedTime = messageDate.toLocaleTimeString([], {
          hour: '2-digit',
          minute: '2-digit',
          hour12: true,
        });
        return `Last seen on ${formattedDate} at ${formattedTime}`;
      }
    };

    $scope.adjustHeight = function (event) {
      const textarea = event.target;
      textarea.style.height = 'auto'; 
      textarea.style.height = `${textarea.scrollHeight}px`; 
    };
    
    $scope.triggerFileInput = function() {
      console.log("Clicked");
      document.getElementById('fileInput').click();
    }

    $scope.logSelectedFile = function() {
      const fileInput = document.getElementById('fileInput');
      if (fileInput.files && fileInput.files.length > 0) {
        const file = fileInput.files[0]; 
        $scope.$apply(() => {
          $scope.selectedFile = file; // Update AngularJS scope
        });
        // $scope.selectedFile = file;
        $scope.openDialog();
        console.log('Selected File:', $scope.selectedFile);
      } else {
        console.log('No file selected.');
      }
    }

    $scope.openDialog = function() {
      const dialog = document.querySelector("#fileDialog");
      dialog.showModal();
    }
    
    $scope.closeDialog = function() {
      const dialog = document.querySelector("#fileDialog");
      dialog.close();
    }


    $scope.sendMessage = function(){
      if ($scope.message.trim()) {
        console.log($scope.message);
        $scope.message = '';
      }
    }


  },
});
