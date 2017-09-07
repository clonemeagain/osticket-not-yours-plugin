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
    
    $replace = [
      '#EEE' => $this->getConfig()->get('normal-background'),
      '#FF23B6' => $this->getConfig()->get('warning-background')
    ];
    // Write our script.. if it was more complicated, we would put it in an external file and pull it in.
    // If we had this hosted on our server in another place, we wouldn't need this, just set:
    $stylesheet = file_get_contents(__DIR__ . '/stylesheet.css');
    AttachmentPreviewPlugin::add_arbitrary_html(
      '<style>' .
         str_replace(array_keys($replace), array_values($replace), $stylesheet) .
         '</style>', 'tag', 'body');
    
    $script = file_get_contents(__DIR__ . '/script.js');
    // Replace the colors in the script with the admin configured ones:
    
    $script = str_replace(array_keys($replace), array_values($replace), $script);
    
    // Send to browser
    AttachmentPreviewPlugin::add_script($script);
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
