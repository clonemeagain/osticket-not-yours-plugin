<?php
require_once (INCLUDE_DIR . 'class.plugin.php');
require_once ('config.php');

/**
 * Ensure that Agents are notified garishly when viewing tickets that are not assigned to them.
 */
class IndicateNotYoursPlugin extends Plugin
{

    var $config_class = 'IndicateNotYoursPluginConfig';

    function bootstrap()
    {
        // We could seriously try and recreate the HTML in DOM objects, inject them into the page..
        // HOWEVER: The tickets.js onload function will simply override anything we write.. I know. I tried.
        // SO, the simplest way, is to simply emulate a "click" on the note tab.. at least we don't need translations!
        if (! class_exists('AttachmentPreviewPlugin')) {
            global $ost;
            $ost->logError("Attachment Preview Plugin not enabled.", "To use plugin Indicate Not Yours, you need to enable the Attachment Preview Plugin");
            return;
        }

        // Probably more efficient to see if we can even use it first, then build stuff:
        if (! AttachmentPreviewPlugin::isTicketsView()) {
            return;
        }

        // We want to make a script element
        $dom = new DOMDocument();
        $script = $dom->createElement('script');
        $script->setAttribute('type', 'text/javascript');

        // Write our script.. if it was more complicated, we would put it in an external file and pull it in.
        // If we had this hosted on our server in another place, we wouldn't need this, just set:
        // $script->setAttribute('src', 'http://server/path/file.js');
        $script->nodeValue = <<<SCRIPT
// http://github.com/clonemeagain/osticket-plugin-indicate-not-yours
(function($){
          //$(document).on('ready pjax:success',function(){
            console.log("Not Yours Plugin active.");
			if ($("#msg_warning .assignedTicket").length) {
			    var warning = $("#msg_warning .assignedTicket");
				// Tell the current user blatantly that this is someone elses!
				document.body.style.background = '#ff23b6'; // Background colour of garishness
				$('#content').prepend('<div id="ticketNotYours" name="ticketNotYours" style="position:absolute; top:50%; float:right;-webkit-transform:rotate(45deg);-moz-transform:rotate(45deg);-o-transform: rotate(45deg);filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=2);font-size:3em;border: 10px dotted #ff23b6;padding: 20px; text-align: center; ">'
					+ warning.text().trim() + '</div>'); // Construct an overlay that get's in your face
				// Self-executing recursive animation to make the overlay fade in and out forever.
				(function pulse() { $("#ticketNotYours").delay(200).fadeOut('slow').delay(50).fadeIn('false', pulse);})();
			}
        //  });
})(jQuery);
SCRIPT;

        // Let's build the required signal structure.
        // We want to inject our script on tickets pages..
        /**
         * Based on: attachment_preview exposed functionality
         *
         * $structure = array(
         * /REGEX/ => array((object)[
         * 'element' => $element, // The DOMElement to replace/inject etc.
         * 'locator' => 'tag', // EG: tag/id/xpath
         * 'replace_found' => FALSE, // default value, only really have to include if you want to replace it
         * 'expression' => 'body' // which tag/id/xpath etc. eg: 'body', 'head', when locator=> 'id' you can use any html id attribute. (without # like jQuery).
         * ],
         * ... Additional Objects if required, all structures for matching regex get loaded if regex matches path
         * )
         */

        // Let's build the required signal structure, containing both DOM manipulations.
        // We want this script at the bottom of the "<body>", the default method is appendChild, and specifying "tag" will find it by tag.
        // Luckily there are never more than one <body> element's in an HTML page.. that could get weird.
        // Regex is which pages to operate on: in this case, tickets pages.
        $signal_structure = array(
            (object) [
                'locator' => 'tag',
                'expression' => 'body',
                'element' => $script
            ]
        );

        // Connect to the attachment_previews plugin and send the structures. :-)
        Signal::send('attachments.wrapper', $this, $signal_structure);
    }

    /**
     * Required stub.
     *
     * {@inheritDoc}
     *
     * @see Plugin::uninstall()
     */
    function uninstall()
    {
        $errors = array();
        parent::uninstall($errors);
    }

    /**
     * Required stub
     */
    public function getForm()
    {
        return array();
    }
}
