<?php
/**
 * Plugin Name: Sleep Apnea Estimator + GoHighLevel
 * Plugin URI: https://upwork.com/freelancers/adelsherif8
 * Description: Sleep apnea / sleep appliance estimator with GoHighLevel CRM integration. Use shortcode [sleep_apnea_form].
 * Version:     1.0.22
 * Author:      Adel Emad
 * Author URI:  https://upwork.com/freelancers/adelsherif8
 * Requires PHP: 7.4
 * License:     GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'SAPN_SLUG',           'sleep-apnea-ghl' );
define( 'SAPN_OPTION',         'sapn_settings' );
define( 'SAPN_ENTRIES_DB_VER', '1.0' );
define( 'SAPN_FILE',           __FILE__ );

// ═══════════════════════════════════════════════════════════════
//  DATABASE — ENTRIES TABLE
// ═══════════════════════════════════════════════════════════════
register_activation_hook( __FILE__, 'sapn_create_entries_table' );

function sapn_create_entries_table() {
    global $wpdb;
    $table           = $wpdb->prefix . 'sapn_entries';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS {$table} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        first_name  VARCHAR(100) NOT NULL DEFAULT '',
        last_name   VARCHAR(100) NOT NULL DEFAULT '',
        email       VARCHAR(200) NOT NULL DEFAULT '',
        phone       VARCHAR(50)  NOT NULL DEFAULT '',
        meta        LONGTEXT,
        ghl_status  VARCHAR(10)  NOT NULL DEFAULT 'ok',
        created_at  DATETIME     NOT NULL,
        PRIMARY KEY (id),
        KEY idx_created (created_at),
        KEY idx_status  (ghl_status)
    ) {$charset_collate};";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
    update_option( 'sapn_entries_db_version', SAPN_ENTRIES_DB_VER );
}

add_action( 'plugins_loaded', function () {
    if ( get_option( 'sapn_entries_db_version' ) !== SAPN_ENTRIES_DB_VER ) {
        sapn_create_entries_table();
    }
    // One-time migration: rename the old CTA defaults to the new ones for existing
    // installs that pre-date v1.0.16. wp_parse_args only fills missing keys, so the
    // old saved values would otherwise persist forever.
    if ( ! get_option( 'sapn_mig_cta_labels_v1' ) ) {
        $s = get_option( SAPN_OPTION, [] );
        $changed = false;
        if ( ( $s['result_book_label']      ?? '' ) === 'Book My Sleep Consultation' ) { $s['result_book_label']      = 'Submit Estimate'; $changed = true; }
        if ( ( $s['result_insurance_label'] ?? '' ) === 'Ask About Insurance Coverage' ) { $s['result_insurance_label'] = 'Contact Us';      $changed = true; }
        if ( $changed ) update_option( SAPN_OPTION, $s );
        update_option( 'sapn_mig_cta_labels_v1', '1' );
    }
}, 20 );

// ═══════════════════════════════════════════════════════════════
//  AUTO-UPDATE FROM GITHUB
// ═══════════════════════════════════════════════════════════════
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
$sapn_update_checker = YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
    'https://github.com/adelsherif8/sleep-apnea-ghl/',
    __FILE__,
    'sleep-apnea-ghl'
);
$sapn_update_checker->getVcsApi()->enableReleaseAssets();
$sapn_update_checker->addResultFilter( function( $info ) {
    $icon = 'https://raw.githubusercontent.com/adelsherif8/sleep-apnea-ghl/main/logo.svg';
    $info->icons = [ 'svg' => $icon, '1x' => $icon, '2x' => $icon ];
    return $info;
} );

// ═══════════════════════════════════════════════════════════════
//  DEFAULTS / SETTINGS HELPERS
// ═══════════════════════════════════════════════════════════════
function sapn_defaults() {
    return [
        // GHL connection
        'ghl_api_key'      => '',
        'ghl_location_id'  => '',

        // Branding
        'brand_name'       => 'Riverwalk Dentistry',
        'brand_city'       => 'Waterloo',

        // Hero / landing copy (rendered on the landing view if the shortcode is used with show_landing="1")
        'hero_eyebrow'     => 'Dental Sleep Medicine',
        'hero_heading'     => 'Quieter nights. Easier mornings.',
        'hero_subheading'  => 'Custom oral appliances, snoring support, and airway-focused care — designed to help you sleep more comfortably and wake up feeling more like yourself.',
        'hero_cta'         => 'Get My Sleep Appliance Estimate',

        // Intro
        'intro_heading'    => 'Get Your Sleep Appliance Estimate',
        'intro_sub'        => "Answer a few quick questions about your sleep, snoring, CPAP experience, and goals. We'll help you understand which treatment path may fit you best and what your estimated investment could look like.",
        'intro_cta'        => 'Start My Estimate',

        // Results CTAs
        'result_book_label'      => 'Submit Estimate',
        'result_book_url'        => '',
        'result_insurance_label' => 'Contact Us',
        'result_insurance_url'   => '',

        // 'What to expect / Next steps' card on the results screen
        'next_steps_show'        => '1',
        'next_steps_heading'     => 'So, you have been diagnosed with sleep apnea — now what?',
        'next_steps_body'        => "Bring a copy of your sleep study to your consultation, or sign a document release form so we can reach out to your sleep clinic on your behalf. Please also fill out our online sleep questionnaires before your visit:",
        'next_steps_link1_label' => 'Berlin Questionnaire',
        'next_steps_link1_url'   => 'https://smilesinwaterloo.com/services/sleep-apnea-snoring/#',
        'next_steps_link2_label' => 'STOP-Bang Questionnaire',
        'next_steps_link2_url'   => 'https://smilesinwaterloo.com/services/sleep-apnea-snoring/#',
        'next_steps_link3_label' => 'Learn how ProSomnus treatment works',
        'next_steps_link3_url'   => 'https://prosomnus.com/how-it-works/',
        'next_steps_hint'        => 'Not sure where to start?',

        // Submission behaviour
        'success_redirect_url'   => '',
        'lead_tag'               => 'sleep-apnea-form',
        'lead_source'            => 'Sleep Apnea Estimator',

        // Spam
        'spam_honeypot'          => '1',

        // Hide the site header / footer on pages where the form is shown
        'hide_header'            => '0',
        'hide_footer'            => '0',
    ];
}

function sapn_get( $key = null ) {
    $opts = get_option( SAPN_OPTION, [] );
    $opts = wp_parse_args( $opts, sapn_defaults() );
    if ( $key !== null ) return $opts[ $key ] ?? null;
    return $opts;
}

function sapn_register_settings() {
    register_setting( 'sapn_settings_group', SAPN_OPTION, [
        'sanitize_callback' => 'sapn_sanitize_settings',
    ] );
}
add_action( 'admin_init', 'sapn_register_settings' );

function sapn_sanitize_settings( $input ) {
    $defaults = sapn_defaults();
    $clean    = [];
    foreach ( $defaults as $key => $default ) {
        if ( in_array( $key, [ 'hero_subheading', 'intro_sub', 'next_steps_body' ], true ) ) {
            $clean[ $key ] = sanitize_textarea_field( $input[ $key ] ?? $default );
        } elseif ( in_array( $key, [ 'success_redirect_url', 'result_book_url', 'result_insurance_url', 'next_steps_link1_url', 'next_steps_link2_url', 'next_steps_link3_url' ], true ) ) {
            $clean[ $key ] = esc_url_raw( $input[ $key ] ?? $default );
        } else {
            $clean[ $key ] = sanitize_text_field( $input[ $key ] ?? $default );
        }
    }
    return $clean;
}

// ═══════════════════════════════════════════════════════════════
//  GHL FIELD DEFINITIONS
//  Sleep-apnea-form-specific fields + shared UTM fields.
//  The UTM block uses the SAME field keys as contact-form-ghl so
//  both plugins push to the same GHL custom fields.
// ═══════════════════════════════════════════════════════════════
function sapn_ghl_field_definitions() {
    $sleep_fields = [
        [ 'name' => 'Sleep — Reason',           'key' => 'sleep_reason' ],
        [ 'name' => 'Sleep — Apnea Status',     'key' => 'sleep_apnea_status' ],
        [ 'name' => 'Sleep — CPAP Experience',  'key' => 'sleep_cpap_experience' ],
        [ 'name' => 'Sleep — Symptoms',         'key' => 'sleep_symptoms' ],
        [ 'name' => 'Sleep — Airway Concerns',  'key' => 'sleep_airway_concerns' ],
        [ 'name' => 'Sleep — Estimate Range',   'key' => 'sleep_estimate_range' ],
        [ 'name' => 'Sleep — Contact Preference','key' => 'sleep_contact_pref' ],
        [ 'name' => 'Sleep — Insurance Benefits','key' => 'sleep_benefits' ],
        [ 'name' => 'Latest Form Date',          'key' => 'latest_form_date' ],
    ];
    $utm_fields = [
        [ 'name' => 'UTMCampaign_custom', 'key' => 'UTMCampaign_custom' ],
        [ 'name' => 'UTMMedium_custom',   'key' => 'UTMMedium_custom' ],
        [ 'name' => 'UTMContent_custom',  'key' => 'UTMContent_custom' ],
        [ 'name' => 'UTMKeyword_custom',  'key' => 'UTMKeyword_custom' ],
        [ 'name' => 'UTMTerm_custom',     'key' => 'UTMTerm_custom' ],
        [ 'name' => 'GCLID_custom',       'key' => 'GCLID_custom' ],
    ];
    $sleep_fields = array_map( fn( $f ) => $f + [ 'folder' => 'Sleep Apnea Form' ], $sleep_fields );
    $utm_fields   = array_map( fn( $f ) => $f + [ 'folder' => 'UTM Forms' ], $utm_fields );
    return [
        'Sleep Apnea Form' => $sleep_fields,
        'UTM Forms'        => $utm_fields,
    ];
}

function sapn_folder_names() {
    return [ 'Sleep Apnea Form', 'UTM Forms' ];
}

function sapn_get_folder_ids( $location_id ) {
    return get_option( 'sapn_folder_ids_' . md5( $location_id ), [] );
}

// ═══════════════════════════════════════════════════════════════
//  ADMIN AJAX — GHL FIELDS / FOLDERS
// ═══════════════════════════════════════════════════════════════
add_action( 'wp_ajax_sapn_check_ghl_fields',      'sapn_ajax_check_ghl_fields' );
add_action( 'wp_ajax_sapn_create_ghl_field',      'sapn_ajax_create_ghl_field' );
add_action( 'wp_ajax_sapn_move_ghl_fields',       'sapn_ajax_move_ghl_fields' );
add_action( 'wp_ajax_sapn_save_folder_ids',       'sapn_ajax_save_folder_ids' );
add_action( 'wp_ajax_sapn_detect_folder_ids',     'sapn_ajax_detect_folder_ids' );
add_action( 'wp_ajax_sapn_create_checker_fields', 'sapn_ajax_create_checker_fields' );
add_action( 'wp_ajax_sapn_delete_checker_fields', 'sapn_ajax_delete_checker_fields' );

function sapn_checker_fields() {
    return [
        'sapn_checker_sleep_apnea_form' => 'Sleep Apnea Form',
        'sapn_checker_utm_forms'        => 'UTM Forms',
    ];
}

function sapn_ghl_clean_key( $api_key ) {
    $k = trim( (string) $api_key );
    // If the user pasted the token with a "Bearer " prefix already, strip it.
    if ( stripos( $k, 'Bearer ' ) === 0 ) $k = trim( substr( $k, 7 ) );
    return $k;
}

function sapn_ghl_headers( $api_key ) {
    return [
        'Authorization' => 'Bearer ' . sapn_ghl_clean_key( $api_key ),
        'Content-Type'  => 'application/json',
        'Version'       => '2021-07-28',
    ];
}

function sapn_ghl_base() { return 'https://services.leadconnectorhq.com'; }

// Extract a useful error message from a GHL response (or wp_error).
function sapn_ghl_error( $r, $code = null ) {
    if ( is_wp_error( $r ) ) return $r->get_error_message();
    if ( $code === null )   $code = wp_remote_retrieve_response_code( $r );
    $raw  = wp_remote_retrieve_body( $r );
    $body = json_decode( $raw, true );
    $msg  = '';
    if ( is_array( $body ) ) {
        $msg = $body['message'] ?? '';
        if ( ! $msg && isset( $body['error'] ) ) $msg = is_string( $body['error'] ) ? $body['error'] : wp_json_encode( $body['error'] );
    }
    if ( ! $msg ) $msg = $raw ? wp_strip_all_tags( substr( $raw, 0, 240 ) ) : '(empty body)';
    return 'HTTP ' . $code . ' — ' . $msg;
}

function sapn_ajax_check_ghl_fields() {
    check_ajax_referer( 'sapn_fields_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Unauthorized.' );

    $s = sapn_get();
    if ( ! $s['ghl_api_key'] || ! $s['ghl_location_id'] ) wp_send_json_error( 'API key or Location ID not configured.' );

    $r = wp_remote_get( sapn_ghl_base() . '/locations/' . $s['ghl_location_id'] . '/customFields', [
        'headers' => sapn_ghl_headers( $s['ghl_api_key'] ),
        'timeout' => 15,
    ] );
    if ( is_wp_error( $r ) ) wp_send_json_error( $r->get_error_message() );
    $code = wp_remote_retrieve_response_code( $r );
    if ( $code === 401 || $code === 403 ) {
        wp_send_json_error( 'GHL auth failed (' . sapn_ghl_error( $r, $code ) . '). Common causes: '
            . '(1) Wrong API key type — use a Private Integration Token (Sub-account → Settings → Private Integrations), not a legacy API key. '
            . '(2) Token missing one of these scopes: contacts.write, custom-fields.readonly, custom-fields.write. '
            . '(3) Wrong Location ID — must match the sub-account that issued the token.' );
    }

    $body     = json_decode( wp_remote_retrieve_body( $r ), true );
    $existing = [];
    foreach ( $body['customFields'] ?? [] as $f ) {
        if ( ! isset( $f['fieldKey'] ) ) continue;
        $bare = strtolower( preg_replace( '/^contact\./', '', $f['fieldKey'] ) );
        $existing[ $bare ]                    = true;
        $existing[ strtolower( $f['fieldKey'] ) ] = true;
    }

    $defs   = sapn_ghl_field_definitions();
    $result = [];
    foreach ( $defs as $group => $fields ) {
        foreach ( $fields as $f ) {
            $result[] = [
                'group'  => $group,
                'name'   => $f['name'],
                'key'    => $f['key'],
                'exists' => isset( $existing[ strtolower( $f['key'] ) ] ),
            ];
        }
    }
    wp_send_json_success( $result );
}

function sapn_ajax_create_ghl_field() {
    check_ajax_referer( 'sapn_fields_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Unauthorized.' );

    $s           = sapn_get();
    $field_key   = sanitize_text_field( $_POST['field_key'] ?? '' );
    $field_name  = sanitize_text_field( $_POST['field_name'] ?? $field_key );
    $folder_name = sanitize_text_field( $_POST['folder'] ?? '' );
    if ( ! $s['ghl_api_key'] || ! $s['ghl_location_id'] || ! $field_key ) wp_send_json_error( 'Missing parameters.' );

    $headers   = sapn_ghl_headers( $s['ghl_api_key'] );
    $base      = sapn_ghl_base();
    $loc       = $s['ghl_location_id'];
    $parent_id = null;

    if ( $folder_name ) {
        $fr = wp_remote_get( "{$base}/locations/{$loc}/customFieldsFolders", [ 'headers' => $headers, 'timeout' => 15 ] );
        if ( ! is_wp_error( $fr ) && wp_remote_retrieve_response_code( $fr ) < 300 ) {
            $fb = json_decode( wp_remote_retrieve_body( $fr ), true );
            foreach ( $fb['folders'] ?? [] as $folder ) {
                if ( strtolower( $folder['name'] ) === strtolower( $folder_name ) ) { $parent_id = $folder['id']; break; }
            }
        }
        if ( ! $parent_id ) {
            $cr = wp_remote_post( "{$base}/locations/{$loc}/customFieldsFolders", [
                'headers' => $headers, 'body' => wp_json_encode( [ 'name' => $folder_name ] ), 'timeout' => 15,
            ] );
            if ( ! is_wp_error( $cr ) ) {
                $cb = json_decode( wp_remote_retrieve_body( $cr ), true );
                $parent_id = $cb['folder']['id'] ?? $cb['id'] ?? null;
            }
        }
    }

    $payload = [ 'name' => $field_name, 'fieldKey' => $field_key, 'dataType' => 'TEXT', 'position' => 0 ];
    if ( $parent_id ) $payload['parentId'] = $parent_id;

    $r = wp_remote_post( "{$base}/locations/{$loc}/customFields", [
        'headers' => $headers, 'body' => wp_json_encode( $payload ), 'timeout' => 15,
    ] );
    if ( is_wp_error( $r ) ) wp_send_json_error( $r->get_error_message() );
    $code = wp_remote_retrieve_response_code( $r );
    $body = json_decode( wp_remote_retrieve_body( $r ), true );
    if ( $code >= 200 && $code < 300 ) wp_send_json_success( 'Field created.' );
    wp_send_json_error( $body['message'] ?? 'HTTP ' . $code );
}

function sapn_ajax_save_folder_ids() {
    check_ajax_referer( 'sapn_fields_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Unauthorized.' );
    $s = sapn_get();
    if ( ! $s['ghl_location_id'] ) wp_send_json_error( 'Location ID not configured.' );

    $map = [];
    foreach ( sapn_folder_names() as $name ) {
        $key = 'folder_' . str_replace( ' ', '_', strtolower( $name ) );
        $id  = sanitize_text_field( $_POST[ $key ] ?? '' );
        if ( $id ) $map[ $name ] = $id;
    }
    update_option( 'sapn_folder_ids_' . md5( $s['ghl_location_id'] ), $map );
    wp_send_json_success( $map );
}

function sapn_ajax_detect_folder_ids() {
    check_ajax_referer( 'sapn_fields_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Unauthorized.' );
    $s = sapn_get();
    if ( ! $s['ghl_api_key'] || ! $s['ghl_location_id'] ) wp_send_json_error( 'API key or Location ID not configured.' );

    $headers = sapn_ghl_headers( $s['ghl_api_key'] );
    $base    = sapn_ghl_base();
    $loc     = $s['ghl_location_id'];

    // 1. Fetch fields (some accounts inline `folders` in this response)
    $fr = wp_remote_get( "{$base}/locations/{$loc}/customFields", [ 'headers' => $headers, 'timeout' => 15 ] );
    if ( is_wp_error( $fr ) ) wp_send_json_error( $fr->get_error_message() );
    $code = wp_remote_retrieve_response_code( $fr );
    if ( $code === 401 || $code === 403 ) wp_send_json_error( 'GHL auth failed (' . sapn_ghl_error( $fr, $code ) . ').' );
    $body = json_decode( wp_remote_retrieve_body( $fr ), true );

    // 2. Always hit the dedicated folders endpoint too — many tenants only return folders here
    $folders_inline = $body['folders'] ?? [];
    $folders_dedicated = [];
    $fr2 = wp_remote_get( "{$base}/locations/{$loc}/customFieldsFolders", [ 'headers' => $headers, 'timeout' => 15 ] );
    if ( ! is_wp_error( $fr2 ) ) {
        $b2 = json_decode( wp_remote_retrieve_body( $fr2 ), true );
        if ( is_array( $b2 ) ) {
            $folders_dedicated = $b2['folders'] ?? ( isset( $b2[0] ) ? $b2 : [] );
        }
    }
    $all_folders = array_merge( $folders_inline, $folders_dedicated );

    // 3. Match folders by name (case-/whitespace-insensitive)
    $by_name = [];
    foreach ( $all_folders as $folder ) {
        if ( ! empty( $folder['id'] ) && ! empty( $folder['name'] ) ) {
            $by_name[ strtolower( trim( $folder['name'] ) ) ] = $folder['id'];
        }
    }
    $matched = [];
    foreach ( sapn_folder_names() as $name ) {
        $key = strtolower( $name );
        if ( isset( $by_name[ $key ] ) ) $matched[ $name ] = $by_name[ $key ];
    }

    // 4. Fallback: deduce folder IDs from the parentId of existing fields. This kicks in when
    //    folder names aren't available via either endpoint, but the user has dropped checker /
    //    plugin fields into the folders.
    $parent_ids = []; // folder_id => [ field_keys ]
    foreach ( $body['customFields'] ?? [] as $f ) {
        if ( empty( $f['parentId'] ) ) continue;
        $bare = strtolower( preg_replace( '/^contact\./', '', $f['fieldKey'] ?? '' ) );
        $parent_ids[ $f['parentId'] ][] = $bare;
    }

    // 5. Score each parent folder against each target folder. Checker fields → 999 (guaranteed win).
    $checker_for_folder = []; // folder name => checker fieldKey
    foreach ( sapn_checker_fields() as $ck => $folder_name ) {
        $checker_for_folder[ $folder_name ] = strtolower( $ck );
    }
    $group_keys = []; // folder name => [ expected field keys (lowercased) ]
    foreach ( sapn_ghl_field_definitions() as $folder_name => $fields ) {
        $group_keys[ $folder_name ] = array_map( fn( $f ) => strtolower( $f['key'] ), $fields );
    }

    $best = []; // folder name => [ id, score ]
    foreach ( $parent_ids as $pid => $keys ) {
        $top_name = null; $top_score = 0;
        foreach ( $group_keys as $folder_name => $expected ) {
            if ( ! empty( $checker_for_folder[ $folder_name ] ) && in_array( $checker_for_folder[ $folder_name ], $keys, true ) ) {
                $score = 999;
            } else {
                $score = count( array_intersect( $expected, $keys ) );
            }
            if ( $score > $top_score ) { $top_score = $score; $top_name = $folder_name; }
        }
        if ( $top_name && $top_score > 0 && $top_score > ( $best[ $top_name ]['score'] ?? 0 ) ) {
            $best[ $top_name ] = [ 'id' => $pid, 'score' => $top_score ];
        }
    }
    foreach ( $best as $folder_name => $info ) {
        if ( ! isset( $matched[ $folder_name ] ) ) {
            $matched[ $folder_name ] = $info['id'];
        }
    }

    $stored = sapn_get_folder_ids( $loc );
    if ( ! empty( $matched ) ) {
        $stored = array_merge( $stored, $matched );
        update_option( 'sapn_folder_ids_' . md5( $loc ), $stored );
    }
    wp_send_json_success( [
        'detected'    => $parent_ids,
        'by_name'     => $matched,
        'stored'      => $stored,
        'raw_folders' => $all_folders,
    ] );
}

function sapn_ajax_create_checker_fields() {
    check_ajax_referer( 'sapn_fields_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Unauthorized.' );
    $s = sapn_get();
    if ( ! $s['ghl_api_key'] || ! $s['ghl_location_id'] ) wp_send_json_error( 'API key or Location ID not configured.' );

    $headers = sapn_ghl_headers( $s['ghl_api_key'] );
    $base    = sapn_ghl_base();
    $loc     = $s['ghl_location_id'];
    $created = []; $errors = [];

    foreach ( sapn_checker_fields() as $key => $folder ) {
        $label = ucwords( str_replace( [ 'sapn_checker_', '_' ], [ '', ' ' ], $key ) ) . ' Checker';
        $r = wp_remote_post( "{$base}/locations/{$loc}/customFields", [
            'headers' => $headers,
            'body'    => wp_json_encode( [ 'name' => $label, 'fieldKey' => $key, 'dataType' => 'TEXT', 'position' => 0 ] ),
            'timeout' => 15,
        ] );
        $code = is_wp_error( $r ) ? 0 : wp_remote_retrieve_response_code( $r );
        if ( $code >= 200 && $code < 300 ) $created[] = $key;
        elseif ( $code === 400 ) $created[] = $key; // already exists
        elseif ( $code === 401 || $code === 403 ) {
            wp_send_json_error( 'GHL auth failed (' . sapn_ghl_error( $r, $code ) . '). '
                . 'Use a Private Integration Token (Settings → Private Integrations) with scopes contacts.write + custom-fields.readonly + custom-fields.write.' );
        }
        else $errors[] = $key . ': ' . sapn_ghl_error( $r, $code );
    }
    wp_send_json_success( [ 'created' => $created, 'errors' => $errors ] );
}

function sapn_ajax_delete_checker_fields() {
    check_ajax_referer( 'sapn_fields_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Unauthorized.' );
    $s = sapn_get();
    if ( ! $s['ghl_api_key'] || ! $s['ghl_location_id'] ) wp_send_json_error( 'API key or Location ID not configured.' );

    $headers = sapn_ghl_headers( $s['ghl_api_key'] );
    $base    = sapn_ghl_base();
    $loc     = $s['ghl_location_id'];

    $fr = wp_remote_get( "{$base}/locations/{$loc}/customFields", [ 'headers' => $headers, 'timeout' => 15 ] );
    if ( is_wp_error( $fr ) ) wp_send_json_error( $fr->get_error_message() );

    $checker_keys = array_keys( sapn_checker_fields() );
    $deleted = []; $errors = [];
    foreach ( json_decode( wp_remote_retrieve_body( $fr ), true )['customFields'] ?? [] as $f ) {
        $bare = strtolower( preg_replace( '/^contact\./', '', $f['fieldKey'] ?? '' ) );
        if ( ! in_array( $bare, $checker_keys, true ) ) continue;
        $dr = wp_remote_request( "{$base}/locations/{$loc}/customFields/{$f['id']}", [
            'method'  => 'DELETE', 'headers' => $headers, 'timeout' => 15,
        ] );
        $code = is_wp_error( $dr ) ? 0 : wp_remote_retrieve_response_code( $dr );
        if ( $code >= 200 && $code < 300 ) $deleted[] = $bare;
        else $errors[] = $bare . ': HTTP ' . $code;
    }
    wp_send_json_success( [ 'deleted' => $deleted, 'errors' => $errors ] );
}

function sapn_ajax_move_ghl_fields() {
    check_ajax_referer( 'sapn_fields_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Unauthorized.' );
    $s = sapn_get();
    if ( ! $s['ghl_api_key'] || ! $s['ghl_location_id'] ) wp_send_json_error( 'API key or Location ID not configured.' );

    $folder_map = sapn_get_folder_ids( $s['ghl_location_id'] );
    if ( empty( $folder_map ) ) wp_send_json_error( 'No folder IDs saved yet. Save folder IDs first.' );

    $headers = sapn_ghl_headers( $s['ghl_api_key'] );
    $base    = sapn_ghl_base();
    $loc     = $s['ghl_location_id'];

    $fr = wp_remote_get( "{$base}/locations/{$loc}/customFields", [ 'headers' => $headers, 'timeout' => 15 ] );
    if ( is_wp_error( $fr ) ) wp_send_json_error( $fr->get_error_message() );

    $ghl_fields = [];
    foreach ( json_decode( wp_remote_retrieve_body( $fr ), true )['customFields'] ?? [] as $f ) {
        if ( empty( $f['fieldKey'] ) ) continue;
        $bare = strtolower( preg_replace( '/^contact\./', '', $f['fieldKey'] ) );
        $ghl_fields[ $bare ] = $f;
    }

    $defs    = sapn_ghl_field_definitions();
    $moved = 0; $skipped = 0; $errors = [];
    foreach ( $defs as $group => $fields ) {
        $folder_id = $folder_map[ $group ] ?? null;
        if ( ! $folder_id ) { foreach ( $fields as $f ) $errors[] = $f['key'] . ': no folder ID for "' . $group . '"'; continue; }
        foreach ( $fields as $f ) {
            $bare = strtolower( $f['key'] );
            if ( ! isset( $ghl_fields[ $bare ] ) ) { $skipped++; continue; }
            $ghl_f = $ghl_fields[ $bare ];
            if ( ( $ghl_f['parentId'] ?? null ) === $folder_id ) { $skipped++; continue; }
            $pr = wp_remote_request( "{$base}/locations/{$loc}/customFields/{$ghl_f['id']}", [
                'method' => 'PUT', 'headers' => $headers,
                'body'   => wp_json_encode( [ 'parentId' => $folder_id ] ), 'timeout' => 15,
            ] );
            $pcode = is_wp_error( $pr ) ? 0 : wp_remote_retrieve_response_code( $pr );
            if ( $pcode >= 200 && $pcode < 300 ) $moved++;
            else $errors[] = $bare . ': HTTP ' . $pcode;
        }
    }
    wp_send_json_success( [ 'moved' => $moved, 'skipped' => $skipped, 'errors' => $errors ] );
}

// ═══════════════════════════════════════════════════════════════
//  ENTRY LOGGER
// ═══════════════════════════════════════════════════════════════
function sapn_log_entry( $first, $last, $email, $phone, $meta = [], $ghl_status = 'ok' ) {
    global $wpdb;
    if ( get_option( 'sapn_entries_db_version' ) !== SAPN_ENTRIES_DB_VER ) {
        sapn_create_entries_table();
    }
    $wpdb->insert(
        $wpdb->prefix . 'sapn_entries',
        [
            'first_name' => $first,
            'last_name'  => $last,
            'email'      => $email,
            'phone'      => $phone,
            'meta'       => wp_json_encode( $meta ),
            'ghl_status' => $ghl_status,
            'created_at' => current_time( 'mysql' ),
        ],
        [ '%s', '%s', '%s', '%s', '%s', '%s', '%s' ]
    );

    if ( $ghl_status === 'error' ) {
        wp_mail(
            get_option( 'admin_email' ),
            '[Sleep Apnea GHL] Submission failed to reach GHL',
            "A submission failed to push to GoHighLevel.\n\n"
            . "Name: {$first} {$last}\nEmail: {$email}\nPhone: {$phone}\n\n"
            . "Open the Entries tab for details:\n"
            . admin_url( 'admin.php?page=' . SAPN_SLUG . '&sapn_tab=entries' )
        );
    }
}

// ═══════════════════════════════════════════════════════════════
//  SUBMIT — PUSH TO GHL
// ═══════════════════════════════════════════════════════════════
add_action( 'wp_ajax_sapn_submit',        'sapn_ajax_submit' );
add_action( 'wp_ajax_nopriv_sapn_submit', 'sapn_ajax_submit' );

function sapn_ajax_submit() {
    if ( ! isset( $_POST['sapn_nonce'] ) || ! wp_verify_nonce( $_POST['sapn_nonce'], 'sapn_submit' ) ) {
        wp_send_json_error( 'Security check failed. Please refresh and try again.' );
    }
    $s = sapn_get();

    // Honeypot
    if ( $s['spam_honeypot'] === '1' ) {
        $hp = sanitize_text_field( $_POST['sapn_hp'] ?? '' );
        if ( ! empty( $hp ) ) wp_send_json_error( 'Submission blocked.' );
    }

    // Sanitise
    $first  = sanitize_text_field( $_POST['firstName']    ?? '' );
    $email  = sanitize_email(      $_POST['email']        ?? '' );
    $phone  = sanitize_text_field( $_POST['phone']        ?? '' );

    if ( $first === '' )            wp_send_json_error( 'First name is required.' );
    if ( ! is_email( $email ) )     wp_send_json_error( 'A valid email address is required.' );

    if ( empty( $s['ghl_api_key'] ) || empty( $s['ghl_location_id'] ) ) {
        wp_send_json_error( 'The form is not fully configured yet. Please contact us directly.' );
    }

    // Answers
    $reason       = sanitize_text_field( $_POST['reason']        ?? '' );
    $study        = sanitize_text_field( $_POST['study']         ?? '' );
    $cpap         = sanitize_text_field( $_POST['cpap']          ?? '' );
    $symptoms     = sanitize_text_field( $_POST['symptoms']      ?? '' ); // comma-joined client-side
    $airway       = sanitize_text_field( $_POST['airway']        ?? '' );
    $est_range    = sanitize_text_field( $_POST['estimateRange'] ?? '' );
    $contact_pref = sanitize_text_field( $_POST['contactPref']   ?? '' );
    $benefits     = sanitize_text_field( $_POST['benefits']      ?? '' );

    // Build customFields
    $custom_fields = [];
    $custom_fields[] = [ 'key' => 'latest_form_date', 'field_value' => current_time( 'M j, Y g:i A' ) ];
    if ( $reason )       $custom_fields[] = [ 'key' => 'sleep_reason',           'field_value' => $reason ];
    if ( $study )        $custom_fields[] = [ 'key' => 'sleep_apnea_status',     'field_value' => $study ];
    if ( $cpap )         $custom_fields[] = [ 'key' => 'sleep_cpap_experience',  'field_value' => $cpap ];
    if ( $symptoms )     $custom_fields[] = [ 'key' => 'sleep_symptoms',         'field_value' => $symptoms ];
    if ( $airway )       $custom_fields[] = [ 'key' => 'sleep_airway_concerns',  'field_value' => $airway ];
    if ( $est_range )    $custom_fields[] = [ 'key' => 'sleep_estimate_range',   'field_value' => $est_range ];
    if ( $contact_pref ) $custom_fields[] = [ 'key' => 'sleep_contact_pref',     'field_value' => $contact_pref ];
    if ( $benefits )     $custom_fields[] = [ 'key' => 'sleep_benefits',         'field_value' => $benefits ];

    // UTM / GCLID — same field keys as contact-form-ghl
    foreach ( [ 'utmcampaign_custom', 'utmmedium_custom', 'utmcontent_custom', 'utmkeyword_custom', 'utmterm_custom', 'gclid_custom' ] as $k ) {
        $val = sanitize_text_field( $_POST[ $k ] ?? '' );
        if ( $val !== '' ) $custom_fields[] = [ 'key' => $k, 'field_value' => $val ];
    }

    $payload = [
        'firstName'  => $first,
        'email'      => $email,
        'phone'      => $phone,
        'locationId' => $s['ghl_location_id'],
        'source'     => $s['lead_source'] ?: 'Sleep Apnea Estimator',
        'tags'       => [ $s['lead_tag'] ?: 'sleep-apnea-form' ],
    ];
    if ( $custom_fields ) $payload['customFields'] = $custom_fields;

    $response = wp_remote_post( sapn_ghl_base() . '/contacts/upsert', [
        'headers' => sapn_ghl_headers( $s['ghl_api_key'] ),
        'body'    => wp_json_encode( $payload ),
        'timeout' => 15,
    ] );

    if ( is_wp_error( $response ) ) {
        error_log( '[SAPN] GHL request failed: ' . $response->get_error_message() );
        sapn_log_entry( $first, '', $email, $phone, [
            'reason'=>$reason, 'study'=>$study, 'cpap'=>$cpap, 'symptoms'=>$symptoms,
            'airway'=>$airway, 'estimate_range'=>$est_range, 'contact_pref'=>$contact_pref, 'benefits'=>$benefits,
            'utm_campaign'=>sanitize_text_field($_POST['utmcampaign_custom']??''),
            'utm_medium'  =>sanitize_text_field($_POST['utmmedium_custom']  ??''),
            'utm_content' =>sanitize_text_field($_POST['utmcontent_custom'] ??''),
            'utm_keyword' =>sanitize_text_field($_POST['utmkeyword_custom'] ??''),
            'utm_term'    =>sanitize_text_field($_POST['utmterm_custom']    ??''),
            'gclid'       =>sanitize_text_field($_POST['gclid_custom']      ??''),
            '_ghl_error'  => $response->get_error_message(),
            '_ghl_fields_sent' => $custom_fields,
        ], 'error' );
        wp_send_json_error( 'Could not reach the CRM. Please try again in a moment.' );
    }

    $code = wp_remote_retrieve_response_code( $response );
    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    $ghl_ok = ( $code === 200 || $code === 201 );

    $entry_meta = [
        'reason'=>$reason, 'study'=>$study, 'cpap'=>$cpap, 'symptoms'=>$symptoms,
        'airway'=>$airway, 'estimate_range'=>$est_range, 'contact_pref'=>$contact_pref, 'benefits'=>$benefits,
        'utm_campaign'=>sanitize_text_field($_POST['utmcampaign_custom']??''),
        'utm_medium'  =>sanitize_text_field($_POST['utmmedium_custom']  ??''),
        'utm_content' =>sanitize_text_field($_POST['utmcontent_custom'] ??''),
        'utm_keyword' =>sanitize_text_field($_POST['utmkeyword_custom'] ??''),
        'utm_term'    =>sanitize_text_field($_POST['utmterm_custom']    ??''),
        'gclid'       =>sanitize_text_field($_POST['gclid_custom']      ??''),
        '_ghl_fields_sent' => $custom_fields,
        '_ghl_http_code'   => $code,
        '_ghl_response'    => $body,
    ];
    $entry_meta = array_filter( $entry_meta, fn( $v ) => $v !== '' && $v !== null );

    sapn_log_entry( $first, '', $email, $phone, $entry_meta, $ghl_ok ? 'ok' : 'error' );

    if ( $ghl_ok ) wp_send_json_success( 'Saved.' );
    wp_send_json_error( $body['message'] ?? ( 'Unexpected error (HTTP ' . $code . ').' ) );
}

// ═══════════════════════════════════════════════════════════════
//  ADMIN MENU + SETTINGS PAGE (TABS)
// ═══════════════════════════════════════════════════════════════
add_action( 'admin_menu', function () {
    add_menu_page(
        'Sleep Apnea GHL', 'Sleep Apnea GHL',
        'manage_options', SAPN_SLUG, 'sapn_settings_page',
        'dashicons-heart', 31
    );
} );

function sapn_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;
    $s   = sapn_get();
    $tab = sanitize_key( $_GET['sapn_tab'] ?? 'settings' );
    if ( ! in_array( $tab, [ 'settings', 'ghl_fields', 'entries' ], true ) ) $tab = 'settings';

    $base_url = admin_url( 'admin.php?page=' . SAPN_SLUG );
    ?>
    <div class="wrap">
        <h1 style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
            <span style="display:inline-block;width:42px;height:42px;border-radius:8px;overflow:hidden;line-height:0;">
                <img src="<?= esc_url( plugins_url( 'logo.svg', __FILE__ ) ) ?>" alt="" width="42" height="42">
            </span>
            <span>Sleep Apnea Estimator <span style="color:#94a3b8;font-weight:400;font-size:14px;">— by Adel Emad</span></span>
        </h1>

        <h2 class="nav-tab-wrapper" style="margin-top:14px;">
            <a href="<?= esc_url( add_query_arg( 'sapn_tab', 'settings',   $base_url ) ) ?>" class="nav-tab <?= $tab === 'settings'   ? 'nav-tab-active' : '' ?>">Settings</a>
            <a href="<?= esc_url( add_query_arg( 'sapn_tab', 'ghl_fields', $base_url ) ) ?>" class="nav-tab <?= $tab === 'ghl_fields' ? 'nav-tab-active' : '' ?>">GHL Custom Fields</a>
            <a href="<?= esc_url( add_query_arg( 'sapn_tab', 'entries',    $base_url ) ) ?>" class="nav-tab <?= $tab === 'entries'    ? 'nav-tab-active' : '' ?>">Entries</a>
        </h2>

        <?php if ( isset( $_GET['settings-updated'] ) ): ?>
            <div class="notice notice-success is-dismissible"><p><strong>Settings saved.</strong></p></div>
        <?php endif; ?>

        <div style="background:#fff;border:1px solid #e2e4e9;border-top:none;border-radius:0 0 6px 6px;padding:22px 24px;">
            <?php
            if ( $tab === 'settings' )    sapn_render_settings_tab( $s );
            if ( $tab === 'ghl_fields' )  sapn_render_ghl_fields_tab( $s );
            if ( $tab === 'entries' )     sapn_render_entries_tab();
            ?>
        </div>

        <p style="margin-top:18px;color:#6b7280;font-size:12px;">
            Use the shortcode <code>[sleep_apnea_form]</code> on any page to embed the estimator.
        </p>
    </div>
    <?php
}

// ─── Settings tab ───────────────────────────────────────────────
function sapn_render_settings_tab( $s ) {
    ?>
    <form method="post" action="options.php">
        <?php settings_fields( 'sapn_settings_group' ); ?>

        <h2 style="margin-top:0;">GHL Connection</h2>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><label for="sapn_ghl_api_key">GHL API Key (Private Integration Token)</label></th>
                <td>
                    <input type="text" id="sapn_ghl_api_key" name="<?= SAPN_OPTION ?>[ghl_api_key]" value="<?= esc_attr( $s['ghl_api_key'] ) ?>" class="regular-text" style="font-family:monospace;" autocomplete="off"/>
                    <p class="description">Create one in GHL → Settings → Private Integrations with scopes: <code>contacts.write</code>, <code>custom-fields.readonly</code>, <code>custom-fields.write</code>.</p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="sapn_ghl_loc">GHL Location ID</label></th>
                <td><input type="text" id="sapn_ghl_loc" name="<?= SAPN_OPTION ?>[ghl_location_id]" value="<?= esc_attr( $s['ghl_location_id'] ) ?>" class="regular-text" style="font-family:monospace;" placeholder="AbCdEfGhIj123"/></td>
            </tr>
        </table>

        <h2>Branding & Copy</h2>
        <table class="form-table" role="presentation">
            <tr><th><label>Brand name</label></th><td><input type="text" name="<?= SAPN_OPTION ?>[brand_name]" value="<?= esc_attr( $s['brand_name'] ) ?>" class="regular-text"/></td></tr>
            <tr><th><label>City</label></th><td><input type="text" name="<?= SAPN_OPTION ?>[brand_city]" value="<?= esc_attr( $s['brand_city'] ) ?>" class="regular-text"/></td></tr>
            <tr><th><label>Hero eyebrow</label></th><td><input type="text" name="<?= SAPN_OPTION ?>[hero_eyebrow]" value="<?= esc_attr( $s['hero_eyebrow'] ) ?>" class="regular-text"/></td></tr>
            <tr><th><label>Hero heading</label></th><td><input type="text" name="<?= SAPN_OPTION ?>[hero_heading]" value="<?= esc_attr( $s['hero_heading'] ) ?>" class="regular-text"/></td></tr>
            <tr><th><label>Hero subheading</label></th><td><textarea name="<?= SAPN_OPTION ?>[hero_subheading]" class="large-text" rows="3"><?= esc_textarea( $s['hero_subheading'] ) ?></textarea></td></tr>
            <tr><th><label>Hero CTA text</label></th><td><input type="text" name="<?= SAPN_OPTION ?>[hero_cta]" value="<?= esc_attr( $s['hero_cta'] ) ?>" class="regular-text"/></td></tr>
            <tr><th><label>Intro heading</label></th><td><input type="text" name="<?= SAPN_OPTION ?>[intro_heading]" value="<?= esc_attr( $s['intro_heading'] ) ?>" class="regular-text"/></td></tr>
            <tr><th><label>Intro subtext</label></th><td><textarea name="<?= SAPN_OPTION ?>[intro_sub]" class="large-text" rows="3"><?= esc_textarea( $s['intro_sub'] ) ?></textarea></td></tr>
            <tr><th><label>Intro CTA text</label></th><td><input type="text" name="<?= SAPN_OPTION ?>[intro_cta]" value="<?= esc_attr( $s['intro_cta'] ) ?>" class="regular-text"/></td></tr>
        </table>

        <h2>Results page CTAs</h2>
        <p style="margin-top:-6px;color:#64748b;font-size:12px;">Two buttons appear at the bottom of the results screen. By default they're "Submit Estimate" → your thank-you page and "Contact Us" → your contact page.</p>
        <table class="form-table" role="presentation">
            <tr><th><label>Submit-estimate button text</label></th><td><input type="text" name="<?= SAPN_OPTION ?>[result_book_label]" value="<?= esc_attr( $s['result_book_label'] ) ?>" class="regular-text"/></td></tr>
            <tr><th><label>Submit-estimate button URL</label></th><td><input type="url" name="<?= SAPN_OPTION ?>[result_book_url]" value="<?= esc_attr( $s['result_book_url'] ) ?>" class="regular-text" placeholder="https://example.com/thank-you"/><p class="description">Optional. If empty, falls back to the Thank-you page URL below.</p></td></tr>
            <tr><th><label>Contact-us button text</label></th><td><input type="text" name="<?= SAPN_OPTION ?>[result_insurance_label]" value="<?= esc_attr( $s['result_insurance_label'] ) ?>" class="regular-text"/></td></tr>
            <tr><th><label>Contact-us button URL</label></th><td><input type="url" name="<?= SAPN_OPTION ?>[result_insurance_url]" value="<?= esc_attr( $s['result_insurance_url'] ) ?>" class="regular-text" placeholder="https://example.com/contact-us"/></td></tr>
        </table>

        <h2>"Next steps" card on results screen</h2>
        <p style="margin-top:-6px;color:#64748b;font-size:12px;">A short guidance card with up to three links, shown under the estimate. Useful for sleep-study handoff, intake questionnaires, or "learn more" links.</p>
        <table class="form-table" role="presentation">
            <tr><th><label>Show this card</label></th><td><label><input type="checkbox" name="<?= SAPN_OPTION ?>[next_steps_show]" value="1" <?= checked( $s['next_steps_show'], '1', false ) ?>/> Show the "Next steps" card on the results screen.</label></td></tr>
            <tr><th><label>Heading</label></th><td><input type="text" name="<?= SAPN_OPTION ?>[next_steps_heading]" value="<?= esc_attr( $s['next_steps_heading'] ) ?>" class="regular-text"/></td></tr>
            <tr><th><label>Body text</label></th><td><textarea name="<?= SAPN_OPTION ?>[next_steps_body]" class="large-text" rows="3"><?= esc_textarea( $s['next_steps_body'] ) ?></textarea><p class="description">Plain text. The two questionnaire links are appended after this on the same line.</p></td></tr>
            <tr><th><label>Link 1 label / URL</label></th><td><input type="text" name="<?= SAPN_OPTION ?>[next_steps_link1_label]" value="<?= esc_attr( $s['next_steps_link1_label'] ) ?>" class="regular-text" placeholder="Berlin Questionnaire"/><br><input type="url" name="<?= SAPN_OPTION ?>[next_steps_link1_url]" value="<?= esc_attr( $s['next_steps_link1_url'] ) ?>" class="regular-text" placeholder="https://…" style="margin-top:4px;"/></td></tr>
            <tr><th><label>Link 2 label / URL</label></th><td><input type="text" name="<?= SAPN_OPTION ?>[next_steps_link2_label]" value="<?= esc_attr( $s['next_steps_link2_label'] ) ?>" class="regular-text" placeholder="STOP-Bang Questionnaire"/><br><input type="url" name="<?= SAPN_OPTION ?>[next_steps_link2_url]" value="<?= esc_attr( $s['next_steps_link2_url'] ) ?>" class="regular-text" placeholder="https://…" style="margin-top:4px;"/></td></tr>
            <tr><th><label>"Not sure where to start?" hint</label></th><td><input type="text" name="<?= SAPN_OPTION ?>[next_steps_hint]" value="<?= esc_attr( $s['next_steps_hint'] ) ?>" class="regular-text"/></td></tr>
            <tr><th><label>Link 3 label / URL</label></th><td><input type="text" name="<?= SAPN_OPTION ?>[next_steps_link3_label]" value="<?= esc_attr( $s['next_steps_link3_label'] ) ?>" class="regular-text" placeholder="Learn how ProSomnus treatment works"/><br><input type="url" name="<?= SAPN_OPTION ?>[next_steps_link3_url]" value="<?= esc_attr( $s['next_steps_link3_url'] ) ?>" class="regular-text" placeholder="https://…" style="margin-top:4px;"/></td></tr>
        </table>

        <h2>Submission</h2>
        <table class="form-table" role="presentation">
            <tr><th><label>Thank-you page URL (optional)</label></th><td><input type="url" name="<?= SAPN_OPTION ?>[success_redirect_url]" value="<?= esc_attr( $s['success_redirect_url'] ) ?>" class="regular-text" placeholder="https://example.com/thank-you"/><p class="description">URL behind the primary <strong>"Submit Estimate"</strong> button on the results screen. (Falls back to the "Submit-estimate button URL" below if that one's set.)</p></td></tr>
            <tr><th><label>GHL contact tag</label></th><td><input type="text" name="<?= SAPN_OPTION ?>[lead_tag]" value="<?= esc_attr( $s['lead_tag'] ) ?>" class="regular-text"/></td></tr>
            <tr><th><label>GHL contact source</label></th><td><input type="text" name="<?= SAPN_OPTION ?>[lead_source]" value="<?= esc_attr( $s['lead_source'] ) ?>" class="regular-text"/></td></tr>
            <tr><th><label>Enable honeypot</label></th><td><label><input type="checkbox" name="<?= SAPN_OPTION ?>[spam_honeypot]" value="1" <?= checked( $s['spam_honeypot'], '1', false ) ?>/> Drop submissions where the hidden field is filled.</label></td></tr>
            <tr>
                <th><label>Hide site chrome</label></th>
                <td>
                    <label><input type="checkbox" name="<?= SAPN_OPTION ?>[hide_header]" value="1" <?= checked( $s['hide_header'], '1', false ) ?>/> Hide the WordPress theme's <strong>header</strong> on pages with this form.</label><br>
                    <label><input type="checkbox" name="<?= SAPN_OPTION ?>[hide_footer]" value="1" <?= checked( $s['hide_footer'], '1', false ) ?>/> Hide the WordPress theme's <strong>footer</strong> on pages with this form.</label>
                    <p class="description">Per-shortcode override: <code>[sleep_apnea_form hide_header="1" hide_footer="0"]</code></p>
                </td>
            </tr>
        </table>

        <?php submit_button( 'Save Settings' ); ?>
    </form>
    <?php
}

// ─── GHL Fields tab ─────────────────────────────────────────────
function sapn_render_ghl_fields_tab( $s ) {
    $location_id  = $s['ghl_location_id'] ?? '';
    $saved_fids   = sapn_get_folder_ids( $location_id );
    $folder_names = sapn_folder_names();
    ?>
    <h2 style="margin-top:0;">GHL Custom Fields</h2>
    <p style="color:#64748b;">Check, create, and organise the custom fields this plugin uses in GoHighLevel.</p>

    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:16px 20px;margin-bottom:22px;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:14px;">
            <div>
                <strong style="font-size:13px;color:#1e293b;">Folder IDs</strong>
                <div style="margin-top:8px;font-size:11px;color:#64748b;line-height:1.9;">
                    <strong style="color:#0f172a;font-size:11.5px;">One-time setup — follow these steps in order:</strong><br>
                    <strong style="color:#2563eb;">Step 1.</strong> In GHL → Settings → Custom Fields → create these folders named exactly:&nbsp;
                    <?php foreach ( $folder_names as $fn ): ?>
                        <code style="background:#e2e8f0;padding:1px 5px;border-radius:3px;"><?= esc_html( $fn ) ?></code>&nbsp;
                    <?php endforeach; ?><br>
                    <strong style="color:#2563eb;">Step 2.</strong> Click <strong>+ Create Checker Fields</strong> — temporary marker fields will appear in GHL under Additional Info<br>
                    <strong style="color:#2563eb;">Step 3.</strong> In GHL, drag each checker field into its matching folder (names make it obvious)<br>
                    <strong style="color:#2563eb;">Step 4.</strong> Click <strong>Auto-detect</strong> — folder IDs are found and saved automatically<br>
                    <strong style="color:#2563eb;">Step 5.</strong> Click <strong>Move All to Folders</strong> — all fields are organised into their folders<br>
                    <strong style="color:#2563eb;">Step 6.</strong> Click <strong>Delete Checkers</strong> — temporary marker fields removed from GHL
                </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-end;">
                <button type="button" id="sapn-create-checkers-btn" class="button button-primary" style="font-size:11px;padding:4px 14px;white-space:nowrap;">&#43; Create Checker Fields</button>
                <button type="button" id="sapn-detect-folders-btn" class="button" style="font-size:11px;padding:4px 14px;white-space:nowrap;">&#128269; Auto-detect</button>
                <button type="button" id="sapn-delete-checkers-btn" class="button" style="font-size:11px;padding:4px 14px;white-space:nowrap;color:#dc2626;border-color:#fca5a5;">&#128465; Delete Checkers</button>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:10px;">
            <?php foreach ( $folder_names as $fn ): $sk = str_replace( ' ', '_', strtolower( $fn ) ); ?>
                <label style="font-size:12px;color:#374151;">
                    <?= esc_html( $fn ) ?>
                    <input type="text" id="sapn-fid-<?= esc_attr( $sk ) ?>" placeholder="folder ID…"
                           value="<?= esc_attr( $saved_fids[ $fn ] ?? '' ) ?>"
                           style="display:block;width:100%;margin-top:4px;font-family:monospace;font-size:11px;padding:4px 7px;border:1px solid #cbd5e1;border-radius:4px;box-sizing:border-box;">
                </label>
            <?php endforeach; ?>
        </div>
        <div style="margin-top:12px;padding:10px 12px;background:#f1f5f9;border-radius:6px;font-size:11px;color:#374151;">
            <strong>Paste GHL URL to extract ID:</strong>
            <div style="display:flex;gap:8px;margin-top:6px;">
                <input type="text" id="sapn-folder-url-input" placeholder="Paste GHL URL here (e.g. …?folderId=AbCdEf…)" style="flex:1;font-size:11px;padding:4px 7px;border:1px solid #cbd5e1;border-radius:4px;">
                <select id="sapn-folder-url-target" style="font-size:11px;padding:4px 7px;border:1px solid #cbd5e1;border-radius:4px;">
                    <?php foreach ( $folder_names as $fn ): $sk = str_replace( ' ', '_', strtolower( $fn ) ); ?>
                        <option value="<?= esc_attr( $sk ) ?>"><?= esc_html( $fn ) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="button" id="sapn-folder-url-btn" class="button" style="font-size:11px;padding:3px 10px;">Extract</button>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;margin-top:12px;">
            <button type="button" id="sapn-save-folder-ids-btn" class="button button-secondary" style="font-size:12px;padding:4px 14px;">&#10003; Save Folder IDs</button>
            <span id="sapn-folder-ids-status" style="font-size:11px;color:#6b7280;"></span>
        </div>
    </div>

    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:18px;">
        <button type="button" id="sapn-fields-check-btn" class="button button-primary" style="font-size:13px;padding:6px 18px;">&#9654; Check Fields</button>
        <button type="button" id="sapn-fields-create-all-btn" class="button" style="font-size:13px;padding:6px 18px;display:none;">&#43; Create All Missing</button>
        <button type="button" id="sapn-fields-move-btn" class="button" style="font-size:13px;padding:6px 18px;">&#8594; Move All to Folders</button>
        <span id="sapn-fields-status" style="font-size:12px;color:#6b7280;"></span>
    </div>

    <table id="sapn-fields-table" style="width:100%;border-collapse:collapse;font-size:13px;display:none;">
        <thead>
            <tr style="border-bottom:2px solid #e5e7eb;">
                <th style="text-align:left;padding:8px 12px;color:#374151;font-weight:600;">Status</th>
                <th style="text-align:left;padding:8px 12px;color:#374151;font-weight:600;">Field Name</th>
                <th style="text-align:left;padding:8px 12px;color:#374151;font-weight:600;">Field Key</th>
                <th style="text-align:left;padding:8px 12px;color:#374151;font-weight:600;">Folder</th>
                <th style="text-align:left;padding:8px 12px;color:#374151;font-weight:600;">Action</th>
            </tr>
        </thead>
        <tbody id="sapn-fields-tbody"></tbody>
    </table>

    <script>
    (function(){
        var NONCE        = '<?= wp_create_nonce( 'sapn_fields_nonce' ) ?>';
        var AJAX         = '<?= admin_url( 'admin-ajax.php' ) ?>';
        var FOLDER_KEYS  = <?= wp_json_encode( array_map( fn($n) => str_replace( ' ', '_', strtolower( $n ) ), $folder_names ) ) ?>;

        function setStatus(el, msg, color) { el.textContent = msg; el.style.color = color || '#6b7280'; }

        // Save folder IDs
        document.getElementById('sapn-save-folder-ids-btn').addEventListener('click', function(){
            var btn = this, fst = document.getElementById('sapn-folder-ids-status');
            btn.disabled = true; setStatus(fst, 'Saving…');
            var body = 'action=sapn_save_folder_ids&nonce=' + NONCE;
            FOLDER_KEYS.forEach(function(k){
                var val = (document.getElementById('sapn-fid-' + k) || {}).value || '';
                body += '&folder_' + k + '=' + encodeURIComponent(val);
            });
            fetch(AJAX, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: body })
                .then(function(r){ return r.json(); })
                .then(function(res){
                    btn.disabled = false;
                    setStatus(fst, res.success ? '✓ Saved' : '✗ ' + (res.data || 'Error'), res.success ? '#16a34a' : '#dc2626');
                }).catch(function(){ btn.disabled=false; setStatus(fst, '✗ Request failed', '#dc2626'); });
        });

        // URL extractor
        document.getElementById('sapn-folder-url-btn').addEventListener('click', function(){
            var url = document.getElementById('sapn-folder-url-input').value.trim();
            var target = document.getElementById('sapn-folder-url-target').value;
            var fst = document.getElementById('sapn-folder-ids-status');
            var m = url.match(/[?#&/]folderId[=/]([A-Za-z0-9_-]+)/i) ||
                    url.match(/folder[_-]?id[=:/]([A-Za-z0-9_-]+)/i) ||
                    url.match(/\/([A-Za-z0-9]{15,25})(?:[/?#]|$)/);
            if (!m) { setStatus(fst, '✗ No folder ID found in URL', '#dc2626'); return; }
            var el = document.getElementById('sapn-fid-' + target);
            if (el) { el.value = m[1]; setStatus(fst, '✓ Extracted: ' + m[1], '#16a34a'); }
            document.getElementById('sapn-folder-url-input').value = '';
        });

        // Create checker fields
        document.getElementById('sapn-create-checkers-btn').addEventListener('click', function(){
            var btn = this, fst = document.getElementById('sapn-folder-ids-status');
            btn.disabled = true; setStatus(fst, 'Creating checker fields…');
            fetch(AJAX, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
                body: 'action=sapn_create_checker_fields&nonce=' + NONCE })
                .then(function(r){ return r.json(); }).then(function(res){
                    btn.disabled = false;
                    if (res.success) {
                        var msg = '✓ Created ' + res.data.created.length + ' checker fields';
                        if (res.data.errors.length) msg += ' — ' + res.data.errors.length + ' error(s): ' + res.data.errors.join(' | ');
                        else msg += '. Now drag each into its folder, then click Auto-detect.';
                        setStatus(fst, msg, res.data.errors.length ? '#d97706' : '#16a34a');
                    } else setStatus(fst, '✗ ' + (res.data || 'Error'), '#dc2626');
                }).catch(function(){ btn.disabled=false; setStatus(fst, '✗ Request failed', '#dc2626'); });
        });

        // Delete checker fields
        document.getElementById('sapn-delete-checkers-btn').addEventListener('click', function(){
            var btn = this, fst = document.getElementById('sapn-folder-ids-status');
            btn.disabled = true; setStatus(fst, 'Deleting checker fields…');
            fetch(AJAX, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
                body: 'action=sapn_delete_checker_fields&nonce=' + NONCE })
                .then(function(r){ return r.json(); }).then(function(res){
                    btn.disabled = false;
                    if (res.success) setStatus(fst, '✓ Deleted ' + res.data.deleted.length + ' checker field(s).', res.data.errors.length ? '#d97706' : '#16a34a');
                    else setStatus(fst, '✗ ' + (res.data || 'Error'), '#dc2626');
                }).catch(function(){ btn.disabled=false; setStatus(fst, '✗ Request failed', '#dc2626'); });
        });

        // Auto-detect folder IDs
        document.getElementById('sapn-detect-folders-btn').addEventListener('click', function(){
            var btn = this, fst = document.getElementById('sapn-folder-ids-status');
            btn.disabled = true; setStatus(fst, 'Detecting…');
            fetch(AJAX, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
                body: 'action=sapn_detect_folder_ids&nonce=' + NONCE })
                .then(function(r){ return r.json(); }).then(function(res){
                    btn.disabled = false;
                    if (!res.success) { setStatus(fst, '✗ ' + (res.data || 'Error'), '#dc2626'); return; }
                    var byName = res.data.by_name || {};
                    var matches = Object.keys(byName).length;
                    if (matches > 0) {
                        Object.keys(byName).forEach(function(name){
                            var k = name.toLowerCase().replace(/ /g, '_');
                            var el = document.getElementById('sapn-fid-' + k);
                            if (el) el.value = byName[name];
                        });
                        setStatus(fst, '✓ Matched ' + matches + ' of ' + FOLDER_KEYS.length + ' folders by name — saved automatically.', matches === FOLDER_KEYS.length ? '#16a34a' : '#d97706');
                    } else {
                        setStatus(fst, 'No folders detected. Create the folders in GHL, move a field into each, then re-detect.', '#d97706');
                    }
                }).catch(function(){ btn.disabled=false; setStatus(fst, '✗ Request failed', '#dc2626'); });
        });

        // Check fields
        document.getElementById('sapn-fields-check-btn').addEventListener('click', function(){
            var btn = this;
            btn.disabled = true;
            document.getElementById('sapn-fields-status').textContent = 'Checking…';
            document.getElementById('sapn-fields-table').style.display = 'none';
            document.getElementById('sapn-fields-create-all-btn').style.display = 'none';

            fetch(AJAX, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
                body: 'action=sapn_check_ghl_fields&nonce=' + NONCE })
                .then(function(r){ return r.json(); }).then(function(res){
                    btn.disabled = false;
                    if (!res.success) {
                        setStatus(document.getElementById('sapn-fields-status'), '✗ ' + (res.data || 'Error'), '#dc2626');
                        return;
                    }
                    var fields = res.data;
                    var missing = fields.filter(function(f){ return !f.exists; }).length;
                    setStatus(document.getElementById('sapn-fields-status'),
                        (fields.length - missing) + ' / ' + fields.length + ' fields exist' + (missing ? ' — ' + missing + ' missing' : ' ✓'),
                        missing ? '#d97706' : '#16a34a');

                    var tbody = document.getElementById('sapn-fields-tbody');
                    tbody.innerHTML = '';
                    var prev = null;
                    fields.forEach(function(f){
                        if (f.group !== prev) {
                            var gr = document.createElement('tr');
                            gr.innerHTML = '<td colspan="5" style="padding:12px 12px 4px;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:#9ca3af;background:#f9fafb;">' + f.group + '</td>';
                            tbody.appendChild(gr);
                            prev = f.group;
                        }
                        var tr = document.createElement('tr');
                        tr.id = 'sapn-field-row-' + f.key;
                        tr.style.borderBottom = '1px solid #f3f4f6';
                        tr.innerHTML =
                            '<td style="padding:8px 12px;">' + (f.exists ? '<span style="color:#16a34a;font-size:16px;">&#10003;</span>' : '<span style="color:#dc2626;font-size:16px;">&#10007;</span>') + '</td>' +
                            '<td style="padding:8px 12px;color:#111827;">' + f.name + '</td>' +
                            '<td style="padding:8px 12px;font-family:monospace;color:#4b5563;font-size:12px;">' + f.key + '</td>' +
                            '<td style="padding:8px 12px;color:#6b7280;">' + f.group + '</td>' +
                            '<td style="padding:8px 12px;">' + (!f.exists
                                ? '<button type="button" class="button" style="font-size:11px;padding:2px 10px;" onclick="sapnCreateField(\'' + f.key + '\',\'' + f.name.replace(/\'/g,"\\\\'") + '\',\'' + f.group.replace(/\'/g,"\\\\'") + '\',this)">Create</button>'
                                : '') + '</td>';
                        tbody.appendChild(tr);
                    });
                    document.getElementById('sapn-fields-table').style.display = 'table';
                    if (missing) document.getElementById('sapn-fields-create-all-btn').style.display = '';
                });
        });

        document.getElementById('sapn-fields-create-all-btn').addEventListener('click', function(){
            document.querySelectorAll('#sapn-fields-tbody tr[id^="sapn-field-row-"] button.button').forEach(function(b){ b.click(); });
        });

        window.sapnCreateField = function(key, name, folder, btn) {
            btn.disabled = true; btn.textContent = 'Creating…';
            var body = 'action=sapn_create_ghl_field&nonce=' + NONCE
                     + '&field_key=' + encodeURIComponent(key)
                     + '&field_name=' + encodeURIComponent(name)
                     + '&folder='     + encodeURIComponent(folder);
            fetch(AJAX, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: body })
                .then(function(r){ return r.json(); }).then(function(res){
                    if (res.success) {
                        var row = document.getElementById('sapn-field-row-' + key);
                        if (row) row.cells[0].innerHTML = '<span style="color:#16a34a;font-size:16px;">&#10003;</span>';
                        if (row) row.cells[4].innerHTML = '';
                    } else { btn.disabled = false; btn.textContent = 'Retry'; alert(res.data || 'Error creating field'); }
                });
        };

        document.getElementById('sapn-fields-move-btn').addEventListener('click', function(){
            var btn = this, status = document.getElementById('sapn-fields-status');
            btn.disabled = true; setStatus(status, 'Moving fields to folders…');
            fetch(AJAX, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
                body: 'action=sapn_move_ghl_fields&nonce=' + NONCE })
                .then(function(r){ return r.json(); }).then(function(res){
                    btn.disabled = false;
                    if (res.success) {
                        var d = res.data;
                        var msg = '✓ Moved ' + d.moved + ' field(s)';
                        if (d.skipped) msg += ' (' + d.skipped + ' already correct/missing)';
                        if (d.errors && d.errors.length) msg += ' — ' + d.errors.length + ' error(s)';
                        setStatus(status, msg, d.errors && d.errors.length ? '#d97706' : '#16a34a');
                    } else setStatus(status, '✗ ' + (res.data || 'Error'), '#dc2626');
                });
        });
    })();
    </script>
    <?php
}

// ─── Entries tab ────────────────────────────────────────────────
function sapn_render_entries_tab() {
    global $wpdb;
    $table = $wpdb->prefix . 'sapn_entries';
    $base_url = admin_url( 'admin.php?page=' . SAPN_SLUG . '&sapn_tab=entries' );

    // Delete / clear actions
    if ( isset( $_GET['sapn_action'] ) && current_user_can( 'manage_options' ) ) {
        check_admin_referer( 'sapn_entries_action' );
        if ( $_GET['sapn_action'] === 'delete' && ! empty( $_GET['entry_id'] ) ) {
            $wpdb->delete( $table, [ 'id' => absint( $_GET['entry_id'] ) ], [ '%d' ] );
            echo '<div class="notice notice-success is-dismissible"><p>Entry deleted.</p></div>';
        } elseif ( $_GET['sapn_action'] === 'clear_all' ) {
            $wpdb->query( "TRUNCATE TABLE {$table}" );
            echo '<div class="notice notice-success is-dismissible"><p>All entries cleared.</p></div>';
        } elseif ( $_GET['sapn_action'] === 'export_csv' ) {
            $all = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY created_at DESC", ARRAY_A );
            header( 'Content-Type: text/csv; charset=utf-8' );
            header( 'Content-Disposition: attachment; filename="sleep-apnea-entries-' . date('Y-m-d') . '.csv"' );
            $out = fopen( 'php://output', 'w' );
            fputcsv( $out, [ 'ID', 'Date', 'First Name', 'Email', 'Phone', 'GHL Status', 'Meta' ] );
            foreach ( $all as $row ) {
                fputcsv( $out, [ $row['id'], $row['created_at'], $row['first_name'], $row['email'], $row['phone'], $row['ghl_status'], $row['meta'] ] );
            }
            fclose( $out );
            exit;
        }
    }

    $filter_search = sanitize_text_field( $_GET['filter_search'] ?? '' );
    $filter_status = sanitize_text_field( $_GET['filter_status'] ?? '' );
    $per_page      = 25;
    $current_page  = max( 1, absint( $_GET['entries_page'] ?? 1 ) );
    $offset        = ( $current_page - 1 ) * $per_page;

    $where  = 'WHERE 1=1';
    $params = [];
    if ( $filter_status ) { $where .= ' AND ghl_status = %s'; $params[] = $filter_status; }
    if ( $filter_search ) {
        $like = '%' . $wpdb->esc_like( $filter_search ) . '%';
        $where .= ' AND (first_name LIKE %s OR email LIKE %s OR phone LIKE %s)';
        $params[] = $like; $params[] = $like; $params[] = $like;
    }
    $count_sql = $params ? $wpdb->prepare( "SELECT COUNT(*) FROM {$table} {$where}", $params ) : "SELECT COUNT(*) FROM {$table} {$where}";
    $total     = (int) $wpdb->get_var( $count_sql );
    $rows_sql  = $params
        ? $wpdb->prepare( "SELECT * FROM {$table} {$where} ORDER BY created_at DESC LIMIT %d OFFSET %d", array_merge( $params, [ $per_page, $offset ] ) )
        : $wpdb->prepare( "SELECT * FROM {$table} {$where} ORDER BY created_at DESC LIMIT %d OFFSET %d", $per_page, $offset );
    $rows  = $wpdb->get_results( $rows_sql, ARRAY_A );
    $pages = max( 1, ceil( $total / $per_page ) );
    $nonce_url = wp_nonce_url( $base_url, 'sapn_entries_action' );
    ?>
    <h2 style="margin-top:0;">Entries</h2>
    <p style="color:#64748b;">All Sleep Apnea Estimator submissions.</p>

    <style>
        #sapn-entries-table{width:100%;border-collapse:collapse;background:#fff;border:1px solid #c3c4c7;border-radius:4px;}
        #sapn-entries-table th{background:#f6f7f7;padding:9px 12px;font-size:12px;font-weight:700;text-align:left;border-bottom:1px solid #c3c4c7;color:#1d2327;white-space:nowrap;}
        #sapn-entries-table td{padding:9px 12px;font-size:13px;border-bottom:1px solid #f0f0f1;vertical-align:middle;}
        #sapn-entries-table tr:hover td{background:#f9f9f9;}
        .sapn-badge-ok{display:inline-block;background:#dcfce7;color:#166534;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;}
        .sapn-badge-error{display:inline-block;background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;}
        .sapn-detail{display:none;background:#f8fafc;border-top:1px solid #e5e7eb;padding:18px 16px;}
        .sapn-detail-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:14px;}
        .sapn-detail-group{background:#fff;border:1px solid #e5e7eb;border-radius:7px;padding:12px 14px;}
        .sapn-detail-title{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9ca3af;margin-bottom:8px;}
        .sapn-detail-row{display:flex;gap:8px;padding:4px 0;border-bottom:1px solid #f3f4f6;align-items:flex-start;font-size:12px;}
        .sapn-detail-row:last-child{border-bottom:none;}
        .sapn-detail-key{font-weight:600;color:#6b7280;min-width:120px;}
        .sapn-detail-val{color:#111827;word-break:break-word;}
        .sapn-pagination{display:flex;align-items:center;gap:6px;margin-top:14px;font-size:13px;}
        .sapn-pagination a,.sapn-pagination span{padding:5px 10px;border:1px solid #c3c4c7;border-radius:4px;text-decoration:none;color:#2271b1;background:#fff;}
        .sapn-pagination span.current{background:#2271b1;color:#fff;border-color:#2271b1;}
    </style>

    <form method="get" style="display:flex;gap:8px;align-items:center;margin:14px 0 10px;flex-wrap:wrap;">
        <input type="hidden" name="page" value="<?= esc_attr( SAPN_SLUG ) ?>"/>
        <input type="hidden" name="sapn_tab" value="entries"/>
        <input type="text" name="filter_search" value="<?= esc_attr( $filter_search ) ?>" placeholder="Search name, email, phone…" style="padding:6px 10px;border:1px solid #8c8f94;border-radius:4px;font-size:13px;width:240px;"/>
        <select name="filter_status" style="padding:6px 10px;border:1px solid #8c8f94;border-radius:4px;font-size:13px;">
            <option value="">All statuses</option>
            <option value="ok"    <?= selected( $filter_status, 'ok', false ) ?>>GHL OK</option>
            <option value="error" <?= selected( $filter_status, 'error', false ) ?>>GHL Error</option>
        </select>
        <button type="submit" class="button">Filter</button>
        <?php if ( $filter_search || $filter_status ): ?><a href="<?= esc_url( $base_url ) ?>" class="button">Clear</a><?php endif; ?>
        <span style="flex:1;"></span>
        <a href="<?= esc_url( add_query_arg( [ 'sapn_action' => 'export_csv' ], $nonce_url ) ) ?>" class="button">⬇ Export CSV</a>
        <?php if ( $total > 0 ): ?>
            <a href="<?= esc_url( add_query_arg( [ 'sapn_action' => 'clear_all' ], $nonce_url ) ) ?>" class="button" style="color:#b91c1c;border-color:#fca5a5;" onclick="return confirm('Delete ALL <?= $total ?> entries?')">🗑 Clear All</a>
        <?php endif; ?>
    </form>

    <div style="font-size:12px;color:#646970;margin-bottom:8px;">
        Showing <?= $total === 0 ? 0 : $offset + 1 ?>–<?= min( $offset + $per_page, $total ) ?> of <strong><?= $total ?></strong>
    </div>

    <table id="sapn-entries-table">
        <thead><tr><th>Date</th><th>Name</th><th>Email</th><th>Phone</th><th>Range</th><th>GHL</th><th>Actions</th></tr></thead>
        <tbody>
        <?php if ( empty( $rows ) ): ?>
            <tr><td colspan="7" style="text-align:center;padding:28px;color:#646970;">No entries yet.</td></tr>
        <?php else: foreach ( $rows as $row ):
            $meta = json_decode( $row['meta'] ?? '{}', true ) ?: [];
        ?>
            <tr style="cursor:pointer;" onclick="document.getElementById('sapn-detail-<?= $row['id'] ?>').style.display = (document.getElementById('sapn-detail-<?= $row['id'] ?>').style.display === 'table-row' ? 'none' : 'table-row')">
                <td><?= esc_html( date( 'M j, Y g:ia', strtotime( $row['created_at'] ) ) ) ?></td>
                <td><?= esc_html( $row['first_name'] ) ?></td>
                <td><?= esc_html( $row['email'] ) ?></td>
                <td><?= esc_html( $row['phone'] ) ?></td>
                <td><?= esc_html( $meta['estimate_range'] ?? '—' ) ?></td>
                <td><span class="sapn-badge-<?= $row['ghl_status'] === 'ok' ? 'ok' : 'error' ?>"><?= $row['ghl_status'] === 'ok' ? '✓ Sent' : '✗ Error' ?></span></td>
                <td onclick="event.stopPropagation()">
                    <a href="<?= esc_url( add_query_arg( [ 'sapn_action' => 'delete', 'entry_id' => $row['id'] ], $nonce_url ) ) ?>" class="button button-small" style="color:#b91c1c;" onclick="return confirm('Delete this entry?')">Delete</a>
                </td>
            </tr>
            <tr id="sapn-detail-<?= $row['id'] ?>" style="display:none;"><td colspan="7" style="padding:0;">
                <div class="sapn-detail" style="display:block;">
                    <div class="sapn-detail-grid">
                        <div class="sapn-detail-group">
                            <div class="sapn-detail-title">Contact</div>
                            <div class="sapn-detail-row"><span class="sapn-detail-key">Name</span><span class="sapn-detail-val"><?= esc_html( $row['first_name'] ) ?></span></div>
                            <div class="sapn-detail-row"><span class="sapn-detail-key">Email</span><span class="sapn-detail-val"><a href="mailto:<?= esc_attr( $row['email'] ) ?>"><?= esc_html( $row['email'] ) ?></a></span></div>
                            <div class="sapn-detail-row"><span class="sapn-detail-key">Phone</span><span class="sapn-detail-val"><?= esc_html( $row['phone'] ) ?></span></div>
                            <div class="sapn-detail-row"><span class="sapn-detail-key">Submitted</span><span class="sapn-detail-val"><?= esc_html( date( 'M j, Y g:ia', strtotime( $row['created_at'] ) ) ) ?></span></div>
                            <div class="sapn-detail-row"><span class="sapn-detail-key">GHL</span><span class="sapn-detail-val">HTTP <?= esc_html( $meta['_ghl_http_code'] ?? '?' ) ?></span></div>
                            <?php if ( ! empty( $meta['_ghl_response']['contact']['id'] ) ): ?>
                                <div class="sapn-detail-row"><span class="sapn-detail-key">GHL Contact ID</span><span class="sapn-detail-val" style="font-family:monospace;"><?= esc_html( $meta['_ghl_response']['contact']['id'] ) ?></span></div>
                            <?php endif; ?>
                        </div>
                        <div class="sapn-detail-group">
                            <div class="sapn-detail-title">Answers</div>
                            <?php foreach ( [
                                'reason' => 'Reason', 'study' => 'Apnea Status', 'cpap' => 'CPAP Experience',
                                'symptoms' => 'Symptoms', 'airway' => 'Airway Concerns',
                                'estimate_range' => 'Estimate Range', 'contact_pref' => 'Contact Preference', 'benefits' => 'Insurance Benefits',
                            ] as $k => $label ): if ( empty( $meta[ $k ] ) ) continue; ?>
                                <div class="sapn-detail-row"><span class="sapn-detail-key"><?= esc_html( $label ) ?></span><span class="sapn-detail-val"><?= esc_html( $meta[ $k ] ) ?></span></div>
                            <?php endforeach; ?>
                        </div>
                        <?php
                        $utm_present = array_filter( array_intersect_key( $meta, array_flip( [ 'utm_campaign','utm_medium','utm_content','utm_keyword','utm_term','gclid' ] ) ) );
                        if ( ! empty( $utm_present ) ): ?>
                            <div class="sapn-detail-group">
                                <div class="sapn-detail-title">Traffic Source</div>
                                <?php foreach ( $utm_present as $k => $v ): ?>
                                    <div class="sapn-detail-row"><span class="sapn-detail-key"><?= esc_html( $k ) ?></span><span class="sapn-detail-val"><?= esc_html( $v ) ?></span></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ( ! empty( $meta['_ghl_fields_sent'] ) ): ?>
                            <div class="sapn-detail-group">
                                <div class="sapn-detail-title">Custom Fields Sent to GHL</div>
                                <?php foreach ( $meta['_ghl_fields_sent'] as $cf ): ?>
                                    <div class="sapn-detail-row"><span class="sapn-detail-key" style="font-family:monospace;font-size:11px;"><?= esc_html( $cf['key'] ) ?></span><span class="sapn-detail-val"><?= esc_html( $cf['field_value'] ) ?></span></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </td></tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>

    <?php if ( $pages > 1 ): ?>
        <div class="sapn-pagination">
            <?php for ( $p = 1; $p <= $pages; $p++ ): ?>
                <?php if ( $p === $current_page ): ?>
                    <span class="current"><?= $p ?></span>
                <?php else: ?>
                    <a href="<?= esc_url( add_query_arg( [ 'entries_page' => $p, 'filter_search' => $filter_search, 'filter_status' => $filter_status ], $base_url ) ) ?>"><?= $p ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
    <?php
}

// ═══════════════════════════════════════════════════════════════
//  DASHBOARD WIDGET
// ═══════════════════════════════════════════════════════════════
add_action( 'wp_dashboard_setup', function () {
    wp_add_dashboard_widget( 'sapn_dashboard_widget', '🛌 Sleep Apnea GHL — Submissions', 'sapn_render_dashboard_widget' );
} );

function sapn_render_dashboard_widget() {
    global $wpdb;
    $t = $wpdb->prefix . 'sapn_entries';
    $today = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$t} WHERE DATE(created_at) = CURDATE()" );
    $week  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$t} WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)" );
    $month = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$t} WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)" );
    $url   = admin_url( 'admin.php?page=' . SAPN_SLUG . '&sapn_tab=entries' );
    ?>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:10px;">
        <div style="background:#ecfdf5;border-radius:8px;padding:10px;text-align:center;"><div style="font-size:22px;font-weight:700;color:#059669;"><?= $today ?></div><div style="font-size:10px;color:#10b981;font-weight:600;">TODAY</div></div>
        <div style="background:#f0fdf4;border-radius:8px;padding:10px;text-align:center;"><div style="font-size:22px;font-weight:700;color:#16a34a;"><?= $week ?></div><div style="font-size:10px;color:#22c55e;font-weight:600;">THIS WEEK</div></div>
        <div style="background:#faf5ff;border-radius:8px;padding:10px;text-align:center;"><div style="font-size:22px;font-weight:700;color:#7c3aed;"><?= $month ?></div><div style="font-size:10px;color:#a855f7;font-weight:600;">30 DAYS</div></div>
    </div>
    <a href="<?= esc_url( $url ) ?>" style="font-size:12px;color:#2271b1;">View all entries →</a>
    <?php
}

// ═══════════════════════════════════════════════════════════════
//  FRONT-END ASSETS + SHORTCODE
// ═══════════════════════════════════════════════════════════════
function sapn_enqueue_estimator_assets() {
    $ver = wp_get_environment_type() === 'production' ? '1.0.0' : (string) time();
    wp_enqueue_style(  'sapn-estimator', plugins_url( 'assets/sapn-estimator.css', __FILE__ ), [], $ver );
}

add_shortcode( 'sleep_apnea_form', 'sapn_shortcode' );

function sapn_shortcode( $atts ) {
    $atts = shortcode_atts( [ 'hide_header' => null, 'hide_footer' => null, 'hide_chrome' => null ], $atts, 'sleep_apnea_form' );
    sapn_enqueue_estimator_assets();
    $s         = sapn_get();
    $nonce     = wp_create_nonce( 'sapn_submit' );
    $ajax_url  = admin_url( 'admin-ajax.php' );
    $redirect  = $s['success_redirect_url'];

    $truthy = function( $v ) {
        return in_array( strtolower( (string) $v ), [ '1', 'true', 'yes', 'on' ], true );
    };
    // hide_chrome is a legacy shortcut that toggles both header and footer.
    $chrome_attr = $atts['hide_chrome'] !== null ? $truthy( $atts['hide_chrome'] ) : null;
    $hide_header = $atts['hide_header'] !== null
        ? $truthy( $atts['hide_header'] )
        : ( $chrome_attr !== null ? $chrome_attr : ( $s['hide_header'] === '1' ) );
    $hide_footer = $atts['hide_footer'] !== null
        ? $truthy( $atts['hide_footer'] )
        : ( $chrome_attr !== null ? $chrome_attr : ( $s['hide_footer'] === '1' ) );

    ob_start();
    if ( $hide_header || $hide_footer ) {
        $sels = [];
        if ( $hide_header ) {
            $sels = array_merge( $sels, [
                'body > header','#masthead','.site-header','header.site-header',
                'header[role="banner"]','.wp-site-blocks > header',
                '.elementor-location-header','.e-con-header','.fl-page-header',
                '.et_builder_inner_content > header','#wpadminbar',
            ] );
        }
        if ( $hide_footer ) {
            $sels = array_merge( $sels, [
                'body > footer','#colophon','.site-footer','footer.site-footer',
                'footer[role="contentinfo"]','.wp-site-blocks > footer',
                '.elementor-location-footer','.e-con-footer','.fl-page-footer',
                '.et_builder_inner_content > footer',
            ] );
        }
        echo '<style id="sapn-hide-chrome">' . implode( ',', $sels ) . '{display:none!important;}'
            . ( $hide_header ? 'html{margin-top:0!important;}body{padding-top:0!important;}' : '' )
            . '</style>';
    }
    require __DIR__ . '/sleep-apnea-template.php';
    return ob_get_clean();
}
