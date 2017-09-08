<?php
require_once INCLUDE_DIR . 'class.plugin.php';

class IndicateNotYoursPluginConfig extends PluginConfig
{

    // Provide compatibility function for versions of osTicket prior to
    // translation support (v1.9.4)
    function translate()
    {
        if (! method_exists('Plugin', 'translate')) {
            return array(
                function ($x) {
                    return $x;
                },
                function ($x, $y, $n) {
                    return $n != 1 ? $y : $x;
                }
            );
        }
        return Plugin::translate('indicate_not_yours');
    }

    /**
     * Build an Admin settings page.
     *
     * {@inheritdoc}
     *
     * @see PluginConfig::getOptions()
     */
    function getOptions()
    {
        list ($__, $_N) = self::translate();
        return [
            'normal-background' => new TextboxField([
                'label' => $__('Normal background color'),
                'hint' => $__('Default is #EEE'),
                'default' => '#EEE'
            ]),
            'warning-background' => new TextboxField([
                'label' => $__('Warning background color'),
                'hint' => $__('Default is #FF23B6, any CSS compatible color works.'),
                'default' => '#FF23B6'
            ])
        ];
    }
}
