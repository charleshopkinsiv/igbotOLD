<?php



namespace igbot\user;



class IgUserMapper {

    private $db, $select;

    public function __construct() {

        $this->db = \web\data\Db::loadDb();

        $this->select = "SELECT * FROM users ";

        $this->where = "WHERE 1=1 ";

    }

    public function insert(IgUser $user) {

        $sql = "INSERT IGNORE INTO users 
                SET username = '" . $user->get('username') . "',
                    suspect = '" . $user->get('suspect') . "'";

        if($this->db->query($sql)->execute()) {

            $user->set('id', $this->db->lastId());

            return $user;

        }

    }


    public function select() {

        $sql = $this->select . $this->where;

        $USERS = $this->db->query($sql)->resultSet();

        return $USERS;

    }


    public function where($stmt) {

        $this->where .= "AND " . $stmt . " ";

    }


    public function update($USERS, $col, $val) {

        if(!is_array($USERS)) $USERS = [$USERS];

        $sql = "UPDATE users 
                SET " . $col . " = '" . $val . "' 
                WHERE username IN ('" . implode("','", $USERS) . "')"; 

        return $this->db->query($sql)->execute();

    }

}