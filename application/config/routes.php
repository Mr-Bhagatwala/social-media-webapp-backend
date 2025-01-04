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
$route['register'] = 'AuthController/register_user';
$route['login'] = "AuthController/login_user";
$route['logout'] = "AuthController/logout";
$route['details'] = "AuthController/addProfileDetails";
$route['contact'] = "ContactController/addContactDetails";

$route['alternativeEmail'] = "AlternativeEmailsController/addAlternativeEmail";
$route['alternativeEmail/delete/(:num)'] = "AlternativeEmailsController/removeAlternativeEmail/$1";
$route['alternativeEmail/list'] = "AlternativeEmailsController/listAlternativeEmails";

$route['alternativePhone'] = "AlternativePhonesController/addAlternativePhone";
$route['alternativePhone/delete/(:num)'] = "AlternativePhonesController/removeAlternativePhone/$1";
$route['alternativePhone/list'] = "AlternativePhonesController/listAlternativePhones";

$route['education'] = "EducationController/addEducationDetails";
$route['education/update/(:num)'] = "EducationController/updateEducationDetails/$1"; // Update education details
$route['education/delete/(:num)'] = "EducationController/removeEducationDetails/$1";// Delete education details


$route['workHistory'] = "WorkController/addWorkDetails"; // Add work details
$route['workHistory/update/(:num)'] = "WorkController/updateWorkDetails/$1"; // Update work details
$route['workHistory/delete/(:num)'] = "WorkController/removeWorkDetails/$1"; // Delete work details



$route['404_override'] = '';



/* End of file routes.php */
/* Location: ./application/config/routes.php */