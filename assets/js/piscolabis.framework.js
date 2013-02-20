$(document).ready(function() {
	
	//formulario
	var emailValido = {
	       email: function(el) {return /^[A-Za-z][A-Za-z0-9_]*@[A-Za-z0-9_]+\.[A-Za-z0-9_.]+[A-za-z]$/.test($(el).val());}
	}
	
	$("#userEmail").blur(function(){
		var v = $(this).attr("value");
		if (v) {
			$(this).removeClass("error");
			$($($(this).parents("p")[0]).children("span")[0]).removeClass("error").addClass("ok").text("Correcto");
			var er_email = /^(.+\@.+\..+)$/
			if(!er_email.test(v)) {
				$(this).addClass("error");
				$($($(this).parents("p")[0]).children("span")[0]).removeClass("ok").addClass("error").text("Email inválido");
			} else 	{
				$(this).removeClass("error");
				$($($(this).parents("p")[0]).children("span")[0]).removeClass("error").addClass("ok").text("Correcto");
			}
		}
		else {
			$(this).addClass("error");
			$($($(this).parents("p")[0]).children("span")[0]).removeClass("ok").addClass("error");
		}
	});
	
	//closeThis y openThat
	$('.closeThis').click(
		function()
		{
			var e = $(this).data('close');
			$('#'+e).slideUp('fast');
			return false;
		}
	);
	$('.openThat').click(
		function()
		{
			var e = $(this).data('open');
			$('#'+e).slideDown('fast');
			return false;
		}
	);
	
	//textareas quita texto y pon boton
	$('.magical').each(function(){
	   // tomamos el valor actual del input
	   var currentValue = $(this).val();
	   // en el focus() comparamos si es el mismo por defecto, y si es asi lo vaciamos
	   $(this).focus(function(){
	      if( $(this).val() == currentValue ) {
	         $(this).val('');
	      };
	      $(this).css('color', '#333');
	      boton = $(this).siblings('p'); 
	      boton.removeClass('hidden');
	   });
	   // en el blur, si el usuario dejo el value vacio, lo volvemos a restablecer
	   $(this).blur(function(){
	      if( $(this).val() == '' ) {
	         $(this).val(currentValue);
	      };
	       $(this).css('color', '#777');
	       $(this).siblings('p').addClass('hidden');
	   });
	});
	
		
	//formularios label encima del input
	$('.label-on-field .texto').focus(
		function(){
           $(this).siblings('label').css('opacity', '0.4')
        }
  	);
    $('.label-on-field .texto').blur(
        function(){
           if ($(this).val() == '') {
               $(this).siblings('label').css('opacity', '1')
           }
        }
    );
    $('.label-on-field .texto').keyup(
        function(){
           if ($(this).val() == '') {
               $(this).siblings('label').css('display', 'block')
           }
           else {
               $(this).siblings('label').css('display', 'none')
           }
        }
    );
    // o encima del label:
    $('.label-on-field label').click(
		function(){
           $(this).css('opacity', '0.4')
           $(this).siblings('.texto').focus();
        }
  	);

	

});