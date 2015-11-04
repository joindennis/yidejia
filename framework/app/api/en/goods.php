<?php

/**
 * 模块：end.goods
 * 功能: 国际站商品管理
 * 栏目：国际站API
 */
class api_en_goods {

    private $model;

    public function __construct() {
        $this->model = new module_en_goods();
    }

    /**
     * api：en.goods.get
     * 功能：根据商品id获取商品信息
     * 调用：CT_Api::get();
     * 参数：$id：商品id
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
     * api：en.goods.getByUrlName
     * 功能：根据URL名称获取商品信息
     * 调用：CT_Api::get();
     * 参数：$url_name：商品url_name
     */
    public function getByUrlName() {
        $url_name = core::getInput("url_name");
        $data = $this->model->getByUrlName($url_name);
        if ($data) {
            //存在
            core::outPut(1, $data);
        } else {
            //不存在
            core::outPut(-1);
        }
    }

    /**
     * api：en.goods.getByName
     * 功能：根据商品名称获取商品信息
     * 调用：CT_Api::get();
     * 参数：$name：商品名称
     */
    public function getByName() {
        $name = core::getInput("name");
        $data = $this->model->getByName($name);
        if ($data) {
            //存在
            core::outPut(1, $data);
        } else {
            //不存在
            core::outPut(-1);
        }
    }

    /**
     * api： end.goods.getList
     * 功能：获取商品列表
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
     * api：en.goods.getListByQty
     * 功能：根据购物车商品串获取购物车商品详情信息
     * 调用：CT_Api::get();
     * 参数：$goods_qty：购物车商品串 必须
     */
    public function getListByQty() {
        $goods_qty = core::getInput("goods_qty");
        $products = explode(";", $goods_qty);
        $ids = array();
        foreach ($products as $v) {
            if ($v) {
                list($id, $quantity) = explode(",", $v);
                $ids[$id] = (int) $quantity;
            }
        }
        $allData = $this->model->getList("goods_id in (" . implode(",", array_keys($ids)) . ")", $option, $fields);
        if (!$allData) {
            core::outPut(-1);
        }
        $data = array();
        foreach ($allData as $v) {
                 $data[] = array('id' => $v['goods_id'], 'type' => $v['type'], 'url_name' => $v['url_name'], 'quantity' => $ids[$v['goods_id']], 'goods_name' => $v['goods_name'], 'desc' => $v['desc'], 'imgname' => $v['imgname'], 'price' => $v['sale_price']);
        }
        if ($data) {
            //存在
            core::outPut(1, $data);
        } else {
            //不存在
            core::outPut(-1);
        }
    }

    /**
     * api：en.goods.getSuitList
     * 功能：根据产品ID获取组合套装信息
     * 调用：CT_Api::get();
     * 参数：$goods_id: 产品ID 必须
     */
    public function getSuitList() {
        $goods_id = core::getInput("goods_id");
        $data = $this->model->getSuitList($goods_id);
        if ($data) {
            core::outPut(1, $data);
        } else {
            core::outPut(-1);
        }
    }

    /**
     * api： en.goods.save
     * 功能：保存（新建/更新）商品信息
     * 调用：CT_Api::post();
     * 参数：$data: CT_Api::setParams($data); 一维数组 对应数据库字段的键值对 必须 若包含id则为更新，若不包含id即为新增
     */
    public function save() {
        $data = core::getInput();
        $result = $this->model->save($data);
        if ($result) {
            //成功
            core::outPut(1, $result);
        } else {
            //失败
            core::outPut(-1);
        }
    }

    /**
     * api： en.goods.likes
     * 功能：增加人气
     * 调用：CT_Api::post();
     * 参数：$id: 商品ID  必须
     */
    public function likes() {
        $id = core::getInput("id");
        $result = $this->model->likes($id);
        if ($result) {
            //成功
            core::outPut(1);
        } else {
            //失败
            core::outPut(-1);
        }
    }
	
	
	
	
	
    /**
     * api： en.goods.likes
     * 功能：增加人气
     * 调用：CT_Api::post();
     * 参数：$id: 商品ID  必须
     */
    public function view() {
        $id = core::getInput("id");
        $result = $this->model->view($id);
        if ($result) {
            //成功
            core::outPut(1);
        } else {
            //失败
            core::outPut(-1);
        }
    }
	
	
	

    /**
     * api： en.goods.delete
     * 功能：删除商品信息
     * 调用：CT_Api::post();
     * 参数：$id: 商品ID  必须
     */
    public function delete() {
        $id = core::getInput("id");
        $result = $this->model->delete($id);
        if ($result) {
            //成功
            core::outPut(1);
        } else {
            //失败
            core::outPut(-1);
        }
    }

}

?>
