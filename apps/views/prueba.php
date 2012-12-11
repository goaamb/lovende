<div class="wrapper clearfix"><?php
$CI = &get_instance ();
$CI->load->model ( "Paypal_model", "paypal" );
if (! isset ( $_SESSION ["accessToken"] ) || ! isset ( $_SESSION ["tokenSecret"] )) {
	$token = $CI->paypal->requestPermissions ();
	
	if ($token) {
		?><a
		href='<?=$this->configuracion->variables ("urlBase") . "cgi-bin/webscr?cmd=_grant-permission&request_token=" . $token?>'>Dar
		Permisos</a><?php
	} else {
		print "ocurrio un error";
	}
} else {
	print $_SESSION ["accessToken"] . "<br/>";
	print $_SESSION ["tokenSecret"] . "<br/>";
}
?>
</div>