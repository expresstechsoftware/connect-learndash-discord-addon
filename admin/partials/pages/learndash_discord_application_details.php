<?php
$ets_learndash_discord_client_id     = sanitize_text_field( trim( get_option( 'ets_learndash_discord_client_id' ) ) );
$ets_learndash_discord_client_secret = sanitize_text_field( trim( get_option( 'ets_learndash_discord_client_secret' ) ) );
$ets_learndash_discord_bot_token     = sanitize_text_field( trim( get_option( 'ets_learndash_discord_bot_token' ) ) );
$ets_learndash_discord_redirect_url  = sanitize_text_field( trim( get_option( 'ets_learndash_discord_redirect_url' ) ) );
$ets_learndash_discord_redirect_page_id  = sanitize_text_field( trim( get_option( 'ets_learndash_discord_redirect_page_id' ) ) );
$ets_learndash_discord_server_id     = sanitize_text_field( trim( get_option( 'ets_learndash_discord_server_id' ) ) );
$ets_learndash_discord_connected_bot_name     = sanitize_text_field( trim( get_option( 'ets_learndash_discord_connected_bot_name' ) ) );
?>
<form method="post" action="<?php echo esc_url( get_site_url() ) . '/wp-admin/admin-post.php'; ?>">
  <input type="hidden" name="action" value="learndash_discord_application_settings">
  <input type="hidden" name="current_url" value="<?php echo esc_url ( ets_learndash_discord_get_current_screen_url() )?>">   
	<?php wp_nonce_field( 'save_learndash_discord_general_settings', 'ets_learndash_discord_save_settings' ); ?>
  <div class="ets-input-group">
	<label><?php esc_html_e( 'Client ID', 'connect-learndash-discord-addon' ); ?> :</label>
	<input type="text" class="ets-input" name="ets_learndash_discord_client_id" value="<?php
	if ( isset( $ets_learndash_discord_client_id ) ) {
		echo esc_attr( $ets_learndash_discord_client_id ); }
	?>" required placeholder="Discord Client ID">
  </div>
	<div class="ets-input-group">
	  <label><?php esc_html_e( 'Client Secret', 'connect-learndash-discord-addon' ); ?> :</label>
		<input type="password" class="ets-input" name="ets_learndash_discord_client_secret" value="<?php
		if ( isset( $ets_learndash_discord_client_secret ) ) {
			echo esc_attr( $ets_learndash_discord_client_secret ); }
    ?>" required placeholder="Discord Client Secret">
	</div>
	<div class="ets-input-group">
            <label><?php esc_html_e( 'Redirect URL', 'connect-learndash-discord-addon' ); ?> :</label>
            <p class="redirect-url"><b><?php echo esc_url( $ets_learndash_discord_redirect_url ) ?></b></p>
		<select class= "ets-input" id="ets_learndash_discord_redirect_url" name="ets_learndash_discord_redirect_url" style="max-width: 100%" required>
		<?php _e( ets_learndash_discord_pages_list( wp_kses( $ets_learndash_discord_redirect_page_id , array ( 'option' => array( 'data-page-url' => array() ) ) ) ) ) ; ?>
		</select>
		<p class="description"><?php esc_html_e( 'Registered discord app redirect url', 'connect-learndash-discord-addon' ); ?><span class="spinner"></span></p>
                <p class="description ets-discord-update-message"><?php _e( sprintf( wp_kses( __( 'Redirect URL updated, kindly add/update the same in your discord.com application link <a href="https://discord.com/developers/applications/%s/oauth2/general">https://discord.com/developers</a>', 'connect-learndash-discord-addon' ) , array( 'a' => array( 'href' => array() ) ) ) ,  $ets_learndash_discord_client_id ) ); ?></p>                
	</div>
	<div class="ets-input-group">
            <label><?php esc_html_e( 'Admin Redirect URL Connect to bot', 'connect-learndash-discord-addon' ); ?> :</label>
            <input type="text" class="ets-input" value="<?php echo get_admin_url('', 'admin.php').'?page=connect-learndash-discord-addon&via=learndash-discord-bot'; ?>" readonly required />
        </div>
	<div class="ets-input-group">
            <?php
            if ( isset( $ets_learndash_discord_connected_bot_name ) && !empty( $ets_learndash_discord_connected_bot_name ) ){
                _e( sprintf( wp_kses ( __( '<p class="description">Make sure the Bot %1$s <span class="discord-bot"><b>BOT</b></span>have the high priority than the roles it has to manage. Open <a href="https://discord.com/channels/%2$s">Discord Server</a></p>', 'connect-learndash-discord-addon'), array( 'p' => array( 'class' => array() ) , 'a' => array( 'href' => array() ), 'span' => array( 'class' => array() ), 'b' => array() ) ), $ets_learndash_discord_connected_bot_name, $ets_learndash_discord_server_id ) );
            }
            ?>
	  <label><?php esc_html_e( 'Bot Token', 'connect-learndash-discord-addon' ); ?> :</label>
		<input type="password" class="ets-input" name="ets_learndash_discord_bot_token" value="<?php
		if ( isset( $ets_learndash_discord_bot_token ) ) {
			echo esc_attr( $ets_learndash_discord_bot_token ); }
		?>" required placeholder="Discord Bot Token">
	</div>
	<div class="ets-input-group">
	  <label><?php esc_html_e( 'Server ID', 'connect-learndash-discord-addon' ); ?> :</label>
		<input type="text" class="ets-input" name="ets_learndash_discord_server_id"
		placeholder="Discord Server Id" value="<?php
		if ( isset( $ets_learndash_discord_server_id ) ) {
			echo esc_attr( $ets_learndash_discord_server_id ); }
		?>" required>
	</div>
	<?php if ( empty( $ets_learndash_discord_client_id ) || empty( $ets_learndash_discord_client_secret ) || empty( $ets_learndash_discord_bot_token ) || empty( $ets_learndash_discord_redirect_url ) || empty( $ets_learndash_discord_server_id ) ) { ?>
	  <p class="ets-danger-text description">
		<?php esc_html_e( 'Please save your form', 'connect-learndash-discord-addon' ); ?>
	  </p>
	<?php } ?>
	<p>
	  <button type="submit" name="submit" value="ets_discord_submit" class="ets-submit ets-bg-green">
		<?php esc_html_e( 'Save Settings', 'connect-learndash-discord-addon' ); ?>
	  </button>
	  <?php if ( get_option( 'ets_learndash_discord_client_id' ) ) : ?>
	  <?php
			$params                    = array(
				'client_id'     => sanitize_text_field( trim( get_option( 'ets_learndash_discord_client_id' ) ) ),
				'redirect_uri'  => get_admin_url('', 'admin.php').'?page=connect-learndash-discord-addon&via=learndash-discord-bot',
				'response_type' => 'code',
				'scope'         => 'bot',
				'permissions' => LEARNDASH_DISCORD_BOT_PERMISSIONS,
				'guild_id'    => sanitize_text_field( trim( get_option( 'ets_learndash_discord_server_id' ) ) ),
				'disable_guild_select' => 'true'
				);
			$discord_authorise_api_url = LEARNDASH_DISCORD_API_URL . 'oauth2/authorize?' . http_build_query( $params );            
            
            ?>
            <a href="<?php echo esc_url(  $discord_authorise_api_url )?>" class="ets-btn learndash-btn-connect-to-bot" id="learndash-connect-discord-bot"><?php esc_html_e( 'Connect your Bot', 'connect-learndash-discord-addon' ); ?> <i class='fab fa-discord'></i></a>
	  <?php endif; ?>
	</p>
</form>
