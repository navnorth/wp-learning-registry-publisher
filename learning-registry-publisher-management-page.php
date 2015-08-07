<?PHP

	class LearningRegistryPublisherDocumentManagementPage{
	
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
			add_submenu_page( 'edit.php?post_type=lrnode', 'Manage documents', 'Manage document submissions', 'LearningRegistryPublisherHistory', 'lrp_manage_documents', array($this, 'admin_page') );
		}

		function admin_page(){
		
			global $wpdb;
			
			$current_url = "edit.php?post_type=lrnode&page=lrp_manage_documents";
			
			?><h2>All documents sent to a Learning Registry Node</h2><?PHP
		
			$querystr = "
				SELECT " . $wpdb->prefix . "lrp_documents_history.* 
				FROM " . $wpdb->prefix . "lrp_documents_history ";
				
			$filter = false;	
			
			if(isset($_GET['lrpost'])){
				$querystr .= "WHERE post = " . $_GET['lrpost'];
				$filter = true;
			}
	
			if(isset($_GET['lrnode'])){
				$querystr .= "WHERE lrnode = " . $_GET['lrnode'];
				$filter = true;
			}
			
			if(isset($_GET['lrkey'])){
				$querystr .= "WHERE lrkey = " . $_GET['lrkey'];
				$filter = true;
			}
			
			if(isset($_GET['lrschema'])){
				$querystr .= "WHERE lrschema = " . $_GET['lrschema'];
				$filter = true;
			}
			
			if(isset($_GET['lruser'])){
				$querystr .= "WHERE lruser = " . $_GET['lruser'];
				$filter = true;
			}
				
			if(isset($_GET['lrdate'])){
				$querystr .= "WHERE date_submitted < " . ($_GET['lrdate'] - 3600) . "";
				$filter = true;
			}	
				
			$querystr .= " ORDER BY " . $wpdb->prefix . "lrp_documents_history.id DESC";

			$pageposts = $wpdb->get_results($querystr, OBJECT);
			
			if($filter){
				echo "<p><a href='" . $current_url . "'>Remove filters</a></p>";
			}
			
			?><table id="myTable" class="tablesorter"> 
						<thead> 
							<tr> 
								<th>WordPress Document ID (click to sort)</th> 
								<th>LR Node Document ID (click to sort)</th> 
								<th>Action (click to sort)</th> 
								<th>Title (click to sort)</th> 
								<th>Node (click to sort)</th> 
								<th>Key (click to sort)</th> 
								<th>Schema (click to sort)</th> 
								<th>User (click to sort)</th> 
								<th>Date (click to sort)</th> 
							</tr> 
						</thead> 
						<tbody>
					<?PHP
			
			foreach($pageposts as $page){
				
				$post = get_post($page->post);
				$title = $post->post_title; 
				$node = get_post($page->lrnode);
				$key = get_post($page->lrkey);
				$schema = get_post($page->lrschema);
				$user = get_userdata($page->lruser);
				$date = date("G:i:s F, jS Y", $page->date_submitted);
								
				echo "<tr>";
				echo "<td>";
				if(current_user_can("edit_others_posts")){
					echo "<a href='post.php?post=" . $page->post . "&action=edit'>";
				}
				echo $page->post;
				if(current_user_can("edit_others_posts")){
					echo "</a>";
				}
				echo "<a class='lrfilter' href='" . $current_url . "&lrpost=" . $page->post . "'>filter</a>";
				echo "<td>";
				echo "<a href='https://" . get_post_meta($node->ID, "lrnode_url", true) . "/obtain?request_id=" . $page->lrdocid . "&by_doc_ID=T'>" . $page->lrdocid . "</a>";
				echo"</td>";
				echo"<td>";
				echo $page->lraction;
				echo"</td>";
				echo "<td>";
				if(current_user_can("edit_others_posts")){
					echo "<a href='post.php?post=" . $page->post . "&action=edit'>";
				}
				echo $title;
				if(current_user_can("edit_others_posts")){
					echo "</a>";
				}
				echo "<a class='lrfilter' href='" . $current_url . "&lrpost=" . $page->post . "'>filter</a>";
				echo"</td>";				
				echo "<td>";
				if(current_user_can("edit_others_lrnodes")){
					echo "<a href='post.php?post=" . $node->ID . "&action=edit'>";
				}
				echo $node->post_title;
				if(current_user_can("edit_others_lrnodes")){
					echo "</a>";
				}
				echo "<a class='lrfilter' title='filter by this' href='" . $current_url . "&lrnode=" . $node->ID . "'>filter</a>";
				echo"</td>";
				echo "<td>";
				if($page->lrkey!=0){
					if(current_user_can("edit_others_lrkeys")){
						echo "<a href='post.php?post=" . $key->ID . "&action=edit'>";
					}
					echo $key->post_title;
					if(current_user_can("edit_others_lrkeys")){
						echo "</a>";
					}
					echo "<a class='lrfilter' title='filter by this' href='" . $current_url . "&lrkey=" . $key->ID . "'>filter</a>";
				}
				echo "</td>";
				echo "<td>";
				if(current_user_can("edit_others_lrschemas")){
					echo "<a href='post.php?post=" . $schema->ID . "&action=edit'>";
				}
				echo $schema->post_title;
				if(current_user_can("edit_others_lrschemas")){
					echo "</a>";
				}
				echo "<a class='lrfilter' title='filter by this' href='" . $current_url . "&lrschema=" . $schema->ID . "'>filter</a>";
				echo "</td>";
				echo "<td>";
				if(current_user_can("edit_users")){
					echo "<a href='user-edit.php?user_id=" . $user->ID . "'>";
				}
				echo $user->data->user_nicename;
				if(current_user_can("edit_users")){
					echo "</a>";
				}
				echo "<a class='lrfilter' title='filter by this' href='" . $current_url . "&lruser=" . $user->ID . "'>filter</a>";
				echo "</td>";
				echo "<td>" . $date;
				echo "<a class='lrfilter' href='" . $current_url . "&lrdate=" . $page->date_submitted . "'>filter</a>";
				echo  "</td>";
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
	
	$LearningRegistryPublisherDocumentManagementPage = new LearningRegistryPublisherDocumentManagementPage();