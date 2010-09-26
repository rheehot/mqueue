<?php

class MovieController extends Zend_Controller_Action
{

	public function init()
	{
		/* Initialize action controller here */
	}

	public function indexAction()
	{
		$form = new Default_Form_Filters();
		$this->view->formFilter = $form;
		 
		// If want to clear the filter do so, otherwise try to validate it
		if ($this->_getParam('clear', false))
		{
			$filters = array();
		}
		else
		{
			$filters = $this->getRequest()->getParams();
		}
		$form->setDefaults($filters);

		$this->view->users = array();
		$filters = $form->getValues();
		foreach ($filters as $key => $filter)
		{
			if (!preg_match('/^filter\d+$/', $key))
				continue;
				
			$this->view->users [$filter['user']]= Default_Model_UserMapper::find($filter['user']);
		}
		
		//w($filters);	
		// Set up the paginator
		Zend_Paginator::setDefaultScrollingStyle('Elastic');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');
		$this->view->sort = $this->getRequest()->getParam('sort');
		$this->view->sortOrder = $this->getRequest()->getParam('sortOrder');
		$this->view->paginator = Zend_Paginator::factory(Default_Model_MovieMapper::getFilteredQuery($filters, $this->view->sort, $this->view->sortOrder));
		$this->view->paginator->setCurrentPageNumber($this->_getParam('page'));
		$this->view->paginator->setItemCountPerPage($this->_getParam('perPage', 25));
	}

	public function viewAction()
	{
		if ($this->getRequest()->getParam('idMovie'))
		{
			$this->view->movie = Default_Model_MovieMapper::find($this->getRequest()->getParam('idMovie'));
		}

		$this->view->headLink()->appendAlternate($this->view->serverUrl() . $this->view->url(array('controller' => 'feed', 'action' => 'index', 'movie' => $this->view->movie->id, 'format' => 'atom'), null, true), 'application/rss+xml', $this->view->translate('mQueue - Activity for %s', array($this->view->movie->getTitle())));

		if (!$this->view->movie)
		{
			throw new Exception($this->view->translate('Movie not found'));
		}

		$this->view->users = Default_Model_UserMapper::fetchAll();
	}

	public function addAction()
	{
		$request = $this->getRequest();
		$form    = new Default_Form_Movie();

		if ($this->getRequest()->isPost())
		{
			if ($form->isValid($request->getPost()))
			{
				$values = $form->getValues();
				$movie = Default_Model_MovieMapper::find(Default_Model_Movie::extractId($values['id']));
				if (!$movie)
				{
					$movie = Default_Model_MovieMapper::getDbTable()->createRow();
					$movie->setId($values['id']);
					$movie->save();
				}

				$this->view->movies = array($movie);
				//$this->_helper->FlashMessenger('We did something in the last request');
				//$this->view->messages = $this->_helper->FlashMessenger->getMessages();
			}
		}

		$this->view->form = $form;
	}

	public function importAction()
	{
		$request = $this->getRequest();
		$form    = new Default_Form_Import();
		$form->setDefaults(array('favoriteMinimum' => 9, 'excellentMinimum' => 7, 'okMinimum' => 5));
		

		if ($this->getRequest()->isPost())
		{
			if ($form->isValid($request->getPost()))
			{
				$values = $form->getValues();
				$page = file_get_contents($values['url']);
				
				$r = '|<a href="/title/tt(\d{7})/">.*</td>\s<td.*>(\d+(\.\d)*)</td>|U';
				preg_match_all($r, $page, $matches);
				
				$movies = array();
				for ($i = 0; $i < count($matches[1]); $i++)
				{
					$id = $matches[1][$i];
					$imdbRating = $matches[2][$i];
					
					$movie = Default_Model_MovieMapper::find($id);
					if (!$movie)
					{
						$movie = $mapper->getDbTable()->createRow();
						$movie->setId($id);
						$movie->save();
					}

					if ($imdbRating >= $values['favoriteMinimum'])
						$rating = Default_Model_Status::Favorite;
					elseif ($imdbRating >= $values['excellentMinimum'])
						$rating = Default_Model_Status::Excellent;
					elseif ($imdbRating >= $values['okMinimum'])
						$rating = Default_Model_Status::Ok;
					else
						$rating = Default_Model_Status::Bad;
						
					$movie->setStatus(Default_Model_User::getCurrent()->id, $rating);
					$movies []= $movie;
				}
				$this->view->movies = $movies;
			}
		}

		$this->view->form = $form;
	}


}




