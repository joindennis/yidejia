<?php

/**
 * 模块：en.goods
 * 功能: 国际站商品模型类
 * 
 * @author 刘松森  <liusongsen@gmail.com>
 */
class module_en_goods {

    private $db;
    private $cache;
    private $key_prefix = "EN_OAT_GOODS_";
    private $table_name = 'oat_goods';

    public function __construct() {
        $this->db = db::getInstance("guojizhan");
        $this->cache = cache::getInstance("memcached");
    }

    /**
     * 获取一条信息
     * 
     * @param int $id 商品id
     * @return array
     */
    public function get($id) {
        $key = $this->key_prefix . $id;
        $data = $this->cache->fetch($key);
        if (!$data) {
            $data = $this->db->fetch("select * from {$this->table_name} where goods_id={$id}");
            $this->cache->store($key, $data, array('life_time' => 3600 * 24 * 10));
        }
        if ($data) {
            //获取标签集合
            $sql = "select a.*,b.url_name,b.name from oat_goods_tag a, oat_tag b where a.tag_id = b.id and a.goods_id=" . $id;
            $tags = $this->db->fetchAll($sql);
            $data['tags'] = $tags;
            foreach ((array) $tags as $v) {
                $data['tag_ids'][] = $v['tag_id'];
            }
            //获取类型名称
            $sql = "select * from oat_category where id=" . (int) $data['type'];
            $types = $this->db->fetch($sql);
            $data['types'] = $types;
        }
        return $data;
    }

    /**
     * 获取一条信息
     * 
     * @param string $name  商品名称
     * @return array
     */
    public function getByName($name) {
        $data = $this->db->fetch("select * from {$this->table_name} where goods_name='" . $name . "'");
        if ($data) {
            //获取标签集合
            $sql = "select a.*,b.url_name,b.name from oat_goods_tag a, oat_tag b where a.tag_id = b.id and a.goods_id=" . $data['goods_id'];
            $tags = $this->db->fetchAll($sql);
            $data['tags'] = $tags;
            foreach ((array) $tags as $v) {
                $data['tag_ids'][] = $v['tag_id'];
            }
            //获取类型名称
            $sql = "select * from oat_category where id=" . (int) $data['type'];
            $types = $this->db->fetch($sql);
            $data['types'] = $types;
        }
        return $data;
    }
    
    
 
	

    /**
     * 获取一条信息
     * 
     * @param string $url_name  商品访问地址
     * @return array
     */
    public function getByUrlName($url_name) {
        $data = $this->db->fetch("select * from {$this->table_name} where url_name='" . $url_name . "'");
        if ($data) {
            //获取标签集合
            $sql = "select a.*,b.url_name,b.name from oat_goods_tag a, oat_tag b where a.tag_id = b.id and a.goods_id=" . $data['goods_id'];
            $tags = $this->db->fetchAll($sql);
            $data['tags'] = $tags;
            foreach ((array) $tags as $v) {
                $data['tag_ids'][] = $v['tag_id'];
            }
            //获取类型名称
            $sql = "select * from oat_category where id=" . (int) $data['type'];
            $types = $this->db->fetch($sql);
            $data['types'] = $types;
        }
        return $data;
    }

    /**
     * 获取套装产品组合信息
     * 
     * @param int $goods_id 产品ID
     * @return array
     */
    public function getSuitList($goods_id) {
        $sql = "select * from {$this->table_name} where pre_goods_id=" . $goods_id;
        $data = $this->db->fetchAll($sql);
        if (!$data) {
            return false;
        }
        $result = $colors = $sizes = array();
        foreach ($data as $v) {
            $colors[] = $v['color'];
            $sizes[] = $v['size'];
            $result[] = array(
                'variation_id' => $v['goods_id'],
                'variation_is_visible' => true,
                'is_purchasable' => true,
                'attributes' => array(
                    'attribute_pa_size' => $v['size'], //尺寸
                    'attribute_color' => $v['color']//颜色
                ),
                'image_src' => 'http://img2.atido.com/' . $v['imgname'], //图片
                'image_link' => '',
                'image_title' => $v['desc'], //图片title
                'image_alt' => $v['goods_name'],
                'price_html' => '',
                'availability_html' => "<p class='stock in-stock'>Stock 100</p>", //库存
                'sku' => $v['sku_id'],
                'weight' => '',
                'dimensions' => $v['spec'],
                'min_qty' => 1,
                'max_qty' => 100,
                'backorders_allowed' => false,
                'is_in_stock' => true,
                'is_downloadable' => false,
                'is_virtual' => false,
                'is_sold_individually' => 'no'
            );
        }
        return array('data' => $result, 'colors' => array_unique($colors), 'sizes' => array_unique($sizes));
    }

    /**
     * 功能:获取商品列表
     * 
     * @param array|string $where 查询条件
     * @param array   $option  操作选项
     * @param string  $fields  查询字段
     * @return array
     */
    public function getList($where = null, $option = array(), $fields = "*") {
        $sql = $this->db->getSelectSql($this->table_name, $where, $fields, $option);
        $data = $this->db->fetchAll($sql);
        if ($data) {
            foreach ($data as $k => $v) {
                //获取类型名称
                $sql = "select * from oat_category where id=" . (int) $v['type'];
                $data[$k]['types'] = $this->db->fetch($sql);
            }
        }
        return $data;
    }

    /**
     * 保存商品信息
     * 
     * @param array $data 数据
     * @return int
     */
    public function save($data) {
        $tags = $data['tag'];
        $sizes = $data['size'];
        $colors = $data['color'];
        unset($data['tag']);
        if ((int) $data['goods_id'] > 0) {
            //更新
            $key = $this->key_prefix . $data['goods_id'];
            $this->cache->delete($key);
            $result = $this->db->update($this->table_name, 'goods_id', $data);
            $result && $goods_id = $data['goods_id'];
        } else {
            //新建
            $data['goods_id'] = 0;
            $this->db->insert($this->table_name, $data);
            $goods_id = $this->db->getLastId();
        }
        //获取产品详情
        $sql = "select * from oat_goods where goods_id=" . $goods_id;
        $goodInfo = $this->db->fetch($sql);
        //先获取所有子产品
        if ($goods_id > 0 && $goodInfo['pre_goods_id'] == 0) {
            $newData = array();
            $sql = "select * from oat_goods where pre_goods_id=" . $goods_id;
            $childrens = $this->db->fetchAll($sql);
            foreach ((array) $childrens as $v) {
                $newData[$v['color'] . "#" . $v['size']] = array('img' => $v['imgname'], 'sku_id' => $v['sku_id']);
            }
            //先删除所有的
            if ($childrens) {
                $sql = "delete from oat_goods where pre_goods_id=" . $goods_id;
                $this->db->query($sql);
            }
            if ($sizes != "") {
                $sizes = explode(",", $sizes);
                foreach ($sizes as $v) {
                    if ($v) {
                        list($color, $size) = explode("#", $v);
                        $tempData = $data;
                        $tempData['goods_id'] = 0;
                        $tempData['pre_goods_id'] = $goods_id;
                        $tempData['imgname'] = $newData[$v]['img'];
                        $tempData['goods_name'] = $data['goods_name'] . "/" . $color . "/" . $size;
                        $tempData['url_name'] = $data['url_name'] . "/" . $color . "/" . $size;
                        $tempData['color'] = $color;
                        $tempData['size'] = $size;
                        $tempData['sku_id'] = (int) $newData[$v]['sku_id'];
                        $this->db->insert($this->table_name, $tempData);
                        $this->db->getLastId();
                    }
                }
            } else {
                $colors = explode(",", $colors);
                foreach ($colors as $v) {
                    if ($v) {
                        $tempData = $data;
                        $tempData['goods_id'] = 0;
                        $tempData['pre_goods_id'] = $goods_id;
                        $tempData['imgname'] = $newData[$v . "#"]['img'];
                        $tempData['goods_name'] = $data['goods_name'] . "/" . $v . "/" . $size;
                        $tempData['url_name'] = $data['url_name'] . "/" . $v . "/" . $size;
                        $tempData['color'] = $v;
                        $tempData['size'] = "";
                        $tempData['sku_id'] = (int) $newData[$v . "#"]['sku_id'];
                        $this->db->insert($this->table_name, $tempData);
                        $this->db->getLastId();
                    }
                }
            }
        }
        //批量同步修改所有产品图片
        if ($data['goods_id'] > 0 && $data['imgname'] != "" && $goodInfo['pre_goods_id'] > 0) {
            $sql = "update oat_goods set imgname='" . $data['imgname'] . "' where pre_goods_id=" . $goodInfo['pre_goods_id'] . " and color='" . $data['color'] . "'";
            $this->db->query($sql);
        }
        //先删除
        if ($goods_id > 0) {
            $sql = "delete from oat_goods_tag where goods_id=" . $goods_id;
            $this->db->query($sql);
        }
        //同步保存标签关联数据
        if ($goods_id > 0 && $tags) {
            foreach ($tags as $v) {
                if ((int) $v == 0) {
                    continue;
                }
                $this->db->insert("oat_goods_tag", array('goods_id' => (int) $goods_id, 'tag_id' => (int) $v));
                $this->db->getLastId();
            }
        }
        return $goods_id;
    }

    /**
     * 增加人气
     * 
     * @param int id 商品ID
     * @return bool
     */
    public function likes($id) {
        $key = $this->key_prefix . $id;
        $this->cache->delete($key);
        return $this->db->query("update $this->table_name set likes = likes + 1 where goods_id={$id}");
    }




  /**
     * 增加人气
     * 
     * @param int id 商品ID
     * @return bool
     */
    public function view($id) {
        $key = $this->key_prefix . $id;
        $this->cache->delete($key);
        return $this->db->query("update $this->table_name set view = view + 1 where goods_id={$id}");
    }






    /**
     * 删除商品信息
     * 
     * @param int id 商品ID
     * @return bool
     */
    public function delete($id) {
        $key = $this->key_prefix . $id;
        $this->cache->delete($key);
        return $this->db->query("DELETE FROM $this->table_name where goods_id={$id}");
    }

}

?>
