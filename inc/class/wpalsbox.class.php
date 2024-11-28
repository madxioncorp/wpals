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
        $value = esc_html($value);
        ?>
        <label for="wpals_shortened">Description for this field</label>
        <input type="text" name="wpals_shortened" class="components-text-control__input" value="<?php echo $value; ?>">
        
        <?php
    }

    public static function save_postdata( $post_id ) {
        $url = site_url()."?p=".$post_id;
        if( isset($_POST['wpals_shortened']) && $_POST['wpals_shortened'] == "" ) {
            $shortener = get_option('wpals_shortener');
            if( $shortener == "bitly" ) {
                $shortened = Wpals::getBitly($url);
            } else if ( $shortener == "tinyurl" ) {
                $shortened = Wpals::tinyurl($url);
            } else {
                $shortened = esc_attr($_POST['wpals_shortened']);
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