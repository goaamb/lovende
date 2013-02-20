<div class="col300">
	<div class="siguenos300">
		<h2><?=traducir("Síguenos en Facebook")?></h2>
		<p><?=traducir("Que no se te escape nada")?></p>
		<div class="fb clearfix">
			<a href="http://www.facebook.com/pages/Lovende/471203802892372"
				title="<?=traducir("Ir a Facebook de Lovende")?>" class="f-l mrl"><img
				src="assets/images/ico/ico-fb-29.png" alt="Facebook" /></a>
			<div id="fb-root"></div>
			<script>(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/es_ES/all.js#xfbml=1";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
			<div class="mts f-l">
				<div class="fb-like"
					data-href="http://www.facebook.com/pages/Lovende/471203802892372"
					data-send="false" data-layout="button_count" data-width="250"
					data-show-faces="false" data-font="arial"></div>
			</div>
		</div>
	</div>
	<div class="bannerIpad">
		<a href="home/modal/enviar-mail/mail/5" class="nmodal"
			title="<?=traducir("Preguntas frecuentes")?>"><?=traducir("¿Tienes alguna pregunta?")?></a>
	</div>
</div>
<!--col300-->
<div class="categoriesBox">
	<div class="cab">
		<h2 class="t-c">
			<?=traducir("Compra y vende por <strong>subasta</strong> o <strong>precio fijo</strong>")?>
		</h2>
	</div>
	<div class="cont clearfix">
		<?php
		$nc = floor ( count ( $categorias ) / 3 );
		?><ul class="col"><?php
		$count = 0;
		$max = 3 * $nc;
		foreach ( $categorias as $id => $c ) {
			$count ++;
			?><li><a href="<?=base_url()."?categoria=$id"?>"
				title="<?=$c["nombre"]?>"><?=$c["nombre"]?></a></li><?php
			if ($count < $max && $count % $nc == 0 && $count !== count ( $categorias )) {
				?></ul>
		<ul class="col"><?php
			}
		}
		?></ul>
	</div>
</div>
<!--categoriesBox-->
<div class="quickImageLinks">
	<ul>
		<li><a href="?categoria=1890" title="Ir a videojuegos">
				<div class="image">
					<img src="assets/images/html/quick-videojuegos.png"
						alt="Videojuego" />
				</div> Videojuegos
		</a></li>
		<li><a href="?categoria=1531" title="Ir a Monedas">
				<div class="image">
					<img src="assets/images/html/quick-monedas.png" alt="Monedas" />
				</div> Monedas
		</a></li>
		<li><a href="?categoria=993" title="Ir a Juguetes">
				<div class="image">
					<img src="assets/images/html/quick-barbie.png" alt="Barbie" />
				</div> Juguetes
		</a></li>
		<li><a href="?categoria=37" title="Ir a Antigüedades">
				<div class="image">
					<img src="assets/images/html/quick-silla.png" alt="Silla" />
				</div> Antigüedades
		</a></li>
		<li><a href="?categoria=105" title="Ir a Audio">
				<div class="image">
					<img src="assets/images/html/quick-audio.png" alt="Audio" />
				</div> Audio
		</a></li>
		<li><a href="?categoria=1765" title="Ir a Ropa mujer">
				<div class="image">
					<img src="assets/images/html/quick-ropa-mujer.png" alt="Vestido" />
				</div> Ropa mujer
		</a></li>
		<li><a href="?categoria=1734" title="Ir a Ropa hombre">
				<div class="image">
					<img src="assets/images/html/quick-ropa-hombre.png"
						alt="Camiseta polo" />
				</div> Ropa hombre
		</a></li>
		<li><a href="?categoria=869" title="Ir a Informática">
				<div class="image">
					<img src="assets/images/html/quick-pc.png" alt="ordenador PC" />
				</div> Informática
		</a></li>
		<li><a href="?categoria=1542" title="Ir a IPhone">
				<div class="image">
					<img src="assets/images/html/quick-iphone.png" alt="IPhone" />
				</div> IPhone
		</a></li>
		<li><a href="?categoria=1671" title="Ir a Relojes">
				<div class="image">
					<img src="assets/images/html/quick-reloj.png" alt="reloj" />
				</div> Relojes
		</a></li>
		<li><a href="?categoria=994" title="Ir a Figuras">
				<div class="image">
					<img src="assets/images/html/quick-figura.png" alt="Figura" />
				</div> Figuras
		</a></li>
		<li><a href="?categoria=1376" title="Ir a Cómics">
				<div class="image">
					<img src="assets/images/html/quick-comic.png" alt="Cómic" />
				</div> Cómics
		</a></li>
		<li><a href="?categoria=199" title="Ir a Perfumes">
				<div class="image">
					<img src="assets/images/html/quick-colonia.png" alt="Perfume" />
				</div> Perfumes
		</a></li>
		<li><a href="?categoria=1808" title="Ir a Sellos">
				<div class="image">
					<img src="assets/images/html/quick-sellos.png" alt="Sellos" />
				</div> Sellos
		</a></li>
		<li><a href="?categoria=851" title="Ir a Cámaras">
				<div class="image">
					<img src="assets/images/html/quick-camara.png" alt="Cámara" />
				</div> Cámaras
		</a></li>
		<li><a href="?categoria=1793" title="Ir a Bolsos">
				<div class="image">
					<img src="assets/images/html/quick-bolso.png" alt="Bolso" />
				</div> Bolsos
		</a></li>
		<li><a href="?categoria=1842" title="Ir a Coches">
				<div class="image">
					<img src="assets/images/html/quick-coche.png" alt="Coche" />
				</div> Coches
		</a></li>
		<li><a href="?categoria=1864" title="Ir a Motos">
				<div class="image">
					<img src="assets/images/html/quick-moto.png" alt="Moto" />
				</div> Motos
		</a></li>
		<li><a href="?categoria=146" title="Ir a Televisores">
				<div class="image">
					<img src="assets/images/html/quick-tv.png" alt="TV" />
				</div> Televisores
		</a></li>
		<li><a href="?categoria=1671" title="Ir a Joyas">
				<div class="image">
					<img src="assets/images/html/quick-joyas.png" alt="Joyas" />
				</div> Joyas
		</a></li>
	</ul>
</div>
<!--quickImageLinks-->

<span class="separator15"></span>

<div class="clearfix d-b">

	<div class="testimoniosHome">
	<?=traducir ( '<div class="images">
			<img src="assets/images/html/foto-chica.png"
				alt="Foto de Ana Giménez" /> <img
				src="assets/images/html/foto-chico.png" alt="Foto de Joel Hughs" />
		</div>
		<div class="cont">
			<div class="cab">
				<h3>Testimonios en Lovende</h3>
				<p>Lee las opiniones de nuestros usuarios</p>
			</div>
			<div class="testimonio">
				<em>"Vender aquí es más rentable que en otras webs"</em>
				<p class="who">Ana Giménez, Electricalia.</p>
			</div>
			<div class="testimonio">
				<em>“He puesto a la venta mis 7000 libros sin pagar nada” </em>
				<p class="who">Joel Hughs, Libros Isis.</p>
			</div>
		</div>
		<p class="readMore">
			<a href="#" title="Leer más testimonios">leer más</a>
		</p>' );?>
	</div>
	<!--testimoniosHome-->

	<div class="noLoTires">
	<?=traducir ( '
		<div class="cab">
			<h3>
				No lo tires<br /> <strong>¡VÉNDELO!</strong>
			</h3>
			<p>Nosotros te explicamos cómo.</p>
		</div>
		<p class="t-c">
			<img src="assets/images/html/bambas.png" alt="Zapatillas" />
		</p>
		<p class="readMore">
			<a href="#" title="Más información">leer más</a>
		</p>' );?>
	</div>
	<!--noLoTires-->

	<div class="bannerVendedor">
	<?=traducir ( '
		<div class="cab">
			<h3>¿ERES VENDEDOR PROFESIONAL?</h3>
			<p>Descubre las ventajas de tener tu tienda en Lovende</p>
		</div>
		<p class="readMore">
			<a href="#" title="Conoce las ventajas de Lovende">leer más</a>
		</p>' );?>
	</div>
	<!--bannerVendedor-->

</div>
<div class="carruselHome">
	<h3><?=traducir("Artículos que pueden interesarte")?></h3>
	<ul id="carruselHome">
	<?php
	foreach ( $articulos as $articulo ) {
		if ($articulo->usuario) {
			$imagen = array_shift ( explode ( ",", $articulo->foto ) );
			$imagen = imagenArticulo ( $articulo->usuario, $imagen, "thumb" );
			if ($imagen) {
				list ( $w, $h ) = getimagesize ( BASEPATH . "../$imagen" );
				$nh = 95;
				$nw = $nh * $w / $h;
				$furl = "product/" . $articulo->id . "-" . normalizarTexto ( $articulo->titulo );
				?><li><div class="image">
				<a href="<?=$furl?>" title="<?=$articulo->titulo?>"> <img src="<?=$imagen?>" alt="<?=$articulo->titulo?>"
					style="width:<?=$nw?>px;height:<?=$nh?>px;" /></a>
			</div>
			<h4>
				<a href="<?=$furl?>" title="<?=traducir("Ver producto")?>"
					style="height: 2.5em; display: inline-block; overflow: hidden;"><?=$articulo->titulo?></a>
			</h4>
			<p class="price"><?=formato_moneda($articulo->tipo=="Fijo" || $articulo->tipo=="Cantidad"?$articulo->precio:$articulo->mayorPuja)." €"?></p></li><?php
			}
		}
	}
	?>
	</ul>
</div>
<span class="separator"></span>

<article>
<?=traducir ( '
	<header>
		<h3>Subastas de arte, coleccionismo y tecnología: Lovende.es, la web
			donde vender y comprar de todo</h3>
	</header>
	<h4>Encuentra lo que buscas en Lovende, la web donde tiendas y
		particulares compran y venden antigüedades, arte y objetos de
		coleccionismo</h4>
	<p>¿Quieres comprarte un libro descatalogado?, ¿buscas un DVD edición
		especial francesa de una película de culto?, ¿necesitas zapatos de
		vestir para una fiesta? A diario cientos de miles de usuarios de todo
		el mundo venden estos artículos en Lovende para que los puedas comprar
		mediante subasta o precio fijo. Podrás encontrar desde artículos
		nuevos a estrenar hasta artículos de segunda mano y antigüedades de
		coleccionismo.</p>
	<h4>Vende lo que ya no quieres y gana un sobresueldo a fin de mes</h4>
	<p>
		Lovende no está pensado únicamente para vendedores profesionales: si
		tienes algún artículo que ya no necesitas o no utilizas, ¡no lo
		tires!, ponlo en subasta y en unos días te dará dinero. Crear subastas
		o anuncios de precio fijo en Lovende es fácil y gratuito, únicamente
		necesitas un par de buenas fotos y una descripción del artículo que
		quieres vender y en tan sólo unos minutos tendrás tu primera subasta
		en marcha. Una vez que se realice la venta y confirmes haber recibido
		el dinero, Lovende cobrará una pequeña comisión sobre el importe de la
		venta. Ya que <a href="product/nuevo" title="Publica tu anuncio gratis">poner
			anuncios es gratis</a> no tienes nada que perder y sí mucho dinero
		que ganar, ¡anímate y pon en marcha tu primera subasta, verás como
		repites!
	</p>
	<h4>¿Qué artículos puedo comprar y vender en Lovende?</h4>
	<p>Prácticamente todo lo que sea legal comprar en tu país, por ejemplo
		Pinturas, Joyas, Relojes, Monedas, Bronces, Esculturas, Marfil, Arte
		Oriental, Antigüedades, Plata y Objetos de Plata, CDs, DVDs, Blu-rays,
		Juguetes, Modelismo, Coches, Motos, Playstation 3, Wii-U, PSP, Xbox
		360, Ordenadores, Iphone, Samsung, Portátiles, Cámaras digitales,
		Móviles, Gps, Mp3, Coches, Motos, Ropa, Nike, Bolsos, Equipamiento
		deportivo, Gafas de sol, Videojuegos, Suplementos alimenticios y
		Vitaminas, Colonias, Relojes de lujo, Postales, Figuras y Militaria
		entre muchos otros.</p>
	<h4>Ahorra comprando</h4>
	<p>Comprar en internet ofrece muchas ventajas: se eliminan los
		intermediarios y los gastos de alquiler de almacenes, lo que permite
		que los vendedores puedan ofrecer sus artículos más baratos. También
		podrás poner en seguimiento todas las subastas que quieras y nuestro
		sistema de avisos te enviará un email siempre que una de tus subastas
		en seguimiento esté a punto de finalizar o cuando te sobrepujen, de
		esta forma no pasarás por alto la oportunidad de conseguir algo que
		quieres a precio de ganga.</p>
	<h4>Gana más vendiendo</h4>
	<p>En Lovende los vendedores tienen comisiones mucho más bajas que en
		otras webs similares lo que permite que sus precios aquí sean más
		competitivos, eso repercute en un aumento de las ventas y a que la
		comunidad en Lovende sea cada vez mayor, aumentando los compradores
		potenciales y las posibilidades de vender a buen precio. Tu tienda en
		Lovende es gratuita y todos tus anuncios que finalicen sin venderse se
		volverán a poner automáticamente en venta y aparecerán en la portada
		de la web, ¡se acabó tener que estar poniendo los mismos anuncios una
		y otra vez para ganar relevancia y visibilidad!</p>
	<h4>Ante todo seguridad</h4>
	<p>En Lovende nos tomamos muy en serio la seguridad de nuestros
		clientes, aquí podrás comprar y vender artículos nuevos y de segunda
		mano con la mayor seguridad que internet puede ofrecer: Lovende cuenta
		con un sistema de reputación que protege tanto al comprador como al
		vendedor (los compradoes también pueden recibir votos negativos) y un
		sistema personalizado de reclamaciones, denuncias y devoluciones;
		además tenemos integradas en nuestro sistema las pasarelas de pago más
		seguras de tarjetas de crédito, transferencias bancarias y Paypal.</p>
	<h4>¿Tienes alguna duda?</h4>
	<p>Nuestro Servicio de Atención al Cliente estará encantado de
		responder a tus consultas, no dudes en ponerte en contacto con
		nosotros a través del formulario de contacto y te responderemos con la
		mayor brevedad posible. Si tienes una tienda online podemos ayudarte
		en la importación automática de todo tu catálogo, de esta forma podrás
		tener tu tienda en Lovende en marcha y facturando desde el primer día.</p>' );?>
</article>

<div class="clearfix d-b">
	<div class="newsletterBox">
		<h3><?=traducir('Lovende Newsletter');?></h3>
		<div class="formBox">
			<form action="" method="post">
				<input name="__accion" type="hidden" value="newsletter" /> <input
					name="destino" type="hidden" value="" />
				<p>
					<input type="text"
						placeholder="<?=traducir('Introduce tu Email');?>" />
				</p>
				<p>
					<button onclick="this.form.destino.value='profesional';"><?=traducir('Profesionales');?></button>
					<button onclick="this.form.destino.value='particulares';"><?=traducir('Particulares');?></button>
				</p>
			</form>
		</div>
		<div class="textBox">
		<?=traducir ( '<h4>Recibe ofertas y promociones por email</h4>
			<p>Tus datos no serán revelados a terceros. Puedes darte de baja en
				cualquier momento.</p>' );?>
		</div>
	</div>
	<!--newsletterBox-->

	<div class="formasDePagoBox">
		<h3><?=traducir('Formas de pago');?></h3>
		<img src="assets/images/html/formas-de-pago.png"
			alt="<?=traducir('visa, mastercard, paypal...');?>" />
	</div>
</div>

<span class="separator"></span>