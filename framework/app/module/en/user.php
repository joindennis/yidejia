<?php

/**
 * 模块：en.user
 * 功能: 国际站用户模型类
 */
class module_en_user {

    private $db; //数据库对象
    private $cache; //缓存对象
    private $key_prefix = "EN_OAT_USER_"; //缓存key前缀
    private $table_name = 'oat_user'; //表名称

    public function __construct() {
        $this->db = db::getInstance('guojizhan');
        $this->cache = cache::getInstance("memcached");
    }



  /**
     * 获取一条皮肤分析记录
     * 
     * @param int $id 皮肤分析记录id
     * @return array
     */
    public function getByUID($uid) {
        $uid = intval($uid);
        $key = $this->key_prefix . $uid;
        $data = $this->cache->fetch($key);
        if (!$data) {
            $data = $this->db->fetch("select * from {$this->table_name} where id=$uid");
            $this->cache->store($key, $data, array('life_time' => 600));
        }
        return $data;
    }



    /**
     * 获取一条用户信息
     * 
     * @param int $id 用户ID
     * @return array
     */
    public function get($id) {
        $id = intval($id);
        $key = $this->key_prefix . $id;
        $data = $this->cache->fetch($key);
        if (!$data) {
            $data = $this->db->fetch("select * from {$this->table_name} where id=$id");
            $this->cache->store($key, $data, array('life_time' => 3600 * 24 * 10));
        }
        return $data;
    }

    /**
     * 获取一条用户信息
     * 
     * @param string $email 邮箱
     * @return array
     */
    public function getByEmail($eamil) {
        return $this->db->fetch("select * from {$this->table_name} where email='" . $eamil . "' and status='y'");
    }

    /**
     * 获取注册邮箱验证码
     * 
     * @param int $id 验证码ID
     * @return array
     */
    public function getVerifyCode($id) {
        $sql = "select * from oat_verify_code where id=" . (int) $id;
        return $this->db->fetch($sql);
    }

    /**
     * 保存注册邮箱验证码
     * 
     * @param string $email 邮箱
     * @param string $code  验证码
     * @return array
     */
    public function saveVerifyCode($email, $code, $promoCode) {
        $sql = "select count(*) as total,max(the_time) the_time from oat_verify_code where email='" . $email . "' and the_time >='" . date("Y-m-d") . "'";
        $data = $this->db->fetch($sql);
        //超过限制，同一个邮箱一天发送数量不能超过10次
        if ((int) $data['total'] > 10) {
            return -1;
        }
        //两次发送的间隔必须小于60s
        if ($data['the_time'] && (time() - strtotime($data['the_time']) < 60)) {
            return -2;
        }
		 // save promo code with user email
		$this->db->insert("oat_user_promocodes", array('email' => $email, 'promo' => $promoCode ));
		
		
        $this->db->insert("oat_verify_code", array('email' => $email, 'code' => $code, 'the_time' => date("Y-m-d H:i:s")));
      	return $this->db->getLastId();
	 
        
    }

    /**
     * 激活注册邮箱
     * 
     * @param string $email 邮箱
     * @return array
     */
    public function activeEmail($email, $code) {
        $sql = "select * from {$this->table_name} where email='" . $email . "' and status='n'";
        $data = $this->db->fetch($sql);
        if (!$data) {
            return false;
        }
        $key = $this->key_prefix . $data['id'];
        $this->cache->delete($key);
        $sql = "update  $this->table_name set status='y',active_time='" . date("Y-m-d H:i:s") . "',verify_code='" . $code . "' where email='" . $email . "' and status='n'";
        return $this->db->query($sql);
    }

    /**
     * 重设密码
     * 
     * @param string $email 邮箱
     * @param string $password 新密码
     * @return array
     */
    public function resetPwd($email, $password) {
        $sql = "select * from {$this->table_name} where email='" . $email . "' and status='y'";
        $data = $this->db->fetch($sql);
        if (!$data) {
            return false;
        }
        $key = $this->key_prefix . $data['id'];
        $this->cache->delete($key);
        $sql = "update  $this->table_name set password=md5('" . $password . "') where email='" . $email . "' and status='y'";
        return $this->db->query($sql);
    }

    /**
     * 获取用户列表
     * 
     * @param array|string $where 查询条件
     * @param array   $option  操作选项
     * @param string  $fields  查询字段
     * @return array
     */
    public function getList($where = null, $option = array(), $fields = "*") {
        $sql = $this->db->getSelectSql($this->table_name, $where, $fields, $option);
        return $this->db->fetchAll($sql);
    }

    /**
     * 保存(新增/修改)用户数据
     *
     * @param array $data 数据
     * @return array
     */
    public function save($data) {
        $sql = "select * from {$this->table_name} where email='" . $data['email'] . "' and status='n'";
        $email = $this->db->fetch($sql);
        $email && $data['id'] = $email['id'];
        if ($data['id']) {
            $key = $this->key_prefix . $data['id'];
            $this->cache->delete($key);
            return $this->db->update($this->table_name, "id", $data);
        } else {
            $data['id'] = 0;
            $this->db->insert($this->table_name, $data);
            return $this->db->getLastId();
        }
    }
	
	
	
	 /**
     * 保存(新增/修改)皮肤分析记录数据
     *
     * @param array $data 数据
     * @return array
     */
    public function updatePassword($data) {
    	if ($data['id']) {
    		$key = $this->key_prefix . $data['id'];
            $this->cache->delete($key);
			$sql = "update ".$this->table_name. " set password='" . md5($data['password']) . "' where id='" . $data['id'] . "' and status='y'";
			return $this->db->query($sql);
		}
		return false;
    }
	
	
	
	

    /**
     * 保存皮肤分析器的客户资料
     *
     * @param array $data 数据
     * @return array
     */
    public function savePffx($data) {
        //参数集合
        $in_array = array(
            'p_customer_id' => 0, #int(10), # 客户id(尽可能返回客户id，调用时填0)
            'p_improve_type' => $data['improve_type'], #最想改善的肌肤问题
            'p_skin_type' => $data['skin_type'], # 肤质('','Neutral Skin','Sensitive Skin','Oily Skin','Dry Skin','Combination Skin')
            'p_want_type' => $data['want_type'], # 需求类型（多选 Blackhead.,Acne.,Freckle.,Dark circle.,Under-eye puffiness.,Wrinkles.,Fat
            'p_buy_channel' => $data['buy_channel'], #购买渠道
            'p_used_brand' => $data['used_brand'], # 曾用品牌
            'p_comments' => $data['comments'], #备注
            'p_customer_name' => $data['customer_name'], #varchar(30), # 姓名()
            'p_gender' => $data['gender'], #varchar(20), # 性别
            'p_handset' => (int) $data['handset'], #varchar(20), # 手机号(1开头的11位全数字串)
            'p_handset_yz' => $data['handset_yz'], #是否通过手机验证(y,n)不能为空
            'p_facebook' => (int) $data['facebook'], #varchar(40), # facebook个人主页
            'p_emall' => $data['emall'], #varchar(20), # 推介人类型及id(格式：a17 b3167，录入时有效)
            'p_cpser_type_id' => $data['cpsid'], #varchar(20), # 推介人类型及id(格式：a17 b3167，录入时有效)
            'p_guid' => (int) $data['guid'], #int(10) unsigned, guid()
            'p_ip' => $data['ip'], #varchar(20),ip地址
            'p_source' => $data['source'], #varchar(10),来源(网站，移动)
            'p_device_type' => $data['device_type'], #varchar(200),设备类型()
            'p_browser' => $data['browser'], # 浏览器
            'p_cps_facebook' => '', #varchar(30), # cps对应的facebook账号
            'p_cps_facebook_link' => '', #varchar(100),cps对应的facebook链接
            'p_password' => '', #varchar(20),   # 密码,验证位为y时,且成功注册后,返回随机密码
            'p_result' => ''                             #varchar(255)      # 返回信息(成功返回"success"+提示，失败返回原因)
        );
        //输出参数
        $out_array = array(
            'p_password', 'p_result', 'p_customer_id', 'p_cps_facebook', 'p_cps_facebook_link'
        );
        core::log($in_array);
        //执行储存过程
        $result = $this->db->exec_proc("oap_mall_pffx", $in_array, $out_array);
        core::log($result);
        return $result;
    }

    /**
     * 删除用户信息
     *
     * @param int $id 用户id
     * @return int
     */
    public function delete($id) {
        $key = $this->key_prefix . $id;
        $this->cache->delete($key);
        $sql = "delete from " . $this->table_name . " where id=" . $id;
        return $this->db->query($sql);
    }
	
	
	
	

	
	
	

}

?>
