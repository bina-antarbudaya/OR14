		</div>
		<footer class="global-footer">
			<!-- Untuk bantuan, baca <strong><a href="<?php L(array('controller' => 'applicant', 'action' => 'guide')); ?>">panduan</a></strong>, mention/DM <strong>@afsbandung</strong> atau e-mail <strong>seleksi@binabudbdg.org</strong>. -->
			<!-- <address class="afs-partner"><img src="<?php L('/assets/images/AFS_Partner.png') ?>" alt="A Partner of AFS Intercultural Programs"></address> -->
		</footer>
		<?php if ($piwik_server = Helium::conf('piwik_server')): ?>

		<script type="text/javascript">
		  var _paq = _paq || [];
		  _paq.push(["trackPageView"]);
		  _paq.push(["enableLinkTracking"]);

		  (function() {
		    var u=(("https:" == document.location.protocol) ? "https" : "http") + "://<?php echo $piwik_server ?>/piwik/";
		    _paq.push(["setTrackerUrl", u+"piwik.php"]);
		    _paq.push(["setSiteId", "1"]);
		    var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
		    g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);
		  })();
		</script>
		<?php endif; ?>
	</body>

</html>