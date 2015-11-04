<?php

/**
 * 模块：en.order
 * 功能: 国际站订单管理
 * 栏目：国际站API
 */
class api_en_order {

    private $model;

    public function __construct() {
        $this->model = new module_en_order();
    }

    /**
     * api： en.order.get
     * 功能：根据订单ID获取订单信息
     * 调用：CT_Api::get();
     * 参数：$id：订单ID 必须
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
     * api： en.order.getByCode
     * 功能：根据订单编码获取订单信息
     * 调用：CT_Api::get();
     * 参数：$code: 订单编码 必须
     */
    public function getByCode() {
        $code = core::getInput("code");
        $data = $this->model->getByCode($code);
        if ($data) {
            //存在
            core::outPut(1, $data);
        } else {
            //不存在
            core::outPut(-1);
        }
    }

    /**
     * api： en.order.getList
     * 功能：获取订单列表
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
     * api： en.order.save
     * 功能：保存（新建/更新）订单信息
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
     * api： en.order.pay
     * 功能：修改订单支付状态
     * 调用：CT_Api::get();
     * 参数：$code: 订单编码,$token: payPai支付产生的token 必须,$payerid: payPai买家id 必须,$flag: 支付成功标示y:成功n:失败 默认失败
     */
    public function pay() {
        $code = core::getInput("code");
        $token = core::getInput("token");
        $payerid = core::getInput("payerid");
        $flag = core::getInput('flag');
        $transaction_id = core::getInput("transaction_id");
        $transaction_type = core::getInput("transaction_type");
        $result = $this->model->pay($code, $token, $payerid, $transaction_id, $transaction_type, $flag);
        if ($result) {
            core::outPut(1, $result);
        } else {
            core::outPut(-1);
        }
    }

    /**
     * api： en.order.delete
     * 功能：删除订单信息
     * 调用：CT_Api::post();
     * 参数：$id: 订单id  必须
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

    /**
     * api： en.order.checkComments
     * 功能：检查用户是否有权限发生评论
     * 调用：CT_Api::post();
     * 参数：$user_id: 用户ID,$goods_id: 产品ID
     */
    public function checkComments() {
        $user_id = core::getInput("user_id");
        $goods_id = core::getInput("goods_id");
        $result = $this->model->checkComments($user_id, $goods_id);
        if ($result) {
            core::outPut(1, $result);
        } else {
            core::outPut(-1);
        }
    }

    /**
     * api： en.order.getAmount
     * 功能：获取购物车商品总金额
     * 调用：CT_Api::get();
     * 参数：$goods_qty: 购物车商品串
     */
    public function getAmount() {
        $goods_qty = core::getInput("goods_qty");
        $data = $this->model->getAmount($goods_qty);
        if ($data) {
            core::outPut(1, $data);
        } else {
            core::outPut(-1);
        }
    }

}

?>
