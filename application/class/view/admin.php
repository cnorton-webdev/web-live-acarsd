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
        // '<p><a href="a.php?action=clicked">' . $this->model->string . "</a></p>";
        require_once($this->model->template);
    }
}