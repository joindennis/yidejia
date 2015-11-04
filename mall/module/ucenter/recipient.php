<?php

/**
 * 结算管理
 *
 * @author 刘松森 <liusongsen@gmail.com>
 */
class ucenter_recipient extends app {

    public function __construct() {
        parent::__construct();
    }

    //编辑收件人信息
    public function recipientPost() {
        if ($this->user) {
            core::outPut(1);
        } else {
            core::outPut(1001, "Please login");
        }
    }

}
