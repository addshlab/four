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
        $this->go_to( home_url() . '?p=10000000' );
        $this->assertTrue( is_404() );
    }
}
