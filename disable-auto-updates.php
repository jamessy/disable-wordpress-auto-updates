<?php
/*
Plugin Name: Disable Auto Updates
Description: Adds an admin toggle to enable or disable all WordPress updates. When disabled, no updates (auto or manual) will work.
Version: 1.0
Author: James Godwin
Author URI: https://james-godwin.co.uk
*/

define( 'SG_UPDATE_LOG_FILE', WP_CONTENT_DIR . '/sg-update-attempts.log' );

// --- Add admin menu ---
add_action( 'admin_menu', function() {
    add_options_page( 'Disable Auto Updates', 'Disable Auto Updates', 'manage_options', 'disable-auto-updates', 'sg_update_toggle_page' );
});

// --- Render settings page ---
function sg_update_toggle_page() {
    if ( isset( $_POST['sg_update_toggle_nonce'] ) && wp_verify_nonce( $_POST['sg_update_toggle_nonce'], 'sg_update_toggle' ) ) {
        update_option( 'sg_updates_disabled', ! empty( $_POST['sg_updates_disabled'] ) ? 1 : 0 );
        echo '<div class="updated"><p>Update setting saved.</p></div>';
    }

    $disabled = get_option( 'sg_updates_disabled', 0 );
    ?>
    <div class="wrap">
        <h1>Disable Auto Updates</h1>
        <form method="post">
            <?php wp_nonce_field( 'sg_update_toggle', 'sg_update_toggle_nonce' ); ?>
            <label>
                <input type="checkbox" name="sg_updates_disabled" value="1" <?php checked( 1, $disabled ); ?> />
                Disable all WordPress updates (core, plugins, themes, manual + auto)
            </label>
            <p><input type="submit" class="button button-primary" value="Save Changes" /></p>
        </form>
    </div>
    <?php
}

// --- Logging helper ---
function sg_log_update_attempt( $message ) {
    $entry = date( 'Y-m-d H:i:s' ) . ' - ' . $message . "\n";
    file_put_contents( SG_UPDATE_LOG_FILE, $entry, FILE_APPEND | LOCK_EX );
}

// --- Update blocker ---
function sg_updates_blocker( $value = false ) {
    if ( get_option( 'sg_updates_disabled', 0 ) ) {
        sg_log_update_attempt( 'Update attempt blocked.' );
        return false;
    }
    return $value;
}

function sg_null_update_check( $value ) {
    if ( get_option( 'sg_updates_disabled', 0 ) ) {
        sg_log_update_attempt( 'Update check blocked.' );
        return null;
    }
    return $value;
}

// --- Apply filters ---
add_filter( 'auto_update_plugin', 'sg_updates_blocker', 10, 1 );
add_filter( 'auto_update_theme', 'sg_updates_blocker', 10, 1 );
add_filter( 'auto_update_core', 'sg_updates_blocker', 10, 1 );

add_filter( 'pre_site_transient_update_core', 'sg_null_update_check' );
add_filter( 'pre_site_transient_update_plugins', 'sg_null_update_check' );
add_filter( 'pre_site_transient_update_themes', 'sg_null_update_check' );

add_filter( 'upgrader_pre_download', function( $reply, $package ) {
    if ( get_option( 'sg_updates_disabled', 0 ) ) {
        sg_log_update_attempt( "Manual update download blocked: $package" );
        return new WP_Error( 'sg_update_blocked', 'Updates are disabled by Disable Auto Updates plugin.' );
    }
    return $reply;
}, 10, 2 );

// --- Constants for extra protection (optional, only when updates disabled) ---
add_action( 'init', function() {
    if ( get_option( 'sg_updates_disabled', 0 ) ) {
        if ( ! defined( 'AUTOMATIC_UPDATER_DISABLED' ) ) {
            define( 'AUTOMATIC_UPDATER_DISABLED', true );
        }
        if ( ! defined( 'WP_AUTO_UPDATE_CORE' ) ) {
            define( 'WP_AUTO_UPDATE_CORE', false );
        }
        if ( ! defined( 'DISALLOW_FILE_MODS' ) ) {
            define( 'DISALLOW_FILE_MODS', true );
        }
    }
});
