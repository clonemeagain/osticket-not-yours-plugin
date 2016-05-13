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
     * {@inheritDoc}
     *
     * @see PluginConfig::getOptions()
     */
    function getOptions()
    {
        list ($__, $_N) = self::translate();
        return array(
            'indicate-not-yours' => new SectionBreakField(array(
                'label' => $__('To whom should we alert garishly?'),
                'description' => $__('Only makes sense to indicate this to Agents, setting disabled. To disable, simply disable plugin.'),
            )),
        );
    }
}
