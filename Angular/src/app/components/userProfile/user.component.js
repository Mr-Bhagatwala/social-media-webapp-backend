"use strict";

angular
  .module("myApp")
  .controller(
    "user",
    function ($scope, $http, $location, UserService, $routeParams) {
      $scope.admin = true;
      $scope.isAuthenticated = UserService.isAuthenticated();
      const urlUserId = $routeParams.userId;
      const urlUserName = $routeParams.userName;
      const user_id = localStorage.getItem("user_id");
      console.log("I am from url " + urlUserId);
      $scope.requestStatus = "send request";

      if ($scope.isAuthenticated) {
        if (user_id === urlUserId) {
          $scope.admin = true;
          UserService.fetchUserData().then(function (user) {
            if (user) {
              populateUserProfile(user);
            } else {
              console.log("User not authenticated or data not found.");
            }
          });
        } else {
          $scope.admin = false;
          fetchProfileFromURL(urlUserName, urlUserId);
        }
      } else {                    
        console.log("User is not authenticated.");
      }

      // Function to fetch profile based on username and id from the URL
      async function fetchProfileFromURL(username, userId) {
        try {
          const response = await $http.post(
            `http://localhost/codeigniter/index.php/getUser?id=${userId}`
          );
          if (response.data.status === "success") {
            console.log(
              "Reached here abcdadsaajcaknavjal:::              ",
              response
            );
            populateUserProfile(response.data.user);
          } else {
            console.log("Failed to fetch user data from URL.");
          }
        } catch (error) {
          console.log("Error fetching user data from URL");
        }
      }

      function populateUserProfile(user) {
        $scope.userDetails = user;
        $scope.profile.basicDetails.profile_photo = user[0].profile_photo;
        $scope.profile.basicDetails.name = user[0].name;
        $scope.profile.basicDetails.bio = user[0].bio;
        $scope.profile.basicDetails.hometown = user[0].hometown;
        $scope.profile.contactDetails.primaryEmail = user[0].email;
      }

      console.log("I am user Id " + user_id);
      $scope.userData = "";
      $scope.profile = {
        basicDetails: {
          name: "",
          profile_picture: "",
          bio: "",
          hometown: "",
        },
        contactDetails: {
          primaryEmail: "",
          primaryPhone: "",
          alternateEmails: [],
          nae: "",
          nap: "",
          alternatePhones: [],
          linkedinUrl: null,
        },
        education: [],
        workHistory: [],
      };

      $scope.educationIdCounter = $scope.profile.education.length + 1;
      $scope.workIdCounter = $scope.profile.workHistory.length + 1;
      //   $scope.contactIdCounter = $scope.profile.contactDetails.length + 1;

      $scope.editType = null;
      $scope.editIndex = null;
      $scope.dialogTitle = "";
      $scope.editData = {};

      $scope.years = [];
      const currentYear = new Date().getFullYear();
      for (let year = 1950; year <= currentYear; year++) {
        $scope.years.push(year);
      }

      $scope.getButtonStatus = function () {
        const requestData = {
          sender_id: user_id,
          receiver_id: urlUserId,
        };
        $http
          .post(
            "http://localhost/codeigniter/index.php/getFriendRequestStatus",
            requestData
          )
          .then(function (response) {
            if (response.data.status == "success") {
              if (response.data.data == "accepted") {
                $scope.requestStatus = "connected";
              } else {
                $scope.requestStatus = "send  request";
              }
            }
          })
          .catch(function (error) {
            console.error(
              "Error while fetching  friend request status:",
              error
            );
            if (error.data) {
              alert("Error get button status error h : " + error.data.message);
            } else {
              alert(
                "Error while fetching  friend request status. Please try again."
              );
            }
          });
      };

      if (!$scope.admin) $scope.getButtonStatus();

      $scope.clearEndYear = function () {
        if ($scope.editData.isCurrent) {
          $scope.editData.endYear = null;
        }
      };

      $scope.onLogout = function () {
        console.log("I got clicked logout button");
        UserService.removeUserData();
        $location.path("/login");
      };

      $scope.openDialog = function (type, data, index) {
        $scope.editType = type;
        $scope.editIndex = index;

        if (type === "basicDetails") {
          $scope.dialogTitle = "Basic Details";
          $scope.editData = { ...$scope.profile.basicDetails };
          console.log($scope.editData);
        } else if (type === "contactDetails") {
          $scope.dialogTitle = "Contact Details";
          $scope.editData = { ...$scope.profile.contactDetails };

          // Ensure alternateEmails and alternatePhones are initialized
          if (!$scope.editData.alternateEmails) {
            $scope.editData.alternateEmails = [];
          }
          if (!$scope.editData.alternatePhones) {
            $scope.editData.alternatePhones = [];
          }
        } else if (type === "education") {
          $scope.dialogTitle = "Education";

          $scope.editData = data
            ? { ...data }
            : { institution: "", degree: "", startYear: "", endYear: "" };
          console.log($scope.editData);
        } else if (type === "work") {
          $scope.dialogTitle = "Work History";
          $scope.editData = data
            ? { ...data }
            : { position: "", company: "", startYear: "", endYear: "" };
        }
        const dialog = document.querySelector("#userDialog");
        dialog.showModal();
      };

      $scope.closeDialog = function closeDialog() {
        var dialog = document.querySelector("#userDialog");
        // $scope.selected
        dialog.close();
        $scope.editType = null;
        $scope.editIndex = null;
        $scope.editData = {};
      };

      $scope.saveDetails = function () {
        if ($scope.editType === "basicDetails") {
          console.log("Edditt data " + $scope.editData.name);

          const updatedBasic = {
            user_id: $scope.userDetails[0].id,
            name: $scope.editData.name,
            bio: $scope.editData.bio,
            hometown: $scope.editData.hometown,
          };
          $http
            .post(
              "http://localhost/codeigniter/index.php/edit/basic-details",
              updatedBasic
            )
            .then(function (response) {
              if (response.data.status === "success") {
                console.log(response.data.updatedData);

                $scope.profile.basicDetails = { ...$scope.editData };
                alert("Basic details updated successfully");
              } else {
                alert("Failed to update details: " + response.data.message);
              }
              $scope.closeDialog(); //close the dialog on success
            })
            .catch(function (error) {
              console.error("Error updating basic details:", error);
              alert("An error occurred. Please try again.");
            });
          // $scope.profile.basicDetails = { ...$scope.editData };
        } else if ($scope.editType === "contactDetails") {
          console.log("Contact detailsss " + $scope.editData.primaryPhone);
          console.log("Contact detailsss " + $scope.editData.primaryEmail);

          const updatedContactEmail = {
            pEmail: $scope.editData.primaryEmail,
            user_id: user_id,
          };

          const updatedContact = {
            user_id: user_id,
            primaryPhone: $scope.editData.primaryPhone,
            linkedinUrl: $scope.editData.linkedinUrl,
          };

          if (
            updatedContactEmail.pEmail !=
            $scope.profile.contactDetails.primaryEmail
          ) {
            //Here do call the api for email
            $http
              .post(
                "http://localhost/codeigniter/index.php/updateEmail",
                updatedContactEmail
              )
              .then(function (response) {
                if (response.data.status === "success") {
                  console.log("Updated email ", response.data);
                } else {
                  console.log("Not done email updation ", response.data);
                }
              })
              .catch(function (error) {
                console.log(error);
                alert(
                  "An error occurred while updating basic details. Please try again."
                );
              });
          }

          $http
            .post(
              "http://localhost/codeigniter/index.php/edit/contact-details",
              updatedContact
            )
            .then(function (response) {
              if (response.data.status === "success") {
                console.log(response.data.updatedData);

                $scope.profile.contactDetails = { ...$scope.editData };
                alert("Contact details updated successfully");
              } else {
                alert("Failed to update details: " + response.data.message);
              }
              $scope.closeDialog();
            })
            .catch(function (error) {
              console.error("Error updating contact details:", error);
              alert("An error occurred. Please try again.");
            });
        } else if ($scope.editType === "education") {
          // console.log("Edu " + $scope.editData.startYear);
          // console.log("years " + $scope.years);

          if (
            !$scope.editData.degree ||
            !$scope.editData.institution ||
            !$scope.editData.startYear ||
            (!$scope.editData.isCurrent && !$scope.editData.endYear)
          ) {
            alert(
              "Please fill in all mandatory fields: degree, institution, start year, and end year (or mark as currently pursuing)."
            );
            return;
          }

          const educationPayload = {
            user_id: user_id,
            college_school: $scope.editData.institution,
            degree_program: $scope.editData.degree,
            start_year: $scope.editData.startYear,
            end_year: $scope.editData.isCurrent
              ? null
              : $scope.editData.endYear,
            is_current: $scope.editData.isCurrent ? 1 : 0,
          };

          if ($scope.editData.id) {
            // Update existing education entry
            const index = $scope.profile.education.findIndex(
              (edu) => edu.id === $scope.editData.id
            );
            $scope.profile.education[index] = { ...$scope.editData };
          } else {
            // Add new education entry
            $scope.editData.id = $scope.educationIdCounter++;
            console.log($scope.editData);

            $scope.profile.education.push({ ...$scope.editData });
          }

          // Send to backend
          $http
            .post(
              "http://localhost/codeigniter/index.php/education",
              educationPayload
            )
            .then(function (response) {
              if (response.data.status === "success") {
                alert("Education details saved successfully.");
              } else {
                alert(
                  "Error saving education details: " + response.data.message
                );
              }
            })
            .catch(function (error) {
              console.error("Error saving education details:", error);
              alert(
                "An error occurred while saving education details. Please try again."
              );
            });
          $scope.closeDialog();
        } else if ($scope.editType === "work") {
          if (
            !$scope.editData.company ||
            !$scope.editData.position ||
            !$scope.editData.startYear ||
            (!$scope.editData.isCurrent && !$scope.editData.endYear)
          ) {
            alert(
              "Please fill in all mandatory fields: company, designation, start year, and end year (or mark as currently pursuing)."
            );
            return;
          }

          const workPayload = {
            user_id: user_id,
            company_organisation: $scope.editData.company,
            designation: $scope.editData.position,
            start_year: $scope.editData.startYear,
            end_year: $scope.editData.isCurrent
              ? null
              : $scope.editData.endYear,
            is_current: $scope.editData.isCurrent ? 1 : 0,
          };

          if ($scope.editData.id) {
            // Update existing work entry
            const index = $scope.profile.workHistory.findIndex(
              (work) => work.id === $scope.editData.id
            );
            $scope.profile.workHistory[index] = { ...$scope.editData };
          } else {
            // Add new work entry
            $scope.editData.id = $scope.workIdCounter++;
            console.log($scope.editData);

            $scope.profile.workHistory.push({ ...$scope.editData });
          }

          // Send to backend
          $http
            .post(
              "http://localhost/codeigniter/index.php/workHistory", // Update the endpoint as per your API
              workPayload
            )
            .then(function (response) {
              if (response.data.status === "success") {
                alert("Work details saved successfully.");
              } else {
                alert("Error saving work details: " + response.data.message);
              }
            })
            .catch(function (error) {
              console.error("Error saving work details:", error);
              alert(
                "An error occurred while saving work details. Please try again."
              );
            });

          // $scope.closeDialog();
          //////////////////////////////////////////////////////////////////////////
          // if ($scope.editData.id) {
          //   const index = $scope.profile.workHistory.findIndex(
          //     (work) => work.id === $scope.editData.id
          //   );

          //   $scope.profile.workHistory[index] = { ...$scope.editData };
          // } else {
          //   $scope.editData.id = $scope.workIdCounter++;
          //   $scope.profile.workHistory.push({ ...$scope.editData });
          // }
        }

        console.log("Updated Profile:", $scope.profile);
        // $scope.closeDialog();
      };

      $scope.deleteDetail = function () {
        if ($scope.editType === "education") {
          const educationToDelete = $scope.profile.education[$scope.editIndex];
          console.log(educationToDelete);
          console.log($scope.profile.education);

          const dataToSend = {
            user_id: user_id, // Assuming you have user_id in the education object
            degree_program: educationToDelete.degree, // Assuming degree_program is also present
          };

          console.log("I am deleting " + dataToSend);
          $http
            .post(
              "http://localhost/codeigniter/index.php/education/delete",
              dataToSend
            )
            .then(function (response) {
              if (response.data.status === "success") {
                alert(response.data.message);
              }
            })
            .catch(function (error) {
              console.error("Error while deleting education: ", error);
              alert("Failed to delete education details.");
            });

          $scope.profile.education = $scope.profile.education.filter(
            (_, index) => index !== $scope.editIndex
          );
        } else if ($scope.editType === "work") {
          const workToDelete = $scope.profile.workHistory[$scope.editIndex];
          console.log(workToDelete);
          console.log($scope.profile.workHistory);

          const dataToSend = {
            user_id: user_id, // Assuming you have user_id in the work object
            company_organisation: workToDelete.company, // Assuming company_organisation field is present
            designation: workToDelete.position, // Assuming designation field is present
          };

          console.log("I am deleting " + JSON.stringify(dataToSend));
          $http
            .post(
              "http://localhost/codeigniter/index.php/workHistory/delete",
              dataToSend
            )
            .then(function (response) {
              if (response.data.status === "success") {
                alert(response.data.message);
              }
            })
            .catch(function (error) {
              console.error("Error while deleting work: ", error);
              alert("Failed to delete work details.");
            });

          $scope.profile.workHistory = $scope.profile.workHistory.filter(
            (_, index) => index !== $scope.editIndex
          );
        }
        $scope.closeDialog();
      };

      $scope.addAlternateEmail = function () {
        console.log($scope.editData.nae);

        if ($scope.editData.nae.trim()) {
          // $scope.editData.alternateEmails.push($scope.newAlternateEmail);
          const alternateEmailData = {
            alternative_email: $scope.editData.nae,
            user_id: $scope.userDetails[0].id,
          };
          $http
            .post(
              "http://localhost/codeigniter/index.php/alternativeEmail",
              alternateEmailData
            )
            .then(function (response) {
              if (response.data.status === "success") {
                alert("Email added successfully");
                $scope.profile.contactDetails = { ...$scope.editData };
                console.log(
                  "Response from backend (alternate Email Details):",
                  response.data
                );
              } else {
                console.log(
                  "Response from backend (alternate Email Details):",
                  response.data
                );
              }
            })
            .catch(function (error) {
              console.error(
                "Error updating contact in alternative email details:",
                error
              );
              alert(
                "An error occurred while updating alternate email details. Please try again."
              );
            });

          const newObj = {
            user_id: user_id,
            alternate_email: $scope.editData.nae,
          };

          $scope.editData.alternateEmails.push(newObj);
          $scope.editData.nae = "";
        } else {
          console.warn("No valid email provided");
        }
      };

      $scope.removeAlternateEmail = function (index) {
        console.log(
          "I got deleted " +
            $scope.editData.alternateEmails[index].alternate_email
        );
        const alternateEmail =
          $scope.editData.alternateEmails[index].alternate_email;
        const requestData = {
          user_id: user_id,
          email: alternateEmail,
        };
        $http
          .post(
            "http://localhost/codeigniter/index.php/alternativeEmail/delete",
            requestData
          )
          .then(function (response) {
            console.log("Response from backend:", response.data);

            if (response.data.status === "success") {
              $scope.editData.alternateEmails.splice(index, 1);
              alert(response.data.message);
            } else {
              alert("Error deleting alternate phone: " + response.data.message);
            }
          })
          .catch(function (error) {
            console.error("Error deleting alternate phone:", error);
            if (error.data) {
              alert("Error: " + error.data.message);
            } else {
              alert(
                "An error occurred while deleting alternate phone. Please try again."
              );
            }
          });
      };

      $scope.addAlternatePhone = function () {
        if ($scope.editData.nap && $scope.editData.alternatePhones) {
          const alternatePhoneData = {
            alternative_phones: $scope.editData.nap,
            user_id: $scope.userDetails[0].id,
          };

          $http
            .post(
              "http://localhost/codeigniter/index.php/alternativePhone",
              alternatePhoneData
            )
            .then(function (response) {
              if (response.data.status === "success") {
                console.log("Inside contact " + $scope.editData);

                // $scope.profile.contactDetails = { ...$scope.editData };
                alert("Phone added successfully");
                console.log(
                  "Response from backend (alternate Phone Details):",
                  response.data
                );
              } else {
                console.log(
                  "Response from backend (alternate Phone Details):",
                  response.data
                );
              }
            })
            .catch(function (error) {
              console.error(
                "Error updating contact in alternative phone details:",
                error
              );
              alert(
                "An error occurred while updating alternate phone details. Please try again."
              );
            });

          const newObj = {
            user_id: user_id,
            alternate_phone: $scope.editData.nap,
          };

          $scope.editData.alternatePhones.push(newObj);
          console.log($scope.editData.alternatePhones);
          $scope.editData.nap = "";
        }
      };

      $scope.removeAlternatePhone = function (index) {
        console.log(
          "I got deleted " +
            $scope.editData.alternatePhones[index].alternate_phone
        );
        const alternatePhone =
          $scope.editData.alternatePhones[index].alternate_phone;
        const requestData = {
          user_id: user_id,
          phone: alternatePhone,
        };
        $http
          .post(
            "http://localhost/codeigniter/index.php/alternativePhone/delete",
            requestData
          )
          .then(function (response) {
            console.log("Response from backend:", response.data);

            if (response.data.status === "success") {
              // Remove the phone from the list in the frontend
              $scope.editData.alternatePhones.splice(index, 1);
              alert(response.data.message);
            } else {
              alert("Error deleting alternate phone: " + response.data.message);
            }
          })
          .catch(function (error) {
            console.error("Error deleting alternate phone:", error);
            if (error.data) {
              alert("Error: " + error.data.message);
            } else {
              alert(
                "An error occurred while deleting alternate phone. Please try again."
              );
            }
          });
      };

      $scope.sendRequest = function () {
        const requestData = {
          sender_id: user_id,
          receiver_id: urlUserId,
        };
        //for sending friend request
        $http
          .post(
            "http://localhost/codeigniter/index.php/send-friend-request",
            requestData
          )
          .then(function (response) {
            console.log("Response from backend:", response.data);
            // if (!$scope.admin)
            //   $scope.getButtonStatus();
            if (response.data.status === "success") {
              // Remove the phone from the list in the frontend
              alert(response.data.message);
            } else {
              alert(
                "Error while sending friend request: " + response.data.message
              );
            }
          })
          .catch(function (error) {
            console.error("Error while sending friend request:", error);
            if (error.data) {
              alert("Error: " + error.data.message);
            } else {
              alert("Error while sending friend request. Please try again.");
            }
          });
        // //for fetching request status
        // $http
        //   .post(
        //     "http://localhost/codeigniter/index.php/getFriendRequestStatus",
        //     requestData
        //   )
        //   .then(function (response) {
        //     if (response.data.status == "success") {
        //       if (response.data.data == "accepted") {
        //         $scope.requestStatus = "connected";
        //       } else {
        //         $scope.requestStatus = response.data.data;
        //       }
        //     }
        //   })
        //   .catch(function (error) {
        //     console.error(
        //       "Error while fetching  friend request status:",
        //       error
        //     );
        //     if (error.data) {
        //       alert("Error: " + error.data.message);
        //     } else {
        //       alert(
        //         "Error while fetching  friend request status. Please try again."
        //       );
        //     }
        //   });
      };

      $scope.openChat = function openChat() {
        
        // Validate IDs
        if (!user_id || !urlUserId) {
            alert("Invalid user information.");
            return;
        }
        
        $http
            .post(`http://localhost/codeigniter/index.php/create-chat/${user_id}/${urlUserId}`)
            .then(function (response) {
                if (response.data.status === "success") {
                    console.log("Chat created or already exists:", response.data);
                    // Navigate to the chat page
                    $location.path('/chat');
                } else {
                    console.error("Failed to create chat:", response.data.message);
                    alert("Could not create chat: " + response.data.message);
                }
            })
            .catch(function (error) {
                console.error("Error while creating chat:", error);
                alert("An error occurred while creating the chat.");
            });
      };
    


      $scope.navigationUserPost = function navigationUserPost() {
        const route = `/user-post/${urlUserId}`;
        $location.path(route);
      };

      $scope.openFileDialog = function openFileDialog() {
        const fileInput = document.getElementById("profilePhotoInput");
        console.log("I got triggered");
        if (fileInput) {
          console.log("file " + fileInput);
          fileInput.click();
        } else {
          console.error("File input element not found");
        }
      };

      // get work history
      $http
        .post(
          "http://localhost/codeigniter/index.php/workHistory/list",
          urlUserId
        )
        .then(function (response) {
          console.log(
            "Response from backend (work Details):",
            response.data.work
          );
          if (response.data.status === "success") {
            $scope.profile.workHistory = response.data.work.map(
              (work, index) => ({
                id: index + 1,
                company: work.company_organisation,
                position: work.designation,
                startYear: parseInt(work.start_year, 10),
                endYear: work.end_year ? parseInt(work.end_year, 10) : null,
                isCurrent: work.is_current === "1" || work.is_current === 1,
              })
            );
            $scope.workIdCounter = $scope.profile.workHistory.length + 1;
          } else {
            alert("Error getting work details: " + response.data.message);
          }
        })
        .catch(function (error) {
          console.error("Error fetching work details:", error);
          // Handle network errors or backend errors more explicitly
          if (error.data) {
            alert("Error: " + error.data.message);
          } else {
            alert(
              "An error occurred in work details in frontend. Please try again."
            );
          }
        });

      //get education details
      $http
        .post(
          "http://localhost/codeigniter/index.php/education/list",
          urlUserId
        )
        .then(function (response) {
          console.log(
            "Response from backend (education Details):",
            response.data.education
          );
          if (response.data.status === "success") {
            $scope.profile.education = response.data.education.map(
              (education, index) => ({
                id: index + 1,
                institution: education.college_school,
                degree: education.degree_program,
                startYear: parseInt(education.start_year, 10), // Parse startYear to integer
                endYear: education.end_year
                  ? parseInt(education.end_year, 10)
                  : null,
                isCurrent:
                  education.is_current === "1" || education.is_current === 1,
              })
            );
            $scope.educationIdCounter = $scope.profile.education.length + 1;
          } else {
            alert("Error getting education details: " + response.data.message);
          }
        })
        .catch(function (error) {
          console.error("Error ferching  education details:", error);
          if (error.data) {
            alert("Error: " + error.data.message);
          } else {
            alert(
              "An error occurred in education details in frontend. Please try again."
            );
          }
        });

      //get user contact details
      $http
        .post("http://localhost/codeigniter/index.php/contact/list", urlUserId)
        .then(function (response) {
          console.log(
            "Response from backend (contact Details):",
            response.data.contact
          );

          if (response.data.status === "success") {
            const contact = response.data.contact;

            if (Array.isArray(contact) && contact.length > 0) {
              $scope.profile.contactDetails.primaryPhone =
                contact[0].primary_phone || "N/A";
              $scope.profile.contactDetails.linkedinUrl =
                contact[0].linkedin_url || "Not provided";
            } else {
              $scope.profile.contactDetails.primaryPhone = "N/A";
              $scope.profile.contactDetails.linkedinUrl = "Not provided";
              console.log("Contact details array is empty.");
            }
          } else {
            alert("Error getting contact details: " + response.data.message);
          }
        })
        .catch(function (error) {
          console.error("Error fetching contact details:", error);
          if (error.data) {
            alert("Error: " + error.data.message);
          } else {
            alert(
              "An error occurred while fetching contact details. Please try again."
            );
          }
        });

      // get alternative emails
      $http
        .post(
          "http://localhost/codeigniter/index.php/alternativeEmail/list",
          urlUserId
        )
        .then(function (response) {
          console.log(
            "Response from backend (alternate emails Details):",
            response.data.emails
          );
          if (response.data.status === "success") {
            $scope.profile.contactDetails.alternateEmails =
              response.data.emails;
          } else {
            alert(
              "Error getting alternate emails details: " + response.data.message
            );
          }
        })
        .catch(function (error) {
          console.error("Error fetching alternate emails details:", error);
          if (error.data) {
            alert("Error: " + error.data.message);
          } else {
            alert(
              "An error occurred while fetching alternate emails details. Please try again."
            );
          }
        });
      // get alternative phones
      $http
        .post(
          "http://localhost/codeigniter/index.php/alternativePhone/list",
          urlUserId
        )
        .then(function (response) {
          console.log(
            "Response from backend (alternate phone Details):",
            response.data.phones
          );
          if (response.data.status === "success") {
            $scope.profile.contactDetails.alternatePhones =
              response.data.phones;
          } else {
            alert(
              "Error getting alternate phone details: " + response.data.message
            );
          }
        })
        .catch(function (error) {
          console.error("Error fetching alternate phone details:", error);
          if (error.data) {
            alert("Error: " + error.data.message);
          } else {
            alert(
              "An error occurred while fetching alternate phone details. Please try again."
            );
          }
        });

      // $scope.navigationUserPost = function () {
      //   const urlUserId = $routeParams.userId;
      //   const route = `user-post/${urlUserId}`;
      //   $location.path(route);
      // };
      $scope.uploadProfilePhoto = function uploadProfilePhoto(input) {
        if (input.files && input.files[0]) {
          const file = input.files[0];
          const allowedTypes = ["image/jpeg", "image/png", "image/jpg"];

          if (!allowedTypes.includes(file.type)) {
            alert("Invalid file type. Please upload a JPG or PNG image.");
            return;
          }

          const maxSize = 5 * 1024 * 1024;
          if (file.size > maxSize) {
            alert("File size exceeds 5MB. Please upload a smaller file.");
            return;
          }

          const formData = new FormData();
          formData.append("profile_photo", file);
          formData.append("user_id", user_id);

          fetch("http://localhost/codeigniter/index.php/profile/uploadPhoto", {
            method: "POST",
            body: formData,
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.status === "success") {
                const profilePhotoElm =
                  document.querySelector(".profile-photo");
                if (profilePhotoElm) {
                  profilePhotoElm.src = data.photo_url;
                }
                alert("Photo updated");
              } else {
                alert("Error updating profile photo: " + data.message);
              }
            })
            .catch((error) => {
              console.error("Error uploading profile photo:", error);
              alert(
                "An error occurred while uploading the profile photo. Please try again."
              );
            });
          // console.log("I got this file "+file.name);
        }
      };
    }
  );
