<?php

$inicio = (isset ( $inicio ) ? intval ( $inicio ) + 1 : 0);
$totalpagina = (isset ( $totalpagina ) ? intval ( $totalpagina ) : 0);
$total = (isset ( $total ) ? intval ( $total ) : 0);
$mensaje = (isset ( $mensaje ) ? $mensaje : null);


$seccionPerfil = isset ( $seccionPerfil ) ? $seccionPerfil : "";


				

	
?>	
		

	<header class="cont-cab">
		<h1><?="Seguimientos";?></h1>
		<p>

			 
                                
                             
                             <?php echo $num_results; ?> artículos, mostrando del <strong><?=$inicio?></strong> al
			<strong id="contadorFinal"><?=($inicio+count($mensaje)-1)?></strong>
		
		</p>
	</header>	
		<table class="tablesorter">
			<thead>
				<tr>
                                    
					
				<?php foreach($fields as $field_name => $field_display): ?>
			<?php
                         if($field_display=="Título")
                        {
                            echo"<th>";
                            echo"Título";
                            echo"</th>";
                        }
                        else
                        {
                        ?>
                                    <th>
				<?php echo $field_display; ?>
			</th>
			<?php 
                        }
                        endforeach; ?>	
                                    
				</tr>
			</thead>
			<tbody>
		
                           <?php 
                            if (isset ( $articulos ) && is_array ( $articulos ) && count ( $articulos ) > 0)
                            {
                           foreach($articulos as $film): ?>
			<tr>
				<?php foreach($fields as $field_name => $field_display): ?>
				<td>
					<?php echo $film->$field_name; ?>
				</td>
				<?php endforeach; ?>
			</tr>
			<?php endforeach; 
                            }
                        ?>
					
				
			</tbody>
		</table>
<p class="ver-mas"><a href="#" title="Ver más ventas">Ver más</a></p>