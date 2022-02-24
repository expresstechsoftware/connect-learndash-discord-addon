<div class="error-log">
<?php
	$uuid     = get_option( 'ets_learndash_discord_uuid_file_name' );
	$filename = $uuid . LearnDash_Discord_Add_On_Logs::$log_file_name;
	$handle   = fopen( WP_CONTENT_DIR . '/' . $filename, 'a+' );
  if ( $handle ) {
    while ( ! feof( $handle ) ) {
        echo fgets( $handle ) . '<br />';
    }
    fclose( $handle );            
}
?>
</div>
<div class="learndash-clrbtndiv">
	<div class="form-group">
		<input type="button" class="ets-learndash-clrbtn ets-submit ets-bg-red" id="ets-learndash-clrbtn" name="learndash_clrbtn" value="Clear Logs !">
		<span class="clr-log spinner" ></span>
	</div>
	<div class="form-group">
		<input type="button" class="ets-submit ets-bg-green" value="Refresh" onClick="window.location.reload()">
	</div>
	<div class="form-group">
		<a href="<?php echo esc_attr( content_url('/') . $filename ); ?>" class="ets-submit ets-learndash-bg-download" download><?php echo __( 'Download', 'learndash-discord'  ); ?></a>
	</div>
	<div class="form-group">
            <a href="<?php echo get_admin_url('', 'tools.php') . '?page=action-scheduler&status=pending&s=learndash'; ?>" class="ets-submit ets-learndash-bg-scheduled-actions"><?php echo __( 'Scheduled Actions', 'learndash-discord'  ); ?></a>
	</div>    
</div>
