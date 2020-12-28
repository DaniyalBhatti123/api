<?php
    class WholeSellerController{
        public static function wholeSellerReciept($request,$response,$args){
            $postData = $request->getParams();
            $date = date('Y-m-d h:i:sa');
            $userObj = $postData['userObj'];

            $isChannelArrayComplete = false;
            $isSheetArrayComplete = false;
            $totalPrice = 0;
            if(!empty($postData['channel'])) {
                for ($x = 0; $x < sizeof($postData['channel']); $x++) {
                    $channelStock = Model::Factory('channelStock')->where('item',$postData['channel'][$x]['ft'])->find_one();
                    $channel = Model::Factory('channel')->create();
                    $channel->id = '';
                    $channel->name = $postData['channel'][$x]['ft'];
                    $channel->qty = $postData['channel'][$x]['qty'];
                    $channel->running_ft = $postData['channel'][$x]['rf'];
                    $channel->running_ft_rate = $postData['channel'][$x]['rfr'];
                    $channel->final_rate = $postData['channel'][$x]['finalRate'];
                    $channel->invoice = $postData['userObj']['invoice'];
                    $channel->is_created = $date;
                    $channel->is_done = 1;
                    $channel->save();

                    $newQty = (int)$channelStock->qty - (int)$postData['channel'][$x]['qty'];
                    $channelStock->qty = (string)$newQty;
                    $channelStock->save();

                    $totalPrice += (int)$postData['channel'][$x]['finalRate'];

                    if($x == sizeof($postData['channel']) - 1) $isChannelArrayComplete = true;
                }
            } else $isChannelArrayComplete = true;

            if(!empty($postData['sheets'])){
                for ($x = 0; $x < sizeof($postData['sheets']); $x++) {
                    $sheets = Model::Factory('sheets')->create();
                    $sheets->id = '';
                    $sheets->sheets = $postData['sheets'][$x]['sheet'];
                    $sheets->qty = $postData['sheets'][$x]['qty'];
                    $sheets->type = $postData['sheets'][$x]['selectedType'];
                    $sheets->series = $postData['sheets'][$x]['selectedItem'];
                    $sheets->rate = $postData['sheets'][$x]['rate'];
                    $sheets->final_rate = $postData['sheets'][$x]['finalRate'];
                    $sheets->invoice = $postData['userObj']['invoice'];
                    $sheets->is_created = $date;
                    $sheets->is_done = 1;
                    $sheets->save();

                    $totalPrice += (int)$postData['sheets'][$x]['finalRate'];
                    if(sizeof($postData['sheets'][$x]['type']) > 0 && sizeof($postData['sheets'][$x]['series']) > 0) {
                        $sheetsStock = Model::Factory('series')->where('name',$postData['sheets'][$x]['selectedItem'])->find_one();
                        $newQty = (int)$sheetsStock->qty - (int)$postData['sheets'][$x]['qty'];
                        $sheetsStock->qty = (string)$newQty;
                        $sheetsStock->save();
                    } else if(sizeof($postData['sheets'][$x]['type']) > 0 && sizeof($postData['sheets'][$x]['series']) == 0) {
                        $typeStock = Model::Factory('sheetType')->where('type_name',$postData['sheets'][$x]['selectedType'])->find_one();
                        $newQty = (int)$typeStock->qty - (int)$postData['sheets'][$x]['qty'];
                        $typeStock->qty = (string)$newQty;
                        $typeStock->save();
                    } else {
                        $sheetStock = Model::Factory('sheetsstock')->where('item',$postData['sheets'][$x]['sheet'])->find_one();
                        $newQty = (int)$sheetStock->qty - (int)$postData['sheets'][$x]['qty'];
                        $sheetStock->qty = (string)$newQty;
                        $sheetStock->save();
                    }

                    if($x == sizeof($postData['sheets']) - 1) $isSheetArrayComplete = true;
                }
            } else $isSheetArrayComplete = true;

            if($isChannelArrayComplete == true && $isSheetArrayComplete == true) {
                $isUserExist = Model::Factory('remainingBalance')->where('customer_contact',$postData['userObj']['phone'])->find_one();
                $isPaymentAdded = false;
                if(!$isUserExist) {
                    $remainingAmount = (int)$postData['userObj']['total'] - (int)$postData['userObj']['givingBalance'];
                    if($remainingAmount != 0) {
                        $paymentCreate = Model::Factory('remainingBalance')->create();
    
                        $paymentCreate->id = '';
                        $paymentCreate->customer_contact = $postData['userObj']['phone'];
                        $paymentCreate->amount = (string)$remainingAmount;
                        $paymentCreate->is_created = $date;
    
                        $paymentCreate->save();
                    }

                    $user = Model::Factory('customer')->create();

                    $user->id = '';
                    $user->name = $postData['userObj']['name'];
                    $user->contact = $postData['userObj']['phone'];
                    $user->address = isset($postData['userObj']['address']) ? $postData['userObj']['address'] : '';
                    $user->total = (int)$postData['userObj']['total'];
                    $user->giving_balance = (int)$postData['userObj']['givingBalance'];
                    $user->remaining_balance = (string)$remainingAmount;
                    $user->invoice = $postData['userObj']['invoice'];
                    if((int)$postData['userObj']['total'] == (int)$postData['userObj']['givingBalance']) $user->isDone = 1;
                    else $user->isDone = 0;
                    $user->is_deleted = 0;
                    $user->is_created = $date;

                    $user->save();

                    $isPaymentAdded = true;
                } else {
                    $givingAmount = (int)$postData['userObj']['givingBalance'];
                    $remainingAmount = ((int)$isUserExist->amount + (int)$postData['userObj']['total']) - (int)$postData['userObj']['givingBalance'];
                    if($remainingAmount != 0){
                        $isUserExist->amount = (string)$remainingAmount;
                        $isUserExist->save();
                    } else {
                        $isUserExist->delete();
                    }
                    $allClear = false;
                    $user = Model::Factory('customer')->where('contact',$postData['userObj']['phone'])->where('isDone',0)->find_array();
                    for($y = 0; $y < sizeof($user); $y++){
                        $oneUser = Model::Factory('customer')->where('invoice',$user[$y]['invoice'])->find_one();
                        if((int)$remainingAmount != 0) {
                            if((int)$givingAmount > ((int)$oneUser->remaining_balance) + (int)$postData['userObj']['total']) {
                                $givingAmount = (int)$givingAmount - (int)$oneUser->remaining_balance;
                                $oneUser->remaining_balance = '0';
                                $oneUser->isDone = 1;
                                $oneUser->save();
                            } else if((int)$givingAmount == (int)$oneUser->remaining_balance){
                                $oneUser->remaining_balance = '0';
                                $oneUser->isDone = 1;
                                $oneUser->save();
                                break;
                            } else {
                                if((int)$givingAmount > (int)$oneUser->remaining_balance) {
                                    (int)$givingAmount = (int)$givingAmount - (int)$oneUser->remaining_balance;
                                    $oneUser->giving_balance = (int)$oneUser->giving_balance + (int)$oneUser->remaining_balance;
                                    $oneUser->remaining_balance = '0';
                                    $oneUser->isDone = 1;
                                    $oneUser->save();
                                } else {
                                    $givingAmount = 0;
                                    $oneUser->remaining_balance = (int)$oneUser->remaining_balance - (int)$givingAmount;
                                    $oneUser->save();
                                    break;
                                }
                            }
                        } else {
                            (int)$givingAmount = (int)$givingAmount - (int)$oneUser->remaining_balance;
                            $oneUser->giving_balance = (int)$oneUser->giving_balance + (int)$oneUser->remaining_balance;
                            $oneUser->remaining_balance = '0';
                            $oneUser->isDone = 1;
                            $oneUser->save();

                            $allClear = true;
                        }
                    }
                    if($givingAmount == 0) {
                        $postData['userObj']['givingBalance'] = '0';
                        $postData['userObj']['remainingBalance'] = (string)$postData['userObj']['total'];
                    } else {
                        $postData['userObj']['givingBalance'] = (int)$givingAmount;
                        $remainingBalanceOfUser = (int)$postData['userObj']['total'] - (int)$givingAmount;
                        $postData['userObj']['remainingBalance'] = $remainingBalanceOfUser;
                    }
                    $user = Model::Factory('customer')->create();

                    $user->id = '';
                    $user->name = $postData['userObj']['name'];
                    $user->contact = $postData['userObj']['phone'];
                    $user->address = isset($postData['userObj']['address']) ? $postData['userObj']['address'] : '';
                    $user->total = (int)$postData['userObj']['total'];
                    $user->giving_balance = (int)$givingAmount;
                    $user->remaining_balance = $remainingBalanceOfUser;
                    $user->invoice = $postData['userObj']['invoice'];
                    if($allClear == true) $user->isDone = 1;
                    else $user->isDone = 0;
                    $user->is_deleted = 0;
                    $user->is_created = $date;

                    $user->save();

                    $isPaymentAdded = true;
                }
                if($isPaymentAdded == true) {
                    CommonController::sendResponseWithCode(200,'OK','Reciept added successfully',$response);
                } else CommonController::sendResponseWithCode(403,'FAIL','Internal Server Error',$response);
            } else CommonController::sendResponseWithCode(403,'FAIL','Internal Server Error',$response);
        }

        public static function getReciepts($request,$response,$args){
            $postData = $request->getParams();
            $limit  = isset($postData['limit'])? $postData['limit'] : 10;
            $offset = isset($postData['offset'])? $postData['offset'] : 0;
            $name = isset($postData['name'])? $postData['name'] : "";
            $contact = isset($postData['contact'])? $postData['contact'] : "";
            $invoice = isset($postData['invoice'])? $postData['invoice'] : "";
            $recieptData = Model::Factory('customer')->where_like('name','%'.$name.'%')->where_like('contact','%'.$contact.'%')->where_like('invoice','%'.$invoice.'%')->limit($limit)->offset($offset)->_add_order_by('id','DESC')->find_array();
            $count = count(Model::Factory('customer')->where_like('name','%'.$name.'%')->where_like('contact','%'.$contact.'%')->where_like('invoice','%'.$invoice.'%')->find_array());
            
            for($x = 0; $x < sizeof($recieptData); $x++){
                $channelReciept = Model::Factory('channel')->where('invoice',$recieptData[$x]['invoice'])->find_array();
                $sheetReciept = Model::Factory('sheets')->where('invoice',$recieptData[$x]['invoice'])->find_array();
                $remainingBalance = Model::Factory('remainingBalance')->where('customer_contact',$recieptData[$x]['contact'])->find_array();
                $recieptData[$x]['channelData'] = $channelReciept;
                $recieptData[$x]['sheetData'] = $sheetReciept;
                $recieptData[$x]['remainingBalance'] = $remainingBalance;
            }

            if($count != 0) {
                CommonController::sendBodyResponseWithCode(200,'OK','Data Found',array('recieptData'=>$recieptData,'total'=>$count),$response);
            } else CommonController::sendBodyResponseWithCode(200,'OK','No Record Found',array('recieptData'=>[],'total'=>0),$response);
        }

        public static function getRemainingBalance($request,$response,$args){
            $postData = $request->getParams();
            $remainingBalance = Model::Factory('remainingBalance')->where('customer_contact',$postData['contact'])->find_array();
            $remainingInvoices = Model::Factory('customer')->where('contact',$postData['contact'])->where('isDone',0)->_add_order_by('id','DESC')->find_array();
            if($remainingBalance && $remainingInvoices) {
                CommonController::sendBodyResponseWithCode(200,'OK','Data Found',array('balance'=>$remainingBalance[0],'invoice'=>$remainingInvoices),$response);
            }else CommonController::sendResponseWithCode(200,'FAIL','No data found',$response);
        }
    }
?>