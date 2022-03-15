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
				<button class="skltbs-tab" data-identity="settings" ><?php echo __( 'Application Details', 'learndash-discord' ); ?><span class="initialtab spinner"></span></button>
				</li>
				<li class="skltbs-tab-item">
				<?php if ( learndash_discord_check_saved_settings_status() ): ?>
				<button class="skltbs-tab" data-identity="level-mapping" ><?php echo __( 'Role Mappings', 'learndash-discord' ); ?></button>
				<?php endif; ?>
				</li>
				<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="advanced" ><?php echo __( 'Advanced', 'learndash-discord' ); ?>	
				</button>
				</li>
				<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="appearance" ><?php echo __( 'Appearance', 'learndash-discord' ); ?>	
				</button>
				</li>                                
				<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="logs" ><?php echo __( 'Logs', 'learndash-discord' ); ?>	
				</button>
				</li>                                
			
                        </ul>
			<div class="skltbs-panel-group">
				<div id="ets_learndash_application_details" class="learndash-discord-tab-conetent skltbs-panel">
				<?php require_once LEARNDASH_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/pages/learndash_discord_application_details.php'; ?>
				</div>
				<?php if ( learndash_discord_check_saved_settings_status() ): ?>      
				<div id="ets_learndash_discord_role_mapping" class="learndash-discord-tab-conetent skltbs-panel">
					<?php require_once LEARNDASH_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/pages/learndash_discord_role_mapping.php'; ?>
				</div>
				<?php endif; ?>
				<div id='ets_learndash_discord_advanced' class="learndash-discord-tab-conetent skltbs-panel">
				<?php require_once LEARNDASH_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/pages/learndash_discord_advanced.php'; ?>
				</div>
				<div id='ets_learndash_discord_appearance' class="learndash-discord-tab-conetent skltbs-panel">
				<?php require_once LEARNDASH_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/pages/learndash_discord_appearance.php'; ?>
				</div>                            
				<div id='ets_learndash_discord_logs' class="learndash-discord-tab-conetent skltbs-panel">
				<?php require_once LEARNDASH_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/pages/learndash_discord_error_log.php'; ?>
				</div>                            
			</div>  
		</div>