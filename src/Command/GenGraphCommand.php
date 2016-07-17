<?php

namespace JLaso\SimpleStats\Command;

use JLaso\SimpleStats\Graph\BaseGraph;
use JLaso\SimpleStats\Graph\Scatter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenGraphCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('graph:create')
            ->setDescription('Create graph')
            ->addOption('graph', null, InputOption::VALUE_REQUIRED, 'Graph type')
            ->addOption('event', null, InputOption::VALUE_REQUIRED, 'Event name')
            ->addOption('width', null, InputOption::VALUE_OPTIONAL, 'Graph width', 600)
            ->addOption('height', null, InputOption::VALUE_OPTIONAL, 'Graph height', 480)
            ->addOption('start', null, InputOption::VALUE_OPTIONAL, 'Start date', 0)
            ->addOption('end', null, InputOption::VALUE_OPTIONAL, 'End date', 99999999999)
            ->addOption('title', null, InputOption::VALUE_OPTIONAL, 'Report title', 'This is the title')
            ->addOption('output', null, InputOption::VALUE_OPTIONAL, 'Output file', 'temp.svg.html')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);
        $outputFile = $input->getOption('output');
        $width = $input->getOption('width');
        $height = $input->getOption('height');
        $title = $input->getOption('title');
        $startDate = $input->getOption('start');
        $endDate = $input->getOption('end');
        $sourceEvent = $input->getOption('event');
        
        $graphType = ucfirst(strtolower($input->getOption('graph')));
        switch ($graphType) {
            case 'Scatter':
                $graph = Scatter::getInstance();
                break;

            default:
                throw new \Exception("Graph type '{$graphType}' not recognized!");
        }
        
        /** @var BaseGraph $graph */
        $graph->draw($title, $sourceEvent, array($startDate, $endDate), $width, $height, $outputFile);
        $output->writeln('Graph generated on '.$outputFile.' in '.intval((microtime(true)-$start)*1000).' msec');
        $output->writeln('Done!');
    }
}
