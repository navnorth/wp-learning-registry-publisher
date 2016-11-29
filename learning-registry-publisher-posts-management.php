<?PHP

	class LearningRegistryPublisherPostsManagement{
	
		function __construct(){
			add_action( 'admin_head', array($this, 'prepare_editor') );	  	  
			add_action( 'admin_enqueue_scripts', array($this, 'enqueue_scripts') );	  	  
		}
		
		function enqueue_scripts(){
			if(isset($_GET['post_type'])){
				if(strpos($_GET['post_type'],"lr")!==0){
					if(current_user_can("LearningRegistryPublisherHistory") || current_user_can("LearningRegistryPublisherManageDocument")){
						wp_enqueue_script("jquery");
						wp_enqueue_script("jquery-ui-tabs");
						wp_enqueue_script('lrp_tabs', plugins_url('/js/lrp_tabs.js', __FILE__), array('jquery'), '1.0.0', true );
						wp_enqueue_script('lrp_table_sort', plugins_url('/js/jquery.tablesorter.min.js', __FILE__), array("jquery"), '1.0.0', true );
						wp_enqueue_style('lrp_tabs_css', plugins_url('/css/lrp_tabs.css', __FILE__));
						wp_enqueue_style('lrp-tabs-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/smoothness/jquery-ui.css', false, PLUGIN_VERSION, false);
					}
				}
			}else{
				global $post;
				if(isset($post)){
					if(strpos($post->post_type,"lr")){
						if(current_user_can("LearningRegistryPublisherHistory") || current_user_can("LearningRegistryPubliisherManageDocument")){
							wp_enqueue_script("jquery");
							wp_enqueue_script("jquery-ui-tabs");
							wp_enqueue_script('lrp_tabs', plugins_url('/js/lrp_tabs.js', __FILE__), array('jquery'), '1.0.0', true );
							wp_enqueue_script( 'lrp_table_sort', plugins_url('/js/jquery.tablesorter.min.js', __FILE__), array("jquery"), '1.0.0', true );
							wp_enqueue_style('lrp_tabs_css', plugins_url('/css/lrp_tabs.css', __FILE__));
							wp_enqueue_style('lrp-tabs-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/smoothness/jquery-ui.css', false, '', false);
						}
					}
				}
			}
		}
		
		function prepare_editor( ) {
			if(isset($_GET['post_type'])){
				if(strpos($_GET['post_type'],"lr")!==0){
					if(current_user_can("LearningRegistryPublisherHistory") || current_user_can("LearningRegistryPublisherManageDocument")){
						add_meta_box( "lrmanage", __("Learning Registry Document History"), array($this, "editor_meta_box"));
					}
				}
			}else{
				global $post;
				if(isset($post)){
					if(strpos($post->post_type,"lr")!==0){
						if(current_user_can("LearningRegistryPublisherHistory") || current_user_can("LearningRegistryPubliisherManageDocument")){
							add_meta_box( "lrmanage", __("Learning Registry Document History"), array($this, "editor_meta_box"));
						}
					}
				}
			}
		}
		
		function editor_meta_box(){
		
			global $post, $wpdb; 
			
			$this_post = $post->ID;
			
			$args = array(
							'posts_per_page'   => 9999999,
							'orderby'          => 'post_title',
							'order'            => 'ASC',
							'post_type'        => 'lrschema',
							'post_status'      => 'publish',
							'suppress_filters' => true 
						);
			$schema_posts = get_posts( $args );
			
			$args = array(
							'posts_per_page'   => 9999999,
							'orderby'          => 'post_title',
							'order'            => 'ASC',
							'post_type'        => 'lrnode',
							'post_status'      => 'publish',
							'suppress_filters' => true 
						);
			$node_posts = get_posts( $args );
			
			if(count($schema_posts)==0 || count($node_posts)==0){

				?><P>You need to create a <a href="post-new.php?post_type=lrnode">node</a> and <a href="post-new.php?post_type=lrschema">schema</a> before submitting</P><?PHP
			
			}else{
			
				if(current_user_can("LearningRegistryPublisherHistory")){
				
					?><div id="tabs">
					  <ul>
						<li><a href="#tabs-1">Basic</a></li>
						<li><a href="#tabs-2">Advanced</a></li>
					  </ul>
					  <div id="tabs-1"><?PHP
					
					$querystr = "
						SELECT " . $wpdb->prefix . "lrp_documents_history.* 
						FROM " . $wpdb->prefix . "lrp_documents_history
						WHERE " . $wpdb->prefix . "lrp_documents_history.post = " . $this_post . " 
						ORDER BY " . $wpdb->prefix . "lrp_documents_history.id DESC
					 ";

					$pageposts = $wpdb->get_results($querystr, OBJECT);
					
					$counter = 1;
					
					if(count($pageposts) == 0){
					
						echo "<p id='lrp_last_publish'>";
						echo "This document has never been submitted";
						echo "</p>";
						echo "</div><div id='tabs-2'>";
						?><table id="myTable" class="tablesorter"> 
							<thead> 
								<tr> 
									<th>Document ID</th>
									<th>Node</th> 
									<th>Key</th> 
									<th>Schema</th> 
									<th>User</th> 
									<th>Date</th> 
								</tr> 
							</thead> 
							<tbody>
								<tr><td>No Submissions Yet</td></tr><?PHP
					
					}else{
					
						$recent = $pageposts[0];
					
						foreach($pageposts as $page){
							
							$node = get_post($page->lrnode);
							if($page->lrkey!=0){
								$key = get_post($page->lrkey);
							}else{
								$key = new StdClass();
								$key->ID = 0;
								$key->post_title = "Not signed";
							}
							$schema = get_post($page->lrschema);
							$user = get_userdata($page->lruser);
							$date = date("G:i:s F, jS Y", $page->date_submitted);
							
							if($counter==1){
							
								$node = get_post($recent->lrnode);
								if($recent->lrkey!=0){
									$key = get_post($recent->lrkey);
								}else{
									$key = new StdClass();
									$key->ID = 0;
									$key->post_title = "Not signed";
								}
								$schema = get_post($recent->lrschema);
								$user = get_userdata($recent->lruser);
								$date = date("G:i:s F, jS Y", $recent->date_submitted);
								
								$current_user = wp_get_current_user();
							
								echo "<p id='lrp_last_publish'>";
								echo "Last ";
								if($page->lraction=="update"){
									echo $page->lraction . "d";
								}else{
									echo $page->lraction . "ed";
								}
								echo " to <a href='post.php?post=" . $node->ID . "&action=edit'>" . $node->post_title . "</a> | <a href='https://" . get_post_meta($node->ID, "lrnode_url", true) . "/obtain?request_id=" . $page->lrdocid . "&by_doc_ID=T'>" . get_post_meta($node->ID, "lrnode_url", true) . "</a>) on " . $date;
								echo "</p>";
								if($key->ID!=0){
									echo "<a class='button button-primary button-large' onclick='javascript:lrp_update(" . $this_post, ", " . $node->ID . ", " . $key->ID . ", " . $schema->ID . ", " . $current_user->ID . ", \"" . $page->lrdocid . "\")'>Update Document</a>";
								}
								echo "</div><div id='tabs-2'>";
								?><table id="myTable" class="tablesorter"> 
									<thead> 
										<tr> 
											<th>Document ID</th>
											<th>Action</th>
											<th>Node</th> 
											<th>Key</th> 
											<th>Schema</th> 
											<th>User</th> 
											<th>Date</th> 
											<th></th> 
										</tr> 
									</thead> 
									<tbody>
								<?PHP
								
								$counter++;							
								array_push($pageposts, $recent);
								
							}
						
							echo "<tr>";				
							echo "<td>";
							echo "<a href='https://" . get_post_meta($node->ID, "lrnode_url", true) . "/obtain?request_id=" . $page->lrdocid . "&by_doc_ID=T'>" . $page->lrdocid  . "</a>";
							echo "</td>";
							echo"<td>";
							echo $page->lraction;
							echo "</td>";
							echo "<td>";
							if(current_user_can("edit_others_lrnodes")){
								echo "<a href='post.php?post=" . $node->ID . "&action=edit'>";
							}
							echo $node->post_title;
							if(current_user_can("edit_others_lrnodes")){
								echo "</a>";
							}
							echo"</td>";
							echo "<td>";
							if(current_user_can("edit_others_lrkeys")){
								if($key->ID!="0"){
									echo "<a href='post.php?post=" . $key->ID . "&action=edit'>";
								}
							}
							echo $key->post_title;
							if(current_user_can("edit_others_lrkeys")){
								if($key->ID!="0"){
									echo "</a>";
								}
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
							echo "</td>";
							echo "<td>";
							if(current_user_can("edit_users")){
								echo "<a href='user-edit.php?user_id=" . $user->ID . "'>";
							}
							echo $user->data->user_nicename;
							if(current_user_can("edit_users")){
								echo "</a>";
							}
							echo "</td>";
							echo "<td>" . $date . "</td>";
							echo "<td>";
							if($key->ID!=0){
								echo "<a class='button button-primary button-large' onclick='javascript:lrp_update(" . $this_post, ", " . $node->ID . ", " . $key->ID . ", " . $schema->ID . ", " . $current_user->ID . ", \"" . $page->lrdocid . "\" )'>Update Document</a></td>";
							}
							echo "</tr>";
								
						}
						
					}
				
				?>
					</tbody>
				</table>
				<?php if(strpos($post->post_type,"lr")){ ?>
				<script type="text/javascript" language="javascript">
					jQuery(document).ready(function() 
						{ 
							jQuery("#myTable").tablesorter(); 
						} 
					);
				</script>
				<?php } ?>
				<?PHP
					
				}
				
				?></div></div><?PHP
							
				if(current_user_can("LearningRegistryPublisherManageDocument")){
				
					echo "<h3 id='lrp_submit'>Submit Document</h3>";
		
					$signon = "";
		
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
							
							?><p>Choose Node : <select id="lrnode" onchange="javascript:select_update('#lrnode');"><option>Select a Node</option><?PHP
							foreach($posts as $post){
								$sign_status = get_post_meta($post->ID, "lrnode_sign", true);
								if ($sign_status)
									$signon = 'data-signing="true"';
								else
									$signon = "";
								?><option <?PHP if(get_option("lrnode_default")==$post->ID){ echo " selected='true' "; } ?> value="<?PHP echo $post->ID; ?>" <?php echo $signon; ?>><?PHP echo $post->post_title; ?></option><?PHP
							}
							?></select></p><?PHP
						
						}else{
							?><input id="lrnode" type="hidden" value="<?PHP echo get_option("lrnode_default"); ?>" /><?PHP
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
							
							?><p>Choose Node : <select id="lrnode"><option>Select a node</option><?PHP
							foreach($posts as $post){
								$sign_status = get_post_meta($post->ID, "lrnode_sign", true);
								if ($sign_status)
									$signon = 'data-signing="true"';
								else
									$signon = "";
								?><option value="<?PHP echo $post->ID; ?>" <?php echo $signon; ?>><?PHP echo $post->post_title; ?></option><?PHP
							}
							?></select></p><?PHP
					
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
							
							?><p>Choose Schema : <select id="lrschema"><option>Select a Schema</option><?PHP
							foreach($posts as $post){
								?><option <?PHP if(get_option("lrschema_default")==$post->ID){ echo " selected='true' "; } ?> value="<?PHP echo $post->ID; ?>"><?PHP echo $post->post_title; ?></option><?PHP
							}
							?></select></p><?PHP
						
						}else{
							?><input id="lrschema" type="hidden" value="<?PHP echo get_option("lrschema_default"); ?>" /><?PHP
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
							
							?><p>Choose Schema : <select id="lrschema"><option>Select a Schema</option><?PHP
							foreach($posts as $post){
								?><option value="<?PHP echo $post->ID; ?>"><?PHP echo $post->post_title; ?></option><?PHP
							}
							?></select></p><?PHP
					
					}
					
					$sign = get_post_meta(get_option("lrnode_default"), "lrnode_sign", true); 
					
					$key_disabled = "";
					
					if (!$sign)
						$key_disabled = "disabled";
						
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
								
								?><p>Choose key : <select id="lrkey" <?php echo $key_disabled; ?>><option>Select a Key</option><?PHP
								foreach($posts as $post){
									?><option <?PHP if(get_option("lrkey_default")==$post->ID){ echo " selected='true' "; } ?> value="<?PHP echo $post->ID; ?>"><?PHP echo $post->post_title; ?></option><?PHP
								}
								?></select></p>
								<?php
								if (empty($posts)) {
									?>
									<input id="lrkey" type="hidden" value="0" />
									<?php
								}
								?>
								<p>If you don't select a key it will be an unsigned submission</p><?PHP
							
							}else{
								?><input id="lrkey" type="hidden" value="<?PHP echo get_option("lrkey_default"); ?>" /><?PHP
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
								
								?><p>Choose key : <select id="lrkey" <?php echo $key_disabled; ?>><option>Select a Key</option><?PHP
								foreach($posts as $post){
									?><option value="<?PHP echo $post->ID; ?>"><?PHP echo $post->post_title; ?></option><?PHP
								}
								?></select></p>
								<?php
								if (empty($posts)) {
									?>
									<input id="lrkey" type="hidden" value="0" />
									<?php
								}
								?>
								<p>If you don't select a key it will be an unsigned submission</p><?PHP
						
						}
						
					
					$current_user = wp_get_current_user();
					echo "<a class='button button-primary button-large' onclick='javascript:lrp_submit(" . $this_post, ", " . $current_user->ID . ", false, true)'>Submit Document</a>";
				}
				
			}
		
		}
		
	}
	
	$LearningRegistryPublisherPostsManagement = new LearningRegistryPublisherPostsManagement();