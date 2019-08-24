<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 1/20/19
 * Time: 11:57 PM
 */

namespace app\common\service;


class CoreSeek
{
    private static $instance;
    public $coreseek = null;
    public $max_matches = 1000;

    final protected function __construct(){
        $this->coreseek = new \SphinxClient();

        $this->coreseek->setServer("127.0.0.1", 9312);
        $this->coreseek->setMaxQueryTime(30);
    }

    static function getInstance(...$args)
    {
        if(!isset(self::$instance)){
            self::$instance = new self(...$args);
        }
        return self::$instance;
    }

    public function query($keywords,$offset = 0, $limit = 10, $index = '*'){
        $this->keywords = $keywords;
        $this->index = $index;
        $max_matches = $limit > $this->max_matches ? $limit : $this->max_matches;
        $this->coreseek->setLimits($offset, $limit, $max_matches);
        $query_results = $this->coreseek->query($keywords, $index);
        return $query_results;
    }

    public function getClient(){
        return $this->coreseek;
    }

    public function getTotal(){
        $res = $this->coreseek->query($this->keywords, $this->index);
        return $res['total_found'];
    }

    public function getError(){
        return $this->coreseek->getLastError();
    }
}