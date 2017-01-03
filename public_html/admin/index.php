<?php
require('../../application/class/controller/admin.php');
require('../../application/class/model/admin.php');
require('../../application/class/view/admin.php');

$model = new cnorton_webdev\Model();
$controller = new cnorton_webdev\Controller($model);
$view = new cnorton_webdev\View($controller, $model);

$model->template('../../application/class/view/html/admin.phtml');

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