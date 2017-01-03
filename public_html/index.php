<?php
require('../application/class/controller/main.php');
require('../application/class/model/model.php');
require('../application/class/view/main.php');

$model = new cnorton_webdev\Model();
$controller = new cnorton_webdev\Controller($model);
$view = new cnorton_webdev\View($controller, $model);
if (isset($_GET['view']) && $_GET['view'] == 'map') {
    $model->template('../application/class/view/html/map.phtml');
} else {
    $model->template('../application/class/view/html/index.phtml');
}
if (isset($_GET['action']) && !empty($_GET['action'])) {
    if (isset($_GET['id'])) {
        $controller->{$_GET['action']}($_GET['id']);
    } else {
        $controller->{$_GET['action']}();
    }
    echo $view->{$_GET['action']}();
} else {
    echo $view->output();
}