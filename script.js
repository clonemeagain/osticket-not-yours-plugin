(function($) {
	console.log("Plugin: Indicate not yours active.");
	var original_background_color = '#EEE', warning_background_color = '#FF23B6';
	$(document).on(
			'ready pjax:success',
			function() {
				if ($("#msg_warning .assignedTicket").length) {
					var warning = $("#msg_warning .assignedTicket");
					// Tell the current user blatantly that this is someone
					// elses!
					document.body.style.background = warning_background_color;
					// Construct an overlay that get's in your face
					$('#content').prepend(
							'<div id="plugin-nyt">' + warning.text().trim()
									+ "</div>");
					// Self-executing recursive animation to make the overlay
					// fade in and out forever.
					(function pulse() {
						$("#plugin-nyt").delay(200).fadeOut('slow').delay(50)
								.fadeIn('false', pulse);
					})();
				} else {
					// Undo our changes
					$('#plugin-nyt').remove();
					document.body.style.background = original_background_color;
				}
			});
})(jQuery);