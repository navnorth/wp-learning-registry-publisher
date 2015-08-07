<?PHP

	class LearningRegistryPublisherPostColumns{
	
		function __construct(){
			add_action('manage_post_posts_custom_column' , array($this, 'custom_columns'), 10, 2 );
			add_filter('manage_edit-post_columns', array($this, 'add_new_edit_page_columns') );
		}

		function add_new_edit_page_columns($gallery_columns) {
		
			if(current_user_can("LearningRegistryPublisherHistory")){
				$gallery_columns['submitted'] = __('Times Submitted');
				$gallery_columns['last_submitted'] = __('Last Submitted');
				$gallery_columns['resubmit'] = __('Current Status');
			}
	
			return $gallery_columns;
			
		}

		function custom_columns( $column, $post_id ) {
		
			if(current_user_can("LearningRegistryPublisherHistory")){
		
				global $wpdb;
				
				$querystr_posts = "
					SELECT post 
					FROM " . $wpdb->prefix . "lrp_documents_history 
					WHERE post = ";
					
				$querystr_time = "
					SELECT max(date_submitted) as last
					FROM " . $wpdb->prefix . "lrp_documents_history 
					WHERE post = ";
			
				$querystr_post_submit = "
					SELECT max(date_submitted) as last, UNIX_TIMESTAMP(post_modified) as modified
					FROM " . $wpdb->prefix . "lrp_documents_history, " . $wpdb->prefix . "posts 
					WHERE " . $wpdb->prefix . "lrp_documents_history.post = " . $wpdb->prefix . "posts.ID
					and " . $wpdb->prefix . "lrp_documents_history.post = ";
			
				switch ( $column ) {
					case 'submitted':
						
						$pageposts = $wpdb->get_results($querystr_posts . $post_id, OBJECT);
						if(count($pageposts)!=0){
							echo count($pageposts);
						}else{
							echo "Never published";
						}
						
						break;
					
					case 'last_submitted':
						$pageposts = $wpdb->get_results($querystr_time . $post_id, OBJECT);
						if($pageposts[0]->last!=""){
							echo date("G:i:s F, jS Y", $pageposts[0]->last);
						}else{
							echo "Never published";
						}
						break;
						
					case 'resubmit':
						$pageposts = $wpdb->get_results($querystr_posts . $post_id, OBJECT);
						if(count($pageposts)!=0){
							$pageposts = $wpdb->get_results($querystr_post_submit . $post_id, OBJECT);
							if($pageposts[0]->last < $pageposts[0]->modified){
								echo "Needs updating";
							}else{
								echo "Up to date";
							}	
						}else{
							echo "Never published";
						}
						break;
						
				}
				
			}
			
		}
		
	}
	
	$LearningRegistryPublisherPostColumns = new LearningRegistryPublisherPostColumns();