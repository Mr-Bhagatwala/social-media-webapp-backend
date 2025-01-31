"use strict";

angular
  .module("myApp")
  .controller(
    "NotificationController",
    function ($scope, $http, $window, UserService) {
      let id = UserService.getUserData();
      $scope.selectedFilter = 'all';
      $scope.notifications = [];
      $scope.filteredNotifications = [];
      $scope.offset = 0; // Starting offset
      $scope.limit = 50; // Number of notifications to load per request
      $scope.loading = false; // Prevent multiple requests
      $scope.allLoaded = false; // Flag to indicate if all notifications are loaded

      // Function to load notifications
      $scope.loadNotifications = function () {
        if ($scope.loading || $scope.allLoaded) return;
        $scope.loading = true;

        $http
          .get(
            `http://localhost/codeigniter/index.php/get-notifications/${id}?offset=${$scope.offset}&limit=${$scope.limit}`
          )
          .then(function (response) {
            if (response.data && response.data.status == "success") {
              const newNotifications = response.data.data;
              if (newNotifications && newNotifications.length > 0) {
                $scope.notifications =
                  $scope.notifications.concat(newNotifications);
                $scope.offset += $scope.limit; // Update offset
                console.log($scope.notifications);
                $scope.filteredNotifications = $scope.notifications;
              } else {
                $scope.allLoaded = true; // No more notifications
              }
            }
          })
          .catch(function (err) {
            console.log("Error occurred: ", err.message);
          })
          .finally(function () {
            $scope.loading = false; // Reset loading state
          });
      };


      $scope.setFilter = function (filter) {
        $scope.selectedFilter = filter;
        $scope.applyFilter(); // Reapply filter when clicked
    };

    $scope.applyFilter = function () {
      if ($scope.selectedFilter === 'all') {
          $scope.filteredNotifications = $scope.notifications;
      } else if ($scope.selectedFilter === 'unread') {
          $scope.filteredNotifications = $scope.notifications.filter(n => n.is_read === "0");
      } else if ($scope.selectedFilter === 'read') {
          $scope.filteredNotifications = $scope.notifications.filter(n => n.is_read === "1");
      }
  };

      //delete notification 
      $scope.deleteNotification = function(notification_id){
        $http
        .post("http://localhost/codeigniter/index.php/notification/deleteNotification", {
          id:notification_id,
        })
        .then(function (response) {
          if (response.data.status === "success") {
            alert("Notification deleted successfully");
            $scope.notifications = $scope.notifications.filter(function (notification) {
              return notification.id !== notification_id;
          });
            $scope.loadNotifications();
          } else {
            alert("Failed to delete notification");
          }
        })
        .catch(function (error) {
          console.error("Error while deleting the notification:", error);
        });
      }


      //mark as read notifications

      $scope.markAsRead = function(notification_id){
        $http
        .post("http://localhost/codeigniter/index.php/notification/markAsReadNotification", {
          id:notification_id,
        })
        .then(function (response) {
          if (response.data.status === "success") {
            alert("Notification set as read successfully");
            for (let i = 0; i < $scope.notifications.length; i++) {
              if ($scope.notifications[i].id === notification_id) {
                $scope.notifications[i].is_read = '1'; // Mark as read
                break;
              }
            }
            $scope.loadNotifications();
          } else {
            alert("Failed to set as read notification");
          }
        })
        .catch(function (error) {
          console.error("Error while deleting   set as read the notification:", error);
        });
      }

      $scope.markAllAsRead = function() {
        $http
        .post("http://localhost/codeigniter/index.php/notification/markAsReadAllNotification", {
            user_id: id,  // Make sure `id` is properly defined
        })
        .then(function (response) {
          console.log("user id for notification ", id)
            if (response.data.status === "success") {
              console.log("user id for notification ", id)
                alert("All Notifications marked as read successfully.");
                $scope.loadNotifications();  // Reload notifications to reflect the changes
            } else {
                alert("Failed to mark notifications as read.");
            }
        })
        .catch(function (error) {
            console.error("Error while marking notifications as read:", error);
        });
    };

    $scope.deleteAllNotifications = function() {
      $http
      .post("http://localhost/codeigniter/index.php/notification/deleteAllNotification", {
          user_id: id,  // Make sure `id` is properly defined
      })
      .then(function (response) {
        console.log("user id for notification ", id)
          if (response.data.status === "success") {
            console.log("user id for notification ", id)
              alert("All Notifications delete successfully.");
              $scope.loadNotifications();  // Reload notifications to reflect the changes
          } else {
              alert("Failed to delete all  notifications ");
          }
      })
      .catch(function (error) {
          console.error("Error while delete all  notifications:", error);
      });
  };
    



      // Clear all notifications
      $scope.isOptionsVisible = false;  // Flag to control visibility of option buttons

      $scope.toggleOptions2 = function() {
        $scope.isOptionsVisible = !$scope.isOptionsVisible;  // Toggle visibility
      };
      


      // Initial load
      $scope.loadNotifications();

      // Infinite scroll logic using $window
      angular.element($window).bind("scroll", function () {
        const scrollTop =
          $window.pageYOffset || document.documentElement.scrollTop;
        const windowHeight = $window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;

        if (scrollTop + windowHeight >= documentHeight - 100) {
          $scope.loadNotifications();
        }
      });

      $scope.notifications.forEach((n) => (n.openOption = false)); // Ensure all are closed
      $scope.toggleOptions = function (event, notification) {
        event.stopPropagation(); // Prevent event bubbling
        // Close other open options
        $scope.notifications.forEach((n) => {
          if (n !== notification) n.openOption = false;
        });

        // Toggle current notification options
        notification.openOption = !notification.openOption;
      };

      // Close options when clicking outside
      document.addEventListener("click", function () {
        $scope.$apply(function () {
          $scope.notifications.forEach((n) => (n.openOption = false));
        });
      });
    }
  );
