<?php

/**
 * 模块：en.blog
 * 功能: 国际站博客管理
 * 栏目：国际站API
 */
class api_en_blog {

    private $model;

    public function __construct() {
        $this->model = new module_en_blog();
    }

    /**
     * api： en.blog.get
     * 功能：根据博客ID获取博客信息
     * 调用：CT_Api::get();
     * 参数：$id：账单ID 必须
     */
    public function get() {
        $id = core::getInput("id");
        $data = $this->model->get($id);
        if ($data) {
            //存在
            core::outPut(1, $data);
        } else {
            //不存在
            core::outPut(-1);
        }
    }
    
    /**
     * api： en.blog.getCategories
     * func：Get all categories
     * call：CT_Api::post();
     */
    public function getCategories() {
    	$data = $this->model->getCategories();
    	if ($data) {
    		core::outPut(1, $data);
    	} else {
    		core::outPut(-1);
    	}
    }
    
    /**
     * api： en.blog.getRecentPost
     * 功能：获取博客近期发布内容列表
     * 调用：CT_Api::post();
     */
    public function getRecentPost() {
    	$num = core::getInput("num");
    	$data = $this->model->getRecentPost($num);
    	if ($data) {
    		core::outPut(1, $data);
    	} else {
    		core::outPut(-1);
    	}
    }
    
    /**
     * api： en.blog.getHotPost
     * 功能：获取博客热门内容列表
     * 调用：CT_Api::post();
     */
    public function getHotPost() {
    	$num = core::getInput("num");
    	$data = $this->model->getHotPost($num);
    	if ($data) {
    		core::outPut(1, $data);
    	} else {
    		core::outPut(-1);
    	}
    }
    
    /**
     * api： en.blog.getRelatedPost
     * 功能：获取博客热门内容列表
     * 调用：CT_Api::post();
     */
    public function getRelatedPost() {
    	$input = core::getInput();
    	$num = $input['num'];
    	$id = $input['id'];
    	$tags = array_slice($input, 3, -4);
    	$data = $this->model->getRelatedPost($num, $id, $tags);
    	core::outPut(1, $data);
    	if ($data) {
    		core::outPut(1, $data);
    	} else {
    		core::outPut(-1);
    	}
    }

    /**
     * api： en.blog.getList
     * 功能：获取博客内容列表
     * 调用：CT_Api::getList($condition,$option,$fields);
     * 参数：$condition: 查询的条件 格式为数组 如 $condition = array(‘type’=>1,'id’=>2)或者$condition = “type=1 and id=2”
     *       $option: 格式 array(‘order’=>’id desc’,’offset’=>0,’limit’=>10,’group’=>’ id’); 每个值都是可选的 默认为null
     *       $fields: 要查询的字段，默认为所有 即 *
     */
    public function getList() {
        $where = core::getInput('where');
        $fields = core::getInput('fields');
        $option = core::getInput('option');
        $data = $this->model->getList($where, $option, $fields);
        if ($data) {
            //存在
            core::outPut(1, $data);
        } else {
            //不存在
            core::outPut(-1);
        }
    }
    
    /**
     * api： en.blog.getListByTags
     * 功能：获取博客内容列表
     * 调用：CT_Api::post();
     * 参数：$tags: 数组，要查询的标签
     *       $condition: 查询的条件 格式为数组 如 $condition = array(‘type’=>1,'id’=>2)或者$condition = “type=1 and id=2”
     *       $option: 格式 array(‘order’=>’id desc’,’offset’=>0,’limit’=>10,’group’=>’ id’); 每个值都是可选的 默认为null
     *       $fields: 要查询的字段，默认为所有 即 *
     */
    public function getListByTags() {
    	$tags = core::getInput('tags');
        $where = core::getInput('where');
        $fields = core::getInput('fields');
        $option = core::getInput('option');
        $data = $this->model->getListByTags($tags, $where, $option, $fields);
        if ($data) {
            //存在
            core::outPut(1, $data);
        } else {
            //不存在
            core::outPut(-1);
        }
    }
    
    /**
     * api：  en.blog.blog_count
     * func： get blog total number
     * call： CT_Api::post();
     * param：$uid: user id
     *        $category: int
     *        $tags: str
     */
    public function blog_count() {
    	$uid = core::getInput('uid');
    	$category = core::getInput('category');
    	$tags = core::getInput('tags');
    	$data = $this->model->blog_count($uid, $category, $tags);
    	if ($data) {
    		core::outPut(1, $data);
    	} else {
    		core::outPut(-1);
    	}
    }
    
    /**
     * api： en.blog.getComments
     * func：get all comments of a specific blog article
     * call：CT_Api::post();
     * param：$blog_id：article id
     */
    public function getComments() {
        $bid = core::getInput("blog_id");
        $data = $this->model->getComments($bid);
        if ($data) {
            //存在
            core::outPut(1, $data);
        } else {
            //不存在
            core::outPut(-1);
        }
    }
    
    /**
     * api： en.blog.getCommentsCount
     * func：get number of all comments of a specific blog article
     * call：CT_Api::post();
     * param：$blog_id：article id
     */
    public function getCommentsCount() {
    	$bid = core::getInput("blog_id");
        $data = $this->model->getCommentsCount($bid);
        if ($data) {
            //存在
            core::outPut(1, $data);
        } else {
            //不存在
            core::outPut(-1);
        }
    }
    
    /**
     * api： en.blog.saveComment
     * func：update/add blog comment
     * call：CT_Api::post();
     * param：$data: CT_Api::setParams($data); 一维数组 对应数据库字段的键值对 必须 若包含id则为更新，若不包含id即为新增
     */
    public function saveComment() {
        $data = core::getInput();
        $result = $this->model->saveComment($data);
        if ($result) {
            core::outPut(1, $result);
        } else {
            core::outPut(-1);
        }
    }

    /**
     * api： en.blog.save
     * 功能：保存（新建/更新）博客信息
     * 调用：CT_Api::post();
     * 参数：$data: CT_Api::setParams($data); 一维数组 对应数据库字段的键值对 必须 若包含id则为更新，若不包含id即为新增
     */
    public function save() {
        $data = core::getInput();
        $result = $this->model->save($data);
        if ($result) {
            core::outPut(1, $result);
        } else {
            core::outPut(-1);
        }
    }

    /**
     * api： en.blog.delete
     * 功能：删除博客信息
     * 调用：CT_Api::post();
     * 参数：$id: 博客id  必须
     */
    public function delete() {
        $id = core::getInput("id");
        $result = $this->model->delete($id);
        if ($result) {
            //存在
            core::outPut(1, $result);
        } else {
            //不存在
            core::outPut(-1);
        }
    }

}

?>
