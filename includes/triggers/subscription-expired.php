<?php
/**
 * Subscription Expired
 *
 * @package     AutomatorWP\Integrations\Paid_Memberships_Pro\Triggers\Subscription_Expired
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Paid_Memberships_Pro_Subscription_Expired extends AutomatorWP_Integration_Trigger {

    public $integration = 'paid_memberships_pro';
    public $trigger = 'paid_memberships_pro_subscription_expired';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User subscription of a membership level expires', 'automatorwp-paid-memberships-pro' ),
            'select_option'     => __( 'User subscription of a membership level <strong>expires</strong>', 'automatorwp-paid-memberships-pro' ),
            /* translators: %1$s: Content title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User subscription of %1$s expires %2$s time(s)', 'automatorwp-paid-memberships-pro' ), '{membership}', '{times}' ),
            /* translators: %1$s: Content title. */
            'log_label'         => sprintf( __( 'User subscription of %1$s expires', 'automatorwp-paid-memberships-pro' ), '{membership}' ),
            'action'            => 'pmpro_subscription_expired',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(
                'membership' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'membership',
                    'name'              => __( 'Membership Level:', 'automatorwp-paid-memberships-pro' ),
                    'option_none_value' => 'any',
                    'option_none_label' => __( 'any membership level', 'automatorwp-paid-memberships-pro' ),
                    'action_cb'         => 'automatorwp_paid_memberships_pro_get_memberships',
                    'options_cb'        => 'automatorwp_paid_memberships_pro_options_cb_membership',
                    'default'           => 'any'
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param MemberOrder $morder
     */
    public function listener( $morder ) {

        $user                = $morder->getUser();
        $membership          = $morder->getMembershipLevel();
        $user_id             = $user->ID;
        $membership_id       = $membership->id;

        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'membership_id' => $membership_id,
        ) );

    }

    /**
     * User deserves check
     *
     * @since 1.0.0
     *
     * @param bool      $deserves_trigger   True if user deserves trigger, false otherwise
     * @param stdClass  $trigger            The trigger object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return bool                          True if user deserves trigger, false otherwise
     */
    public function user_deserves_trigger( $deserves_trigger, $trigger, $user_id, $event, $trigger_options, $automation ) {

        // Don't deserve if membership is not received
        if( ! isset( $event['membership_id'] ) ) {
            return false;
        }

        $membership_id = absint( $event['membership_id'] );

        // Don't deserve if membership doesn't exists
        if( $membership_id === 0 ) {
            return false;
        }

        $required_membership_id = absint( $trigger_options['membership'] );

        // Don't deserve if membership doesn't match with the trigger option
        if( $trigger_options['membership'] !== 'any' && $membership_id !== $required_membership_id ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_Paid_Memberships_Pro_Subscription_Expired();