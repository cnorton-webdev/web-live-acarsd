<?php
namespace cnorton_webdev;
class Controller
{
    private $model;

    public function __construct($model){
        $this->model = $model;
    }
    
    public function update_check() {
       $pdo = new \PDO("mysql:host=" . $this->model->db_host . ";" . "dbname=" . $this->model->db_name, $this->model->db_username, $this->model->db_password); 
        $stmt = $pdo->prepare('SELECT id FROM acars');
        $stmt->execute();
        $count = $stmt->rowCount();
        $pdo = null;
        $this->model->last_id = $count;
    }
    
    public function fetch_data($id) {
        $pdo = new \PDO("mysql:host=" . $this->model->db_host . ";" . "dbname=" . $this->model->db_name, $this->model->db_username, $this->model->db_password); 
        if ($id == 0) {
            $stmt = $pdo->prepare('SELECT * FROM acars ORDER BY id DESC LIMIT 5');
            $stmt->execute();
        } else {
            $stmt = $pdo->prepare('SELECT * FROM acars WHERE id > ? ORDER BY id DESC');
            $stmt->execute(array($id));
        }
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $rows = array_reverse ( $rows );
        $pdo = null;
        $data = '';
        $imgs = array();
        foreach($rows as $row) {    
            $format = 'Y-m-d H:i:s';
            $date = \DateTime::createFromFormat($format, $row['date'] . ' ' . $row['time']);
            
            $place_holders = array(
                '%reg%',
                '%ac_type%',
                '%mode%',
                '%label%',
                '%label_info%',
                '%block_id%',
                '%msg_num%',
                '%message%',
                '%flt_id%',
                '%flt_path%',
                '%airline%',
                '%month%',
                '%day%',
                '%year%',
                '%hour%',
                '%minute%',
                '%second%'
            );
            
            $values = array(
                $row['reg'],
                $row['plane'], 
                $row['mode'],
                $row['label'],
                $this->label_name($row['label']),
                $row['blockid'],
                $row['msgno'],
                $row['message'],
                $row['flight'],
                '',
                $row['airline'],
                $date->format('m'),
                $date->format('d'),
                $date->format('Y'),
                $date->format('H'),
                $date->format('i'),
                $date->format('s')
            );
            $data .= str_replace($place_holders, $values, $this->model->output_format);

            if ($row['flagged'] == 1) {
                $data .= 'Flag above message as interesting: <input type="checkbox" name="flag_msg" value="' . $row['id'] . '" disabled="true" checked="true" />' . "\n\n";
            } else {
                $data .= 'Flag above message as interesting: <input type="checkbox" name="flag_msg" value="' . $row['id'] . '" />' . "\n\n";
            }

            $imgs['img'] = $this->fetch_image($row['reg']);
            $imgs['reg'] = $row['reg'];
            $imgData[] = $imgs;
        }
        $output = array('html' => $data, 'last_id' => (int)$row['id'], 'images' => $imgData);
        $this->model->acars_content = json_encode($output, JSON_HEX_QUOT | JSON_HEX_TAG);
    }
    
    private function fetch_image($reg) {
        if (!file_exists("./ac_img_cache/{$reg}.jpg")) {
            try
                {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'http://www.airliners.net/search?keywords=' . $reg . '&sortBy=dateAccepted&sortOrder=desc&perPage=1&display=detail');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $source = curl_exec($ch);
                }

                catch(Exception $e)
                {
                    throw $e->getMessage();
                }
            preg_match("/Displaying photos <strong>(.*?)<\/strong>/", $source, $output_array);
            if (isset($output_array[0])) {
                preg_match("/<img class=\"lazy-load\" src=\"(.*?)\"/", $source, $output_array);
                $image = file_get_contents($output_array[1]);
                file_put_contents("ac_img_cache/{$reg}.jpg", $image);
                return "ac_img_cache/{$reg}.jpg";
            } else {
                return "images/photo-not-available.png";
            }
        } else {
            return "ac_img_cache/{$reg}.jpg";
        }
    }
    
    private function label_name($label) {
        $type = '';
        if ($label >= 80 && $label <= 89) {
            $type = 'Aircraft addressed downlinks';
        } else {
            $labels = array('_' => 'No information to transmit',
                            '00' => 'Emergency situation report',
                            '10' => 'Frequency Change',
                            '21' => 'Frontier Airlines Position Report ?',
                            '2S' => 'Weather request',
                            '2U' => 'Weather',
                            '26' => 'Air France',
                            '2F' => 'Air France position report',
                            '4M' => 'Cargo information',
                            '51' => 'Ground GMT request response',
                            '52' => 'Ground UTC request',
                            '54' => 'Aircrew initiated voice contact request',
                            '57' => 'Alternate aircrew initiated position report',
                            '5D' => 'ATIS request',
                            '5P' => 'Temporary suspension of ACARS',
                            '5R' => 'Aircraft initiated position report',
                            '5U' => 'Weather request',
                            '5Y' => 'Revision to previous ETA',
                            '5Z' => 'Airline designated downlink',
                            '7A' => 'Aircraft initiated engine data',
                            '7B' => 'Aircraft initiated miscellaneous message',
                            'A1' => 'Deliver oceanic clearance',
                            'A2' => 'Deliver departure clearance',
                            'A3' => 'Deliver departure clearance',
                            'A4' => 'Acknowledge PDC',
                            'A5' => 'Request position report',
                            'A6' => 'Request ADS report',
                            'A7' => 'Forward free text to aircraft',
                            'A8' => 'Deliver departure slot',
                            'A9' => 'Deliver ATIS information',
                            'A0' => 'ATIS Facilities notification',
                            'B0' => 'ATIS facilities notification',
                            'B1' => 'Request oceanic clearance',
                            'B2' => 'Request oceanic readback',
                            'B3' => 'Request departure clearance',
                            'B4' => 'Acknowledge departure clearance',
                            'B5' => 'Provide position report',
                            'B6' => 'Provide ADS report',
                            'B7' => 'Forward free text to ATS',
                            'B8' => 'Request departure slot',
                            'B9' => 'Request ATIS information',
                            'C0' => 'Uplink message to all cockpit printers',
                            'C1' => 'Uplink message to cockpit printer #1',
                            'C2' => 'Uplink message to cockpit printer #2',
                            'C3' => 'Uplink message to cockpit printer #3',
                            'CA' => 'Printer status = error',
                            'CB' => 'Printer status = busy',
                            'CC' => 'Printer status = local',
                            'CD' => 'Printer status = no paper',
                            'CE' => 'Printer status = buffer overrun',
                            'CF' => 'Printer status = reserved',
                            'F3' => 'Dedicated transceiver advisory',
                            'H1' => 'Message to/from terminal',
                            'HX' => 'Undelivered uplink report',
                            'M1' => 'IATA Departure message',
                            'M2' => 'IATA Arrival message',
                            'M3' => 'IATA Return to ramp message',
                            'M4' => 'IATA Return from airborne message',
                            'Q0' => 'ACARS link test',
                            'Q1' => 'Departure/arrival reports',
                            'Q2' => 'ETA reports',
                            'Q3' => 'Clock update',
                            'Q4' => 'Voice circuit busy',
                            'Q5' => 'Unable to process uplinked messages',
                            'Q6' => 'Voice-to-ACARS change-over',
                            'Q7' => 'Delay message',
                            'QA' => 'Out/fuel report',
                            'QB' => 'Off report',
                            'QC' => 'On report',
                            'QD' => 'In/fuel report',
                            'QE' => 'Out/fuel destination report',
                            'QF' => 'Off/destination report',
                            'QG' => 'Out/return in report',
                            'QH' => 'Out report',
                            'QK' => 'Landing report',
                            'QL' => 'Arrival report',
                            'QM' => 'Arrival information report',
                            'QN' => 'Diversion report',
                            'QX' => 'Intercept',
                            'RA' => 'Command aircraft terminal to transmit data',
                            'RB' => 'Response of aircraft terminal to RA message',
                            ':;' => 'Command aircraft transceiver to change freq.'
                           );
            if ( array_key_exists(strtoupper($label), $labels) ) {
                $type = $labels[strtoupper($label)];
            }
        }
        return $type;
    }
    public function map_data() {
        $pdo = new \PDO("mysql:host=" . $this->model->db_host . ";" . "dbname=" . $this->model->db_name, $this->model->db_username, $this->model->db_password); 
        $stmt = $pdo->prepare("SELECT * FROM acars WHERE message LIKE '3N01%' OR message LIKE '#M1BPOS%' OR message LIKE '71,G%' OR message LIKE 'N %,W%' OR message LIKE '#DFBTRP%' OR message LIKE 'POSN%'  OR message LIKE '28,C%' OR message LIKE 'DFB(POS%' OR message LIKE 'POS02%' OR message LIKE '%,N %,W%' OR message LIKE '#DFB*POS%' ORDER BY id DESC LIMIT 5");
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $rows = array_reverse ( $rows );
        $data = [];
        $i = 0;
        foreach($rows as $row) {
            $reg = $row['reg'];
            $data[$i] = [];
            $lat = '';
            $lng = '';
            
            if (strpos($row['message'], '3N01 POSRPT') > -1 || strpos($row['message'], '#M1BPOS') > -1 || strpos($row['message'], 'POS02,') > -1 || strpos($row['message'], '#DFB*POS') > -1 || strpos($row['message'], 'POSN')) {
                preg_match("/N([0-9.]{1,})W([0-9.]{4,5})/", $row['message'], $output);
                $lat = substr($output[1],0,2) . '.' . substr($output[1],2);
                if (substr($output[2],0,1) == 0) {
                    $lng = '-' . substr($output[2],1,2) . '.' . substr($output[2],3);
                } else {
                    $lng = substr($output[2],1,2) . '.' . substr($output[2],3);
                }
            } elseif (substr($row['flight'],0,2) == 'WS') {
                $msg_data = explode(',', $row['message']);
                $lat = str_replace('N ', '', $msg_data[0]);
                $lng = str_replace('W ', '-', $msg_data[1]);
            } elseif (strpos($row['message'], '#DFBTRP') > -1) {
                $msg_data = explode('  ', $row['message']);
                $lat = $msg_data[1];
                $lng = $msg_data[2];
            } elseif (preg_match("/N\s([0-9.]{3,}),W\s([0-9.]{4,7})/", $row['message'])) {
                preg_match("/N\s([0-9.]{3,}),W\s([0-9.]{4,7})/", $row['message'], $output);
                $lat = substr($output[1],0,2) . '.' . substr($output[1],2);
                if (substr($output[2],0,1) == ' ') {
                    $lng = '-' . substr($output[2],1,2) . '.' . substr($output[2],3);
                } else {
                    $lng = substr($output[2],1,2) . '.' . substr($output[2],3);
                }
            }
            
            $data[$i]['lat'] = $lat;
            $data[$i]['lng'] = $lng;
            $data[$i]['reg'] = $reg;
            $info_window = '<div class="info_content">
                <h3 class="info_title"> Tail number: <a href="http://flightaware.com/live/flight/'. $reg . '" target="_blank">'. $reg . '</a></h3>
                <div class="info_flight">Flight: ' . $row['flight'] . '</div>
                <div class="info_date"> Date/Time: ' . $row['date'] . ' ' . $row['time'] . '</div>
                <div class="info_message">Message Content: ' . $row['message'] . '</div>
            </div>';
            $data[$i]['info'] = $info_window;
            $i++;
        }
        $this->model->map_markers = json_encode($data);
    }
    public function last_map_id() {
        $pdo = new \PDO("mysql:host=" . $this->model->db_host . ";" . "dbname=" . $this->model->db_name, $this->model->db_username, $this->model->db_password); 
        $stmt = $pdo->prepare("SELECT * FROM acars WHERE message LIKE '3N01%' OR message LIKE '#M1BPOS%' OR message LIKE '71,G%' OR message LIKE 'N %,W%' OR message LIKE '#DFBTRP%' OR message LIKE 'POSN%'  OR message LIKE '28,C%' OR message LIKE 'DFB(POS%' OR message LIKE 'POS02%' OR message LIKE '%,N %,W%' OR message LIKE '#DFB*POS%' ORDER BY id DESC LIMIT 1");
        $stmt->execute();
        $id = $stmt->fetchColumn();
        $data = [];
        $data['id'] = $id;
        $this->model->last_map_id = json_encode($data);
    }
}