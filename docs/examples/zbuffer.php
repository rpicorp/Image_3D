<?php

use Pear\Image3D;
use Pear\Image3D\Color;
use Pear\Image3D\Point;

set_time_limit(0);
require_once(__DIR__ . '/../../vendor/autoload.php');

$world = new Image3D();
$world->setColor(new Color(50, 50, 50));

$light1 = $world->createLight('Light', array(-20, -20, -20));
$light1->setColor(new Color(255, 255, 255));

$light2 = $world->createLight('Light', array(20, 20, -20));
$light2->setColor(new Color(0, 200, 0));

$p1 = $world->createObject('polygon', array(new Point(-30, 100, 0), new Point(-30, -150, 0), new Point(80, 0, 30)));
$p1->setColor(new Color(100, 200, 100));
$p2 = $world->createObject('polygon', array(new Point(-100, 50, 30), new Point(-70, -100, -20), new Point(150, 90, 0)));
$p2->setColor(new Color(100, 100, 200));
$p2 = $world->createObject('polygon', array(new Point(-30, 20, -50), new Point(-50, -30, -80), new Point(50, 30, 40)));
$p2->setColor(new Color(200, 100, 100, 100));

$world->transform($world->createMatrix('Rotation', array(90, 90, 0)));

$world->setOption(Image3D::IMAGE_3D_OPTION_BF_CULLING, false);
$world->setOption(Image3D::IMAGE_3D_OPTION_FILLED, true);

$world->createRenderer('perspectively');
$world->createDriver('ZBuffer');
$world->render(400, 400, 'Image_3D_ZBuffer.png');

echo $world->stats( );

