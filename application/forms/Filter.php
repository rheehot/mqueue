<?php

class Default_Form_Filter extends Zend_Form_SubForm
{	
	public function init()
	{
		// Set the method for the display form to GET
		$this->setMethod('get');
		
		$users = array();
		if (Default_Model_User::getCurrent())
		{
			$users = array(0 => _tr('<< me >>'));
		}
		
		foreach (Default_Model_UserMapper::fetchAll() as $user)
			$users[$user->id] = $user->nickname;
		
		$this->addElement('select', 'user', array(
			'multiOptions'   => $users,
			'label'	=> _tr('User :'),
			'class' => 'filterUser'
		));
		
		$status = array(-1 => _tr('<< rated >>'), 0 => _tr('<< no rated >>'), -2 => _tr('<< all >>'));
		$status = $status + Default_Model_Status::$ratings;
		
		$this->addElement('select', 'status', array(
			'multiOptions'   => $status,
			'label'	=> _tr('Rating :'),
			'class' => 'filterStatus',
		));
		
		
        // Add the filter element
        $this->addElement('text', 'title', array(
            'label'      => 'Title :',
        ));
		

		$this->setDecorators(array(
				'FormElements',
				array(array('row' => 'HtmlTag'), array('tag' => 'dl', 'class' => 'filter')),				
				));
	}
	

    /**
     * Override setDefaults to dynamically generate subforms
     * Will add a subform per day that is present in the defaults data.
     * (Be sure to provide a day key, even if no tasks exist.
     * @param array $defaults
     */
    public function setDefaults(array $defaults)
    {
		// Initialize session
		$session = new Zend_Session_Namespace();
		$name = $this->getName();
		
		// Find the correct filtered user, or default on current logged user
		$idUser = 0;
		if (isset($defaults[$name]['user']) && (integer)$defaults[$name]['user'] > 0)
		{
			$defaults[$name]['user'] = (integer)$defaults[$name]['user'];
		}
		elseif (isset($session->idUser) && (integer)$session->idUser > 0)
		{
			$defaults[$name]['user'] = (integer)$session->idUser;
		}
		else
		{
			$users = Default_Model_UserMapper::fetchAll();
			$firstUser = $users->current();
			$defaults[$name]['user'] = (integer)$firstUser->id;
		}
		

		// Get the filter for status, or default on all rated movies
		if (isset($defaults[$name]['status']) && (integer)$defaults[$name]['status'] >= -2 && (integer)$defaults[$name]['status'] <= 5)
		{
			$defaults[$name]['status'] = (integer)$defaults[$name]['status'];
		}
		else
		{
			$defaults[$name]['status'] = -1;
		}
		

		if (isset($defaults[$name]['title']) && trim($defaults[$name]['title']))
		{
			$defaults[$name]['title'] = trim($defaults[$name]['title']);
		}
		else
		{
			$defaults[$name]['title'] = '';
		}
        
        // set defaults, which will propagate to newly created subforms
        parent::setDefaults($defaults);
    }
	
}
