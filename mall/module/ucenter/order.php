<?php

/**
 * 用户中心
 *
 * @author 刘松森 <liusongsen@gmail.com>
 */
class ucenter_order extends app {

    public function __construct() {
        parent::__construct();
    }

    //订单列表
    public function index() {
        //pass
    }

    //付款订单
    public function payOrder() {
        $token = $_GET['token'];
        $payerid = $_GET['PayerID'];
        //验证token
        $payment = payment::getInstance();
        $data = $payment->GetExpressCheckOutDetails($token);
        //记录操作日志
        
       
        
        $this->CT_Api->api = "en.paylog.save";
        $this->CT_Api->setParams(array(
            'api_name' => 'GetExpressCheckoutDetails',
            'order_code' => $data['INVNUM'],
            'the_time' => time(),
            'response' => http_build_query($data),
            'amount' => $data['AMT'],
            'token' => $token,
            'ack' => $data['ACK'],
            'buyer_id' => $payerid,
            'buyer_email' => $data['EMAIL'],
            'ip' => getIP(),
        ));
        $this->CT_Api->post();
        if ($data['ACK'] != "Success") {
            alert("无效的token");
        }
        $code = $data['INVNUM'];
        //执行付款
        $result = $payment->DoExpressCheckOutDetails($token, $payerid, $data['AMT'], "Sale");
        $transaction_id = $result['PAYMENTINFO_0_TRANSACTIONID'];
        $transaction_type = $result['PAYMENTINFO_0_TRANSACTIONTYPE'];
        //记录操作日志
        $this->CT_Api->api = "en.paylog.save";
        $this->CT_Api->setParams(array(
            'api_name' => 'DoExpressCheckoutPayment',
            'order_code' => $data['INVNUM'],
            'the_time' => time(),
            'response' => http_build_query($result),
            'amount' => $data['AMT'],
            'token' => $token,
            'ack' => $result['ACK'],
            'buyer_id' => $payerid,
            'buyer_email' => $data['EMAIL'],
            'ip' => getIP(),
        ));
        $this->CT_Api->post();
        //同步修改订单付款状态
        $this->CT_Api->api = "en.order.pay";
        $this->CT_Api->code = $code;
        $this->CT_Api->token = $token;
        $this->CT_Api->payerid = $payerid;
        $this->CT_Api->transaction_id = $transaction_id;
        $this->CT_Api->transaction_type = $transaction_type;
        $this->CT_Api->flag = $result['ACK'] == "Success" ? 'y' : 'n';
        $this->CT_Api->get();
        if ($result['ACK'] == "Success") {
        	
			//remove promo code
 		
		if ($this->user) {
        $user_id = $this->user['id'];
		$user_email = $this->user['email'];
		}
 	
			$this->CT_Api->api = 'en.ucenter.save';
			$this->CT_Api->setParams(array(
			'mode' => 'update',
			'id' => $user_id,
			'promo' => 'USED',
			));
			$data = $this->CT_Api->post();
			
			
			$this->CT_Api->api = 'en.promo.promoUsed';	
	 		$this->CT_Api->email = $user_email;
			$promoUsed = $this->CT_Api->post();
			
			
			
			//end remove promo code			
            redirect("/index.php?m=ucenter&c=order&a=orderInfo&code=" . $code);
        } else {
            alert("Payment failed!");
        }
    }

    //保存订单
    public function saveOrder() {
        if ($this->user) {
            $billing_id = $_GET['billing_id'];
            $recipient_id = $_GET['recipient_id'];
            $comments = $_GET['comments'];
            $the_date = date("Y-m-d H:i:s");
            if (!verify::vIsNNUll($billing_id)) {
                alert("账单ID不能为空");
            }
            if (!verify::vIsNumber($billing_id)) {
                alert("账单ID格式错误");
            }
            if (!verify::vIsNNUll($recipient_id)) {
                alert("收件人ID不能为空");
            }
            if (!verify::vIsNumber($recipient_id)) {
                alert("收件人ID格式错误");
            }
            //保存订单
            $this->CT_Api->api = "en.order.save";
            $this->CT_Api->setParams(array(
                'user_id' => $this->user['id'],
                'the_date' => $the_date,
                'pay_date' => "0000-00-00 00:00:00",
                'billing_id' => $billing_id,
                'recipient_id' => $recipient_id,
                'status' => "录入",
                'comments' => $comments,
                'ship_fee' => 0.00,
                'logistic' => "Fedex",
                'payerid' => "",
                'token' => "",
                'goods_qty' => $this->shopcart->getQtyScr()
            ));
            $orderResponse = $this->CT_Api->post();
            $order = $orderResponse['response'];
            if (strtolower(substr($order['msg'], 0, 7)) != "success") {
                alert("订单提交失败:" . $order['msg']);
            } else {
                $this->shopcart->flush();
            }
 
// save to paylog table (with discount already)			
			
            $order_code = $order['order_code'];
            $the_amount = $order['the_amount'];
            $payment = payment::getInstance();
            $result = $payment->GetToken($the_amount, $order_code, $this->shopcart->getList());
            //记录操作日志
            
            
            
        	$user_id = $this->user['id'];
            
            $this->CT_Api->api = 'en.ucenter.getByUID';	
	 	$this->CT_Api->id = $user_id;
		$dataPromo = $this->CT_Api->get();
 		 
		$this->assign('dataPromo', $dataPromo['response']);
				
				if ($dataPromo['response']['promo'] !=''){
					$percentage=10;
				}
				else{
					$percentage=0;
				}
				
            
            
            
            
            		$percentage10 =$percentage/100; 
					
					$amount_before = $the_amount;
					
					$ammount_percentage =$percentage10*$amount_before;
					$new_sum = $amount_before-$ammount_percentage; 
					$new_sum=  round($new_sum, 2);
					 
            
            
            
            $this->CT_Api->api = "en.paylog.save";
            $this->CT_Api->setParams(array(
                'api_name' => 'SetExpressCheckout',
                'order_code' => $order_code,
                'the_time' => time(),
                'response' => http_build_query($result),
                'amount' => $new_sum,
                'token' => $result['TOKEN'],
                'ack' => $result['ACK'],
                'ip' => getIP(),
            ));
            $this->CT_Api->post();
            if ($result['ACK'] != "Success") {
                alert("An error occurred while pay PayPal");
            }
            $url = $payment->buildPayPaiUrl($result['TOKEN']);
			
			
			//print_r($url); die();
			
            redirect($url);
        } else {
            redirect("/index.php?m=user&c=index&a=login&ReturnUrl=" . getRequest());
        }
    }


 public function saveRedeemOrder() {
 	
	  if ($this->user) {
            $billing_id = $_GET['billing_id'];
            $recipient_id = $_GET['recipient_id'];
			$order_code = $_GET['order_code'];
			
			
	
	// TOTAL POINTS	
				// count all points
				$this->CT_Api->api = "en.order.getList";
                $ordersResponsePaid = $this->CT_Api->getList("user_id=" . $this->user['id']. " and status='已付款'", array("order" => "id desc"));
                $ordersPaid = $ordersResponsePaid['response'];
				//$this->assign('ordersPaid', $ordersPaid);
				
				foreach ($ordersPaid as $key => $value){
 				$price_of_item = $ordersPaid[$key]['the_amount'];
  				$total_spent_money += $price_of_item; 
				}	
					
					
			    	$total_spent=$total_spent_money;
        	
        	switch (true) {
        		

				case ($total_spent ==0):
        	  
        		$my_vip_status = 'Visitor';
        		$points_range = 1;
        		break;


        		case (($total_spent >= 0) and ($total_spent <=499)):
        	  
        		$my_vip_status = 'Silver Member';
        		$points_range = 2;
        		break;
        		
        		
        		
        		case (($total_spent >=500) and ($total_spent <=999)):
        		 
        		$my_vip_status = 'Gold Member';
        		$points_range = 3;
        		break;
        		
        		
        		case (($total_spent >=1000) and ($total_spent <=2399)):
        		
        		$my_vip_status = 'Platinum Member';
        		$points_range = 4;
        		break;
        		
        		
        		case (($total_spent >=2400) and ($total_spent <=4799)):
        		
        		$my_vip_status = 'Diamond Member';
        		$points_range = 5;
        		break;
        		
        		
        		case ($total_spent >=4800):
        		
        		$my_vip_status = 'Sapphire Member';
        		$points_range = 6;
        		break;
 
        		
        	}

         
    
    // total points for all the time    	
    foreach ($ordersPaid as $key => $value){
 	$amount_item = $ordersPaid[$key]['the_amount'];
 	$pointsPerOrder = floor($amount_item / 30)*$points_range;
 	$totalPoints = $totalPoints + $pointsPerOrder; 
 	}
 			
						// echo "total points: <pre>";
						// print_r($totalPoints);
						// echo "</pre>";
// END TOTAL POINTS					 

 // get redeem cart from DB and count total ammount
 
 
	
		
		$this->CT_Api->api = 'en.redeemInfo.getByUID';	
	 	$this->CT_Api->id = $this->user['id'];
		$redeemInfo = $this->CT_Api->get();
 
$itemsRedeemCart  = $redeemInfo['response']['items'];

$goodsOrderCart = $itemsRedeemCart; 

$piecesItems = explode("#", $itemsRedeemCart);

				for ($j=1; $j < count($piecesItems); $j++) {
				$itemIds[] = $piecesItems[$j];
				}
				
				$itemIds = implode(',', $itemIds);
		 
		 	 	$ids ='goods_id in ('.$itemIds.')';
				
				 
				$this->CT_Api->api = "en.goods.getList";
        		$urlList = $this->CT_Api->getList($ids);
       			$urlList = $urlList['response'];
		  				
		 	  	$ids2 ='product_id in ('.$itemIds.') order by product_id';
				
				 
				$this->CT_Api->api = "en.redeem.getList";
        		$pointsList = $this->CT_Api->getList($ids2);
       			$pointsList = $pointsList['response'];
		  		
				
				foreach($pointsList as $key => $value){
                $total_amount_redeem_cart += $pointsList[$key]['points'];  	
                }

				//echo "total_amount_redeem_cart=".$total_amount_redeem_cart;
// END get redeem cart from DB and count total ammount 	

// testing payment
//$totalPoints = 500;


//get points from table

		$this->CT_Api->api = 'en.points.getByUID';	
	 	$this->CT_Api->id = $this->user['id'];
		$userPointsLeft = $this->CT_Api->get();

// 
//  
    // echo "userPointsLeft<pre>";
    // print_r($userPointsLeft);
    // echo "</pre>";
 
  // echo "total_amount_redeem_cart<pre>";
    // print_r($total_amount_redeem_cart);
    // echo "</pre>";

	if ($userPointsLeft['response']['points'] < $total_amount_redeem_cart){
		//echo 'You do not have enough points.';
			
			
		
			
			
			$this->CT_Api->api = 'en.redeemOrder.save';
			$this->CT_Api->setParams(array(
			
			 
			'order_code' => $order_code,
			'response' => 'ERROR',
			'ship_date' => '0000-00-00 00:00:00',
			'user_id' => $this->user['id'],
			'billing_id' => $billing_id,
			'the_amount' => $total_amount_redeem_cart,
			'goods' => $goodsOrderCart,
			'created' => time(),
			'updated' => time(),
		
			));
			$redeemOrderSave = $this->CT_Api->post();
		// revert after test !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		
		
		
	}

else {
	$new_points_amount = $userPointsLeft['response']['points'] - $total_amount_redeem_cart;
	//echo "new_points_amount=".$new_points_amount;  
	
	
	 
	
	
	
			$this->CT_Api->api = 'en.points.save';
			$this->CT_Api->setParams(array(
			
			 
			'user_id' => $this->user['id'],
			'points' => $new_points_amount,
			'updated' => time(),
		
			));
			$pointsNewAmountSave = $this->CT_Api->post();
	
	if ($pointsNewAmountSave['code'] == 1)
	{
			$this->CT_Api->api = 'en.redeemOrder.save';
			$this->CT_Api->setParams(array(
			
			 
			'order_code' => $order_code,
			'response' => 'SUCCESS',
			'ship_date' => '0000-00-00 00:00:00',
			'user_id' => $this->user['id'],
			'billing_id' => $billing_id,
			'the_amount' => $total_amount_redeem_cart,
			'goods' => $goodsOrderCart,
			'created' => time(),
			'updated' => time(),
		
			));
			$redeemOrderSave = $this->CT_Api->post();
			
			setcookie("REDEEM_CART", "", time()-3600);
		
	}
	
	
}

 

	redirect("/index.php?m=ucenter&c=index&a=redeem");
	// revert after test !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	} // if user 
}
 



    //订单跟踪
    public function orderTrace() {
        if ($_POST) {
            $order_code = $_POST['order_code'];
            $billing_email = $_POST['billing_email'];
            if (!verify::vIsNNUll($order_code)) {
                $errmsg = "订单号码输入不能为空";
                $this->assign('email', $billing_email);
                $this->assign('order_code', $order_code);
                $this->assign('errmsg', $errmsg);
                $this->display("tracking");
            }
            if (!verify::vIsNNUll($billing_email)) {
                $errmsg = "账单邮箱输入不能为空";
                $this->assign('email', $billing_email);
                $this->assign('order_code', $order_code);
                $this->assign('errmsg', $errmsg);
                $this->display("tracking");
            }
            if (!verify::vEmail($billing_email)) {
                $errmsg = "账单邮箱格式输入错误";
                $this->assign('email', $billing_email);
                $this->assign('order_code', $order_code);
                $this->assign('errmsg', $errmsg);
                $this->display("tracking");
            }
            if (!preg_match("/^d\d{10,15}$/is", $order_code)) {
                $errmsg = "订单号码格式输入错误";
                $this->assign('email', $billing_email);
                $this->assign('order_code', $order_code);
                $this->assign('errmsg', $errmsg);
                $this->display("tracking");
            } else {
                $this->CT_Api->api = "en.order.getByCode";
                $this->CT_Api->code = $order_code;
                $dataResponse = $this->CT_Api->get();
                $data = $dataResponse['response'];
            }
            if (!$data) {
                $errmsg = "Sorry, we could not find that order id in our database.";
            }
            //渲染视图
            $this->assign('errmsg', $errmsg);
            $this->assign('email', $billing_email);
            $this->assign('order_code', $order_code);
            $this->assign('data', $data);
            $this->display("tracking");
        } else {
            $this->display("tracking");
        }
    }

    //订单跟踪
    public function orderInfo() {
        $order_code = $_GET['code'];
        $this->CT_Api->api = "en.order.getByCode";
        $this->CT_Api->code = $order_code;
        $dataResponse = $this->CT_Api->get();
        $data = $dataResponse['response'];
        if (!$data) {
            redirect("/not-found.html");
        }
        //渲染视图
        $this->assign('data', $data);
        $this->display("payment");
    }

}
