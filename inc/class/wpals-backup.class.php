<?php

if ( ! class_exists( 'Wpals' ) ) {

    class Wpals 
    {

        function __construct()
        {

            register_activation_hook(
                __FILE__,
                'wpals_activate'
            );
            
            register_deactivation_hook(
                __FILE__,
                'wpals_deactivate'
            );
            
            
            // self::registerOptions();

            add_action( 'admin_init', 'Wpals::registerOptions' );
            add_action( 'admin_init', 'Wpalstable::addCols' );
            add_action( 'admin_menu', 'Wpals::wpals_options_page' );

            add_action( 'add_meta_boxes', 'Wpalsbox::add_custom_box' );
            add_action( 'save_post', 'Wpalsbox::save_postdata' );
            
        }

        public static function registerOptions () {
            $args = array(
                'type' => 'string', 
                'sanitize_callback' => 'sanitize_text_field',
                'default' => NULL,
                );
            register_setting('wpals_options', 'wpals_shortener', $args);

            $args = array(
                'type' => 'string', 
                'sanitize_callback' => 'sanitize_text_field',
                'default' => NULL,
                );
            register_setting('wpals_options', 'wpals_apikey', $args);


            $args = array(
                'type' => 'string', 
                'sanitize_callback' => 'sanitize_text_field',
                'default' => NULL,
                );
            register_setting('wpals_options', 'wpals_bitly_guid', $args);

            
            add_settings_section(
                'wpals_settings_section',
                'WPALS Settings', 'Wpals::settings_section_callback',
                'wpals_options'
            );

            add_settings_field(
                'wpals_settings_field_shortener',
                'URL Shortener', 'Wpals::shortener_callback',
                'wpals_options',
                'wpals_settings_section'
            );

            add_settings_field(
                'wpals_settings_field_apikey',
                'API KEY (Bitly)', 'Wpals::apikey_callback',
                'wpals_options',
                'wpals_settings_section'
            );

            add_settings_field(
                'wpals_settings_field_bitly_guid',
                'Bitly Group ID', 'Wpals::bitly_guid_callback',
                'wpals_options',
                'wpals_settings_section'
            );
        }

        public static function shortener_callback() {
            $short = self::tinyurl("https://www.detik.com");
            // get the value of the setting we've registered with register_setting()
            $setting = get_option('wpals_shortener');
            // output the field

            $bitly = $setting == "bitly" ? "selected": "";
            $tinyurl = $setting == "tinyurl" ? "selected": "";
            ?>
            <select name="wpals_shortener">
                <option value="bitly" <?php echo esc_attr( $bitly );?>>Bit.Ly</option>
                <option value="tinyurl" <?php echo esc_attr( $tinyurl );?>>TinyURL</option>
            </select>
            <?php
            print_r($short);
        }

        public static function apikey_callback() {
            // get the value of the setting we've registered with register_setting()
            $setting = get_option('wpals_apikey');
            // output the field
            ?>
            <input type="text" name="wpals_apikey" value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
                
            <?php
        }

        public static function bitly_guid_callback() {
            // get the value of the setting we've registered with register_setting()
            $wpals_bitly_guid = get_option('wpals_bitly_guid');
            // output the field
            ?>
            <input type="text" name="wpals_bitly_guid" value="<?php echo isset( $wpals_bitly_guid ) ? esc_attr( $wpals_bitly_guid ) : ''; ?>">
                
            <?php
        }

        public static function settings_section_callback() {
            echo '<p>URL Shortener Configuration.</p>';
        }

        public static function wpals_options_page_html() {
            ?>
            <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                // output security fields for the registered setting "wporg_options"
                settings_fields( 'wpals_options' );
                // output setting sections and their fields
                // (sections are registered for "wporg", each field is registered to a specific section)
                do_settings_sections( 'wpals_options' );
                // output save settings button
                submit_button( __( 'Save Settings', 'wpals' ) );
                ?>
            </form>
            </div>
            <?php
        }

        
        public static function wpals_options_page() {
            add_menu_page(
                'WPALS',
                'WPALS Options',
                'manage_options',
                'wpals',
                'Wpals::wpals_options_page_html',
                plugins_url('wpals/assets/icon-wpals-plugin.png'),
                20
            );
        }

        public static function activate () {
            echo "activate";

        }

        public static function deactivate () {

        }

        public static function fetch ($url, $method, $arrval, $auth) {

            // If the API is using a Bearer Token
            $auth_scheme = 'Bearer';
            $api_key     = esc_html($auth['token']);


            $response = wp_remote_get( esc_url($url), array(
                'headers' => array(
                    'Accepts'       => 'application/json',
                    'Authorization' => $auth_scheme . ' ' . $api_key
                ),
            ) );

            if ( is_wp_error( $response ) ) {
                return $response;
            } else {
                $body = wp_remote_retrieve_body( $response );
                $data = json_decode( $body, true );
                
                return $data;
            }

        }

        public static function getBitly ($longurl) {

            $apikey = get_option('wpals_apikey');
            $group_guid = get_option('wpals_bitly_guid');

            $arrval = array(
                'long_url' => $longurl,
                'group_guid' => $group_guid, //'Bobg2vKrKPj'
                'domain' => 'bit.ly'
            );
            $auth = array(
                'status' => TRUE,
                'token' => $apikey
            );
            $req = self::fetch("https://api-ssl.bitly.com/v4/shorten", "POST", $arrval, $auth );

            return $req['link'];

        }

        public static function tinyurl($url) {

            $fp = wp_remote_get (
                esc_url_raw( 'https://www.shareaholic.com/v2/share/shorten_link?url='.$url.'&service=tinyurl'));
            $body = wp_remote_retrieve_body( $fp );
            // $data = json_decode( $fp, true );

            print_r($body);

            // return $data['data'];

        }
    }

}