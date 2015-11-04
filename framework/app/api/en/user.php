<?php

/**
 * 模块：en.user
 * 功能: 国际站用户管理
 * 栏目：国际站API
 */
class api_en_user {

    private $model;

    public function __construct() {
        $this->model = new module_en_user();
    }


/**
     * api： en.ucenter.get
     * 功能：根据皮肤分析ID获取皮肤分析信息
     * 调用：CT_Api::get();
     * 参数：$id：皮肤分析id 必须
     */
    public function getByUID() {
        $id = core::getInput("id");
        $data = $this->model->getByUID($id);
        if ($data) {
            //存在
            core::outPut(1, $data);
        } else {
            //不存在
            core::outPut(-1);
        }
    }



    /**
     * api： en.user.get
     * 功能：根据用户ID获取用户信息
     * 调用：CT_Api::get();
     * 参数：$id：用户ID 必须
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
     * api： en.user.getByEmail
     * 功能：根据邮箱获取用户信息
     * 调用：CT_Api::get();
     * 参数：$email：邮箱 必须
     */
    public function getByEmail() {
        $email = core::getInput("email");
        $data = $this->model->getByEmail($email);
        if ($data) {
            //存在
            core::outPut(1, $data);
        } else {
            //不存在
            core::outPut(-1);
        }
    }

    /**
     * api： en.user.getVerifyCode
     * 功能：获取邮箱验证码
     * 调用：CT_Api::get();
     * 参数：$email：邮箱 必须
     */
    public function getVerifyCode() {
        $email = core::getInput("email");
        $code = rand(100000, 999999);
		
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 10; $i++) {
    $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
	$promoCode=$randomString;	
		
		
		
		
        $result = $this->model->saveVerifyCode($email, $code, $promoCode);
        
        //发送邮件
        if ($result > 0) {
            $token = md5(md5((int) $result));
			

			
            $content=<<<EOF
Dear valued customer {$email} , 
<br/>
Thanks for registering on www.yidejia.ca. Please keep this e-mail for your
<br/>savebox. Your account information is as follows:
<br/>
<br/>
---------------------------- 
<br/>Username: {$email}
<br/>
----------------------------
<br/>
<br/>
---------------------------- 
<br/>Promo Code: {$promoCode}
<br/>
----------------------------
<br/>
<br/>
Your account is currently inactive, the administrator of the board will 
<br/>need to activate it before you can log in. Please click the link below to 
<br/>complete the registration.
<br/>
<a href='http://www.yidejia.ca/index.php?m=user&c=index&a=activeEmail&id={$result}&token={$token}' />http://www.yidejia.ca/index.php?m=user&c=index&a=activeEmail&id={$result}&token={$token}</a>
<br/>
If clicking doesn’t work, please copy it and paste into the browser address 
<br/>bar. 
<br/>Thank you for registering. 
<br/>YIDEJIA
<br/>
<br/>
EOF;
            $mail = new module_common_email();
            $mail->send($email, "E-mail verification remind", $content, "en");
        }
        core::outPut(1, $result);
    }

    /**
     * api： en.user.getPwdVerifyCode
     * 功能：获取邮箱验证码
     * 调用：CT_Api::get();
     * 参数：$email：邮箱 必须
     */
    public function getPwdVerifyCode() {
        $email = core::getInput("email");
        $code = rand(100000, 999999);
        $result = $this->model->saveVerifyCode($email, $code);
        //发送邮件
        if ($result > 0) {
            $token = md5(md5((int) $result));
            $content=<<<EOF
Dear {$email}, 
<br/>You have requested to reset your password on 
<br/><a href="http://www.yidejia.cn" >www.yidejia.ca</a>
<br/>
because you have forgotten your password.
<br/>
<br/>
If you did not request this, please ignore it. It will expire and become
<br/>
useless in 24 hours time. 
<br/>To reset your password, please visit the following page: 
<br/><a href='http://www.yidejia.ca/index.php?m=user&c=index&a=resetPassword&id={$result}&token={$token}' >http://www.yidejia.ca/index.php?m=user&c=index&a=resetPassword&id={$result}&token={$token}</a>
<br/>When you visit that page, your password will be reset, and the new password 
<br/>will be emailed to you. 
<br/>
<br/>Your username is: {$email}
EOF;
            $mail = new module_common_email();
            $mail->send($email, "E-mail find password remind", $content, "en");
        }
        core::outPut(1, $result);
    }

    /**
     * api： en.user.activation
     * 功能：激活邮箱
     * 调用：CT_Api::get();
     * 参数：$token：激活的token 必须,$id: 激活码ID 必须
     */
    public function activation() {
        $token = core::getInput("token");
        $id = (int) core::getInput("id");
        $data = $this->model->getVerifyCode($id);
        if (!$data) {
            core::outPut(-1, $data);
        }
        if (md5(md5($id)) != $token) {
            core::outPut(-1, "The link authentication failed.");
        }
        if (time() - strtotime($data['the_time']) > 7200) {
            core::outPut(-1, "The link has expired.");
        }
        $result = $this->model->activeEmail($data['email'], $data['code']);
        if ($result) {
            core::outPut(1, "Your account has been activated,please login in.");
        } else {
            core::outPut(-1, "The mailbox activation failed.");
        }
    }



/////////////////////////////////////////////



  public function sendConfirmationEmail() {
   
   
      $content=<<<EOF
Dear {$email},  THIS IS THE TEST !!!!!!
<br/>You have requested to reset your password on 
<br/><a href="http://www.yidejia.cn" >www.yidejia.ca</a>
<br/>
because you have forgotten your password.
<br/>
<br/>
If you did not request this, please ignore it. It will expire and become
<br/>
useless in 24 hours time. 
<br/>To reset your password, please visit the following page: 
<br/><a href='http://www.yidejia.ca/index.php?m=user&c=index&a=resetPassword&id={$result}&token={$token}' >http://www.yidejia.ca/index.php?m=user&c=index&a=resetPassword&id={$result}&token={$token}</a>
<br/>When you visit that page, your password will be reset, and the new password 
<br/>will be emailed to you. 
<br/>
<br/>Your username is: {$email}
EOF;
            $mail = new module_common_email();
            $mail->send('work.khakimov@gmail.com', "E-mail confirmation", $content, "en");
   core::outPut(1, "Your account has been activated,please login in.");
   
   
   
    }





/////////////////////////////////////////////

    /**
     * api： en.user.resetPwd
     * 功能：激活邮箱
     * 调用：CT_Api::get();
     * 参数：$token：激活的token 必须,$id: 激活码ID 必须,$password: 新密码
     */
    public function resetPwd() {
        $token = core::getInput("token");
        $id = (int) core::getInput("id");
        $password = core::getInput("password");
        $data = $this->model->getVerifyCode($id);
        if (!$data) {
            core::outPut(-1, $data);
        }
        if (md5(md5($id)) != $token) {
            core::outPut(-1, "The link authentication failed.");
        }
        if (time() - strtotime($data['the_time']) > 7200) {
            core::outPut(-1, "The link has expired.");
        }
        $result = $this->model->resetPwd($data['email'], $password);
        if ($result) {
            core::outPut(1, "Change password successfully.");
        } else {
            core::outPut(-1, "Sorry,Change the password failed.");
        }
    }

    /**
     * api： en.user.getList
     * 功能：获取用户列表
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
     * api： en.user.save
     * 功能：保存（新建/更新）用户信息
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
     * api： en.user.save
     * 功能：保存（新建/更新）用户信息
     * 调用：CT_Api::post();
     * 参数：$data: CT_Api::setParams($data); 一维数组 对应数据库字段的键值对 必须 若包含id则为更新，若不包含id即为新增
     */
    public function updatePassword() {
        $data = core::getInput();
        $result = $this->model->updatePassword($data);
        if ($result) {
            core::outPut(1, $result);
        } else {
            core::outPut(-1);
        }
    }
	
	

    /**
     * api： en.user.pffx
     * 功能：保存皮肤分析信息
     * 调用：CT_Api::post();
     * 参数：$data: CT_Api::setParams($data); 一维数组 对应数据库字段的键值对 必须 若包含id则为更新，若不包含id即为新增
     */
    public function pffx() {
        $data = core::getInput();
        $result = $this->model->savePffx($data);
        if ($result) {
            core::outPut(1, $result);
        } else {
            core::outPut(-1);
        }
    }

    /**
     * api： en.user.delete
     * 功能：删除用户信息
     * 调用：CT_Api::post();
     * 参数：$id: 用户id  必须
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
