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
    $scope.messages = [];
    $scope.chatData = null;
  


    console.log("user details ----> ",$scope.userDetails);


    this.$onChanges = function (changes) {
      if (changes.selectedChat) {
        $scope.chatData = changes.selectedChat.currentValue;
        console.log("chatData : ", $scope.chatData);
      }
      $scope.getPastMessages();
    };


    

    $scope.getParentMessage = function (parentMessageId) {
      return $scope.messages.find((msg) => msg.message_id === parentMessageId) || {};
    };
    
    $scope.getPastMessages = function () {
      if (!$scope.chatData || !$scope.chatData.chat_id) {
        console.warn("chatData or chat_id is not available");
        return;
      }
      var chatId = $scope.chatData.chat_id; // Assuming chatData is defined in your scope
      console.log(chatId)

      $http
        .get(
          "http://localhost/codeigniter/index.php/message/past?chatId=" + chatId
        )
        .then(function (response) {
          console.log("Past data of user:", response.data);
          if (response.data) {
            $scope.messages = response.data;
            // $scope.getPastMessages();
          }
        })
        .catch(function (error) {
          console.error("Error getting past messages:", error);
        });
    };

    // console.log("Message before trimming:", $scope.message);

    $scope.menuIndex = null;

    $scope.toggleMenu = function (index, event) {
      event.stopPropagation(); // Prevent click propagation
      $scope.menuIndex = $scope.menuIndex === index ? null : index; // Toggle menu
    };
    
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

    $scope.replyMessage = null; // Store the message being replied to
    $scope.replyMessage_id = null;
    $scope.replyToMessage = function (message) {
      $scope.replyMessage = message; // Store the message to reply to
      $scope.replyMessage_id = message.message_id;
      // $scope.message = ""; // Clear the input message
      if (message.file_url) {
        $scope.replyMessage.file_name = message.file_url.split("/").pop(); // Extract file name from URL
      }
    };

    $scope.clearReply = function () {
      $scope.replyMessage = null; // Clear the reply message
    };



    $scope.adjustHeight = function (event) {
      const textarea = event.target;
      textarea.style.height = "auto";
      textarea.style.height = `${textarea.scrollHeight}px`;
    };

    $scope.triggerFileInput = function () {
      console.log("Clicked");
      document.getElementById("fileInput").click();
    };

    $scope.logSelectedFile = function () {
      const fileInput = document.getElementById("fileInput");
      if (fileInput.files && fileInput.files.length > 0) {
        const file = fileInput.files[0];
        $scope.$apply(() => {
          $scope.selectedFile = file; // Update AngularJS scope
        });
        // $scope.selectedFile = file;
        $scope.openDialog();
        console.log("Selected File:", $scope.selectedFile);
      } else {
        console.log("No file selected.");
      }
    };

    $scope.openDialog = function () {
      const dialog = document.querySelector("#fileDialog");
      dialog.showModal();
    };

    $scope.closeDialog = function () {
      const dialog = document.querySelector("#fileDialog");
      dialog.close();
    };

    $scope.deleteMessage = function (message_id) {
      $http
        .post("http://localhost/codeigniter/index.php/message/delete", {
          message_id: message_id,
        })
        .then(function (response) {
          if (response.data.status === "success") {
            alert("Message deleted successfully");
            // Optionally, you can remove the message from the local `messages` array
            $scope.getPastMessages();
            $scope.replyBox = false;
            $scope.messages = $scope.messages.filter(
              (message) => message.message_id !== message_id
            );
          } else {
            alert("Failed to delete message");
          }
        })
        .catch(function (error) {
          console.error("Error while deleting the message:", error);
        });
    };

    $scope.sendFile = function () {
      const formData = new FormData();
      formData.append("file", $scope.selectedFile);
      formData.append("chat_id", $scope.chatData.chat_id); // Example chat_id
      formData.append("sender_id", $scope.userDetails); // Example sender_id

      $http
        .post(
          "http://localhost/codeigniter/index.php/message/sendFile",
          formData,
          {
            headers: { "Content-Type": undefined },
          }
        )
        .then((response) => {
          console.log("File upload success:", response.data);
          $scope.selectedFile = null; // Clear the selected file after successful upload
          alert("file send successfully");
          $scope.closeDialog();
          $scope.replyBox = false;
          $scope.getPastMessages();
          // $scope.getLastSeenMessage();
        })
        .catch((error) => {
          console.error("Error uploading file:", error);
        });
    };

    $scope.sendMessage = function (message) {
      // alert($scope.message)
      if (!message || !message.trim()) {
        return; // Exit if the message is empty or whitespace
      }
      var newMessage = {
        chat_id: $scope.chatData.chat_id,
        sender_id: $scope.userDetails,
        message_text:message,
      };
      $http
        .post("http://localhost/codeigniter/index.php/message/send", newMessage)
        .then((response) => {
          $scope.messages.push(response.data);
          document.getElementById("myTextarea").value = "";
          $scope.message = ""; // This will clear the textarea
          $scope.getPastMessages();
        })
        .catch((error) => {
          console.error("Error sending message:", error);
        });
    };
    

    $scope.sendReplyMessage = function(message){
        console.log("Reply message called" , $scope.replyMessage_id);
        if (!message || !message.trim()) {
          return; // Exit if the message is empty or whitespace
        }
      
        var newMessage = {
          chat_id: $scope.chatData.chat_id,
          sender_id: $scope.userDetails,
          message_text: message,
          parent_message_id:$scope.replyMessage_id
        };
      
        $http
          .post("http://localhost/codeigniter/index.php/message/reply", newMessage)
          .then((response) => {
            // Add the new message to the messages array
            $scope.messages.push(response.data);
            
            // Clear the textarea by resetting $scope.message
            $scope.message = ""; // This will clear the textarea
            $scope.clearReply();
            $scope.replyBox = false;
            $scope.getPastMessages();
          })
          .catch((error) => {
            console.error("Error sending message:", error);
          });
    }


    $scope.sendFileReplyMessage = function () {
      console.log("sendFileReplyMessage called")
      const formData = new FormData();
      formData.append("file", $scope.selectedFile);
      formData.append("chat_id", $scope.chatData.chat_id); // Example chat_id
      formData.append("sender_id", $scope.userDetails); // Example sender_id
      formData.append("parent_message_id",$scope.replyMessage_id);

      $http
        .post(
          "http://localhost/codeigniter/index.php/message/fileReply",
          formData,
          {
            headers: { "Content-Type": undefined },
          }
        )
        .then((response) => {
          console.log("File upload success:", response.data);
          $scope.selectedFile = null; // Clear the selected file after successful upload
          alert("file send successfully");
          $scope.closeDialog();
          $scope.clearReply();
          $scope.replyBox = false;
          $scope.getPastMessages();
          // $scope.getLastSeenMessage();
        })
        .catch((error) => {
          console.error("Error uploading file:", error);
        });
    };
    
  },
});
