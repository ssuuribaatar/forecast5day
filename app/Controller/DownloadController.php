<?php

App::uses('AppController', 'Controller');

class DownloadController extends AppController {
    public function index(){
        $url = "http://tsag-agaar.gov.mn/forecast_xml";
        $xml = file_get_contents($url);
        $response = simplexml_load_string($xml);
        $objects = $response->forecast5day;
        $this->loadModel('Weather');
        foreach($objects as $object) {
            $city = $object->city;
            $result = $this->Weather->query("SELECT id FROM cities WHERE name='$city'");
            $city_id = $result[0]['cities']['id'];
            $datas = $object->data; 
            foreach($datas->weather as $data){
                $date = $data->date;
                $temperaturenight = $data->temperatureNight;
                $temperatureday = $data->temperatureDay;
                $element_id_night = $data->phenoIdNight;
                $element_id_day = $data->phenoIdDay;
                $windnight = $data->windNight;
                $windday = $data->windDay;
                $timestamp = strtotime($date);
                $garig = date("l", $timestamp);
                if($garig == "Monday") $garig = "Даваа";
                if($garig == "Tuesday") $garig = "Мягмар";
                if($garig == "Wednesday") $garig = "Лхавга";
                if($garig == "Thursday") $garig = "Пүрэв";
                if($garig == "Friday") $garig = "Баасан";
                if($garig == "Saturday") $garig = "Бямба";
                if($garig == "Sunday") $garig = "Ням";
                // $this->request->data['Weather']['city_id'] = $city_id;
                // $this->request->data['Weather']['date'] = (string)$date;
                // $this->request->data['Weather']['temperaturenight'] = (int)$temperaturenight;
                // $this->request->data['Weather']['temperatureday'] = (int)$temperatureday;
                // $this->request->data['Weather']['element_id_night'] = (int)$element_id_night;
                // $this->request->data['Weather']['element_id_day'] = (int)$element_id_day;
                // $this->request->data['Weather']['windnight'] = (int)$windnight;
                // $this->request->data['Weather']['windday'] = (int)$windday;
                // $this->request->data['Weather']['garig'] = (string)$garig;
                // $this->Weather->save($this->request->data);
                $check = $this->Weather->query("SELECT id FROM weathers WHERE date = '$date' AND city_id = $city_id");
                if(count($check)>0){
                    $inserted_id = $check[0]['weathers']['id'];
                    $query = "UPDATE `weathers` SET `temperaturenight`=$temperaturenight,`temperatureday`=$temperatureday,`element_id_night`=$element_id_night,`element_id_day`=$element_id_day,`windnight`=$windnight,`windday`=$windday WHERE id = $inserted_id";
                    $this->Weather->query($query);
                } else {
                    $query = "INSERT INTO `weathers`(`city_id`, `date`, `temperaturenight`, `temperatureday`, `element_id_night`, `element_id_day`, `windnight`, `windday`, `garig`) VALUES ($city_id,'$date',$temperaturenight,$temperatureday,$element_id_night,$element_id_day,$windnight,$windday,'$garig')";
                    $this->Weather->query($query);
                }
            } 
        }
        exit();
    }
}
