<?php

require_once __DIR__.'/../../vendor/autoload.php';

$stats = \JLaso\SimpleStats\Stats::getInstance();

$scatterGraph = new \JLaso\SimpleStats\Graph\Scatter();
$barGraph = new \JLaso\SimpleStats\Graph\Bar();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<h1>Example of Simple Stats</h1>
<div>
    <?php $scatterGraph->draw('Example scatter', 'clicks,ips', array(0,99999999999), 600, 480); ?>
</div>
<div>
    <a href="../click.php?data=click_link&redirect=plain-php/<?php echo(basename(__FILE__)); ?>">Click</a>
    <span>&nbsp;count [<?php echo $stats->getCountByData('ips', $stats->getUserIP());?>]</span>
</div>
<div>
    <?php $barGraph->draw('Example bars', 'logins', array(0,99999999999), 600, 480); ?>
</div>
</body>
</html>