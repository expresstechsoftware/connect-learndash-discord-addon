jQuery(function($){
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
//        console.log(etsLearnDashParams);
        if (etsLearnDashParams.is_admin) {
            $('#ets_learndash_discord_redirect_url').select2({});
            
		$.ajax({
			type: "POST",
			dataType: "JSON",
			url: etsLearnDashParams.admin_ajax,
			data: { 'action': 'ets_learndash_discord_load_discord_roles', 'ets_learndash_discord_nonce': etsLearnDashParams.ets_learndash_discord_nonce },
			beforeSend: function () {
				$(".learndash-discord-roles .spinner").addClass("is-active");
				$(".initialtab.spinner").addClass("is-active");
                                
			},
			success: function (response) {

				if (response != null && response.hasOwnProperty('code') && response.code == 50001 && response.message == 'Missing Access') {
					$(".learndash-btn-connect-to-bot").show();
				} else if (response == null || response.message == '401: Unauthorized' || response.hasOwnProperty('code') || response == 0) {
					$("#learndash-connect-discord-bot").show().html("Error: Please check all details are correct").addClass('error-bk');
				} else {
					if ($('.ets-tabs button[data-identity="level-mapping"]').length) {
						$('.ets-tabs button[data-identity="level-mapping"]').show();
					}
					$("#learndash-connect-discord-bot").show().html("Bot Connected <i class='fab fa-discord'></i>").addClass('not-active');

					var activeTab = localStorage.getItem('activeTab');
					if ($('.ets-tabs button[data-identity="level-mapping"]').length == 0 && activeTab == 'level-mapping') {
						$('.ets-tabs button[data-identity="settings"]').trigger('click');
					}
					$.each(response, function (key, val) {
                                            
						var isbot = false;
						if (val.hasOwnProperty('tags')) {
							if (val.tags.hasOwnProperty('bot_id')) {
								isbot = true;
							}
						}
                                                

						if (key != 'previous_mapping' && isbot == false && val.name != '@everyone') {
							$('.learndash-discord-roles').append('<div class="makeMeDraggable" style="background-color:#'+val.color.toString(16)+'" data-learndash_role_id="' + val.id + '" >' + val.name + '</div>');
							$('#learndash-defaultRole').append('<option value="' + val.id + '" >' + val.name + '</option>');
							makeDrag($('.makeMeDraggable'));
						}
					});
					var defaultRole = $('#selected_default_role').val();
					if (defaultRole) {
						$('#learndash-defaultRole option[value=' + defaultRole + ']').prop('selected', true);
					}

					if (response.previous_mapping) {
						var mapjson = response.previous_mapping;
					} else {
						var mapjson = localStorage.getItem('learndash_mappingjson');
					}

					$("#learndash_maaping_json_val").html(mapjson);
					$.each(JSON.parse(mapjson), function (key, val) {
							var arrayofkey = key.split('id_');
							var preclone = $('*[data-learndash_role_id="' + val + '"]').clone();
							
							if(preclone.length>1){
								preclone.slice(1).hide();
							}
							if (jQuery('*[data-learndash_level_id="' + arrayofkey[1] + '"]').find('*[data-learndash_role_id="' + val + '"]').length == 0) {
								$('*[data-learndash_level_id="' + arrayofkey[1] + '"]').append(preclone).attr('data-drop-learndash_role_id', val).find('span').css({ 'order': '2' });
							}
							if ($('*[data-learndash_level_id="' + arrayofkey[1] + '"]').find('.makeMeDraggable').length >= 1) {
								$('*[data-learndash_level_id="' + arrayofkey[1] + '"]').droppable("destroy");
							}
							preclone.css({ 'width': '100%', 'left': '0', 'top': '0', 'margin-bottom': '0px', 'order': '1' }).attr('data-learndash_level_id', arrayofkey[1]);
							makeDrag(preclone);
							
						});
				}

			},
			error: function (response ,  textStatus, errorThrown) {
				$("#learndash-connect-discord-bot").show().html("Error: Please check all details are correct").addClass('error-bk');
				console.log( textStatus + " :  " + response.status + " : " + errorThrown );
                                
			},
			complete: function () {
				$(".learndash-discord-roles .spinner").removeClass("is-active").css({ "float": "right" });
				$("#skeletabsTab1 .spinner").removeClass("is-active").css({ "float": "right", "display": "none" });
			}
		});            
        }

});
if ( etsLearnDashParams.is_admin ) {
	/*Tab options*/
	jQuery.skeletabs.setDefaults({
		keyboard: false
	});
}
