<?php

/**
 * Converts Latitude and Longitude co-ordinates into a numerical grid, ideal for indexing in a relational database.
 *
 * @license MIT
 */
namespace JamieMBrown\GridLatLong;

class GridLatLong
{

    // How granularly would you like to split the globe?
    private $grid_size;

    // Distance unit can be K (km), M (miles) or N (nautical miles)
    private $distance_units;

    // The radius of the earth, based on the distance units
    private $earth_radius;

    /**
     * Set the key variables at init time
     *
     * @param int    $grid_size
     * @param string $distance_units
     */
    public function __construct($grid_size = 1000, $distance_units = 'K') {

        $this->grid_size = $grid_size;
        $this->distance_units = $distance_units;

        if ($this->distance_units == "K") {
            $this->earth_radius = 6378.1;
        } else if ($this->distance_units == "N") {
            $this->earth_radius = 3441.6147438;
        } else {
            $this->earth_radius = 3963.1676;
        }

    }

    /**
     * Get an array of grid references around (or at) a latlong
     *
     * @param float $latitude
     * @param float $longitude
     * @param float $radius
     *
     * @return array
     */
    public function getGridReferences($latitude, $longitude, $radius = 0) {

        // Work out the bounding box max and min in our internal grid reference.

        $bounding_north = $this->getInternalGridReference($this->addLatitudeLongitude($latitude, $longitude, 0, $radius));
        $bounding_east = $this->getInternalGridReference($this->addLatitudeLongitude($latitude, $longitude, 90, $radius));
        $bounding_south = $this->getInternalGridReference($this->addLatitudeLongitude($latitude, $longitude, 180, $radius));
        $bounding_west = $this->getInternalGridReference($this->addLatitudeLongitude($latitude, $longitude, 270, $radius));

        // Extract all combinations of the grid reference, using cantor pairing to combine them.

        $references = array();

        for ($i = $bounding_south[0]; $i <= $bounding_north[0]; $i++) {
            for ($a = $bounding_west[1]; $a <= $bounding_east[1]; $a++) {
                $references[] = (($i + $a) * ($i + $a + 1)) / 2 + $a;
            }

        }

        // Return the reference.

        return $references;

    }

    /**
     * Add a distance to a latitude and longitude
     *
     * @param float $latitude
     * @param float $longitude
     * @param int   $bearing
     * @param float $distance
     *
     * @return array
     */
    private function addLatitudeLongitude($latitude, $longitude, $bearing, $distance) {

        $new_latitude = rad2deg(asin(sin(deg2rad($latitude)) * cos($distance / $this->earth_radius) + cos(deg2rad($latitude)) * sin($distance / $this->earth_radius) * cos(deg2rad($bearing))));
        $new_longitude = rad2deg(deg2rad($longitude) + atan2(sin(deg2rad($bearing)) * sin($distance / $this->earth_radius) * cos(deg2rad($latitude)), cos($distance / $this->earth_radius) - sin(deg2rad($latitude)) * sin(deg2rad($new_latitude))));

        return array($new_latitude, $new_longitude);

    }       

    /**
     * Convert a latitude and longitude to an internal grid reference
     *
     * @param array $latlong
     *
     * @return array
     */
    private function getInternalGridReference($latlong) {

        $grid_latitude = floor(($this->grid_size / 180) * (90 + $latlong[0]));
        $grid_longitude = floor(($this->grid_size / 360) * (180 + $latlong[1]));

        return array($grid_latitude, $grid_longitude);

    }

}