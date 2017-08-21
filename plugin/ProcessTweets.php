<?php

require_once ('../config.php');
require_once ('../db_lib.php');

class ProcessTweets {
    private $db;
    private $lastTweetId = null;

    public function __construct(){
        $this->db = new db;
    }

    public function exec() {
        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache");
        header("Connection: keep-alive");

        $lastId = isset($_SERVER["HTTP_LAST_EVENT_ID"]) ? $_SERVER["HTTP_LAST_EVENT_ID"] : 0;
        if (isset($lastId) && !empty($lastId) && is_numeric($lastId)) {
            $lastId = intval($lastId);
            $lastId++;
        }

        while (true) {
            $data = $this->getTweets();
            if ( isset($data) ) {
                $this->sendTweets($lastId, $data);
                $lastId++;
            }
            sleep(1);
        }
    }

    private function getTweets() {
        $query = 'SELECT * FROM tweets ';

        if ( isset($this->lastTweetId) ) {
            $query .= 'WHERE tweet_id > "' . $this->lastTweetId . '" ';
        }

        $query .= 'ORDER BY tweet_id DESC LIMIT ' . TWEET_DISPLAY_COUNT;
        $result = $this->db->select($query);

        $tweets = array();
        while($row = mysqli_fetch_assoc($result)) {
            $tweets[] = $row;
        }

        if ( empty($tweets) ) {
            return null;
        }

        $this->lastTweetId = $tweets[0]['tweet_id'];

        return json_encode(array("tweets" => array_reverse($tweets), "count" => TWEET_DISPLAY_COUNT));
    }

    private function sendTweets($id, $data) {
        echo "id: $id" . PHP_EOL;
        echo "event: tweets" . PHP_EOL;
        echo "data: $data\n" . PHP_EOL;
        ob_flush();
        flush();
    }
}
