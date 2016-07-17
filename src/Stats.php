<?php

namespace JLaso\SimpleStats;

class Stats extends StatsBase
{
    protected static $instance;

    /**
     * Stats constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return Stats
     */
    public static function getInstance()
    {
        if(!self::$instance){
            self::$instance = new Stats();
        }

        return self::$instance;
    }

    public function insert($event, $data)
    {
        if (!$model = $this->getModel($event)) {
            throw new \Exception("Event {$event} does not have a model associated !");
        }
        $this->db->insertData($model, $data);
    }
}