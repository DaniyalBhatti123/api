<?php
    class DashboardController {
        public static function getStats($request,$response,$args){
            $startDate = date('Y-m-1 00:00:00');
            $endDate = date('Y-m-t 23:59:59');
            $totalPrice = Model::Factory('customer')->where_gte('is_created',$startDate)->where_lte('is_created',$endDate)->sum('total');
            $givingBalance = Model::Factory('customer')->where_gte('is_created',$startDate)->where_lte('is_created',$endDate)->sum('giving_balance');
            $remaingBalance = Model::Factory('customer')->where_gte('is_created',$startDate)->where_lte('is_created',$endDate)->sum('remaining_balance');
            $count = count(Model::Factory('customer')->where_gte('is_created',$startDate)->where_lte('is_created',$endDate)->find_array());
            $channelStock = Model::Factory('channelStock')->find_array();
            $sheetStock = Model::Factory('sheetsstock')->find_array();

            $channelData = array();
            for($x = 0; $x < sizeof($channelStock); $x++){
                $qtySum = Model::Factory('channel')->where('name',$channelStock[$x]['item'])->where_gte('is_created',$startDate)->where_lte('is_created',$endDate)->sum('qty');
                $channelData[$channelStock[$x]['item']] = ($qtySum) ? $qtySum : 0;
            }

            $sheetData = array();
            for($x = 0; $x < sizeof($sheetStock); $x++){
                $qtySum = Model::Factory('sheets')->where('sheets',$sheetStock[$x]['item'])->where_gte('is_created',$startDate)->where_lte('is_created',$endDate)->sum('qty');
                $sheetData[$sheetStock[$x]['item']] = ($qtySum) ? $qtySum : 0;
            }

            $data = array('totalInvoices'=>$count, 'totalPrice'=>$totalPrice, 'givingBalance'=>$givingBalance, 'remainingBalance'=>$remaingBalance, 'channelData'=>$channelData, "sheetData"=>$sheetData);

            if($data) {
                CommonController::sendBodyResponseWithCode(200,'OK','Data Found',$data,$response);
            }else CommonController::sendResponseWithCode(200,'FAIL','No data found',$response);
        }
    }
?>