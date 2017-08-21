/**
* Twitter Feed Plugin
*/

(function ($) {
    if(typeof(EventSource) === "undefined") {
        alert("Your browser does not support Server-sent events! Please upgrade it!");
        return;
    }

    $(function () {
        createEventSourceConnection();
    });

    function createEventSourceConnection() {
        var source = new EventSource("lastTweets.php");

        source.addEventListener("tweets", function(e) {
            console.log('Getting tweets');
            console.log(JSON.parse(e.data));
            tweetsList(JSON.parse(e.data))
        }, false);

        source.addEventListener("open", function(e) {
            console.log("Connection was opened.");
        }, false);

        source.addEventListener("error", function(e) {
            console.log("Error - connection was lost.");
        }, false);
    }

    function tweetsList(data) {
        var $tweetsContainer = $('#tweets-container');

        if ( $tweetsContainer.children().first().data('id') === data.tweets.slice(-1)[0]["tweet_id"] ) {
            return;
        }

        data.tweets.map(function (tweet) {
            var html =
                "<div class='tweet' data-id='" + tweet["tweet_id"] + "'>" +
                "  <div class='tweet-image'>" +
                "    <a href='http://twitter.com/" + tweet["screen_name"] + "'>" +
                "    <img src='" + tweet["profile_image_url"] + "' width='48' height='48'></a>" +
                "  </div>" +
                "  <div class='tweet-right'>" +
                "    <div class='tweet-screen-name'>" +
                "      <a href='http://twitter.com/" + tweet["screen_name"] + "'>" + tweet["screen_name"] + "</a>" +
                "      <span class='tweet-name'>" + tweet["name"] + "</span>" +
                "    </div>" +
                "    <div class='tweet-text'>" + tweetTextWrapper(tweet["tweet_text"]) +
                "      <div class='tweet-date'>" +
                "        <a href='http://twitter.com/" + tweet["screen_name"] + "/status/" + tweet["tweet_id"] + "'>" +
                         tweet["created_at"] + "</a>" +
                "      </div>" +
                "    </div>" +
                "  </div>";

            $tweetsContainer.prepend(html);

            if ( $tweetsContainer.children().length <= data.count ) {
                return;
            }
            $tweetsContainer.children().last().remove();
        });
    }

    function tweetTextWrapper(text) {
        var linkRegex = /(?:(https?\:\/\/[^\s]+))/ig;
        var mentionRegex = /\B@(\w+(?!\/))\b/ig;
        var hashtagRegex = /(^|\W)#([a-z\d][\w-]*)/ig;
        text = linkRegex.test(text) ?
            text.replace(linkRegex,'<a href="$1">$1</a>') : text;
        text = mentionRegex.test(text) ?
            text.replace(mentionRegex,'<a href="https://twitter.com/$1">@$1</a>') : text;
        text = hashtagRegex.test(text) ?
            text.replace(hashtagRegex,'<a href="https://twitter.com/search?q=%23$2">#$2</a>') : text;
        return text;
    }

})(window.jQuery);