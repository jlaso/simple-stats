<?php

namespace JLaso\SimpleStats\Graph;

class Scatter extends BaseGraph
{
    public function getGraphType()
    {
        return count($this->sourceEvents) > 1 ? 'MultiScatterGraph' : 'ScatterGraph';
    }

    public function getSettings($settings = array())
    {
        return array_merge($settings,
            array(
                'back_colour' => '#eee', 'stroke_colour' => '#000',
                'back_stroke_width' => 0, 'back_stroke_colour' => '#eee',
                'axis_colour' => '#333', 'axis_overlap' => 2,
                'axis_font' => 'Georgia', 'axis_font_size' => 10,
                'grid_colour' => '#666', 'label_colour' => '#000',
                'pad_right' => 20, 'pad_left' => 20,
                'marker_colour' => array('red', 'blue', 'green', 'orange'),
                'marker_type' => array('square', 'triangle', 'x', 'cross'),
                'marker_size' => array(2, 3, 4, 3),
                'scatter_2d' => true,
                'best_fit' => 'straight', 'best_fit_dash' => '2,2',
                'best_fit_colour' => array('red', 'blue', 'green', 'orange'),
            )
        );
    }

    protected function genValues($data)
    {
        $values = array();
        
        foreach($data as $eventName=>$eventData) {
            if(count($eventData) > 0) {
                $first = $eventData[0]['date'];
                foreach ($eventData as $item) {
                    $values[$eventName][] = array(intval(($item['date'] - $first) / 86400), $item['count']);
                }
            }
        }
        
        return $values;
    }


}