<?php
require_once __DIR__ . '/../public/class-minifystuff-public.php';
//require_once __DIR__ . '../../../wp-load.php'; 

class Minify_Stuff_Test extends WP_UnitTestCase {
		
	/**
	 * Unit test initialization. Makes sure database tables are created, etc.
	 */

	 public function setUp(): void {
		parent::setUp();

		$this->version = '1.0.0';
		$this->plugin_name = 'minifyStuff';
		$this->minify = new minifyStuff_Public($this->plugin_name, $this->version);
	}

	public function test_comment_remove() {

		$pre_buffer  = "<!-- This comment is for test -->";
		$post_buffer = "";

		$output = $this->minify->minify_stuff_output($pre_buffer);
		$this->assertEquals( $post_buffer, $output, 'html comment minify' );	
		
		
		$pre_buffer  = '<script>
						/*! This comment is for test */
						</script>';
		$post_buffer = '<script>
</script>';

		$output = $this->minify->minify_stuff_output($pre_buffer);
		$this->assertEquals( $post_buffer, $output, 'js script minify' );	

		$pre_buffer  = '<style>
						/* This comment is for test */
						</style>';
		$post_buffer = '<style></style>';

		$output = $this->minify->minify_stuff_output($pre_buffer);
		$this->assertEquals( $post_buffer, $output, 'css style minify' );	

	}


}

