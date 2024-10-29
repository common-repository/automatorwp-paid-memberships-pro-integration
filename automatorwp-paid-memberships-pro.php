<?php
/**
 * Plugin Name:           AutomatorWP - Paid Memberships Pro integration
 * Plugin URI:            https://wordpress.org/plugins/automatorwp-paid-memberships-pro-integration/
 * Description:           Connect AutomatorWP with Paid Memberships Pro.
 * Version:               1.0.3
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-paid-memberships-pro-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.9
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Paid_Memberships_Pro
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Paid_Memberships_Pro_Integration {

    /**
     * @var         AutomatorWP_Paid_Memberships_Pro_Integration $instance The one true AutomatorWP_Paid_Memberships_Pro_Integration
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Paid_Memberships_Pro_Integration self::$instance The one true AutomatorWP_Paid_Memberships_Pro_Integration
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Paid_Memberships_Pro_Integration();
            self::$instance->constants();
            self::$instance->includes();
            self::$instance->hooks();
            self::$instance->load_textdomain();
        }

        return self::$instance;
    }

    /**
     * Setup plugin constants
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function constants() {
        // Plugin version
        define( 'AUTOMATORWP_PAID_MEMBERSHIPS_PRO_VER', '1.0.3' );

        // Plugin file
        define( 'AUTOMATORWP_PAID_MEMBERSHIPS_PRO_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_PAID_MEMBERSHIPS_PRO_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_PAID_MEMBERSHIPS_PRO_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include plugin files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function includes() {

        if( $this->meets_requirements() && ! $this->pro_installed() ) {

            // Includes
            require_once AUTOMATORWP_PAID_MEMBERSHIPS_PRO_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_PAID_MEMBERSHIPS_PRO_DIR . 'includes/functions.php';

            // Triggers
            require_once AUTOMATORWP_PAID_MEMBERSHIPS_PRO_DIR . 'includes/triggers/purchase-membership.php';
            require_once AUTOMATORWP_PAID_MEMBERSHIPS_PRO_DIR . 'includes/triggers/cancel-subscription.php';
            require_once AUTOMATORWP_PAID_MEMBERSHIPS_PRO_DIR . 'includes/triggers/subscription-expired.php';

            // Actions
            require_once AUTOMATORWP_PAID_MEMBERSHIPS_PRO_DIR . 'includes/actions/add-user-membership.php';

        }
    }

    /**
     * Setup plugin hooks
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function hooks() {

        add_action( 'automatorwp_init', array( $this, 'register_integration' ) );

        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
    }

    /**
     * Registers this integration
     *
     * @since 1.0.0
     */
    function register_integration() {

        automatorwp_register_integration( 'paid_memberships_pro', array(
            'label' => 'Paid Memberships Pro',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/paid-memberships-pro.svg',
        ) );

    }

    /**
     * Plugin admin notices.
     *
     * @since  1.0.0
     */
    public function admin_notices() {

        if ( ! $this->meets_requirements() && ! defined( 'AUTOMATORWP_ADMIN_NOTICES' ) ) : ?>

            <div id="message" class="notice notice-error is-dismissible">
                <p>
                    <?php printf(
                        __( 'AutomatorWP - Paid Memberships Pro requires %s and %s in order to work. Please install and activate them.', 'automatorwp-paid-memberships-pro-integration' ),
                        '<a href="https://wordpress.org/plugins/automatorwp/" target="_blank">AutomatorWP</a>',
                        '<a href="https://wordpress.org/plugins/paid-memberships-pro/" target="_blank">Paid Memberships Pro</a>'
                    ); ?>
                </p>
            </div>

            <?php define( 'AUTOMATORWP_ADMIN_NOTICES', true ); ?>

        <?php elseif ( $this->pro_installed() && ! defined( 'AUTOMATORWP_ADMIN_NOTICES' ) ) : ?>

            <div id="message" class="notice notice-error is-dismissible">
                <p>
                    <?php echo __( 'You can uninstall AutomatorWP - Paid Memberships Pro Integration because you already have the pro version installed and includes all the features of the free version.', 'automatorwp-paid-memberships-pro-integration' ); ?>
                </p>
            </div>

            <?php define( 'AUTOMATORWP_ADMIN_NOTICES', true ); ?>

        <?php endif;

    }

    /**
     * Check if there are all plugin requirements
     *
     * @since  1.0.0
     *
     * @return bool True if installation meets all requirements
     */
    private function meets_requirements() {

        if ( ! class_exists( 'AutomatorWP' ) ) {
            return false;
        }

        if ( ! function_exists( 'pmpro_init' ) ) {
            return false;
        }

        return true;

    }

    /**
     * Check if the pro version of this integration is installed
     *
     * @since  1.0.0
     *
     * @return bool True if pro version installed
     */
    private function pro_installed() {

        if ( ! class_exists( 'AutomatorWP_Paid_Memberships_Pro' ) ) {
            return false;
        }

        return true;

    }

    /**
     * Internationalization
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function load_textdomain() {

        // Set filter for language directory
        $lang_dir = AUTOMATORWP_PAID_MEMBERSHIPS_PRO_DIR . '/languages/';
        $lang_dir = apply_filters( 'automatorwp_paid_memberships_pro_languages_directory', $lang_dir );

        // Traditional WordPress plugin locale filter
        $locale = apply_filters( 'plugin_locale', get_locale(), 'automatorwp-paid-memberships-pro-integration' );
        $mofile = sprintf( '%1$s-%2$s.mo', 'automatorwp-paid-memberships-pro-integration', $locale );

        // Setup paths to current locale file
        $mofile_local   = $lang_dir . $mofile;
        $mofile_global  = WP_LANG_DIR . '/automatorwp-paid-memberships-pro-integration/' . $mofile;

        if( file_exists( $mofile_global ) ) {
            // Look in global /wp-content/languages/automatorwp-paid-memberships-pro-integration/ folder
            load_textdomain( 'automatorwp-paid-memberships-pro-integration', $mofile_global );
        } elseif( file_exists( $mofile_local ) ) {
            // Look in local /wp-content/plugins/automatorwp-paid-memberships-pro-integration/languages/ folder
            load_textdomain( 'automatorwp-paid-memberships-pro-integration', $mofile_local );
        } else {
            // Load the default language files
            load_plugin_textdomain( 'automatorwp-paid-memberships-pro-integration', false, $lang_dir );
        }

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Paid_Memberships_Pro_Integration instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Paid_Memberships_Pro_Integration The one true AutomatorWP_Paid_Memberships_Pro_Integration
 */
function AutomatorWP_Paid_Memberships_Pro_Integration() {
    return AutomatorWP_Paid_Memberships_Pro_Integration::instance();
}
add_action( 'plugins_loaded', 'AutomatorWP_Paid_Memberships_Pro_Integration' );
