<?php

/**
 * @package GPL Cart core
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */
return array(
    'visit_date' => array(
        'name' => /* @text */'Visits by date',
        'query' => array(
            'metrics' => 'ga:visits',
            'dimensions' => 'ga:date',
        )
    ),
    'visit_country' => array(
        'name' => /* @text */'Visits by countries',
        'query' => array(
            'sort' => '-ga:visits',
            'metrics' => 'ga:visits',
            'dimensions' => 'ga:country'
        )
    ),
    'visit_city' => array(
        'name' => /* @text */'Visits by cities',
        'query' => array(
            'sort' => '-ga:visits',
            'metrics' => 'ga:visits',
            'dimensions' => 'ga:city'
        )
    ),
    'visit_language' => array(
        'name' => /* @text */'Visits by languages',
        'query' => array(
            'sort' => '-ga:visits',
            'metrics' => 'ga:visits',
            'dimensions' => 'ga:language'
        )
    ),
    'visit_browser' => array(
        'name' => /* @text */'Visits by browsers',
        'query' => array(
            'sort' => '-ga:visits',
            'metrics' => 'ga:visits',
            'dimensions' => 'ga:browser'
        )
    ),
    'visit_os' => array(
        'name' => /* @text */'Visits by OS',
        'query' => array(
            'sort' => '-ga:visits',
            'metrics' => 'ga:visits',
            'dimensions' => 'ga:operatingSystem'
        )
    ),
    'visit_resolution' => array(
        'name' => /* @text */'Visits by screen resolution',
        'query' => array(
            'sort' => '-ga:visits',
            'metrics' => 'ga:visits',
            'dimensions' => 'ga:screenResolution'
        )
    ),
    'visit_mobile_os' => array(
        'name' => /* @text */'Visits by mobile OS',
        'query' => array(
            'sort' => '-ga:visits',
            'segment' => 'gaid::-11',
            'metrics' => 'ga:visits',
            'dimensions' => 'ga:operatingSystem'
        )
    ),
    'visit_mobile_resolution' => array(
        'name' => /* @text */'Visits by mobile resolution',
        'query' => array(
            'sort' => '-ga:visits',
            'segment' => 'gaid::-11',
            'metrics' => 'ga:visits',
            'dimensions' => 'ga:screenResolution'
        )
    ),
    'pageview_date' => array(
        'name' => /* @text */'Pageviews by date',
        'query' => array(
            'dimensions' => 'ga:date',
            'metrics' => 'ga:pageviews'
        )
    ),
    'content_statistic' => array(
        'name' => /* @text */'Content statistic',
        'query' => array(
            'metrics' => 'ga:pageviews,ga:uniquePageviews'
        )
    ),
    'top_pages' => array(
        'name' => /* @text */'Top pages',
        'query' => array(
            'sort' => '-ga:pageviews',
            'metrics' => 'ga:pageviews',
            'dimensions' => 'ga:pagePath'
        )
    ),
    'source' => array(
        'name' => /* @text */'Traffic sources',
        'query' => array(
            'metrics' => 'ga:visits',
            'dimensions' => 'ga:medium',
        )
    ),
    'keyword' => array(
        'name' => /* @text */'Keywords',
        'query' => array(
            'sort' => '-ga:visits',
            'metrics' => 'ga:visits',
            'dimensions' => 'ga:keyword'
        )
    ),
    'referral' => array(
        'name' => /* @text */'Referrals',
        'query' => array(
            'sort' => '-ga:visits',
            'metrics' => 'ga:visits',
            'dimensions' => 'ga:source'
        )
    ),
    'audience' => array(
        'name' => /* @text */'Audience',
        'query' => array(
            'metrics' => 'ga:visitors,ga:newVisits,ga:percentNewVisits,ga:visits,ga:pageviews,ga:bounces,ga:visitBounceRate,ga:timeOnSite,ga:avgTimeOnSite',
        )
    )
);
