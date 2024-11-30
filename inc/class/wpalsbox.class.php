<?php


class Wpalsbox
{
    public function __construct()
    {
        
    }

    public static function add_custom_box() {
        $screens = [ 'post', 'page' ];
        foreach ( $screens as $screen ) {
            add_meta_box(
                'wpals_box_id',                 // Unique ID
                'URL Shortened',      // Box title
                'Wpalsbox::custom_box_html',  // Content callback, must be of type callable
                $screen                            // Post type
            );
        }
    }

    public static function custom_box_html( $post ) {
        $value = get_post_meta( $post->ID, '_wpals_meta_shorten', true );
        ?>
        <label for="wpals_shortened">Shortened Link</label>
        <input type="text" name="wpals_shortened" class="components-text-control__input" value="<?php echo esc_html($value); ?>">
        
        <?php
    }

    public static function save_postdata( $post_id ) {
        $url = site_url()."?p=".$post_id;
        $nonce = isset($_POST['_wpnonce']) ? esc_url_raw( wp_unslash($_POST['_wpnonce'])): false;

        if ( FALSE != $nonce && ! wp_verify_nonce( $nonce ) ) {

            die( 'Security check' ); 

        } else {

            if( isset($_POST['wpals_shortened']) && $_POST['wpals_shortened'] == "" ) {
                $shortener = get_option('wpals_shortener');
                if( $shortener == "bitly" ) {
                    $shortened = Wpals::getBitly($url);
                } else if ( $shortener == "tinyurl" ) {
                    $shortened = Wpals::tinyurl($url);
                } else {
                    $shortened = esc_url_raw(wp_unslash($_POST['wpals_shortened']));
                }
            } else {
                $shortened = "";
            }
            
            if ( array_key_exists( 'wpals_shortened', $_POST ) ) {
                update_post_meta(
                    $post_id,
                    '_wpals_meta_shorten',
                    $shortened
                );
            }
        }
        
    }


    
    
    
}