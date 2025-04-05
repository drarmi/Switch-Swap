<?php
/**
 * Analytics settings functionality for the theme.
 *
 * @package omnis_base
 * @since 4.4.0
 */

/**
 *  This class handles the registration of analytics code fields using ACF and renders the code
 *  in the appropriate sections of the theme.
 */
class Analytics_Settings {

    /**
     * Initialize hooks and field registration.
     */
    public function __construct() {
        add_action( 'acf/init', array( $this, 'register_analytics_fields' ) );
        add_action( 'omnis_before_close_head_tag', array( $this, 'render_head_section_codes' ), 100 );
        add_action( 'omnis_after_open_body_tag', array( $this, 'render_body_section_codes' ), 100 );
    }

    /**
     * Register ACF fields for analytics codes if ACF is available.
     *
     * @return void
     */
    public function register_analytics_fields(): void {
        if ( function_exists( 'acf_add_local_field_group' ) ) {
            acf_add_local_field_group(
                array(
                    'key' => 'group_662226a068db1',
                    'title' => 'Analytics Settings',
                    'fields' => array(
                        array(
                            'key' => 'field_662226a1bf356',
                            'label' => 'Codes for HEAD section',
                            'name' => 'codes_for_head_section',
                            'aria-label' => '',
                            'type' => 'textarea',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'maxlength' => '',
                            'rows' => '',
                            'placeholder' => '',
                            'new_lines' => '',
                        ),
                        array(
                            'key' => 'field_662226c9bf357',
                            'label' => 'Codes for AFTER BODY OPEN TAG section',
                            'name' => 'codes_for_after_body_open_tag_section',
                            'aria-label' => '',
                            'type' => 'textarea',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'maxlength' => '',
                            'rows' => '',
                            'placeholder' => '',
                            'new_lines' => '',
                        ),
                    ),
                    'location' => array(
                        array(
                            array(
                                'param' => 'options_page',
                                'operator' => '==',
                                'value' => 'theme-options',
                            ),
                        ),
                    ),
                    'menu_order' => 0,
                    'position' => 'normal',
                    'style' => 'default',
                    'label_placement' => 'top',
                    'instruction_placement' => 'label',
                    'hide_on_screen' => '',
                    'active' => true,
                    'description' => '',
                    'show_in_rest' => 0,
                )
            );
        }
    }

    /**
     * Render the content for the HEAD section if available in the theme settings.
     *
     * @return void
     */
    public function render_head_section_codes(): void {
        if ( function_exists( 'get_field' ) ) {
            $head_codes = get_field( 'codes_for_head_section', 'theme-options' );
            if ( $head_codes ) {
                echo do_shortcode( $head_codes );
            }
        }
    }

    /**
     * Render the content for the AFTER BODY OPEN TAG section if available in the theme settings.
     *
     * @return void
     */
    public function render_body_section_codes(): void {
        if ( function_exists( 'get_field' ) ) {
            $body_codes = get_field( 'codes_for_after_body_open_tag_section', 'theme-options' );
            if ( $body_codes ) {
                echo do_shortcode( $body_codes );
            }
        }
    }
}

// Initialize the Analytics_Settings class.
new Analytics_Settings();
