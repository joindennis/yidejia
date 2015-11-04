<?php

/**
 * Module：en.facebook
 * Function: Facebook database management
 * Class：guojizhan API
 */
class api_en_facebook {

    private $model;

    public function __construct() {
        $this->model = new module_en_facebook();
    }
    
    /**
     * api： en.facebook.getById
     */
    public function getById() {
        $id = core::getInput("id");
        $data = $this->model->getById($id);
        if ($data) {
            //存在
            core::outPut(1, $data);
        } else {
            //不存在
            core::outPut(-1);
        }
    }

    /**
     * api： en.facebook.getByEmail
     */
    public function getByEmail() {
        $email = core::getInput("Email");
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
     * api： en.facebook.getList
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
     * api： en.facebook.save
     */
    public function save() {
        $data = core::getInput();
        $data = array_slice($data, 1, -4);
        $result = $this->model->save($data);
        if ($result) {
            core::outPut(1, $result);
        } else {
            core::outPut(-1);
        }
    }

    /**
     * api： en.facebook.delete
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
     * api： en.facebook.freeSample
     */
    public function freeSample() {
        $data = core::getInput();
        $result = $this->model->freeSample($data);
        if ($result) {
            core::outPut(1, $result);
        } else {
            core::outPut(-1);
        }
    }
    
    /**
     * api： en.facebook.getFreeSampleInfo
     */
    public function getFreeSampleInfo() {
    	$result = $this->model->getFreeSampleInfo();
    	if ($result) core::outPut(1, $result);
    	else core::outPut(-1);
    }

}

?>
