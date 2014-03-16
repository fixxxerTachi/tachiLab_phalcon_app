<?php
use Phalcon\Mvc\Model\Behavior\Timestampable;
class Article extends \Phalcon\Mvc\Model
{
	public function initialize()
	{
		$this->setSource('articles');
		$this->addBehavior(new Timestampable(
			array(
				'beforeCreate'=>array(
					'field'=>array('created_at','updated_at'),
					'format'=>'Y-m-d H:i:s',
				)
			)
		));
	}
	
}
