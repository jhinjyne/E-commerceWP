<?php

/**
 * Simple DTO: SingleCollectionPoint class.
 *
 * @package WPDesk\AbstractShipping\CollectionPoint
 */
namespace UpsFreeVendor\WPDesk\AbstractShipping\CollectionPoints;

use UpsFreeVendor\WPDesk\AbstractShipping\Shipment\Address;
/**
 * Define Single Collection Point.
 */
final class CollectionPoint
{
    /**
     * Collection point ID.
     *
     * @var string
     */
    public $collection_point_id;
    /**
     * Collection point name.
     *
     * @var string
     */
    public $collection_point_name;
    /**
     * Address.
     *
     * @var Address
     */
    public $collection_point_address;
}
