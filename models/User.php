<?php

namespace app\models;
use yii\db\ActiveRecord;

class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    public $id;

    public $authKey;
    public $accessToken;

    public static function findIdentity($id)
    {   
        $users=self::GetUsersAll();
        return isset($users[$id]) ? new static($users[$id]) : null;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
       $users=self::GetUsersAll();
        foreach ($users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }
        return null;
    }
    
    public static function findByUsername($username)
    {        
        $users=self::GetUsersAll();
        foreach ($users as $user) {
            if (strcasecmp($user['username'], $username) === 0) {
                return new static($user);
            }
        }
        return null;
    }
    
    public static function GetUsersAll(){
        $usersMas=User::find()->asArray()->all();
        $users=array();
        foreach ($usersMas as $user){
            $users[$user["id"]]["id"]=$user["id"];
            $users[$user["id"]]["username"]=$user["username"];
            $users[$user["id"]]["password"]=$user["password"];
        }
        return $users;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getAuthKey()
    {
        return $this->authKey;
    }
    
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }
    
    public function validatePassword($password)
    {
        return $this->password === $password;
    }
    
    public function addUser($data){
        $rez=array();
        $rez['error']=0;
        if(!empty($data["username"])&&!empty($data["password"]))
        {   
            $count = User::find()->where(['username' => $data["username"]])->count();
            if($count>0){
                $rez['error']=1;
                $rez['errorMess']='Такой логин уже существует';
            }
            else{
                $addUser = new User;
                $addUser->username=$data["username"];
                $addUser->password=$data["password"];
                $addUser->save();
            }
        }
        else{
            $rez['error']=1;
            $rez['errorMess']='Не все данные заполнены';
        }
        echo json_encode($rez);
    }
}
