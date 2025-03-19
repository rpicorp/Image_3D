<?php

namespace Pear\Image3D\Paintable\Object;

use Pear\Image3D\Paintable\Polygon;
use Pear\Image3D\Paintable\PaintableObject;
use Pear\Image3D\Point;

class Cone extends PaintableObject {

    public function __construct($parameter) {
        parent::__construct();

        $radius = 1;
        $height = 1;
        $detail = max(3, (int) $parameter['detail']);

        // Generate points according to parameters
        $top = new Point(0, $height, 0);
        $bottom = new Point(0, 0, 0);

        $last = new Point(1, 0, 0);
        $points[] = $last;

        for ($i = 1; $i <= $detail; ++$i) {
            $actual = new Point(cos(deg2rad(360 * $i / $detail)), 0, sin(deg2rad(360 * $i / $detail)));
            $points[] = $actual;

            // Build polygon
            $this->_addPolygon(new Polygon($top, $last, $actual));
            $this->_addPolygon(new Polygon($bottom, $last, $actual));
            $last = $actual;
        }

        // Build closing polygon
        $this->_addPolygon(new Polygon($top, $last, $points[0]));
        $this->_addPolygon(new Polygon($bottom, $last, $points[0]));
    }
}
