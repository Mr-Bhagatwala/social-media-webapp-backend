angular.module("myApp").service("TagService", function () {
    let taggedUsers = [];
  
    return {
      getTaggedUsers: function () {
        return taggedUsers;
      },
      addTaggedUser: function (user) {
        const exists = taggedUsers.some((u) => u.id === user.id);
        if (!exists) {
          taggedUsers.push(user);
        }
      },
      removeTaggedUser: function (id) {
        taggedUsers = taggedUsers.filter((user) => user.id !== id);
      }
    };
  });
  