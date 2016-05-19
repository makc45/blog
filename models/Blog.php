<?php

namespace app\models;
use yii\db\ActiveRecord;

class Blog extends ActiveRecord
{
     /**
     * @inheritdoc
     */
public function GetBlogs($getMonth,$getTags) {
        $blog = new Blog;
        $WMonth="";
        $WTag="";
        if(!empty($getMonth))
            {
                $FMonth=explode(",", $getMonth);
                foreach ($FMonth as $month)
                    {   
                        if(!empty($WMonth))$WMonth.=" or ";
                        $WMonth.=" month=".$month;
                    }
            }
        $BlogTags=array();
        if(!empty($getTags))
            {   
                $Ftag=explode(",", $getTags);
                
                $WTags="";
                foreach ($Ftag as $tag)
                    {   if(!empty($WTags))$WTags.=" or ";
                        
                        $WTags.=" id_tags=".$tag;
                    }
                $BlogTag= new Blog_tag;
                $BlogTags=$BlogTag->find()->where($WTags)->groupBy('id_blog')->asArray()->all();
                 if(count($BlogTags)>0){
                foreach ($BlogTags as $BlogId)
                    {
                        if(!empty($WTag))$WTag.=" or ";
                        $WTag.=" id=".$BlogId['id_blog'];
                        
                        
                    }
                 }
                 else   {
                     if(!empty($WTag))$WTag.=" or ";
                        $WTag.=" id=-1";
                 }
                
            }
            $where="";
            if(!empty($WMonth)&&!empty($WTag))
                {
                    $where="(".$WMonth.") AND (".$WTag.")";
                }
                else {$where=$WMonth.$WTag;}
                //echo $where;
        $blogMas=$blog->find()->where($where)->orderBy('month DESC, date DESC')->asArray()->all();
        
        return $blogMas;
    }
    
public function getUsers()
    {
        $user= new User;
        $userMas=$user->find()->select("id,username")->asArray()->all();
        $users=array();
        foreach($userMas as $userEl)
            {
                $users[$userEl['id']]=$userEl['username'];
            }
        return $users;    
    
    }
public function GetMonth()
    {
    return array(1=>"Январь",2=>"Февраль",3=>"Март",4=>"Апрель",5=>"Май",6=>"Июнь",7=>"Июль",8=>"Август",9=>"Сентябрь",10=>"Октябрь",11=>"Ноябрь",12=>"Декабрь");
    }
public function GetTags()
    {
        $tag= new Tags;
        $tagsMas=$tag->find()->asArray()->orderBy('name_tag')->all();
        $tags=array();
        foreach($tagsMas as $tagsEl)
            {
                $tags[$tagsEl['id']]=$tagsEl['name_tag'];
            }
        return $tags;
    }
public function GetRating()
    {
        $Ratig= new Rating;
        $RatigMas=$Ratig->find()->asArray()->all();
        $ratig=array();
        foreach($RatigMas as $RatigsEl)
            {
                if (array_key_exists($RatigsEl['id_blog'], $ratig)) {
                    
                        $ratig[$RatigsEl['id_blog']]['summa']=$ratig[$RatigsEl['id_blog']]['summa']+$RatigsEl['rating'];
                        $ratig[$RatigsEl['id_blog']]['count']=$ratig[$RatigsEl['id_blog']]['count']+1;
                    }
                    else
                        {
                            //$ratig[$RatigsEl['id_blog']]=array();
                            $ratig[$RatigsEl['id_blog']]['summa']=$RatigsEl['rating'];
                            $ratig[$RatigsEl['id_blog']]['count']=1;
                        }
            }
        
        return $ratig;
    }
    
public function GetBlogTags()
    {
        $BlogTag= new Blog_tag;
        $BlogTags=$BlogTag->find()->asArray()->all();
        $BlogTagMas=array();
        foreach($BlogTags as $BlogTagEl)
            {
                if (array_key_exists($BlogTagEl['id_blog'], $BlogTagMas)) {
                        
                        array_push($BlogTagMas[$BlogTagEl['id_blog']], $BlogTagEl['id_tags']);
                    }
                    else
                        {
                            $BlogTagMas[$BlogTagEl['id_blog']]=array();
                            array_push($BlogTagMas[$BlogTagEl['id_blog']], $BlogTagEl['id_tags']);
                        }

                
            }
        return $BlogTagMas;
    }
    public function BlogTags() {
        $tags = new Tags;
        $TagsMas=$tags->find()->select("id,name_tag as name")->asArray()->orderBy('name')->all();
       // $array = get_object_vars($object);
        $rezMas="[";
        $i=0;
        foreach ($TagsMas as $tag)
            {   
                if($i!=0)$rezMas.=",";
                $i++;
                $rezMas.="{id:'".$tag["id"]."',name:'".$tag["name"]."'}";
            }
            $rezMas.="]";
            return $TagsMas;
    }
public function addBlog($data,$id){
    $rez=array();
    $rez['error']=0;
    if(!empty($data["header"])&&!empty($data["text"])&&!empty($data["month"])&&!empty($id))
    {   
        //$count = Blog::find()->where(['username' => $data["username"]])->count();
        
        
            $AddBlog = new Blog;
            
            $AddBlog->id_user=$id;
            $AddBlog->header=$data["header"];
            $AddBlog->text=$data["text"];
            $AddBlog->month=$data["month"];
            $AddBlog->date=time();
            //print_r($data['tags']);
            $AddBlog->save();
            $idBlog=$AddBlog->id;
            if(!empty($data['tags'])){
                $tags=$data['tags'];
                foreach ($tags as $tag)
                    {
                        if($tag['id']==-1)
                            {
                                $AddTags=new Tags;
                                $AddTags->name_tag=$tag['name'];
                                $AddTags->save();
                                $idTags=$AddTags->id;
                                $AddBlogTags = new Blog_tag;
                                $AddBlogTags->id_blog=$idBlog;
                                $AddBlogTags->id_tags=$idTags;
                                $AddBlogTags->save();
                            }
                            else{
                                $AddBlogTags = new Blog_tag;
                                $AddBlogTags->id_blog=$idBlog;
                                $AddBlogTags->id_tags=$tag['id'];
                                $AddBlogTags->save();
                            }
                    }
                
                
            }
    }
    else{
        $rez['error']=1;
        $rez['errorMess']='Не все данные заполнены';
    }
    echo json_encode($rez);
    
    
}

public function DelBlog($data)
    {
        $rez=array();
        $rez['error']=0;
        if(!empty($data["id"]))
            {
                $Blog= new Blog;
                $BlogDel=$Blog->findOne($data["id"]);
                $BlogDel->delete();
                
                $BlogTag= new Blog_tag;
                //$BlogTags=$BlogTag->find()->where("id_blog=".$data["id"])->asArray()->all();
                $BlogTag->deleteAll("id_blog=".$data["id"]);
                $Comment=new Comments;
                $Comment->deleteAll("id_blog=".$data["id"]);
                
                $Rating=new Rating;
                $Rating->deleteAll("id_blog=".$data["id"]);
            }
            else{
            $rez['error']=1;
            $rez['errorMess']='Не все данные заполнены';
        }
        echo json_encode($rez);
    }
    
    
    
public function addRating($data,$id){
    $rez=array();
    $rez['error']=0;
    if(!empty($data["rating"])&&!empty($data["id"]))
    {   
            $AddRating = new Rating;
            
            $AddRating->id_blog=$data["id"];
            $AddRating->id_user=$id;
            $AddRating->rating=$data["rating"];
            $AddRating->save();
    }
    else{
        $rez['error']=1;
        $rez['errorMess']='Не все данные заполнены';
    }
    echo json_encode($rez);
}
}
class Rating extends ActiveRecord {}
class Tags extends ActiveRecord {}
class Blog_tag extends ActiveRecord {}