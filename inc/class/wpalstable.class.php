<?php

class Wpalstable
{
    public static function addCols() {
        $post_type = array(
            'post',
            'page'
        );

        foreach ( $post_type as $type ) {
            add_filter( "manage_{$type}_posts_columns", function ( $defaults ) {
                $defaults['shortened-link'] = 'Shortened Link';
                return $defaults;
            } );
    
            add_action( "manage_{$type}_posts_custom_column", function ( $column_name, $post_id ) {
            if ( $column_name == 'shortened-link' ) {
                $shortened = get_post_meta($post_id, '_wpals_meta_shorten');
                if( array_key_exists(0, $shortened) ) {

                    echo '<a href="'.esc_html($shortened[0]).'" target="_blank">'.esc_html($shortened[0]).'</a>';
                }
                
            }
            }, 10, 2 );
        }

        

        
    }
}