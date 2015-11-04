<?php

/**
 * 模块：en.seo
 * 功能: 国际站SEO模型类
 */
class module_en_seo {

    private $db; //数据库对象
    private $cache; //缓存对象
    private $key_prefix = "EN_OAT_PAGE_SEO_"; //缓存key前缀
    private $table_name = 'oat_page_seo'; //表名称

    public function __construct() {
        $this->db = db::getInstance('guojizhan');
        $this->cache = cache::getInstance("memcached");
    }

    /**
     * 获取一条seo记录
     * 
     * @param int $id seo记录id
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
     * 获取一条seo记录
     * 
     * @param string url地址
     * @return array
     */
    public function getByUrl($url) {
        return $this->db->fetch("select * from {$this->table_name} where url='" . $url . "'");
    }

    /**
     * 获取seo记录列表
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
     * 保存(新增/修改)seo数据
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
     * 删除seo数据
     *
     * @param int $id seo记录id
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
