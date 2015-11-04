<?php

/**
 * 模块：end.goodsTag
 * 功能: 国际站商品管理
 * 栏目：国际站API
 */
class api_en_goodsTag {

    private $model;

    public function __construct() {
        $this->model = new module_en_goodsTag();
    }

    /**
     * api：en.goodsTag.get
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

  
 

}

?>
