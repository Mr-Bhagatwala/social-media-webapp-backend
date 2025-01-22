"use strict";


angular.module("myApp").controller("alternateEmail", function($scope, $location) {
    $scope.alternateEmail = "";

    $scope.onSubmit = function (alternateEmailForm) {
        if (alternateEmailForm.$valid) {
            console.log("Alternate Email Submitted:", $scope.alternateEmail);

            // Reset the form after successful submission
            $scope.alternateEmail = "";
            alternateEmailForm.$setPristine();
            alternateEmailForm.$setUntouched();
        } else {
            console.log("Form is invalid.");
        }
    };
});
