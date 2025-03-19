<?php

use Pear\Image3D;
use Pear\Image3D\Color;

set_time_limit(0);
require_once(__DIR__ . '/../../vendor/autoload.php');

$world = new Image3D();
$world->setColor(new Color(255, 255, 255));

$light = $world->createLight('Light', array(-2000, -2000, -2000));
$light->setColor(new Color(255, 255, 255));

$redLight = $world->createLight('Light', array(90, 0, 50));
$redLight->setColor(new Color(255, 0, 0));

$sphere = $world->createObject('sphere', array('r' => 150, 'detail' => 4));
$sphere->setColor(new Color(150, 150, 150));

$renderer = $world->createRenderer('perspectively');

$world->createDriver('GD');
$world->render(400, 400, 'example.png');

echo $world->stats();

