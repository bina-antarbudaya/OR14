			</div>
		</div>

		<footer class="global-footer">
		</footer>

		<!-- Load JS -->
	    <script src="//ajax.aspnetcdn.com/ajax/jQuery/jquery-1.11.0.min.js"></script>
	    <script src="//ajax.aspnetcdn.com/ajax/jquery.migrate/jquery-migrate-1.2.1.min.js"></script>
	   	<script src="<?php L('assets/js/bootstrap.min.js') ?>"></script>
		<?php foreach ($this->additional_js as $js): ?>

	 	<script src="<?php L('assets/js/' . $js . '.js') ?>"></script>
		<?php endforeach; ?>
		<?php if ($piwik_server = Helium::conf('piwik_server')): ?>

		<script type="text/javascript">
			var _paq = _paq || [];
			_paq.push(["trackPageView"]);
			_paq.push(["enableLinkTracking"]);

			(function() {
				var u=(("https:" == document.location.protocol) ? "https" : "http") + "://<?php echo $piwik_server ?>/piwik/";
				_paq.push(["setTrackerUrl", u+"piwik.php"]);
				_paq.push(["setSiteId", "1"]);
				<?php if ($this->is_logged_in()): ?>

				_paq.push(['setCustomVariable', 1, "LoggedIn", true, "visit"]);
				_paq.push(['setCustomVariable', 2, "Username", "<?php echo $this->session->user->username ?>", "visit"]);
				<?php if ($applicant = $this->session->user->applicant): ?>

				_paq.push(['setCustomVariable', 3, "Chapter", "<?php echo $applicant->chapter->chapter_code ?>", "visit"]);
				_paq.push(['setCustomVariable', 4, "ApplicantId", "<?php echo $applicant->id ?>", "visit"]);
				_paq.push(['setCustomVariable', 5, "ApplicantId", "<?php echo $applicant->sanitized_full_name ?>", "visit"]);
				<?php endif; ?>
				<?php else: ?>

				_paq.push(['setCustomVariable', 1, "LoggedIn", false, "visit"]);
				<?php endif; ?>

				var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
				g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);
			})();
		</script>
		<?php endif; ?>
	</body>

</html>