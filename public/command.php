<?php
define('APP_ROOT', dirname(__DIR__));
require APP_ROOT . '/vendor/autoload.php';

use App\Config;
use App\ApiClient;

use App\Services\Posts;
use App\Services\Auth;
use App\Services\States;
use App\Services\AggregatedPosts;

$config = new Config();
$flatConfig = $config->getConfig();

switch ($_SERVER['REQUEST_URI']) {
    case '/command/auth/authorise':

        // only implemented to check it can get accessToken
        $auth = new Auth($flatConfig['auth'], $flatConfig['authStorage']['path']);
        print $auth->getToken();
        break;

    case '/command/posts/process':
        echo '/command/posts/process';

        // Auth
        // get Token, usually this would be handled by a middleware
        $auth = new Auth($flatConfig['auth'], $flatConfig['authStorage']['path']);
        $accessToken = $auth->getToken();

        // State
        /*
         * TODO: State currently take care about pages only but if the page does not have full list of records
         * ie. 100 records but 50. This page would needed to be restarted. State currently does not keep info about records per page.
         */
        $state = new States($flatConfig['storage']['path']);

        // Post
        $post = new Posts($flatConfig['storage']['path']);

        $page = 0;
        $maxPages = 4;
        $dataTypeState = 'post';

        $dataSource = $flatConfig['post']['data_source'];
        $counter = $flatConfig['post']['counter'];

        $mapping = ['id', 'from_name', 'from_id', 'message', 'type', 'created_time']; // set the map of the fields of fetched posts

        // API Client
        $ApiClient = new ApiClient();
        //$ApiClient->setDataOnly(true);

        $url = $flatConfig['post']['url'];
        $data = [$flatConfig['auth']['access_token_name'] => $accessToken];

        do {
            $data[$counter] = $page++;

            $fetchedPosts = $ApiClient->get($url, $data); // fetch data from the endpoint

            $fetchedRequestID = $fetchedPosts['meta']['request_id'] ?? false;

            if (is_string($fetchedRequestID) && $existingState = $state->getExistingState($dataSource, $dataTypeState, $fetchedRequestID))
            {
                $requestId = $existingState['request_id'] ?? false;

                if(!empty($requestId) && $requestId == $fetchedRequestID)
                {
                    continue;
                }
            }

            $enrichedPosts = $post->enrichPosts($dataSource, $mapping, $fetchedPosts['data']['posts']);

            $post->storeBulkPosts($enrichedPosts);

            $state->storeOrUpdateState($dataSource, $dataTypeState, $counter, $page, $fetchedRequestID); // update state to not perform fetching the same page

        } while ($page < $maxPages);

        break;

    case '/command/posts/aggregate':
        echo '/command/posts/aggregate';

        // TODO: improvement to gathering data by replacing day,week,month,year with scope_from and scope_to
        // TODO: optimisation would be to get daily aggregated data from aggregated table to generate monthly aggregation

        // Post
        $post = new Posts($flatConfig['storage']['path']);
        $flatConfig = $config->getConfig();

        $dataSource = $flatConfig['post']['data_source'];

        // Aggregate Posts
        $aggregatedPosts = new AggregatedPosts($flatConfig['aggregatedStorage']['path']);

        // start aggregating from
        $startYear = 2021;
        $startMonth = 1;
        $startWeek = 1;

        // Average character length of posts per month
        $aggregatedPosts->processAggregated($dataSource, 'averageChar', 'month', $post->getAverageCharPerMonth($startYear, $startMonth));

        // Longest post by character length per month
        $aggregatedPosts->processAggregated($dataSource, 'longestPostByChar', 'month', $post->getlongestPostPerMonth($startYear, $startMonth));

        // Total posts split by week number
        $aggregatedPosts->processAggregated($dataSource, 'totalPosts', 'week', $post->getTotalPostsPerWeek($startYear, $startWeek));

        // Average number of posts per user per month
        $aggregatedPosts->processAggregated($dataSource, 'averagePostsPerUser', 'month', $post->getAveragePostsPerUserPerMonth($startYear, $startMonth));

        break;

    case '/':
    default:
        echo 'Commander ;)';
}
