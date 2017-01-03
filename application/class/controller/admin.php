<?php
namespace cnorton_webdev;
class Controller
{
    private $model;

    public function __construct($model){
        $this->model = $model;
    }

    public function get_flagged_count() {
        $pdo = \Database::connect();
        $stmt = $pdo->prepare('SELECT count(*) FROM acars WHERE flagged = 1 AND confirmed = 0');
        $stmt->execute();
        $flagged = $stmt->fetchColumn();
        $this->model->flagged = $flagged;
    }
    
    public function get_acars_count_24hr() {
        $pdo = \Database::connect();
        $stmt = $pdo->prepare('SELECT count(*) FROM acars WHERE date >= ? AND time >= ?');
        $date = (new \DateTime())->modify('-24 hours');
        $day = $date->format('Y-m-d');
        $time = $date->format('H:i:s');
        $stmt->execute(array($day, $time));
        $acars24 = $stmt->fetchColumn();
        $this->model->acars24 = $acars24;
    }
    
    public function get_last_week_count() {
        $pdo = \Database::connect();
        $msg_count_arr = [];
        for ($i=7;$i>0;$i--) {
            $stmt = $pdo->prepare('SELECT count(*) FROM acars WHERE date = ?');
            $date = (new \DateTime())->modify("-{$i} day");
            $day = $date->format('Y-m-d');
            $stmt->execute(array($day));
            $count = $stmt->fetchColumn();
            $msg_count_arr[$day] = $count;
        }
        $chartscript = "<script>
        new Morris.Line({
          // ID of the element in which to draw the chart.
          element: 'myfirstchart',
          // Chart data records -- each entry in this array corresponds to a point on
          // the chart.
          data: [";
        foreach ($msg_count_arr as $key => $val) {
            $chartscript .= "{ day: '{$key}', value: {$val} },";
        }
        $chartscript = rtrim($chartscript, ',') . "\n";
        $chartscript .= "],
          // The name of the data record attribute that contains x-values.
          xkey: 'day',
          // A list of names of data record attributes that contain y-values.
          ykeys: ['value'],
          // Labels for the ykeys -- will be displayed when you hover over the
          // chart.
          labels: ['Messages'],
          xLabelMargin: 10,
          xLabelAngle: 50
        });
        </script>";
        $this->model->chart_script = $chartscript;
    }
    
    public function get_flagged($offset = 0) {
        $pdo = \Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM acars WHERE flagged = 1 AND confirmed = 0 ORDER BY id DESC LIMIT {$offset},5");
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $this->model->flagged_msgs = $rows;
    }
    
}