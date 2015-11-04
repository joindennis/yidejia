<?php

/**
 * module��en.userMessages
 * function: user messages management
 * class��API
 *
 * user id = 0: system
 * user id < 0: sales men
 */
class api_en_userMessages {

    private $model;

    public function __construct() {
        $this->model = new module_en_userMessages();
    }

    /**
     * api�� en.userMessages.getAllMsgs
     * function��get all messages of a user
     * call��CT_Api::post();
     * param��$uid��user id (must be assigned)
     */
    public function getAllMsgs() {
        $uid = core::getInput("uid");
        $data = $this->model->getAllMsgs($uid);
        if ($data) {
            //Exist
            core::outPut(1, $data);
        } else {
            //Not exist
            core::outPut(-1);
        }
    }
    
    /**
     * api�� en.userMessages.getSentMsgs
     * function��get messages sent of a user
     * call��CT_Api::post();
     * param��$uid��sender id (must be assigned)
     */
    public function getSentMsgs() {
        $uid = core::getInput("uid");
        $data = $this->model->getSentMsgs($uid);
        if ($data) {
            //Exist
            core::outPut(1, $data);
        } else {
            //Not exist
            core::outPut(-1);
        }
    }
    
    /**
     * api�� en.userMessages.getReceivedMsgs
     * function��get messages received of a user
     * call��CT_Api::post();
     * param��$uid��receiver id (must be assigned)
     */
    public function getReceivedMsgs() {
        $uid = core::getInput("uid");
        $data = $this->model->getReceivedMsgs($uid);
        if ($data) {
            //Exist
            core::outPut(1, $data);
        } else {
            //Not exist
            core::outPut(-1);
        }
    }

    /**
     * api�� en.userMessages.getMsgs
     * function��get messages with any conditions
     * call��CT_Api::getList($condition,$option,$fields);
     * param��$condition: [search condition(array)] $condition = array(��type��=>1,'id��=>2) or $condition = ��type=1 and id=2��
     *        $option: [format] array(��order��=>��id desc��,��offset��=>0,��limit��=>10,��group��=>�� id��); you can choose any key with any value, default is null
     *        $fields: fields used to be searched��default is *
     */
    public function getMsgs() {
        $where = core::getInput('where');
        $option = core::getInput('option');
        $fields = core::getInput('fields');
        $data = $this->model->getMsgs($where, $option, $fields);
        if ($data) {
            //Exist
            core::outPut(1, $data);
        } else {
            //Not exist
            core::outPut(-1);
        }
    }
    
    /**
     * api�� en.userMessages.getUnreadMsgs
     * function��get unread messages
     * call��CT_Api::post();
     * param��$uid��user id (must be assigned)
     * return: number of unread messages
     */
    public function getUnreadMsgs() {
        $uid = core::getInput("uid");
        $data = $this->model->getUnreadMsgs($uid);
        if ($data) {
            //Exist
            core::outPut(1, $data);
        } else {
            //Not exist
            core::outPut(-1);
        }
    }

    /**
     * api�� en.userMessages.sendMsg
     * function��send data
     * call��CT_Api::post();
     * param��$data: CT_Api::setParams($data);
     */
    public function sendMsg() {
        $data = core::getInput();
        $result = $this->model->sendMsg($data);
        if ($result) {
            core::outPut(1, $result);
        } else {
            core::outPut(-1);
        }
    }
    
    /**
     * api�� en.userMessages.markMsgs
     * function��mark msgs
     * call��CT_Api::post();
     * param��$data: CT_Api::setParams($data);
     *
     * Note: $mode must be assigned before $ids
     */
    public function markMsgs() {
        $data = core::getInput();
        $mode = $data['mode'];
        $ids = $data['id'];
        if (!$ids) {
        	$ids = array_slice($data, 2, -4);
        }
        $result = $this->model->markMsgs($ids, $mode);
        if ($result) {
            core::outPut(1, $result);
        } else {
            core::outPut(-1);
        }
    }
    
    /**
     * api�� en.userMessages.readMsg
     * function��set msgs as 'isRead' and can return the msg content
     * call��CT_Api::post();
     * param��$data: CT_Api::setParams($data);
     *
     * Note: $getMsg must be assigned before $ids
     */
    public function readMsg() {
        $data = core::getInput();
        $getMsg = $data['getMsg'];
        $ids = $data['id'];
        if (!$ids) {
	        if ($getMsg) $ids = array_slice($data, 2, -4);
	        else $ids = array_slice($data, 1, -4);
	    }
	    $result = $this->model->readMsg($ids, $getMsg);
	    
        if ($result) {
            core::outPut(1, $result);
        } else {
            core::outPut(-1);
        }
    }

    /**
     * api�� en.userMessages.deleteMsgs
     * function��delete messages
     * call��CT_Api::post();
     * param��$ids: message ids array() (must be assigned)
     *
     * Note: $mode and $uid must be assigned before $ids
     */
    public function deleteMsgs() {
        $data = core::getInput();
        $mode = $data['mode'];
        $uid = $data['uid'];
        $ids = $data['id'];
        if (!$ids) {
        	if ($uid) $ids = array_slice($data, 3, -4);
        	else $ids = array_slice($data, 2, -4);
        }
        $result = $this->model->deleteMsgs($ids, $mode, $uid);
        if ($result) {
            //Exist
            core::outPut(1, $result);
        } else {
            //Not exist
            core::outPut(-1);
        }
    }
}

?>

