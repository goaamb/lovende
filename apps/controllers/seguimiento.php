<?php
require_once ("basecontroller.php");
class Seguimiento extends BaseController 
{


	public function __construct() {
		parent::__construct ();
		$this->load->helper ( 'url' );
		$this->load->model ( "Articulo_model", "articulo" );
		$this->load->library ( 'image_lib' );
	}
       
            public function following() {
		
              
		$data['fields'] = array(
                    //campo => titulo de campo
                    'titulo' => 'Título',
			'tipo' => 'Pujas/oferta',
			'duracion' => 'Tiempo restante',
			'precio' => 'precio',

		);	
                $view = "usuario/following_view";
               
                $this->load->library('table');
		$this->load->model ( "following_model" );//cargamos el archivo usuario_mensaje.php
                //$result = $this->following_model->get_seguimiento($this->myuser->id);
                $data['articulos'] = $this->following_model->get_seguimiento($this->myuser->id);
                //$data['articulos'] = $this->following_model->get_seguimiento($this->myuser->id);
                $config = array();
                $results = $this->following_model->contar_seguimiento($this->myuser->id);
                $data['num_results'] = $results['num_rows'];
             

		$this->loadGUI ( $view, $data);
            
	}
         
        
      
        
}

?>
