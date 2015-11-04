<?php

/**
 * 首页管理
 *
 * @author 刘松森 <liusongsen@gmail.com>
 */
class ucenter_comments extends app {

    public function __construct() {
        parent::__construct();
    }

    //评论
    public function postComments() {
        if ($this->user) {
            $user_id = $this->user['id'];
            $goods_id = $_POST['goods_id'];
            $the_date = $_POST['the_date'];
            $starts = $_POST['starts'];
            $comments = $_POST['comments'];
            if (!verify::vIsNNUll($comments)) {
                core::outPut(-1, "评论内容输入不能为空");
            }
            if (!verify::vIsNumber($starts)) {
                core::outPut(-1, "评论星级选择不能为空");
            }
            if (!verify::vIsNumber($goods_id)) {
                core::outPut(-1, "商品ID不能为空");
            }
            //先判断该用户是否有购买过该商品
            $this->CT_Api->api = "en.order.checkComments";
            $this->CT_Api->user_id = $user_id;
            $this->CT_Api->goods_id = $goods_id;
            $result = $this->CT_Api->get();
            if ($result != 1) {
                core::outPut(-1, "必须购买了该产品才能评论");
            }
            $this->CT_Api->api = "en.comments.save";
            $this->CT_Api->setParams(array(
                'line_id' => $result['response']['id'],
                'user_id' => $user_id,
                'goods_id' => $goods_id,
                'the_date' => $the_date,
                'starts' => $starts,
                'content' => $comments,
            ));
            $result = $this->CT_Api->post();
            if ($result['code'] == 1) {
                core::outPut(1, "评论发表成功");
            } else {
                core::outPut(-1);
            }
        } else {
            core::outPut(1001, "Please login");
        }
    }

}
