<?php

/**
 * @package Google Analytics Report
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */
return array(
    'visit_date' => array(
        'name' => 'Visits by date', // @text
        'template' => 'ga_report|panels/visit_date',
        'query' => array(
            'metrics' => 'ga:pageviews',
            'dimensions' => 'ga:date',
            'date' => array('30DaysAgo', 'today')
        )
    ),
    'visit_country' => array(
        'name' => 'Visits by countries', // @text
        'template' => 'ga_report|panels/visit_country',
        'query' => array(
            'sort' => array('ga:pageviews' => array('value', 'descending')),
            'metrics' => 'ga:pageviews',
            'dimensions' => 'ga:country',
            'date' => array('30DaysAgo', 'today')
        )
    ),
    'visit_city' => array(
        'name' => 'Visits by cities', // @text
        'template' => 'ga_report|panels/visit_city',
        'query' => array(
            'sort' => array('ga:pageviews' => array('value', 'descending')),
            'metrics' => 'ga:pageviews',
            'dimensions' => 'ga:city',
            'date' => array('30DaysAgo', 'today')
        )
    ),
    'visit_language' => array(
        'name' => 'Visits by languages', // @text
        'template' => 'ga_report|panels/visit_language',
        'query' => array(
            'sort' => array('ga:pageviews' => array('value', 'descending')),
            'metrics' => 'ga:pageviews',
            'dimensions' => 'ga:language',
            'date' => array('30DaysAgo', 'today')
        )
    ),

    'visit_browser' => array(
        'name' => 'Visits by browsers', // @text
        'template' => 'ga_report|panels/visit_browser',
        'query' => array(
            'sort' => array('ga:pageviews' => array('value', 'descending')),
            'metrics' => 'ga:pageviews',
            'dimensions' => 'ga:browser',
            'date' => array('30DaysAgo', 'today')
        )
    ),
    'visit_os' => array(
        'name' => 'Visits by OS', // @text
        'template' => 'ga_report|panels/visit_os',
        'query' => array(
            'sort' => array('ga:pageviews' => array('value', 'descending')),
            'metrics' => 'ga:pageviews',
            'dimensions' => 'ga:operatingSystem',
            'date' => array('30DaysAgo', 'today')
        )
    ),
    'visit_resolution' => array(
        'name' => 'Visits by screen resolution', // @text
        'template' => 'ga_report|panels/visit_resolution',
        'query' => array(
            'sort' => array('ga:pageviews' => array('value', 'descending')),
            'metrics' => 'ga:pageviews',
            'dimensions' => 'ga:screenResolution',
            'date' => array('30DaysAgo', 'today')
        )
    ),
    'source' => array(
        'name' => 'Traffic sources', // @text
        'template' => 'ga_report|panels/source',
        'query' => array(
            'sort' => array('ga:pageviews' => array('value', 'descending')),
            'metrics' => 'ga:pageviews',
            'dimensions' => array('ga:medium', 'ga:source', 'ga:campaign'),
            'date' => array('30DaysAgo', 'today')
        )
    ),
    'keyword' => array(
        'name' => 'Keywords', // @text
        'template' => 'ga_report|panels/keyword',
        'query' => array(
            'sort' => array('ga:pageviews' => array('value', 'descending')),
            'metrics' => 'ga:pageviews',
            'dimensions' => 'ga:keyword',
            'date' => array('30DaysAgo', 'today')
        )
    ),
    'speed' => array(
        'name' => 'Site speed', // @text
        'template' => 'ga_report|panels/speed',
        'query' => array(
            'sort' => array('ga:pageLoadTime' => array('value', 'descending')),
            'metrics' => array('ga:pageLoadTime', 'ga:serverConnectionTime', 'ga:serverResponseTime'),
            'dimensions' => array('ga:fullReferrer'),
            'date' => array('30DaysAgo', 'today')
        )
    ),
    'referral' => array(
        'name' => 'Referrals', // @text
        'template' => 'ga_report|panels/referral',
        'query' => array(
            'sort' => array('ga:pageviews' => array('value', 'descending')),
            'metrics' => 'ga:pageviews',
            'dimensions' => array('ga:fullReferrer'),
            'date' => array('30DaysAgo', 'today')
        )
    ),
    'content_statistic' => array(
        'name' => 'Content statistic', // @text
        'template' => 'ga_report|panels/content_statistic',
        'query' => array(
            'metrics' => array(
                'ga:pageviews',
                'ga:uniquePageviews',
                'ga:avgTimeOnPage',
                'ga:pageviewsPerSession',
                'ga:entranceRate',
                'ga:exitRate'
            ),
            'date' => array('30DaysAgo', 'today')
        )
    ),
);
