angular.module("myApp").component("storyBar", {
  templateUrl: "./app/components/story/story.html",
  controller: function ($http, $element, UserService, $sce) {
    this.stories = [];
    this.activeStory = null;
    this.currentUser = UserService.getUserData(); // Replace with the actual current user ID

    const API_BASE_URL = "http://localhost/codeigniter/index.php"; // Replace with your actual backend URL
    // Example function in your controller
    // Define helper functions in your controller
    this.isImage = function (mediaUrl) {
      const imageExtensions = ["jpg", "jpeg", "png", "gif", "webp"];
      if (!mediaUrl) {
        alert("no media url specified");
        this.activeStory = null;
      }
      const extension = mediaUrl.split(".").pop().toLowerCase();
      return imageExtensions.includes(extension);
    };

    this.isVideo = function (mediaUrl) {
      const videoExtensions = ["mp4", "webm", "ogg"];
      if (!mediaUrl) {
        alert("no media url specified");
        this.activeStory = null;
      }
      const extension = mediaUrl.split(".").pop().toLowerCase();
      return videoExtensions.includes(extension);
    };

    this.fetchStories = function () {
      $http
        .get(`${API_BASE_URL}/get-stories/${this.currentUser}`)
        .then((response) => {
          this.stories = response.data;
          console.log(this.stories);
          this.stories.forEach((story) => {
            story.isCurrentUserStory = story.user_id == this.currentUser;
            $http
              .get(
                `${API_BASE_URL}/is-viewed-by-user/${story.story_id}?userId=${this.currentUser}`
              )
              .then((viewedResponse) => {
                story.viewed = viewedResponse.data;
              });

            $http
              .get(
                `${API_BASE_URL}/is-liked/${story.story_id}?userId=${this.currentUser}`
              )
              .then((likedResponse) => {
                story.liked = likedResponse.data.length > 0;
              });
          });
        })
        .catch((error) => {
          alert("Network error in fetching stories");
        });
    };

    // Trigger file input click when "+" button is clicked
    this.triggerFileInput = function () {
      const fileInput = $element[0].querySelector("#file-input");
      fileInput.click(); // Trigger file input dialog
    };

    // Handle the file selection and upload the file
    this.uploadMediaFile = function () {
      const fileInput = $element[0].querySelector("#file-input");
      const file = fileInput.files[0]; // Get the selected file

      if (!file) {
        alert("Please select a file to upload");
        return;
      }
      console.log(file);
      // Create a FormData object
      const formData = new FormData();
      formData.append("userId", this.currentUser); // Add userId as a form field
      formData.append("media", file); // Add the file to the form data
      $http
        .post(`${API_BASE_URL}/upload-story`, formData, {
          transformRequest: angular.identity,
          headers: { "Content-Type": undefined }, // Let the browser set the content type
        })
        .then((response) => {
          if (response.data.status == "success") {
            alert("File uploaded successfully");
            this.fetchStories();
          } else if (response.data.status == "error") {
            alert("Error uploading file", response.messages);
          } else {
            alert("Failed to upload file");
          }
        })
        .catch((error) => {
          alert("Error uploading file. Please try again.");
        });
    };

    // Open the selected story
    this.openStory = function (story) {
      this.activeStory = story;
      $http
        .get(`${API_BASE_URL}/get-story-view/${this.activeStory.story_id}`)
        .then((response) => {
          this.activeStory.views = response.data;
        })
        .catch((error) => {
          alert("Error fetching story views. Please try again.");
        });
      $http
        .get(`${API_BASE_URL}/get-story-likes/${this.activeStory.story_id}`)
        .then((response) => {
          this.activeStory.likes = response.data;
        })
        .catch((error) => {
          alert("Error fetching story likes. Please try again.");
        });
      if (!story.viewed) {
        $http
          .post(
            `${API_BASE_URL}/mark-story-viewed/${this.activeStory.story_id}`,
            {
              userId: this.currentUser,
            }
          )
          .then((response) => {
            this.activeStory.viewed = response.data;
          })
          .catch((error) => {
            alert("Error marking story as viewed. Please try again.");
          });
      }
    };

    // Close the story modal
    this.closeStory = function () {
      this.activeStory = null;
    };

    // Navigate to the next unviewed story
    this.nextUnviewedStory = function () {
      const nextStory = this.stories.find((s) => !s.viewed);
      if (nextStory) {
        this.openStory(nextStory);
      } else {
        this.closeStory();
      }
    };
    // Like the active story
    this.like = function (story) {
      this.activeStory = story;
      if (!this.activeStory.liked) {
        $http
          .get(
            `${API_BASE_URL}/like/${this.activeStory.story_id}?userId=${this.currentUser}`
          )
          .then((response) => {
            this.activeStory.liked = response.data;
          })
          .catch((error) => {
            alert("Error liking the story. Please try again.");
          });
      }
    };
    // Fetch stories when the component is initialized
    this.$onInit = function () {
      this.fetchStories();
    };
  },
});
