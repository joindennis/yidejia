<?php

include '../boot.php';

core::dispatcher(true);

/**
 * 权限认证失败时的错误处理回调函数，如果没有此函数定义则会按默认抛出异常
 *
 */
function _onAuthFailed($module, $controller, $action) {
    redirect("http://u.yidejia.com/index.php?m=ucenter&c=user&a=userLogin");
}

?>