<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class CI_Myemail {
	private $CI;
	public function __construct($config = array()) {
		$this->CI = & get_instance ();
		$this->CI->load->library ( "email" );
	}
	public function enviar($email, $asunto, $mensaje) {
		/*
		 * $this->CI->email->initialize ( array ( 'charset' => 'utf-8',
		 * 'mailtype' => 'html', 'protocol' => 'smtp', 'smtp_port' => 2525,
		 * 'smtp_host' => '209.236.66.107', 'smtp_user' =>
		 * 'support@lovende.com', 'smtp_pass' => '1fIV{mul3_Hi', 'smtp_timeout'
		 * => 30 ) );
		 */
		$this->CI->email->initialize ( array (
				'charset' => 'utf-8',
				'mailtype' => 'html',
				'protocol' => 'smtp',
				'smtp_port' => 465,
				'smtp_host' => 'ssl://dl-572-15.slc.westdc.net',
				'smtp_user' => 'support@lovende.com',
				'smtp_pass' => '1fIV{mul3_Hi',
				'validate' => true,
				'smtp_timeout' => 5 
		) );
		
		/*
		 * $this->CI->email->initialize ( array ( 'charset' => 'utf-8',
		 * 'mailtype' => 'html', 'protocol' => 'smtp', 'smtp_port' => 465,
		 * 'smtp_host' => 'ssl://smtp.googlemail.com', 'smtp_user' =>
		 * 'goaamb@gmail.com', 'smtp_pass' => '470N31N7H3D4RK', 'smtp_timeout'
		 * => 30 ) );
		 */
		$this->CI->email->set_newline ( "\r\n" );
		$this->CI->email->set_crlf ( "\r\n" );
		$this->CI->email->set_priority ( "\r\n" );
		$this->CI->email->from ( $this->CI->configuracion->variables ( "emailFrom" ), 'El equipo de Lovende' );
		$this->CI->email->to ( $email );
		$this->CI->email->subject ( $asunto );
		$this->CI->email->message ( $mensaje );
		return $this->CI->email->send ();
	}
	public function enviarTemplate($email, $asunto, $template, $params = array()) {
		if (! is_array ( $params )) {
			$params = array ();
		}
		return $this->enviar ( $email, $asunto, $this->CI->load->view ( $template, $params, true ) );
	}
}

?>