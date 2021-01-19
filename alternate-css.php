<?php
/*
 * Plugin Name: Alternate CSS
 * Plugin URI:  https://wordpress.org/plugins/alternate-css/
 * Description: Load alternate CSS for accessibility or theming purposes.
 * Version:     1.0
 * Author:      SatelliteWP
 * Author URI:  https://satellitewp.com/en
 * Text Domain: alternate-css
 * Requires PHP: 7.0
 * Requires at least: 4.9
 * License:     GPL-3.0+
 */

 namespace satellitewp;

 class Alternate_CSS {

    /**
     * Constructor
     */
    public function __construct() {

    }

    /**
     * Starts everything...
     */
    public function start() {
        add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
    }

    /**
     * Ennqueue JS & CSS
     */
    public function wp_enqueue_scripts() {
        global $wp_styles;

        $to_enqueue = $this->get_alternate_styles();
        foreach($to_enqueue as $style) {
            $key = (empty( $style['directory'] ) ? $style['id'] : $style['directory'] . '-' . $style['id'] );
            wp_enqueue_style( $key, $style['url'] );
            
            $wp_styles->add_data( $key , 'title', $style['title'] );
            $wp_styles->add_data( $key , 'alt', 'yes' );
        }

        wp_enqueue_script( 'switcherjs', plugin_dir_url( __FILE__ ) . 'js/switcher.js', array( 'jquery' ) );
    }

    /**
     * Get alternate stylesheets
     * 
     * @return array Array of what needs to be enqueued
     */
    public function get_alternate_styles() {
        $result = array();

        $directories = array();
        $directories[] = "";
        $directories = apply_filters( 'alternate_css_directories', $directories );
        
        foreach( $directories as $directory ) {
            $abs_directory = get_stylesheet_directory() . '/' . $directory;
            $files = array_diff( scandir( $abs_directory ), array( '.', '..' ) );

            $starts_with = 'style-alt-';
            $starts_with_length = strlen( $starts_with );
            foreach( $files as $file ) {
                $parts = pathinfo( $file );

                if ( is_file( $abs_directory . '/' . $file )
                        && $parts['extension'] == 'css' 
                        && substr( $parts['filename'], 0 , $starts_with_length ) == $starts_with ) {
                    
                    $min = str_replace('.css', '.min.css',  $file );

                    // If minimized version exists, skip this one
                    if ( in_array( $min, $files ) ) continue;
                    
                    $pos_point = strpos( $parts['filename'], '.', $starts_with_length + 1 );
                    $pos_dash = strpos( $parts['filename'], '-', $starts_with_length + 1 );
    
                    $id = null;
                    $title = null;
                    if ( $pos_point === false && $pos_dash === false ) {
                        $title = $parts['filename'];
                        $id = $parts['filename'];
                    }
                    elseif ( $pos_point === false ) {
                        $title = substr( $parts['filename'], 0, $pos_dash );
                        $id = $parts['filename'];
                    }
                    elseif ( $pos_dash === false ) {
                        $title = substr( $parts['filename'], 0, $pos_point );
                        $id = substr( $parts['filename'], 0, $pos_point );
                    }
                    else {
                        $title = substr( $parts['filename'], 0, $pos_dash );
                        $id = substr( $parts['filename'], 0, $pos_point );
                    }
    
                    $data = array(
                        'title' => $title,
                        'id' => $id,
                        'directory' => $directory,
                        'url' => get_stylesheet_directory_uri() . ( empty( $directory ) ? '' : '/' . $directory ) . '/' . $file 
                    );

                    $result[] = $data;
                }
            }
        }

        return $result;
    }
 }

 $obj = new Alternate_CSS();
 $obj->start();
