<?php

namespace JLaso\SimpleStats\Model;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Yaml\Yaml;

class StatsModel
{
    protected $event;
    protected $fields;

    /**
     * @param string $event
     * @param array|null $fields
     */
    public function __construct($event, $fields = null)
    {
        $this->event = $event;
        $this->fields = $fields ? $fields : array(
            'count' => 'INT',
            'date' => 'INT',
            'data' => 'TEXT',
        );
    }

    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }
    
    /**
     * @return string
     */
    public function getCreateTableSentence()
    {
        $fields = $this->getFieldsForCreateTable();

        return "CREATE TABLE `{$this->event}` ({$fields});";
    }

    /**
     * @param array $data
     * @return string
     */
    public function getInsert($data)
    {
        $fields = implode(',', array_keys($this->fields));
        $values = $this->getValues($data);

        return "INSERT INTO `{$this->event}` ({$fields}) VALUES ({$values})\n";
    }

    /**
     * @param string $where
     * @return string
     */
    public function getSelect($where)
    {
        return "SELECT * FROM `{$this->event}` WHERE {$where};";
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return string
     * @throws \Exception
     */
    public function getDateRange($startDate, $endDate)
    {
        if($startDate instanceof \DateTime){
            $startDate = intval($startDate->format('U'));
        }
        if($endDate instanceof \DateTime){
            $endDate = intval($endDate->format('U'));
        }
        if(!is_int($startDate) || !is_int($endDate)){
            throw new \Exception('getDateRange expects dates as \DateTime or integer');
        }
        return "`date` >= {$startDate} AND `date` <= {$endDate}";
    }

    /**
     * @param array $range
     * @return string
     * @throws \Exception
     */
    public function getSelectDateRange($range)
    {
        list($startDate, $endDate) = $range;
        $where = $this->getDateRange($startDate, $endDate);
        
        return $this->getSelect($where);
    }
    

    protected function getValues($data)
    {
        $result = array();
        foreach ($this->fields as $fieldName => $fieldType) {
            $value = $data[$fieldName];
            $result[] = 'TEXT' == $fieldType ? "'{$value}'" : $value;
        }

        return implode(',', $result);
    }

    /**
     * @return string
     */
    protected function getFieldsForCreateTable()
    {
        $result = array();
        foreach ($this->fields as $fieldName => $fieldType) {
            $result[] = $fieldName . ' ' . $fieldType;
        }

        return implode(',', $result);
    }

}