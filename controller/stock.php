<?php
    class StockController {
        public static function addSeriesStock($request,$response,$args){
            $postData = $request->getParams();
            $date = date('Y-m-d h:i:sa');
            $isArrayComplete = false;
            for($x=0; $x < sizeof($postData); $x++) {
                $stocks = Model::Factory('series')->create();
                $stocks->id = '';
                $stocks->sheet_type_id = $postData[$x]['sheetTypeId'];
                $stocks->name = $postData[$x]['name'];
                $stocks->qty = $postData[$x]['qty'];
                $stocks->is_deleted = 0;
                $stocks->is_created = $date;
                $stocks->save();
                if($x == sizeof($postData)-1) $isArrayComplete = true;
            }

            if($isArrayComplete == true) {
                CommonController::sendResponseWithCode(200,'OK','Stocks added successfully',$response);
            } else CommonController::sendResponseWithCode(403,'FAIL','Internal Server Error',$response);
        }

        public static function addChannelStock($request,$response,$args){
            $postData = $request->getParams();
            $date = date('Y-m-d h:i:sa');
            $isArrayComplete = false;
            for($x=0; $x < sizeof($postData); $x++) {
                $stocks = Model::Factory('channelStock')->create();
                $stocks->id = '';
                $stocks->item = $postData[$x]['name'];
                $stocks->size = $postData[$x]['size'];
                $stocks->qty = $postData[$x]['qty'];
                $stocks->rate = $postData[$x]['rate'];
                $stocks->isEdit = ($postData[$x]['isEdit'] == true) ? 1 : 0;
                $stocks->is_deleted = 0;
                $stocks->is_created = $date;
                $stocks->save();
                if($x == sizeof($postData)-1) $isArrayComplete = true;
            }
            if($isArrayComplete == true) {
                CommonController::sendResponseWithCode(200,'OK','Stocks added successfully',$response);
            } else CommonController::sendResponseWithCode(403,'FAIL','Internal Server Error',$response);
        }

        public static function getStock($request,$response,$args){
            $channelData = Model::Factory('channelStock')->find_array();
            $sheetsData = Model::Factory('sheetsstock')->find_array();
            for($x = 0; $x < sizeof($sheetsData); $x++) {
                if($sheetsData[$x]['is_type']) {
                    $sheetsType = Model::Factory('sheetType')->where('sheet_id',$sheetsData[$x]['id'])->find_array();
                    $sheetsData[$x]['type'] = $sheetsType;
                    for($y = 0; $y < sizeof($sheetsType); $y++){
                        $series = Model::Factory('series')->where('sheet_type_id',$sheetsType[$y]['id'])->find_array();
                        $sheetsData[$x]['type'][$y]['sheets'] = $series;
                    }
                }
            }
            $body;
            $body['channelData'] = $channelData;
            $body['sheetData'] = $sheetsData;
            if($body){
                CommonController::sendBodyResponseWithCode(200,'OK','Channel Found',$body,$response);
            } else CommonController::sendBodyResponseWithCode(200,'OK','No Record Found',array('channelData'=>[], 'sheetData'=>[]),$response);
        }
        
        public static function getStockForInvoice($request,$response,$args){
            $channelData = Model::Factory('channelStock')->where('is_deleted',0)->find_array();
            $sheetsData = Model::Factory('sheetsstock')->where('is_deleted',0)->find_array();
            for($x = 0; $x < sizeof($sheetsData); $x++) {
                if($sheetsData[$x]['is_type']) {
                    $sheetsType = Model::Factory('sheetType')->where('sheet_id',$sheetsData[$x]['id'])->where('is_deleted',0)->find_array();
                    $sheetsData[$x]['type'] = $sheetsType;
                    for($y = 0; $y < sizeof($sheetsType); $y++){
                        $series = Model::Factory('series')->where('sheet_type_id',$sheetsType[$y]['id'])->where('is_deleted',0)->find_array();
                        $sheetsData[$x]['type'][$y]['sheets'] = $series;
                    }
                }
            }
            $body;
            $body['channelData'] = $channelData;
            $body['sheetData'] = $sheetsData;
            if($body){
                CommonController::sendBodyResponseWithCode(200,'OK','Channel Found',$body,$response);
            } else CommonController::sendBodyResponseWithCode(200,'OK','No Record Found',array('channelData'=>[], 'sheetData'=>[]),$response);
        }

        public static function getSheetType($request,$response,$args){
            $sheets = Model::Factory('sheetType')->where('is_sheet',1)->find_array();
            if($sheets) {
                CommonController::sendBodyResponseWithCode(200,'OK','Sheet type Found',$sheets,$response);
            } else CommonController::sendBodyResponseWithCode(200,'OK','No Record Found',[],$response);
        }

        public static function getSheet($request,$response,$args){
            $sheets = Model::Factory('sheetsstock')->where('is_type',1)->find_array();
            if($sheets) {
                CommonController::sendBodyResponseWithCode(200,'OK','Sheets Found',$sheets,$response);
            } else CommonController::sendBodyResponseWithCode(200,'OK','No Record Found',[],$response);
        }

        public static function addSheet($request,$response,$args){
            $postData = $request->getParams();
            $addSheet = Model::Factory('sheetsstock')->create();
            $date = date('Y-m-d h:i:sa');

            $addSheet->id = '';
            $addSheet->item = $postData['item'];
            $addSheet->is_type = ($postData['isType'] == true) ? 1 : 0;
            $addSheet->qty = isset($postData['qty']) ? $postData['qty'] : '';
            $addSheet->price = isset($postData['price']) ? $postData['price'] : '';
            $addSheet->is_deleted = 0;
            $addSheet->is_created = $date;

            if($addSheet->save()) {
                CommonController::sendResponseWithCode(200,'OK','Sheet added successfully',$response);
            } else CommonController::sendResponseWithCode(403,'FAIL','Internal Server Error',$response);
        }

        public static function addSheetType($request,$response,$args){
            $postData = $request->getParams();
            $addSheetType = Model::Factory('sheetType')->create();
            $date = date('Y-m-d h:i:sa');

            $addSheetType->id = '';
            $addSheetType->sheet_id = $postData['sheetId'];
            $addSheetType->type_name = $postData['typeName'];
            $addSheetType->rate = $postData['rate'];
            $addSheetType->qty = isset($postData['qty']) ? $postData['qty'] : '';
            $addSheetType->is_sheet = ($postData['isSheet'] == true) ? 1 : 0;
            $addSheetType->is_deleted = 0;
            $addSheetType->is_created = $date;

            if($addSheetType->save()) {
                CommonController::sendResponseWithCode(200,'OK','Sheet Type added successfully',$response);
            } else CommonController::sendResponseWithCode(403,'FAIL','Internal Server Error',$response);
        }

        public static function addSeries($request,$response,$args){
            $postData = $request->getParams();
            $date = date('Y-m-d h:i:sa');

            $isArrayComplete = false;
            for($x = 0; $x < sizeof($postData); $x++){
                $addSeries = Model::Factory('series')->create();

                $addSeries->id = '';
                $addSeries->sheet_type_id = $postData[$x]['sheetTypeId'];
                $addSeries->name = $postData[$x]['typeName'];
                $addSeries->qty = $postData[$x]['qty'];
                $addSeries->is_deleted = 0;
                $addSeries->is_created = $date;

                $addSeries->save();
            }

            if($isArrayComplete) {
                CommonController::sendResponseWithCode(200,'OK','Sheet Type added successfully',$response);
            } else CommonController::sendResponseWithCode(403,'FAIL','Internal Server Error',$response);
        }

        public static function editChannel($request,$response,$args){
            $postData = $request->getParams();

            $channel = Model::Factory('channelStock')->where('id',$postData['id'])->find_one();

            $channel->item = $postData['item'];
            $channel->size = $postData['size'];
            $channel->qty = $postData['qty'];
            $channel->rate = $postData['rate'];

            if($channel->save()){
                CommonController::sendResponseWithCode(200,'OK','Channel updated successfully',$response);
            } else CommonController::sendResponseWithCode(403,'FAIL','Internal Server Error',$response);
        }
        
        public static function deleteChannel($request,$response,$args){
            $postData = $request->getParams();

            $channel = Model::Factory('channelStock')->where('id',$postData['id'])->find_one();

            $channel->is_deleted = $postData['isDeleted'];

            if($channel->save()){
                if($postData['isDeleted'] == 1){
                    CommonController::sendResponseWithCode(200,'OK','Channel hided successfully',$response);
                } else {
                    CommonController::sendResponseWithCode(200,'OK','Channel unhided successfully',$response);
                }
            } else CommonController::sendResponseWithCode(403,'FAIL','Internal Server Error',$response);
        }

        public static function editStock($request,$response,$args){
            $postData = $request->getParams();

            $stock = Model::Factory('sheetsstock')->where('id',$postData['id'])->find_one();

            $stock->item = $postData['item'];
            $stock->qty = $postData['qty'];
            $stock->price = $postData['price'];

            if($stock->save()){
                CommonController::sendResponseWithCode(200,'OK','Stock updated successfully',$response);
            } else CommonController::sendResponseWithCode(403,'FAIL','Internal Server Error',$response);
        }
        
        public static function editStockWithType($request,$response,$args){
            $postData = $request->getParams();

            $stock = Model::Factory('sheetsstock')->where('id',$postData['id'])->find_one();

            $stock->item = $postData['item'];

            if($stock->save()){
                CommonController::sendResponseWithCode(200,'OK','Stock updated successfully',$response);
            } else CommonController::sendResponseWithCode(403,'FAIL','Internal Server Error',$response);
        }
        
        public static function deleteStock($request,$response,$args){
            $postData = $request->getParams();

            $stock = Model::Factory('sheetsstock')->where('id',$postData['id'])->find_one();

            $stock->is_deleted = $postData['isDeleted'];

            if($stock->save()){
                if($postData['isDeleted'] == 1){
                    CommonController::sendResponseWithCode(200,'OK','Stock hided successfully',$response);
                } else {
                    CommonController::sendResponseWithCode(200,'OK','Stock unhided successfully',$response);
                }
            } else CommonController::sendResponseWithCode(403,'FAIL','Internal Server Error',$response);
        }

        public static function editTypeWithSeries($request,$response,$args){
            $postData = $request->getParams();
            $type = Model::Factory('sheetType')->where('id',$postData['id'])->find_one();
            if($postData['type'] == "WithSeries"){
                $type->type_name = $postData['type_name'];
                $type->rate = $postData['rate'];
            } else {
                $type->type_name = $postData['type_name'];
                $type->qty = $postData['qty'];
                $type->rate = $postData['rate'];
            }

            if($type->save()){
                CommonController::sendResponseWithCode(200,'OK','Type updated successfully',$response);
            } else CommonController::sendResponseWithCode(403,'FAIL','Internal Server Error',$response);
        }
        
        public static function deleteType($request,$response,$args){
            $postData = $request->getParams();
            $type = Model::Factory('sheetType')->where('id',$postData['id'])->find_one();
            
            $type->is_deleted = $postData['isDeleted'];

            if($type->save()){
                if($postData['isDeleted'] == 1){
                    CommonController::sendResponseWithCode(200,'OK','Type hided successfully',$response);
                } else {
                    CommonController::sendResponseWithCode(200,'OK','Type unhided successfully',$response);
                }
            } else CommonController::sendResponseWithCode(403,'FAIL','Internal Server Error',$response);
        }

        public static function editSeries($request,$response,$args){
            $postData = $request->getParams();
            $series = Model::Factory('series')->where('id',$postData['id'])->find_one();

            $series->name = $postData['name'];
            $series->qty = $postData['qty'];

            if($series->save()){
                CommonController::sendResponseWithCode(200,'OK','Series updated successfully',$response);
            } else CommonController::sendResponseWithCode(403,'FAIL','Internal Server Error',$response);
        }
        
        public static function deleteSeries($request,$response,$args){
            $postData = $request->getParams();
            $series = Model::Factory('series')->where('id',$postData['id'])->find_one();

            $series->is_deleted = $postData['isDeleted'];

            if($series->save()){
                if($postData['isDeleted'] == 1){
                    CommonController::sendResponseWithCode(200,'OK','Series hided successfully',$response);
                } else {
                    CommonController::sendResponseWithCode(200,'OK','Series unhided successfully',$response);
                }
            } else CommonController::sendResponseWithCode(403,'FAIL','Internal Server Error',$response);
        }
    }
?>