<?php
class ArticlesController extends ControllerBase
{
	public function initialize()
	{
		$this->view->disableLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
	}
	public function addAction()
	{
		if($this->request->isPost())
		{
			$title=$this->request->getPost('title');
			$contents=$this->request->getPost('contents');
			$tag=$this->request->getPost('tag');
			$article=new Article();
			$article->title=$title;
			$article->contents=$contents;
			$article->tag=$tag;
			if($article->create())
			{
				$this->response->redirect('index/index');
			}
		}
	}
	public function listAction()
	{
		$articles=Article::find(array(
			'order'=>'id desc'
		));
		$this->view->articles=$articles;
	}
	public function editAction($id=null)
	{
		$article=Article::findFirst((int)$id);
		$this->view->article=$article;
		if($this->request->isPost())
		{
			$article=Article::findFirst((int)$this->request->getPost('id'));
			$article->title=$this->request->getPost('title');
			$article->contents=$this->request->getPost('contents');
			$article->tag=$this->request->getPost('tag');
			if($article->save())
			{
				return $this->response->redirect('articles/list/');
			}
		}
	}
	public function deleteAction($id=null)
	{
		$article=Article::find((int)$id);
		$article->delete();
		return $this->response->redirect('articles/list/');
	}
	public function loginAction()
	{
		if($this->request->isPost())
		{
			$username=$this->request->getPost('username');
			$password=$this->request->getPost('password');
			$user=User::findFirst(array(
				"username=:username: and password=:password:",
				"bind"=>array(
					"username"=>$username,
					"password"=>$password
				)
			));
			if($user)
			{
				$this->session->set('username',$username);
				$this->flashMess->success('logined');
				return $this->response->redirect('articles/list');
			}else{
				$this->flashMess->error('incorrect username or password');
			}
		}
	}
	
	public function logoutAction()
	{
		$this->session->remove('username');
		$this->session->destroy();
		$this->flashMess->success('logout');
		return $this->response->redirect('articles/list');
	}

	public function beforeExecuteRoute($dispatcher)
	{
		$actionName=$dispatcher->getActionName();
		$aclLists=array('edit','add');
		if(in_array($actionName,$aclLists)){
			if(!$this->session->has('username')){
				$this->response->redirect('articles/login');
				return false;
			}
		}
	}
}
