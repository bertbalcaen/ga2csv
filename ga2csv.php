<?php
// CONFIG START
date_default_timezone_set("Europe/Brussels");

$email = 'YOUR_EMAIL';
$password = 'YOUR_PASSWORD';

$startDate = strtotime('January 1st, 2013');
$endDate   = strtotime('today');

$sites = array(
	array(
		'table_id' => 'GA_TABLE_ID',
		'name' => 'SITE_NAME',
	),
);

// CONFIG END

$paths = array(
    realpath(dirname(__FILE__) . '/ZendFramework-1.12.3/library'),
    '.',
);
set_include_path(implode(PATH_SEPARATOR, $paths));

require_once('Zend/Gdata/Analytics.php');
require_once('Zend/Gdata/ClientLogin.php');
 
$service   = Zend_Gdata_Analytics::AUTH_SERVICE_NAME;
$client    = Zend_Gdata_ClientLogin::getHttpClient($email, $password, $service);
$analytics = new Zend_Gdata_Analytics($client);

$totals = array();

foreach ($sites as $site) {

	$profileID = $site['table_id'];
	$name = $site['name'];
	print $name . PHP_EOL;

	$query = $analytics->newDataQuery()
	       ->setProfileId($profileID)
		->addMetric(Zend_Gdata_Analytics_DataQuery::METRIC_VISITS)
		->addMetric(Zend_Gdata_Analytics_DataQuery::METRIC_NEW_VISITS)
		->addMetric(Zend_Gdata_Analytics_DataQuery::METRIC_VISITORS)
		->addDimension(Zend_Gdata_Analytics_DataQuery::DIMENSION_DATE)
		->setStartDate(date('Y-m-d', $startDate))
		->setEndDate(date('Y-m-d', $endDate))
		->setMaxResults(1000);
	$result = $analytics->getDataFeed($query);

	$data = array();
	$dateHere = $startDate;
	$total = 0;
	foreach($result as $row) {

		$date = date('d/m/y', $dateHere);

	    $data[] = array(
			$date,
			$row->getMetric(Zend_Gdata_Analytics_DataQuery::METRIC_VISITS)->getValue(),
			$row->getMetric(Zend_Gdata_Analytics_DataQuery::METRIC_NEW_VISITS)->getValue(),
			$row->getMetric(Zend_Gdata_Analytics_DataQuery::METRIC_VISITORS)->getValue(),
	    );

	    $total += $row->getMetric(Zend_Gdata_Analytics_DataQuery::METRIC_VISITS)->getValue();	

	    $dateHere += 86400;

	}

	$totals[] = array($name, $total);

	$fp = fopen('data/' . $name . '.csv', 'w');
	if (!$fp) {
		print 'Could not open file'; 
		exit;
	}
	$headers = array('date', 'visits', 'new_visits', 'visitors');
	fputcsv($fp, $headers);
	foreach ($data as $row) {
		fputcsv($fp, $row);
	}

}

$fp = fopen('data/totals.csv', 'w');
if (!$fp) {
	print 'Could not open file'; 
	exit;
}
$headers = array('site', 'total visits');
fputcsv($fp, $headers);
foreach ($totals as $row) {
	fputcsv($fp, $row);
}

print 'Ready' . PHP_EOL;