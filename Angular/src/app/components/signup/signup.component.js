
angular
  .module("myApp")
  .controller("signup", function ($scope, $http, $location) {
    $scope.name = "";
    $scope.email = "";
    $scope.password = "";

    $scope.onSubmit = function (signupForm) {
      if (signupForm.$valid) {
        // Prepare data to send to backend
        const postData = {
          name: $scope.name,
          email: $scope.email,
          password: $scope.password,
        };
        console.log("Form Submitted", postData);

        // Make POST request to backend
        $http
          .post("http://localhost/codeigniter/index.php/register", postData)
          .then(function (response) {
            console.log("Response from backend:", response.data);
            if (response.data.status === "success") {
              alert("Registration successful!");
              console.log("Response data signup " + response.data);

              // Store user_id in local storage if available
              if (response.data.userId) {
                localStorage.setItem("user_id", response.data.userId);
                $location.path("/profile");
              } else {
                alert("Error: User ID not found in response.");
              }
            } else {
              let errorMessage =
                response.data.errors ||
                response.data.message ||
                "An unknown error occurred.";
              alert("Error: " + errorMessage);
            }
          })
          .catch(function (error) {
            console.error("Error connecting to backend:", error);
            alert("An error occurred while registering. Please try again.");
          });
      }
    };
  });
