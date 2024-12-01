<?php
/**
 * CSV Exporter for Hitcount
 */
include('../../../gsconfig.php');
$admin = defined('GSADMIN') ? GSADMIN : 'admin';
include("../../../${admin}/inc/common.php");
$loggedin = cookie_check();
if (!$loggedin) die("Not logged in!");

require_once('../reader.class.php');
require_once('../exporter.class.php');
define('HITCOUNT_INDEX_DIR','hitcount_index/');
i18n_merge('hitcount','en');

# get parameters from request
$details = isset($_REQUEST['details']);
if (isset($_REQUEST['tab']) && $_REQUEST['tab'] != 'slug') {
  $tab = $_REQUEST['tab'];
  $hasDetails = in_array($tab,array('browser','lang','os'));
  $name = $tab.($details && $hasDetails ? '_d' : '');
  $names = array($name => null);
} else {
  $tab = 'slug';
  $names = array('total' => null, 'slug' => null);
}
$sep = isset($_REQUEST['excel']) ? ';' : ',';
$from = @$_REQUEST['from'];
$to = @$_REQUEST['to'];
# read data
$reader = new HitcountReader($from, $to);
$reader->read($names);
$reader->sort();
$dates = $reader->getDates();
$hits = $reader->getHits();
$visits = $reader->getVisits();
# output CSV
if ($sep == ',') {
  header('Content-Type: text/csv; charset=ISO-8859-1');
} else {
  header('Content-Type: application/vnd.ms-excel');
}
header('Content-Disposition: attachment; filename=hitcount-'.$tab.'-'.strftime('%Y%m%d').'.csv');
HitcountExporter::exportCSV($dates,$hits,$visits,$sep);
die;

  