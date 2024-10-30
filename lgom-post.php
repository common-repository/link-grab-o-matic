<?php
    ini_set('display_errors', 'On');    
    require_once('../../../wp-blog-header.php');
    include_once"../../../wp-load.php";
    include_once"../../../wp-includes/wp-db.php";
    require_once('../../../wp-admin/includes/image.php');
    $options = get_option('lgom_options');
    if (!empty($_POST)) {
      if(empty($options['radio_directlink']))
      {
        $descriptiontext = $_POST['description'] . "<br/><a href='{$_POST['url']}'>source</a>";
      }
      else
      {
        $descriptiontext = $_POST['description'];
      }
      
      if(empty($options['radio_publish']))
      {
        $publishstatus = 'draft';
      }
      else
      {
        $publishstatus = $options['radio_publish'];
      }

      if(empty($options['select_category']))
        {
            $options['select_category'] = 1;
        }
      $selectcategory = array($options['select_category']);

      $my_post = array(
         'post_title' => $_POST['title'],
         'post_content' =>  $descriptiontext,
         'post_status' => $publishstatus,
         'post_category' => $selectcategory,
      );

       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $_POST['image']);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       $contents = curl_exec ($ch);
       curl_close ($ch);
       $upload = wp_upload_bits(basename($_POST['image']), null, $contents);

      // Insert the post into the database
      $my_post_id = wp_insert_post( $my_post );
      add_post_meta($my_post_id, 'thumbnail', $_POST['image']);
      
    
      if($options['radio_directlink'] == 'true')
      {
        add_post_meta($my_post_id, 'title_url', $_POST['url']);
      }

      //upload and attach if any image is suppies
      if(!empty($_POST['image']))
      {  
          $wp_filetype = wp_check_filetype(basename($upload['file']), null );
          $wp_upload_dir = wp_upload_dir();
          $attachment = array(
             'guid' => $wp_upload_dir['baseurl'] . $upload['file'], 
             'post_mime_type' => $wp_filetype['type'],
             'post_title' => preg_replace('/\.[^.]+$/', '', $_POST['title']),
             'post_content' => '',
             'post_status' => 'inherit'
          );
          $attach_id = wp_insert_attachment( $attachment, $upload['file'], $my_post_id );
  
          $attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
          wp_update_attachment_metadata( $attach_id, $attach_data );
          add_post_meta($my_post_id, '_thumbnail_id', $attach_id);

          //add the image to the post if set
          $default_attr = array(
	            'class'	=> "alignleft size-thumbnail wp-image-20",
	            'alt'   => trim(strip_tags( $_POST['title'] )),
	            'title' => trim(strip_tags( $_POST['title'] )),
            );

            $updated_post = array();
            $updated_post['ID'] = $my_post_id;
            $updated_post['post_content'] =  wp_get_attachment_image( $attach_id, 'thumb', 0, $default_attr) .  $descriptiontext;
          
            // Update the post into the database
            wp_update_post( $updated_post );

      }
      //redirect to post
      $adminurl = admin_url();
      $editpath = $adminurl . "post.php?post=" . $my_post_id . "&action=edit";
      header( 'Location: ' . $editpath ) ;
      //echo '{ "status" : "' . $my_post_id . '"}';

    }
    else
    { 
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/json'); 
        echo '{ "status" : "failed" }';
    }
?>

