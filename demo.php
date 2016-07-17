<?php

require_once __DIR__.'/vendor/autoload.php';

$settings = array(
    'back_colour' => 'white',
    'graph_title' => 'Start of Fibonacci series'
);
$graph = new SVGGraph(600, 450, $settings);

$graph->Values(0, 1, 1, 2, 3, 5, 8, 13, 21);
$graph->Render('BarGraph');
