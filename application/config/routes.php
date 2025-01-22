<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "welcome";
$route['session'] = "AuthController/check_session";


$route['register'] = 'AuthController/register_user';
$route['login'] = "AuthController/login_user";
$route['logout'] = "AuthController/logout";
$route['details'] = "AuthController/addProfileDetails";
$route['getUser'] = "AuthController/getUser";
$route['check1'] = "AuthController/checkUser";
$route['getAll'] = "AuthController/fetchUsers";
$route['profile/uploadPhoto'] = "AuthController/uploadPhoto";

$route['updateEmail'] = "AuthController/updateEmail";
$route['search/users/(:any)'] = "AuthController/searchUsers/$1";
$route['edit/basic-details'] = "AuthController/editBasicDetails";

$route['contact'] = "ContactController/addContactDetails";
$route['contact/list'] = "ContactController/listContactDetails";
$route['cont'] = "ContactController/checkContact";
$route['edit/contact-details'] = "ContactController/editContactDetails";


$route['alternativeEmail'] = "AlternativeEmailsController/addAlternativeEmail";
$route['alternativeEmail/delete'] = "AlternativeEmailsController/removeAlternativeEmail";
$route['alternativeEmail/list'] = "AlternativeEmailsController/listAlternativeEmails";

$route['abc/(:num)/(:any)'] = "contorl/func/$1/$2";

$route['alternativePhone'] = "AlternativePhonesController/addAlternativePhone";
$route['alternativePhone/delete'] = "AlternativePhonesController/removeAlternativePhone";
$route['alternativePhone/list'] = "AlternativePhonesController/listAlternativePhones";

$route['education'] = "EducationController/addEducationDetails";
$route['education/list'] = "EducationController/listEducationHistory";
$route['education/delete'] = "EducationController/removeEducationDetails";// Delete education details


$route['workHistory'] = "WorkController/addWorkDetails"; // Add work details
$route['workHistory/list'] = "WorkController/listWorkHistory"; // Add work details
$route['workHistory/update/(:num)'] = "WorkController/updateWorkDetails/$1"; // Update work details
$route['workHistory/delete'] = "WorkController/removeWorkDetails"; // Delete work details
$route['send-friend-request'] = 'FriendRequestController/sendRequest';
$route['get-requests/(:num)'] = 'FriendRequestController/getRequests/$1';
$route['getFriendRequestStatus'] = 'FriendRequestController/getFriendRequestStatus';
// $route['respond-friend-request'] = 'FriendRequestController/respondRequest';
$route['rfr'] = 'FriendRequestController/respondRequest';
$route['get-friends/(:num)'] = 'FriendRequestController/getFriends/$1';

// Stories
$route['upload-story'] = 'StoriesController/uploadStory';
$route['get-stories/(:num)'] = 'StoriesController/getStoriesofUser/$1';
$route['get-my-story/(:num)'] = 'StoriesController/getMyStory/$1';
$route['mark-story-viewed/(:num)'] = 'StoriesController/markStoryAsViewed/$1';
$route['react-to-story/ (:num)'] = 'StoriesController/reactToStory/$1';
$route['getFriendsStories/(:num)'] = 'StoriesController/getFriendsStories/$1';
$route['delete-expired-stories'] = 'StoriesController/deleteExpiredStories';
$route['is-viewed-by-user/(:num)'] = 'StoriesController/isViewedByUser/$1';
$route['get-story-view/(:num)'] = 'StoriesController/getStoryView/$1';
$route['like/(:num)'] = 'StoriesController/like/$1';
$route['get-story-likes/(:num)'] = 'StoriesController/getLikes/$1';
$route['is-liked/(:num)'] = 'StoriesController/isLiked/$1';


// Posts-related routes
$route['posts/create'] = 'PostController/createPost';  // POST: Create a new post
$route['posts/delete/(:num)'] = 'PostController/deletePost/$1';  // POST: Delete a post by post ID
$route['posts/feed'] = 'PostController/getFeed';  // GET: Get paginated feed
$route['posts/like/(:num)'] = 'PostController/likePost/$1';  // POST: Like a post by post ID
$route['posts/comment/(:num)'] = 'PostController/addComment/$1';  // POST: Add a comment to a post by post ID
$route['posts/getcomments/(:num)'] = 'PostController/getComments/$1'; // Get all comments of a post by postIdś
$route['posts/post-by-user'] = 'PostController/getUserPost';
$route['posts/toggle_like']='PostController/toggle_like' ;

$route['get-notifications/(:num)'] = 'NotificationController/getNotificationofUser/$1';

//chats
$route['create-chat/(:num)/(:num)'] = 'ChatController/createChat/$1/$2';
$route['get-all-chats/(:num)'] = 'ChatController/getAllChats/$1';
$route['mute-chat/(:num)/(:num)'] = 'ChatController/muteChat/$1/$2';
$route['unmute-chat/(:num)/(:num)'] = 'ChatController/unmuteChat/$1/$2';
$route['block-user/(:num)/(:num)'] = 'ChatController/blockUser/$1/$2';
$route['unblock-user/(:num)/(:num)'] = 'ChatController/unblockUser/$1/$2';
$route['clear-chat/(:num)'] = 'ChatController/clearChat/$1';
$route['delete-chat/(:num)'] = 'ChatController/deleteChat/$1';
$route['pin-chat/(:num)'] = 'ChatController/pinChat/$1';
$route['unpin-chat/(:num)'] = 'ChatController/unpinChat/$1';

//messsages
$route['message/past'] = 'MessageController/getPastMessages';
$route['message/send'] = 'MessageController/sendMessage';
$route['message/sendFile'] = 'MessageController/sendFile';
$route['message/delete'] = 'MessageController/deleteMessage';
$route['message/reply'] = 'MessageController/replyToMessage';
$route['message/fetch'] = 'MessageController/fetchMessage';

$route['404_override'] = '';

/* End of file routes.php */
/* Location: ./application/config/routes.php */
