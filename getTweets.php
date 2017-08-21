<?php
/**
* Collect tweets from the Twitter streaming API
* This must be run as a continuous background process
*/
require_once ('config.php');
require_once ('vendor\autoload.php');
require_once ('db_lib.php');

class Consumer extends \OauthPhirehose
{
  public $oDB;
  public function db_connect() {
    $this->oDB = new db;
  }
	
  // This function is called automatically by the Phirehose class
  // when a new tweet is received with the JSON data in $status
  public function enqueueStatus($status) {
    $tweet_object = json_decode($status);
		
	// Ignore tweets without a properly formed tweet id value
    if (!(isset($tweet_object->id_str))) { return;}

    $field_values = $this->parseTweets($tweet_object);

    if ( empty($field_values) ) {
        return;
    }
    $this->oDB->insert('tweets',$field_values);
  }
  
  private function parseTweets($tweet_object) {
      if ($tweet_object->lang <> TWEETS_LANGUAGE) {
          return null;
      }

      // The streaming API sometimes sends duplicates
      $tweet_id = $tweet_object->id_str;
      if ($this->oDB->in_table('tweets','tweet_id=' . $tweet_id )) {
          return null;
      }

      $user_object = $tweet_object->user;

      return 'tweet_id = ' . $tweet_id . ', ' .
          'tweet_text = "' . $this->oDB->escape($tweet_object->text) . '", ' .
          'created_at = "' . $this->oDB->date($tweet_object->created_at) . '", ' .
          'user_id = ' .  $user_object->id_str . ', ' .
          'screen_name = "' . $this->oDB->escape($user_object->screen_name) . '", ' .
          'name = "' . $this->oDB->escape($user_object->name) . '", ' .
          'profile_image_url = "' . $user_object->profile_image_url . '"';
  }
}

$stream = new Consumer(OAUTH_TOKEN, OAUTH_SECRET, Phirehose::METHOD_FILTER);
$stream->db_connect();

// The keywords for tweet collection are entered here as an array
// For example: array('recipe','food','cook','restaurant','great meal')
$stream->setTrack(array('recipe'));
$stream->consume();
