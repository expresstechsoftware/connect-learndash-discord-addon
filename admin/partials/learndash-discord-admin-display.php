<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.expresstechsoftwares.com
 * @since      1.0.0
 *
 * @package    Learndash_Discord
 * @subpackage Learndash_Discord/admin/partials
 */
?>
<?php
if ( isset( $_GET['save_settings_msg'] ) ) {
?>
<div class="notice notice-success is-dismissible support-success-msg">
    <p><?php echo esc_html( $_GET['save_settings_msg'] ); ?></p>
</div>
<?php
}
?>
<h1><?php echo __( 'LearnDash Discord Add On Settings', 'learndash-discord' ); ?></h1>
<div id="learndash-discord-outer" class="skltbs-theme-light" data-skeletabs='{ "startIndex": 0 }'>
  <ul class="skltbs-tab-group">
  <li class="skltbs-tab-item">
		<button class="skltbs-tab" data-identity="settings" ><?php echo __( 'Application details', 'learndash-discord' ); ?><span class="initialtab spinner"></span></button>
  </li>

  </ul>
  <div class="skltbs-panel-group">
		<div id="ets_learndash_application_details" class="learndash-discord-tab-conetent skltbs-panel">
		<?php
			require_once LEARNDASH_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/pages/learndash_discord_application_details.php';
    ?>
		</div>
  </div>  
    

</div>