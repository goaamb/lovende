<?php
final class Configuracion {
	private $variables;
	public $archivo;
	function __construct() {
		$variables = array (
				"maximoCantidad" => 3,
				"minimoMonto" => 0.5,
				"defaultHeaderTitle" => "Lovende",
				"defaultHeaderDescription" => "Lovende",
				"defaultHeaderKeywords" => "Lovende",
				"cantidadPaginacion" => 25,
				"tipoUbicacion" => "dominio",
				"vencimientoOferta" => 28,
				"imagenArticuloMinimoAncho" => 60,
				"imagenArticuloMinimoAlto" => 60,
				"cantidadVendidos" => 10,
				"securityUserid" => "xxx",
				"securityPassword" => "xxxx",
				"securitySignature" => "xxxx",
				"applicationId" => "xxxx",
				"urlBase" => "xxxx",
				"urlBaseSVCS" => "xxxx",
				"denuncia1a" => 10,
				"denuncia1b" => 10,
				"denuncia2a" => 10,
				"denuncia2b" => 10,
				"denuncia2c" => 10,
				"denuncia3a" => 10,
				"denuncia3b" => 10,
				"denuncia3c" => 10,
				"denuncia4a" => 20,
				"denuncia4b" => 10,
				"denuncia4c" => 10,
				"publicidad" => "No",
				"payAccount" => "xxx",
				"notificacionSeguimiento" => 24,
				"cantidadEmails" => 50,
				"emailFrom" => "support@lovende.com",
				"servicioPuerto" => "604438",
				"maestra" => "70V3ND3.35" 
		);
		$this->archivo = APPPATH . "xml/conf.xml";
		$this->leerConfiguracion ( $this->archivo );
	}
	public function leerConfiguracion($archivo) {
		if (is_file ( $archivo )) {
			$xml = simplexml_load_file ( $archivo );
			if ($xml) {
				$this->variables ["maximoCantidad"] = ( string ) $xml->variables->ofertas->maximoCantidad;
				$this->variables ["minimoMonto"] = ( string ) $xml->variables->subastas->minimoMonto;
				$this->variables ["defaultHeaderTitle"] = ( string ) $xml->variables->metatags->defaultHeaderTitle;
				$this->variables ["defaultHeaderDescription"] = ( string ) $xml->variables->metatags->defaultHeaderDescription;
				$this->variables ["defaultHeaderKeywords"] = ( string ) $xml->variables->metatags->defaultHeaderKeywords;
				$this->variables ["cantidadPaginacion"] = ( string ) $xml->variables->paginacion->cantidad;
				$this->variables ["tipoUbicacion"] = ( string ) $xml->variables->ubicacion->tipo;
				$this->variables ["vencimientoOferta"] = ( string ) $xml->variables->ofertas->vencimiento;
				$this->variables ["imagenArticuloMinimoAlto"] = ( string ) $xml->variables->imagen->articulo->minimaAlto;
				$this->variables ["imagenArticuloMinimoAncho"] = ( string ) $xml->variables->imagen->articulo->minimaAncho;
				$this->variables ["cantidadVendidos"] = ( string ) $xml->variables->paginacion->cantidadVendidos;
				$this->variables ["securityUserid"] = ( string ) $xml->variables->paypal->securityUserid;
				$this->variables ["securityPassword"] = ( string ) $xml->variables->paypal->securityPassword;
				$this->variables ["securitySignature"] = ( string ) $xml->variables->paypal->securitySignature;
				$this->variables ["payAccount"] = ( string ) $xml->variables->paypal->payAccount;
				$this->variables ["applicationId"] = ( string ) $xml->variables->paypal->applicationId;
				$this->variables ["urlBase"] = ( string ) $xml->variables->paypal->urlBase;
				$this->variables ["urlBaseSVCS"] = ( string ) $xml->variables->paypal->urlBaseSVCS;
				$this->variables ["denuncia1a"] = ( string ) $xml->variables->denuncias->d1a;
				$this->variables ["denuncia1b"] = ( string ) $xml->variables->denuncias->d1b;
				$this->variables ["denuncia2a"] = ( string ) $xml->variables->denuncias->d2a;
				$this->variables ["denuncia2b"] = ( string ) $xml->variables->denuncias->d2b;
				$this->variables ["denuncia2c"] = ( string ) $xml->variables->denuncias->d2c;
				$this->variables ["denuncia3a"] = ( string ) $xml->variables->denuncias->d3a;
				$this->variables ["denuncia3b"] = ( string ) $xml->variables->denuncias->d3b;
				$this->variables ["denuncia3c"] = ( string ) $xml->variables->denuncias->d3c;
				$this->variables ["denuncia4a"] = ( string ) $xml->variables->denuncias->d4a;
				$this->variables ["denuncia4b"] = ( string ) $xml->variables->denuncias->d4b;
				$this->variables ["denuncia4c"] = ( string ) $xml->variables->denuncias->d4c;
				$this->variables ["publicidad"] = ( string ) $xml->variables->publicidad->valor;
				$this->variables ["notificacionSeguimiento"] = ( string ) $xml->variables->notificacion->seguimiento;
				$this->variables ["cantidadEmails"] = ( string ) $xml->variables->masivo->cantidad;
				$this->variables ["emailFrom"] = ( string ) $xml->variables->emailFrom;
				$this->variables ["servicioPuerto"] = ( string ) $xml->variables->servicio;
				$this->variables ["maestra"] = ( string ) $xml->variables->maestra;
			}
		}
	}
	public function variables($variable) {
		if (isset ( $this->variables [$variable] )) {
			return $this->variables [$variable];
		}
		return false;
	}
}

?>