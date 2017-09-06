<?php
require_once (INCLUDE_DIR . 'class.plugin.php');
require_once ('config.php');

/**
 * Ensure that Agents are notified garishly when viewing tickets that are not
 * assigned to them.
 */
class IndicateNotYoursPlugin extends Plugin {

  var $config_class = 'IndicateNotYoursPluginConfig';

  function bootstrap() {
    // Ensure plugin does not run during cron calls.
    if (php_sapi_name() == 'cli') {
      return;
    }
    
    // We could seriously try and recreate the HTML in DOM objects, inject them into the page..
    // HOWEVER: The tickets.js onload function will simply override anything we write.. I know. I tried.
    // SO, the simplest way, is to simply emulate a "click" on the note tab.. at least we don't need translations!
    if (! class_exists('AttachmentPreviewPlugin')) {
      error_log("Attachment Preview Plugin not enabled.",
        "To use plugin Indicate Not Yours, you need to enable the Attachment Preview Plugin");
      return;
    }
    else {
      error_log("Loading plugin not-yours");
    }
    
    // Probably more efficient to see if we can even use it first, then build stuff:
    if (! AttachmentPreviewPlugin::isTicketsView()) {
      return;
    }
    
    static $has_run = false;
    if ($has_run) {
      return;
    }
    else {
      $has_run = true;
    }
    
    // We want to make a script element & a tiny stylesheet to make it pretty.
    // No need to build a DOMDocument anymore, new AttachmentsPreviewPlugin API exposes simpler ::addRawHtml method which does that for us.
    
    // Write our script.. if it was more complicated, we would put it in an external file and pull it in.
    // If we had this hosted on our server in another place, we wouldn't need this, just set:
    ob_start();
    ?>
<style>
#plugin-nyt {
	position: absolute;
	top: 50%;
	float: right;
	-webkit-transform: rotate(45deg);
	-moz-transform: rotate(45deg);
	-o-transform: rotate(45deg);
	filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=2);
	font-size: 3em;
	border: 10px dotted #ff23b6;
	padding: 20px;
	text-align: center;
}
</style>
<script type="text/javascript" name="Plugin: Not Your Ticket"
	source="https://github.com/clonemeagain/osticket-plugin-indicate-not-yours">
(function($){
	console.log("Plugin: Indicate not yours active.");
	var original_background_color = '#EEE',warning_background_color = '#FF23B6';
          $(document).on('ready pjax:success',function(){            
			if ($("#msg_warning .assignedTicket").length) {
			    var warning = $("#msg_warning .assignedTicket");
				// Tell the current user blatantly that this is someone elses!
				document.body.style.background = warning_background_color;
				// Construct an overlay that get's in your face
				$('#content').prepend('<div id="plugin-nyt">' + warning.text().trim() + '</div>'); 
				// Self-executing recursive animation to make the overlay fade in and out forever.
				(function pulse() { $("#plugin-nyt").delay(200).fadeOut('slow').delay(50).fadeIn('false', pulse);})();
			}else{
				// Undo our changes
				$('#plugin-nyt').remove();
				document.body.style.background = original_background_color;
			}
          });
})(jQuery);
</script>
<?php
    
    // Get admin defined colors:
    $replace = [
      '#EEE' => $this->getConfig()->get('normal-background'),
      '#FF23B6' => $this->getConfig()->get('warning-background')
    ];
    
    // Replace the colors in the script with the admin configured ones:
    $script = str_replace(array_keys($replace), array_values($replace),
      ob_get_clean());
    
    // Send script to browser
    AttachmentPreviewPlugin::addRawHtml($script, 'id', 'pjax-container');
  }

  /**
   * Required stub.
   *
   * {@inheritdoc}
   *
   * @see Plugin::uninstall()
   */
  function uninstall() {
    $errors = array();
    parent::uninstall($errors);
  }

  /**
   * Required stub
   */
  public function getForm() {
    return array();
  }
}
