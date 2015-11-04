<?php

/**
 * 模块：en.facebook
 * 功能: Facebook 模型类
 */
class module_en_facebook {

    private $db; //数据库对象
    private $cache; //缓存对象
    private $key_prefix = "EN_FACEBOOK_"; //缓存key前缀
    private $table_name = 'facebook_user_data'; //表名称
    private $table_freeSample = "facebook_free_sample";

    public function __construct() {
        $this->db = db::getInstance('guojizhan');
        $this->cache = cache::getInstance("memcached");
    }
    
    /**
     * Get Facebook data by id
     */
    public function getById($id) {
        $key = $this->key_prefix . $id;
        $data = $this->cache->fetch($key);
        if (!$data) {
            $data = $this->db->fetch("select * from {$this->table_name} where id=$id");
            $this->cache->store($key, $data, array('life_time' => 600));
        }
        return $data;
    }

    /**
     * Get Facebook data by email
     */
    public function getByEmail($email) {
        $key = $this->key_prefix . $email;
        $data = $this->cache->fetch($key);
        if (!$data) {
            $data = $this->db->fetch("select * from {$this->table_name} where Email='$email'");
            $this->cache->store($key, $data, array('life_time' => 600));
        }
        return $data;
    }


    /**
     * 获取Facebook数据列表
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
     * 保存(新增/修改)Facebook数据
     *
     * @param array $data 数据
     * @return array
     */
    public function save($data) {
        $result = $this->getByEmail($data['Email']);
        if ($result) {
            $doUpdate = false;
            foreach($data as $key => $value) {
                if ($result[$key] != $value) {
                    $doUpdate = true;
                    break;
                }
            }
            if ($doUpdate) {
                $data['id'] = $result['id'];
                $key = $this->key_prefix . $data['Email'];
                $this->cache->delete($key);
                return array('result' => $this->db->update($this->table_name, "id", $data), 'id' => $result['id']);
            } else {
                return $result;
            }
        } else {
            $data['id'] = 0;
            $data['time'] = time();
            $result = $this->db->fetch("select * from oat_user where email='{$data['Email']}'");
            if ($result) {
            	$data['user_id'] = $result['id'];
            } else {
            	$userModule = new module_en_user();
                $result = $userModule->save(array('email' => $data['Email'], 'password' => "other", 'status' => 'y', 'the_time' => date("Y-m-d H:i:s")));
                $data['user_id'] = $result;
            }
            $this->db->insert($this->table_name, $data);
            return $this->db->getLastId();
        }
    }

    /**
     * 删除Facebook记录
     *
     * @param int $id: Facebook id
     * @return int
     */
    public function delete($id) {
        $key = $this->key_prefix . $id;
        $this->cache->delete($key);
        $sql = "delete from " . $this->table_name . " where id=" . $id;
        return $this->db->query($sql);
    }
    
    /**
     * Save Facebook fan page info
     */
    public function freeSample($data) {
        $info = array('First_name'=>$data['First_name'],
                      'Last_name'=>$data['Last_name'],
                      'Email'=>$data['Email']
                );
        if ($data['fb_id']) $info['fb_id'] = $data['fb_id'];
        if ($data['gender']) $info['gender'] = $data['gender'];
        if ($data['locale']) $info['locale'] = $data['locale'];
        if ($data['timezone']) $info['timezone'] = $data['timezone'];
        $result = $this->save($info);
        
        if (is_array($result)) {
            $id = $result['id'];
        } else {
            $id = $result;
        }
        
        $res = $this->db->fetch("select * from {$this->table_freeSample} where id=$id");
        $info = array('id'=>$id,
                      'shared'=>$data['shared'],
                      'liked'=>$data['liked'],
                      'Address1'=>$data['Address1'],
                      'City'=>$data['City'],
                      'State'=>$data['State'],
                      'Country'=>$data['Country'],
                      'Zip'=>$data['Zip'],
                      'time'=>time()
                );
        if ($data['Address2']) $info['Address2'] = $data['Address2'];
        if (!$res) {
            $result2 = $this->db->insert($this->table_freeSample, $info);
        } else {
            $result2 = $this->db->update($this->table_freeSample, "id", $info);
        }
        
        return array($result, $result2);
    }
    
    /**
     * Get Facebook fan page info
     */
    public function getFreeSampleInfo() {
    	$result = $this->getList(null, array('order' => 'id desc'));
    	if (is_null($result)) return null;
    	$sql = $this->db->getSelectSql($this->table_freeSample, null, "*", array('order' => 'id desc'));
    	$data = $this->db->fetchAll($sql);
    	if (is_null($data)) return null;
    	
    	$newResult = array();
    	$newData = array();
    	foreach($result as $key => $value) {
    	    $id = array_shift($value);
    	    $newResult[$id] = $value;
    	}
    	foreach($data as $key => $value) {
    	    $id = array_shift($value);
    	    $newData[$id] = $value;
    	}
    	
    	foreach($newResult as $key => $value) {
    		if (isset($newData[$key])) {
    			$newResult[$key]["info"] = $newData[$key];
    		}
    		else {
    			unset($newResult[$key]);
    		}
    	}
    	
    	return $newResult;
    }

}

?>
