'use strict';

angular.module('dialogBox').component('dialogBox', {
  bindings: {
    dialogTitle: '<',
    editType: '<',
    editData: '=',
    onSave: '&',
    onClose: '&',
    onDelete: '&'
  },
  templateUrl: './app/components/dialogBox/dialog.component.html',
  controller: function () {
    this.$onInit = function () {
      console.log("Dialog title is"+ dialogTitle);
    };

    this.addAlternateEmail = function () {
      if (this.editData.nae && this.editData.nae.trim()) {
        this.editData.alternateEmails.push(this.editData.nae);
        this.editData.nae = "";
      } else {
        console.warn('Invalid email');
      }
    };

    this.removeAlternateEmail = function (index) {
      this.editData.alternateEmails.splice(index, 1);
    };

    this.addAlternatePhone = function () {
      if (this.editData.nap && this.editData.nap.trim()) {
        this.editData.alternatePhones.push(this.editData.nap);
        this.editData.nap = "";
      } else {
        console.warn('Invalid phone number');
      }
    };

    this.removeAlternatePhone = function (index) {
      this.editData.alternatePhones.splice(index, 1);
    };
  }
});
