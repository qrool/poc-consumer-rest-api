<?php
define('APP_ROOT', dirname(__DIR__));
require APP_ROOT . '/vendor/autoload.php';

use App\Config;
use App\Services\AggregatedPosts;

$config = new Config();
$flatConfig = $config->getConfig();

switch ($_SERVER['REQUEST_URI']) {
    case '/query/posts/stats':
        echo '/query/posts/stats';

        // Post Stats
        $aggregatedPosts = new AggregatedPosts($flatConfig['aggregatedStorage']['path']);

        // props
        $dataSource = $flatConfig['post']['data_source'];
        $scopeMonth = 'month';
        $scopeWeek = 'week';

        // Average character length of posts per month
        $averageChars = $aggregatedPosts->getAverageChars($dataSource, $scopeMonth);

        echo '<h4>Average character length of posts per month - source : ' . $dataSource . '</h4>';
        echo '<b> month / year | value </b>';
        foreach ($averageChars as $record) {
            echo '<p>';
            echo $record['month'] . ' / ' . $record['year'];
            echo ' | ';
            echo $record['aggregated_value'];
            echo '</p>';
        }

        // Longest post by character length per month
        $longestPostByChar = $aggregatedPosts->getLongestPostByChar($dataSource, $scopeMonth);
        echo '<h4>Longest post by character length per month - source : ' . $dataSource . '</h4>';
        echo '<b> month / year | value </b>';
        foreach ($longestPostByChar as $record) {
            echo '<p>';
            echo $record['month'] . ' / ' . $record['year'];
            echo ' | ';
            echo $record['aggregated_value'];
            echo '</p>';
        }

        // Total posts split by week number
        $totalPosts = $aggregatedPosts->getTotalPosts($dataSource, $scopeWeek);
        echo '<h4>Total posts split by week number - source : ' . $dataSource . '</h4>';
        echo '<b> week / year | value </b>';
        foreach ($totalPosts as $record) {
            echo '<p>';
            echo $record['week'] . ' / ' . $record['year'];
            echo ' | ';
            echo $record['aggregated_value'];
            echo '</p>';
        }

        // Average number of posts per user per month
        $totalPosts = $aggregatedPosts->getAveragePostsPerUser($dataSource, $scopeMonth);
        echo '<h4>Average number of posts per user per month - source : ' . $dataSource . '</h4>';
        echo '<b> month / year | value </b>';
        foreach ($totalPosts as $record) {
            echo '<p>';
            echo $record['month'] . ' / ' . $record['year'];
            echo ' | ';
            echo $record['aggregated_value'];
            echo '</p>';
        }

        break;
    case '/':
    default:
        echo 'Query or not to Query, this is the question ;)';
}
