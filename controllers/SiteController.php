<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\User;
use app\models\Blog;
use app\models\Comments;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
       
        return $this->render('index');
    }

    public function actionLogin()
    {
        $rez=array();
        $rez['error']=0;
        if (!Yii::$app->user->isGuest) {
            
            $rez['error']=1;
            $rez['errorMess']='Вы уже авторизованны';
            return json_encode($rez);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            
            $rez['text'] = $this->actionViewauth();
            
            return json_encode($rez);
        }
            $rez['error']=1;
            $rez['errorMess']='Неверный логин или пароль';
            
            return json_encode($rez);
    }
    public function actionViewauth(){
        return $this->getView()->render('authentication', $params=[], $this);
    
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->actionViewauth();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionAbout()
    {
        return $this->render('about');
    }
    public function actionUser()
    {
        $method=Yii::$app->request->getMethod();
        switch($method)
            {
                case "POST": 
                    $model = new User();
                    $model->addUser(Yii::$app->request->post());
                    break;
            }
    }
    
    public function actionComments()
    {
        $method=Yii::$app->request->getMethod();
        $model = new Comments();
        switch($method)
            {
            case "GET":
                echo 111;
                break;
            case "POST":
                
                if(Yii::$app->user->isGuest)
                    { $id=-1;}
                else { $id=Yii::$app->user->identity->id;}
                
                $model->addComment(Yii::$app->request->post(),$id);
                break;
            case "DELETE":
                    $model->DelComment(Yii::$app->request->post());
                    
                    break;
            }
    }
    
    public function actionRating()
    {
        $method=Yii::$app->request->getMethod();
        $model = new Blog();
        switch($method)
            {
                case "POST": 
                    if(Yii::$app->user->isGuest)
                        { $id=-1;}
                    else { $id=Yii::$app->user->identity->id;}
                    
                    $model->addRating(Yii::$app->request->post(),$id);
                    break;
            }
    }
    public function actionBlog()
    {
        $method=Yii::$app->request->getMethod();
        $model = new Blog();
        switch($method)
            {
                case "GET": 
                    
                    $get=Yii::$app->request->get();
                    $getMonth="";
                    $getTags="";
                    if(!empty($get['month']))
                        {
                            $getMonth=$get['month'];
                        }
                    if(!empty($get['tags']))
                        {
                            $getTags=$get['tags'];
                        }
                            $Blog=$model->GetBlogs($getMonth,$getTags);
                            
                            $rez=array();
                            $rez['tags']=$model->BlogTags();
                            $modelComments = new Comments();
                            $comments=$modelComments->getComments();
                            $Users=$model->GetUsers();
                            $Month=$model->GetMonth();
                            $Tags=$model->GetTags();
                            $Rating=$model->GetRating();
                            $BlogTag=$model->GetBlogTags();
                            $rez['text'] = $this->getView()->render('BlogView/BlogIndex', 
                                    $params=[
                                        "blog"=>$Blog,
                                        "users"=>$Users,
                                        "month"=>$Month,
                                        "tags"=>$Tags,
                                        "BlogTag"=>$BlogTag,
                                        "Comments"=>$comments,
                                        "Rating"=>$Rating
                                    ], $this);
                            echo json_encode($rez);
                       
                    break;
                case "POST":
                    $id=Yii::$app->user->identity->id;
                    
                    $model->addBlog(Yii::$app->request->post(),$id);
                    
                    break;
                case "DELETE":
                    $model->DelBlog(Yii::$app->request->post());
                    
                    break;
            }
    }

}
