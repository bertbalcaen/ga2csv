ga2csv
======

PHP script to turn Google Analytics data for a list of sites into CSVs

* One CSV per site.
* Each CSV row corresponds to one day. Columns: 'date', 'visits', 'new_visits', 'visitors'.

Configuration
=============

Edit the configuration section of ga2csv.php:

* Set the email and password you use for GA.
* Edit start and end date.
* $sites is an array of sites. You need to specify the GA table id. You can find this in the URL. For example, if the URL is https://www.google.com/analytics/web/?hl=en#report/visitors-overview/a48690296w77739914p81395189/ then 81395189 is the table id. Give the site a name - this will be used to name the CSV.

Usage
=====

php ga2scv.php

The script will create a CSV file for each site in the data directory, and also a file called totals.csv with the total visits for each site.