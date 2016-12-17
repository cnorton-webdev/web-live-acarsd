<?php
require('../application/class/controller/main.php');
require('../application/class/model/model.php');
require('../application/class/view/main.php');

$model = new cnorton_webdev\Model();
$controller = new cnorton_webdev\Controller($model);
$view = new cnorton_webdev\View($controller, $model);

$model->template('../application/class/view/html/index.phtml');

if (isset($_GET['action']) && !empty($_GET['action'])) {
    $controller->{$_GET['action']}();
    echo $view->{$_GET['action']}();
} else {
    echo $view->output();
}