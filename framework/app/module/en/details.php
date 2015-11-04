<?php

/**
 * 模块：en.details
 * 功能: 国际站栏目明细模型类
 */
class module_en_details {

    private $db; //数据库对象
    private $cache; //缓存对象
    private $key_prefix = "EN_OAT_COLUMNS_DETAILS_"; //缓存key前缀
    private $table_name = 'oat_columns_details'; //表名称

    public function __construct() {
        $this->db = db::getInstance('guojizhan');
        $this->cache = cache::getInstance("memcached");
    }

    /**
     * 获取一条明细记录
     * 
     * @param int $id 明细id
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
     * 获取明细列表
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
        //关联产品价格
        foreach ($data as $k => $v) {
            $sql = "select * from oat_goods where goods_id=" . $v['goods_id'];
            $good = $this->db->fetch($sql);
            //获取类型名称
            $sql = "select name from oat_category where id=" . $good['type'];
            $cate = $this->db->fetch($sql);
            $data[$k]['types'] = $cate;
            $data[$k]['type'] = $good['type'];
            $data[$k]['price'] = $good['sale_price'];
            $data[$k]['goods_name'] = $good['goods_name'];
            $data[$k]['url_name'] = $good['url_name'];
        }
        return $data;
    }

    /**
     * 保存(新增/修改)明细数据
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
     * 删除明细数据
     *
     * @param int $id 明细id
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
