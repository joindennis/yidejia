<?php

/**
 * 模块: end.info
 * 功能: 国际站商品详情模型类
 */
class module_en_info {

    private $db; //数据库对象
    private $cache; //缓存对象
    private $key_prefix = "EN_OAT_GOODS_INFO_"; //缓存key前缀
    private $table_name = 'oat_goods_info'; //表名称

    public function __construct() {
        $this->db = db::getInstance('guojizhan');
        $this->cache = cache::getInstance("memcached");
    }

    /**
     * 获取一条商品描述记录
     * 
     * @param int $id 商品id
     * @return array
     */
    public function get($id) {
        $id = intval($id);
        $key = $this->key_prefix . $id;
       // $data = $this->cache->fetch($key);
          $this->cache->delete($key);
        if (!$data) {
            $data = $this->db->fetch("select * from {$this->table_name} where goods_id=$id");
            $this->cache->store($key, $data, array('life_time' => 3600 * 24 * 10));
        }
        return $data;
    }

    /**
     * 获取商品描述列表
     * 
     * @param array|string $where 查询条件
     * @param array   $option  操作选项
     * @param string  $fields  查询字段
     * @return array
     */
    public function getList($where = null, $option = array(), $fields = "*") {
        $sql = $this->db->getSelectSql($this->table_name, $where, $fields, $option);
        $data = $this->db->fetchAll($sql);
        return $data;
    }

    /**
     * 保存(新增/修改)商品描述信息
     *
     * @param array $data 数据
     * @return array
     */
    public function save($data) {
        $goodInfo = $this->get($data['goods_id']);
        if ($goodInfo) {
            $key = $this->key_prefix . $data['goods_id'];
            $this->cache->delete($key);
            return $this->db->update($this->table_name, "goods_id", $data);
        } else {
            $this->db->insert($this->table_name, $data);
            return $this->db->getLastId();
        }
    }

    /**
     * 删除商品描述信息
     *
     * @param int $id 商品id
     * @return int
     */
    public function delete($id) {
        $key = $this->key_prefix . $id;
        $this->cache->delete($key);
        $sql = "delete from " . $this->table_name . " where goods_id=" . $id;
        return $this->db->query($sql);
    }

}

?>
