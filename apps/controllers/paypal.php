<?php
require_once 'basecontroller.php';
class Paypal extends BaseController {
	public function __construct() {
		parent::__construct ();
		$this->load->model ( "Paypal_model", "paypal" );
	}
	public function permisionKey() {
		$token = $this->input->get ( "request_token" );
		$vcode = $this->input->get ( "verification_code" );
		
		$access = $this->paypal->requestGetAccessToken ( $token, $vcode );
		if (isset ( $_SESSION ["token"] )) {
			unset ( $_SESSION ["token"] );
		}
		if ($access) {
			redirect ( "" );
		} else {
			redirect ( "home/estatica/prueba" );
		}
	}
	public function cancel() {
		?><script type="text/javascript">
				if(window.opener && window.opener.location){
					window.opener.location.href=window.opener.location.href.split("#").shift();
				}
				top.close();
				</script><?php
	}
	public function billingPay($factura) {
		$dir = pathinfo ( __FILE__, PATHINFO_DIRNAME ) . "/paypal-ipn.txt";
		$texto = @file_get_contents ( $dir );
		if (! $texto) {
			$texto = "";
		}
		ob_start ();
		print_r ( $_POST );
		print_r ( $_GET );
		print_r ( $_REQUEST );
		$x = ob_get_clean ();
		$texto = "[" . date ( "Y-m-d H:i:s" ) . "]\n" . $x . $texto;
		$status = strtoupper ( $this->input->post ( "payment_status" ) );
		if ($status == "COMPLETED") {
			$texto = "[" . date ( "Y-m-d H:i:s" ) . "] - COMPLETO\n" . $texto;
			$pk = $this->input->post ( "txn_id" );
			$this->db->select ( "id" );
			$this->db->where ( array (
					"paypal_id" => $pk 
			) );
			$res = $this->db->get ( "factura" );
			if ($res && ($r = $res->result ()) && is_array ( $r ) && count ( $r ) > 0) {
				$this->db->update ( "factura", array (
						"estado" => "Pagado" 
				), array (
						"id" => $r [0]->id 
				) );
			} else {
				$pk2 = $this->input->post ( "item_number" );
				$this->db->select ( "id" );
				$this->db->where ( array (
						"codigo" => $pk2 
				) );
				$res = $this->db->get ( "factura" );
				if ($res && ($r = $res->result ()) && is_array ( $r ) && count ( $r ) > 0) {
					$this->db->update ( "factura", array (
							"estado" => "Pagado",
							"paypal_id" => $pk 
					), array (
							"id" => $r [0]->id 
					) );
				}
			}
		} elseif ($status == "PENDING") {
			$texto = "[" . date ( "Y-m-d H:i:s" ) . "] - PENDIENTE\n" . $texto;
			$pk = $this->input->post ( "txn_id" );
			$this->db->select ( "paypal_id" );
			$this->db->where ( array (
					"id" => $factura,
					"estado" => "Pendiente" 
			) );
			$r = $this->db->get ( "factura" )->result ();
			if ($r && is_array ( $r ) && count ( $r ) > 0) {
				$this->db->update ( "factura", array (
						"estado" => "En curso",
						"paypal_id" => $pk 
				), array (
						"id" => $factura 
				) );
			}
		} elseif ($status == "ERROR" || $status == "DENIED") {
			$texto = "[" . date ( "Y-m-d H:i:s" ) . "] - $status\n" . $texto;
			$pk = $this->input->post ( "txn_id" );
			$this->db->select ( "id" );
			$this->db->where ( array (
					"paypal_id" => $pk 
			) );
			$res = $this->db->get ( "factura" );
			if ($res && ($r = $res->result ()) && is_array ( $r ) && count ( $r ) > 0) {
				$this->db->update ( "factura", array (
						"estado" => "Pendiente",
						"paypal_id" => null 
				), array (
						"id" => $r [0]->id 
				) );
			}
		} else {
			$texto = "[" . date ( "Y-m-d H:i:s" ) . "] - $status\n" . $texto;
		}
		file_put_contents ( $dir, $texto );
	}
	public function success() {
		$codigo = $this->input->get ( "codigo" );
		if ($codigo) {
			$this->db->select ( "paykey" );
			$this->db->where ( array (
					"paquete" => $codigo 
			) );
			$this->db->order_by ( "fecha desc" );
			$res = $this->db->get ( "paquete_paypal", 1, 0 );
			if ($res && ($r = $res->result ()) && is_array ( $r ) && count ( $r ) > 0) {
				$pk = $r [0]->paykey;
				$v = $this->paypal->verifyPayKey ( $pk );
				switch ($v) {
					case 1 :
						$this->paypal->actualizarPaquete ( $codigo );
						break;
				}
			}
		}
		?><script type="text/javascript">
		if(window.opener && window.opener.location){
			window.opener.location.href=window.opener.location.href.split("#").shift();
		}
		top.close();
		</script><?php
	}
	public function successBillling() {
		$codigo = $this->input->get ( "codigo" );
		if ($codigo) {
			$this->db->select ( "paypal_id" );
			$this->db->where ( array (
					"id" => $codigo,
					"estado" => "Pendiente" 
			) );
			$r = $this->db->get ( "factura" )->result ();
			if ($r && is_array ( $r ) && count ( $r ) > 0) {
				$pk = $r [0]->paypal_id;
				$v = $this->paypal->verifyPayKey ( $pk );
				switch ($v) {
					case 1 :
						$this->db->update ( "factura", array (
								"estado" => "En Curso" 
						), array (
								"id" => $codigo 
						) );
						break;
				}
			}
		}
		?><script type="text/javascript">
			if(window.opener && window.opener.location){
				window.opener.location.href=window.opener.location.href.split("#").shift();
			}
			top.close();
			</script><?php
	}
	public function ipnResponseBilling() {
		$dir = pathinfo ( __FILE__, PATHINFO_DIRNAME ) . "/bill-ipn.txt";
		$texto = @file_get_contents ( $dir );
		if (! $texto) {
			$texto = "";
		}
		ob_start ();
		print_r ( $_POST );
		print_r ( $_GET );
		print_r ( $_REQUEST );
		$x = ob_get_clean ();
		$texto = "[" . date ( "Y-m-d H:i:s" ) . "]\n" . $x . $texto;
		file_put_contents ( $dir, $texto );
		$status = $this->input->post ( "status" );
		if ($status == "COMPLETED") {
			$pk = $this->input->post ( "pay_key" );
			$this->db->select ( "id" );
			$this->db->where ( array (
					"paypal_id" => $pk 
			) );
			$res = $this->db->get ( "factura" );
			if ($res && ($r = $res->result ()) && is_array ( $r ) && count ( $r ) > 0) {
				$this->db->update ( "factura", array (
						"estado" => "Pagado" 
				), array (
						"id" => $r [0]->id 
				) );
			}
		} elseif ($status == "ERROR") {
			$pk = $this->input->post ( "pay_key" );
			$this->db->select ( "id" );
			$this->db->where ( array (
					"paypal_id" => $pk 
			) );
			$res = $this->db->get ( "factura" );
			if ($res && ($r = $res->result ()) && is_array ( $r ) && count ( $r ) > 0) {
				$this->db->update ( "factura", array (
						"estado" => "Pendiente",
						"paypal_id" => null 
				), array (
						"id" => $r [0]->id 
				) );
			}
		}
	}
	public function ipnResponse() {
		$dir = pathinfo ( __FILE__, PATHINFO_DIRNAME ) . "/log-ipn.txt";
		$texto = @file_get_contents ( $dir );
		if (! $texto) {
			$texto = "";
		}
		ob_start ();
		print_r ( $_POST );
		print_r ( $_GET );
		print_r ( $_REQUEST );
		$x = ob_get_clean ();
		$texto = "[" . date ( "Y-m-d H:i:s" ) . "]\n" . $x . $texto;
		file_put_contents ( $dir, $texto );
		$status = $this->input->post ( "status" );
		if ($status == "COMPLETED") {
			$pk = $this->input->post ( "pay_key" );
			$this->db->select ( "paquete" );
			$this->db->where ( array (
					"paykey" => $pk 
			) );
			$this->db->order_by ( "fecha desc" );
			$res = $this->db->get ( "paquete_paypal", 1, 0 );
			if ($res && ($r = $res->result ()) && is_array ( $r ) && count ( $r ) > 0) {
				$this->paypal->actualizarPaquete ( $r [0]->paquete );
			}
		}
	}
	public function index() {
		redirect ( "/", "refresh" );
	}
}

?>