<?php

namespace JLaso\SimpleStats\Graph;

class Bar extends BaseGraph
{
    public function getGraphType()
    {
        return 'BarGraph';
    }

    public function getSettings($settings = array())
    {
        return array_merge($settings,
            array(
                'back_colour' => '#eee', 'stroke_colour' => '#000',
            )
        );
    }

    protected function genValues($data)
    {
        $values = array();

        foreach($data as $eventName=>$eventData) {
            if(count($eventData) > 0) {
                foreach ($eventData as $item) {
                    $values[$eventName][] = $item['count'];
                }
            }
        }

        return $values;
    }
}