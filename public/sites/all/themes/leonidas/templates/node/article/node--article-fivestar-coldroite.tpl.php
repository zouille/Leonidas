<div id="our-heart-tpl">
	<?php print culturebox_site_l($node->title, "node/$node->nid"); ?>
	<br>
	<div id="our-heart-picture">
		<?php if($field_rate['und'][0]['count'] > 0 ):?>
			<?php if($field_rate['und'][0]['average'] == 20):?>
				<img src="<?php echo $GLOBALS['base_path'] . CULTUREBOX_FIVESTAR_MODULE_PATH; ?>/image/etoiles_1.png" alt="1 étoile">
			<?php elseif($field_rate['und'][0]['average'] == 40):?>
				<img src="<?php echo $GLOBALS['base_path'] . CULTUREBOX_FIVESTAR_MODULE_PATH; ?>/image/etoiles_2.png" alt="2 étoiles">
			<?php elseif($field_rate['und'][0]['average'] == 60):?>
				<img src="<?php echo $GLOBALS['base_path'] . CULTUREBOX_FIVESTAR_MODULE_PATH; ?>/image/etoiles_3.png" alt="3 étoiles">
			<?php elseif($field_rate['und'][0]['average'] == 80):?>
				<img src="<?php echo $GLOBALS['base_path'] . CULTUREBOX_FIVESTAR_MODULE_PATH; ?>/image/etoiles_4.png" alt="4 étoiles">
			<?php elseif($field_rate['und'][0]['average'] == 100):?>
				<img src="<?php echo $GLOBALS['base_path'] . CULTUREBOX_FIVESTAR_MODULE_PATH; ?>/image/etoiles_5.png" alt="5 étoiles">
			<?php endif;?>
		<?php endif;?>
	</div>
</div>