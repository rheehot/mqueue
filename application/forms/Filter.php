<?php

class Default_Form_Filter extends Zend_Form
{
	public function init()
	{
		// Set the method for the display form to GET
		$this->setMethod('get');

		$users = array(0 => _tr('<< me >>'));
		$mapperUser = new Default_Model_UserMapper();
		foreach ($mapperUser->fetchAll() as $user)
			$users[$user->id] = $user->nickname;
		
		$this->addElement('select', 'filterUser', array(
			'multiOptions'   => $users,
			'label'	=> _tr('User :'),
		));

		// Add the submit button
		$this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => _tr('Apply'),
		));

		// Add the submit button
		$this->addElement('submit', 'clear', array(
            'ignore'   => true,
            'label'    => _tr('Clear'),
		));
		
		
		$this->addDisplayGroup(array('filterUser', 'submit','clear'), 'filter', array('legend' => _tr('Filter')));
	}
}
