<?php

/**
 * module：en.userMessages
 * function: user messages management class
 */
class module_en_userMessages {

    private $db; // database instance
    private $table_name = 'oat_user_message'; // table name

    public function __construct() {
        $this->db = db::getInstance('guojizhan');
    }

    /**
     * Get all messages of a user
     * 
     * @param int $uid user id
     * @return array
     */
    public function getAllMsgs($uid) {
        $uid = intval($uid);
        return $this->db->fetchAll("select * from {$this->table_name} where (senderID=$uid and isSenderDeleted='n') or (receiverID=$uid and isReceiverDeleted='n')");
    }
    
    /**
     * Get messages sent of a user
     * 
     * @param int $uid user id
     * @return array
     */
    public function getSentMsgs($uid) {
        $uid = intval($uid);
        return $this->db->fetchAll("select * from {$this->table_name} where senderID=$uid and isSenderDeleted='n'");
    }
    
    /**
     * Get messages received of a user
     * 
     * @param int $uid user id
     * @return array
     */
    public function getReceivedMsgs($uid) {
        $uid = intval($uid);
        return $this->db->fetchAll("select * from {$this->table_name} where receiverID=$uid and isReceiverDeleted='n'");
    }

    /**
     * Get messages with any conditions
     * 
     * @param array|string $where 查询条件
     * @param array   $option  操作选项
     * @param string  $fields  查询字段
     * @return array
     */
    public function getMsgs($where = null, $option = array(), $fields = "*") {
        $sql = $this->db->getSelectSql($this->table_name, $where, $fields, $option);
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get unread messages of a user
     * 
     * @param int $uid user id
     * @return array
     */
    public function getUnreadMsgs($uid) {
        $uid = intval($uid);
        return $this->db->fetchAll("select * from {$this->table_name} where receiverID=$uid and isReceiverDeleted='n' and isRead='n'");
    }

    /**
     * Send msg
     *
     * @param array $data 数据
     * @return int
     */
    public function sendMsg($data) {
    	$data['id'] = 0;
    	if (!$data['senderID']) $data['senderID'] = 0;
        $this->db->insert($this->table_name, $data);
        return $this->db->getLastId();
    }
    
    /**
     * Mark msg
     *
     * @param $ids: int / array, $mode: 'send' / 'receive'
     * @return int
     */
    public function markMsgs($ids, $mode) {
    	if ($mode == 'send') $set = "isSenderMarked='y'";
    	else if ($mode == 'receive') $set = "isReceiverMarked='y'";
    	
    	if ($set) {
    		$sql = "update {$this->table_name} set " . $set . " where ";
    		if (is_array($ids)) {
    			$sql .= "id=$ids[0]";
    			array_shift($ids);
    			foreach($ids as $id) {
    				$sql .= " or id=$id";
    			}
    		}
    		else {
    			$sql .= "id=$ids";
    		}
    		return $this->db->query($sql);
    	}
    	return;
    }
    
    /**
     * Read msg
     *
     * @param $ids: msg id(s), $ifGetMsg: true / false
     * @return array / int{0,1}
     */
    public function readMsg($ids, $ifGetMsg=FALSE) {
    	$sql = "update {$this->table_name} set isRead='y' where ";
    	if (is_array($ids)) {
    		$cond = "id=$ids[0]";
    		array_shift($ids);
    		foreach($ids as $id) {
    			$cond .= " or id=$id";
    		}
    	}
    	else {
    		$cond = "id=$ids";
    	}
    	
    	if ($ifGetMsg) {
    		return array($this->db->query($sql.$cond), $this->db->fetchAll("select * from {$this->table_name} where ".$cond));
    	}
    	else {
    		return $this->db->query($sql);
    	}
    }

    /**
     * Delete messages
     *
     * @param $ids: msg id(s), $mode: 'all' / 'send' / 'receive', $uid: user id
     * @return int
     */
    public function deleteMsgs($ids, $mode, $uid=0) {
    	$sqls = "update {$this->table_name} set isSenderDeleted='y' where ";
    	$sqlr = "update {$this->table_name} set isReceiverDeleted='y' where ";
    	if ($mode == 'all') {
	    	if (is_array($ids)) {
	    		$sid = "(id=$ids[0] and senderID=$uid)";
	    		$rid = "(id=$ids[0] and receiverID=$uid)";
	    		array_shift($ids);
	    		foreach($ids as $i) {
	    			$sid .= " or (id=$i and senderID=$uid)";
	    			$rid .= " or (id=$i and receiverID=$uid)";
	    		}
	    	}
	    	else {
	    		$sid = "(id=$ids and senderID=$uid)";
	    		$rid = "(id=$ids and receiverID=$uid)";
	    	}
	    	$sqls .= $sid;
	    	$sqlr .= $rid;
	    	return array($this->db->query($sqls), $this->db->query($sqlr));
	    }
	    else {
	    	if ($mode == 'send') $sql = $sqls;
	    	else if ($mode == 'receive') $sql = $sqlr;
	    	else return;
	    	
	    	if (is_array($ids)) {
	    		$id = "id=$ids[0]";
	    		array_shift($ids);
	    		foreach($ids as $i) {
	    			$id .= " or id=$i";
	    		}
	    	}
	    	else {
	    		$id = "id=$ids";
	    	}
	    	$sql .= $id;
	    	return $this->db->query($sql);
	    }
    }
}

?>
