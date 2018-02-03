[![Build Status](https://scrutinizer-ci.com/g/gplcart/ga_report/badges/build.png?b=master)](https://scrutinizer-ci.com/g/gplcart/ga_report/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gplcart/ga_report/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gplcart/ga_report/?branch=master)

Google Analytics Report is a [GPL Cart](https://github.com/gplcart/gplcart) that allows administrators to browse various Google Analytics reports right in admin area.
All reports are displayed in plain tables, but you can install the [Chart module](https://github.com/gplcart/chart) to turn them into nice charts

**Dependencies:**

- [Google API](https://github.com/gplcart/gapi)

**Installation**

1. Download and extract to `system/modules` manually or using composer `composer require gplcart/ga_report`. IMPORTANT: If you downloaded the module manually, be sure that the name of extracted module folder doesn't contain a branch/version suffix, e.g `-master`. Rename if needed.
2. Go to `admin/module/list` end enable the module
3. Go to `admin/module/settings/ga_report`, specify your Google API credentials and adjust other settings
4. Go to `admin/user/role/edit`, select a user role to allow access to see the reports, click "Edit", select `Google Analytics reports: access` permission

All available reports are shown on `admin/report/ga`. You can also add panels to your dashboard