<?php
$orden = isset ( $orden ) ? $orden : "";
$ubicacion = isset ( $ubicacion ) ? $ubicacion : "";
$uri = array_shift ( explode ( "/", uri_string () ) );
$profile = (isset ( $profile ) ? $profile : "");
if (trim ( $orden ) === "" && $profile) {
	$orden = "finaliza";
}
?><aside>
	<div class="search-box">
		<form action="" method="get"
			onsubmit="return cambiarCriterioBusqueda.call(this.criterio,true);">
		<?php
		if ($profile) {
			?><p class="label-on-field">
				<input type="text" class="texto OwnTextBox" name="criterio"
					data-class="OwnTextBoxNoData"
					data-text="<?=traducir("Buscar en la tienda")?>" onclick=""
					value="<?=$this->input->get("usuario")?$this->input->get("criterio"):"";?>" />
			</p>
			<script type="text/javascript">profile=<?=$profile?"true":"false";?>;</script><?php
		}
		?>
			<p class="apaisado">
			<?php
			$lista = array (
					"ultimos" => traducir ( "Últimos Anuncios" ),
					// "relavancia" => traducir ( "Relevancia" ),
					"finaliza" => traducir ( "Finalizan Primero" ),
					"mas-alto" => traducir ( "Precio más alto" ),
					"mas-bajo" => traducir ( "Precio más bajo" ) 
			);
			?>
				<label><?=traducir ( "Ordenar por" )?></label> <select class="texto"
					onchange="cambiarOrdenBusqueda.call(this);"><?php
					foreach ( $lista as $k => $v ) {
						?><option value="<?=$k?>"
						<?=($k==$orden)?'selected="selected"':""?>><?=$v?></option><?php
					}
					?></select>
			</p>
			<?php
			if ($this->tipoUbicacion == "dominio") {
				$listaUbicacion = array (
						"A" => traducir ( "Todo el mundo" ),
						"P-" . $this->pais->codigo3 => $this->pais->nombre,
						"C-" . $this->pais->continente => traducir ( $this->pais->continente ) 
				);
			} else {
				$listaUbicacion = array (
						"A" => traducir ( "Todo el mundo" ),
						"P-ESP" =>traducir ( "España"),
						"C-Europe" => traducir ( "Europa" ) 
				);
			}
			?>
			<?php if(!$profile){?>
			<p class="apaisado">
				<label><?=traducir ( "Ubicación" )?></label> <select class="texto"
					onchange="cambiarUbicacionBusqueda.call(this)">
				<?php
				foreach ( $listaUbicacion as $k => $v ) {
					?><option value="<?=$k?>"
						<?=($k==$ubicacion)?'selected="selected"':""?>><?=$v?></option><?php
				}
				?>
				</select>
			</p>
			<?php }?>
		</form>
	</div>
	<h2 class="tit"><?=traducir("Categoría");?></h2>
		<?php $this->load->view("home/listarcategorias");?>
	</aside>