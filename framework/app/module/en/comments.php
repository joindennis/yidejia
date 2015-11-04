<?php

/**
 * 模块：en.comments
 * 功能: 国际站评论模型类
 */
class module_en_comments {

    private $db; //数据库对象
    private $cache; //缓存对象
    private $key_prefix = "EN_OAT_COMMENTS_"; //缓存key前缀
    private $table_name = 'oat_comments'; //表名称

    public function __construct() {
        $this->db = db::getInstance('guojizhan');
        $this->cache = cache::getInstance("memcached");
    }

    /**
     * 获取一条商品评论信息
     * 
     * @param int $id 评论ID
     * @return array
     */
    public function get($id) {
        $key = $this->key_prefix . $id;
        $data = $this->cache->fetch($key);
        if (!$data) {
            $data = $this->db->fetch("select * from {$this->table_name} where id=$id");
            $this->cache->store($key, $data, array('life_time' => 3600));
        }
        return $data;
    }
	
	

	
	
	

    /**
     * 获取评论列表
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
     * 保存(新增/修改)评论数据
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
            $data['id']=0;
            $this->db->insert($this->table_name, $data);
            return $this->db->getLastId();
        }
    }

    /**
     * 删除评论数据
     *
     * @param int $id 品论id
     * @return int
     */
    public function delete($id) {
        $key = $this->key_prefix . $id;
        $this->cache->delete($key);
        $sql = "delete from " . $this->table_name . " where id=" . $id;
        return $this->db->query($sql);
    }




  /**
     * 增加人气
     * 
     * @param int id 商品ID
     * @return bool
     */
    public function thumbsUp($id) {
        $key = $this->key_prefix . $id;
        $this->cache->delete($key);
 		return $this->db->query("update $this->table_name set thumbs_up = thumbs_up + 1 where id={$id}");
		
		
		
    }



  /**
     * 增加人气
     * 
     * @param int id 商品ID
     * @return bool
     */
    public function thumbsDown($id) {
        $key = $this->key_prefix . $id;
        $this->cache->delete($key);
        return $this->db->query("update $this->table_name set thumbs_down = thumbs_down - 1 where id={$id}");
    }




}

?>
