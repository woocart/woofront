<?php
/**
 * Tests the hamburger menu.
 *
 * @package cartfront
 */

use Niteo\WooCart\CartFront\Hamburger_Menu;
use PHPUnit\Framework\TestCase;

class HamburgerMenuTest extends TestCase {

	function setUp() {
		\WP_Mock::setUsePatchwork( true );
		\WP_Mock::setUp();
	}

	function tearDown() {
		$this->addToAssertionCount(
			\Mockery::getContainer()->mockery_getExpectationCount()
		);

		\WP_Mock::tearDown();
	}

	/**
	 * @covers \Niteo\WooCart\CartFront\Hamburger_Menu::__construct
	 */	 
	public function testConstructor() {
		\WP_Mock::userFunction(
			'get_theme_mod', [
				'args' 	 => [ 'cf_hm_enable' ],
				'return' => true
			]
		);

		$menu = new Hamburger_Menu();

		\WP_Mock::expectActionAdded( 'wp_enqueue_scripts', [ $menu, 'add_styles' ], PHP_INT_MAX );
		\WP_Mock::expectActionAdded( 'customize_register', [ $menu, 'customize_register' ] );
		\WP_Mock::expectFilterAdded( 'body_class', [ $menu, 'body_class' ] );

        $menu->__construct();
		\WP_Mock::assertHooksAdded();
	}

	/**
	 * @covers \Niteo\WooCart\CartFront\Hamburger_Menu::__construct
	 * @covers \Niteo\WooCart\CartFront\Hamburger_Menu::add_styles
	 */
	public function testAddStyles() {
		\WP_Mock::userFunction(
			'get_theme_mod', [
				'args' 	 => [ 'cf_hm_enable' ],
				'return' => true
			]
		);
		\WP_Mock::userFunction(
			'sanitize_text_field', [
				'return' => true
			]
		);
		\WP_Mock::userFunction(
			'get_theme_mod', [
				'args' 		=> [
					'storefront_header_background_color',
					'#222222'
				],
				'return' 	=> true
			]
		);
		\WP_Mock::userFunction(
			'get_theme_mod', [
				'args' 		=> [
					'storefront_header_link_color',
					'#dddddd'
				],
				'return' 	=> true
			]
		);
		\WP_Mock::userFunction(
			'storefront_adjust_color_brightness', [
				'args' 		=> [
					'#dddddd',
					-100
				],
				'return' 	=> true
			]
		);

		\WP_Mock::userFunction(
			'wp_add_inline_style', [
				'args' => [
					'-public',
					true
				]
			]
		);

		$menu = new Hamburger_Menu();
		$menu->add_styles();
	}

	/**
	 * @covers \Niteo\WooCart\CartFront\Hamburger_Menu::__construct
	 * @covers \Niteo\WooCart\CartFront\Hamburger_Menu::customize_register
	 * @covers Niteo\WooCart\CartFront\autoloader
	 */
	public function testCustomizeRegister() {
		global $wp_customize;

		$wp_control 	= \Mockery::mock( 'WP_Customize_control' );
		$wp_customize 	= \Mockery::mock( 'WP_Customize_Manager' );
		$menu 			= new Hamburger_Menu();

		$wp_customize->allows()
					 ->add_section(
					 	'cf_hm_section',
					 	[
					 		'title' 	=> 'Hamburger Menu',
					 		'priority' 	=> 60
					 	]
					 )
					 ->andReturns( true );

		$wp_customize->allows()
					 ->add_setting(
					 	'cf_hm_enable',
					 	[
					 		'default' 			=> false,
					 		'sanitize_callback' => 'storefront_sanitize_checkbox',
					 		'transport' 		=> 'postMessage'
					 	]
					 )
					 ->andReturns( true );

		$wp_customize->allows()
					 ->add_control(
					 	'cf_hm_enable',
					 	[
						 	'label'         => 'Enable Hamburger Menu',
			                'description'   => 'Check this box to enable the Hamburger Menu for smaller size devices.',
			                'section'       => 'cf_hm_section',
			                'settings'      => 'cf_hm_enable',
			                'type'          => 'checkbox',
			                'priority'      => 10
			            ]
					 )
					 ->andReturns( true );

		$menu->customize_register( $wp_customize );
	}

	/**
	 * @covers \Niteo\WooCart\CartFront\Hamburger_Menu::__construct
	 * @covers \Niteo\WooCart\CartFront\Hamburger_Menu::body_class
	 */
	public function testBodyClass() {
		$menu = new Hamburger_Menu();
		$this->assertEquals(
			[ 'cartfront-hamburger-menu-active' ], $menu->body_class( [] )
		);
	}

}