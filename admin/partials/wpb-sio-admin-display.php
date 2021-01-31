<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://wpbuild.ru
 * @since      1.0.0
 *
 * @package    Wpb_Sio
 * @subpackage Wpb_Sio/admin/partials
 */
		
		// текущие состояние опций
		//$options = $this->settings;



?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <form action="<?php echo admin_url('options.php') ?>" method="post" target="_self">
        <?php
        settings_errors();
        settings_fields( $this->plugin_name );


        submit_button();
        ?>
    </form>
</div>
