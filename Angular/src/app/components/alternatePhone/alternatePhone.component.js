"use strict";


angular.module("myApp").controller("alternatePhone", function($scope, $location) {
    $scope.alternatePhone = "";

    $scope.onSubmit = function (alternatePhoneForm) {
        if (alternatePhoneForm.$valid) {
            console.log("Alternate phone Submitted:", $scope.alternatePhone);

            // Reset the form after successful submission
            $scope.alternatePhone = "";
            alternatePhoneForm.$setPristine();
            alternatePhoneForm.$setUntouched();
        } else {
            console.log("Form is invalid.");
        }
    };
});
