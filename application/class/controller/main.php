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
    
    public function fetch_data() {
        $pdo = new \PDO("mysql:host=" . $this->model->db_host . ";" . "dbname=" . $this->model->db_name, $this->model->db_username, $this->model->db_password); 
        $stmt = $pdo->prepare('SELECT * FROM acars ORDER BY id DESC LIMIT 5');
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $rows = array_reverse ( $rows );
        $pdo = null;
        $data = '';
        $imgs = array();
        foreach($rows as $row) {

            $format = 'Y-m-d H:i:s';
            $date = \DateTime::createFromFormat($format, $row['date'] . ' ' . $row['time']);

            $data .= '<span class="maroon">ACARS mode: </span><span class="blue">' . $row['mode'] . '</span>  <span class="maroon">Aircraft reg: </span><span class="blue">' . $row['reg'] . '</span>  <span class="green">[' . $row['plane'] . ']</span>' . "\n";

            $data .= '<span class="maroon">Message label:</span> <span class="blue">' . $row['label'] . '</span> <span class="green">[' . $this->label_name($row['label']) . ']</span> <span class="maroon">Block id: <span><span class="blue">' . $row['blockid'] . '</span> <span class="maroon">Msg no: </span><span class="blue">' . $row['msgno'] . '</span>' . "\n";

            $data .= '<span class="maroon">Flight ID:</span> <span class="blue">' . $row['flight'] . '</span> <span class="green">[' . $row['airline'] . ']</span>' . "\n";

            $data .= '<span class="maroon">Message content:</span>' . "\n";

            $data .= '<span class="blue">' . $row['message'] . '</span>' . "\n";

            $data .= '<span class="black">----------------------------------------------------------[ ' .$date->format('m-d-Y H:i:s') . ' ]-</span>' . "\n";
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
                            'A4' => 'Acknowledge PDC',
                            'A5' => 'Request position report',
                            'A6' => 'Request ADS report',
                            'A7' => 'Forward free text to aircraft',
                            'A8' => 'Deliver departure slot',
                            'A9' => 'Deliver ATIS information',
                            'A0' => 'ATIS Facilities notification',
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
}