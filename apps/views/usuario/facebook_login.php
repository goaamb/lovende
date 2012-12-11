<div id="fb-root"></div>
<script>
				        window.fbAsyncInit = function() {
				          FB.init({
				            appId      : '174885255966990',
				            status     : true, 
				            cookie     : true,
				            xfbml      : false
				          });
				          $("#facebook-login").click(function(){
	                        	window.open("<?=$facebook_url;?>","_self");
	                            return false;
	               				});
				        };
				        
				        (function(d){
					        
				           var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
				           js = d.createElement('script'); js.id = id; js.async = true;
				           js.src = "//connect.facebook.net/en_US/all.js";
				           d.getElementsByTagName('head')[0].appendChild(js);
				         }(document));
				      </script>
<div class="fb-login-button" id="facebook-login">
	<a class="fb_button fb_button_medium"><span class="fb_button_text"><?=traducir ( "Entrar
			con Facebook" );?></span></a>
</div>