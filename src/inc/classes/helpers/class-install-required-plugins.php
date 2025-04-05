<?php
/**
 * Installing required plugins
 *
 * @package omnis_base
 * @since 4.4.0
 */

namespace Omnis\src\inc\classes\helpers;

// Include WordPress core files.
use function Omnis\Inc\Classes\Helpers\activate_plugin;
use function Omnis\Inc\Classes\Helpers\add_action;
use function Omnis\Inc\Classes\Helpers\get_plugins;
use function Omnis\Inc\Classes\Helpers\is_plugin_active;
use function Omnis\Inc\Classes\Helpers\is_wp_error;
use function Omnis\Inc\Classes\Helpers\plugins_api;
use function Omnis\Inc\Classes\Helpers\trailingslashit;
use const Omnis\Inc\Classes\Helpers\ABSPATH;

require_once( ABSPATH . 'wp-load.php' );
require_once( ABSPATH . 'wp-includes/pluggable.php' );
require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
require_once( ABSPATH . 'wp-admin/includes/file.php' );
require_once( ABSPATH . 'wp-admin/includes/misc.php' );
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

/**
 * Helper class to install required plugins for the theme.
 */
if ( ! class_exists( 'Install_Required_Plugins' ) ) {

    /**
     * Class for Installing plugins.
     */
    class Install_Required_Plugins {

        /**
         * Holds the singleton instance of this class.
         *
         * @var Install_Required_Plugins
         */
        private static Install_Required_Plugins $instance;

        /**
         * Constructor method to initialize the class
         */
        public function __construct() {
            add_action( 'init', array( $this, 'activate_required_plugins' ) );
        }

        /**
         * Get an instance of the class
         *
         * @return Install_Required_Plugins
         */
        public static function get_instance(): Install_Required_Plugins {
            if ( ! isset( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Activate required plugins upon initialization
         */
        public function activate_required_plugins(): void {
            if ( ! function_exists( 'get_plugins' ) ) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            $plugins = $this->get_config( 'must-have-plugins' );
            foreach ( $plugins as $plugin ) {
                $error = $this->activate_plugin( $plugin );
                if ( ! empty( $error ) ) {
                    error_log( $error ); // Log errors instead of echoing them.
                }
            }
        }

        /**
         * Activate a plugin
         *
         * @param string $plugin The plugin file path.
         *
         * @return string|null Error message if activation fails
         */
        public function activate_plugin( string $plugin ): ?string {
            if ( ! function_exists( 'is_plugin_active' ) ) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            $plugin_mainfile = trailingslashit( WP_PLUGIN_DIR ) . $plugin;

            if ( $this->is_plugin_active( $plugin ) ) {
                return '';
            }

            if ( ! $this->is_plugin_installed( $plugin ) ) {
                if ( isset( $this->get_local_plugins()[ $plugin ] ) ) {
                    $error = $this->install_plugin_from_archive( $this->get_local_plugins()[ $plugin ] );
                } else {
                    $error = $this->install_plugin_from_wp( $plugin );
                }

                if ( ! empty( $error ) ) {
                    return $error;
                }
            }

            if ( ! $this->is_plugin_installed( $plugin ) ) {
                return 'Error: Plugin could not be installed (' . $plugin . ').<br>';
            }

            $error = activate_plugin( $plugin_mainfile );
            if ( is_wp_error( $error ) ) {
                return 'Error: Plugin has not been activated (' . $plugin . ').<br>';
            }

            return 'Success: Plugin installed (' . $plugin . ').<br>';
        }

        /**
         * Check if a plugin is installed.
         *
         * @param string $plugin The plugin file path.
         *
         * @return bool Whether the plugin is installed or not.
         */
        public function is_plugin_installed( string $plugin ): bool {
            if ( ! function_exists( 'get_plugins' ) ) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            $plugins = get_plugins( '/' . $this->get_plugin_dir( $plugin ) );
            return ! empty( $plugins );
        }

        /**
         * Check if a plugin is active
         *
         * @param string $plugin The plugin file path.
         *
         * @return bool Whether the plugin is active or not
         */
        public function is_plugin_active( string $plugin ): bool {
            if ( ! function_exists( 'is_plugin_active' ) ) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            return is_plugin_active( $plugin );
        }

        /**
         * Get the plugin directory
         *
         * @param string $plugin The plugin file path.
         *
         * @return string The plugin directory
         */
        public function get_plugin_dir( string $plugin ): string {
            $chunks = explode( '/', $plugin );
            return is_array( $chunks ) ? $chunks[0] : $chunks;
        }

        /**
         * Install a plugin from the WordPress repository
         *
         * @param string $plugin_slug The plugin slug.
         *
         * @return string|null Error message if installation fails
         */
        public function install_plugin_from_wp( string $plugin_slug ): ?string {
            include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

            $api = plugins_api(
                'plugin_information',
                array(
                    'slug' => $this->get_plugin_dir( $plugin_slug ),
                    'fields' => array( 'sections' => false ),
                )
            );

            if ( is_wp_error( $api ) ) {
                return 'Error: Plugin information could not be retrieved (' . $plugin_slug . ').<br>';
            }

            $upgrader = new \Plugin_Upgrader( new \WP_Upgrader_Skin() );
            $install_result = $upgrader->install( $api->download_link );

            if ( is_wp_error( $install_result ) ) {
                return $install_result->get_error_message();
            }

            return null;
        }

        /**
         * Install a plugin from an archive
         *
         * @param string $plugin_archive_path The path to the plugin archive.
         *
         * @return string|null Error message if installation fails
         */
        public function install_plugin_from_archive( string $plugin_archive_path ): ?string {
            require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

            $upgrader = new \Plugin_Upgrader( new \WP_Upgrader_Skin() );

            $install_result = $upgrader->install( $plugin_archive_path );

            if ( is_wp_error( $install_result ) ) {
                return $install_result->get_error_message();
            }

            return null;
        }

        /**
         * Get configuration data based on context
         *
         * @param string $context The context for which configuration data is required.
         * @return array Configuration data.
         */
        public function get_config( $context ) {
            if ( 'must-have-plugins' === $context ) {
                return array(
                    'contact-form-7/wp-contact-form-7.php',
                    'advanced-cf7-db/advanced-cf7-db.php',
                    'classic-editor/classic-editor.php',
                    'classic-widgets/classic-widgets.php',
                    'svg-support/svg-support.php',
                    'acf-theme-code-pro/acf_theme_code_pro.php',
                    'all-in-one-wp-migration/all-in-one-wp-migration.php',
                    'all-in-one-wp-migration-unlimited-extension/all-in-one-wp-migration-unlimited-extension.php',
                );
            }
        }

        /**
         * Get the list of local plugins to be installed from archives.
         *
         * @return array List of local plugins.
         */
        public function get_local_plugins() {
            return array(
                'acf-theme-code-pro/acf_theme_code_pro.php' => THEME_DIR_PATH . '/plugins/acf-theme-code-pro.zip',
                'all-in-one-wp-migration/all-in-one-wp-migration.php' => THEME_DIR_PATH . '/plugins/all-in-one-wp-migration.zip',
                'all-in-one-wp-migration-unlimited-extension/all-in-one-wp-migration-unlimited-extension.php' => THEME_DIR_PATH . '/plugins/all-in-one-wp-migration-unlimited-extension.zip',
            );
        }
    }

    // Initialize the Install_Required_Plugins class.
    Install_Required_Plugins::get_instance();
}



/**
 * Function to output variable data in a preformatted manner for debugging.
 *
 * @param mixed $data The data to be output.
 */
function var_dump_pre( mixed $data ): void {
    echo '<pre style="direction: ltr; text-align: left">';
    var_dump( $data );
    echo '</pre>';
}
