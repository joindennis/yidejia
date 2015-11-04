<?php

/**
 * 模块：en.order
 * 功能: 国际站订单模型类
 */
class module_en_order {

    private $db; //数据库对象
    private $cache; //缓存对象
    private $key_prefix = "EN_OAT_ORDER_"; //缓存key前缀
    private $table_name = 'oat_order'; //表名称

    public function __construct() {
        $this->db = db::getInstance('guojizhan');
        $this->cache = cache::getInstance("memcached");
    }

    /**
     * 获取一条订单信息
     * 
     * @param int $id 订单ID
     * @return array
     */
    public function get($id) {
        $id = intval($id);
        $key = $this->key_prefix . $id;
        //$data = $this->cache->fetch($key);
        if (!$data) {
            $data = $this->db->fetch("select * from {$this->table_name} where id=$id");
            //获取明细
            $sql = "select a.*,b.url_name,b.goods_name,b.type,b.color,b.size,b.spec from oat_order_lines a,oat_goods b where a.goods_id =b.goods_id and a.order_id=" . $id;
            $data['lines'] = $this->db->fetchAll($sql);
            //获取收件人地址
            $sql = "select * from oat_user_recipient where id=" . (int) $data['recipient_id'];
            $data['recipient'] = $this->db->fetch($sql);
            //获取电子账单地址
            $sql = "select * from oat_user_billing where id=" . (int) $data['billing_id'];
            $data['billing'] = $this->db->fetch($sql);
            $this->cache->store($key, $data, array('life_time' => 10));
        }
        return $data;
    }

    /**
     * 获取一条订单信息
     * 
     * @param string $code 订单编码
     * @return array
     */
    public function getByCode($code) {
        $sql = "select * from oat_order where order_code='" . $code . "'";
        $data = $this->db->fetch($sql);
        if (!$data) {
            return false;
        }
        //获取明细数据
        $sql = "select a.*,b.url_name,b.goods_name,b.type,b.color,b.size,b.spec from oat_order_lines a,oat_goods b where a.goods_id =b.goods_id and a.order_id=" . $data['id'];
        $data['lines'] = $this->db->fetchAll($sql);
        return $data;
    }

    /**
     * 获取订单列表
     * 
     * @param array|string $where 查询条件
     * @param array   $option  操作选项
     * @param string  $fields  查询字段
     * @return array
     */
    public function getList($where = null, $option = array(), $fields = "*") {
        $sql = $this->db->getSelectSql($this->table_name, $where, $fields, $option);
        $data = $this->db->fetchAll($sql);
        if (!$data) {
            return false;
        }
        //获取明细数据
        foreach ($data as $k => $v) {
            $sql = "select a.*,b.url_name,b.goods_name,b.type,b.color,b.size,b.spec from oat_order_lines a,oat_goods b where a.goods_id =b.goods_id and a.order_id=" . $v['id'];
            $v['lines'] = $this->db->fetchAll($sql);
            $data[$k] = $v;
        }
        return $data;
    }

    /**
     * 保存(新增/修改)订单数据
     *
     * @param array $data 数据
     * @return array
     */
    public function save($data) {
        $lines = array();
        $total = 0;
        if ($data['goods_qty'] == "") {
            return array('msg' => "Shopping cart cannot be empty", 'order_code' => '');
			                       
        }
        //计算订单价格
        $goods = explode(";", $data['goods_qty']);
        foreach ($goods as $v) {
            list($goods_id, $quantity) = explode(",", $v);
            $sql = "select sale_price from oat_goods where goods_id=" . $goods_id;
            $tempGood = $this->db->fetch($sql);
            if (!$tempGood) {
                continue;
            }
            $lines[] = array(
                'goods_id' => $goods_id,
                'quantity' => (int) $quantity,
                'price' => $tempGood['sale_price'],
                'the_date' => $data['the_date']
            );
            $total+=$tempGood['sale_price'] * (int) $quantity;
        }
		
	
            $percentage = $_POST['promo'];
            		$percentage10 =$percentage/100; 
					
					$amount_before = $total;
					
					$ammount_percentage =$percentage10*$amount_before;
					$new_sum = $amount_before-$ammount_percentage; 
					$new_sum=  round($new_sum, 2);
					
					
					
					
					
					
					
					
					
		
        $data['the_amount'] =$new_sum;
        //生成order_code
        $key = "beautural_incr";
        $this->cache->increment($key);
        $incre = $this->cache->fetch($key);
        $data['order_code'] = "d" . date("Ymd") . rand(10, 99) . (int) $incre;
        $data['id'] = 0;
        //保存订单数据
        $this->db->insert($this->table_name, $data);
        $order_id = $this->db->getLastId();
        if ($order_id == 0) {
            return array('msg' => "订单提交失败", 'order_code' => '');
        }
        //保存订单明细
        foreach ($lines as $v) {
            $this->db->insert("oat_order_lines", array(
                'order_id' => $order_id,
                'goods_id' => $v['goods_id'],
                'quantity' => $v['quantity'],
                'price' => $v['price'],
                'the_date' => $data['the_date']
            ));
        }
        return array('msg' => "success订单保存成功", 'order_code' => $data['order_code'], 'the_amount' => $total);
    }

    /**
     * 修改订单支付状态
     *
     * @param string $code 订单号码
     * @param string $token payPai支付token
     * @param string $payerid payPai支付payerId
     * @param string $transaction_id payPai支付交易号
     * @param string $transaction_type payPai支付交易类型
     * @param string $flag 支付成功标示[y:支付成功n:支付失败] 默认n
     * @return bool
     */
    public function pay($code, $token, $payerid, $transaction_id, $transaction_type, $flag = 'n') {
        //验证该订单支付状态
        $data = $this->getByCode($code);
        if (!$data || $data['status'] == "已付款") {
            return false;
        }
        //同步清缓存
        $key = $this->key_prefix . $data['id'];
        $this->cache->delete($key);
        if ($flag == 'y') {
            $sql = "update $this->table_name set status='已付款',token='" . $token . "',payerid='" . $payerid . "',pay_date='" . date("Y-m-d H:i:s") . "',transaction_id='" . $transaction_id . "',transaction_type='" . $transaction_type . "' where order_code='" . $code . "'";
        } else {
            $sql = "update $this->table_name set token='" . $token . "',payerid='" . $payerid . "',transaction_id='" . $transaction_id . "',transaction_type='" . $transaction_type . "' where order_code='" . $code . "'";
        }
        return $this->db->query($sql);
    }

    /**
     * 删除订单信息
     *
     * @param int $id 订单id
     * @return int
     */
    public function delete($id) {
        $key = $this->key_prefix . $id;
        $this->cache->delete($key);
        $sql = "delete from " . $this->table_name . " where id=" . $id;
        $result = $this->db->query($sql);
        if (!$result) {
            return false;
        }
        //同步删除明细数据
        $sql = "delete from oat_order_lines where order_id=" . $id;
        $this->db->query($sql);
        return $result;
    }

    /**
     * 检查用户是否购买过某单品
     *
     * @param int $user_id 用户ID
     * @param int $goods_id 产品ID
     * @return int
     */
    public function checkComments($user_id, $goods_id) {
        $sql = "select id from oat_order_lines where user_id=" . $user_id . " and goods_id=" . $goods_id . " and comments_flag='n' order by id asc";
        return $this->db->fetch($sql);
    }

    /**
     * 计算购物车商品总金额
     *
     * @param string $goods_qty
     * @return int
     */
    public function getAmount($goods_qty) {
        $total = 0;
        $goods = explode(";", $goods_qty);
        foreach ($goods as $v) {
            list($goods_id, $quantity) = explode(",", $v);
            $sql = "select sale_price from oat_goods where goods_id=" . $goods_id;
            $tempGood = $this->db->fetch($sql);
            if (!$tempGood) {
                continue;
            }
            $total+=$tempGood['sale_price'] * (int) $quantity;
        }
        return $total;
    }

}

?>
