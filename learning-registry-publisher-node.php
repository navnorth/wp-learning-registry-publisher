<?PHP

	class LearningRegistryPublisherNodePostType{
	
		function __construct(){
			add_action("init", array($this, "create"));
			add_action("admin_menu", array($this, "menu"));
			add_action('manage_lrnode_posts_custom_column' , array($this, 'custom_columns'), 10, 2 );
			add_filter('manage_edit-lrnode_columns', array($this, 'add_new_edit_page_columns') );
			add_filter('posts_results', array($this, 'post_order') );
			add_filter('the_title', array($this, 'post_title'), 10, 2 );
			add_filter('post_updated_messages', array($this, 'update_messages') );
		}
		
		function update_messages($messages){
			unset($messages['post'][1]);
			unset($messages['post'][6]);
			return $messages;
		}
		
		function post_title( $title, $id = null ) {

			if(is_admin()){
				global $wp;
				$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
				if(strpos($current_url, "edit.php")!==FALSE && $_GET['post_type']=="lrnode"){
					if(get_option("lrnode_default")){
						$default_node = get_option("lrnode_default");
						if($id == $default_node){
							$title = "DEFAULT NODE - " . $title;
						}
					}
				}
			}
			return $title;
		}
		
		function post_order($posts){
			if(is_admin()){
				global $wp;
				$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
				if(strpos($current_url, "edit.php")!==FALSE && $_GET['post_type']=="lrnode"){
					if(get_option("lrnode_default")){
						$default_node = get_option("lrnode_default");
						$unset = false;
						foreach($posts as $id => $post){
							if($post->ID == $default_node){
								unset($posts[$id]);
								$unset = true;
							}
						}
						array_unshift($posts, get_post($default_node));
						if(!$unset){
							array_pop($posts);
						}
						$posts = array_filter($posts);
						
					}
				}
			}
			return $posts;
		}

		function add_new_edit_page_columns($gallery_columns) {
		
			$new_columns['cb'] = '<input type="checkbox" />';
			$new_columns['id'] = "";
			
			$new_columns['title'] = __('Title');
		
		
			$new_columns['doc_pub'] = __('Documents published to this node');
			$new_columns['last_pub'] = __('Last Published To');
			$new_columns['date'] = _x('Date', 'column name');
		
	
			return $new_columns;
			
		}

		function custom_columns( $column, $post_id ) {
			
			global $wpdb;
		
			$querystr_posts = "
				SELECT distinct post 
				FROM " . $wpdb->prefix . "lrp_documents_history 
				WHERE lrnode = ";
				
			$querystr_time = "
				SELECT max(date_submitted) as last
				FROM " . $wpdb->prefix . "lrp_documents_history 
				WHERE lrnode = ";
				
			switch ( $column ) {
				case 'doc_pub':
					$pageposts = $wpdb->get_results($querystr_posts . $post_id, OBJECT);
					echo count($pageposts);
					break;

				case 'last_pub':
					$pageposts = $wpdb->get_results($querystr_time . $post_id, OBJECT);
					if($pageposts[0]->last!=""){
						echo date("G:i:s F, jS Y", $pageposts[0]->last);
					}else{
						echo "Never published too";
					}
					break;
			}
		}
		
		function create(){
	
			$labels = array(
				'name' => 'Learning Registry Node',
				'singular_name' => 'Learning Registry Node',
				'add_new' => 'Add new Learning Registry Node',
				'add_new_item' => 'Add Learning Registry Node',
				'edit_item' => 'Edit Learning Registry Node',
				'new_item' => 'New Learning Registry Node',
				'all_items' => 'All Learning Registry Nodes',
				'view_item' => 'View Learning Registry Nodes',
				'search_items' => 'Search Learning Registry Node',
				'not_found' =>  'No Learning Registry Nodes found',
				'not_found_in_trash' => 'No Learning Registry Nodes found in trash', 
				'parent_item_colon' => '',
				'menu_name' => 'Learning Registry'
			);
				
			$args = array(
				'labels' => $labels,
				'public' => false,
				'show_ui' => true,
				'capability_type' => 'lrnode',
				'hierarchical' => false,
				'rewrite' => false,
				'supports' => array('title'),
				'menu_position' => 99,
				'exclude_from_search' => true,
				'publically_queryable' => true,
			);
		
			register_post_type( 'LRNode' , $args );

		}	
	
		function menu(){
		
			global $submenu, $menu;
			
			if ( !current_user_can('edit_lrschema') && !current_user_can('edit_lrnode') && !current_user_can('edit_lrkey') ){
				foreach($menu as $index => $value){
			
					if($menu[$index][0] == "Learning Registry"){
						unset($menu[$index]);
					}
			
				}
			}
			
		}
	
	}
	
	$LearningRegistryPublisherNodePostType = new LearningRegistryPublisherNodePostType();