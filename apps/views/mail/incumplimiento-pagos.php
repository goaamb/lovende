<?php
$this->load->view ( "mail/mail-cabecera" );
?>
<h1
	style="color: #333333; font-size: 28px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 20px">
	Tu cuenta ha sido restringida</h1>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Debido al incumplimiento de los <a href="<?php echo base_url()."terms"?>" title="Términos de uso">Términos de uso</a> hemos restringido indefinidamente tu cuenta en Lovende.
</p>
<p>
	Si crees que esta decisión es errónea envía un mail a <a href="mailto:<?=$emailfrom?>" 
	title="Support Lovende"><?=$emailfrom?></a>.
</p>

<?php
$this->load->view ( "mail/mail-pie" );
?>