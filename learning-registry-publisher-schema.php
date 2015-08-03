<?PHP

	class LearningRegistryPublisherSchemaPostType{
	
		function __construct(){
			add_action("init", array($this, "create"));
			add_action("admin_menu", array($this, "menu"));
			add_action('manage_lrschema_posts_custom_column' , array($this, 'custom_columns'), 10, 2 );
			add_filter('manage_edit-lrschema_columns', array($this, 'add_new_edit_page_columns') );
		add_filter('posts_results', array($this, 'post_order') );
			add_filter('the_title', array($this, 'post_title'), 10, 2 );
		}
		
		function post_title( $title, $id = null ) {

			if(is_admin()){
				global $wp;
				$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
				if(strpos($current_url, "edit.php")!==FALSE && $_GET['post_type']=="lrschema"){
					if(get_option("lrschema_default")){
						$default_node = get_option("lrschema_default");
						if($id == $default_node){
							$title = "DEFAULT SCHEMA - " . $title;
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
				if(strpos($current_url, "edit.php")!==FALSE && $_GET['post_type']=="lrschema"){
					if(get_option("lrschema_default")){
						$default_node = get_option("lrschema_default");
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
		
		
			$new_columns['doc_pub'] = __('Documents using schema');
			$new_columns['doc_not_pub'] = __('Documents not using schema');
			$new_columns['last_pub'] = __('Last Time Schema Used');
			$new_columns['date'] = _x('Date', 'column name');
		
	
			return $new_columns;
			
		}

		function custom_columns( $column, $post_id ) {
			switch ( $column ) {
				case 'doc_pub':
					echo time();
					break;
					
				case 'doc_not_pub':
					echo time();
					break;

				case 'last_pub':
					echo time();
					break;
			}
		}
		
		function create(){
		
			$labels = array(
				'name' => 'Learning Registry Schema',
				'singular_name' => 'Learning Registry Schema',
				'add_new' => 'Add new',
				'add_new_item' => 'Add Learning Registry Schema',
				'edit_item' => 'Edit Learning Registry Schema',
				'new_item' => 'New Learning Registry Schema',
				'all_items' => 'All Learning Registry Schemas',
				'view_item' => 'View Learning Registry Schema',
				'search_items' => 'Search Learning Registry Schemas',
				'not_found' =>  'No Learning Registry Schemas found',
				'not_found_in_trash' => 'No Learning Registry Schemas found in trash', 
				'parent_item_colon' => '',
				'menu_name' => 'Learning Registry Schemas'
			);
				
			$args = array(
				'labels' => $labels,
				'public' => false,
				'show_ui' => true,
				'capability_type' => 'lrschema',
				'hierarchical' => false,
				'rewrite' => false,
				'supports' => array('title', 'editor'),
				'menu_position' => 99,
				'exclude_from_search' => true,
				'publically_queryable' => true,
			);
		
			register_post_type( 'LRSchema' , $args );

		}	
	
		function menu(){
		
			global $submenu, $menu;
			
			$submenu['edit.php?post_type=lrschema'] = "";
			unset($submenu['edit.php?post_type=lrschema']);
			$submenu = array_filter($submenu);
			
			foreach($menu as $index => $value){
			
				if($menu[$index][0] == "Learning Registry Schemas"){
					unset($menu[$index]);
				}
			
			}
			
			$submenu['edit.php?post_type=lrnode'][30] = Array
                (
                    0 => "All Schemas",
                    1 => "edit_lrschemas",
                    2 => "edit.php?post_type=lrschema"
                );
				
			$submenu['edit.php?post_type=lrnode'][35] = Array
                (
                    0 => "Add new schema",
                    1 => "edit_lrschemas",
                    2 => "post-new.php?post_type=lrschema"
                );
			
		}
	
	}
	
	$LearningRegistryPublisherSchemaPostType = new LearningRegistryPublisherSchemaPostType();