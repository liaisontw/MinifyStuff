<?php
require_once __DIR__ . '/../public/class-minifystuff-public.php';
//require_once __DIR__ . '../../../wp-load.php'; 


class Minify_Stuff_Test extends WP_UnitTestCase {
	
	//protected $rusty_factory;
	
	/**
	 * Unit test initialization. Makes sure database tables are created, etc.
	 */

	 public function setUp(): void {
		parent::setUp();

		//$this->rusty_factory = new Rusty_Inc_Org_Chart_Factory();
	}

	public function test_default_nothing() {
		
	}


}

