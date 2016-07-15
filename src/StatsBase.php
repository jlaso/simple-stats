<?php
	
namespace SimpleStats;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Yaml\Yaml;

class StatsBase
{
    /** @var  array */
	protected $config;
    /** @var  string */
    protected $dataBaseFile;
    /** @var string  */
    protected $projectDir;
    /** @var  \SQLite3 */
    protected $conn;
    /** @var  StatsDB[] */
    protected $models;
	
	public function __construct()
    {
                            // vendor/jlaso/simple-stats/src
        $this->projectDir = __DIR__.'/../../../../';
        $this->readConfig();
        if($this->existsDataBaseFile()) {
            $this->conn = new \SQLite3($this->dataBaseFile);
        }else{
            $this->conn = new \SQLite3($this->dataBaseFile);
            $this->startDatabase();
        }
    }

    protected function readConfig()
    {
        $this->config = array_merge(
            Yaml::parse(file_get_contents($this->getConfigFile())),
            array(
                'database' => array(
                    'driver' => 'pdo_sqlite',
                    'path' => '%project_dir%/app/cache/simple_stats.sqlite',
                    'charset' => 'UTF8',
                ),
                'models' => array(
                    'clicks' ,
                ),
            )
        );
        $this->dataBaseFile = str_replace('%project_dir%', $this->projectDir, $this->config['database']['path']);

        foreach($this->config['models'] as $model){
            $this->models[$model] = new StatsDB($model);
        }
    }

    protected function getConfigFile()
    {
        $configFile = $this->projectDir.'/config-stats-base.yml';
        if(!file_exists($configFile)) {
            $configFile = __DIR__ . '/../config-stats-base.yml.dist';
            if (!file_exists($configFile)) {
                throw new FileNotFoundException($configFile);
            }
        }
        if(!is_readable($configFile)){
            throw new \Exception("File {$configFile} is not readable!");
        }

        return $configFile;
    }

    protected function existsDataBaseFile()
    {
        return file_exists($this->dataBaseFile);
    }

    protected function startDatabase()
    {
        foreach($this->models as $model){
            $sql = $model->getCreateTableSentence();
            $this->conn->exec($sql);
        }
    }
}