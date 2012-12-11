<script type="text/javascript">
function buscar(e, valor, tipo) {
	  tecla = (document.all) ? e.keyCode : e.which;
	  if (tecla==13)
	  {
		  //window.locationf="administrador/buscar?valor=" + valor + "&tipo=" + tipo ;
		  location.href="administrador/buscarnotificacion?valor=" + valor + "&tipo=" + tipo ;
	  }
	}

</script>

<div class="content">
	<div class="wrapper clearfix">
		<header class="cont-cab">
			<div class="forms">
				<p>
					<span class="label-on-field"> <label>Search by username</label> <input
						type="text" class="texto"
						onkeypress="buscar(event,value,'usuario')" />
					</span>
				</p>
			</div>
			<h1>Dashboard</h1>
			<p>
				<a href="administration/dashboard" title="reports">Reports</a> |
				admin PM | <a href="administration/billing" title="billing">billing</a>
			</p>
		</header>

		<ul class="comments-list">

			<li class="thread">
				<ul>

					<li>
						<div class="comment-box">
							<form action="administrador/guardarmensaje" method="post">
								<input type="hidden" value="<?php echo $idadmin;?>" name="id" />
								<p>
									<textarea id="textareanuevomensaje" name="mensaje" cols=""
										rows="3"></textarea>
								</p>
								<p class="t-r">
									<input type="submit" class="action" value="Enviar" />
								</p>
							</form>
						</div>
					</li>
				</ul>
				<?php
				
				if ($mensaje) {
					
					foreach ( $mensaje as $row ) {
						
						$this->load->model ( 'usuario_model', 'objusuario' );
						$idusuario = $this->objusuario->darUsuarioXId ( $row->idusuario );
						// $usuario = $this->usuario_model->darUsuarioXId($id);
						
						$imagen = imagenPerfil ( $idusuario, "" );
						?>
				<div class="avatar">
					<img src=<?php echo $imagen;?> alt="Imagen de perfil de xxx"
						width="64" />
				</div>
				<div class="user-comment">
					<p class="user-name">
						<strong><?php echo $row->seudonimo;?></strong> hace 
					<?php
						if ($row->anios >= 1) {
							echo $row->anios . ' años';
						} else {
							if ($row->meses >= 1) {
								echo $row->meses . ' meses';
							} else {
								if ($row->semanas >= 1) {
									echo $row->semanas . ' semanas';
								} else {
									if ($row->dias >= 1) {
										echo $row->dias . ' dias';
									} else {
										if ($row->horas >= 1) {
											echo $row->horas . ' horas';
										} else {
											if ($row->minutos >= 1) {
												echo $row->minutos . ' minutos';
											} else {
												echo $row->segundos . ' segundos';
											}
										}
									}
								}
							}
						}
						
						?>
					
					</p>
					<div class="justify">
						<p><?php echo $row->mensaje;?></p>
						</br>
					</div>

				</div>
				<?php
					}
				}
				?>
				

			</li>
			<!--thread-->

		</ul>
		<!--comments-list-->





	</div>
	<!--wrapper-->
</div>
<!--content-->