<?php

if ($imagen) {
	$dir = "files/" . $usuario->id . "/";
	$name = pathinfo ( $imagen, PATHINFO_FILENAME );
	$ext = pathinfo ( $imagen, PATHINFO_EXTENSION );
	$classcapaphoto .= " uploaded";
}
?>
<div class="uploader <?=$classcapaphoto?>" id="<?=$idcapaphoto?>"
	<?php if($imagen){?> data-name="<?=$imagen?>"
	data-thumb="<?="$dir$name.thumb.$ext"?>" style="background: transparent url(<?php
		if ($quiencapaphoto !== "perfil") {
			print "$dir$name.thumb.$ext";
		} else {
			print "$dir$name.$ext";
		}
		?>) center center no-repeat;"<?php }?>>
	<span class="spanObj" <?php if($imagen){?>
		style="opacity: 0; filter: alpha(opacity =           0);" <?php }?>><a
		href="#" onclick="return false;"
		id="spanButtonPlaceholder<?php print $quiencapaphoto;?>"></a></span> <span
		class="spanTxt" data-html="<?=$textocapaphoto?>" style="top:<?=($quiencapaphoto=="perfil"?110:65);?>px;width:<?=($quiencapaphoto=="perfil"?150:100);?>px;<?php if($imagen){?>opacity: 0; filter: alpha(opacity =       0);<?php }?>"><?=$textocapaphoto?></span>
	<form method="post" class="formImage" id="form<?=$quiencapaphoto?>"
		action="product/uploadImage" enctype="multipart/form-data">
		<input name="quien" value="<?=$quiencapaphoto?>" type="hidden" /> <?php
		?>
	</form>
	<div class="cerrar" title="Eliminar"></div>
	<div class="block" style='<?=($imagen?"display:block;":"")?>
		<?=($quiencapaphoto=="perfil"?"width:150px;height:150px;":"");?>'></div>
</div>
<script type="text/javascript">
		var swfu;
		$(function () {
			swfu = new SWFUpload({
				upload_url: "<?=base_url();?>product/uploadImage",
				post_params: {"quien": "<?php echo $quiencapaphoto ?>","llave":"<?$ss=$this->mysession->userdata("LVSESSION");print $ss["llave"];?>"},
				file_post_name : "imagen",
				file_size_limit : "4 MB",
				file_types : "*.jpg;*.gif;*.png",
				file_types_description : "Web Images Files",
				file_upload_limit : "0",
				file_queue_limit : "1",

				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,

				button_image_url : "<?=base_url();?>assets/images/html/upload_button.png",
				button_placeholder_id : "spanButtonPlaceholder<?php echo $quiencapaphoto ?>",
				button_width:<?=($quiencapaphoto=="perfil"?150:90);?>,
				button_height: <?=($quiencapaphoto=="perfil"?150:90);?>,
				button_text : '',
				button_text_style : '',
				button_text_top_padding: 0,
				button_text_left_padding: 0,
				button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
				button_cursor: SWFUpload.CURSOR.HAND,
				
				flash_url : "<?=base_url();?>assets/swf/swfupload.swf",

				custom_settings : {
					upload_target : "divFileProgressContainer"
				},
				quien:"<?php echo $quiencapaphoto ?>",
				debug: false
			});
		});
	</script>