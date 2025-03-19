<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 3d Library
 *
 * PHP versions 5
 *
 * LICENSE:
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @license   http://www.gnu.org/licenses/lgpl.txt lgpl 2.1
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PackageName
 * @since     File available since Release 0.1.0
 */

namespace Pear\Image3D\Paintable\Object;

use Pear\Image3D\Paintable\Polygon;
use Pear\Image3D\Paintable\PaintableObject;
use Pear\Image3D\Point;

/**
 * Pie
 *
 * @category   Image
 * @package    Image_3D
 * @author     Kore Nordmann <3d@kore-nordmann.de>
 * @copyright  1997-2005 Kore Nordmann
 * @license    http://www.gnu.org/licenses/lgpl.txt lgpl 2.1
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/PackageName
 * @since      Class available since Release 0.1.0
 */
class Pie extends PaintableObject {

    public function __construct($parameter)
    {
        $parameter = $this->_checkParameter($parameter);

        if ($parameter['inside'] == 0) {
            $this->_createPie($parameter);
        } else {
            $this->_createDonutPie($parameter);
        }
    }

    protected function _createPie($parameter)
    {
        $step = ($parameter['end'] - $parameter['start']) / $parameter['detail'];

        // center
        $centerTop = new Point(0, 0, .5);
        $centerBottom = new Point(0, 0, -.5);

        // Add polygones for top and bottom of the pie
        $x = cos($parameter['start']) * $parameter['outside'];
        $y = sin($parameter['start']) * $parameter['outside'];
        $top = new Point($x, $y, .5);
        $bottom = new Point($x, $y, -.5);

        // Polygones for the opening side
        $this->_addPolygon(new Polygon($top, $centerTop, $centerBottom));
        $this->_addPolygon(new Polygon($bottom, $top, $centerBottom));

        for ($i = 1; $i <= $parameter['detail']; $i++) {
            $x = cos($parameter['start'] + $i * $step) * $parameter['outside'];
            $y = sin($parameter['start'] + $i * $step) * $parameter['outside'];

            $newTop = new Point($x, $y, .5);
            $newBottom = new Point($x, $y, -.5);

            $this->_addPolygon(new Polygon($centerTop, $top, $newTop));
            $this->_addPolygon(new Polygon($centerBottom, $bottom, $newBottom));

            // Rand
            $this->_addPolygon(new Polygon($top, $newBottom, $newTop));
            $this->_addPolygon(new Polygon($top, $bottom, $newBottom));

            $top = $newTop; $bottom = $newBottom;
        }

        // Polygones for the closing side
        $this->_addPolygon(new Polygon($top, $centerTop, $centerBottom));
        $this->_addPolygon(new Polygon($bottom, $top, $centerBottom));
    }

    protected function _checkParameter($array)
    {
        $array['detail'] = max(1, (int) @$array['detail']);
        $array['outside'] = max(0, (int) @$array['outside']);
        $array['inside'] = min(max(0, (int) @$array['inside']), @$array['outside']);
        $array['start'] = max(0, deg2rad((float) @$array['start']));
        $array['end'] = max(0, deg2rad((float) @$array['end']));

        return ($array);
    }
}
