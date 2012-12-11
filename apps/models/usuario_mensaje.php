<?php

class Usuario_mensaje extends CI_Model {
	
	public function __construct() {
		parent::__construct ();
	}
  
    function get_mensaje($idreceptor,$num = false,$offset =false, $estado, $emisor)
    {  
    	$this->load->model("Usuario_model","usuarioM");
    	$usuarioR=$this->usuarioM->darUsuarioXId($idreceptor);
    	if($emisor != false)
    	{
    		$this->db->select("mensaje.id, mensaje.receptor, mensaje.emisor, mensaje.mensaje, mensaje.fecha, mensaje.estado, mensaje.articulo, mensaje.estado_receptor, usuario.seudonimo, usuario.imagen,
    				mensaje.tipo as 'tipomensaje'
    				,TIMESTAMPDIFF(YEAR,TIMESTAMP(fecha),TIMESTAMP(now())) as anios
    				,TIMESTAMPDIFF(MONTH,TIMESTAMP(fecha),TIMESTAMP(now())) as meses
    				,TIMESTAMPDIFF(WEEK,TIMESTAMP(fecha),TIMESTAMP(now())) as semanas
    				,TIMESTAMPDIFF(DAY,TIMESTAMP(fecha),TIMESTAMP(now())) as dias
    				,TIMESTAMPDIFF(HOUR,TIMESTAMP(fecha),TIMESTAMP(now())) as horas
    				,TIMESTAMPDIFF(MINUTE,TIMESTAMP(fecha),TIMESTAMP(now())) as minutos
    				,TIMESTAMPDIFF(SECOND,TIMESTAMP(fecha),TIMESTAMP(now())) as segundos, usuario.estado as estadousuario, usuario2.estado as estadousuario2",false);
    		 
    		$this->db->from('mensaje');
    		$this->db->join('usuario', 'usuario.id = mensaje.emisor');
    		$this->db->join('usuario as usuario2', 'usuario2.id = mensaje.receptor');
    		 
    		$this->db->where('(((receptor = '.$idreceptor.' and emisor = '.$emisor.') or (receptor = '.$emisor.' and emisor = '.$idreceptor.')) and (visible = '.$idreceptor.' or visible = 0))');
    		//$this->db->where('');
    		$this->db->where('denuncia','No denunciado');
    		$this->db->order_by('segundos','DESC');
    		
    		if($num != false)
    		{
    			//$limite = 'limit '.$num.','.$offset;
    			$this->db->limit($num,$offset);
    		}
    		
    		$res = $this->db->get ();
    		if ($res) {
    			$res = $res->result ();
    			if ($res && is_array ( $res ) && count ( $res ) > 0) {
    				return $res;
    			}
    		}
    		return false;
    	}
    	else
    	{
    		$limite = '';
    		if($num != false)
    		{
    			$limite = 'limit '.$num.','.$offset;
    		}
    		$condicionestado = '';
    		
    		$condicionnotificacion = "where (noti.receptor = $idreceptor or noti.receptor is null) and noti.fecha>='$usuarioR->registro'";
    		
    		if($estado != false)
    		{
    			//$condicionestado = "and (sub.estado = '".$estado."' or sub.estado_receptor = '".$estado."')";
    			
    			$condicionestado = "from (select * from mensaje order by id DESC)sub left join usuario on usuario.id = sub.emisor 
    			left JOIN usuario as usuario2 ON usuario2.id = sub.receptor
    			where ((receptor = $idreceptor and sub.estado = '$estado') or (emisor = $idreceptor and sub.estado_receptor = '$estado')) 
    			and (sub.visible='0' or sub.visible=$idreceptor) order by segundos ASC $limite";
    			
    			/*$condicionnotificacion = " LEFT JOIN notificacion_leido as notiledio ON notiledio.usuario = $idreceptor
										  where (notiledio.usuario=$idreceptor and notiledio.visible=0) and (noti.receptor = $idreceptor or noti.receptor is null)";*/
    			
    			$condicionnotificacion = " where noti.id in(select notificacion from notificacion_leido where usuario=$idreceptor and visible=0) and noti.fecha>='$usuarioR->registro'";
    			
    		}
    		else
    		{
    			$condicionestado = "from (select * from mensaje where receptor = $idreceptor or emisor= $idreceptor order by id DESC)sub left join usuario on usuario.id = sub.emisor
    			left JOIN usuario as usuario2 ON usuario2.id = sub.receptor
    				where (sub.visible='0' or sub.visible=$idreceptor) $condicionestado order by segundos ASC $limite";
    		}
    		 

    		
    		
    		$res = $this->db->query("(select sub.id, sub.receptor, usuario.seudonimo,sub.mensaje,sub.fecha,sub.estado, sub.estado_receptor, sub.articulo,sub.emisor,
    				TIMESTAMPDIFF(YEAR,TIMESTAMP(sub.fecha),TIMESTAMP(now())) as anios
    				,TIMESTAMPDIFF(MONTH,TIMESTAMP(sub.fecha),TIMESTAMP(now())) as meses
    				,TIMESTAMPDIFF(WEEK,TIMESTAMP(sub.fecha),TIMESTAMP(now())) as semanas
    				,TIMESTAMPDIFF(DAY,TIMESTAMP(sub.fecha),TIMESTAMP(now())) as dias
    				,TIMESTAMPDIFF(HOUR,TIMESTAMP(sub.fecha),TIMESTAMP(now())) as horas
    				,TIMESTAMPDIFF(MINUTE,TIMESTAMP(sub.fecha),TIMESTAMP(now())) as minutos
    				,TIMESTAMPDIFF(SECOND,TIMESTAMP(sub.fecha),TIMESTAMP(now())) as segundos, usuario.imagen, sub.tipo as tipomensaje, 
    				usuario.estado as estadousuario, usuario2.estado as estadousuario2
    				$condicionestado ) UNION ALL
    				(
    				select noti.id, $idreceptor, 'ADMIN-LOVENDE' as seudonimo, noti.mensaje, noti.fecha, NULL as estado, NULL as estado_receptor, NULL as articulo, NULL as emisor,
					TIMESTAMPDIFF(YEAR,TIMESTAMP(noti.fecha),TIMESTAMP(now())) as anios 
					,TIMESTAMPDIFF(MONTH,TIMESTAMP(noti.fecha),TIMESTAMP(now())) as meses 
					,TIMESTAMPDIFF(WEEK,TIMESTAMP(noti.fecha),TIMESTAMP(now())) as semanas 
					,TIMESTAMPDIFF(DAY,TIMESTAMP(noti.fecha),TIMESTAMP(now())) as dias 
					,TIMESTAMPDIFF(HOUR,TIMESTAMP(noti.fecha),TIMESTAMP(now())) as horas 
					,TIMESTAMPDIFF(MINUTE,TIMESTAMP(noti.fecha),TIMESTAMP(now())) as minutos 
					,TIMESTAMPDIFF(SECOND,TIMESTAMP(noti.fecha),TIMESTAMP(now())) as segundos
					, usuario.imagen , 'notificacion' as tipomensaje, NULL as estadousuario , NULL as estadousuario2 
					from notificacion as noti
					LEFT JOIN usuario ON usuario.id = noti.emisor $condicionnotificacion
    				)order by segundos ASC
    				
    				");
    		
    		if ($res) {
    			$res = $res->result ();
    			if ($res && is_array ( $res ) && count ( $res ) > 0) {
    				return $res;
    			}
    		}
    		return false;
    	}    		
    }  

    function get_cantidad_listas($id,$estado=false)
    {
    	$condicionestado = '';
    	if($estado != false)
    	{
    		$condicionestado = "and sub.estado = '".$estado."'";
    	}
    	
    	
    	$this->db->where("receptor = $id $condicionestado and
    			(visible='0' or visible=$id) group by emisor");
    	$this->db->from("mensaje");
    	
    	$res = $this->db->get ();
    		if ($res) {
    			$res = $res->result ();
    			if ($res && is_array ( $res ) && count ( $res ) > 0) {
    				return count($res);
    			}
    		}
    		return false;
    }
    
    
    function get_mensaje_cantidad ($id,$idemisor)
    {
    	if ($idemisor != false)
    	{
    		$this->db->where('(((receptor = '.$id.' and emisor = '.$idemisor.') or (receptor = '.$idemisor.' and emisor = '.$id.'))and (visible = '.$id.' or visible = 0))');	
    		$this->db->where('denuncia','No denunciado');
    	}
    	else
    	{
    		$this->db->where('receptor = ',$id);
    		$this->db->where('(visible = 0 or visible = '.$id.')');
    	}
    	
    	
    	return $this->db->count_all_results('mensaje');
    }
    
    function cambiar_estado($receptor, $emisor, $datosmodificar)
    {
    	$this->db->where('receptor', $receptor);
    	$this->db->where('emisor', $emisor);
    	$this->db->update('mensaje',$datosmodificar);
    }
    
    function cambiar_estado2($receptor=false, $emisor=false, $datosmodificar, $tipo = false)
    {
    	if($tipo !=FALSE)
    	{
    		$this->db->where("(receptor = $receptor or emisor= $receptor) and tipo='Admin'");
    	}
    	else
    	{
    		$this->db->where('(receptor = '.$receptor.' and emisor = '.$emisor.') or (receptor = '.$emisor.' and emisor = '.$receptor.')');
    	}
    	
    	$this->db->update('mensaje',$datosmodificar);
    }
    
    function devolver_estado($receptor,$emisor)
    {
    	$res = $this->db->query("SELECT visible FROM mensaje where receptor=$receptor and emisor=$emisor limit 1");
    		
    		if ($res) {
    			$res = $res->result ();
    			if ($res && is_array ( $res ) && count ( $res ) > 0) {
    				return $res;
    			}
    		}
    		return false;	
    	
    }
    
    function cambiar_estado_id($id, $datosmodificar)
    {
    	$this->db->where('id', $id);
    	$this->db->update('mensaje',$datosmodificar);
    }
    
    function eliminar_mensaje($id)
    {
    	$this->db->delete('mensaje', array ('id' => $id));
    }
        
    public function cantidad_mensaje_usuario($usuario)
    {
         $query = $this->db->query("select count(*) as count
                                    from mensaje m inner join usuario u on u.id=m.receptor inner join usuario ue on ue.id=m.emisor 
                                    where  m.id in (select mensaje from mensaje 
                                                    where receptor=$usuario 
                                                    ) ");
         
       		
		return ($query->row()!=null)?$query->row()->count:0;
                
              
        
    }

    public function devolverXmensaje($idmensaje)
    {
    	$this->db->select('*');
    	$this->db->from('mensaje');
    	$this->db->where('id', $idmensaje);
    	
    	$res = $this->db->get();
    	
    	if($res)
    	{
    		$res = $res->result();
    		if($res && is_array($res) && count ($res) > 0)
    		{
    			return $res;
    		}
    	}
    	return false;
    }
    
    //notificacion
    public function darnotificacion($idmensaje)
    {
    	$this->db->select('noti.id,usua.id as idusuario, usua.seudonimo, noti.mensaje, noti.fecha
					,TIMESTAMPDIFF(YEAR,TIMESTAMP(fecha),TIMESTAMP(now())) as anios
    				,TIMESTAMPDIFF(MONTH,TIMESTAMP(fecha),TIMESTAMP(now())) as meses
    				,TIMESTAMPDIFF(WEEK,TIMESTAMP(fecha),TIMESTAMP(now())) as semanas
    				,TIMESTAMPDIFF(DAY,TIMESTAMP(fecha),TIMESTAMP(now())) as dias
    				,TIMESTAMPDIFF(HOUR,TIMESTAMP(fecha),TIMESTAMP(now())) as horas
    				,TIMESTAMPDIFF(MINUTE,TIMESTAMP(fecha),TIMESTAMP(now())) as minutos
    				,TIMESTAMPDIFF(SECOND,TIMESTAMP(fecha),TIMESTAMP(now())) as segundos',false);
		
		$this->db->from('notificacion as noti');
		$this->db->join('usuario as usua', 'usua.id = noti.emisor', 'left');
		$this->db->where('noti.id',$idmensaje);
		
		$res =$this->db->get();		
		
		if ($res) {
			$res = $res->result ();
			if ($res && is_array ( $res ) && count ( $res ) > 0) {
				return $res;
			}
		}
		return false;
    }
    
    public function verestadonotificacion($idnotificacion,$usuario)
    {
    	$this->db->select('visible as estado');
    	$this->db->from('notificacion_leido');
    	$this->db->where('notificacion', $idnotificacion);
    	$this->db->where('usuario', $usuario);
    	$res =$this->db->get();		
		
		if ($res) {
			$res = $res->result ();
			if ($res && is_array ( $res ) && count ( $res ) > 0) {
				return $res;
			}
		}
		return false;
    }
    
    public function guardardetallenotificacion($idnotificacion,$usuario)
    {
    	$data = array(
   				'notificacion' => $idnotificacion ,
   				'usuario' => $usuario ,   				
				'fecha_leido' => strftime( "%Y-%m-%d %H:%M:%S", time() ),
    			'visible' => 0
				);

				$this->db->insert('notificacion_leido', $data);
    }
    
    public function modificarestadoadmin($idnotificacion,$usuario, $datosmodificar)
    {
    	$this->db->where('(usuario = '.$usuario.' and notificacion = '.$idnotificacion.')');
    	
    	$this->db->update('notificacion_leido',$datosmodificar);
    }
    public function get_mensaje_admin($idusuario, $inicio = false, $limit = false)
    {
    	$limite = "";
    	if($inicio != FALSE)
    	{
    		$limite = "limit ".$limit.",". $inicio;
    	}
    	
    	$res = $this->db->query("select sub.id, sub.receptor, usuario.seudonimo,sub.mensaje,sub.fecha,sub.estado,sub.articulo,sub.emisor,
    			TIMESTAMPDIFF(YEAR,TIMESTAMP(sub.fecha),TIMESTAMP(now())) as anios
    			,TIMESTAMPDIFF(MONTH,TIMESTAMP(sub.fecha),TIMESTAMP(now())) as meses
    			,TIMESTAMPDIFF(WEEK,TIMESTAMP(sub.fecha),TIMESTAMP(now())) as semanas
    			,TIMESTAMPDIFF(DAY,TIMESTAMP(sub.fecha),TIMESTAMP(now())) as dias
    			,TIMESTAMPDIFF(HOUR,TIMESTAMP(sub.fecha),TIMESTAMP(now())) as horas
    			,TIMESTAMPDIFF(MINUTE,TIMESTAMP(sub.fecha),TIMESTAMP(now())) as minutos
    			,TIMESTAMPDIFF(SECOND,TIMESTAMP(sub.fecha),TIMESTAMP(now())) as segundos, usuario.imagen, sub.tipo as tipomensaje
    			from (select * from mensaje where (receptor = $idusuario or emisor= $idusuario) and tipo='Admin' order by id DESC)
    			sub left join usuario on usuario.id = sub.emisor where (sub.visible='0' or sub.visible=78) order by segundos DESC 
    			$limite
    				");
    		
    	if ($res) {
    		$res = $res->result ();
    		if ($res && is_array ( $res ) && count ( $res ) > 0) {
    			return $res;
    		}
    	}
    	return false;
    }
}
?>
