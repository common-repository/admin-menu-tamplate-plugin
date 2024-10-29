<?php
/*
Plugin Name: Admin Menu Tamplate Plugin
Plugin URI: http://wordpress.org/extend/plugins/admin-menu-tamplate-plugin/
Description: The best plugin template to kick start plugin development.
Author: Mallikarjun Yawalkar
Version: 1.0
Author URI: http://digitalfair.tk
*/   
   
/*  Copyright 2012  

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
* Guess the wp-content and plugin urls/paths
*/
// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );


if (!class_exists('fb_sub')) {
    class fb_sub {
        //This is where the class variables go, don't forget to use @var to tell what they're for
        /**
        * @var string The options string name for this plugin
        */
        var $optionsName = 'fb_sub_options';
        
        /**
        * @var string $localizationDomain Domain used for localization
        */
        var $localizationDomain = "fb_sub";
        
        /**
        * @var string $pluginurl The path to this plugin
        */ 
        var $thispluginurl = '';
        /**
        * @var string $pluginurlpath The path to this plugin
        */
        var $thispluginpath = '';
            
        /**
        * @var array $options Stores the options for this plugin
        */
        var $options = array();
        
        //Class Functions
        /**
        * PHP 4 Compatible Constructor
        */
        function fb_sub(){$this->__construct();}
        
        /**
        * PHP 5 Constructor
        */        
        function __construct(){
            //Language Setup
            $locale = get_locale();
            $mo = dirname(__FILE__) . "/languages/" . $this->localizationDomain . "-".$locale.".mo";
            load_textdomain($this->localizationDomain, $mo);

            //"Constants" setup
            $this->thispluginurl = PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)).'/';
            $this->thispluginpath = PLUGIN_PATH . '/' . dirname(plugin_basename(__FILE__)).'/';
            
            //Initialize the options
            //This is REQUIRED to initialize the options when the plugin is loaded!
            $this->getOptions();
            
            //Actions        
            add_action("admin_menu", array(&$this,"admin_menu_link"));

            
            //Widget Registration Actions
            add_action('plugins_loaded', array(&$this,'register_widgets'));
            
            /*
            add_action("wp_head", array(&$this,"add_css"));
            add_action('wp_print_scripts', array(&$this, 'add_js'));
            */
            
            //Filters
            /*
            add_filter('the_content', array(&$this, 'filter_content'), 0);
            */
        }        
        
        /**
        * Retrieves the plugin options from the database.
        * @return array
        */
        function getOptions() {
            //Don't forget to set up the default options
            if (!$theOptions = get_option($this->optionsName)) {
                $theOptions = array('default'=>'options');
                update_option($this->optionsName, $theOptions);
            }
            $this->options = $theOptions;
            
            //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            //There is no return here, because you should use the $this->options variable!!!
            //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        }
        /**
        * Saves the admin options to the database.
        */
        function saveAdminOptions(){
            return update_option($this->optionsName, $this->options);
        }
        
        /**
        * @desc Adds the options subpanel
        */
        function admin_menu_link() {
            //If you change this from add_options_page, MAKE SURE you change the filter_plugin_actions function (below) to
            //reflect the page filename (ie - options-general.php) of the page your plugin is under!
//            add_options_page('{Full Plugin Name}', '{Full Plugin Name}', 10, basename(__FILE__), array(&$this,'admin_options_page'));

		// Add Main DF-SEO menu
		add_menu_page('Main Menu Settings', 'Template Menu', 'administrator', __FILE__,array(&$this,'admin_options_page'), WP_PLUGIN_URL . '/admin-menu-tamplate-plugin/icon.jpg');
	
		//Add breadcrumb submenu to DF-SEO menu
		add_submenu_page(__FILE__, 'Submenu 1 Settings', 'Menu Item 1','administrator', __FILE__,array(&$this,'admin_options_page'));

		//Add Pagination submenu to DF-SEO menu
		add_submenu_page(__FILE__, 'Submenu 2 Settings', 'Menu Item 2','administrator', __FILE__,array(&$this,'admin_options_page'));


			add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'filter_plugin_actions'), 10, 2 );
        }
        
        /**
        * @desc Adds the Settings link to the plugin activate/deactivate page
        */
        function filter_plugin_actions($links, $file) {
           //If your plugin is under a different top-level menu than Settiongs (IE - you changed the function above to something other than add_options_page)
           //Then you're going to want to change options-general.php below to the name of your top-level page
           $settings_link = '<a href="admin.php?page=admin-menu-tamplate-plugin/' . basename(__FILE__) . '">' . __('Settings') . '</a>';
           array_unshift( $links, $settings_link ); // before other links

           return $links;
        }

        /**
        * Adds settings/options page
        */
        function admin_options_page() { 
            if($_POST['fb_sub_save']){
                if (! wp_verify_nonce($_POST['_wpnonce'], 'fb_sub-update-options') ) die('Whoops! There was a problem with the data you posted. Please go back and try again.'); 
                $this->options['fb_sub_path'] = $_POST['fb_sub_path'];                   
                $this->options['fb_sub_allowed_groups'] = $_POST['fb_sub_allowed_groups'];
                $this->options['fb_sub_enabled'] = ($_POST['fb_sub_enabled']=='on')?true:false;
                                        
                $this->saveAdminOptions();
                
                echo '<div class="updated"><p>Success! Your changes were sucessfully saved!</p></div>';
            }
?>                                   
                <div class="wrap">
                <h2>Admin Menu Tamplate Plugin</h2>
                <form method="post" id="fb_sub_options">
                <?php wp_nonce_field('fb_sub-update-options'); ?>
                    <table width="100%" cellspacing="2" cellpadding="5" class="form-table"> 
                        <tr valign="top"> 
                            <th width="33%" scope="row"><?php _e('Option 1:', $this->localizationDomain); ?></th> 
                            <td><input name="fb_sub_path" type="text" id="fb_sub_path" size="45" value="<?php echo $this->options['fb_sub_path'] ;?>"/>
                        </td> 
                        </tr>
                        <tr valign="top"> 
                            <th width="33%" scope="row"><?php _e('Option 2:', $this->localizationDomain); ?></th> 
                            <td><input name="fb_sub_allowed_groups" type="text" id="fb_sub_allowed_groups" value="<?php echo $this->options['fb_sub_allowed_groups'] ;?>"/>
                            </td> 
                        </tr>
                        <tr valign="top"> 
                            <th><label for="fb_sub_enabled"><?php _e('CheckBox #1:', $this->localizationDomain); ?></label></th><td><input type="checkbox" id="fb_sub_enabled" name="fb_sub_enabled" <?=($this->options['fb_sub_enabled']==true)?'checked="checked"':''?>></td>
                        </tr>
                        <tr>
                            <th colspan=2><input type="submit" name="fb_sub_save" value="Save" /></th>
                        </tr>
                    </table>
                </form>
                <?php
        }
        
        /*
        * ============================
        * Plugin Widgets
        * ============================
        */                        
        function register_widgets() {
            //Make sure the widget functions exist
            if ( function_exists('wp_register_sidebar_widget') ) {
                //============================
                //Example Widget 1
                //============================
                function display_fb_sub_widget($args) {                    
                    extract($args);
                    echo $before_widget . $before_title . $this->options['title'] . $after_title;
                    echo '<ul>';
                    //!!! Widget 1 Display Code Goes Here!
                    echo '</ul>';
                    echo $after_widget;
                }                                                                             
                function fb_sub_widget_control() {            
                    if ( $_POST["fb_sub_fb_sub_widget_submit"] ) {
                        $this->options['fb_sub-comments-title'] = stripslashes($_POST["fb_sub-comments-title"]);        
                        $this->options['fb_sub-comments-template'] = stripslashes($_POST["fb_sub-comments-template"]);
                        $this->options['fb_sub-hide-admin-comments'] = ($_POST["fb_sub-hide-admin-comments"]=='on'?'':'1');
                        $this->saveAdminOptions();
                    }                                                                  
                    $title = htmlspecialchars($options['fb_sub-comments-title'], ENT_QUOTES);
                    $template = htmlspecialchars($options['fb_sub-comments-template'], ENT_QUOTES);
                    $hide_admin_comments = $options['fb_sub-hide-admin-comments'];      
                ?>
                    <p><label for="fb_sub-comments-title"><?php _e('Title:', $this->localizationDomain); ?> <input style="width: 250px;" id="fb_sub-comments-title" name="fb_sub-comments-title" type="text" value="<?= $title; ?>" /></label></p>               
                    <p><label for="fb_sub-comments-template"><?php _e('Template:', $this->localizationDomain); ?> <input style="width: 250px;" id="fb_sub-comments-template" name="fb_sub-comments-template" type="text" value="<?= $template; ?>" /></label></p>
                    <p><?php _e('The template is made up of HTML and tokens. You can get a list of available tokens at the', $this->localizationDomain); ?> <a href='http://pressography.com/plugins/wp-fb_sub/#tokens-recent' target='_blank'><?php _e('plugin page', $this->localizationDomain); ?></a></p>
                    <p><input id="fb_sub-hide-admin-comments" name="fb_sub-hide-admin-comments" type="checkbox" <?= ($hide_admin_comments=='1')?'':'checked="CHECKED"'; ?> /> <label for="fb_sub-hide-admin-comments"><?php _e('Show Admin Comments', $this->localizationDomain); ?></label></p>
                    <input type="hidden" id="fb_sub_fb_sub_widget_submit" name="fb_sub_fb_sub_widget_submit" value="1" />
                <?php
                }
                $widget_ops = array('classname' => 'fb_sub_widget', 'description' => __( 'Widget Description', $this->localizationDomain ) );
                wp_register_sidebar_widget('fb_sub-fb_sub_widget', __('Widget Title', $this->localizationDomain), array($this, 'display_fb_sub_widget'), $widget_ops);
                wp_register_widget_control('fb_sub-fb_sub_widget', __('Widget Title', $this->localizationDomain), array($this, 'fb_sub_widget_control'));
                
            }  
        }       
        
  } //End Class
} //End if class exists statement

//instantiate the class
if (class_exists('fb_sub')) {
    $fb_sub_var = new fb_sub();
}
?>