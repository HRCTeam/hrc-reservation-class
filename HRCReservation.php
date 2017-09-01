<?php

class HRCReservation {

    private $token = '';
    private $device = '';
    private $host = '';

    private function post($url, $data)
    {
        if( $curl = curl_init() ) {
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $out = curl_exec($curl);
            curl_close($curl);

            return $out;
        }
        else {
            throw new Exception('Need install CURL plugin');
        }
    }

    public function __construct($file_path) {
        if(!file_exists($file_path)) {
            throw new Exception('Wrong licence file path');
        }

        $content = file_get_contents($file_path);
        $content = base64_decode($content);

        if($content === false) {
            throw new Exception('Wrong licence file');
        }

        $content = json_decode($content, true);

        if(!isset($content['ip']) || !isset($content['token'])) {
            throw new Exception('Wrong licence file');
        }

        $this->token = $content['token'];
        $this->device = $_SERVER['HTTP_HOST'];
        $this->host = $content['ip'];

        $answer = $this->post('http://' . $this->host . '/api/licence/check', http_build_query([
            'unn' => $this->token,
            'key' => $this->device,
            'work_code' => '0',
            'work_group' => '0',
            'soft' => 'Web'
        ]));

        if(!$answer) {
            throw new Exception('HRC Admin Next server not connected');
        }

        $answer = json_decode($answer, true);
        if($answer['status'] != 'success') {
            throw new Exception('Terminal not activated');
        }

        $this->token = $answer['token'];
    }

    public function send($date, $time, $name, $phone, $guests_count, $message = '') {
        $answer = $this->post('http://' . $this->host . '/api/reserve/store', http_build_query([
            'token' => $this->token,
            'date' => $date,
            'time' => $time,
            'guests_count' => $guests_count,
            'message' => $message,
            'client_name' => $name,
            'client_phone' => $phone
        ]));

        if(!$answer) {
            throw new Exception('Can not create reservation');
        }

        $answer = json_decode($answer, true);
        if($answer['status'] !== 'success') {
            throw new Exception($answer['more']);
        }

        return true;
    }

}