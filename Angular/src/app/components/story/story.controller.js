"use strict";
angular.module("storyBar").controller("StoryBarController", function () {
  this.stories = [
    {
      avatar: "https://via.placeholder.com/50",
      username: "John Doe",
      viewed: false,
      created: "2025-01-01T12:00:00Z",
    },
    {
      avatar: "https://via.placeholder.com/50",
      username: "Jane Smith",
      viewed: true,
      created: "2025-01-02T15:00:00Z",
    },
    {
      avatar: "https://via.placeholder.com/50",
      username: "Alice Johnson",
      viewed: false,
      created: "2025-01-03T18:00:00Z",
    },
    {
      avatar: "https://via.placeholder.com/50",
      username: "Michael Brown",
      viewed: true,
      created: "2025-01-04T09:00:00Z",
    },
    {
      avatar: "https://via.placeholder.com/50",
      username: "Emily Davis",
      viewed: false,
      created: "2025-01-04T14:30:00Z",
    },
    {
      avatar: "https://via.placeholder.com/50",
      username: "Chris Wilson",
      viewed: true,
      created: "2025-01-05T11:00:00Z",
    },
    {
      avatar: "https://via.placeholder.com/50",
      username: "Olivia Martinez",
      viewed: false,
      created: "2025-01-05T16:00:00Z",
    },
    {
      avatar: "https://via.placeholder.com/50",
      username: "Daniel Anderson",
      viewed: true,
      created: "2025-01-06T08:00:00Z",
    },
    {
      avatar: "https://via.placeholder.com/50",
      username: "Sophia Garcia",
      viewed: false,
      created: "2025-01-06T13:15:00Z",
    },
    {
      avatar: "https://via.placeholder.com/50",
      username: "William Hernandez",
      viewed: true,
      created: "2025-01-06T17:45:00Z",
    },
    {
      avatar: "https://via.placeholder.com/50",
      username: "Ava Thompson",
      viewed: false,
      created: "2025-01-07T10:30:00Z",
    },
    {
      avatar: "https://via.placeholder.com/50",
      username: "James Lee",
      viewed: true,
      created: "2025-01-07T14:00:00Z",
    },
  ];

  this.addStory = function () {
    console.log("Add Story functionality triggered");
    // Add logic to upload a story
  };
});
