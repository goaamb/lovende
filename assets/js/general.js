$(function() {
	
	//editComment
	$('.editComment').click(function(){
		$(this).parents('div.comment').hide();
		$(this).parents('li').children('.edit-comment').show();
		return false;
	});
	//cancelEditComment
	$('.cancelEditComment').click(function(){
		$(this).parents('div.edit-comment').hide();
		$(this).parents('li').children('div.comment').show();
		return false;
	});

	//product-file gallery
	$('#productGallery').easySlider({
		auto: false,
		continuous: false,
		numeric: false,
		controlsBefore:	'<div class=\'controls\'>',
		controlsAfter:		'</div>',
		speed: 1000,
		pause: 10000
	});

	// ofertasInferiores:
	$('input.ofertasInferioresTrigger').change(function(){
		if($(this).is(':checked')) {
			$('#ofertasInferiores').show();
		} else {
			$('#ofertasInferiores').hide();
		}
	});

	// inicio line-category
	$('div.line-category a.trigger').click(function(){
		$(this).hide();
		$(this).siblings('div.cat').slideDown();
		return false;
	});

	$('div.line-category div.cat a').click(function(){
		var padre = $(this).parent('li').parent('ul').parent('div.cat').parent('div.line-category');
		padre.children('div.cat').hide();
		var category = $(this).data('value');
		padre.children('span.choice').text(category);
		padre.addClass('l-cat-selected');
		padre.next('div.postCat').show();
		padre.next().next('div.line-category').removeClass('hidden');
		return false;
	});

	$('a.reset-line-categories').click(function(){
		$('div.line-category').addClass('hidden');
		$('div.line-category').removeClass('l-cat-selected');
		$('div.line-category a.trigger').css('display', 'block');
		$('span.choice').text('');
		$('div.postCat').hide();
		$('div.line-category').first().removeClass('hidden');
		return false;
	});
	// fin line-category

	//checkboxes de tipo-precio
	$('input.tipo-precio').click(function(){
		var valor = $(this).val();
		$('div.tipo-precio-box').hide();
		$('#'+valor).show();
	});

	//consejos popups
	$('div.con-consejo .enfoque').focusin(function(){
		var consejo = $(this).data('consejo');
		var ancho = $(this).width()+12;
		var offset = $(this).offset();
		$('#'+consejo).show().css({'top': offset.top-3, 'left': offset.left+ancho+20});
	});
	$('div.con-consejo .enfoque').focusout(function(){
		$('.consejos').hide();
	});

	// registro-dual tamaños
	$('.registro-dual').each(function(){
		var w1 = $(this).children('input').width();
		var w2 = $(this).children('.o').width();
		$(this).width(w1+w2+250);
	});

});

jQuery.fn.actMenu = function(){
	$(this).on('click', function() {
		$this = $(this);
		el = $this.data('rel');
		bubble = $('#'+el);
		offset = $this.offset();
		w = $this.width();
		console.log(bubble.html());
		if(bubble.hasClass('hidden')) {
			$('.act-menu').addClass('hidden');
			bubble.css({top: offset.top - 2, left: offset.left + w + 10}).removeClass('hidden');
		} else {
			bubble.addClass('hidden');
		}
		return false;		
	})
	$('body').click(function(){
		$('.act-menu').addClass('hidden');
	});
}

jQuery.fn.actMenuB = function(){
	$(this).on('click', function() {
		$this = $(this);
		desp = $this.siblings('div.act-menu-b');
		offset = $this.offset();
		w = $this.width();
		//console.log(desp.html());
		if(desp.is(':hidden')) {
			$('.act-menu-b').hide();
			desp.css({top: offset.top - 2, left: offset.left + w + 10}).show();
		} else {
			$('.act-menu-b').hide();
			desp.hide();
		}
		return false;		
	})
	$('body').click(function(){
		$('.act-menu-b').hide();
	});
}