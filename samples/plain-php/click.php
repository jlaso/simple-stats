<?php

require_once __DIR__.'/../../vendor/autoload.php';

$stats = \JLaso\SimpleStats\Stats::getInstance();

$data = isset($_GET['data']) ? trim($_GET['data']) : 'default';
$redirect = isset($_GET['redirect']) ? trim($_GET['redirect']) : 'demo.php';

$stats->insert('clicks', $data);

$userIp = $stats->getUserIP();
$stats->insert('ips', $userIp);

header('Location: '.$redirect);
die;