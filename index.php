<?php
/*
Plugin Name: Link Grab-O-Matic
Plugin URI: http://www.ryanlane.com/apps/linkgrab-o-matic/
Description: Link Grab-O-Matic, is a simple interface to quickly and easily post links to your blog as posts. 
Version: 1.1
Author: Ryan Lane
Author URI: http://www.ryanlane.com/
License: GPLv3
*/

/*  Copyright 2012  Ryan Lane  (email : me@ryanlane.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 3, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

   
function cs_external_permalink($permalink) {
    global $post;
 
    $thePostID = $post->ID;
    $post_id = get_post($thePostID);
    $title = $post_id->post_title;
 
    $post_keys = array(); $post_val  = array();
    $post_keys = get_post_custom_keys($thePostID);
 
    if (!empty($post_keys)) {
        foreach ($post_keys as $pkey) {
            if ($pkey=='url1' || $pkey=='title_url' || $pkey=='url_title'  || $pkey=='external') {
            $post_val = get_post_custom_values($pkey);
            }
        }
        if (empty($post_val)) {
            $link = $permalink;
        } else {
            $link = $post_val[0];
        }
    } else {
     $link = $permalink;
    }
 
    return $link; 
}

add_filter('the_permalink','cs_external_permalink');
add_filter('the_permalink_rss','cs_external_permalink');    
    
add_action( 'admin_init', 'lgom_plugin_admin_init' );
add_action('admin_menu', 'lgom_plugin_menu');

 function lgom_plugin_admin_init() {
        /* Register our script. */
        wp_register_script( 'lgom_plugin_jquery_script', plugins_url('js/jquery-1.7.1.min.js', __FILE__) );
        wp_register_script( 'lgom_plugin_jqueryui_script', plugins_url('js/jquery-ui-1.8.18.custom.min.js', __FILE__) );
        wp_register_script( 'lgom_plugin_script', plugins_url('js/script.min.js', __FILE__) );
        wp_register_style( 'lgom_plugin_stylesheet', plugins_url('css/styles.min.css', __FILE__) );
        wp_register_style( 'lgom_plugin_jqueryui_stylesheet', plugins_url('css/custom-theme/jquery-ui-1.8.18.custom.css', __FILE__) );
    }

function lgom_plugin_menu() {
	 $page = add_posts_page('LinkGrab', 'Link Grab-o-Matic', 'read', 'lgom-menu-item', 'my_plugin_function');
     add_action( 'admin_print_styles-' . $page, 'lgom_plugin_admin_styles' );
}

function lgom_plugin_admin_scripts() {
        
    }

function lgom_plugin_admin_styles() {
    wp_enqueue_script( 'lgom_plugin_jquery_script' );   
    wp_enqueue_script( 'lgom_plugin_jqueryui_script' );   
    wp_enqueue_script( 'lgom_plugin_script' );   
    wp_enqueue_style( 'lgom_plugin_stylesheet' );
    wp_enqueue_style( 'lgom_plugin_jqueryui_stylesheet' );
   }
function my_plugin_function () {

    
    
    
    ?>    
            <div id="prestep">
                <form method="post" id="getinfo" name="getinfo" action="<?php echo plugins_url('link-grab-o-matic/linkgrab.php'); ?>">
                    <div class="inputwrapper">
                        <input type="text" name="url" id="urlform" placeholder="http://www.website.com" />
                    </div><!-- inputwrapper -->
                    <button type="button" class="urlsubmit" name="submit"  value="submit">go</button>
                </form>
                <div style="clear:both;"></div>
            </div><!-- prestep -->
            <div style="clear:both;"></div>
        <div id="wizardharry">
            <ul>
                <li><a href="#stepOne">Step 1 - Link Title</a></li>
                <li><a href="#stepTwo">Step 2 - Image</a></li>
                <li><a href="#stepThree">Step 3 - Description</a></li>
                <li><a href="#stepFour">Step 4 - Review</a></li>
            </ul>
            <div id="stepOne" class="steps">            
                <div class='sourceTitle' contenteditable="true"><?php //echo $title[0]->innertext; ?></div>
                <div class="buttons"><a href="#" class="pagenav" data-page="1">next</a></div>
                <div style="clear:both;"></div>
            </div><!-- stepOne -->
        
        
        <div id="stepTwo" class="steps">
            Images are <input type="checkbox" id="check" checked="checked"/><label for="check" id="imageOnLabel">On</label>
            <div id="imagewrapper">
                <p>Pick one image from the <?php   //echo count($images) . " found."; ?></p>
                <div id="imagecollection"></div>

            <div style="clear:both;"></div>
            </div><!-- imagewrapper -->
            <div class="buttons"><a href="#" class="pagenav" data-page="0">prev</a> <a href="#" class="pagenav" data-page="2">next</a></div>
            <div style="clear:both;"></div>
        </div><!-- stepTwo -->
        <div id="stepThree" class="steps">
            Select some text to use or write your own.
        <div id="textwrapper">

            </div><!-- textwrapper -->
            <div class="buttons"><a href="#" class="pagenav" data-page="1">prev</a><a href="#" class="pagenav" data-page="3">next</a></div>
            <div style="clear:both;"></div>
            </div><!-- stepThree -->
            
            <div id="stepFour" class="steps">
            <p>Congratulations! Review before you post. Click to edit</p>
            <div id="reviewPost">
                <div id="reviewTitle" contenteditable="true"></div>
                <div id="reviewImage"></div>
                <div id="reviewCopy" contenteditable="true"></div>
            </div>
            <div class="buttons"><a href="#" class="pagenav" data-page="2">prev</a><a href="#" class="pagenav pagedone" data-posturl="<?php echo plugins_url('link-grab-o-matic/lgom-post.php'); ?>">done</a></div>
                <div style="clear:both;"></div>
                <form id="finalizePost" name="finalize" action="<?php echo plugins_url('link-grab-o-matic/lgom-post.php'); ?>" method="post">
                    <input type="hidden" name="title" id="rTitle" />
                    <input type="hidden" name="image" id="rImage" />
                    <input type="hidden" name="description" id="rCopy" />
                    <input type="hidden" name="url" id="rUrl" value=""/>
                </form>
                
            </div><!-- stepFour -->
            </div><!-- wizard -->

        <div id="dialog-fetching" title="Fetching the page">
	        <div class="center">Please standby while I grab that for you.</div>
            <div class="center"><img alt="loading" src="<?php echo plugins_url('link-grab-o-matic/images/ajax-loader.gif'); ?>" /></div>
        </div>
        <div id="dialog-postit" title="Generating Post">
	        <div class="center">Standby - I'm turning that in to a post for you now.</div>
            <div class="center"><img alt="loading" src="<?php echo plugins_url('link-grab-o-matic/images/ajax-loader.gif'); ?>" /></div>
        </div>
    <?php

}
    // add the admin options page
    add_action('admin_menu', 'lgom_admin_add_page');
    function lgom_admin_add_page() {
        add_options_page('Link Grab-o-Matic Options', 'Link Grab-o-Matic', 'manage_options', 'lgom', 'lgom_options_page');
    }

// add the admin settings and such
add_action('admin_init', 'lgom_admin_init');
function lgom_admin_init(){
    register_setting( 'lgom_options', 'lgom_options', 'lgom_options_validate' );
    add_settings_section('lgom_main', 'Power User Options Plus', 'lgom_section_text', 'lgom');
    add_settings_field('lgom_radio_directlink', 'Posts link directly to source', 'lgom_setting_radio', 'lgom', 'lgom_main');
    add_settings_field('lgom_radio_publish', 'Publish setting', 'lgom_setting_publish', 'lgom', 'lgom_main');
    add_settings_field('lgom_select_category', 'Category', 'lgom_setting_category', 'lgom', 'lgom_main');
    add_settings_field('lgom_radio_image', 'Images should be added ', 'lgom_setting_images', 'lgom', 'lgom_main');
}

function lgom_section_text() {
    echo '<p>Setup how you would like your LGOM posts to be formatted.</p>';
}

function lgom_setting_images() {
    $options = get_option('lgom_options');
    
    $isInline = "";
    $isThumb = "";

    // Checks to see if variable is empty. Ex: not yet defined by the users.
	if(empty($options['radio_image'])){
        // If it is empty assign default value thumbnail
		$options['radio_image'] = 'thumbnail';
	}

    if($options['radio_image'] == 'inline')
    {
        $isInline = "checked='checked'";
    }
    else
    {
         $isThumb = "checked='checked'";
    }
    echo "<input id='lgom_radio_image' name='lgom_options[radio_image]' size='40' type='radio' value='inline' {$isInline} />inline ";
    echo "<input id='lgom_radio_image' name='lgom_options[radio_image]' size='40' type='radio' value='thumbnail' {$isThumb} />thumbnail";
} 

function lgom_setting_string() {
    $options = get_option('lgom_options');
    echo "<input id='lgom_text_string' name='lgom_options[text_string]' size='40' type='text' value='{$options['text_string']}'/>";
} 

function lgom_setting_category() {
    $options = get_option('lgom_options');
    if(empty($options['select_category']))
    {
        $options['select_category'] = 1;
    }
    wp_dropdown_categories('show_count=1&hide_empty=0&hierarchical=1&name=lgom_options[select_category]&selected= ' . $options['select_category']);
} 

function lgom_setting_radio() {
    $options = get_option('lgom_options');
    $isTrue = "";
    
    if($options['radio_directlink'] == 'on')
    {
        $isTrue = "checked='checked'";
    }

    echo "<input id='lgom_radio_directlink' name='lgom_options[radio_directlink]' size='40' type='checkbox' {$isTrue} /> ";
} 

function lgom_setting_publish() {
    $options = get_option('lgom_options');
    $isDraft = "";
    $isPub = "";

    // Checks to see if variable is empty. Ex: not yet defined by the users.
	if(empty($options['radio_publish'])){
        // If it is empty assign it default value true.
		$options['radio_publish'] = 'draft';
	}

    if($options['radio_publish'] == 'draft')
    {
        $isDraft = "checked='checked'";
    }
    else
    {
         $isPub = "checked='checked'";
    }
    echo "<input id='lgom_radio_publish' name='lgom_options[radio_publish]' size='40' type='radio' value='draft' {$isDraft} />draft ";
    echo "<input id='lgom_radio_publish' name='lgom_options[radio_publish]' size='40' type='radio' value='publish' {$isPub} />publish";
} 

// validate our options
function lgom_options_validate($input) {
    $options = get_option('lgom_options');
    $options['text_string'] = trim($input['text_string']);
    $options['radio_directlink'] = trim($input['radio_directlink']);
    $options['radio_publish'] = trim($input['radio_publish']);
    $options['radio_image'] = trim($input['radio_image']);
    $options['select_category'] = trim($input['select_category']);
    return $options;
}

 // display the admin options page
function lgom_options_page() {
?>
<div>
<h2>Link Grab-o-Matic Options</h2>
From here you can set you preferences for how LGOM works. These settings here are currently global but can be overridden on a per post basis.
<form action="options.php" method="post">
<?php settings_fields('lgom_options'); ?>
<?php do_settings_sections('lgom'); ?>
<p>&nbsp;</p>
<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
</form></div>

<?php
}
 
?>