<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	/*
 * | ------------------------------------------------------------------------- |
 * URI ROUTING |
 * ------------------------------------------------------------------------- |
 * This file lets you re-map URI requests to specific controller functions. | |
 * Typically there is a one-to-one relationship between a URL string | and its
 * corresponding controller class/method. The segments in a | URL normally
 * follow this pattern: | |	example.com/class/method/id/ | | In some instances,
 * however, you may want to remap this relationship | so that a different
 * class/function is called than the one | corresponding to the URL. | | Please
 * see the user guide for complete details: |
 * |	http://codeigniter.com/user_guide/general/routing.html | |
 * ------------------------------------------------------------------------- |
 * RESERVED ROUTES |
 * ------------------------------------------------------------------------- | |
 * There area two reserved routes: | |	$route['default_controller'] = 'welcome';
 * | | This route indicates which controller class should be loaded if the | URI
 * contains no data. In the above example, the "welcome" class | would be
 * loaded. | |	$route['404_override'] = 'errors/page_missing'; | | This route
 * will tell the Router what URI segments to use if those provided | in the URL
 * cannot be matched to a valid route. |
 */
$route ['search/(:any)'] = "home/index/search/$1";
$route ['auction/(:any)'] = "home/index/auction/$1";
$route ['default_controller'] = "home";
$route ['fees'] = "home/estatica/fees";
$route ['terms'] = "home/estatica/terms";
$route ['benefit'] = "home/estatica/benefit";
$route ['privacy'] = "home/estatica/privacy";
$route ['logout'] = "usuario/logout";
$route ['login'] = "usuario/login";
$route ['login/(:any)'] = "usuario/login/$1";
$route ['register'] = "usuario/register";
$route ['register/(:any)'] = "usuario/register";
$route ['forgot'] = "usuario/forgot";
$route ['category'] = "home/category";
$route ['category/(:any)'] = "home/category/$1";
$route ['product/new'] = "articulo/nuevo";
$route ['product/nuevo'] = "articulo/nuevo";
$route ['product/new/(:any)'] = "articulo/nuevo/$1";
$route ['product/rechazarOferta/(:any)'] = "articulo/rechazarOferta/$1";
$route ['product/aceptarOferta/(:any)'] = "articulo/aceptarOferta/$1";
$route ['product/nuevo/(:any)'] = "articulo/nuevo/$1";
$route ['product/edit'] = "articulo/edit";
$route ['product/edit/(:any)'] = "articulo/edit/$1";
$route ['product/uploadImage'] = "articulo/uploadImage";
$route ['product/remove'] = "articulo/remove";
$route ['product/remove/(:any)'] = "articulo/remove/$1";
$route ['product/getCategory/(:any)'] = "articulo/getCategory/$1";
$route ['product/verificador'] = "articulo/verificarUploadProgress";
$route ['product/end/(:any)'] = "articulo/end/$1";
$route ['product/begin/(:any)'] = "articulo/begin/$1";
$route ['product/follow/(:any)'] = "articulo/follow/$1";
$route ['articulo/new'] = "articulo/nuevo";
$route ['product/(:any)'] = "articulo/item/$1";
$route ['perfil'] = "usuario/perfil";
$route ['profile'] = "usuario/perfil";
$route ['perfil/(:any)'] = "usuario/perfil/$1";
$route ['store/(:any)'] = "usuario/perfil/$1";
$route ['store/(:any)'] = "usuario/perfil/$1";
$route ['messageprofile/(:any)'] = "usuario/perfilMensaje/$1";
$route ['profile/(:any)'] = "usuario/perfil/$1";
$route ['edit'] = "usuario/editar";
$route ['edit/(:any)'] = "usuario/editar/$1";
$route ['administration/newsletter'] = "home/newsletter";
$route ['administration/newsletter/(:any)'] = "home/newsletter/$1";

$route ['administration/dashboard'] = "administrador/index/dashboard";
$route ['administration/pending'] = "administrador/index/pending";
$route ['administration/unmarked'] = "administrador/index/unmarked";
$route ['administration/billing'] = "administrador/index/billing";
$route ['administration/admipm'] = "administrador/index/admipm";

$route ['administration/curse'] = "administrador/index/curse";
$route ['administration/facpending'] = "administrador/index/facpending";
$route ['administration/paid'] = "administrador/index/paid";

$route ['404_override'] = 'home/index/404';


/* End of file routes.php */
/* Location: ./application/config/routes.php */