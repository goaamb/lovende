<?php
class Paypal_Model extends CI_Model {
	private $headers = array ();
	private $urlBase = "";
	private $urlBaseSVCS = "";
	public function __construct() {
		parent::__construct ();
		$signature = $this->configuracion->variables ( "securitySignature" );
		$this->headers = array (
				"Content-Type: text/namevalue",
				"X-PAYPAL-SECURITY-USERID: " . $this->configuracion->variables ( "securityUserid" ),
				"X-PAYPAL-SECURITY-PASSWORD: " . $this->configuracion->variables ( "securityPassword" ),
				"X-PAYPAL-SECURITY-SIGNATURE: " . $signature,
				"X-PAYPAL-APPLICATION-ID: " . $this->configuracion->variables ( "applicationId" ),
				"X-PAYPAL-REQUEST-DATA-FORMAT: NV",
				"X-PAYPAL-RESPONSE-DATA-FORMAT: NV" 
		);
		
		if (isset ( $_SESSION ["accessToken"] ) && isset ( $_SESSION ["tokenSecret"] )) {
			$accessToken = $_SESSION ["accessToken"];
			$timestamp = time ();
			$this->headers [] = "X-PAYPAL-AUTHORIZATION: timestamp=$timestamp,token=$accessToken,signature=$signature";
		}
		
		$this->urlBase = $this->configuracion->variables ( "urlBase" );
		$this->urlBaseSVCS = $this->configuracion->variables ( "urlBaseSVCS" );
	}
	public function requestGetAccessToken($token, $code) {
		if (! isset ( $_SESSION ["accessToken"] ) || ! isset ( $_SESSION ["tokenSecret"] )) {
			$response = $this->PPHttpPost ( $this->urlBaseSVCS . "Permissions/GetAccessToken", "requestEnvelope.errorLanguage=en_US&token=$token&verifier=$code", $this->headers );
			$p_response = $this->parseAPIResponse ( $response );
			if ($p_response && is_array ( $p_response ) && isset ( $p_response ["token"] ) && isset ( $p_response ["tokenSecret"] )) {
				$token = $p_response ["token"];
				$tokenSecret = $p_response ["tokenSecret"];
				$_SESSION ["accessToken"] = $token;
				$_SESSION ["tokenSecret"] = $tokenSecret;
				
				return array (
						'token' => $token,
						'tokenSecret' => $tokenSecret 
				);
			}
			return false;
		} else {
			return array (
					'token' => $_SESSION ["accessToken"],
					'tokenSecret' => $_SESSION ["tokenSecret"] 
			);
		}
	}
	public function requestPermissions() {
		if (! isset ( $_SESSION ["tiempoToken"] ) && isset ( $_SESSION ["token"] )) {
			unset ( $_SESSION ["token"] );
		}
		if (isset ( $_SESSION ["tiempoToken"] )) {
			$tiempo = time () - intval ( $_SESSION ["tiempoToken"] );
			$limite = 14 * 60; // 14minutos
			if ($tiempo > $limite) {
				unset ( $_SESSION ["token"] );
			}
		}
		if (! isset ( $_SESSION ["token"] )) {
			$response = $this->PPHttpPost ( $this->urlBaseSVCS . "Permissions/RequestPermissions", "requestEnvelope.errorLanguage=en_US&scope=AUTH_CAPTURE&callback=" . base_url () . "paypal/permisionKey", $this->headers );
			$p_response = $this->parseAPIResponse ( $response );
			if ($p_response && is_array ( $p_response )) {
				if (isset ( $p_response ["token"] )) {
					$_SESSION ["token"] = $p_response ["token"];
					$_SESSION ["tiempoToken"] = time ();
					return $p_response ["token"];
				} elseif (isset ( $p_response ["responseEnvelope.ack"] ) && $p_response ["responseEnvelope.ack"] == "Failure") {
					?><div style="display: none;"><?php var_dump($p_response)?></div><?php
					return false;
				}
			}
			return false;
		} else {
			return $_SESSION ["token"];
		}
	}
	public function actualizarPaquete($paquete, $tipo = 4) {
		$res = $this->db->query ( "select * from paquete where (estado='Sin Pago' or estado='Disputa' ) and id='$paquete'" );
		if ($res && ($r = $res->result ()) && is_array ( $r ) && count ( $r ) > 0) {
			if ($this->db->update ( "paquete", array (
					"estado" => "Sin Envio",
					"fecha_pago" => date ( "Y-m-d H:i:s" ),
					"tipo_pago" => $tipo,
					"denuncia2" => 0,
					"fecha_disputa1" => null 
			), array (
					"id" => $paquete 
			) )) {
				$this->db->select ( "articulos,transacciones" );
				$this->db->where ( array (
						"id" => $paquete 
				) );
				$res = $this->db->get ( "paquete" );
				if ($res && ($r = $res->result ()) && is_array ( $r ) && count ( $r ) > 0) {
					$articulos = explode ( ",", $r [0]->articulos );
					foreach ( $articulos as $a ) {
						$this->db->update ( "articulo", array (
								"estado" => "Sin Envio" 
						), array (
								"id" => $a 
						) );
					}
					$transacciones = explode ( ",", $r [0]->transacciones );
					foreach ( $transacciones as $t ) {
						$this->db->update ( "transaccion", array (
								"estado" => "Sin Envio" 
						), array (
								"id" => $t 
						) );
					}
					$this->db->update ( "reporte", array (
							"estado" => "Finalizado" 
					), array (
							"paquete" => $paquete,
							"asunto" => "No Pagado" 
					) );
				}
			}
		}
	}
	public function getPayKey($paquete, $total) {
		if ($paquete && $total) {
			$this->db->select ( "paykey" );
			$this->db->where ( array (
					"paquete" => $paquete 
			) );
			$this->db->order_by ( "fecha desc" );
			$res = $this->db->get ( "paquete_paypal", 1, 0 );
			if ($res && ($r = $res->result ()) && is_array ( $r ) && count ( $r ) > 0) {
				$pk = $r [0]->paykey;
				$v = $this->verifyPayKey ( $pk );
				switch ($v) {
					case 1 :
						$this->actualizarPaquete ( $paquete );
						return true;
						break;
					case 2 :
						return $r [0]->paykey;
						break;
				}
			}
			$this->db->select ( "usuario.paypal as email" );
			$this->db->join ( "paquete", "paquete.vendedor=usuario.id and paquete.id='$paquete'" );
			$ru = $this->db->get ( "usuario" )->result ();
			$this->db->select ( "usuario.paypal as email" );
			$this->db->join ( "paquete", "paquete.comprador=usuario.id and paquete.id='$paquete'" );
			$rc = $this->db->get ( "usuario" )->result ();
			if ($ru && is_array ( $ru ) && count ( $ru ) > 0) {
				if (trim ( $rc [0]->email ) == "") {
					return - 1;
				}
				if (trim ( $ru [0]->email ) == "") {
					return - 2;
				}
				$endpoint = $this->urlBaseSVCS . "AdaptivePayments/Pay";
				$api_str = $this->requestPayKey ( $paquete, $ru [0]->email, "CASHADVANCE", $total );
				$response = $this->PPHttpPost ( $endpoint, $api_str, $this->headers );
				$p_response = $this->parseAPIResponse ( $response );
				if ($p_response && is_array ( $p_response )) {
					if (isset ( $p_response ["payKey"] )) {
						if ($this->db->insert ( "paquete_paypal", array (
								"paquete" => $paquete,
								"paykey" => $p_response ["payKey"],
								"fecha" => date ( "Y-m-d H:i:s" ) 
						) )) {
							return $p_response ["payKey"];
						}
					} elseif (isset ( $p_response ["responseEnvelope.ack"] ) && $p_response ["responseEnvelope.ack"] == "Failure") {
						?><div style="display: none;"><?php var_dump($p_response)?></div><?php
						return - 3;
					}
				}
			}
		}
		return false;
	}
	public function getPayKeyBilling($factura) {
		if ($factura) {
			$this->db->select ( "paypal_id,monto_tarifa,iva" );
			$this->db->where ( array (
					"id" => $factura 
			) );
			$r = $this->db->get ( "factura" )->result ();
			$monto = 0;
			if ($r && is_array ( $r ) && count ( $r ) > 0) {
				$monto = floatval ( $r [0]->monto_tarifa + $r [0]->iva );
				$pk = $r [0]->paypal_id;
				$v = $this->verifyPayKey ( $pk );
				switch ($v) {
					case 1 :
						// $this->actualizarFactura ( $paquete );
						return true;
						break;
					case 2 :
						return $r [0]->paypal_id;
						break;
				}
			}
			if ($monto <= 0) {
				return false;
			}
			$email = $this->configuracion->variables ( "payAccount" );
			$endpoint = $this->urlBaseSVCS . "AdaptivePayments/Pay";
			$api_str = $this->requestPayKeyBilling ( $factura, $email, "CASHADVANCE", $monto );
			$response = $this->PPHttpPost ( $endpoint, $api_str, $this->headers );
			$p_response = $this->parseAPIResponse ( $response );
			if ($p_response && is_array ( $p_response ) && isset ( $p_response ["payKey"] )) {
				if ($this->db->update ( "factura", array (
						"paypal_id" => $p_response ["payKey"],
						"estado" => "Pendiente" 
				), array (
						"id" => $factura 
				) )) {
					return $p_response ["payKey"];
				}
			}
		}
		return false;
	}
	public function verifyPayKey($pk) {
		$api_str = "payKey=$pk&requestEnvelope.errorLanguage=en_US";
		$response = $this->paypal->PPHttpPost ( "{$this->urlBaseSVCS}AdaptivePayments/PaymentDetails", $api_str, $this->headers );
		if ($response) {
			$response = $this->parseAPIResponse ( $response );
			if ($response && is_array ( $response ) && isset ( $response ["status"] )) {
				if ($response ["status"] == "COMPLETED") {
					return 1; // completed key
				} else if ($response ["status"] == "CREATED" || $response ["status"] == "PROCESSING" || $response ["status"] == "PENDING") {
					return 2; // pending key
				}
			}
		}
		return 0; // invalid key
	}
	function parseAPIResponse($response) {
		$response = explode ( "&", $response );
		$parsed_response = array ();
		foreach ( $response as $i => $value ) {
			$tmpAr = explode ( "=", $value );
			if (sizeof ( $tmpAr ) > 1) {
				$parsed_response [$tmpAr [0]] = $tmpAr [1];
			}
		}
		return $parsed_response;
	}
	function requestPayKey($codigo, $email, $type, $total) {
		//$reqstr = "actionType=PAY&cancelUrl=" . base_url () . "paypal/cancel&currencyCode=EUR&returnUrl=" . base_url () . "paypal/success?codigo=$codigo&ipnNotificationUrl=" . base_url () . "paypal/ipnResponse&requestEnvelope.errorLanguage=en_US&receiverList.receiver(0).amount=$total&receiverList.receiver(0).email=$email&receiverList.receiver(0).paymentType=$type&receiverList.receiver(0).invoiceId=" . rand ();
		$reqstr = "actionType=PAY&cancelUrl=" . base_url () . "paypal/cancel&currencyCode=EUR&returnUrl=" . base_url () . "paypal/success?codigo=$codigo&ipnNotificationUrl=" . base_url () . "paypal/ipnResponse&requestEnvelope.errorLanguage=en_US&receiverList.receiver(0).amount=$total&receiverList.receiver(0).email=$email";
		return $reqstr;
	}
	function requestPayKeyBilling($codigo, $email, $type, $total) {
		$reqstr = "actionType=PAY&cancelUrl=" . base_url () . "paypal/cancel&currencyCode=EUR&returnUrl=" . base_url () . "paypal/successBillling?codigo=$codigo&ipnNotificationUrl=" . base_url () . "paypal/ipnResponseBillling&requestEnvelope.errorLanguage=en_US&receiverList.receiver(0).amount=$total&receiverList.receiver(0).email=$email&receiverList.receiver(0).paymentType=$type&receiverList.receiver(0).invoiceId=" . rand ();
		return $reqstr;
	}
	function PPHttpPost($my_endpoint, $my_api_str, $headers) {
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $my_endpoint );
		curl_setopt ( $ch, CURLOPT_VERBOSE, 1 );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $my_api_str );
		$httpResponse = curl_exec ( $ch );
		if (! $httpResponse) {
			$response = "$my_endpoint failed: " . curl_error ( $ch ) . "(" . curl_errno ( $ch ) . ")";
			return $response;
		}
		
		return $httpResponse;
	}
	function formLight($key, $tipo = "apdg") {
		$urlPay = "{$this->urlBase}webapps/adaptivepayment/flow/pay";
		?><form action='<?=$urlPay?>' target='PPDGFrame' id="formPaypalPago"
	onsubmit="setTimeout(function(){('.nyroModalClose').click();},5000)">
	<p class="actions">
		<input id='type' type='hidden' name='expType' value='mini' /> <input
			id='paykey' type='hidden' name='paykey' value='<?=$key?>' /> <input
			type='submit' id='submitBtn' class="bt"
			value="<?=traducir("Pagar ahora por Paypal")?>" /> <span class="mhm">o</span>
		<a class="nyroModalClose">cancelar</a>
	</p>
</form>
<script>
		G.util.includeJS('https://www.paypalobjects.com/js/external/<?=$tipo?>.js');
		var dgFlow=false;
		function triggerPAYPAL(){
			try{
				dgFlow = new PAYPAL.apps.<?=$tipo=="apdg"?"DGFlowMini":"DGFlow"?>({
			        trigger: 'submitBtn'
			    });
		    }catch(e){
		    	setTimeout(triggerPAYPAL,1000);
			}
		}
		triggerPAYPAL();
	    	
	</script><?php
	}
	function formLightBilling($key, $tipo = "apdg") {
		$urlPay = "{$this->urlBase}webapps/adaptivepayment/flow/pay";
		?><form action='<?=$urlPay?>' target='PPDGFrame' id="formPaypalPago">
	<input id='type' type='hidden' name='expType' value='mini' /> <input
		id='paykey' type='hidden' name='paykey' value='<?=$key?>' /> <input
		type='submit' id='submitBtn' class="bt" value="Marcar como pagado" />
</form>
<script>
			G.util.includeJS('https://www.paypalobjects.com/js/external/<?=$tipo?>.js');
			var dgFlow=false;
			function triggerPAYPAL(){
				try{
					dgFlow = new PAYPAL.apps.<?=$tipo=="apdg"?"DGFlowMini":"DGFlow"?>({
				        trigger: 'submitBtn'
				    });
			    }catch(e){
			    	setTimeout(triggerPAYPAL,1000);
				}
			}
			triggerPAYPAL();
		    	
		</script><?php
	}
}

?>