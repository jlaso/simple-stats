<?php

namespace JLaso\SimpleStats\Command;

use JLaso\SimpleStats\Stats;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SeedCommand extends Command
{
    const RECORDS = 1000;

    protected function configure()
    {
        $this
            ->setName('database:create-and-seed')
            ->setDescription('Create database and seed with date')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);
        $stats = Stats::getInstance();

        $output->writeln('ConfigFile '.$stats->getConfigFile());
        $output->writeln('Database '.$stats->getDataBaseFile());
        $output->writeln('Config '.print_r($stats->getConfig(),true));
        $output->writeln('ProjectDir '.$stats->getProjectDir());

        foreach($stats->getModels() as $model){
            for($i=0; $i<self::RECORDS; $i++) {
                $data = array(
                    'data' => '#' .intval((microtime(true)-$start)*1E10),
                    'count' => rand(1, 10),
                    'date' => intval(date('U')) + 86400 * rand(-5, 5),
                );
                $stats->getConn()->exec($model->getInsert($data));
            }
        }
        $output->writeln('Done!');
    }
}
