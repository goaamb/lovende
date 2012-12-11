<script type="text/javascript">

$(document).ready(function(){ 
		      
    $('#filtrofecha').change( function()
    { 

        var id_usuario = $('#filtrofecha').val();
        location.href="administrador/buscarfacturaXfecha?valor=" + id_usuario;
    });
}); 

function buscar(e, valor, tipo) {
	  tecla = (document.all) ? e.keyCode : e.which;
	  if (tecla==13)
	  {
		  //window.locationf="administrador/buscar?valor=" + valor + "&tipo=" + tipo ;
		  location.href="administrador/buscarfactura?valor=" + valor + "&tipo=" + tipo ;
	  }
	}

function historial(valor, tipo) 
	{	  
		  location.href="administrador/buscarfactura?valor=" + valor + "&tipo=" + tipo ;	  
	}


</script>

<div class="content">
	<div class="wrapper clearfix">
		<header class="cont-cab">
			<div class="forms">
				<p>

					<select class="texto" name="filtrofecha" id="filtrofecha">
						<option value="<?php echo $valordefecto;?>">Filtrado por: <?php echo $valordefecto;?></option>
					<?php
					foreach ( $grupofechas as $rowfechas ) {
						?>
							<option value="<?php echo $rowfechas->mes;?>"><?php echo $rowfechas->mes;?></option>
							
					<?php }?>
					</select> <a
						href="administrador/exportarexcel?fecha=<?php echo $valordefecto;?>">Exportar
						XLS &nbsp;&nbsp;</a> <span class="label-on-field"> <label>Search
							by username</label> <input type="text" class="texto"
						onkeypress="buscar(event,value,'usuario')" />
					</span> <span class="label-on-field"> <label>Search by Bill ID</label>
						<input type="text" class="texto"
						onkeypress="buscar(event,value,'factura')" />
					</span>
				</p>
			</div>
			<h1>Dashboard</h1>
			<p>
				<a href="administration/dashboard" title="reports">Reports</a> | <a
					href="administration/admipm" title="Admin">admin PM</a> | billing <a
					href="administrador/buscarfactura" style="float: right;">Clear
					search</a>
			</p>
		</header>

		<div class="post">
		<?php if($vista =="seeall"){?>
			<p class="dark-grey">See all (<?php echo $cantidadtotal;?>) | <a
					href="administration/curse" title="See only curse">only on curse (<?php echo $curso;?>)</a>
				| <a href="administration/facpending" title="See only pending">only pending (<?php echo $pendiente;?>)</a>
				| <a href="administration/paid" title="See only paid">only paid (<?php echo $pagado;?>)</a>
			</p>
		<?php }?>
		<?php if($vista =="curse"){?>
			<p class="dark-grey">
				<a href="administration/billing" title="See only curse">See all (<?php echo $cantidadtotal;?>)</a> | only on curse (<?php echo $curso;?>) | <a
					href="administration/facpending" title="See only pending">only pending (<?php echo $pendiente;?>)</a>
				| <a href="administration/paid" title="See only paid">only paid (<?php echo $pagado;?>)</a>
			</p>
		<?php }?>
		<?php if($vista =="pending"){?>
			<p class="dark-grey">
				<a href="administration/billing" title="See only curse">See all (<?php echo $cantidadtotal;?>)</a>
				| <a href="administration/curse" title="See only curse">only on curse (<?php echo $curso;?>)</a> | only pending (<?php echo $pendiente;?>) | <a
					href="administration/paid" title="See only paid">only paid (<?php echo $pagado;?>)</a>
			</p>
		<?php }?>
		<?php if($vista =="paid"){?>
			<p class="dark-grey">
				<a href="administration/billing" title="See only curse">See all (<?php echo $cantidadtotal;?>)</a>
				| <a href="administration/curse" title="See only curse">only on curse (<?php echo $curso;?>)</a>
				| <a href="administration/facpending" title="See only pending">only pending (<?php echo $pendiente;?>)</a>
				| <a href="administration/paid" title="See only paid">only paid (<?php echo $pagado;?>)</a>
			</p>
		<?php }?>
		</div>
		<?php
		
		if (($reporte)) {
			?>
		<table class="border mbl">
			<thead>
				<tr>
					<th>Bill ID</th>
					<th>Date</th>
					<th>User</th>
					<th>Plan</th>
					<th>Stock</th>
					<th>Bill (&#8364;)</th>
					<th>Rate (&#8364;)</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
			
			<?php
			// var_dump($reporte);
			foreach ( $reporte as $row ) {
				?>
				<tr>
					<td><a
						href="home/modal/factura-detalle/facturaDetalle/<?=$row->id."/".$row->idusu?>"
						target="_blank"><?php echo $row->codigo;?></a></td>
					<td><?php echo $row->mes;?></td>
					<td><a href="#" class="actMenuB"><?php echo $row->seudonimo;?></a>
						<span class="green">+<?php echo $row->positivo;?></span>
						<?php
				if ($row->negativo) {
					?>
								 <span class="red">-<?php echo $row->negativo;?></span>	
							<?php
				}
				?>
						<div class="act-menu-b">
							<div class="cont">
								<ul>
									<li><a href="store/<?php echo $row->seudonimo;?>"
										target="_blank">Go to</a></li>
									<li><a
										href="javascript:historial('<?php echo $row->seudonimo."','usuario";?>')">History</a></li>
									<li><a
										href="administrador/mostrarmodalmensaje?id=
										<?php echo $row->idusu.'&seu='.$row->seudonimo.'&posi='.$row->positivo.'&neg='.$row->negativo;?>"
										class="nmodal">Send PM</a></li>

								</ul>
							</div>
							<div class="arrow"></div>
						</div></td>
					<td><?php echo $row->tipo_tarifa;?></td>
					<td><?php echo $row->monto_stock;?></td>
					<td><?php echo $row->monto_venta;?></td>
					<td><?php  echo $row->monto_tarifa;?></td>
					<td><?php echo $row->estado;?></td>
				</tr>
				<?php }?>
			</tbody>
		</table>
		<?php if($cantidadtotal > $this->configuracion->variables ( "cantidadPaginacion" )){?>
		<p class="ver-mas">
			<a title="Ver más" href="#">Ver más</a>
		</p>
				<?php } }?>
		<script>
			$('.actMenuB').actMenuB();
		</script>

	</div>
	<!--wrapper-->
</div>
<!--content-->
