<?php

include_once '../../sdk/CT_Api.php';
/**
 * 国际版皮肤测试
 * User: 宋伟航
 * Date: 14-12-12
 * Time: 上午9:45
 */
class ucenter_index extends app {


    private $confData;
    private $CT;


    public function __construct() {
        parent::__construct();
        $this->CT = new CT_Api();
        
   
    }

    
// =============================== CONFIRM =============================================    
 public function confirmOrder() {
            
        if ($this->user) {
        $user_id = $this->user['id'];
        $user_email = $this->user['email'];

        $seo_ucenter_title['title'] = 'Support Center'; 
        $this->assign('seo_ucenter_title', $seo_ucenter_title['title']); 
        
        }
        
 
        if ($this->user) {
        $user_id = $this->user['id'];}
         
                
        $this->CT->api = 'en.ucenter.getByUID'; 
        $this->CT->id = $user_id;
        $data = $this->CT->get();
         
        $this->assign('data', $data['response']);
        
        
        
        if ( !$user_id){
            redirect("/login.html"); 
        }
        
        else {
            
            $order_id = $_POST['order_id']; 
            $all_vars = base64_decode($order_id);
            $vars_exploded = explode('|', $all_vars);
            
            $this->CT->api = 'en.order.get';    
            $this->CT->id = $vars_exploded[2];
            $goodsTable = $this->CT->get(); 
            
            if (($goodsTable['response']['order_code'] == $vars_exploded[0]) and  ($goodsTable['response']['created'] == $vars_exploded[1]) and ($goodsTable['response']['id'] == $vars_exploded[2]) and ($vars_exploded[0] !='') and ($vars_exploded[1] !='') and ($vars_exploded[2] !=''))
{


  
          $this->CT->api = 'en.orderConfirmation.save';
          $this->CT->setParams(array(
          'id' => 0,
          'order_id' => $vars_exploded[0],
          'user_id' => $user_id,
          'confirmation'=>1,
          'created'=>time(),
            
            ));
            $dataSave = $this->CT->post();
    
            
            $this->assign('data',$dataSave);
    
            if ($dataSave['code'] == 1){
            $is_deleted = 1;    
            } else {
             $is_deleted = 0;   
            }
            
}       
        
else {
 
    // echo 'wrong hash';
    $is_deleted = 0;
}
        
         
        $this->assign('is_deleted', $is_deleted); 
            
            
            
            $this->assign('order_id', $goodsTable);
            
            
            $this->display('orderConfirm');
        }
    
    
    
         
}


	
// =============================== SUPPORT =============================================	
 public function support() {
			
		if ($this->user) {
        $user_id = $this->user['id'];
		$user_email = $this->user['email'];

		$seo_ucenter_title['title'] = 'Support Center'; 
		$this->assign('seo_ucenter_title', $seo_ucenter_title['title']); 
		
		}
		
 
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
 		 
		$this->assign('data', $data['response']);
		
		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';
 
 
 	
 	
	$this->display('support');
		 
}

// =============================== LOAD UNPAID =============================================    
 public function LoadUnpaid() {
            
        if ($this->user) {
        $user_id = $this->user['id'];
        $user_email = $this->user['email'];

        $seo_ucenter_title['title'] = 'Unpaid'; 
        $this->assign('seo_ucenter_title', $seo_ucenter_title['title']); 
        
        }
        
 
        $this->CT->api = 'en.ucenter.getByUID'; 
        $this->CT->id = $user_id;
        $data = $this->CT->get();
         
        $this->assign('data', $data['response']);
        

 
 
    
    
      $this->CT->api = "en.order.getList";
                $ordersResponse = $this->CT->getList("user_id=" . $this->user['id']." AND status='录入'", array("order" => "id desc"));
                $orders = $ordersResponse['response'];
                $this->assign('orders', $orders);
                
                for ($j=0; $j < count($orders); $j++) {
                $itemIds[] = $orders[$j]['lines'][0]['goods_id'];
                }
                
                $itemIds = implode(',', $itemIds);
                      
                 $ids ='goods_id in ('.$itemIds.')';
                
                 
                 $this->CT->api = "en.goods.getList";
                 $urlList = $this->CT->getList($ids);
                 $urlList = $urlList['response'];
                 
                 $allData_new = array();
                 foreach($urlList as $d) {
                    $id = $d['goods_id'];
                     array_shift($d);
                    $allData_new[$id] = $d;
                 }
                 
                 $this->assign('urlList', $allData_new);

                        
        $seo_ucenter_title['title'] = 'My Orders'; 
        $this->assign('seo_ucenter_title', $seo_ucenter_title['title']);
        
        $this->display('LoadUnpaid'); 
         
}



// =============================== LOAD PAID =============================================    
 public function LoadPaid() {
            
        if ($this->user) {
        $user_id = $this->user['id'];
        $user_email = $this->user['email'];

        $seo_ucenter_title['title'] = 'Unpaid'; 
        $this->assign('seo_ucenter_title', $seo_ucenter_title['title']); 
        
        }
        
 
        $this->CT->api = 'en.ucenter.getByUID'; 
        $this->CT->id = $user_id;
        $data = $this->CT->get();
         
        $this->assign('data', $data['response']);
        
        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';
 
                
    
    
      $this->CT->api = "en.order.getList";
                $ordersResponse = $this->CT->getList("user_id=" . $this->user['id']." AND status='已付款'", array("order" => "id desc"));
                $orders = $ordersResponse['response'];
                $this->assign('orders', $orders);
                
                // echo "<pre>";
                    // print_r($orders);
                    // echo "</pre>";
//                 
                //get list with confirmed items
                
               
                    
                
                
                
                
                
                for ($j=0; $j < count($orders); $j++) {
                $itemIds[] = $orders[$j]['lines'][0]['goods_id'];
                }
                
                $itemIds = implode(',', $itemIds);
                      
                 $ids ='goods_id in ('.$itemIds.')';
                
                 
                 $this->CT->api = "en.goods.getList";
                 $urlList = $this->CT->getList($ids);
                 $urlList = $urlList['response'];
                 
                 $allData_new = array();
                 foreach($urlList as $d) {
                    $id = $d['goods_id'];
                     array_shift($d);
                    $allData_new[$id] = $d;
                 }
                 
                 $this->assign('urlList', $allData_new);

                        
        $seo_ucenter_title['title'] = 'My Orders'; 
        $this->assign('seo_ucenter_title', $seo_ucenter_title['title']);
        
        
         
                $this->CT->api = "en.orderConfirmation.getList";
                $ordersResponse = $this->CT->getList("order_id='" . $orders[0]['order_code'] . "'",  array('id' => "id desc"));
                $confirmed = $ordersResponse['response'];
                $this->assign('confirmed', $confirmed);
                
                
                     
                    // echo "<pre>";
                    // print_r($confirmed);
                    // echo "</pre>";
        
        
        
        
        $this->display('LoadPaid'); 
         
}




// =============================== LOAD ITEMS =============================================    
 public function LoadItems() {
            
        if ($this->user) {
        $user_id = $this->user['id'];
        $user_email = $this->user['email'];

        $seo_ucenter_title['title'] = 'Unpaid'; 
        $this->assign('seo_ucenter_title', $seo_ucenter_title['title']);
        
        $order_id = $_POST['order_id']; 
        //print_r($order_id);
        
        $this->assign('order_id', $order_id);
         
        }
        
 
        $this->CT->api = 'en.ucenter.getByUID'; 
        $this->CT->id = $user_id;
        $data = $this->CT->get();
         
        $this->assign('data', $data['response']);
        
           $this->CT->api = "en.order.getList";
                $ordersResponse = $this->CT->getList("user_id=" . $this->user['id']." AND order_code='".$order_id."'", array("order" => "id desc"));
                $orders = $ordersResponse['response'];
                $this->assign('orders2', $orders);
                
                
                
                
         
                $this->CT->api = "en.orderConfirmation.getList";
                $ordersResponse = $this->CT->getList("order_id='" . $orders[0]['order_code'] . "'",  array('id' => "id desc"));
                $confirmed = $ordersResponse['response'];
                $this->assign('confirmed', $confirmed);
                
                
                
                     
                    // echo "<pre>";
                    // print_r($confirmed);
                    // echo "</pre>";
//                     
                
                
                
                for ($j=0; $j < count($orders); $j++) {
                $itemIds[] = $orders[$j]['lines'][0]['goods_id'];
                }
                
                $itemIds = implode(',', $itemIds);
                      
                 $ids ='goods_id in ('.$itemIds.')';
                
                 
                 $this->CT->api = "en.goods.getList";
                 $urlList = $this->CT->getList($ids);
                 $urlList = $urlList['response'];
                 
                 $allData_new = array();
                 foreach($urlList as $d) {
                    $id = $d['goods_id'];
                     array_shift($d);
                    $allData_new[$id] = $d;
                 }
                 
                 $this->assign('urlList', $allData_new);

    //get all items with qnty for current transaction
    $goods_into_transaction =  $orders[0]['goods_qty'];
    

    //divide current transaction
    $goods_into_transaction_explode = explode(';', $goods_into_transaction);
    //clean empty values into array
    $goods_into_transaction_clear = array_filter($goods_into_transaction_explode);
    //get count of items
    $count_goods_into_transaction = count($goods_into_transaction_clear);
    


    //get ids without qnty
    for ($m=0; $m <=$count_goods_into_transaction ; $m++) { 
    $single_id_goods_into_transaction[$m] = explode(',', $goods_into_transaction_clear[$m]);
    $tmp_id = $single_id_goods_into_transaction[$m][0];
    $final_ids[$m] =$tmp_id; 

    }
    
    
    //clear empty values in array
     $final_ids = array_filter($final_ids);
     
     //combine all ids
     $itemIds = implode(',', $final_ids);
     $ids ='goods_id in ('.$itemIds.')';
     
    
     // get list of all items
     $this->CT->api = "en.goods.getList";
     $urlList = $this->CT->getList($ids);
     $urlList = $urlList['response'];
       
    
    
    //secho "<pre>";
    //print_r($goods_into_transaction);
    // print_r($goods_into_transaction_explode);
    //print_r($goods_into_transaction_clear);
    //print_r($single_id_goods_into_transaction);
    //print_r($final_ids);
    //print_r($ids);
    //print_r($urlList);
    //print_r($list_of_id);
    //echo "</pre>";
    


                        
        $seo_ucenter_title['title'] = 'My Orders'; 
        $this->assign('seo_ucenter_title', $seo_ucenter_title['title']);
        $this->assign('orders', $urlList);
        
        
        $this->display('LoadItems'); 
         
}






 public function supportSkinResults() {
			
		if ($this->user) {
        $user_id = $this->user['id'];
		$user_email = $this->user['email'];
		
		}
		
 
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
		$is_admin = $data['response']['sales'];
 		 
		$this->assign('data', $data['response']);
		
		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';
 
 
 if ($is_admin){
 	$this->CT->api = 'en.skin.getList';
	$dataSkinTestResults = $this->CT->getList("", array('order' => 'id desc'), "*");
	$this->assign('dataSkinTestResults', $dataSkinTestResults['response']);
 	
 	
	$this->display('support_skinResults');
 }
 
 	 
}
 
 
 
 
 
 
 public function supportFacebookResults() {
			
		if ($this->user) {
        $user_id = $this->user['id'];
		$user_email = $this->user['email'];
		
		}
		
 
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
		$is_admin = $data['response']['sales'];
 		 
		$this->assign('data', $data['response']);
		
		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';
 
 
 if ($is_admin){
 		$this->CT->api = "en.facebook.getFreeSampleInfo";
    	$result = $this->CT->post();
    	if ($result['code'] == 1) $this->assign('result', $result['response']);
 	
 	
	$this->display('support_FacebookResults');
 }
 
 	 
}
 
 
 
 
 


 public function supportLoadAllHelpMessagesPage() {
			
		if ($this->user) {
        $user_id = $this->user['id'];
		$user_email = $this->user['email'];
		
		}
		
 
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
		$is_admin = $data['response']['sales'];
 		 
		$this->assign('data', $data['response']);
		
		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';
 
 
 if ($is_admin){
 			
		// all messages = 0	
 		$user_id = 0;
		$this->CT->api = "en.help.getList";
        $allDataResponse = $this->CT->getList("sender>" . $user_id . "",  array('order' => "id desc"));
        $helpMessages = $allDataResponse['response'];
 		$this->assign('helpMessages', $helpMessages);
 	
 	
	$this->display('support_allMessages');
 }
 
 	 
}

 


 public function supportLoadUnreadHelpMessagesPage() {
			
		if ($this->user) {
        $user_id = $this->user['id'];
		$user_email = $this->user['email'];
		
		}
		
 
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
		$is_admin = $data['response']['sales'];
 		 
		$this->assign('data', $data['response']);
		
		 // echo '<pre>';
		 // print_r($data);
		 // echo '</pre>';
 
 
 if ($is_admin){
 			
		
                $this->CT->api = "en.help.getList";
                $unreadMessages = $this->CT->getList("support_read=0", array('order' => "created desc"));
                $unreadMessagesList = $unreadMessages['response'];
				$this->assign('unreadMessages', $unreadMessagesList);
 	
		 // echo '<pre>';
		 // print_r($orders);
		 // echo '</pre>';
		 
		 
		 
		 
		 
 	
	$this->display('support_unreadMessages');
 }
 
 	 
}






 public function supportLoadReadHelpMessagesPage() {
			
		if ($this->user) {
        $user_id = $this->user['id'];
		$user_email = $this->user['email'];
		
		}
		
 
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
		$is_admin = $data['response']['sales'];
 		 
		$this->assign('data', $data['response']);
		
		 // echo '<pre>';
		 // print_r($data);
		 // echo '</pre>';
 
 
 if ($is_admin){
 			
		
                $this->CT->api = "en.help.getList";
                $readMessages = $this->CT->getList("support_read=1", array('order' => "created desc"));
                $readMessagesList = $readMessages['response'];
				$this->assign('readMessages', $readMessagesList);
 	
		  // echo '<pre>';
		  // print_r($readMessages);
		  // echo '</pre>';
 	
	$this->display('support_readMessages');
 }
 
 	 
}
 
 
 
 
 
 
 
 
 public function supportLoadReplyHelpMessagesPage() {
			
		if ($this->user) {
        $user_id = $this->user['id'];
		$user_email = $this->user['email'];
		
		}
		
 
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
		$is_admin = $data['response']['sales'];
 		 
		$this->assign('data', $data['response']);
		
		 // echo '<pre>';
		 // print_r($data);
		 // echo '</pre>';
 
 
 if ($is_admin){
 			
		
                $this->CT->api = "en.helpReply.getList";
                $replyMessages = $this->CT->getList("is_read>0", array('order' => "created desc"));
                $replyMessagesList = $replyMessages['response'];
				$this->assign('replyMessages', $replyMessagesList);
 	
		  // echo '<pre>';
		  // print_r($readMessages);
		  // echo '</pre>';
 	
	$this->display('support_replyMessages');
 }
 
 	 
}
 




	
	
// =============================== SAVE PAGE VIEWS (COUNTER) =============================================	
 public function saveViews() {
			
		if ($this->user) {
        $user_id = $this->user['id'];
		$user_email = $this->user['email'];
		
		}
		
 
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
 		 
		$this->assign('data', $data['response']);
		
		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';
 
 
			$post_goods_id = $_POST['pid'];
		 	$this->CT->api = "en.goods.view";
            $this->CT->id = $post_goods_id;
            $itemSaveView = $this->CT->get();	
			$this->display('saveViews');
		 
}



	
// =============================== THUMBS =============================================	
 public function thumbsUp() {
			
		if ($this->user) {
        $user_id = $this->user['id'];
		$user_email = $this->user['email'];
		
		}
		
		 	if ( !$user_id){
			      	
				// display login means->login before vote  (file= ca_footer.html  LINE: 920 - we can change on others)
			      echo 'login';
		}
		
		else {
			
		$comment_id = $_POST['comment_id'];
		
	 		// check if user voted already
		$this->CT->api = "en.commentsThumbs.getList";
        $DataThumbsResponse = $this->CT->getList("user_id=" . $user_id . " and comment_id=".$comment_id."",  array('order' => "id desc"));
		$userThumbsCount = count($DataThumbsResponse['response']);
        $DataThumbsResponse = $DataThumbsResponse['response'];
		
		if ($userThumbsCount !=1){
				$this->assign('data', $data['response']);
		
		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';
 
			$comment_id = $_POST['comment_id'];
		
		 	$this->CT->api = "en.comments.thumbsUp";
            $this->CT->id = $comment_id;
            $itemSaveThumbsUp = $this->CT->get();
			$this->assign('itemSaveThumbsUp', $itemSaveThumbsUp);	
			
			
			// save users thumbs for this comment
			$this->CT->api = 'en.commentsThumbs.save';
			$this->CT->setParams(array(
			'user_id' => $user_id,
			'comment_id' => $comment_id,
			));
			$thumbsTracking = $this->CT->post();
 		 	$this->assign('thumbsTracking', $thumbsTracking);
			
			
			
			$this->CT->api = "en.comments.get";
            $this->CT->id = $comment_id;
            $itemSaveThumbsUpNewValue = $this->CT->get();
			$this->assign('itemSaveThumbsUpNewValue', $itemSaveThumbsUpNewValue);
			
			$this->display('saveThumbsUp');
		} 
		 
		else{
			$this->CT->api = "en.comments.get";
            $this->CT->id = $comment_id;
            $itemSaveThumbsUpNewValue = $this->CT->get();
			$this->assign('itemSaveThumbsUpNewValue', $itemSaveThumbsUpNewValue);
			
			$this->display('saveThumbsUp');
		} 
		 
		
		}	 
}

				
				
				
public function thumbsDown() {
			
				
		if ($this->user) {
        $user_id = $this->user['id'];
		$user_email = $this->user['email'];
		
		}
		// display login means->login before vote  (file= ca_footer.html  LINE: 920 - we can change on others)
			if ( !$user_id){
			     echo 'login';
		}
		
		else {
			 
		$comment_id = $_POST['comment_id'];
		
	  		// check if user voted already
		$this->CT->api = "en.commentsThumbs.getList";
        $DataThumbsResponse = $this->CT->getList("user_id=" . $user_id . " and comment_id=".$comment_id."",  array('order' => "id desc"));
		$userThumbsCount = count($DataThumbsResponse['response']);
        $DataThumbsResponse = $DataThumbsResponse['response'];
		
		if ($userThumbsCount !=1){
			
		 	$this->CT->api = "en.comments.thumbsDown";
            $this->CT->id = $comment_id;
            $itemSaveThumbsDown = $this->CT->get();
			$this->assign('itemSaveThumbsDown', $itemSaveThumbsDown);	
			
			
			$this->CT->api = "en.comments.get";
            $this->CT->id = $comment_id;
            $itemSaveThumbsDownNewValue = $this->CT->get();
			$this->assign('itemSaveThumbsDownNewValue', $itemSaveThumbsDownNewValue);
			
			$this->display('saveThumbsDown');	
		}
	
	else {
	$this->CT->api = "en.comments.get";
            $this->CT->id = $comment_id;
            $itemSaveThumbsDownNewValue = $this->CT->get();
			$this->assign('itemSaveThumbsDownNewValue', $itemSaveThumbsDownNewValue);
			
			$this->display('saveThumbsDown');	
		}	 
		
 
		
			} 
}

				
// =============================== GET PROMO =============================================	
 public function getMyPromo() {
			
		if ($this->user) {
        $user_id = $this->user['id'];
		$user_email = $this->user['email'];
		
		$seo_ucenter_title['title'] = 'Promo'; 
		$this->assign('seo_ucenter_title', $seo_ucenter_title['title']); 
		 	
		
		
		
		}
		
 
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
 		 
		$this->assign('data', $data['response']);
		
		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';
 
		
		
		if ( !$user_id){
			        redirect("/login.html"); 
		}
		
		else {
			
		$this->CT->api = 'en.promo.getByEmail';	
	 	$this->CT->email = $user_email;
		$userPromo = $this->CT->get();
 		 
		$this->assign('userPromo', $userPromo['response']);
			
			
			
			 
	if ($userPromo['response']['promo'] =='') {
			
			
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 10; $i++) {
    $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
	$promoCode=$randomString;	
			
			
			
			
			
	$promo_temp = $promoCode;
	 
		
		
			$this->CT->api = 'en.promo.save';
			$this->CT->setParams(array(
			'promo' => $promo_temp,
			'email' => $user_email,
			));
			$dataSave = $this->CT->post();
 		 	$this->assign('dataSave', $dataSave);
		
		//print_r( $dataSave);
		
		
	}	
	
	
else {
		$this->CT->api = 'en.promo.getByEmail';	
	 	$this->CT->email = $user_email;
		$userPromo = $this->CT->get();
		
		 
	
}

		$this->CT->api = 'en.promo.getByEmail';	
	 	$this->CT->email = $user_email;
		$userPromo = $this->CT->get();


	$this->assign('userPromo', $userPromo['response']);
	$this->display('getMyPromo');
		}
}
	
	
// =============================== HOME =============================================

	public function home() {
			
		if ($this->user) {
        $user_id = $this->user['id'];
		
		$seo_ucenter_title['title'] = 'My Information'; 
		$this->assign('seo_ucenter_title', $seo_ucenter_title['title']); 
		 	
		
		}
		
		$user_email = $this->user['email'];
		
		
	 
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
 		 
		$this->assign('data', $data['response']);
		
		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';
 
		
		
		if ( !$user_id){
		redirect("/login.html"); 
		}
		
		else {

		
                $this->CT->api = "en.order.getList";
                $ordersResponse = $this->CT->getList("user_id=" . $user_id, array("order" => "id desc"));
                $ordersUnpaid = $ordersResponse['response'];
                $this->assign('unpaid', $ordersUnpaid);
        

            //get list of Shipped items
                $this->CT->api = "en.orderConfirmation.getList";
                $ordersResponse = $this->CT->getList("user_id=" . $user_id,  array('id' => "id desc"));
                $finished = $ordersResponse['response'];
                $this->assign('finished', $finished);
        
        
        
            
           
            
        
        
        	
		if ($_POST['promo_action'] == 'savePromo') {

		$this->CT->api = 'en.promo.getByEmail';	
	 	$this->CT->email = $data['response']['email'];
		$dataPromoCode = $this->CT->get();
		$this->assign('dataPromoCode', $dataPromoCode['response']); 
		 
			
			if ($_POST['my_promo_code'] == $dataPromoCode['response']['promo']){
			// data including user info + promo code
			$this->CT->api = 'en.ucenter.save';
			$this->CT->setParams(array(
			'mode' => 'update',
			'id' => $data['response']['id'],
			'promo' => $_POST['my_promo_code'],
			
			));
			$dataSave = $this->CT->post();
			$this->assign('dataSave', $dataSave['response']);
			
			$dataSavePromoResponse=1;
			$this->assign('dataSavePromoResponse', $dataSavePromoResponse);
			$promoResult=$_POST['my_promo_code'];
			$this->assign('promoResult', $promoResult);

            }
            else if ($_POST['my_promo_code'] != $dataPromoCode['response']['promo']){
					
				$dataSavePromoResponse=2;	
				$this->assign('dataSavePromoResponse', $dataSavePromoResponse);	
				$promoResult='Invalid code';
				$this->assign('promoResult', $promoResult);
			}
			
			}
			
			
 
		// checking for table with user info
		if ($data['code'] == -1){
		//	echo '<br><br><br><br>creating record for user with id'.$user_id;
		
		
		//date
		$timestamp = date_create();
	 	
		
		// creating record for user info
		 $this->CT->api = 'en.ucenter.addById';
		 $this->CT->setParams(array(
		 'id' => $user_id,
		 'first_name' => '',
		 'last_name' => '',
		 'nick_name' => '',
		 'gender' => 'f',
		 'birthday' => '',
		 'email' => $user_email,
		 'phone' => '',
		 'skin_type' => 0,
		 'updated' => 0,
		 'last_login' => date_timestamp_get($timestamp),
		 'promo' => '',
		 ));
		 $dataUser = $this->CT->post();

		 
	  	 $this->assign('dataUser', $dataUser['response']);
		 
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
 		
	
		 
		 
		}
		
// twitter feed
 		
 		// get Replies for HELP (messages)
 		$user_id = $data['response']['id'];
		$this->CT->api = "en.help.getList";
        $helpReplyUnread = $this->CT->getList("sender=" . $user_id . " and reply_id>0 and reply_read=0",  array('order' => "id desc"));
        $helpReplyUnreadCount = count($helpReplyUnread['response']);
 		$this->assign('helpReplyUnreadCount', $helpReplyUnreadCount);
		  
		
		//total orders
		$this->CT->api = "en.order.getList";
        $ordersResponse = $this->CT->getList("user_id=" . $this->user['id'], array("order" => "id desc"));
        $ordersCount = count($ordersResponse['response']);
		
		$allOrders = $ordersResponse;
		
		foreach ($allOrders as $key => $value) {
			$total_spent = $total_spent + $allOrders['response'][$key]['receive_amount'];  
		}
		
		
		
		$this->assign('allOrders', $allOrders);
		$this->assign('ordersCount', $ordersCount);
		$this->assign('total_spent', $total_spent); 
		
		
		
 		//get promo for user
 	 	$this->CT->api = 'en.promo.getByEmail';	
	 	$this->CT->email = $user_email;
		$dataPromoCode = $this->CT->get();
		$this->assign('dataPromoCode', $dataPromoCode['response']); 
	
	
	
				// count all points
				$this->CT->api = "en.order.getList";
                $ordersResponsePaid = $this->CT->getList("user_id=" . $this->user['id']. " and status='已付款'", array("order" => "id desc"));
                $ordersPaid = $ordersResponsePaid['response'];
				$this->assign('ordersPaid', $ordersPaid);
				
 	
 	// first day and last day for each month whole year
 	$first_minute = mktime(0, 0, 0, date("n"), 1);
	$last_minute = mktime(23, 59, 0, date("n"), date("t"));
	
				// count all points
				$this->CT->api = "en.order.getList";
                $ordersResponsePaidThisMonth = $this->CT->getList("user_id=" . $this->user['id']. " and status='已付款' and created>".$first_minute." and created<".$last_minute."", array("order" => "id desc"));
                $ordersPaidThisMonth = $ordersResponsePaidThisMonth['response'];
				$this->assign('ordersPaidThisMonth', $ordersPaidThisMonth);
				
		
		$this->assign('data', $data['response']);
        
        
        if ($data['response']['promo'] == 'USED'){
                    
                    
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
        
            for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            
            // get id in promo table to UPDATE with new promo
            $this->CT->api = 'en.promo.getByEmail'; 
            $this->CT->email = $data['response']['email'];
            $userPromo = $this->CT->get();
            
            
            if ($userPromo['code'] ==1){
            $get_id_user_in_promo = $userPromo['response']['id'];
                
            $promo_temp = $randomString; 
                    
            $this->CT->api = 'en.promo.save';
            $this->CT->setParams(array(
            'id' => $get_id_user_in_promo,
            'promo' => $promo_temp,
            'used' => 0,
            ));
            $dataSave = $this->CT->post();
            
            
            
            $this->CT->api = 'en.ucenter.save';
            $this->CT->setParams(array(
            'mode' => 'update',
            'id' => $this->user['id'],
            'promo' => '',
            ));
            $dataResetPromo = $this->CT->post();  
 
                
            }
            else {
            $get_id_user_in_promo = 0;
            }
            
            
               
        }
        

			 
 		// end count all points
		$this->display('home');
		}
}
	


// =============================== MY ORDER =============================================

	public function myOrder() {
			
			
			       if ($this->user) {
        $user_id = $this->user['id'];
				   
				   
		$seo_ucenter_title['title'] = 'My Orders'; 
		$this->assign('seo_ucenter_title', $seo_ucenter_title['title']); 
		 	
				   
				   
				   }
	 
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$userInfo = $this->CT->get();
 		 
		$this->assign('userInfo', $userInfo['response']);
		
		if ( !$user_id){
			        redirect("/login.html"); 
		}
		
		else {
			
			 
                $this->CT->api = "en.order.getList";
                $ordersResponse = $this->CT->getList("user_id=" . $this->user['id'], array("order" => "id desc"));
                $orders = $ordersResponse['response'];
				$this->assign('orders', $orders);
				
				for ($j=0; $j < count($orders); $j++) {
				$itemIds[] = $orders[$j]['lines'][0]['goods_id'];
				}
				
				$itemIds = implode(',', $itemIds);
				      
				 $ids ='goods_id in ('.$itemIds.')';
				
				 
				 $this->CT->api = "en.goods.getList";
        		 $urlList = $this->CT->getList($ids);
       			 $urlList = $urlList['response'];
       			 
				 $allData_new = array();
				 foreach($urlList as $d) {
				 	$id = $d['goods_id'];
					 array_shift($d);
				 	$allData_new[$id] = $d;
				 }
				 
				 $this->assign('urlList', $allData_new);

						
		$seo_ucenter_title['title'] = 'My Orders'; 
		$this->assign('seo_ucenter_title', $seo_ucenter_title['title']);
		
		$this->display('myOrder'); 
            }
           
			

	}
	



// remove item from orederlist
      public function orderListRemove() {
       
      	if ($this->user) {
        $user_id = $this->user['id'];}
		 
				
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
 		 
		$this->assign('data', $data['response']);
		
		
		
		if ( !$user_id){
			redirect("/login.html"); 
		}
		
		else {
							
		$goods_id = $_POST['goods_id'];	
// 			
// 						
		// $this->CT->api = 'en.wishList.get';	
	 	// $this->CT->id = $goods_id_post;
		// $goodsTable = $this->CT->get();			
// 					
// 		 
// 			
// 		  
		// $remove_id = $goodsTable['response']['id'];
// 		  
		// $this->CT->api = 'en.wishList.delete';	
	 	// $this->CT->id = $remove_id;
		// $goodsTable = $this->CT->get();
		  
		  
		 
		  
	
		
		$all_vars = base64_decode($goods_id);
		$vars_exploded = explode('|', $all_vars);


 
    // echo "VARS SENT: <pre>";
    // print_r($vars_exploded);
    // echo "</pre>";
  

		$this->CT->api = 'en.order.get';	
	 	$this->CT->id = $vars_exploded[2];
		$goodsTable = $this->CT->get();		
	
	
	// echo "GET: <pre>";
    // print_r($goodsTable);
    // echo "</pre>";
	
	
	if (($goodsTable['response']['order_code'] == $vars_exploded[0]) and  ($goodsTable['response']['created'] == $vars_exploded[1]) and ($goodsTable['response']['id'] == $vars_exploded[2]) and ($vars_exploded[0] !='') and ($vars_exploded[1] !='') and ($vars_exploded[2] !=''))
{
		$this->CT->api = 'en.order.delete';	
	 	$this->CT->id = $vars_exploded[2];
		$goodsTable = $this->CT->get();			
	// echo 'deleted';
	$is_deleted = 1;
}		
		
else {
	// echo 'wrong hash';
	$is_deleted = 0;
}
		
		 
		$this->assign('is_deleted', $is_deleted); 
		 
      	$this->display("removeMyOrder");
			
		}
    }










// remove item from orederlist
      public function reviewRemove() {
       
      	if ($this->user) {
        $user_id = $this->user['id'];}
		 
				
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
 		 
		$this->assign('data', $data['response']);
		
		
		
		if ( !$user_id){
			redirect("/login.html"); 
		}
		
		else {
							
		$review_id = $_POST['review_id'];	
// 			
// 						
		// $this->CT->api = 'en.wishList.get';	
	 	// $this->CT->id = $goods_id_post;
		// $goodsTable = $this->CT->get();			
// 					
// 		 
// 			
// 		  
		// $remove_id = $goodsTable['response']['id'];
// 		  
		// $this->CT->api = 'en.wishList.delete';	
	 	// $this->CT->id = $remove_id;
		// $goodsTable = $this->CT->get();
		  
		  
		 
		  
	
		
		$all_vars = base64_decode($review_id);
		$vars_exploded = explode('|', $all_vars);


 
    // echo "VARS SENT: <pre>";
    // print_r($vars_exploded);
    // echo "</pre>";
  

		$this->CT->api = 'en.comments.get';	
	 	$this->CT->id = $vars_exploded[0];
		$commentsTable = $this->CT->get();		
	
	
	// echo "GET: <pre>";
    // print_r($commentsTable);
    // echo "</pre>";
	
	
	if (($commentsTable['response']['id'] == $vars_exploded[0]) and  ($commentsTable['response']['the_date'] == $vars_exploded[1]) and ($commentsTable['response']['created'] == $vars_exploded[2]) and ($vars_exploded[0] !='') and ($vars_exploded[1] !='') and ($vars_exploded[2] !=''))
{
		$this->CT->api = 'en.comments.delete';	
	 	$this->CT->id = $vars_exploded[0];
		$commentsTable = $this->CT->get();			
	// echo 'deleted';
	$is_deleted = 1;
}		
		
else {
	// echo 'wrong hash';
	$is_deleted = 0;
}
		// remove after test
		 
		$this->assign('is_deleted', $is_deleted); 
		 
      	$this->display("removeMyReview");
			
		}
    }









// =============================== MY ADDRESS =============================================
	
	public function myAddress() {
				
			
			
		if ($this->user) {
        $user_id = $this->user['id'];
		$user_email = $this->user['email'];
		
		$seo_ucenter_title['title'] = 'My address'; 
		$this->assign('seo_ucenter_title', $seo_ucenter_title['title']); 
		 	
		
				
		}
		 
				
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
 		 
		$this->assign('data', $data['response']);
		
		
		
		if ( !$user_id){
			redirect("/login.html"); 
		}
		
			
			
			
		if ( !$_POST['hidden_update_address'])
			{
				
				
				
		//check if record exist
		$this->CT->api = "en.billing.getList";
        $dataBilling = $this->CT->getList("billing_email='".$user_email."'");
	 	$this->assign('dataBilling', $dataBilling['response']);
		
		if ($dataBilling['code']== -1){
		//echo '<br><br><br><br> record created';
				
		
				
		$this->CT->api = 'en.billing.save';
		$this->CT->setParams(array(
 		'id' => 0,
 		'the_time'=> date("Y-m-d H:i:s"),
 		'billing_country'=>'',
 		'billing_first_name'=>'',
 		'billing_last_name'=>'',
 		'billing_company'=>'',
 		'billing_address_1'=>'',
 		'billing_address_2'=>'',
 		'billing_city'=>'',
 		'billing_state'=>'',
 		'billing_postcode'=>'',
 		'billing_email'=>$user_email,
 		'billing_phone'=>'',
 		'user_id'=>$user_id,
 		'is_delete'=>1,
 		'created'=>time(),
 		'updated'=>time(),
		'user_id' => $user_id,
		));
		$createBillingAddress = $this->CT->post();
				
		$statusAccount = 'SAVED';
		$this->assign('statusAccount', $statusAccount);		
		
 	
		}	
			
			
			else {
				$statusAccount = 'EXIST';
				$this->assign('statusAccount', $statusAccount);
				
				$this->CT->api = "en.billing.getList";
        		$dataBilling = $this->CT->getList("billing_email='".$user_email."'");
	 		 	$this->assign('dataBilling', $dataBilling['response']);
				
			}
				
			$this->display('myAddress');	
			} 

			else {
				
				
		
				
 
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
 		$this->assign('data', $data['response']);
		
		$id_user = $data['response']['id']; 
		

		switch ($_POST['hidden_update_address']) {
			
    case 1:
    	// update billing address
		$bill_addr = $_POST['bill_address'];
		$bill_city = $_POST['bill_city'];
		$bill_prov = $_POST['bill_prov'];
		$bill_country = $_POST['bill_country'];
		$bill_zip = $_POST['bill_postcode'];
		$bill_company = $_POST['bill_company'];		
		
		
		if ($_POST['bill_country'] == '')	{
     	$status_update_billing = '<span class="notification_error">Please select your country for: Billing</span>';
			}
		
			
		// get order id to update billing address !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!move after update billing info !!!!!!!
		// $this->CT->api = "en.order.getList";
        // $orderId = $this->CT->getList("user_id=" . $user_id, array("limit" => 1));
        // $orderId = $orderId['response'];
		// $this->assign('orderId', $orderId);
		
else {
		// get id to update billing
		$this->CT->api = "en.billing.getList";
        $dataBilling = $this->CT->getList("billing_email='".$user_email."'", array("limit" => 1));
	 		 
		 
		
    	// update billing address
		$this->CT->api = 'en.billing.save';
		$this->CT->setParams(array(
 		'id' => $dataBilling['response'][0]['id'],
		'user_id' => $user_id,
		'billing_country' => $bill_country,
		'billing_first_name' => $data['response']['first_name'],
		'billing_last_name' => $data['response']['last_name'],
		'billing_company' => $bill_company,
		'billing_address_1' => $bill_addr,
		'billing_address_2' => '',
		'billing_city' => $bill_city,
		'billing_state' => $bill_prov,
		'billing_postcode' => $bill_zip,
		'billing_email' => $data['response']['email'],
		'billing_phone' => $data['response']['phone'],
		
		));
		$updateBillingAddress = $this->CT->post();
		
		$this->assign('dataBilling', $updateBillingAddress['response']); 
		$status_update_billing = '<span class="notification_success">Your settings were saved for: Billing</span>';
}
		
		
			
	break;
  
    case 2:
		// update shipping address
    	$ship_addr = $_POST['ship_address'];
		$ship_city = $_POST['ship_city'];
		$ship_prov = $_POST['ship_prov'];
		$ship_country = $_POST['ship_country'];
		$ship_zip = $_POST['ship_zip'];	
		
		
		if ($_POST['ship_country'] == '')	{
     	$status_update_billing = '<span class="notification_error">Please select your country for: Shipping</span>';
			}
else {
	

		
		
		$this->CT->api = 'en.ucenter.save';
		$this->CT->setParams(array(
		'mode' => 'update',
		'id' => $id_user,
		'ship_addr' => $ship_addr,
		'ship_city' => $ship_city,
		'ship_prov' => $ship_prov,
		'ship_country' => $ship_country,
		'ship_zip' => $ship_zip,
		));
		$data = $this->CT->post();	
	$status_update_billing = '<span class="notification_success">Your settings were saved for: Shipping</span>';
}	
	break;
    
}

 
			$this->assign('status_update_billing', $status_update_billing); 
			$this->display('myAddress');	
			}
			
		
}
	

// =============================== SETTINGS =============================================

	public function settings() {
		
		
		if ($this->user) {
        $user_id = $this->user['id'];
		$user_email = $this->user['email'];	
		
		$seo_ucenter_title['title'] = 'Settings'; 
		$this->assign('seo_ucenter_title', $seo_ucenter_title['title']); 			
		
}

				
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
 		 
		$this->assign('data', $data['response']);
		
		 	 
		// get user details from oat_user table	to check if user logged in via facebook	 		 
		 $this->CT->api = 'en.user.getByUID';	
	 	 $this->CT->id = $data['response']['id'];
		 $oatUser = $this->CT->get();
 		 
		 $this->assign('oatUser', $oatUser);
		
		if ( !$user_id){
			redirect("/login.html"); 
		}
		
		else {
			
		if ($_POST['update']){
		
		// updating tables

		$id_user = $data['response']['id'];
		
	
		$first_name = $_POST['fname'];
		$last_name = $_POST['lname'];
		$nickname = $_POST['nickname'];
		//$birthday = $_POST['birthday'];
		
		$birthday = $_POST['birthday_date'].'/'.$_POST['birthday_month'].'/'.$_POST['birthday_year'];
		
		
		$gender = $_POST['gender'];	
		$email = $_POST['email'];	
		
		if ($email !=''){
				// compare new email with existing. If code= -1 NO Email into system, if code=0 Email exist.
		
		 	
				$this->CT->api = "en.user.getList";
                $emailResponse = $this->CT->getList("email='".$email."'", array("order" => "id asc"));
               
				$this->assign('emailResponse', $emailResponse);
				
				if ($emailResponse['code'] == -1){
					$email = $email;
					$email_notification = '<span class="success">Email updated!</span>';
					
					
					
				// updating email into user table
				$this->CT->api = "en.user.save";
                $this->CT->setParams(array('email' => $email, 'id' => $user_id, 'status' => 'y', 'the_time' => date("Y-m-d H:i:s")));
                $updateEmailUser = $this->CT->post();
				
				
				// updating email into user_info table
				$this->CT->api = "en.ucenter.save";
                $this->CT->setParams(array('email' => $email, 'id' => $user_id));
                $updateEmailUserCenter = $this->CT->post();
					
					
					
				}
				
					else{
						$email=$emailResponse['code']['response'][0]['email'];
						$email_notification = '<span class="error">This email address already exists please choose a different one.</span>';
					}
			 
					// echo "post<pre>";
					// print_r($email);
					// print_r($emailResponse);
					// echo "</pre>";
		 
		 $this->assign('email_notification', $email_notification);		
		// END compare new email with existing
		}
	
		$phone = $_POST['phone'];
		if ($email == ''){
			$email = $data['response']['email'];
		}


			$this->CT->api = 'en.ucenter.save';
			$this->CT->setParams(array(
			'mode' => 'update',
			'id' => $id_user,
			'first_name' => $first_name,
			'last_name' => $last_name,
			'nick_name' => $nickname,
			'birthday' => $birthday,
			'gender' => $gender,
			'email' => $email,
			'phone' => $phone,
			));
			$data = $this->CT->post();
			
			// get id to update phone number
			$this->CT->api = "en.billing.getList";
        	$dataBilling = $this->CT->getList("billing_email='".$email."'", array("limit" => 1));
			
			
		// update phone number	
		$this->CT->api = 'en.billing.save';
		$this->CT->setParams(array(
 		'id' => $dataBilling['response'][0]['id'],
		'billing_phone' => $phone,
		
		));
		$updateBillingAddress = $this->CT->post();
			
			
			
			
			
			
			
		// get order id to update billing address
		$this->CT->api = "en.order.getList";
        $orderId = $this->CT->getList("user_id=" . $user_id, array("limit" => 1));
        $orderId = $orderId['response'];
		$this->assign('orderId', $orderId);
			
			
			if ($orderId[0]['billing_id'] !=''){
			// updating phone, email into billing table
			$this->CT->api = 'en.billing.save';
			$this->CT->setParams(array(
			'id' => $orderId[0]['billing_id'],
		 	'billing_first_name' => $first_name,
			'billing_last_name' => $last_name,
			'billing_email' => $email,
			'billing_phone' => $phone,
			));
			$data = $this->CT->post();
				
			}
			
			
			
		 

		 $this->assign('settings_response', '1');   // set response 1 or 0;   1=user pressed on Update; 0 user pressed on menu settings
		 
		 
		 $this->CT->api = 'en.ucenter.getByUID';	
	 	 $this->CT->id = $user_id;
		 $data = $this->CT->get();
 		 
		 $this->assign('data', $data['response']);
		 
		
		 
		 	
			
			$this->CT->api = "en.skinType.getList";
                $skinTypeResponse = $this->CT->getList("id<=9", array("order" => "id asc"));
                $skinType = $skinTypeResponse['response'];
				$this->assign('skinType', $skinType);
				
 
				
				if ($data['response']['skin_type'] !=0){
					$checked_id = $data['response']['skin_type'];
				}
				
				else {
					$checked_id = 0;
				}
				
				$this->assign('checked_id', $checked_id);
			
			
			
			$this->display('settings');
			} else {
				
				
				
				$this->CT->api = "en.skinType.getList";
                $skinTypeResponse = $this->CT->getList("id<=9", array("order" => "id asc"));
                $skinType = $skinTypeResponse['response'];
				$this->assign('skinType', $skinType);
				
 
				
				if ($data['response']['skin_type'] !=0){
					$checked_id = $data['response']['skin_type'];
				}
				
				else {
					$checked_id = 0;
				}
				
				
				
				
				$this->assign('checked_id', $checked_id);
				$this->display('settings');
			}
			 
			
		}
	
}
	





// saveMySkinType
      public function saveMySkinType() {
       
      	if ($this->user) {
        $user_id = $this->user['id'];
		}
		 
				
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
 		 
		$this->assign('data', $data['response']);
		
		
		
		if ( !$user_id){
			redirect("/login.html"); 
		}
		
		else {
							
			$skin_checked = $_POST['skin_checked'];	
			
			if ($skin_checked !=''){
								
								
					if ($data['response']['skin_type'] != $skin_checked){
 						
 					$this->CT->api = 'en.ucenter.save';
					$this->CT->setParams(array(
					'mode' => 'update',
					'id' => $user_id,
					'skin_type' => $skin_checked,
				 	
				 	));
					$saveSkinType = $this->CT->post();
 			}
			}
			
 
 
			// get all skin types
			$this->CT->api = "en.skinType.getList";
            $skinTypeResponse = $this->CT->getList("id<=9", array("order" => "id asc"));
            $skinType = $skinTypeResponse['response'];
			$this->assign('skinType', $skinType);
			$this->assign('skin_checked', $skin_checked);				
				
			
			
			$this->display('saveMySkinType');
		}
	  }

// =============================== PASSWORD =============================================
	
	public function password() {

		$seo_ucenter_title['title'] = 'Password'; 
		$this->assign('seo_ucenter_title', $seo_ucenter_title['title']); 
			
		
		if ($_POST) {
			$v = array();
			$v[0] = $_POST['current_password'];
			$v[1] = $_POST['new_password'];
			$v[2] = $_POST['confirm_password'];
			$this->assign('updatePassword', $v);
			
	
		 
		if ($this->user) {
        $user_id = $this->user['id'];}
        $this->CT->api = 'en.user.get';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
		 
		$this->assign('dataUser', $data['response']);
			
			 
		if (($v[1] == $v[2]) and ($data['response']['password'] == "other" or $data['response']['password'] == md5($v[0])))
			{
	
 			$this->CT_Api->api = "en.user.updatePassword";
            $this->CT_Api->setParams(array('password' => $v[1], 'id' => $data['response']['id'])); 
            $this->CT_Api->post();
			$this->display('updatePassword');	
			}
			else{
			$this->display('updatePasswordWrong');
}		
			
		}
		else {
			if ($this->user) {
		        $this->CT->api = 'en.user.get';
			 	$this->CT->id = $this->user['id'];
				$data = $this->CT->get();
				if ($data['response']['password'] == "other") {
					$this->assign("login_method", "facebook");
				}
			}
			$this->display('password');
		}
		

		
}


// =============================== POINTS =============================================	
	
	public function points() {
					
		if ($this->user) {
        $user_id = $this->user['id'];
		
		$seo_ucenter_title['title'] = 'Points'; 
		$this->assign('seo_ucenter_title', $seo_ucenter_title['title']); 		
		
		}
	 
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$userInfo = $this->CT->get();
 		 
		$this->assign('userInfo', $userInfo['response']);
		
		if ( !$user_id){
			        redirect("/login.html"); 
		}
		
		else {
					
			 
                $this->CT->api = "en.order.getList";
                $ordersResponse = $this->CT->getList("user_id=" . $this->user['id'], array("order" => "id desc"));
                $orders = $ordersResponse['response'];
				$this->assign('orders', $orders);
				
				
				  $this->CT->api = "en.order.getList";
                $ordersResponsePaid = $this->CT->getList("user_id=" . $this->user['id']. " and status='已付款'", array("order" => "id desc"));
                $ordersPaid = $ordersResponsePaid['response'];
				$this->assign('ordersPaid', $ordersPaid);
				
 
				
				for ($j=0; $j < count($orders); $j++) {
				$itemIds[] = $orders[$j]['lines'][0]['goods_id'];
				}
				
				
				$itemIds = implode(',', $itemIds);
				
 
				       
				      
				 $ids ='goods_id in ('.$itemIds.')';
 
				 
				 $this->CT->api = "en.goods.getList";
        		 $urlList = $this->CT->getList($ids);
       			 $urlList = $urlList['response'];
       			 
				 $allData_new = array();
				 foreach($urlList as $d) {
				 	$id = $d['goods_id'];
					 array_shift($d);
				 	$allData_new[$id] = $d;
				 }
				 
				 $this->assign('urlList', $allData_new);

				
				$this->CT->api = "en.redeemOrder.getList";
        		$ordersRedeemResponse = $this->CT->getList("user_id=" . $this->user['id'], array("order" => "id desc"));
        		$ordersRedeem = $ordersRedeemResponse['response'];
				$this->assign('ordersRedeem', $ordersRedeem);
				
				// update points
				
				
				foreach ($ordersPaid as $key => $value){
  		
  		$price_of_item = $ordersPaid[$key]['the_amount'];
  		$total_spent_money += $price_of_item; 
  	
  	
  }
  
 

	 
        	
        	switch (true) {
        		
        		case (($total_spent_money >= 0) and ($total_spent_money <=499)):
        	  
        		$points_range = 2;
        		break;
        		
        		
        		
        		case (($total_spent_money >=500) and ($total_spent_money <=999)):
        		 
        		$points_range = 3;
        		break;
        		
        		
        		case (($total_spent_money >=1000) and ($total_spent_money <=2399)):
        		
        		$points_range = 4;
        		break;
        		
        		
        		case (($total_spent_money >=2400) and ($total_spent_money <=4799)):
        		
        		$points_range = 5;
        		break;
        		
        		
        		case ($total_spent_money >=4800):
        		
        		$points_range = 6;
        		break;
 
        		
        	}

         

  
 foreach ($ordersPaid as $key => $value){
 	
 	
  $amount_item = $ordersPaid[$key]['the_amount'];
 
 	$pointsPerOrder = floor($amount_item / 30)*$points_range;
 	
 	
 	$totalPoints = $totalPoints + $pointsPerOrder;  		  
 		  
 }
 
 
 foreach ($ordersRedeem as $key => $value) {
     if ($ordersRedeem[$key]['response'] == 'SUCCESS'){
 		 $totalPointsSpent += $ordersRedeem[$key]['the_amount'];
 	}
 	else {
 		$totalPointsSpent += 0;
 	}
 }
$points_available = $totalPoints - $totalPointsSpent;

// updating database with points

			$this->CT->api = 'en.points.save';
			$this->CT->setParams(array(
			
			 
			'user_id' => $this->user['id'],
			'points' => $points_available,
			'updated' => time(),
		
			));
			$pointsNewAmountSave = $this->CT->post();				
				
				// END update points
				
				
				
				$this->display('points'); 
            }
           
			

	}  



// =============================== WISHLIST =============================================	
	
	public function wishList() {
				
		if ($this->user) {
        $user_id = $this->user['id'];
		
		$seo_ucenter_title['title'] = 'Wish List'; 
		$this->assign('seo_ucenter_title', $seo_ucenter_title['title']); 

		}
		
		 
				
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
 		 
		$this->assign('data', $data['response']);
		
			  
		 
		
		
		if ( !$user_id){
			echo 'login';
			//redirect("/login.html"); 
		}
		
		else {
			
					// read wish list items	
		
		$this->CT->api = "en.wishList.getList";
        $wishList = $this->CT->getList("user_id=" . $user_id . "",  array('order' => "id desc"));
        $dataWishList = $wishList['response'];
 		$this->assign('dataWishList', $dataWishList);
		

 // saving all ids
	$k=0;		
	foreach ($dataWishList as $key => $value) {
		$array_ids[$k] = $dataWishList[$key]['goods_id'];
		
		$k++; 
	}
		
 
		// get goods id
					$goods_id_post = intval($_POST['goods_id']);
		 
		 		    // if pressed Add to wish list
		 		    if ($goods_id_post !=0){
		 		    
					
					
					// if goods id already exist into DB then exit without saving else - save to DB
				 foreach ($array_ids as $key => $value) {
					
					 if ($goods_id_post == $array_ids[$key]) {
					 		$exist = 1;
					 	break;
					 } 
					 else {
					 	$exist = 0;
					 }
				 }
					
					
 				if ($exist == 0) {
 					
					// save item to wish list
					$this->CT->api = 'en.wishList.save';
					$this->CT->setParams(array(
					'id' => 0,
					'goods_id' => $goods_id_post,
					'user_id' => $user_id,
				 	 
					));
					$save = $this->CT->post();
					echo 'SAVED';
					
		}
				 
    	
		 		    }
		 		    
		 	
					
					
					else{
						
	 
		
		
		 /*  get details like: tag + url  for each product  */ 

 		$i=0;
 		foreach ($dataWishList as $key => $value) {
 			
		$goods_id = $dataWishList[$key]['goods_id'];
		$this->CT->api = 'en.goods.get';	
	 	$this->CT->id = $goods_id;
		$goods[$i] = $this->CT->get();
		$i++;	
			
			
		 }
 		$this->assign('goods', $goods);	
 	$this->display('wishList');
						
					}
					
 
	
		}
	}			  
	



// remove item from wishlist
      public function wishListRemove() {
       
      	if ($this->user) {
        $user_id = $this->user['id'];}
		 
				
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
 		 
		$this->assign('data', $data['response']);
		
		
		
		if ( !$user_id){
			redirect("/login.html"); 
		}
		
		else {
							
		$goods_id_post = intval($_POST['goods_id']);	
			
						
		$this->CT->api = 'en.wishList.get';	
	 	$this->CT->id = $goods_id_post;
		$goodsTable = $this->CT->get();			
					
		 
			
		  
		$remove_id = $goodsTable['response']['id'];
		  
		$this->CT->api = 'en.wishList.delete';	
	 	$this->CT->id = $remove_id;
		$goodsTable = $this->CT->get();
		  
		  if ($goodsTable['code'] == 1){
		  	echo '1';
		  }
else {
		echo '0';
}  
	  
		  
     // $this->display("wishList");
			
		}
    }




// =============================== MESSAGES =============================================

	
	public function systemMessages() {
	
		if ($this->user) {
        $user_id = $this->user['id'];}
			
			
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$userInfo = $this->CT->get();
 		 
		$this->assign('userInfo', $userInfo['response']);
		
		
		
		if ( !$user_id){
			redirect("/login.html"); 
		}
		else {
			
			
			
		// read system messages	
		$user_id = $userInfo['response']['id'];
		$this->CT->api = "en.messages.getList";
        $DataMessagesResponse = $this->CT->getList("id>0",  array('order' => "id desc"));
        $systemMessages = $DataMessagesResponse['response'];
 		$this->assign('systemMessages', $systemMessages);
		
		
		 
			
		$this->display('systemMessages');
		}
	

}			  



      public function messagesLoadMessage() {
       
        
      if ($this->user) {
        $user_id = $this->user['id'];}
			
			
			
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$userInfo = $this->CT->get();
 		 
		$this->assign('userInfo', $userInfo['response']);
		
		
		
		if ( !$user_id){
			redirect("/login.html"); 
		}
		else {
				
				
				$hash = $_GET['mid'];
			
			// read message (single)
			$this->CT->api = "en.messages.get";
      		$this->CT->id = $hash;
       		$dataMessage = $this->CT->get();
 			$this->assign('dataMessage', $dataMessage);
			
			
			
			
			$this->CT->api = 'en.ucenter.getByUID';	
	 		$this->CT->id = $dataMessage['response']['sender'];
			$userInfo = $this->CT->get();
 		 	$this->assign('userInfo', $userInfo['response']);
			
		 
 
 	$save_or_not = $dataMessage['response']['read'];
 
 switch ($save_or_not) {
     case 0:
        			// mark message as read into DB
					$this->CT->api = 'en.messages.save';
					$this->CT->setParams(array(
					'id' => $dataMessage['response']['id'],
					'is_read' => 1,
				 	 
					));
					$markRead = $this->CT->post();
         break;
     
     case 1:
         			
         break;
 }
 

			$this->display('helpLoadMessage');
		}
		
    }


	


// =============================== REVIEWS =============================================
	
	public function review() {

		 if ($this->user) {
         $user_id = $this->user['id'];
		 
		$seo_ucenter_title['title'] = 'Reviews'; 
		$this->assign('seo_ucenter_title', $seo_ucenter_title['title']); 
		 
		 
		 }
			
			
			
		 $this->CT->api = 'en.ucenter.getByUID';	
	 	 $this->CT->id = $user_id;
		 $data = $this->CT->get();
 		 
		 $this->assign('data', $data['response']);
		
		
// 		 
			// echo "<pre>";
			// print_r($data);
			// echo "</pre>";
		 
		
		
		if ( !$user_id){
			redirect("/login.html"); 
		}
		else {
			
		$item_goods_id = $_POST['item_goods_id'];
		$user_id = $this->user['id'];
		$today = date("Y-m-d H:i:s"); 
		$item_stars = $_POST['rating'];
		$item_status = 'y';
		$item_comment = htmlspecialchars($_POST['comment']);
		
		$cutted_string = substr($item_comment,0,999);
		$review = $cutted_string; 
		
		$item_url = $_POST['comment_url'];
		$title_review = $_POST['title_review'];
		
		// echo 'Comment review: <pre>';
// 		
		// echo '$item_goods_id='; echo $item_goods_id; echo '<br>';
		// echo '$user_id='; echo $user_id; echo '<br>';
		// echo '$item_stars='; echo $item_stars; echo '<br>';
		// echo '$item_status='; echo $item_status; echo '<br>';
		// echo '$item_url='; echo $item_url; echo '<br>';
		// echo '$title_review='; echo $title_review; echo '<br>';
// 		 
		// echo '$item_comment='; echo $item_comment; echo '<br>';
		// echo '</pre>';
// 		
		if ($item_comment){
		$this->CT->api = 'en.comments.save';
			$this->CT->setParams(array(
			 
			'goods_id' => $item_goods_id,
			'user_id' => $user_id,
			'the_date' => date("Y-m-d H:i:s"),
			'starts' => $item_stars,
			'status' => $item_status,
			'content' => $review,
			'title_review' => $title_review,
			));
			$saveComment = $this->CT->post();	
		}

		// echo '<pre>';
		
		// print_r($review);
		// echo '</pre>';
		// echo '<pre>';
		
		// print_r($saveComment);
		// echo '</pre>';
		
			
		$this->assign('comment', $comment);
			
		$this->CT->api = "en.comments.getList";
        $allDataResponse = $this->CT->getList("user_id=" . $user_id . "", $options, "");
        $userComments = $allDataResponse['response'];
 		$this->assign('userComments', $userComments);
		
		 
	
//   
     // echo "<pre>";
     // print_r($userComments);
     // echo "</pre>";
    
 /*  get details like: tag + url  for each product  */ 
 
		for ($i=0; $i < count($userComments); $i++) { 
			
		$goods_id = $userComments[$i]['goods_id'];
			
		$this->CT->api = 'en.goods.get';	
	 	$this->CT->id = $goods_id;
		$goods[$i] = $this->CT->get();
	
		}
		
		$this->assign('goods', $goods);	
		
/*  END get details like: tag + url  for each product  */ 
		 
		$this->display('review');
		}
	}


// =============================== HELP =============================================
	
	
	public function help() {
			
		if ($this->user) {
        $user_id = $this->user['id'];
		
		$seo_ucenter_title['title'] = 'Messages'; 
		$this->assign('seo_ucenter_title', $seo_ucenter_title['title']); 
		 		
		}
			
		
			
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$userInfo = $this->CT->get();
		$is_admin = $data['response']['sales'];
 		 
		$this->assign('userInfo', $userInfo['response']);
		
		// echo '<pre>';
		// print_r($userInfo);
		// echo '</pre>';
 	
		if ( !$user_id){
			redirect("/login.html"); 
		}
		else {
			
			switch ($_POST['user_help_status']) {
				case 1:
					
					$user_form_get_topic = $_POST['user_topic'];
					$user_form_get_product = $_POST['user_product'];
					$user_form_get_phone = $_POST['user_phone'];
					$user_form_get_message = $_POST['user_message'];
					$user_form_get_created = time(); 
					
					
					
					if ($user_form_get_topic != '' and $user_form_get_product !='' and $user_form_get_message !=''){
						
					$hashString = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 1) . substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 20);
					
					$this->CT->api = 'en.help.save';
					$this->CT->setParams(array(
					'id' => 0,
					'sender' => $userInfo['response']['id'],
					'topic' => $user_form_get_topic,
					'product' => $user_form_get_product,
					'phone' => $user_form_get_phone,
					'message' => $user_form_get_message,
					'created' => $user_form_get_created,
					'is_read' => 0,
					'reply_id' => 0,
					'reply_read' => 0,
					'hash'=> $hashString,
					));
					$helpSent = $this->CT->post();
					
					
					if ($helpSent[code] == -1){
						$user_form_message = '<p class="red">ERROR</p>';
					}
					
else {
	$user_form_message = '<span class="notification_success">Your message has been sent.</span>';
}
					
				//	$user_form_message = '<p class="green">Your message has been sent.</p>';
					$this->assign('user_form_message', $user_form_message);
					}
										
					else {
						
					$user_form_message = '<div><span class="notification_error">Please fill all fields.</span></div>';
					$this->assign('user_form_message', $user_form_message);	
						
					}
					
					
					break;
				
				case '':
					$user_form_message = '';
					$this->assign('user_form_message', $user_form_message);
					break;
			}
			
			
			
			
			
			$this->display('help');
		}
		
	}
	


	
	public function helpHistory() {
			
		if ($this->user) {
        $user_id = $this->user['id'];}
			
			
			
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$userInfo = $this->CT->get();
 		 
		$this->assign('userInfo', $userInfo['response']);
		
		
		
		if ( !$user_id){
			redirect("/login.html"); 
		}
		else {
			
			
			
		$user_id = $userInfo['response']['id'];
		$this->CT->api = "en.help.getList";
        $allDataResponse = $this->CT->getList("sender=" . $user_id . "",  array('order' => "id desc"));
        $helpMessages = $allDataResponse['response'];
 		$this->assign('helpMessages', $helpMessages);
		
	
			
			
			$this->display('helpHistory'); 
		}
		
	}






	public function supportSaveReplyHelpMessage() {
			
		if ($this->user) {
        $user_id = $this->user['id'];}
			
			
			
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$userInfo = $this->CT->get();
 		 
		$this->assign('userInfo', $userInfo['response']);
		
		
		
		if ( !$user_id){
			redirect("/login.html"); 
		}
		else {
			
		
		
			$support_help_reply = $_POST['support_help_reply'];
			
			//echo $support_help_reply.'<br>';
			
			$support_user_receiver = $_POST['support_user_receiver'];
			
			//echo $support_user_receiver.'<br>';
			
			$support_user_hash = $_POST['support_user_hash'];
			
			//echo $support_user_hash.'<br>';
			
			$support_user_message_id = $_POST['support_user_message_id'];
			
			//echo $support_user_message_id.'<br>';
			
			
			$support_message = $_POST['support_message'];
			
			if ($support_message !=''){
			
					$this->CT->api = 'en.helpReply.save';
					$this->CT->setParams(array(
					'id' => 0,
					'receiver' => $support_user_receiver,
				 	'support_id_reply' => $user_id,
				 	'message' => $support_message,
				 	'is_read' => 0,
				 	'message_id' => $support_user_message_id,
				 	'hash' => $support_user_hash,
				 	'created' => time(),
					));
					$saveReply = $this->CT->post();
					
					
					// get id to save into help table to see if user have notification
					
					$this->CT->api = "en.helpReply.getList";
			        $dataReplyId = $this->CT->getList("hash='".$support_user_hash."'");
				 	//$this->assign('dataReplyId', $dataReplyId['response'][0]);
			 		//$this->display('billingAddress');
			 		$getReplyInfo = $this->CT->post();
			 		
			 		
			 		
			 		$this->CT->api = 'en.help.save';
					$this->CT->setParams(array(
					'id' => $support_user_message_id,
					'reply_id' => $dataReplyId['response'][0]['id'],
				 	 
					));
					$updateReply = $this->CT->post();
			 		
								
					
					
					
					if ($saveReply['code'] != -1){
						$replyStatus = 'Your messages was sent.'; 
						
					}
					
					else {
						$replyStatus = 'ERROR'; 
						
					}
					
					$this->assign('replyStatus', $replyStatus);
						
			
			
			
			
				
				
			}
			 
				
			
			
		  	$this->display('supportSaveResult');
		}
		
	}





public function supportCreateNotification() {
			
		if ($this->user) {
        $user_id = $this->user['id'];}
			
			
			
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$userInfo = $this->CT->get();
 		 
		$this->assign('userInfo', $userInfo['response']);
		
		
		
		if ( !$user_id){
			redirect("/login.html"); 
		}
		else {
			
 
 
 			$command_notification = $_POST['creator_notification_command'];
 
 			if ($command_notification == 1){
 				
				
		 	$user_id = $_POST['creator_notification'];
			$sender_notification_message = $_POST['notification_message'];
			$sender_notification_topic = $_POST['notification_topic'];
			
			
				
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    		$charactersLength = strlen($characters);
    		$randomString = '';
		
    		for ($i = 0; $i < 10; $i++) {
        	$randomString .= $characters[rand(0, $charactersLength - 1)];
	    	}		
				
				
			if (($sender_notification_topic !='') and ($sender_notification_message !='')){
				$this->CT->api = 'en.messages.save';
					$this->CT->setParams(array(
					'id'=>0,
					'receiver' => 0,
					'sender' => $user_id,
					'topic' => $sender_notification_topic,
					'message' => $sender_notification_message,
					'is_read' => 0,
					'reply_id' => 0,
					'reply_read' => 0,
					'hash' => $randomString,
					'created' => time(),
					
				 	 
					));
					$notificationCreate = $this->CT->post();	
			}	
				
		
				
			 
							
			redirect("/index.php?m=ucenter&c=index&a=support");
				 
				
 			  	
 			}
			
		
			
			
			$this->assign('status_notification', $status_notification);
			$this->display('supportCreateNotification'); 
			 
		}
		
	}





      public function helpLoadMessage() {
       
        
      if ($this->user) {
        $user_id = $this->user['id'];}
			
			
			
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$userInfo = $this->CT->get();
 		 
		$this->assign('userInfo', $userInfo['response']);
		
		
		
		if ( !$user_id){
			redirect("/login.html"); 
		}
		else {
				
				
				$hash = $_GET['mid'];
			
			// read message (single)
			$this->CT->api = "en.help.get";
      		$this->CT->id = $hash;
       		$dataMessage = $this->CT->get();
 			$this->assign('dataMessage', $dataMessage);
			
			 
			 
			 
			
			$get_id_reply = $dataMessage['response']['reply_id'];
			
 
		
			// read reply
			 $this->CT->api = "en.helpReply.get";
      		 $this->CT->id = $get_id_reply;
       		 $messageReply = $this->CT->get();
 			 $this->assign('messageReply', $messageReply);

 
 
      
 
 
 switch ($get_id_reply) {
     case 0:
        			// mark message as read into DB
					$this->CT->api = 'en.help.save';
					$this->CT->setParams(array(
					'id' => $dataMessage['response']['id'],
					'is_read' => 1,
				 	 
					));
					$markRead = $this->CT->post();
         break;
     
	 default:
         			// mark message as read and as read reply message into DB
					$this->CT->api = 'en.help.save';
					$this->CT->setParams(array(
					'id' => $dataMessage['response']['id'],
					'is_read' => 1,
				 	'reply_read' => 1,
					));
					$markRead = $this->CT->post();
					
					
					
					// mark message as read (reply table) into DB
					$this->CT->api = 'en.helpReply.save';
					$this->CT->setParams(array(
					'id' => $messageReply['response']['id'],
					'is_read' => 1,
					));
					$markReadReply = $this->CT->post();
					
					
					
         break;
 }
  // get avatar sender
 
 		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $dataMessage['response']['sender'];
		$userInfoSender = $this->CT->get();
 		 
		$this->assign('userInfoSender', $userInfoSender['response']);
 
 // end get avatar sender
 	

		 
 	 // who reply ? 
 
 		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $messageReply['response']['support_id_reply'];
		$userInfoReply = $this->CT->get();
 		 
		$this->assign('userInfoReply', $userInfoReply['response']);
 
 // END who reply ?

			
			$this->display('helpLoadMessage');
		}
		
    }




      public function supportHelpLoadMessage() {
       
        
      if ($this->user) {
        $user_id = $this->user['id'];}
			
			
			
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$userInfo = $this->CT->get();
		$is_admin = $userInfo['response']['sales'];		
 		$this->assign('is_admin', $is_admin);
		$this->assign('userInfo', $userInfo['response']);
		
		
		
		if ( !$user_id){
			redirect("/login.html"); 
		}
		else {
				
				
			$hash = $_GET['mid'];
			
			// read message (single)
			$this->CT->api = "en.help.get";
      		$this->CT->id = $hash;
       		$dataMessage = $this->CT->get();
 			$this->assign('dataMessage', $dataMessage);
			
	 
			
			$get_id_reply = $dataMessage['response']['reply_id'];
			
			// get sender id
			$sender_id = $dataMessage['response']['sender'];
			
			$this->CT->api = 'en.ucenter.getByUID';	
		 	$this->CT->id = $sender_id;
			$senderInfo = $this->CT->get();
			$this->assign('senderInfo', $senderInfo['response']);
				

 
		
			// read reply
			 $this->CT->api = "en.helpReply.get";
      		 $this->CT->id = $get_id_reply;
       		 $messageReply = $this->CT->get();
 			 $this->assign('messageReply', $messageReply);
			 

 
 switch ($get_id_reply) {
     case 0:
        			// mark message as read into DB
					$this->CT->api = 'en.help.save';
					$this->CT->setParams(array(
					'id' => $dataMessage['response']['id'],
					'support_read' => 1,
				 	 
					));
					$markRead = $this->CT->post();
         break;
     
	 default:
         			// mark message as read and as read reply message into DB
					$this->CT->api = 'en.help.save';
					$this->CT->setParams(array(
					'id' => $dataMessage['response']['id'],
					'support_read' => 1,
				 	'reply_read' => 1,
					));
					$markRead = $this->CT->post();
					
					
					
					// mark message as read (reply table) into DB
					$this->CT->api = 'en.helpReply.save';
					$this->CT->setParams(array(
					'id' => $messageReply['response']['id'],
					'is_read' => 1,
					));
					$markReadReply = $this->CT->post();
					
					
					
         break;
 }
 
 // get avatar sender
 
 		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $dataMessage['response']['sender'];
		$userInfoSender = $this->CT->get();
 		 
		$this->assign('userInfoSender', $userInfoSender['response']);
 
 // end get avatar sender
 	

		 
 	 // who reply ? 
 
 		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $messageReply['response']['support_id_reply'];
		$userInfoReply = $this->CT->get();
 		 
		$this->assign('userInfoReply', $userInfoReply['response']);
 
 // END who reply ?
 	

			
			$this->display('helpLoadMessage');
		}
		
    }





// =============================== BILLLING ADDRESS =============================================	
	
public function billingAddress() {
		
		if ($this->user) {
        $user_id = $this->user['id'];
		$user_email = $this->user['email'];
		}
			
			
			
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
 		 
		$this->assign('data', $data['response']);
		
		
		
		if ( !$user_id){
			redirect("/login.html"); 
		}
		else {
			
		
		// get billing address
		$this->CT->api = "en.billing.getList";
        $dataBilling = $this->CT->getList("billing_email='".$user_email."'");
	 	$this->assign('dataBilling', $dataBilling['response'][0]);
 		$this->display('billingAddress');
		}
		
}


	public function changeBillingAddress() {
			
		if ($this->user) {
        $user_id = $this->user['id'];
		$user_email = $this->user['email'];
		}
			
			
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
 		
		$this->assign('data', $data['response']);
		
		
		if ( !$user_id){
			redirect("/login.html"); 
		}
		else {
			
			
		// get billing address
		$this->CT->api = "en.billing.getList";
        $dataBilling = $this->CT->getList("billing_email='".$user_email."'");
	 	$this->assign('dataBilling', $dataBilling['response'][0]);
 		
			
			
		$this->display('changeBillingAddress');
		}
		
		
}

// =============================== SHIPPING ADDRESS =============================================

	public function changeShippingAddress() {
			
		 if ($this->user) {
         $user_id = $this->user['id'];}
			
			
		 $this->CT->api = 'en.ucenter.getByUID';	
	 	 $this->CT->id = $user_id;
		 $data = $this->CT->get();
 		 
		 $this->assign('data', $data['response']);
		
		
		
		if ( !$user_id){
			redirect("/login.html"); 
		}
		else {
			$this->display('changeShippingAddress');
		}

		
}

 

	public function shippingAddress() {
			
		if ($this->user) {
        $user_id = $this->user['id'];}
			
			
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
 		 
		$this->assign('data', $data['response']);
		
		
		
		if ( !$user_id){
			redirect("/login.html"); 
		}
		else {
			$this->display('shippingAddress');
		}
		

}



// =============================== POPULAR =============================================


   public function popular() {
  
        // select all goods with views more then 0 (limit 200)
        $this->CT_Api->api = "en.goods.getList";
        $dataViewsResponse = $this->CT_Api->getList("view>0",  array('order' => "view desc", 'limit' => 200));
        
		
		// select all goods with max views to array
		$p=0;
		foreach ($dataViewsResponse['response'] as $key => $value) {
		$array_top_views[$dataViewsResponse['response'][$key]['view']] = $dataViewsResponse['response'][$key]['goods_id'];
		$array_keys[$dataViewsResponse['response'][$key]['view']] = $key;
		$p++;
		}
		
 
		// creating string with numbers		
		$goods_ids = implode(",", array_slice($array_top_views,0,4));
	 
		 
		 
		$ids ='goods_id in ('.$goods_ids.')';
		
		// get all goods into array		 
		$this->CT->api = "en.goods.getList";
        $urlList = $this->CT->getList($ids,  array('order' => "view desc"));
		
		
			 $urlList = $urlList['response'];
				 
				 $this->assign('urlList', $urlList);
 
		foreach ($urlList as $key => $value) {
		
		
		$item_id = $urlList[$key]['goods_id'];
			//echo $item_id."<br>";
		
		/* get all records for comment */
		$this->CT->api = "en.comments.getList";
        $allComments = $this->CT->getList("goods_id=" . $item_id . " and status='y'", array('order' => "the_date DESC"));
        $pageComments = $allComments['response'];
 		 
 
 
		$stars_1=0;
		$stars_2=0;
		$stars_3=0;
		$stars_4=0;
		$stars_5=0;
		
		
			
		// count stars

			for ($i=0; $i<=count($pageComments); $i++){
	 	switch ($pageComments[$i]['starts']) {
			case 1:
			$stars_1 ++;
			$total_1 += $pageComments[$i]['starts']; 	 
			break;
				
			case 2:
			$stars_2 ++;
			$total_2 += $pageComments[$i]['starts'];
			break;
					
			case 3:
			$stars_3 ++;
			$total_3 += $pageComments[$i]['starts'];
			break;
				
			case 4:
			$stars_4 ++;
			$total_4 += $pageComments[$i]['starts'];
			break;
				
			case 5:
			$stars_5 ++;
			$total_5 += $pageComments[$i]['starts'];
			break;	
				 
			 
		 }
			}

 
		
		$count_rating_step_5 = $stars_5 * 5;
		$count_rating_step_4 = $stars_4 * 4;
		$count_rating_step_3 = $stars_3 * 3;
		$count_rating_step_2 = $stars_2 * 2;
		$count_rating_step_1 = $stars_1 * 1;
		$count_rating_sum	 = $count_rating_step_1 + $count_rating_step_2 + $count_rating_step_3 + $count_rating_step_4 + $count_rating_step_5; 
		//echo 'count_rating_sum='.$count_rating_sum.'<br>';
		$sum_of_stars = $stars_5 + $stars_4 + $stars_3 + $stars_2 + $stars_1;
		//echo 'sum_of_stars='.$sum_of_stars.'<br>';
		$one_hundred_percent = $sum_of_stars * 5; // total 5 stars
		//echo 'one_hundred_percent='.$one_hundred_percent.'<br>';
		$show_percent = ($count_rating_sum / $one_hundred_percent) * 100; // get percent of stars 
		//echo 'show_percent='.$show_percent.'<br>';
		$show_percent = round($show_percent, 2); // round number like: 88.27 instead 88.268954
		//echo 'show_percent='.$show_percent.'<br>';
		
		 
		$stars_of_five = ($count_rating_sum / $one_hundred_percent) * 5; // example: 4.15 of 5
		//echo 'stars_of_five='.$stars_of_five.'<br><br>'; 
		$stars_of_five = round($stars_of_five, 2); // round number like: 4.27 instead 4.268954
		
		
		$show_percent_array[$key]=$show_percent;
		$show_stars_of_five[$key]=$stars_of_five;	
		}
		
		

		 
		$this->assign('show_percent_array', $show_percent_array);
		$this->assign('show_stars_of_five', $show_stars_of_five);	 
	 

  
        $this->display("popular");
}


// =============================== RECOMMENDATION =============================================   
   
      public function recomendation() {
       
        		if ($this->user) {
        $user_id = $this->user['id'];}
			
			
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
 		 
		$this->assign('data', $data['response']);
		
		
	 
 
				 // products ID's
				 $guessIds ='551,7,8,9';
 
				      
				 $ids ='goods_id in ('.$guessIds.')';
 
				 $this->CT->api = "en.goods.getList";
        		 $urlList = $this->CT->getList($ids);
       			 $urlList = $urlList['response'];
				 
				 $this->assign('urlList', $urlList);

				 
	 
foreach ($urlList as $key => $value) {
		
		
		$item_id = $urlList[$key]['goods_id'];
			//echo $item_id."<br>";
		
		/* get all records for comment */
		$this->CT->api = "en.comments.getList";
        $allComments = $this->CT->getList("goods_id=" . $item_id . " and status='y'", array('order' => "the_date DESC"));
        $pageComments = $allComments['response'];
 		 
			  

		$stars_1=0;
		$stars_2=0;
		$stars_3=0;
		$stars_4=0;
		$stars_5=0;
		
		
			
		// count stars

			for ($i=0; $i<=count($pageComments); $i++){
	 	switch ($pageComments[$i]['starts']) {
			case 1:
			$stars_1 ++;
			$total_1 += $pageComments[$i]['starts']; 	 
			break;
				
			case 2:
			$stars_2 ++;
			$total_2 += $pageComments[$i]['starts'];
			break;
					
			case 3:
			$stars_3 ++;
			$total_3 += $pageComments[$i]['starts'];
			break;
				
			case 4:
			$stars_4 ++;
			$total_4 += $pageComments[$i]['starts'];
			break;
				
			case 5:
			$stars_5 ++;
			$total_5 += $pageComments[$i]['starts'];
			break;	
				 
			 
		 }
			}

 
		
		$count_rating_step_5 = $stars_5 * 5;
		$count_rating_step_4 = $stars_4 * 4;
		$count_rating_step_3 = $stars_3 * 3;
		$count_rating_step_2 = $stars_2 * 2;
		$count_rating_step_1 = $stars_1 * 1;
		$count_rating_sum	 = $count_rating_step_1 + $count_rating_step_2 + $count_rating_step_3 + $count_rating_step_4 + $count_rating_step_5; 
		//echo 'count_rating_sum='.$count_rating_sum.'<br>';
		$sum_of_stars = $stars_5 + $stars_4 + $stars_3 + $stars_2 + $stars_1;
		//echo 'sum_of_stars='.$sum_of_stars.'<br>';
		$one_hundred_percent = $sum_of_stars * 5; // total 5 stars
		//echo 'one_hundred_percent='.$one_hundred_percent.'<br>';
		$show_percent = ($count_rating_sum / $one_hundred_percent) * 100; // get percent of stars 
		//echo 'show_percent='.$show_percent.'<br>';
		$show_percent = round($show_percent, 2); // round number like: 88.27 instead 88.268954
		
		
		 
		$stars_of_five = ($count_rating_sum / $one_hundred_percent) * 5; // example: 4.15 of 5
		//echo 'stars_of_five='.$stars_of_five.'<br><br>'; 
		$stars_of_five = round($stars_of_five, 2); // round number like: 4.27 instead 4.268954
		
		
		$show_percent_array[$key]=$show_percent;
		$show_stars_of_five[$key]=$stars_of_five;	
		}
		

		 
		$this->assign('show_percent_array', $show_percent_array);
		$this->assign('show_stars_of_five', $show_stars_of_five);	 
	 

		$this->display("recomendation");
		 
		
      
    }



// =============================== REDEEM =============================================   
   
      public function redeem() {
       
        if ($this->user) {
        $user_id = $this->user['id'];
		
		$seo_ucenter_title['title'] = 'Redeem'; 
		$this->assign('seo_ucenter_title', $seo_ucenter_title['title']); 

}
			
			
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
 		 
		$this->assign('data', $data['response']);
		
		
		
		if ( !$user_id){
			redirect("/login.html"); 
		}
		else {
 
				
						 
	$this->CT->api = 'en.redeem.getList';
	$dataRedeemResults = $this->CT->getList("active=1",  array('order' => "points asc"));
	
	$dataRedeemResults = $dataRedeemResults['response'];
	$this->assign('dataRedeemResults', $dataRedeemResults);
	
	
	 
 	 $i=0;
 		foreach ($dataRedeemResults as $key => $value) {
 			
		$goods_id = $dataRedeemResults[$key]['product_id'];
		$this->CT->api = 'en.goods.get';	
	 	$this->CT->id = $goods_id;
		$goods[$i] = $this->CT->get();
		$i++;	
			
			
		 }
 		$this->assign('goods', $goods);	
						


		// points 
       $this->CT->api = "en.order.getList";
                $ordersResponse = $this->CT->getList("user_id=" . $this->user['id'], array("order" => "id desc"));
                $orders = $ordersResponse['response'];
				$this->assign('orders', $orders);
				
				
				  $this->CT->api = "en.order.getList";
                $ordersResponsePaid = $this->CT->getList("user_id=" . $this->user['id']. " and status='已付款'", array("order" => "id desc"));
                $ordersPaid = $ordersResponsePaid['response'];
				$this->assign('ordersPaid', $ordersPaid);
				
 
				
				for ($j=0; $j < count($orders); $j++) {
				$itemIds[] = $orders[$j]['lines'][0]['goods_id'];
				}
				
				
				$itemIds = implode(',', $itemIds);
				
 
				       
				      
				 $ids ='goods_id in ('.$itemIds.')';
 
				 
				 $this->CT->api = "en.goods.getList";
        		 $urlList = $this->CT->getList($ids);
       			 $urlList = $urlList['response'];
       			 
				 $allData_new = array();
				 foreach($urlList as $d) {
				 	$id = $d['goods_id'];
					 array_shift($d);
				 	$allData_new[$id] = $d;
				 }
				 
				 $this->assign('urlList', $allData_new);


		// end points
		
		
		 		$this->CT->api = "en.redeemOrder.getList";
                $ordersRedeemResponse = $this->CT->getList("user_id=" . $this->user['id'], array("order" => "id desc"));
                $ordersRedeem = $ordersRedeemResponse['response'];
				$this->assign('ordersRedeem', $ordersRedeem);
		
		// read points left
		$this->CT->api = 'en.points.getByUID';	
	 	$this->CT->id = $this->user['id'];
		$userPointsLeft = $this->CT->get();
		$this->assign('userPointsLeft', $userPointsLeft);
			
		
		$this->display("redeem");
		}
		
      
    }



// =============================== ADD TO REDEEM CART =============================================  
 public function addToRedeemCart() {
			
		if ($this->user) {
        $user_id = $this->user['id'];
		$user_email = $this->user['email'];
		
		}
		
 
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
 		 
		$this->assign('data', $data['response']);
		
 
		$goods_id = $_POST['goods_id'];
 		$this->assign('goods_id', $goods_id);
 
 		$this->CT->api = 'en.goods.get';	
	 	$this->CT->id = $goods_id;
		$goods = $this->CT->get();
		$this->assign('goods', $goods);
 	
		
		$this->CT->api = 'en.redeem.getPointsByID';	
	 	$this->CT->id = $goods_id;
		$points = $this->CT->get();
 		$this->assign('points', $points);
		
	
	
	$this->display('addToRedeemCart');
		 
}
 
 
 
  public function updateRedeemCart() {
			
		if ($this->user) {
        $user_id = $this->user['id'];
		$user_email = $this->user['email'];
		
		}
		
 
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
 		 
		$this->assign('data', $data['response']);
		
 
		
		$updated_cookie = $_POST['updated_cookie']; 
		
		echo 'module: updated_cookie='.$updated_cookie;
		
			$this->CT->api = 'en.redeemInfo.save';
			$this->CT->setParams(array(
			'user_id' => $user_id,
			'items' => $updated_cookie,
			 
			'updated' => time(),
			));
			$updatesRedeem = $this->CT->post();
		 
	
 
		echo "<pre>";
		print_r($updatesRedeem );
 
	
	//$this->display('addToRedeemCart');
		 
}
 
public function redeemInfo() {
			
		if ($this->user) {
        $user_id = $this->user['id'];
		$user_email = $this->user['email'];
		
		}
		
 
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
 		 
		$this->assign('data', $data['response']);
		
 
		
		$this->CT->api = 'en.redeemInfo.getByUID';	
	 	$this->CT->id = $user_id;
		$redeemInfo = $this->CT->get();
		
 
		
		$my_cookie = $_COOKIE['REDEEM_CART'];
		
		//  echo 'redeem items= ';
		 // print_r($redeemInfo['response']['items']);
		//  echo '<br>';
		//  echo 'cookie= ';
		// print_r($my_cookie);
		 // echo '<br>';
		
		
 
		
		
		if (($redeemInfo['code'] == -1)){
			$current_redeem_for_user = 'NONE';
		}
		else if ($redeemInfo['response']['items'] == ''){
			$current_redeem_for_user = 'EMPTY';
		}
		else{
			$current_redeem_for_user = $redeemInfo['response']['items'];
		}
		
		
		switch ($current_redeem_for_user) {
			case 'NONE':
			$this->CT->api = 'en.redeemInfo.addById';
			$this->CT->setParams(array(
			'user_id' => $user_id,
			'items' => $my_cookie,
			'created' => time(),
			'updated' => time(),
			));
			$redeemInfoSave = $this->CT->post();
				break;
			
			case 'EMPTY':
			$this->CT->api = 'en.redeemInfo.save';
			$this->CT->setParams(array(
			'user_id' => $user_id,
			'items' => $my_cookie,
			'created' => time(),
			'updated' => time(),
			));
			$redeemInfoSave = $this->CT->post();	
				break;
				
				
			default:
			$this->CT->api = 'en.redeemInfo.save';
			$this->CT->setParams(array(
			'user_id' => $user_id,
			'items' => $my_cookie,
	 
			'updated' => time(),
			));
			$redeemInfoSave = $this->CT->post();	
				break;		
				
				
				
		}
		
		
	 // echo 'current_redeem_for_user='.$current_redeem_for_user;
     // echo "redeemInfoSave<pre>";
     // print_r($redeemInfoSave);
     // echo "</pre>";
 
		
	 
		$this->CT->api = 'en.redeemInfo.getByUID';	
	 	$this->CT->id = $user_id;
		$redeemInfo = $this->CT->get();
	 
 
		
 
 		 // echo "<br>redeemInfo read<pre>";
     	 // print_r($redeemInfo);
     	 // echo "</pre>";
 

		
 
$itemsRedeemCart  = $redeemInfo['response']['items'];
$piecesItems = explode("#", $itemsRedeemCart);

		//if cookie empty ->do not read database
if ($my_cookie ==''){
	$itemsRedeemCart = '';
	$piecesItems = '';
}
 
		 
		 		for ($j=1; $j < count($piecesItems); $j++) {
				$itemIds[] = $piecesItems[$j];
					 
				}
				
			//$itemIds = implode(',', $itemIds);
		 	// $ids ='goods_id in ('.$itemIds.')';
				
			 
		for ($i=0; $i <count($itemIds); $i++) { 

		$goods_id = $itemIds[$i];
		$this->CT->api = 'en.goods.get';	
	 	$this->CT->id = $goods_id;
		$urlList_tmp = $this->CT->get();
		$urlList[$i] = $urlList_tmp['response'];
		
		
		
		
		$this->CT->api = 'en.redeem.getPointsByID';	
	 	$this->CT->id = $goods_id;
		$pointsList_tmp = $this->CT->get();
		$pointsList[$i] = $pointsList_tmp['response'];

		}
				
	 	$this->assign('urlList', $urlList);	
		$this->assign('pointsList', $pointsList);
						
 
		
		
 		$this->assign('redeemInfo', $redeemInfo);
		$this->display('redeem-cart');
		 
}

// =============================== AVATAR =============================================   
   
      public function avatar() {
       
        if ($this->user) {
        $user_id = $this->user['id'];
				
		$user_email = $this->user['email'];

		$seo_ucenter_title['title'] = 'Avatar'; 
		$this->assign('seo_ucenter_title', $seo_ucenter_title['title']); 
		}			
			
		$this->CT->api = 'en.ucenter.getByUID';	
	 	$this->CT->id = $user_id;
		$data = $this->CT->get();
 		 
		$this->assign('data', $data['response']);
		
		
		
		if ( !$user_id){
			redirect("/login.html"); 
		}
		else {
		$current_avatar_image = $data['response']['avatar'];
	 
		if ($current_avatar_image == ''){
		
		$email = $user_email;
		$default = "identicon";
		$size = 147;
		//$grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size;
        $grav_url = "/images/ca/ucenter/avatar_by_default.jpg";  
		$current_avatar = '<img  class="avatar_image" src="'.$grav_url.'">';
		$avatar = 0;
					
		} else	
			{
			 $current_avatar = '<img class="avatar_image" src="/images/ca/ucenter/avatars/'.$current_avatar_image.'">';
			 $avatar = 1;	
			}

			
		$this->assign('current_avatar', $current_avatar);
		$this->assign('avatar', $avatar);
			
			
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    	$charactersLength = strlen($characters);
    	$randomString = '';
		
    	for ($i = 0; $i < 10; $i++) {
        	$randomString .= $characters[rand(0, $charactersLength - 1)];
    	}	
			
			
		 if ($_FILES['userfile']['name']){
		 		
		 	$uploaddir = '/images/ca/ucenter/avatars/';
			$uploaddir = '/home/atido/mall/htdocs/images/ca/ucenter/avatars/';
			$uploadfile = $uploaddir . $randomString.'_'.basename($_FILES['userfile']['name']);
 
			$file_name = $randomString.'_'.basename($_FILES['userfile']['name']);;

 
			if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
  				$status_upload= "File is valid, and was successfully uploaded.\n";
	
			//echo $user_id.'<br>';
			//echo 'avatar file name= '.$file_name;

            
			$this->CT->api = 'en.ucenter.save';
			$this->CT->setParams(array(
			'mode' => 'update',
			'id' => $user_id,
			'avatar' => $file_name,
			));
			$data = $this->CT->post();	
			 
			if ( $_POST['form_upload_avatar'] == 1){
 			redirect('/index.php?m=ucenter&c=index&a=settings'); 
 				}
	
	
					} else {
   							$status_upload=  "Upload failed";
					}

 
		 			}

	 	$this->assign('status_upload', $status_upload);
 		$this->display("avatar");
		
		}
		
      
    }

}