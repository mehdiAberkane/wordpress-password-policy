<?php
/**
 * CLass Db for connect and interact with the bdd
 */
class Db {

    private $db;

    public function __construct($wpdb) {
        $this->db = $wpdb;
        echo $this->db->prefix;
    }

    public function set_config($arr) {
        $this->db->insert($this->db->prefix . 'options', $arr);
    }

    public function update_config($arr) {
        $result = $this->db->replace($this->db->prefix . 'options', $arr);
    }

    public function get_config() {
        $results = $this->db->get_results( "SELECT * FROM {$this->db->prefix}options WHERE option_name = '_password_policy_config'", OBJECT );

        return get_object_vars(json_decode($results[0]->option_value));
    }

    public function drop_config() {
        $result = $this->db->delete($this->db->prefix . 'options', array('option_name' => '_password_policy_config'));
    }
}
