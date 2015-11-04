<?php

/**
 * 模块：en.blog
 * 功能: 国际站博客模型类
 */
class module_en_blog {

    private $db; //数据库对象
    private $cache; //缓存对象
    private $key_prefix = "EN_OAT_BLOG_"; //缓存key前缀
    private $table_name = 'oat_blog'; //表名称
    private $table_categories = 'oat_blog_categories';
    private $table_comment = 'oat_blog_comments';

    public function __construct() {
        $this->db = db::getInstance('guojizhan');
        $this->cache = cache::getInstance("memcached");
    }

    /**
     * 获取一条blog记录
     * 
     * @param int $id blog id
     * @return array
     */
    public function get($id) {
        $id = intval($id);
        $key = $this->key_prefix . $id;
        $article = $this->cache->fetch($key);
        if (!$article) {
            $article = $this->db->fetch("select * from {$this->table_name} where id=$id");
            
            // Set category
            $categories = $this->getCategories();
        	if (!is_null($article['category'])) {
        		$id = $article['category'];
        		if (array_key_exists($id, $categories)) {
        			$article['category_id'] = $id;
        			$article['category'] = $categories[$article['category']]['category'];
        		}
        		else {
        			$article['category_id'] = 0;
        			$article['category'] = "Unknown category";
        		}
        	}
        	// Set tags
        	if (!is_null($article['tags'])) {
        		$article['tags'] = explode(',', trim($article['tags'], ','));
        	}
        	// Set author
        	if ($article['uid']) {
        		$names = $this->getUsersInfo();
        		if ($names[$article['uid']]) {
        			$name = $names[$article['uid']];
        			$name = $name['first_name'] . ' ' . $name['last_name'];
        			$name = ucwords(strtolower($name));
        		} else {
        			$user = $this->db->fetch("Select email From oat_user Where id={$article['uid']}");
        			$name = $user['email'];
        		}
        	} else {
        		$name = $article['author'];
        		$name = ucwords(strtolower($name));
        	}
        	$article['author'] = $name;
        	
            $this->cache->store($key, $article, array('life_time' => 600));
        }
        return $article;
    }
    
    /**
     * Get all blog categories
     * 
     * @return array
     */
    public function getCategories() {
    	$key = $this->key_prefix . "categories";
    	$categories = $this->cache->fetch($key);
    	if (!$categories) {
    		$sql = $this->db->getSelectSql($this->table_categories);
    		$categories = $this->db->fetchAll($sql);
    		$newCategories = array();
	        foreach($categories as $category) {
	        	$id = array_shift($category);
	        	$newCategories[$id] = $category;
	        }
	        $categories = $newCategories;
	        
    		$this->cache->store($key, $categories, array('life_time' => 600));
    	}
    	return $categories;
    }
    
    /**
     * 获取blog近期发布文章列表
     * 
     * @return array
     */
    public function getRecentPost($num) {
    	$key = $this->key_prefix . "recentPost";
    	$articles = $this->cache->fetch($key);
    	if (!$articles) {
    		$sql = $this->db->getSelectSql($this->table_name, null, "id,the_time,title", array('order' => 'the_time DESC, id DESC', 'limit' => $num));
        	$articles = $this->db->fetchAll($sql);
        	$this->cache->store($key, $articles, array('life_time' => 600));
        }
        return $articles;
    }
    
    /**
     * 获取blog热门文章列表
     * 
     * @return array
     */
    public function getHotPost($num) {
    	$key = $this->key_prefix . "hotPost";
    	$articles = $this->cache->fetch($key);
    	if (!$articles) {
	    	$sql = $this->db->getSelectSql($this->table_name, null, "id,the_time,title", array('order' => 'viewed DESC, the_time DESC, id DESC', 'limit' => $num));
	        $articles = $this->db->fetchAll($sql);
	        $this->cache->store($key, $articles, array('life_time' => 180));
	    }
        return $articles;
    }
    
    /**
     * 获取blog相关文章列表
     * 
     * @return array
     */
    public function getRelatedPost($num, $id, $tags) {
    	$key = $this->key_prefix . "RelatedPost_" . $id;
    	$articles = $this->cache->fetch($key);
    	if (!$articles) {
    		$where = "id<>$id AND (false";
    		foreach($tags as $tag) {
    			$where .= " OR tags LIKE '%,$tag,%'";
    		}
    		$where .= ")";
	    	$sql = $this->db->getSelectSql($this->table_name, $where, "id,the_time,title", array('order' => 'viewed DESC, the_time DESC, id DESC', 'limit' => $num));
	        $articles = $this->db->fetchAll($sql);
	        $this->cache->store($key, $articles, array('life_time' => 180));
	    }
        return $articles;
    }

    /**
     * 获取blog列表
     * 
     * @param array|string $where 查询条件
     * @param array   $option  操作选项
     * @param string  $fields  查询字段
     * @return array
     */
    public function getList($where = null, $option = array(), $fields = "*") {
        $sql = $this->db->getSelectSql($this->table_name, $where, $fields, $option);
        $articles = $this->db->fetchAll($sql);
        
        $categories = $this->getCategories();
        $names = $this->getUsersInfo();
        
        foreach($articles as $key => $article) {
        	// Set category
        	if (!is_null($article['category'])) {
        		$id = $article['category'];
        		if (array_key_exists($id, $categories)) {
        			$articles[$key]['category_id'] = $id;
        			$articles[$key]['category'] = $categories[$article['category']]['category'];
        		}
        		else {
        			$articles[$key]['category_id'] = 0;
        			$articles[$key]['category'] = "Unknown category";
        		}
        	}
        	// Set tags
        	if (!is_null($article['tags'])) {
        		$articles[$key]['tags'] = explode(',', trim($article['tags'], ','));
        	}
        	// Set author
        	if ($article['uid']) {
        		if ($names[$article['uid']]) {
        			$name = $names[$article['uid']];
        			$name = $name['first_name'] . ' ' . $name['last_name'];
        			$name = ucwords(strtolower($name));
        		} else {
        			$user = $this->db->fetch("Select email From oat_user Where id={$article['uid']}");
        			$name = $user['email'];
        		}
        	} else {
        		$name = $article['author'];
        		$name = ucwords(strtolower($name));
        	}
        	$articles[$key]['author'] = $name;
        	// Set number of comments
        	$articles[$key]['comment_num'] = $this->getCommentsCount($article['id']);
        }
        
        return $articles;
    }
    
    /**
     * 获取blog列表（加入标签查询）
     * 
     * @param array         $tags    标签
     * @param array|string  $where   查询条件
     * @param array         $option  操作选项
     * @param string        $fields  查询字段
     * @return array
     */
    function getListByTags($tags = null, $where = null, $option = array(), $fields = "*") {
    	if ($where && $tags && !empty($tags)) {
    		foreach($tags as $tag) {
    			$where .= " AND tags LIKE '%,$tag,%'";
    		}
    	}
    	return getList($where, $option, $fields);
    }
    
    /**
     * Get blog total number
     * 
     * uid: user id
     * category: int
     * tags: str
     * 
     * return int
     */
    public function blog_count($uid, $category, $tags) {
    	$where = " Where true";
    	if (isset($uid)) {
    		$where .= " AND uid=$uid";
    	}
    	if (isset($category)) {
    		$where .= " AND category=$category";
    	}
    	if (isset($tags)) {
    		foreach($tags as $tag) {
    			$where .= " AND tags LIKE '%,$tag,%'";
    		}
    	}
    	$result = $this->db->fetch("Select COUNT(*) AS blog_num From {$this->table_name}" . $where);
    	return $result;
    }
    
    /**
     * Get blog comments
     * 
     * bid: blog id
     *
     * return array(comments, number of comments)
     */
    public function getComments($bid) {
    	$key = $this->key_prefix . "comments_" . $bid;
    	$result = $this->cache->fetch($key);
    	if ($result) {
    		return $result;
    	}
    	
    	$result = $this->db->fetchAll("Select * From {$this->table_comment} Where blog_id=$bid Order By id ASC");
    	
    	// Set replies and user name
    	$names = $this->getUsersInfo();
    	$comments = array();
    	$count = 0;
    	foreach($result as $comment) {
    		$count++;
    		
    		// Set user info
    		$info = $names[$comment['user_id']];
    		if ($info) {
    			// Set user name
        		$name = $info['first_name'] . ' ' . $info['last_name'];
    			$name = ucwords(strtolower($name));
    			
    			// Set user portrait
    			$portrait = $info['avatar'];
				if ($portrait == ''){
					$email = $info['email'];
					$default = "identicon";
					$size = 147;
					$portrait = "http://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?d=" . urlencode($default) . "&s=" . $size;
				} else {
					$portrait = '/images/ca/ucenter/avatars/' . $portrait;
				}
    		} else {
    			// Set user name
    			$user = $this->db->fetch("Select email From oat_user Where id={$comment['user_id']}");
    			$name = $user['email'];
    			
    			// Set user default portrait
    			$portrait = '/images/ca/ucenter/no_image_available.jpg';
    		}
    		$comment['user'] = $name;
    		$comment['portrait'] = $portrait;
    		
    		// Set replies
    		if ($comment['comment_id']) {
    			$id = $comment['comment_id'];
    			array_push($comments[$id]['replies'], $comment);
    		}
    		else {
    			$id = array_shift($comment);
    			$comments[$id] = $comment;
    			$comments[$id]['replies'] = array();
    		}
    	}
    	$result = array('comments' => $comments, 'count' => $count);
    	$this->cache->store($key, $result, array('life_time' => 300));
    	return $result;
    }
    
    /**
     * Get blog comments count
     * 
     * bid: blog id
     */
    public function getCommentsCount($bid) {
    	$key = $this->key_prefix . "comments_count_" . $bid;
    	$result = $this->cache->fetch($key);
    	if (!$result) {
    		$result = $this->db->fetchAll("Select * From {$this->table_comment} Where blog_id=$bid");
    		$result = count($result);
    		$this->cache->store($key, $result, array('life_time' => 300));
    	}
    	return $result;
    }
    
    /**
     * Add/Update blog comments
     * 
     * return array/int
     */
    public function saveComment($data) {
    	$key = $this->key_prefix . "comments_" . $data['blog_id'];
    	$this->cache->delete($key);
    	if ($data['id']) {
            return $this->db->update($this->table_comment, "id", $data);
        } else {
        	$key = $this->key_prefix . "comments_count_" . $data['blog_id'];
    		$this->cache->delete($key);
            $data['id'] = 0;
            $this->db->insert($this->table_comment, $data);
            return $this->db->getLastId();
        }
    }

    /**
     * 保存(新增/修改)blog数据
     *
     * @param array $data 数据
     * @return array
     */
    public function save($data) {
    	if ($data['tags']) {
    		$data['tags'] = strtoupper(',' . implode(',', $data['tags']). ',');
    	}
        if ($data['id']) {
            $key = $this->key_prefix . $data['id'];
            $this->cache->delete($key);
            return $this->db->update($this->table_name, "id", $data);
        } else {
        	$key = $this->key_prefix . "recentPost";
        	$this->cache->delete($key);
            $data['id'] = 0;
            $this->db->insert($this->table_name, $data);
            return $this->db->getLastId();
        }
    }

    /**
     * 删除blog数据
     *
     * @param int $id blog id
     * @return array of int
     */
    public function delete($id) {
        $key = $this->key_prefix . $id;
        $this->cache->delete($key);
        $key = $this->key_prefix . "RelatedPost_" . $id;
        $this->cache->delete($key);
        $key = $this->key_prefix . "comments_" . $id;
        $this->cache->delete($key);
        $key = $this->key_prefix . "comments_count_" . $id;
        $this->cache->delete($key);
        $sql = "delete from  " . $this->table_name . " where id=" . $id;
        $sql2 = "delete from  " . $this->table_comment . " where blog_id=" . $id;
        return array($this->db->query($sql), $this->db->query($sql2));
    }
    
    
    // ================= Private =================
    
    private function getUsersInfo() {
    	$key = $this->key_prefix . "usersInfo";
    	$names = $this->cache->fetch($key);
    	if (!$names) {
    		$names = $this->db->fetchAll("Select id,first_name,last_name,email,avatar From oat_user_info");
    		$newNames = array();
	        foreach($names as $name) {
	        	$id = array_shift($name);
	        	$newNames[$id] = $name;
	        }
	        $names = $newNames;
	        
    		$this->cache->store($key, $names, array('life_time' => 600));
    	}
    	
        return $names;
    }
}

?>
