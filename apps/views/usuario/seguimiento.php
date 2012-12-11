
<div class="wrapper clearfix">
<?php
//var_dump($mensaje);
$variabletiempo = 24; // capturar de xml
                      // $this->configuracion->variables("notificacionSeguimiento")

$this->load->view ( "usuario/cabecera-perfil", array (
		"seccion" => "perfil" 
) );

if (count ( $mensaje ) > 3) {
	$num_results = count ( $mensaje ['articulos'] );
	
	// print_r($mensaje['articulos']);
	
	$inicio = (isset ( $inicio ) ? intval ( $inicio ) + 1 : 0);
	$totalpagina = (isset ( $totalpagina ) ? intval ( $totalpagina ) : 0);
	$total = (isset ( $total ) ? intval ( $total ) : 0);
	$mensaje = (isset ( $mensaje ) ? $mensaje : null);
	
	$seccionPerfil = isset ( $seccionPerfil ) ? $seccionPerfil : "";
	
	// revisar
	$vencimientoOferta = intval ( $this->configuracion->variables ( "vencimientoOferta" ) ) * 86400;
} else {
	$num_results = 0;
}

?>	
		

	<header class="cont-cab">
		<div id="cabecera">
			<h1><?="Seguimientos";?></h1>
			<p>
		  	 	<?php if($num_results > 0){ echo $num_results; ?> artículos, mostrando del <strong><?=$inicio?></strong>
				al <strong id="contadorFinal"><?=($inicio+count($mensaje['articulos'])-1)?></strong>
		   		<?php
							} else {
								echo 'Aquí se mostrarán los artículos a los que se este haciendo seguimiento.';
							}
							?>
			</p>
		</div>
		<script type="text/javascript">
	function eliminar(id,idusuario)
	{	
		//var table3 = document.getElementById('faltantes123');
		var parametros = { id: id, idusuario:idusuario};
		$.ajax({
			url:'usuario/quitarseguimiento',
			data:parametros,
			type:"POST",
			
			success: function(datos)
			{
				if(datos !=3)
				{
					//table3.deleteRow(2);
					$("#tr"+id).remove();
					$("#faltantes123").tablesorter(); 
					$("#cabecera").empty();
					$("#cabecera").append(datos);
				}
				else
				{
					location.href="login" ;
				}
				
			}
		});
	};

	function completarlista(id,cantidad)
	{
		var parametros = {id:id, cantidad:cantidad	};
		$.ajax({
				url: 'usuario/listar',
				data: parametros,
				type: "POST",
				success: function(datos)
				{
					$("#faltantes123").append(datos);
					editavermas(id,cantidad);
				}
			
			});
	}

	function editavermas(id,cantidad)
	{
		var parametros = {id:id, cantidad:cantidad	};
		$.ajax({
			url: 'usuario/vermasseguimiento',
			data: parametros,
			type: "POST",
			success: function(datos)
			{
				$("#divvermas").empty();
				$("#divvermas").append(datos);
			}
		});
	}

	$(document).ready(function() 
			  { 
			    $("#faltantes123").tablesorter(); 
			    $('faltantes123 tr').has(':not(th)').hover(
			            function(){
			                $(this).data('currColor',$(this).css('background-color'));
			                $(this).css('background-color','#cdd');
			            },
			            function(){
			                $(this).css('background-color',$(this).data('currColor'));
			            }
			        );
			  } 
			);
	
	</script>

	</header>
		<? if($num_results > 0){ ?>
		<table class="tablesorter" id="faltantes123">
		<thead>
			<tr>
				<th>Título</th>
				<th class="t201 t-r" style="text-align: right;">Pujas / ofertas</th>
				<th class="t201 t-r" data-default="true" style="text-align: right;">Tiempo restante</th>
				<th class="t201 t-r" style="text-align: right;">Precio</th>

			</tr>
		</thead>
		<tbody>
			<?php
			$vw = intval ( $this->configuracion->variables ( "imagenArticuloMinimoAncho" ) );
			$vh = intval ( $this->configuracion->variables ( "imagenArticuloMinimoAlto" ) );
			$idusuario = '';
			foreach ( $mensaje ['articulos'] as $row ) {
				$idusuario = $row->usuarioseguimiento;
				
			$imagen = array_shift ( explode ( ",", $row->foto ) );
			$imagen = imagenArticulo ( $row->usuario, $imagen, "thumb" );
			if ($imagen) {
				list ( $w, $h ) = getimagesize ( BASEPATH . "../$imagen" );
				$nw = intval ( $vw );
				$nh = ceil ( $nw * $h / $w );
				if ($nh > $vh) {
					$nh = intval ( $vh );
					$nw = ceil ( $nh * $w / $h );
				}
			}
				?>
			<tr id=<?php echo "tr".$row->idseguimiento;?>>
				<th class="td-item"><a
					href="product/<?="$row->id - ".normalizarTexto($row->titulo);?>"
					title="<?=($row->titulo)?>"><div class="imagen td-imagen">
							<img src="<?="$imagen"?>" width="<?=$nw;?>" height="<?=$nh;?>" />
						</div><?php
				
				echo $row->titulo;
				// echo $row->usuario->id;
				// $imagen = imagenPerfil( $row->usuario , "thumb" );
				
				// $imagen = imagenArticulo ( $row->usuario, $imagen, "thumb" );
				
				?>
                		 </a><?php
				if ($row->tipo == "Cantidad") {
					print "<br/>" . traducir ( "Cantidad" ) . ": " . $row->cantidad;
				}
				?></th>
				<th style="text-align: right;">
                   <?php $tipooferta= ($row->tipo == 'Fijo' ? $row->cantidadOfertas.' ofertas':($row->tipo == 'Cantidad'?$tipooferta="--": $row->cantidadPujas.' pujas')); echo $tipooferta; ?>
                </th>
				<th style="text-align: right;">
				<?php if($row->terminado == 1){ ?><div><font color="red">Finalizado</font></div><?php }?>
					<div>
                	<?php
				$tiempocolor = '';
				if ($row->tipo == "Fijo") {
					$expira = calculaTiempoDiferencia ( date ( "Y-m-d H:i:s" ), strtotime ( $row->fecha_registro ) + $vencimientoOferta, true );
				} else {
					$expira = calculaTiempoDiferencia ( date ( "Y-m-d H:i:s" ), strtotime ( $row->fecha_registro ) + $row->duracion * 86400, true );
					//$tiempocolor = ($expira == 0 ? "Terminado" : $expira);
				}

				if($expira != '0s')
				{
					echo $expira;
				}
			
				?>
                   </div>
					<div>
						<a
							href="javascript:eliminar('<?php echo $row->idseguimiento."','".$row->usuarioseguimiento;?>')">Eliminar</a>
					</div>
				</th>
				<th style="text-align: right;">
               		<?php $precio= ($row->tipo == 'Fijo'|| $row->tipo == 'Cantidad' ? formato_moneda($row->precio) : formato_moneda($row->mayorPuja)); echo $precio.' EUR'; ?>
               </th>

			</tr>
			<?php
			}
			
			?>
			 
			</tbody>
	</table>
		
        <? if ($totalseguir > $num_results) { ?>
		<div id="divvermas">
		<p class="ver-mas">
			<a
				href="javascript:completarlista('<?php echo $idusuario."','".($inicio+count($mensaje['articulos'])-1);?>')"
				title="Ver más ventas">Ver más</a>
		</p>
	</div>
		<?
			}
		}
		?>
</div>