<?php

namespace app\models;
use yii\db\ActiveRecord;

class Comments extends ActiveRecord
{

public function getComments()
    {
        $Comments = new Comments;
        $com=$Comments->find()->asArray()->orderBy('id_blog, date')->all();
        $comments=array();
        foreach($com as $comEl)
            {
               
                if (array_key_exists($comEl['id_blog'], $comments)) {
                        $el=array();
                        $el['id']=$comEl['id'];
                        $el['id_user']=$comEl['id_user'];
                        $el['text_comment']=$comEl['text_comment'];
                        array_push($comments[$comEl['id_blog']], $el);
                    }
                    else
                        {
                            $comments[$comEl['id_blog']]=array();
                            $el=array();
                            $el['id']=$comEl['id'];
                            $el['id_user']=$comEl['id_user'];
                            $el['text_comment']=$comEl['text_comment'];
                            array_push($comments[$comEl['id_blog']], $el);
                        }
            }
        return $comments;    
    
    }
public function addComment($data,$id){
    $rez=array();
    $rez['error']=0;
    if(!empty($data["comments"])&&!empty($data["id"]))
    {   
        //$count = Blog::find()->where(['username' => $data["username"]])->count();
        
        
            $AddComment = new Comments;
            
            $AddComment->id_user=$id;
            $AddComment->id_blog=$data["id"];
            $AddComment->text_comment=$data["comments"];
            $AddComment->date=time();
            $AddComment->save();

    }
    else{
        $rez['error']=1;
        $rez['errorMess']='Не все данные заполнены';
    }
    echo json_encode($rez);
    
    
}

public function DelComment($data)
    {
        if(!empty($data["id"]))
            {
                $Comments= new Comments;
                $CommentDel=$Comments->findOne($data["id"]);
                $CommentDel->delete();
                
               
            }        
    }
}