<?php

namespace backend\models;

use yii\httpclient\Client;

class SPIK extends \yii\base\Model
{
    const URL = [
        "BASE" => "http://login.scout-gps.ru:8081/",
        "ACTIONS" => [
            "LOGIN" => "spic/auth/rest/Login",
            "ONLINE" => [
                "SUBSCRIBE" => "spic/OnlineDataService/rest/Subscribe",
                "DATA" => "spic/OnlineDataService/rest/GetOnlineData",
            ],
            "UNITS" => [
                "COUNT" => "spic/units/rest/",
                "PAGE" => "spic/units/rest/getAllUnitsPaged",
            ],
            "STATISTICS" => [
                "START" =>          "spic/StatisticsController/rest/StartStatisticsSession",
                "STOP" =>           "spic/StatisticsController/rest/CancelStatisticsSession",
                "CURRENT_CHUNK" =>  "spic/StatisticsController/rest/GetCurrentChunkInfo",
                "NEXT_CHuNK" =>     "spic/StatisticsController/rest/BuildNextChunk",
                "START_BUILD" =>    "spic/StatisticsController/rest/StartBuild",
                "ADD_TRACK" =>      "spic/trackPeriodsMileage/rest/AddStatisticsRequest",
                "GET" =>            "spic/trackPeriodsMileage/rest/GetStatistics",
            ]
        ]
    ];

    public $token;
    public $session_id;
    public $statistics_session_id;

    public function init()
    {
        parent::init();

        if ( !empty(\Yii::$app->session->get("token")) ) {
            $this->token = \Yii::$app->session->get("token");
        } else {
            $token = $this->login();
        }

        if ( !empty(\Yii::$app->session->get("session_id")) ) {
            $this->session_id = \Yii::$app->session->get("session_id");
        } else {
            $subscribe = $this->subscribe();
        }

        if ( !empty(\Yii::$app->session->get("statistics_session_id")) ) {
            $this->statistics_session_id = \Yii::$app->session->get("statistics_session_id");
        }
    }

    private function send($data, $url, $session_id = null, $method = "POST")
    {
        $ch = curl_init();
        $headers = [
            "Accept:json",
            "Content-Type:application/json",
        ];

        if (!is_null($session_id)) {
            $headers[] = "ScoutAuthorization:{$session_id}";
        }

        $options = [
            CURLOPT_URL => self::URL["BASE"] . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode(is_null($data) ? [] : $data),
            CURLOPT_HTTPHEADER => $headers
        ];

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);

        if ($errno = curl_errno($ch)) {
            $error_message = curl_strerror($errno);
            \Yii::error("cURL error ({$errno}):\n {$error_message}");
        }

        $result = json_decode($response, true);

        if ( is_null($result) ) {
            \Yii::error($response);
            return null;
        }
        return $result;
    }

    private function login()
    {
        if (!is_null($this->token)) {
            return $this->token;
        }
        $result = $this->send([
            "Login" => \Yii::$app->params["SPIK"]["login"],
            "Password" => \Yii::$app->params["SPIK"]["password"],
            "TimeStampUtc" => "/Date(" . time() . ")/",
            "TimeZoneOlsonId" => "Aisa/Irkutsk",
            "CultureName" => "ru-ru",
            "UiCultureName" => "ru-ru"
        ], self::URL["ACTIONS"]["LOGIN"]);
        if ($result["SessionId"]) {
            \Yii::$app->session->set("token", $result["SessionId"]);
            $this->token = $result["SessionId"];
        }
        return $this->token;
    }

    private function units($offset = 0, $count = 25)
    {
        return $this->send([
            "Offset" => $offset,
            "Count" => $count
        ], self::URL["ACTIONS"]["UNITS"]["PAGE"], $this->login());
    }

    private function subscribe()
    {
        if ( !is_null($this->session_id) ) {
            return $this->session_id;
        }

        $units = $this->units();
        $ids = [];
        foreach ($units["Units"] as $unit) {
            $ids[] = $unit["UnitId"];
        }

        $result = $this->send([
            "UnitIds" => $ids,
        ], self::URL["ACTIONS"]["ONLINE"]["SUBSCRIBE"], $this->login());
        \Yii::error($result);
        if (isset($result["SessionId"])) {
            \Yii::$app->session->set("session_id", $result["SessionId"]["Id"]);
            $this->session_id = $result["SessionId"]["Id"];
        }
        return $result;
    }

    public function onlineData()
    {
        if ( isset($this->session_id) ) {
            return $this->send([
                "Id" => $this->session_id
            ], self::URL["ACTIONS"]["ONLINE"]["DATA"], $this->login());
        }
        return null;
    }

    private function startStatistic()
    {
        if ( !is_null($this->statistics_session_id) ) {
            return $this->statistics_session_id;
        }
        $data = [
            "Period" => [
                "Begin" => "/Date(" . strtotime("yesterday") . ")/",
                "End" => "/Date(" . time() . ")/"
            ],
            "TargetObject" => [
                "ObjectId" => 246
            ]
        ];
        $result = $this->send($data, self::URL["ACTIONS"]["STATISTICS"]["START"], $this->login());
        if ( $result ) {
            \Yii::$app->session->set("statistics_session_id", $result);
            $this->statistics_session_id = $result;
            return $result;
        }
        \Yii::error("Can`t start statistics");
        return null;
    }

    private function addStatistics()
    {
        $result = $this->send($this->startStatistic(), self::URL["ACTIONS"]["STATISTICS"]["ADD_TRACK"], $this->login());
        if (is_null($result)) {
            \Yii::error("Can`t add statistics\nToken: {$this->token}\nSession_id: {$this->session_id}\nStatistics_id: ".json_encode($this->statistics_session_id)."\n");
        }
        return $result;
    }

    private function buildStatistics()
    {
        $result = $this->send($this->startStatistic(), self::URL["ACTIONS"]["STATISTICS"]["START_BUILD"], $this->login());
        if ( is_null($result) ) {
            \Yii::error("Can`t build statistics\nToken: {$this->token}\nSession_id: {$this->session_id}\nStatistics_id: ".json_encode($this->statistics_session_id)."\n");
        }
        return $result;
    }

    private function getStatistics()
    {
        return $this->send($this->startStatistic(), self::URL["ACTIONS"]["STATISTICS"]["GET"], $this->login());
    }

    public function fuelStatistic()
    {
        $this->addStatistics();
        $this->buildStatistics();
        $s = $this->getStatistics();
        \Yii::error($s);
        return $s;
    }
}