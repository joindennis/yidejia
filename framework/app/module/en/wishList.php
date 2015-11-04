<?php

/**
 * 模块：en.wishlist
 * 功能: 皮肤分析器模型类
 */
class module_en_wishList {

    private $db; //数据库对象
    private $cache; //缓存对象
    private $key_prefix = "EN_OAT_USER_WISHLIST_"; //缓存key前缀
    private $table_name = 'oat_user_wishlist'; //表名称
    

    public function __construct() {
        $this->db = db::getInstance('guojizhan');
        $this->cache = cache::getInstance("memcached");
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
		// test
		$this->cache->delete($key);
		// test end
        $data = $this->cache->fetch($key);
        if (!$data) {
            $data = $this->db->fetch("select * from {$this->table_name} where goods_id=$id");
            $this->cache->store($key, $data, array('life_time' => 3600 * 24 * 10));
        }
        return $data;
    }
	
	
  

    /**
     * 获取皮肤分析记录列表
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
     * 保存(新增/修改)皮肤分析记录数据
     *
     * @param array $data 数据
     * @return array
     */
    public function save($data) {
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
     * 删除皮肤分析记录
     *
     * @param int $id 皮肤分析记录id
     * @return int
     */
    public function delete($id) {
        $key = $this->key_prefix . $id;
        $this->cache->delete($key);
        $sql = "delete from " . $this->table_name . " where id=" . $id;
        return $this->db->query($sql);
    }
    
    
    /**
     * Clear cache data by id
     *
     * @param int $id wishlist id
     * @return null
     */
    public function clearCacheById($id) {
    	$key = $this->key_prefix . $id;
        $this->cache->delete($key);
    }

}

?>

