			</div>
		</div>
		
		<footer class="global-footer">
		</footer>
		
		
		<!-- Load JS -->
	    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	   	<script src="<?php L('assets/js/bootstrap.min.js') ?>"></script>
		<?php foreach ($this->additional_js as $js): ?>
	 	<script src="<?php L('assets/js/' . $js . '.js') ?>"></script>
		<?php endforeach; ?>
	</body>

</html>