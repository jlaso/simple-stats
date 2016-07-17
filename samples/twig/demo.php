<?php

require_once __DIR__.'/../../vendor/autoload.php';

define ("CACHE_DIR",  __DIR__.'/cache');
// in order to check live changes
exec('rm -rf '.CACHE_DIR.'/*');

$loader = new Twig_Loader_Filesystem(__DIR__);
$twig = new Twig_Environment($loader, array(
    'cache' => CACHE_DIR,
));

// TWIG FUNCTIONS TO HANDLE STATS GRAPHICS SIMPLY 
$twig->addFunction(
    new Twig_SimpleFunction('statsGraph', function ($graphName, $title, $event, $range, $width, $height) {
        $graph = null;
        switch (strtolower(trim($graphName))){
            case 'scatter':
                $graph = new \JLaso\SimpleStats\Graph\Scatter();
                break;
            case 'bar':
                $graph = new \JLaso\SimpleStats\Graph\Bar();
                break;
            default:
                return "Graph {$graph} not recognized in statsGraph twig function";
        }
        $file = uniqid($graphName.'-').'.svg';

        $graph->draw($title, $event, $range, $width, $height, CACHE_DIR.'/'.$file);

        return '<img src="cache/'.$file.'" alt="'.$title.'">';

    }, array('pre_escape' => 'html', 'is_safe' => array('html')))
);

$twig->addFunction(
    new Twig_SimpleFunction('statsCount', function ($event, $data) {
        return \JLaso\SimpleStats\Stats::getInstance()->getCountByData($event, $data);
    })
);

$twig->addFunction(
    new Twig_SimpleFunction('userIP', function () {
        return \JLaso\SimpleStats\Stats::getInstance()->getUserIP();
    })
);

$twig->addFunction(
    new Twig_SimpleFunction('home', function () {
      return basename(__FILE__);
    })
);


echo $twig->render('demo.html.twig');