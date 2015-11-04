<?php

/*
 * 模块：en.dwz
 * 功能: 短网址模型类
 */

class module_en_dwz {

    private $db; //数据库对象
    private $cache; //缓存对象
    private $key_prefix = "EN_DWZ_"; //缓存key前缀
    private $table_name = 'dwz'; //表名称
    private $dwz_config; //短网址配置文件

    //构造方法

    public function __construct() {

        $this->db = db::getInstance('guojizhan');
        $this->cache = cache::getInstance("memcached");
        $this->dwz_config = core::getConfig("", "dwz");
    }

    /**
     * 获取一条短网址记录
     *
     * @param int $id 短网址记录id
     * @return array
     */
    public function get($id) {
        $key = $this->key_prefix . $id;
        $data = $this->cache->fetch($key);
        if (!$data) {
            $sql = "SELECT a.*,b.url_link,b.url_name,b.status,b.common,c.category_name
                FROM `bdt_dwz` a,bdt_url b left join bdt_url_category c on b.category_id = c.category_id 
                where a.url_id = b.url_id and a.valid_flag='y' and a.dwz_id={$id}";
            $data = $this->db->fetch($sql);
            $this->cache->store($key, $data, array('life_time' => 3600));
        }
        return $data;
    }

    /**
     * 获取一条常用网址信息
     *
     * @param int $id 常用网址id
     * @return array
     */
    public function getUrlById($id) {
        $key = "BDT_URL_" . $id;
        $data = $this->cache->fetch($key);
        if (!$data) {
            $sql = "select * from bdt_url where url_id={$id}";
            $data = $this->db->fetch($sql);
            $this->cache->store($key, $data, array('life_time' => 3600));
        }
        return $data;
    }

    /**
     * 通过keywords获取短网址信息
     *
     * @param string $keyword 关键字
     * @param string $Ip      ip地址
     * @param string $browser 浏览器类型
     * @param string $refer   refer路径
     * @return array 
     */
    public function getByKeyWords($keyword, $ip, $browser, $refer, $guid = 0, $xycps = 0) {
        #输入参数
        $in_procarr = array(
            'p_staff_id' => 0, #middleint(8)  unsigned, 员工id(成功返回员工id，失败返回0)
            'p_guid' => (int) $guid, #int(10) unsigned, guid()
            'p_xycps' => (int) $xycps, #mediumint(8)  unsigned,xycps()
            'p_dwz_key' => $keyword, #varchar(6),  短网址键值(不能为空)
            'p_ip' => addslashes($ip), #varchar(20), ip地址()
            'p_browser' => addslashes($browser), #varchar(80), 当前登录的浏览器()
            'p_refer_link' => addslashes($refer), #varchar(200), 前页地址()
            'p_url_link' => "", #varchar(200), 地址(成功返回地址，失败返回空串)
            'p_result' => ""              #varchar(255)  返回信息(成功返回"success"+提示，失败返回原因)
        );
        #输出参数
        $out_procarr = array(
            'p_staff_id', 'p_url_link', 'p_result'
        );
        return $this->db->exec_proc("oap_dwz_log", $in_procarr, $out_procarr);
    }

    /**
     * 获取常用网址列表
     * 
     * @param array|string $where 查询条件
     * @param array   $option  操作选项
     * @param string  $fields  查询字段
     * @return array
     */
    public function getCommonUrlList($where = null, $option = array(), $fields = "*") {
        $sql = $this->db->getSelectSql("bdt_url", $where, $fields, $option);
        return $this->db->fetchAll($sql);
    }

    /**
     * 获取短网址记录列表
     *
     * @param array|string $where 查询条件
     * @param array   $option  操作选项
     * @param string  $fields  查询字段
     * @return array
     */
    public function getList($where = null, $option = array('offset' => 0, 'limit' => 10), $fields = "*") {
        $sql = "SELECT " . ($fields == "*" ? "a.*,b.url_link,b.url_name,b.status,b.common,c.category_name" : $fields) . "
                FROM `bdt_dwz` a,bdt_url b left join bdt_url_category c on b.category_id = c.category_id 
                where a.url_id = b.url_id and a.valid_flag='y' and b.status in ('有效','待验证') " . ($where ? " AND " : " ") . $where . " order by a.visits desc,a.creation_date desc  limit {$option['offset']},{$option['limit']}";
        return $this->db->fetchAll($sql);
    }

    /**
     * 保存短网址
     * 
     * @param array $data 数据
     * @return array 
     */
    public function save($data) {
        #输入参数
        $in_procarr = array(
            'p_dwz_id' => (int) $data['dwz_id'], #int(10)  unsigned,     短网址id()
            'p_cur_staff_id' => $data['cur_staff_id'], #middleint(8)  unsigned,  操作人id(不能为空)
            'p_url_link' => stripslashes($data['url_link']), #varchar(200),地址(http://开头)
            'p_url_name' => $data['url_name'], #varchar(50), 标题(地址跟标题，要么都为空，要么都不为空)
            'p_result' => '', #varchar(255) 返回信息(成功返回"success"+提示，失败返回原因)
        );
        #输出参数
        $out_procarr = array(
            'p_dwz_id', 'p_result'
        );
        return $this->db->exec_proc("oap_dwz", $in_procarr, $out_procarr);
    }

    /**
     * 保存常用网址
     * 
     * @param array $data 数据
     * @return array 
     */
    public function saveCommonUrl($data) {
        // 清空缓存
        if ($data['url_id'] > 0) {
            $key = "BDT_URL_" . $data['url_id'];
            $this->cache->delete($key);
        }
        // 输入参数
        $in_procarr = array(
            'p_url_id' => (int) $data['url_id'], #smallint(5)   unsigned,     地址id(尽可能返回地址id，录入时填入0)
            'p_cur_staff_id' => $data['cur_staff_id'], #middleint(8)  unsigned,     操作人id(不能为空)
            'p_ins_upd' => $data['url_id'] > 0 ? 2 : 1, #tinyint(3)    unsigned,     操作类型(不能为空，1录入 2修改)
            'p_url_link' => stripslashes($data['url_link']), #varchar(200),               地址(不能为空，http://开头)
            'p_url_name' => stripslashes($data['url_name']), #varchar(50),                标题(不能为空)
            'p_category' => stripslashes($data['category']), #varchar(10),                分类(不能为空，在分类表中存在)
            'p_status' => stripslashes($data['status']), #varchar(10),                状态(不能为空，'待验证', '有效', '无效', '过期')
            'p_common' => stripslashes($data['common']), #varchar(10),                常用(不能为空，'', '常用')
            'p_comments' => stripslashes($data['comments']), #varchar(255),               备注信息()
            'p_result' => ""            #varchar(255)                返回信息(成功返回"success"+提示，失败返回原因)
        );
        // 输出参数
        $out_procarr = array(
            'p_url_id', 'p_result'
        );
        return $this->db->exec_proc("oap_url", $in_procarr, $out_procarr);
    }

    /**
     * 删除常用网址
     * 
     * @param array $data 数据
     * @return array 
     */
    public function delete($data) {
        // 清空缓存
        if ($data['dwz_id'] > 0) {
            $key = $this->key_prefix . $data['dwz_id'];
            $this->cache->delete($key);
        }
        // 输入参数
        $in_procarr = array(
            'p_dwz_id' => $data['dwz_id'], #int(10) unsigned, 短网址id(不能为空)
            'p_cur_staff_id' => $data['cur_staff_id'], #middleint(8)  unsigned,操作人id(不能为空)
            'p_comments' => $data['comments'], #varchar(255), 删除原因()
            'p_result' => "", #varchar(255) 返回信息(成功返回"success"+提示，失败返回原因)
        );
        // 输出参数
        $out_procarr = array(
            'p_result'
        );
        return $this->db->exec_proc("oap_dwz_del", $in_procarr, $out_procarr);
    }

}

?>
