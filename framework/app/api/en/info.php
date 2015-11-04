<?php

/**
 * 模块：en.info
 * 功能: 国际站商品描述信息管理
 * 栏目：国际站API
 */
class api_en_info {

    private $model;

    public function __construct() {
        $this->model = new module_en_info();
    }

    /**
     * api： en.info.get
     * 功能：根据商品描述id获取商品描述信息
     * 调用：CT_Api::get();
     * 参数：$id：商品id
     */
    public function get() {
        $id = core::getInput("id");
        $data = $this->model->get($id);
        if ($data) {
            core::outPut(1, $data);
        } else {
            //不存在
            core::outPut(-1);
        }
    }

    /**
     * api： en.info.getList
     * 功能：获取商品描述列表
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
     * api： en.info.save
     * 功能：保存（新建/更新）商品描述信息
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
     * api： en.info.delete
     * 功能：删除商品描述信息
     * 调用：CT_Api::post();
     * 参数：$id: 商品id
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
