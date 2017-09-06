<?php
/**
 * This class allows you to update themes and plugin through hosted versions on GitHub
 */
namespace WP_Updater;
use WP_Error as WP_Error;

class Boot {
    
    /**
     * The default parameters for running the updater
     * @access private
     */
    private $params;
    
	/**
	 * Stores data retrieved from GitHub
	 * @access private
	 */
	private $github;    
    
    /**
     * Constructor
     * The constructor accepts for now the github url
     *
     * @param array $params The configuration parameters to let this updater work.
     *
     * @return void
     */
    public function __construct( $params ) {
        
        // Default parameters 
        $defaults = array(
            'url'       => '',
            'type'      => 'theme',
            'verifySSL' => true
        );
        
        $this->params = wp_parse_args( $params, $defaults );
        
        // If we are missing parameters, bail out.
        $check = $this->checkParameters();
        
        if( is_wp_error( $check ) ) {
            echo $check->get_error_message();
            return;
        }
        
        // Runs the scripts for updating a theme
        if( $this->params['type'] == 'theme' )
            $this->updateTheme();
        
        // Runs the scripts for updating a plugin
        if( $this->params['type'] == 'plugin' )
            $this->updatePlugin();
        
        /**
         * Check if we need to verify SSL
         *
         * @param array $args The arguments for the verification
         * @param string $url The url
         *
         * @return array $args
         */
        if( $this->params['verifySSL'] ) {
            add_filter( 'http_request_args', function( $args, $url ) {
                $args[ 'sslverify' ] = $this->config[ 'sslverify' ];
                return $args;            
            }, 10, 2 );
        }
        
    }   
    
    /**
     * Checks our parameters and see if we have everything
     *
     * @return boolean true upon success, object WP_Error upon failure
     */
    private function checkParameters() {
        return true;
        
        return new WP_Error( 'missing', __( "You are missing essential parameters.", "wp-updater" ) );
        
    }
    
    /**
     * Updates a theme
     */
    private function updateTheme() {
        new Themes( $this->params );      
    }
    
    /**
     * Updates a plugin
     */
    private function updatePlugin() {
        new Plugins( $this->params );    
    }
    
}