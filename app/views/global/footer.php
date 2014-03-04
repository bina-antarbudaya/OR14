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
		<?php if ($tracking_code = Helium::conf('tracking_code')) echo $tracking_code ?>
	</body>

</html>