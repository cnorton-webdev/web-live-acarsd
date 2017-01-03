<?php
namespace cnorton_webdev;
class Model
{
    public $last_id;
    public $acars_content;
    public $template;
    public $db_username;
    public $db_password;
    public $db_host;
    public $db_name;
    public $site_title;
    public $frequnecy;
    public $location;
    public $refresh_frequency;
    public $use_img_cache;
    public $output_format;
    
    public function __construct(){
        $this->last_id = 0;
        $this->acars_content = '{}';
        require_once('../../application/conf/config.php');
        $this->frequency = $acars_frequency;
        $this->location = $location;
        $this->site_title = $site_title;
        $this->refresh_frequency = $refresh_frequency;
        $this->use_img_cache = $use_img_cache;
        $this->db_name = $db_name;
        $this->db_host = $db_host;
        $this->db_username = $db_username;
        $this->db_password = $db_password;
        $this->output_format = $output_format;
    }
    
    public function template($tpl) {
        $this->template = $tpl;
    }
}