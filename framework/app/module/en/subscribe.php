<?php

/**
 * 模块：en.subscribe
 * 功能: 国际站订阅模型类
 */
class module_en_subscribe {

    private $db; //数据库对象
    private $cache; //缓存对象
    private $key_prefix = "EN_OAT_SUBSCRIBE_"; //缓存key前缀
    private $table_name = 'oat_subscribe'; //表名称

    public function __construct() {
        $this->db = db::getInstance('guojizhan');
        $this->cache = cache::getInstance("memcached");
    }

    /**
     * 获取一条订阅记录
     * 
     * @param int $id 订阅id
     * @return array
     */
    public function get($id) {
        $id = intval($id);
        $key = $this->key_prefix . $id;
        $data = $this->cache->fetch($key);
        if (!$data) {
            $data = $this->db->fetch("select * from {$this->table_name} where id=$id");
            $this->cache->store($key, $data, array('life_time' => 3600));
        }
        return $data;
    }

    /**
     * 获取订阅列表
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
     * 保存(新增/修改)订阅数据
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
     * 删除订阅数据
     *
     * @param int $id 订阅id
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
