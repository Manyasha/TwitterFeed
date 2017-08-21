# Twitter Feed

## How to run

run ```
    composer install
    ``` from a project baseroot

create mysql database 

create table 
```
CREATE TABLE IF NOT EXISTS `tweets` (
  `tweet_id` bigint(20) unsigned NOT NULL,
  `tweet_text` varchar(160) NOT NULL,
  `created_at` datetime NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `screen_name` char(20) NOT NULL,
  `name` varchar(20) DEFAULT NULL,
  `profile_image_url` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`tweet_id`),
  KEY `created_at` (`created_at`),
  KEY `user_id` (`user_id`),
  KEY `screen_name` (`screen_name`),
  KEY `name` (`name`),
  FULLTEXT KEY `tweet_text` (`tweet_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
```

run **getTweets.php** file as a continuous background process

use webserver         
open **plugin/index.html** file at the any browser you like    
