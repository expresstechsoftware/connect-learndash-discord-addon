jQuery(function($){
//	'use strict';

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

		/*Load all roles from discord server*/
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

					$("#ets_learndash_mapping_json_val").html(mapjson);
					$.each(JSON.parse(mapjson), function (key, val) {
						var arrayofkey = key.split('id_');
						var preclone = $('*[data-learndash_role_id="' + val + '"]').clone();
						if(preclone.length>1){
							preclone.slice(1).hide();
						}
						
						if (jQuery('*[data-learndash_course_id="' + arrayofkey[1] + '"]').find('*[data-learndash_role_id="' + val + '"]').length == 0) {
							$('*[data-learndash_course_id="' + arrayofkey[1] + '"]').append(preclone).attr('data-drop-learndash_role_id', val).find('span').css({ 'order': '2' });
						}
						if ($('*[data-learndash_course_id="' + arrayofkey[1] + '"]').find('.makeMeDraggable').length >= 1) {
							$('*[data-learndash_course_id="' + arrayofkey[1] + '"]').droppable("destroy");
						}

						preclone.css({ 'width': '100%', 'left': '0', 'top': '0', 'margin-bottom': '0px', 'order': '1' }).attr('data-learndash_course_id', arrayofkey[1]);
						makeDrag(preclone);
					});
				}

			},
			error: function (response) {
				$("#learndash-connect-discord-bot").show().html("Error: Please check all details are correct").addClass('error-bk');
				console.error(response);
			},
			complete: function () {
				$(".learndash-discord-roles .spinner").removeClass("is-active").css({ "float": "right" });
				$("#skeletabsTab1 .spinner").removeClass("is-active").css({ "float": "right", "display": "none" });
			}
		});
                
		/*Clear log log call-back*/
		$('#ets-learndash-clrbtn').click(function (e) {
			e.preventDefault();
			$.ajax({
				url: etsLearnDashParams.admin_ajax,
				type: "POST",
				data: { 'action': 'ets_learndash_discord_clear_logs', 'ets_learndash_discord_nonce': etsLearnDashParams.ets_learndash_discord_nonce },
				beforeSend: function () {
					$(".clr-log.spinner").addClass("is-active").show();
				},
				success: function (data) {
         
					if (data.error) {
						// handle the error
						alert(data.error.msg);
					} else {
                                            
						$('.error-log').html("Clear logs Sucesssfully !");
					}
				},
				error: function (response, textStatus, errorThrown ) {
					console.log( textStatus + " :  " + response.status + " : " + errorThrown );
				},
				complete: function () {
					$(".clr-log.spinner").removeClass("is-active").hide();
				}
			});
		});                
		/*RUN API */
		$('.ets-learndash-discord-run-api').click(function (e) {
			e.preventDefault();
			$.ajax({
				url: etsLearnDashParams.admin_ajax,
				type: "POST",
				context: this,
				data: { 'action': 'ets_learndash_discord_run_api', 'ets_learndash_discord_user_id': $(this).data('user-id') , 'ets_learndash_discord_nonce': etsLearnDashParams.ets_learndash_discord_nonce },
				beforeSend: function () {
					$(this).siblings("div.run-api-success").html("");
					$(this).siblings('span.spinner').addClass("is-active").show();
				},
				success: function (data) {         
					if (data.error) {
						// handle the error
						alert(data.error.msg);
					} else {
                                            
						$(this).siblings("div.run-api-success").html("Update Discord Roles Sucesssfully !");
					}
				},
				error: function (response, textStatus, errorThrown ) {
					console.log( textStatus + " :  " + response.status + " : " + errorThrown );
				},
				complete: function () {
					$(this).siblings('span.spinner').removeClass("is-active").hide();
				}
			});
		});                
               
		/*Flush settings from local storage*/
		$("#revertMapping").on('click', function () {
			localStorage.removeItem('learndash_mapArray');
			localStorage.removeItem('learndash_mappingjson');
			window.location.href = window.location.href;
		});        
   
		/*Create droppable element*/
		function init() {
                    //if($('.makeMeDroppable').data('droppable')){
			$('.makeMeDroppable').droppable({
				drop: handleDropEvent,
				hoverClass: 'hoverActive',
			});
                    //}
                    //if($('.learndash-discord-roles-col').data('droppable')){
			$('.learndash-discord-roles-col').droppable({
				drop: handlePreviousDropEvent,
				hoverClass: 'hoverActive',
			});
                    //}
		}

		$(init);

		/*Create draggable element*/
		function makeDrag(el) {
			// Pass me an object, and I will make it draggable
			el.draggable({
				revert: "invalid",
				helper: 'clone',
				start: function(e, ui) {
				ui.helper.css({"width":"45%"});
				}
			});
		}

		/*Handel droppable event for saved mapping*/
		function handlePreviousDropEvent(event, ui) {
			var draggable = ui.draggable;
			if(draggable.data('learndash_course_id')){
				$(ui.draggable).remove().hide();
			}
			$(this).append(draggable);
			$('*[data-drop-learndash_role_id="' + draggable.data('learndash_role_id') + '"]').droppable({
				drop: handleDropEvent,
				hoverClass: 'hoverActive',
			});
			$('*[data-drop-learndash_role_id="' + draggable.data('learndash_role_id') + '"]').attr('data-drop-learndash_role_id', '');

			var oldItems = JSON.parse(localStorage.getItem('learndash_mapArray')) || [];
			$.each(oldItems, function (key, val) {
				if (val) {
					var arrayofval = val.split(',');
					if (arrayofval[0] == 'learndash_course_id_' + draggable.data('learndash_course_id') && arrayofval[1] == draggable.data('learndash_role_id')) {
						delete oldItems[key];
					}
				}
			});
			var jsonStart = "{";
			$.each(oldItems, function (key, val) {
				if (val) {
					var arrayofval = val.split(',');
					if (arrayofval[0] != 'learndash_course_id_' + draggable.data('learndash_course_id') || arrayofval[1] != draggable.data('learndash_role_id')) {
						jsonStart = jsonStart + '"' + arrayofval[0] + '":' + '"' + arrayofval[1] + '",';
					}
				}
			});
			localStorage.setItem('learndash_mapArray', JSON.stringify(oldItems));
			var lastChar = jsonStart.slice(-1);
			if (lastChar == ',') {
				jsonStart = jsonStart.slice(0, -1);
			}

			var learndash_mappingjson = jsonStart + '}';
			$("#ets_learndash_mapping_json_val").html(learndash_mappingjson);
			localStorage.setItem('learndash_mappingjson', learndash_mappingjson);
			draggable.css({ 'width': '100%', 'left': '0', 'top': '0', 'margin-bottom': '10px' });
		}

		/*Handel droppable area for current mapping*/
		function handleDropEvent(event, ui) {
			var draggable = ui.draggable;
			var newItem = [];
			var newClone = $(ui.helper).clone();
			if($(this).find(".makeMeDraggable").length >= 1){
				return false;
			}
			$('*[data-drop-learndash_role_id="' + newClone.data('learndash_role_id') + '"]').droppable({
				drop: handleDropEvent,
				hoverClass: 'hoverActive',
			});
			$('*[data-drop-learndash_role_id="' + newClone.data('learndash_role_id') + '"]').attr('data-drop-learndash_role_id', '');
			if ($(this).data('drop-learndash_role_id') != newClone.data('learndash_role_id')) {
				var oldItems = JSON.parse(localStorage.getItem('learndash_mapArray')) || [];
				$(this).attr('data-drop-learndash_role_id', newClone.data('learndash_role_id'));
				newClone.attr('data-learndash_course_id', $(this).data('learndash_course_id'));

				$.each(oldItems, function (key, val) {
					if (val) {
						var arrayofval = val.split(',');
						if (arrayofval[0] == 'learndash_course_id_' + $(this).data('learndash_course_id')) {
							delete oldItems[key];
						}
					}
				});

				var newkey = 'learndash_course_id_' + $(this).data('learndash_course_id');
				oldItems.push(newkey + ',' + newClone.data('learndash_role_id'));
				var jsonStart = "{";
				$.each(oldItems, function (key, val) {
					if (val) {
						var arrayofval = val.split(',');
						if (arrayofval[0] == 'learndash_course_id_' + $(this).data('learndash_course_id') || arrayofval[1] != newClone.data('learndash_role_id') && arrayofval[0] != 'learndash_course_id_' + $(this).data('learndash_course_id') || arrayofval[1] == newClone.data('learndash_role_id')) {
							jsonStart = jsonStart + '"' + arrayofval[0] + '":' + '"' + arrayofval[1] + '",';
						}
					}
				});

				localStorage.setItem('learndash_mapArray', JSON.stringify(oldItems));
				var lastChar = jsonStart.slice(-1);
				if (lastChar == ',') {
					jsonStart = jsonStart.slice(0, -1);
				}

				var learndash_mappingjson = jsonStart + '}';
				localStorage.setItem('learndash_mappingjson', learndash_mappingjson);
				$("#ets_learndash_mapping_json_val").html(learndash_mappingjson);
			}

			$(this).append(newClone);
			$(this).find('span').css({ 'order': '2' });
			if (jQuery(this).find('.makeMeDraggable').length >= 1) {
				$(this).droppable("destroy");
			}
			makeDrag($('.makeMeDraggable'));
			newClone.css({ 'width': '100%', 'left': '0', 'top': '0', 'margin-bottom': '0px', 'position':'unset', 'order': '1' });
		}
	}
        

});
if ( etsLearnDashParams.is_admin ) {
	/*Tab options*/
	if( typeof(skeletabs) !=='undefined' ){
	jQuery.skeletabs.setDefaults({
		keyboard: false
	});
    }
}
