<?php
use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;


require 'config.php';
require 'vendor/autoload.php';
require 'controller/common.php';
require 'controller/wholeSeller.php';
require 'controller/stock.php';
require 'controller/dashboard.php';
require 'controller/user.php';

//including all models dynamicly
function my_autoloader($className) {
	$arr = preg_split('/(?=[A-Z])/', $className);
	$arr = array_slice($arr, 1);
	$path = false;
	$path = "model/" . $className . ".php";
	if (file_exists($path)) {
		require_once $path;
	}
}
spl_autoload_register("my_autoloader");


$app = new \Slim\App(['settings' => ['displayErrorDetails' => true]]);
$app->post('/whole-seller-reciept', 'WholeSellerController::wholeSellerReciept');
$app->get('/get-reciept-data', 'WholeSellerController::getReciepts');
$app->get('/get-remaining-balance', 'WholeSellerController::getRemainingBalance');
$app->post('/add-series-stock', 'StockController::addSeriesStock');
$app->post('/add-channel-stock', 'StockController::addChannelStock');
$app->get('/get-stocks', 'StockController::getStock');
$app->get('/get-stocks-for-invoice', 'StockController::getStockForInvoice');
$app->get('/get-sheets', 'StockController::getSheet');
$app->get('/get-sheets-types', 'StockController::getSheetType');
$app->post('/add-sheet', 'StockController::addSheet');
$app->post('/add-sheet-type', 'StockController::addSheetType');
$app->post('/add-series', 'StockController::addSeries');
$app->get('/get-stats', 'DashboardController::getStats');
$app->get('/login', 'userController::login');
$app->post('/edit-channel', 'StockController::editChannel');
$app->post('/edit-stock', 'StockController::editStock');
$app->post('/edit-type', 'StockController::editTypeWithSeries');
$app->post('/edit-series', 'StockController::editSeries');
$app->post('/edit-stock-with-type', 'StockController::editStockWithType');
$app->post('/delete-channel', 'StockController::deleteChannel');
$app->post('/delete-stock', 'StockController::deleteStock');
$app->post('/delete-type', 'StockController::deleteType');
$app->post('/delete-series', 'StockController::deleteSeries');
$app->run();
 