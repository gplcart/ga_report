[![Build Status](https://scrutinizer-ci.com/g/gplcart/ga_report/badges/build.png?b=master)](https://scrutinizer-ci.com/g/gplcart/ga_report/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gplcart/ga_report/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gplcart/ga_report/?branch=master)

Google Analytics Report is a [GPL Cart](https://github.com/gplcart/gplcart) that allows administrators to browse various Google Analytics reports right in admin area.

The following reports are supported out-of-box:

- Visits by date
- Visits by countries
- Visits by cities
- Visits by languages
- Visits by browsers
- Visits by OS
- Visits by screen resolution
- Visits by mobile OS
- Visits by mobile resolution
- Pageviews by date
- Content statistic
- Top pages
- Traffic sources
- Keywords
- Referrals
- Audience

You can extend this list by writing your own handler using hook `module.ga.report.handlers`.

All reports are displayed in plain tables, but you can install an [Chart module](https://github.com/gplcart/chart) to turn them into nice charts



NOTE: In order to use this module you have to get `Service Account Key` from [Google API console](https://console.developers.google.com/apis/credentials)


**Installation**

1. Download and extract to `system/modules` manually or using composer `composer require gplcart/ga_report`. IMPORTANT: If you downloaded the module manually, be sure that the name of extracted module folder doesn't contain a branch/version suffix, e.g `-master`. Rename if needed.
2. Go to `admin/module/list` end enable the module
3. Go to `admin/module/settings/ga_report`, specify your Google API credentials and adjust other settings
4. Go to `admin/user/role/edit`, select a user role to allow access to see the reports, click "Edit", select `Google Analytics reports: access` permission

**Usage**

All available reports are shown on `admin/report/ga`. You can also add panels on your dashboard panel