<?php

class User  {
    protected $pdo;

    function __construct($pdo){
        $this->pdo = $pdo;

    }
public function checkInput($variable){
    $variable = htmlspecialchars($variable);
    $variable = trim($variable);
    $variable = stripslashes($variable);
    return $variable;
}

    public function checkEmail($email_mobile){
        $stmt = $this->pdo->prepare("SELECT email FROM users WHERE email = :email");
        $stmt->bindParam(":email", $email_mobile, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->rowCount();
        if($count > 0){
            return true;
        }else{
            return false;
        }


    }
    

public function create($table, $fields=array()){
    $columns = implode(',', array_keys($fields));
    $values = ':'.implode(', :', array_keys($fields));
$sql = "INSERT INTO {$table}({$columns})VALUES ({$values})";

    if($stmt = $this->pdo->prepare($sql)){
        foreach($fields as $key => $data){
            $stmt->bindValue(':'.$key, $data);
        }
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }
}

    public function userIdByUsername($username){
        $stmt = $this->pdo->prepare('SELECT user_id FROM users WHERE userLink = :username');
        $stmt->bindparam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_OBJ);

        return $user->user_id;

    }
    public function userData($profileId){
       $stmt = $this->pdo->prepare("SELECT * FROM users LEFT JOIN profile ON users.user_id = profile.userId WHERE users.user_id = :user_id");
        $stmt->bindParam(':user_id', $profileId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function update($table, $user_id, $fields = array()){
        $columns = '';
        $i = 1;

        foreach($fields as $name => $value){
            $columns .= "{$name} = :{$name}";
            if($i < count($fields)){
                $columns .= ', ';
            }
            $i++;


        }
         $sql = "UPDATE {$table} SET {$columns} WHERE pid = {$user_id}";
        if($stmt = $this->pdo->prepare($sql)){
            foreach($fields as $key => $value){
                $stmt->bindValue(':'.$key, $value);
            }
        }
        $stmt->execute();

    }
}
?>
