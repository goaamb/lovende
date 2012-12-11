<?php print "<?xml version='1.0' encoding='UTF-8'?>"?>
<urlset
	xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
	xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
	>
<url>
<loc><?=base_url()?></loc>
<changefreq>daily</changefreq>
<priority>1.00</priority>
</url>
<?php
if (isset ( $urls ) && count ( $urls ) > 0) {
	foreach ( $urls as $url ) {
		?><url>
<loc><?=$url["url"]?></loc>
<changefreq>daily</changefreq>
<priority>0.75</priority><?php
		if (isset ( $url ["images"] ) && is_array ( $url ["images"] ) && count ( $url ["images"] ) > 0) {
			foreach ($url ["images"] as $img) {
				?><image:image>
	<image:loc><?=$img?></image:loc>
</image:image><?php
			}
		}
		?></url><?php
	}
}
?>
</urlset>