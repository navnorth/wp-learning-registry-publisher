<?PHP

	class LearningRegistryPublisherUnsubmittedManagementPage{
	
		function __construct(){
			add_action('admin_menu', array($this, 'submenu_management_pages'));
			add_action( 'admin_enqueue_scripts', array($this, 'enqueue_scripts') );	 
		}
		
		function enqueue_scripts(){
			if(isset($_GET['page'])){
				if(strpos($_GET['page'],"lrp_manage_documents")!==0 || strpos($_GET['page'],"lrp_unsubmitted_resources")!==0 ){
					wp_enqueue_script("jquery");
					wp_enqueue_style('lrp_tabs_css', plugins_url('/css/lrp_tabs.css', __FILE__));
					wp_enqueue_script('lrp_table_sort', plugins_url('/js/jquery.tablesorter.min.js', __FILE__), array("jquery"), '1.0.0', true );
				}
			}
		}
			
		function submenu_management_pages() {
			add_submenu_page( 'edit.php?post_type=lrnode', 'Unsubmitted resources', 'Unsubmitted resources', 'LearningRegistryPublisherHistory', 'lrp_unsubmitted_resources', array($this, 'admin_page') );
		}

		function admin_page(){
		
			global $wpdb;
			
			$current_url = "edit.php?post_type=lrnode&page=lrp_unsubmitted_resources";
			
			?><h2>All documents not sent to a Learning Registry Node</h2>
			<?PHP
			
			if(current_user_can("LearningRegistryPublisherOverrideDefaults")){
				?>
			<a id="lrp_document_show" href="javascript:lrp_submit_options();">Show Submission Options</a>
				<?PHP
			}
		
			$querystr = "SELECT " . $wpdb->prefix . "posts.* from " . $wpdb->prefix . "posts
							where " . $wpdb->prefix . "posts.ID not in (SELECT " . $wpdb->prefix . "lrp_documents_history.post FROM " . $wpdb->prefix . "lrp_documents_history) 
							AND " . $wpdb->prefix . "posts.post_status = 'publish'
							AND " . $wpdb->prefix . "posts.post_type not like 'lr%'";
			
			$filter = false;	
			
			if(isset($_GET['post_type_filter'])){
				$querystr .= "AND " . $wpdb->prefix . "posts.post_type = '" . $_GET['post_type_filter'] . "'";
				$filter = true;
			}
				
			$querystr .= "order by " . $wpdb->prefix . "posts.ID desc
							limit 99999";
			
			$pageposts = $wpdb->get_results($querystr, OBJECT);
			
			if($filter){
				echo "<p><a href='" . $current_url . "'>Remove filters</a></p>";
			}
			
			if(current_user_can("LearningRegistryPublisherManageDocument")){
			
				if(get_option("lrnode_default")){
				
					if(current_user_can("LearningRegistryPublisherOverrideDefaults")){
				
						$args = array(
							'posts_per_page'   => 9999999,
							'orderby'          => 'post_title',
							'order'            => 'ASC',
							'post_type'        => 'lrnode',
							'post_status'      => 'publish',
							'suppress_filters' => true 
						);
						$posts = get_posts( $args );
						
						$output = "<p>Choose Node : <select id='lrnode_$$$'><option>Select a Node</option>";
						foreach($posts as $post){
							$output .='<option ';
							if(get_option("lrnode_default")==$post->ID){ $output .= " selected='true' "; }
							$output .= 'value="' . $post->ID . '">' . $post->post_title . "</option>";
						}
						$output .= "</select></p>";
					
					}else{
						$output = '<input id="lrnode_$$$" type="hidden" value="' . get_option("lrnode_default") . " />";
					}
				
				}else{
				
					$args = array(
							'posts_per_page'   => 9999999,
							'orderby'          => 'post_title',
							'order'            => 'ASC',
							'post_type'        => 'lrnode',
							'post_status'      => 'publish',
							'suppress_filters' => true 
						);
						$posts = get_posts( $args );
						
						$output = "<p>Choose Node : <select id='lrnode_$$$'><option>Select a Node</option>";
						foreach($posts as $post){
							$output .='<option ';
							if(get_option("lrnode_default")==$post->ID){ $output .= " selected='true' "; }
							$output .= 'value="' . $post->ID . '">' . $post->post_title . "</option>";
						}
						$output .= "</select></p>";
				
				}
				
				if(get_option("lrschema_default")){
				
					if(current_user_can("LearningRegistryPublisherOverrideDefaults")){
				
						$args = array(
							'posts_per_page'   => 9999999,
							'orderby'          => 'post_title',
							'order'            => 'ASC',
							'post_type'        => 'lrschema',
							'post_status'      => 'publish',
							'suppress_filters' => true 
						);
						$posts = get_posts( $args );
						
						$output .= "<p>Choose Schema : <select id='lrschema_$$$'><option>Select a Schema</option>";
						foreach($posts as $post){
							$output .='<option ';
							if(get_option("lrschema_default")==$post->ID){ $output .= " selected='true' "; }
							$output .= 'value="' . $post->ID . '">' . $post->post_title . "</option>";
						}
						$output .= "</select></p>";
					
					}else{
						$output .= '<input id="lrschema_$$$" type="hidden" value="' . get_option("lrschema_default") . " />";
					}
				
				}else{
				
					$args = array(
							'posts_per_page'   => 9999999,
							'orderby'          => 'post_title',
							'order'            => 'ASC',
							'post_type'        => 'lrschema',
							'post_status'      => 'publish',
							'suppress_filters' => true 
						);
						$posts = get_posts( $args );
						
						$output .= "<p>Choose Schema : <select id='lrschema_$$$'><option>Select a Schema</option>";
						foreach($posts as $post){
							$output .='<option ';
							if(get_option("lrschema_default")==$post->ID){ $output .= " selected='true' "; }
							$output .= 'value="' . $post->ID . '">' . $post->post_title . "</option>";
						}
						$output .= "</select></p>";
				
				}
				
				$sign = get_post_meta(get_option("lrnode_default"), "lrnode_sign", true); 
				
				if($sign){
				
					if(get_option("lrkey_default")){
					
						if(current_user_can("LearningRegistryPublisherOverrideDefaults")){
					
							$args = array(
								'posts_per_page'   => 9999999,
								'orderby'          => 'post_title',
								'order'            => 'ASC',
								'post_type'        => 'lrkey',
								'post_status'      => 'publish',
								'suppress_filters' => true 
							);
							$posts = get_posts( $args );
							
							$output .= "<p>Choose Key : <select id='lrkey_$$$'><option>Select a key</option>";
							foreach($posts as $post){
								$output .='<option ';
								if(get_option("lrkey_default")==$post->ID){ $output .= " selected='true' "; }
								$output .= 'value="' . $post->ID . '">' . $post->post_title . "</option>";
							}
							$output .= "</select></p>";
						
						}else{
							$output .= '<input id="lrkey_$$$" type="hidden" value="' . get_option("lrkey_default") . " />";
						}
					
					}else{
					
						$args = array(
								'posts_per_page'   => 9999999,
								'orderby'          => 'post_title',
								'order'            => 'ASC',
								'post_type'        => 'lrkey',
								'post_status'      => 'publish',
								'suppress_filters' => true 
							);
							$posts = get_posts( $args );
							
							$output .= "<p>Choose Key : <select id='lrkey_$$$'><option>Select a key</option>";
							foreach($posts as $post){
								$output .='<option ';
								if(get_option("lrkey_default")==$post->ID){ $output .= " selected='true' "; }
								$output .= 'value="' . $post->ID . '">' . $post->post_title . "</option>";
							}
							$output .= "</select></p>";
					
					}

				}else{
					$output .= '<input id="lrkey_$$$" type="hidden" value="0" />';
				}
				
			}
			
			?><table id="myTable" class="tablesorter"> 
						<thead> 
							<tr> 
								<th>Document ID (click to sort)</th> 
								<th>Title (click to sort)</th> 
								<th>Post type (click to sort)</th> 
								<?PHP
								
									if(current_user_can("LearningRegistryPublisherManageDocument")){
								
									?><th>Submit</th><?PHP
									
									}
									
								?>
							</tr> 
						</thead> 
						<tbody>
					<?PHP
			
			foreach($pageposts as $page){
								
				echo "<tr id='lrp_document_" . $page->ID . "'>";				
				echo "<td>";
				if(current_user_can("edit_others_posts")){
					echo "<a href='post.php?post=" . $page->ID . "&action=edit'>";
				}
				echo $page->ID;
				if(current_user_can("edit_others_posts")){
					echo "</a>";
				}
				echo"</td>";
				echo "<td>";
				if(current_user_can("edit_others_posts")){
					echo "<a href='post.php?post=" . $page->post . "&action=edit'>";
				}
				echo $page->post_title;
				if(current_user_can("edit_others_posts")){
					echo "</a>";
				}
				echo"</td>";
				echo "<td>" . $page->post_type;
				echo "<a class='lrfilter' href='" . $current_url . "&post_type_filter=" . $page->post_type . "'>filter</a>";
				echo "</td>";
				echo "<td><div class='lrp_submit'>" . str_replace("$$$", $page->ID, $output);
				$current_user = wp_get_current_user();
				echo "</div><button class='button button-primary button-large' onclick='javascript:lrp_submit(" . $page->ID, ", " . $current_user->ID . ", true, false)'>Submit Document</button>";
				echo "</td>";
				echo "</tr>";
					
			}
			
			?>
				</tbody>
			</table> 
			<script type="text/javascript" language="javascript">
				jQuery(document).ready(function() 
					{ 
						jQuery("#myTable").tablesorter(); 
					} 
				);
			</script><?PHP
			
		}
	
	}
	
	$LearningRegistryPublisherUnsubmittedManagementPage = new LearningRegistryPublisherUnsubmittedManagementPage();