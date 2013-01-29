<?php

require_once 'PHPUnit/Framework/TestCase.php';

class CssControllerTest extends AbstractControllerTestCase
{

	public function testIndexAction()
	{
        $params = array('action' => 'gravatar.css', 'controller' => 'css', 'module' => 'default');
        $url = $this->url($this->urlizeOptions($params));
        $this->dispatch($url);
		
        // assertions
        $this->assertModule($params['module']);
        $this->assertController($params['controller']);
        $this->assertAction($params['action']);
		
		$this->assertHeaderContains('Content-Type', 'text/css');
		$this->assertContentContains('span.gravatar');
		$this->assertContentContains('span.gravatar.big');
	}

}
