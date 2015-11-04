<?php

/**
 * 模块：en.dwz
 * 功能: 短网址管理
 * 栏目：国际站API
 */
class api_en_dwz {

    private $model;

    //构造函数
    public function __construct() {
        $this->model = new module_en_dwz();
    }

    /*
     * api： user.dwz.get
     * 功能：获取短网址记录信息
     * 调用：CT_Api::get();
     * 参数：$id：短网址记录id 必须
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

    /*
     * api： user.dwz.getUrlById
     * 功能：获取常用网址信息
     * 调用：CT_Api::get();
     * 参数：$id：常用网址id 必须
     */

    public function getUrlById() {
        $id = core::getInput("id");
        $data = $this->model->getUrlById($id);

        if ($data) {
            //存在
            core::outPut(1, $data);
        } else {
            //不存在
            core::outPut(-1);
        }
    }

    /*
     * api： user.dwz.getByKeywords
     * 功能：获取短网址记录信息
     * 调用：CT_Api::get();
     * 参数：$keyword：关键字 必须，$ip:ip地址 必须,$browser:浏览器类型 必须,$refer:refer地址 
     */

    public function getByKeywords() {
        $keyword = core::getInput("keyword");
        $ip = core::getInput("ip");
        $browser = core::getInput("browser");
        $refer = core::getInput("refer");
        $guid = core::getInput("guid");
        $xycps = core::getInput("xycps");
        $data = $this->model->getByKeyWords($keyword, $ip, $browser, $refer, $guid, $xycps);

        if ($data) {
            //存在
            core::outPut(1, $data);
        } else {
            //不存在
            core::outPut(-1);
        }
    }

    /*
     * api： user.dwz.getList
     * 功能：获取短网址列表
     * 调用：CT_Api::getList($condition,$option,$fields);
     * 参数：$condition: 查询的条件 格式为数组 如 $condition = array(‘type’=>1,'id’=>2)或者$condition = “type=1 and id=2”
     *       $option: 格式 array(‘order’=>’id desc’,’offset’=>0,’limit’=>10,’group’=>’ id’); 每个值都是可选的 默认为null
     *       $fields: 要查询的字段，默认为所有 即 *
     */

    public function getList() {
        $where = core::getInput('where');
        $fields = core::getInput('fields');
        $option = core::getInput('option');
        // 处理转义数据
        $where = stripcslashes($where);
        $data = $this->model->getList($where, $option, $fields);

        if ($data) {
            //存在
            core::outPut(1, $data);
        } else {
            //不存在
            core::outPut(-1);
        }
    }

    /*
     * api： user.dwz.getCommonUrlList
     * 功能：获取常用短网址列表
     * 调用：CT_Api::getList($condition,$option,$fields);
     * 参数：$condition: 查询的条件 格式为数组 如 $condition = array(‘type’=>1,'id’=>2)或者$condition = “type=1 and id=2”
     *       $option: 格式 array(‘order’=>’id desc’,’offset’=>0,’limit’=>10,’group’=>’ id’); 每个值都是可选的 默认为null
     *       $fields: 要查询的字段，默认为所有 即 *
     */

    public function getCommonUrlList() {
        $where = core::getInput('where');
        $fields = core::getInput('fields');
        $option = core::getInput('option');
        // 处理转义数据
        $where = stripcslashes($where);
        $data = $this->model->getCommonUrlList($where, $option, $fields);

        if ($data) {
            //存在
            core::outPut(1, $data);
        } else {
            //不存在
            core::outPut(-1);
        }
    }

    /*
     * api： user.dwz.save
     * 功能：保存（新建/更新）短网址信息
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

    /*
     * api： user.dwz.saveCommonUrl
     * 功能：保存（新建/更新）短网址信息
     * 调用：CT_Api::post();
     * 参数：$data: CT_Api::setParams($data); 一维数组 对应数据库字段的键值对 必须 若包含id则为更新，若不包含id即为新增
     */

    public function saveCommonUrl() {
        $data = core::getInput();
        $result = $this->model->saveCommonUrl($data);

        if ($result) {
            core::outPut(1, $result);
        } else {
            core::outPut(-1);
        }
    }

    /*
     * api： user.dwz.delete
     * 功能：删除短网址信息
     * 调用：CT_Api::post();
     * 参数：$data: CT_Api::setParams($data); 一维数组 对应数据库字段的键值对 必须 若包含id则为更新，若不包含id即为新增
     */

    public function delete() {
        $data = core::getInput();
        $result = $this->model->delete($data);
        if ($result) {
            core::outPut(1, $result);
        } else {
            core::outPut(-1);
        }
    }

}

?>
