<?php
$this->load->view ( "mail/mail-cabecera" );
?><h1
	style="color: #333333; font-size: 28px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 20px">Cambio
	de tipo de Tarifa Plana</h1>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Su stock actual en Lovende es de <?=$total?>, debido a que sobrepasa el plan: <?=$tarifa_anterior?> de importe <?=$monto_anterior?> cuyo stock máximo es de: <?=$final_anterior?>, el mes que viene usted pasará al plan: <?=$tarifa_actual?> con un importe de <?=$monto_actual?></p>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Esperamos que disfrute comprando y vendiendo en Lovende.</p><?php
	$this->load->view ( "mail/mail-pie" );
	?>