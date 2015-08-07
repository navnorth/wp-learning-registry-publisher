<?PHP

	class LearningRegistryPublisherFieldPostType{
	
		function __construct(){
			add_action("init", array($this, "create"));
			add_action("admin_menu", array($this, "menu"));
			add_action('manage_lrfield_posts_custom_column' , array($this, 'custom_columns'), 10, 2 );
			add_filter('manage_edit-lrfield_columns', array($this, 'add_new_edit_page_columns') );
		}

		function add_new_edit_page_columns($gallery_columns) {
		
			$new_columns['cb'] = '<input type="checkbox" />';
			$new_columns['id'] = "";
			
			$new_columns['title'] = __('Title');
		
		
			$new_columns['doc_pub'] = __('Schemas using this field');
			$new_columns['date'] = _x('Date', 'column name');
		
	
			return $new_columns;
			
		}

		function custom_columns( $column, $post_id ) {
		
			global $wpdb;
		
			switch ( $column ) {
				case 'doc_pub':
					
					$querystr = "
						SELECT ID 
						FROM " . $wpdb->prefix . "posts 
						WHERE post_status = 'publish'
						AND post_content LIKE '%$$$%'";
					$pageposts = $wpdb->get_results(str_replace("$$$", "%" . strtoupper(get_the_title($post_id)) . "%", $querystr), OBJECT);
					echo count($pageposts) . "<br />";
					foreach($pageposts as $post){
						echo "<a href='post.php?post=" . $post->ID . "&action=edit'>" . get_the_title($post->ID) . "</a><br />";
					}
					
					break;
			}
		}
		
		function create(){
	
			$labels = array(
				'name' => 'Learning Registry Field',
				'singular_name' => 'Learning Registry Field',
				'add_new' => 'Add new Learning Registry Field',
				'add_new_item' => 'Add Learning Registry Field',
				'edit_item' => 'Edit Learning Registry Field',
				'new_item' => 'New Learning Registry Field',
				'all_items' => 'All Learning Registry Fields',
				'view_item' => 'View Learning Registry Fields',
				'search_items' => 'Search Learning Registry Field',
				'not_found' =>  'No Learning Registry Fields found',
				'not_found_in_trash' => 'No Learning Registry Fields found in trash', 
				'parent_item_colon' => '',
				'menu_name' => 'Learning Registry Fields'
			);
				
			$args = array(
				'labels' => $labels,
				'public' => false,
				'show_ui' => true,
				'capability_type' => 'lrfield',
				'hierarchical' => false,
				'rewrite' => false,
				'supports' => array('title','editor'),
				'menu_position' => 99,
				'exclude_from_search' => true,
				'publically_queryable' => true,
			);
		
			register_post_type( 'LRfield' , $args );

		}	
	
		function menu(){
		
			global $submenu, $menu;
			
			$submenu['edit.php?post_type=lrfield'] = "";
			unset($submenu['edit.php?post_type=lrfield']);
			$submenu = array_filter($submenu);
			
			foreach($menu as $index => $value){
			
				if($menu[$index][0] == "Learning Registry Fields"){
					unset($menu[$index]);
				}
			
			}
			
			$submenu['edit.php?post_type=lrnode'][12] = Array
                (
                    0 => "All Fields",
                    1 => "edit_lrfields",
                    2 => "edit.php?post_type=lrfield"
                );
				
			$submenu['edit.php?post_type=lrnode'][15] = Array
                (
                    0 => "Add new field",
                    1 => "edit_lrfields",
                    2 => "post-new.php?post_type=lrfield"
                );
			
		}
	
	}
	
	$LearningRegistryPublisherFieldPostType = new LearningRegistryPublisherFieldPostType();