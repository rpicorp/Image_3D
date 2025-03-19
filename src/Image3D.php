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

namespace Pear;

use Pear\Image3D\Color;
use Pear\Image3D\Driver;
use Pear\Image3D\Matrix;
use Pear\Image3D\Paintable\Light;
use Pear\Image3D\Renderer;

// {{{ Image3D

/**
 * Image3D
 *
 * Class for creation of 3d images only with native PHP.
 *
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @license   http://www.gnu.org/licenses/lgpl.txt lgpl 2.1
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PackageName
 * @since     Class available since Release 0.1.0
 */
class Image3D
{

    // {{{ properties

    /**
     * Backgroundcolor
     *
     * @var Color
     */
    protected $_color;

    /**
     * List of known objects
     *
     * @var array
     */
    protected $_objects;

    /**
     * List of lights
     *
     * @var array
     */
    protected $_lights;

    /**
     * Active renderer
     *
     * @var Renderer
     */
    protected $_renderer;

    /**
     * Active outputdriver
     *
     * @var Driver
     */
    protected $_driver;


    /**
     * Options for rendering
     */
    protected $_option;

    /**
     * Options set by the user
     *
     * @var array
     */
    protected $_optionSet;

    /**
     * Begin of world creation
     *
     * @var float
     */
    protected $_start;

    // }}}
    // {{{ constants

    /**
     * Option for filled polygones (depreceated)
     */
    const IMAGE_3D_OPTION_FILLED = 1;

    /**
     * Option for backface culling (depreceated)
     */
    const IMAGE_3D_OPTION_BF_CULLING = 2;

    // }}}
    // {{{ __construct()

    /**
     * Constructor for Image3D
     *
     * Initialises the environment
     *
     * @return  Image3D                World instance
     */
    public function __construct()
    {
        $this->_objects  = array();
        $this->_lights   = array();
        $this->_renderer = null;
        $this->_driver   = null;
        $this->_color    = null;

        $this->_option[self::IMAGE_3D_OPTION_FILLED]     = true;
        $this->_option[self::IMAGE_3D_OPTION_BF_CULLING] = true;

        $this->_optionSet = array();

        $this->_start = microtime(true);
    }

    // }}}
    // {{{ createObject()

    /**
     * Factory method for Objects
     *
     * Creates and returns a printable object.
     * Standard objects with parameters:
     *     - cube        array(float $x, float $y, float $z)
     *     - sphere    array(float $r, int $detail)
     *     - 3ds        string $filename
     *     - map        [array(array(Point))]
     *     - text        string $string
     *
     * @param string $type      Objectname
     * @param array  $parameter Parameters
     *
     * @return  Object            Object instance
     */
    public function createObject($type, $parameter = array())
    {
        $class = '\\Pear\\Image3D\\Paintable\\Object\\' . ucfirst($type);

        return $this->_objects[] = new $class($parameter);
    }

    // }}}
    // {{{ createLight()

    /**
     * Factory method for lights
     *
     * Creates and returns a light. Needs only the position of the lights as a
     * parameter.
     *
     * @param string $type      Class
     * @param mixed  $parameter Parameters
     *
     * @return  Light          Object instance
     */
    public function createLight($type, $parameter = array())
    {
        $name = ucfirst($type);
        if ($name != 'Light') {
            $class = '\\Pear\\Image3D\\Paintable\\Light\\' . $name;

            return $this->_lights[] = new $class($parameter[0], $parameter[1], $parameter[2], array_slice($parameter, 3));
        } else {
            return $this->_lights[] = new Light($parameter[0], $parameter[1], $parameter[2]);
        }
    }

    // }}}
    // {{{ createMatrix()

    /**
     * Factory method for transformation matrixes
     *
     * Creates a transformation matrix
     * Known matrix types:
     *  - rotation      array(float $x, float $y, float $z)
     *  - scale         array(float $x, float $y, float $z)
     *  - move          array(float $x, float $y, float $z)
     *
     * @param string $type      Matrix type
     * @param array  $parameter Parameters
     *
     * @return  Matrix         Object instance
     */
    public function createMatrix($type, $parameter = array())
    {
        $class = '\\Pear\Image3D\\Matrix\\' . ucfirst($type);

        return new $class($parameter);
    }

    // }}}
    // {{{ setColor()

    /**
     * Sets world backgroundcolor
     *
     * Sets the backgroundcolor for final image. Transparancy is not supported
     * by all drivers
     *
     * @param Color $color Backgroundcolor
     *
     * @return  void
     */
    public function setColor(Color $color)
    {
        $this->_color = $color;
    }

    // }}}
    // {{{ createRenderer()

    /**
     * Factory method for renderer
     *
     * Creates and returns a renderer.
     * Avaible renderers
     *  - Isometric
     *  - Perspektively
     *
     * @param string $type Renderer type
     *
     * @return  Renderer       Object instance
     */
    public function createRenderer($type)
    {
        $class = '\\Pear\\Image3D\\Renderer\\' . ucfirst($type);

        return $this->_renderer = new $class();
    }

    // }}}
    // {{{ createDriver()

    /**
     * Factory method for drivers
     *
     * Creates and returns a new driver
     * Standrad available drivers:
     *  - GD
     *  - SVG
     *
     * @param string $type Driver type
     *
     * @return  Driver         Object instance
     */
    public function createDriver($type)
    {
        $class = '\\Pear\\Image3D\\Driver\\' . ucfirst($type);

        return $this->_driver = new $class();
    }

    // }}}
    // {{{ setOption()

    /**
     * Sets an option for all known objects
     *
     * Sets one of the Image3D options for all known objects
     *
     * @param integer $option Option
     * @param mixed   $value  Value
     *
     * @return  void
     */
    public function setOption($option, $value)
    {
        $this->_option[$option]    = $value;
        $this->_optionSet[$option] = true;

        foreach ($this->_objects as $object) {
            $object->setOption($option, $value);
        }
    }

    // }}}
    // {{{ transform()

    /**
     * Transform all known objects
     *
     * Transform all known objects with the given transformation matrix.
     * Can be interpreted as a transformation of the viewpoint.
     *
     * The id is an optional value which shouldn't be set by the user to
     * avoid double calculations, if a point is related to more than one
     * object.
     *
     * @param Matrix $matrix Transformation matrix
     * @param mixed           $id     Transformation ID
     *
     * @return  void
     */
    public function transform(Matrix $matrix, $id = null)
    {

        if ($id === null) {
            $id = substr(md5(microtime()), 0, 8);
        }

        foreach ($this->_objects as $object) {
            $object->transform($matrix, $id);
        }
    }

    // }}}
    // {{{ render()

    /**
     * Renders the image
     *
     * Starts rendering an image with given size into the given file.
     *
     * @param integer $x    Width
     * @param integer $y    Height
     * @param string  $file Filename
     *
     * @return  boolean Success
     */
    public function render($x, $y, $file)
    {
        // Hack because stdout is not writeable
        if ((is_file($file) || !is_writeable(dirname($file)))
            &&    (!is_file($file) || !is_writeable($file))
            &&    !preg_match('/^\s*php:\/\/(stdout|output)\s*$/i', $file)) {
            throw new \Exception('Cannot write outputfile.');
        }

        $x = min(1280, max(0, (int) $x));
        $y = min(1280, max(0, (int) $y));

        $this->_renderer->setSize($x, $y);
        $this->_renderer->setBackgroundColor($this->_color);
        $this->_renderer->addObjects($this->_objects);
        $this->_renderer->addLights($this->_lights);
        $this->_renderer->setDriver($this->_driver);

        return $this->_renderer->render($file);
    }

    // }}}
    // {{{ stats()

    /**
     * Statistics for Image3D
     *
     * Returns simple statisics for Image3D as a string.
     *
     * @return  string                  Statistics
     */
    public function stats()
    {
        return sprintf('Image 3D

objects:    %d
lights:     %d
polygones:  %d
points:     %d

time:       %.4f s
',
            count($this->_objects),
            $this->_renderer->getLightCount(),
            $this->_renderer->getPolygonCount(),
            $this->_renderer->getPointCount(),
            microtime(true) - $this->_start);
    }

    // }}}
}

// }}}
