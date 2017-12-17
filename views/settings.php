<?php defined('ABSPATH') || die(); ?>
<div class="wrap">
	<h1><?=esc_html__('WW CyrToLat Settings', 'wwc2r'); ?></h1>

	<form action="options.php" method="post">
	<?php
	settings_fields('wwc2r');
	do_settings_sections('wwc2r');
	submit_button(__('Save settings', 'wwc2r'));
	?>
	</form>
</div>
