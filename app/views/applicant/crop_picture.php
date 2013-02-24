<?php
$this->require_css('imgareaselect/imgareaselect-animated');
// $this->require_css('applicant/crop_picture');
$this->print_header();
?>
<header class="page-header">
	<h2>Tahap 3/4</h2>
	<h1>Formulir Pendaftaran</h1>
</header>

<div class="cropper">
	<div class="row-fluid">
		<div class="span10" style="text-align: center">
			<img id="pic" src="<?php echo $picture->get_original_url() ?>" alt="">
		</div>
		<div class="span2">
			<form action="<?php L($this->params) ?>" method="POST">
				<input name="x" id="x" type="hidden">
				<input name="y" id="y" type="hidden">
				<input name="width" id="width" type="hidden">
				<input name="height" id="height" type="hidden">
				<button type="submit" class="btn btn-large btn-success btn-block">Simpan</button>
			</form>
			<p class="instruction">Lakukan <i>cropping</i> pada foto Adik dengan menekan tombol pada <i>mouse</i> dan menggesernya. <i>(click and drag)</i></p>
		</div>
	</div>
</div>
<?php
$this->require_js('jquery.imgareaselect.min');
$this->require_js('crop');
$this->print_footer();
?>