<?php

class Following_model extends CI_Model {
	
	public function __construct() {
		parent::__construct ();
	}
        
       /* function mostrar_seguimiento($limit, $offset, $sort_by, $sort_order) {
		
		$sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';
		$sort_columns = array('id', 'articulo', 'usuario', 'fecha');
		$sort_by = (in_array($sort_by, $sort_columns)) ? $sort_by : 'title';
		
		// results query
		$q = $this->db->select('FID, title, category, length, rating, price')
			->from('film_list')
			->limit($limit, $offset)
			->order_by($sort_by, $sort_order);
		
		$ret['rows'] = $q->get()->result();
		
		// count query
		$q = $this->db->select('COUNT(*) as count', FALSE)
			->from('seguimiento');
		
		$tmp = $q->get()->result();
		
		$ret['num_rows'] = $tmp[0]->count;
		
		return $ret;
	}*/
        function contar_seguimiento($id)
        {   
            $q = $this->db->select('COUNT(*) as count', FALSE)
			->from('siguiendo')
                        ->where(array('usuario' => $id));
                      
          
            
		$tmp = $q->get()->result();
		
		$ret['num_rows'] = $tmp[0]->count;
		
		return $ret;
        }
       /*function get_seguimiento($id)
        {   
          $q = $this->db->select('*')
			->from('siguiendo')
			->where(array('usuario' => $id));
		
		$ret['rows'] = $q->get()->result();
               
        }*/
       
         function get_seguimiento($id)  
        {   
             $query = $this->db->query("select *
                                    from siguiendo
                                    where  'usuario'=$id
                                                    
                                    ");
       return $query;
            
        }
        
}
?>
