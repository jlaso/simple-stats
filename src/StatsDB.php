<?php
	
namespace SimpleStats;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Yaml\Yaml;

class StatsDB
{
	protected $event;
    protected $fields;

    /**
     * StatsDB constructor.
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
    public function getCreateTableSentence()
    {
        $fields = $this->getFieldsForCreateTable();

        return "CREATE TABLE `{$this->event}` ({$fields});";
    }

    /**
     * @return string
     */
    protected function getFieldsForCreateTable()
    {
        $result = array();
        foreach($this->fields as $fieldName=>$fieldType){
            $result[] = $fieldName . ' ' . $fieldType;
        }

        return join(',', $result);
    }

}