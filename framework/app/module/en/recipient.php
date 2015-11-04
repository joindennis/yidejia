<?php

/**
 * 模块：en.recipient
 * 功能: 国际站收件人模型类
 */
class module_en_recipient {

    private $db; //数据库对象
    private $cache; //缓存对象
    private $key_prefix = "EN_OAT_USER_RECIPIENT_"; //缓存key前缀
    private $table_name = 'oat_user_recipient'; //表名称

    public function __construct() {
        $this->db = db::getInstance('guojizhan');
        $this->cache = cache::getInstance("memcached");
    }

    /**
     * 获取一条收件人信息
     * 
     * @param int $id 收件人ID
     * @return array
     */
    public function get($id) {
        $id = intval($id);
        $key = $this->key_prefix . $id;
        $data = $this->cache->fetch($key);
        if (!$data) {
            $data = $this->db->fetch("select * from {$this->table_name} where id=$id and is_delete = 1");
            $this->cache->store($key, $data, array('life_time' => 3600));
        }
        return $data;
    }

    /**
     * 获取一条收件人信息
     * 
     * @param int $id 电子账单ID
     * @return array
     */
    public function getByBillingId($id) {
        return $this->db->fetch("select * from {$this->table_name} where billing_id=$id and is_delete = 1");
    }

    /**
     * 获取收件人列表
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
     * 保存(新增/修改)收件人数据
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
            //判断是否存在相同地址
            $sql = "select * from {$this->table_name} where ship_to_name='" . $data['ship_to_name'] . "' and ship_to_street='" . $data['ship_to_street'] . "' and user_id=" . $data['user_id'];
            $exists = $this->db->fetch($sql);
            if ($exists) {
                return (int) $exists['id'];
            } else {
                $data['id'] = 0;
                $this->db->insert($this->table_name, $data);
                return $this->db->getLastId();
            }
        }
    }

    /**
     * 删除收件人信息
     *
     * @param int $id 收件人id
     * @return int
     */
    public function delete($id) {
        $key = $this->key_prefix . $id;
        $this->cache->delete($key);
        $sql = "update " . $this->table_name . " set is_delete=-1 where id = " . $id;
        return $this->db->query($sql);
    }

}

?>
