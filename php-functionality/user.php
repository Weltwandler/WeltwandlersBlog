<?php
class User {
    // Properties
    public $role;
    public $userid;
    public $username;
    public $display_name;

    function __construct($role, $userid, $username, $display_name) {
        $this->role = $role;
        $this->userid = $userid;
        $this->username = $username;
        $this->display_name = $display_name;
    }

    function display() {
        $display_text = $this->display_name;
        if ($this->role == 2) {
            $display_text += ' <span class="role">Staff</span>';
        } elseif ($this->role === 3) {
            $display_text += ' <span class="role">Admin</span>';
        }
    }
}
?>