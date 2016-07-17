<?php
	
namespace JLaso\SimpleStats;

use JLaso\SimpleStats\Model\DB;
use JLaso\SimpleStats\Model\StatsModel;
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
    /** @var  StatsModel[] */
    protected $models;
    /** @var DB  */
    protected $db;
	
	public function __construct($projectDir = null)
    {
                            // vendor/jlaso/simple-stats/src
        $this->projectDir = $projectDir ? $projectDir : realpath(__DIR__.'/../../../../');
        $this->readConfig();
        if($this->existsDataBaseFile()) {
            $this->conn = new \SQLite3($this->dataBaseFile);
        }else{
            @mkdir(dirname($this->getDataBaseFile()), 0777, true);
            $this->conn = new \SQLite3($this->dataBaseFile);
            $this->startDatabase();
        }
        $this->db = new DB($this->conn);
    }

    /**
     * @return DB
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @param string $event
     * @param string $data
     * @return int
     * @throws \Exception
     */
    public function getCountByData($event, $data)
    {
        if (!$model = $this->getModel($event)) {
            throw new \Exception("Event {$event} does not have a model associated !");
        }
        return $this->db->fetchCountByData($model, $data);
    }

    // found in http://stackoverflow.com/questions/13646690/how-to-get-real-ip-from-visitor
    function getUserIP()
    {
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if(filter_var($client, FILTER_VALIDATE_IP))
        {
            $ip = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP))
        {
            $ip = $forward;
        }
        else
        {
            $ip = $remote;
        }

        return $ip;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getDataBaseFile()
    {
        return $this->dataBaseFile;
    }

    /**
     * @return string
     */
    public function getProjectDir()
    {
        return $this->projectDir;
    }

    /**
     * @return \SQLite3
     */
    public function getConn()
    {
        return $this->conn;
    }

    /**
     * @return StatsModel[]
     */
    public function getModels()
    {
        return $this->models;
    }

    /**
     * @param $model
     * @return StatsModel|null
     */
    public function getModel($model)
    {
        return isset($this->models[$model]) ? $this->models[$model] : null;
    }

    protected function readConfig()
    {
        $this->config = array_merge(
            array(
                'database' => array(
                    'driver' => 'pdo_sqlite',
                    'path' => '%project_dir%/app/cache/simple_stats.sqlite',
                    'charset' => 'UTF8',
                ),
                'models' => array(
                    'clicks' ,
                ),
            ),
            Yaml::parse(file_get_contents($this->getConfigFile()))
        );
        $this->dataBaseFile = str_replace('%project_dir%', $this->projectDir, $this->config['database']['path']);

        foreach($this->config['models'] as $model){
            $this->models[$model] = new StatsModel($model);
        }
    }

    public function getConfigFile()
    {
        $configFile = $this->projectDir.'/config-stats-base.yml';
        if(!file_exists($configFile)) {
            $configFile = dirname(__DIR__) . '/config-stats-base.yml.dist';
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