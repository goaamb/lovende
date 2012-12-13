<div class="wrapper clearfix">
	<div class="formA">
		<div class="errorTxt"><?=isset($Error)?$Error:""?></div>
		<div><?=isset($Mensaje)?$Mensaje:""?></div>
		<form enctype="multipart/form-data" method="post">
			<div class="line">
				<label>Archivo: </label><input name="archivo" type="file" />
			</div>
			<div class="line">
				<input value="Enviar" class="bt" type="submit" />
			</div>
		</form>
	</div>
</div>