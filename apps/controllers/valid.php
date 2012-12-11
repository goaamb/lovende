<?php

class Valid extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->database ();
		$this->load->library ( "mysession" );
	}
	public function unique() {
		$t = $this->input->post ( "table" );
		$f = $this->input->post ( "field" );
		$v = $this->input->post ( "value" );
		ob_start ();
		$this->db->where ( array (
				$f => $v 
		) );
		$res = $this->db->count_all_results ( $t );
		$c = ob_get_clean ();
		if (! $c) {
			$this->output->set_output ( json_encode ( array (
					"unique" => ! ($res > 0) 
			) ) );
		} else {
			$this->output->set_output ( json_encode ( array (
					"error" => $c 
			) ) );
		}
	}
	public function imageCode() {
		$v = $this->input->post ( "value" );
		$captcha = $this->mysession->userdata ( "CAPTCHA" );
		$this->output->set_output ( json_encode ( array (
				"verified" => strtolower ( $captcha ["word"] ) == strtolower ( $v ) 
		) ) );
	}
}

?>