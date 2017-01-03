<?php
namespace cnorton_webdev;
class View
{
    private $model;
    private $controller;

    public function __construct($controller,$model) {
        $this->controller = $controller;
        $this->model = $model;
    }

    public function output() {
        require_once($this->model->template);
    }
    
    public function update_check() {
        return json_encode(array('last_id' => (int)$this->model->last_id));
    }
    
    public function fetch_data() {
        return $this->model->acars_content;
    }
    
    public function map_data() {
        return $this->model->map_markers;
    }
    public function last_map_id() {
        return $this->model->last_map_id;
    }
}