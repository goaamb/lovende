<?php
class Siguiendo_model extends CI_Model {
	
	public function __construct() {
		parent::__construct ();
	}
	function eliminar_siguiendo($id)
    {
    	$this->db->delete('siguiendo', array ('id' => $id));
    }
	
	function get_siguiendo_cantidad($id)
    {
    	
    	$this->db->where('usuario',$id);
    	
    	return $this->db->count_all_results('siguiendo');
    }
	
}

?>