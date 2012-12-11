</div>
<footer class="footer">
	<div class="wrapper">
		<div class="f-l">
			<p>
				<strong>Lovende © 2012</strong> | <a
					href="<?=current_url();?>?lang=<?php
					if (isset ( $this->idioma ) && $this->idioma->language->codigo == "es-ES") {
						print "en-US";
					} else {
						print "es-ES";
					}
					?>"
					title="<?=traducir("Versión en Ingles");?>"><?php
					if (isset ( $this->idioma ) && $this->idioma->language->codigo == "es-ES") {
						print traducir ( "Ingles" );
					} else {
						print traducir ( "Español" );
					}
					?></a>
			</p>
		</div>
		<div class="f-r">
			<p>
				<?=traducir("Síguenos en");?> <a
					href="http://www.facebook.com/pages/Lovende/471203802892372"
					title="<?=traducir("Ir al Facebook de Lovende");?>"><img
					src="assets/images/ico/ico-fb-24.png" alt="Facebook" /></a> <a
					href="https://twitter.com/Lovende_es"
					title="<?=traducir("Ir al Twitter de Lovende");?>"><img
					src="assets/images/ico/ico-tw-24.png" alt="Twiter" /></a> <a
					href="https://plus.google.com/b/108545380659244060869/108545380659244060869/posts"
					title="<?=traducir("Ir al Google+ de Lovende");?>"><img
					src="assets/images/ico/ico-gplus.png" alt="Twiter" /></a>
			</p>
		</div>
		<div class="central">
			<p>
				<a href="benefit" title="Ver ¿por qué Lovende?"><?=traducir("Sobre Lovende");?></a>
				| <a href="fees" title="Ver tarifas de vendedor"><?=traducir("Tarifas");?></a>
				| <a href="terms" title="Ver términos de uso"><?=traducir("Términos de uso");?></a>
				| <a href="privacy" title="Ver privacidad"><?=traducir("Privacidad");?></a>
				| <a href="http://www.lovende.es/blog/"
					title="<?=traducir("Ir al blog de Lovende");?>"><?=traducir("Blog");?></a>
			</p>
		</div>
	</div>
</footer>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-34423273-1']);
  _gaq.push(['_trackPageview']);
  setTimeout('_gaq.push([\'_trackEvent\', \'NoBounce\', \'Over 10 seconds\'])',10000);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>
</html>