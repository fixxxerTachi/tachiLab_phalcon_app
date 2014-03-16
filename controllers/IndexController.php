<?php

class IndexController extends ControllerBase
{

    public function indexAction()
    {
		$currentPage=(int)$_GET['page']?:1;
		$articles=Article::find(array(
			'order'=>'id desc',
			'cache'=>array('key'=>'my-cache'),
			'lifetime'=>60*60,
		));
		$news=Article::find(array(
			'order'=>'id desc',
			'limit'=>7
		));
		$this->view->tags=$this->_tagClouds($articles);
		/*
		$articles=Article::find(array(
			'order'=>'id desc'
		));
		*/
		if(!empty($_GET['search'])){
			$search=$_GET['search'];
			$articles=Article::find(array(
				'title like :search:',
				'bind'=>array('search'=>'%' . $search . '%'),
				'order'=>'id desc'
			));
		}
		if(!empty($_GET['article'])){
			$id=(int)$_GET['article'];
			$articles=Article::find(array(
				"id = :id:",
				"bind"=>array('id'=>$id)
			));
		}
		$paginator=new \Phalcon\Paginator\Adapter\Model(
			array(
				'data'=>$articles,
				'limit'=>3,
				'page'=>$currentPage
			)
		);
		$page=$paginator->getPaginate();
		//$this->view->cache(true);
		$this->view->page=$page;
		$this->view->search=$search;
		$this->view->news=$news;
    }

	public function testAction()
	{
		$conditions="title like :search: ";
		$articles=Article::find(array(
			$conditions,
			'bind'=>array('search'=>'%mysql%')
		));
		foreach($articles as $a){
			echo $a->title . '<br>';
		}
	}

	private function _tagClouds($articles)
	{
		/*
		$articles=Article::find(array(
			'cache'=>array('key'=>'tag-cache'),
			'lifetime'=>60*60,
		));
		*/
		$tags=array();
		foreach($articles as $a){
			if(!empty($a->tag)){
				$tag=str_replace(array(',','、','　'),' ',$a->tag);
				$tag=explode(' ',strtolower($tag));
				$tags=array_unique(array_merge($tags,$tag));
			}
		}
		sort($tags);
		return $tags;
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
				return $this->response->redirect('index/list/');
			}
		}
	}
	
	public function deleteAction($id=null)
	{
		$article=Article::find((int)$id);
		$article->delete();
		return $this->response->redirect('index/list/');
	}

}

