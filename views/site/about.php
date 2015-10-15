<?php

/* @var $this yii\web\View */

$this->title = 'LinkoScope';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>LinkoScope</h1>
    </div>

    <div class="body-content">

        <p>
            This WebApp is a clone of
            <a href="https://news.ycombinator.com/" target="_blank">Hacker News</a>
            that uses WordPress APIs for its back-end storage.
            It can be configured to use the
            <a href="https://developer.wordpress.com/docs/api/" target="_blank">WordPress.com API</a>
            or the
            <a href="http://v2.wp-api.org/" target="_blank"> WP-API plugin API</a>.
        </p>

        <p>
            The original HN scoring is: penalties * (votes - 1)^0.8 / (age_in_hours + 2)^1.8.
        </p>

        <p>
            For simplicity we use: age_in_seconds + votes * factor.
            Where factor is currently 1 day in seconds.
        </p>

    </div>
</div>
