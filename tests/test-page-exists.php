<?php
/**
 * Class SampleTest
 *
 * @package Four
 */

/**
 * Sample test case.
 */
class PageExists extends WP_UnitTestCase {

    /**
     * @test
     */
    public function test_is_404() {
        $this->go_to( home_url() . '?p=99999999' );
        $this->assertTrue( is_404() );
    }

    public function test_post_content() {
        global $post;
        $post_id = $this->factory->post->create( array( 'post_content' => 'test content' ) );
        $post = get_post( $post_id );
        setup_postdata( $post );

        $content2 = '<p>test content</p>' . PHP_EOL;
        $this->expectOutputString( $content2 );
        the_content();
    }

}
