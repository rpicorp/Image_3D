<?php

namespace Pear\Image3D\Driver;

use Pear\Image3D\Color;
use Pear\Image3D\Driver;
use Pear\Image3D\Paintable\Polygon;
use Pear\Image3D\Renderer;

class GD extends Driver
{

    protected $_filetype;

    public function __construct()
    {
        parent::__construct();

        $this->_filetype = 'png';
    }

    public function createImage($x, $y)
    {
        $this->_image = imagecreatetruecolor($x, $y);
    }

    protected function _getColor(Color $color)
    {
        $values = $color->getValues();

        $values[0] = (int) round($values[0] * 255);
        $values[1] = (int) round($values[1] * 255);
        $values[2] = (int) round($values[2] * 255);
        $values[3] = (int) round($values[3] * 127);

        if ($values[3] > 0) {
            // Tranzparente Farbe allokieren
            $color = imageColorExactAlpha($this->_image, $values[0], $values[1], $values[2], $values[3]);
            if ($color === -1) {
                // Wenn nicht Farbe neu alloziieren
                $color = imageColorAllocateAlpha($this->_image, $values[0], $values[1], $values[2], $values[3]);
            }
        } else {
            // Deckende Farbe allozieren
            $color = imageColorExact($this->_image, $values[0], $values[1], $values[2]);
            if ($color === -1) {
                // Wenn nicht Farbe neu alloziieren
                $color = imageColorAllocate($this->_image, $values[0], $values[1], $values[2]);
            }
        }

        return $color;
    }

    public function setBackground(Color $color)
    {
        $bg = $this->_getColor($color);
        imagefill($this->_image, 1, 1, $bg);
    }

    public function drawPolygon(Polygon $polygon)
    {
        // Get points
        $points = $polygon->getPoints();
        $coords = array();
        foreach ($points as $point) {
            $coords = array_merge($coords, $point->getScreenCoordinates());
        }
        $coordCount = (int) (count($coords) / 2);

        if (true) {
            imageFilledPolygon($this->_image, $coords, $coordCount, $this->_getColor($polygon->getColor()));
        } else {
            imagePolygon($this->_image, $coords, $coordCount, $this->_getColor($polygon->getColor()));
        }

    }

    public function drawGradientPolygon(Polygon $polygon)
    {
        $this->drawPolygon($polygon);
    }

    public function setFiletye($type)
    {
        $type = strtolower($type);
        if (in_array($type, array('png', 'jpeg'))) {
            $this->_filetype = $type;
            return true;
        } else {
            return false;
        }
    }

    public function save($file)
    {
        switch ($this->_filetype) {
        case 'png':
            return imagepng($this->_image, $file);
        case 'jpeg':
            return imagejpeg($this->_image, $file);
        }
    }

    public function getSupportedShading()
    {
        return array(Renderer::SHADE_NO, Renderer::SHADE_FLAT);
    }
}

?>
