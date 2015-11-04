<?php

/**
 * 模块：en.goodsTag
 * 功能: 国际站商品模型类
 * 
 * @author 刘松森  <liusongsen@gmail.com>
 */
class module_en_goodsTag {

    private $db;
    private $cache;
    private $key_prefix = "EN_OAT_GOODS_TAG_";
    private $table_name = 'oat_goods_tag';

    public function __construct() {
        $this->db = db::getInstance("guojizhan");
        $this->cache = cache::getInstance("memcached");
    }

    /**
     * 获取一条信息
     * 
     * @param int $id 商品id
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

    
 
 

 
    

}

?>
