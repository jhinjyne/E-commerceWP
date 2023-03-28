<?php

namespace UpsFreeVendor\Octolize\ShippingExtensions\Tracker;

use Exception;
use UpsFreeVendor\Octolize\ShippingExtensions\Tracker\DataProvider\ShippingExtensionsDataProvider;
use UpsFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use UpsFreeVendor\WPDesk_Tracker;
/**
 * .
 */
class Tracker implements \UpsFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /**
     * @var ViewPageTracker
     */
    private $tracker;
    /**
     * @param ViewPageTracker $tracker
     */
    public function __construct(\UpsFreeVendor\Octolize\ShippingExtensions\Tracker\ViewPageTracker $tracker)
    {
        $this->tracker = $tracker;
    }
    /**
     * Hooks.
     */
    public function hooks() : void
    {
        try {
            $tracker = $this->get_tracker();
            $tracker->add_data_provider(new \UpsFreeVendor\Octolize\ShippingExtensions\Tracker\DataProvider\ShippingExtensionsDataProvider($this->tracker));
        } catch (\Exception $e) {
            // phpcs:ignore
            // Do nothing.
        }
    }
    /**
     * @return WPDesk_Tracker
     * @throws Exception
     */
    protected function get_tracker() : \UpsFreeVendor\WPDesk_Tracker
    {
        $tracker = \apply_filters('wpdesk_tracker_instance', null);
        if ($tracker instanceof \UpsFreeVendor\WPDesk_Tracker) {
            return $tracker;
        }
        throw new \Exception('Tracker not found');
    }
}
