<?PHP

	class LearningRegistryPublisherKeyPostType{
	
		function __construct(){
			add_action("init", array($this, "create"));
			add_action("admin_menu", array($this, "menu"));
			add_action('manage_lrkey_posts_custom_column' , array($this, 'custom_columns'), 10, 2 );
			add_filter('manage_edit-lrkey_columns', array($this, 'add_new_edit_page_columns') );
			add_filter('posts_results', array($this, 'post_order') );
			add_filter('the_title', array($this, 'post_title'), 10, 2 );
		}
		
		function post_title( $title, $id = null ) {

			if(is_admin()){
				global $wp;
				$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
				if(strpos($current_url, "edit.php")!==FALSE && $_GET['post_type']=="lrkey"){
					if(get_option("lrkey_default")){
						$default_node = get_option("lrkey_default");
						if($id == $default_node){
							$title = "DEFAULT KEY - " . $title;
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
				if(strpos($current_url, "edit.php")!==FALSE && $_GET['post_type']=="lrkey"){
					if(get_option("lrkey_default")){
						$default_node = get_option("lrkey_default");
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
		
		
			$new_columns['doc_pub'] = __('Documents using this key');
			$new_columns['doc_not_pub'] = __('Documents not using this key');
			$new_columns['last_pub'] = __('Last Time key Used');
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
				'name' => 'Learning Registry PGP Key',
				'singular_name' => 'Learning Registry PGP Key',
				'add_new' => 'Add new',
				'add_new_item' => 'Add Learning Registry PGP Key',
				'edit_item' => 'Edit Learning Registry PGP Key',
				'new_item' => 'New Learning Registry PGP Key',
				'all_items' => 'All Learning Registry PGP Keys',
				'view_item' => 'View Learning Registry PGP Key',
				'search_items' => 'Search Learning Registry PGP Keys',
				'not_found' =>  'No Learning Registry PGP Keys found',
				'not_found_in_trash' => 'No Learning Registry PGP Keys found in trash', 
				'parent_item_colon' => '',
				'menu_name' => 'Learning Registry Keys'
			);
				
			$args = array(
				'labels' => $labels,
				'public' => false,
				'show_ui' => true,
				'capability_type' => 'lrkey',
				'hierarchical' => false,
				'rewrite' => false,
				'supports' => array('title', 'editor'),
				'menu_position' => 99,
				'exclude_from_search' => true,
				'publically_queryable' => true,
			);
		
			register_post_type( 'LRKey' , $args );

		}	
	
		function menu(){
		
			global $submenu, $menu;
			
			$submenu['edit.php?post_type=lrkey'] = "";
			unset($submenu['edit.php?post_type=lrkey']);
			$submenu = array_filter($submenu);
			
			foreach($menu as $index => $value){
			
				if($menu[$index][0] == "Learning Registry Keys"){
					unset($menu[$index]);
				}
			
			}
			
			$submenu['edit.php?post_type=lrnode'][45] = Array
                (
                    0 => "All Keys",
                    1 => "edit_lrkey",
                    2 => "edit.php?post_type=lrkey"
                );
				
			$submenu['edit.php?post_type=lrnode'][50] = Array
                (
                    0 => "Add new key",
                    1 => "edit_lrkey",
                    2 => "post-new.php?post_type=lrkey"
                );
			
		}
	
	}
	
	$LearningRegistryPublisherKeyPostType = new LearningRegistryPublisherKeyPostType();