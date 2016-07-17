<?php

namespace JLaso\SimpleStats\Graph;

interface GraphInterface
{
    public function getSettings($settings = array());
    
    public function getGraphType();
}