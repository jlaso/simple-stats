<?php

namespace JLaso\SimpleStats\Graph;

use JLaso\SimpleStats\Model\DB;
use JLaso\SimpleStats\StatsBase;

abstract class BaseGraph implements GraphInterface
{
    protected static $instance;
    /** @var StatsBase  */
    protected $statsBase;
    /** @var  DB */
    protected $db;
    /** @var  array */
    protected $sourceEvents;

    /**
     * Stats constructor.
     */
    public function __construct()
    {
        $this->statsBase = new StatsBase();
        $this->db = new DB($this->statsBase->getConn());
    }

    /**
     * @return BaseGraph
     */
    public static function getInstance()
    {
        $className = get_called_class();
        if (!self::$instance) {
            self::$instance = new $className;
        }

        return self::$instance;
    }

    public function draw($title, $sourceEvent, $range, $width, $heigth, $destFile = null)
    {
        $this->sourceEvents = explode(',', $sourceEvent);
        $data = array();

        foreach($this->sourceEvents as $event) {
            if (!$model = $this->statsBase->getModel($event)) {
                throw new \Exception("Event {$event} does not have a model associated !");
            }
            $data[$event] = $this->db->fetchDataInRange($model, $range);
        }

        $values = $this->genValues($data);
    
        $settings = $this->getSettings(array(
            'graph_title' => $title,
        ));
        $this->renderGraph($this->getGraphType(), $width, $heigth, $settings, array_values($values), $destFile);
    }
    
    
    protected function renderGraph($graphType, $width, $height, $settings, $values, $destFile = null)
    {
        $graph = new \SVGGraph($width, $height, $settings);
        $graph->Values($values);
        
        if($destFile) {
            ob_start();
        }
        
        $graph->Render($graphType, false, false);
        
        if ($destFile){
            file_put_contents($destFile, ob_get_clean());
        }
    }

    abstract protected function genValues($data);

}