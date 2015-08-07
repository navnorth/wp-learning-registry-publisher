<?php

class LearningRegistryPublisherAjax{
	
	public function __construct() {	
				
		add_action('admin_enqueue_scripts', array($this, 'display_js'));				
		add_action('wp_ajax_lrp_submit', array($this, 'submit_to_lr'));				
		add_action('wp_ajax_lrp_update', array($this, 'update_lr'));				
		
	}
	
	function display_js() {
	
		wp_enqueue_script('lrp_submit_ajax', plugins_url('/js/lrp_submit.js', __FILE__), array(), '1.0.0', true );
		wp_localize_script('lrp_submit_ajax', 'lrp_submit_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'answerNonce' => wp_create_nonce( 'lrp_submit_js_nonce' ) ) );
		
	}
	
	function prepare_schema($post_id, $schema){
		$post = get_post($post_id);
		$this->content = $schema->post_content;
		$this->content = str_replace("%LINK%", $post->guid, $this->content);
		$this->content = str_replace("%TITLE%", $post->post_title, $this->content);
		$this->content = str_replace("%CONTENT%", $post->post_content, $this->content);
		$this->content = str_replace("%DATE%", $post->post_date, $this->content);
		$this->keywords = array();
		$tags = wp_get_post_tags($post_id);
		foreach($tags as $tag){
			array_push($this->keywords, $tags[$tag]->name);
		}
		$categories = wp_get_post_categories($post_id);
		foreach($categories as $category){
			$cat = get_category( $category );
			array_push($this->keywords, $cat->name);
		}
		$content = str_replace("%KEYWORDS%", implode(",", $this->keywords), $this->content);
		$args = array(
							'posts_per_page'   => 9999999,
							'orderby'          => 'post_title',
							'order'            => 'ASC',
							'post_type'        => 'lrfield',
							'post_status'      => 'publish',
							'suppress_filters' => true 
						);
		$fields = get_posts( $args );
		foreach($fields as $field){
			$this->content = str_replace("%" . strtoupper($field->post_title) . "%", $field->post_content, $this->content);
		}
	}
	
	function update_lr(){
	
		if(wp_verify_nonce($_REQUEST['nonce'], 'lrp_submit_js_nonce')){
		
			$submit = new LearningRegistryPublisherSubmit();
			$node = get_post($_POST['node']);
			$signing = false;
			if(get_post_meta($node->ID, "lrnode_sign")=="on"){
				$signing = true;
			}
			
			$submit->update_initialise(
													get_post_meta($node->ID, "lrnode_url", true), 
													get_post_meta($node->ID, "lrnode_username", true), 
													get_post_meta($node->ID, "lrnode_https", true), 
													get_post_meta($node->ID, "lrnode_password", true),
													$signing,
													get_post_meta($node->ID, "lrnode_oauthsig"),
													array(
															get_post_meta($node->ID, "lrnode_url", true), 
															$_POST['lrdocid']
														)
													);
													
			$submit->LRDocument->populateDocument($submit->LR);

			$schema = get_post($_POST['schema']);
			$this->prepare_schema($_POST['post'], $schema);
			$submit->LR->setResFields(
				array(
				'payload_schema' => array(get_post_meta($schema->ID, "lrschema_payload", true)), 
				'resource_locator' => $post->guid, 
				'keys' => $this->keywords, 
				'resource_data' => htmlspecialchars_decode($this->content), 
				'replaces' => $_POST['lrdocid']
				)
			);
			$submit->LR->createDocument();
			
			if ($submit->LR->verifyUpdatedDocument()) {
				$submit->LR->finaliseDocument();
				$submit->LR->UpdateService();
				if ($submit->LR->getOK()!="1") {
					echo "the Error is " . $submit->LR->getError() . "<br />";
				} else {
					$this->update_response($submit->LR->getDocID(), $node, $key, $schema, $user, $date);
				}
			}else{
				print_r($submit->LR->errors);
			}
		}
		
		die();
		
	}
	
	function submit_to_lr(){
	
		if(wp_verify_nonce($_REQUEST['nonce'], 'lrp_submit_js_nonce')){
		
			$date = date("G:i:s F, jS Y", time());
			$node = get_post($_POST['node']);
			$post = get_post($_POST['post']);
			$key = get_post($_POST['key']);
			$schema = get_post($_POST['schema']);
			$this->prepare_schema($_POST['post'], $schema);
			$submit = new LearningRegistryPublisherSubmit();
			$signing = false;
			if(get_post_meta($node->ID, "lrnode_sign")=="on"){
				$signing = true;
			}
			$submit->initialise( 
									get_post_meta($node->ID, "lrnode_url", true), 
									get_post_meta($node->ID, "lrnode_username", true), 
									get_post_meta($node->ID, "lrnode_https", true), 
									get_post_meta($node->ID, "lrnode_password", true),
									$signing,
									get_post_meta($node->ID, "lrnode_oauthsig")
								);
			$submit->LRDocument->setIdFields(
				array(
				'curator' => get_post_meta($node->ID, "lrnode_doccurator", true),
				'owner' => get_post_meta($node->ID, "lrnode_docowner", true),
				'signer' => get_post_meta($key->ID, "lrkey_signer", true),
				'submitter_type' => get_post_meta($node->ID, "lrnode_docsubmittertype", true),
				'submitter' => get_post_meta($node->ID, "lrnode_docsubmitter", true)
				)
			);	
			
			$submit->LRDocument->setResFields(
				array(
				'payload_schema' => array(get_post_meta($schema->ID, "lrschema_payload", true)), 
				'resource_locator' => $post->guid, 
				'keys' => $this->keywords, 
				'resource_data' => htmlspecialchars_decode($this->content), 
				)
			);
			$submit->LR->createDocument();
			if ($submit->LR->verifyDocument()) {
				$submit->LR->finaliseDocument();
				$submit->LR->PublishService();
				if ($submit->LR->getOK()!="1") {
					echo "the Error is " . $submit->LR->getError() . "<br />";
				} else {
					$this->prepare_response($submit->LR->getDocID(), $node, $key, $schema, $user, $date);
				}
			}else{
				print_r($submit->LR->errors);
			}
		
		}
		
		die();
		
	}
	
	function update_response($lr_doc_id, $node, $key, $schema, $user, $date){	
			
		$user = $_POST['user'];
		
		$date = date("G:i:s F, tS Y", time());
		
		$response = new stdClass();
		$response->last =  "Updated on <a href='post.php?post=" . $_POST['node'] . "&action=edit'>" . $node->post_title . "</a> | <a href='https://" . get_post_meta($node->ID, "lrnode_url", true) . "/obtain?request_id=" . $lr_doc_id . "&by_doc_ID=T'>" . get_post_meta($node->ID, "lrnode_url", true) . "</a>) on " . $date;
		$response->publish = "<tr>";				
		$response->publish .= "<td>";
		$response->publish .= "<a href='https://" . get_post_meta($node->ID, "lrnode_url", true) . "/obtain?request_id=" . $lr_doc_id . "&by_doc_ID=T'>" . $lr_doc_id . "</a>";
		$response->publish .= "</td>";
		$response->publish .= "<td>";
		$response->publish .= "Update";
		$response->publish .= "</td>";
		$response->publish .= "<td>";
		if(current_user_can("edit_others_lrnodes")){
			$response->publish .= "<a href='post.php?post=" . $node->ID . "&action=edit'>";
		}
		$response->publish .= $node->post_title;
		if(current_user_can("edit_others_lrnodes")){
			$response->publish .= "</a>";
		}
		$response->publish .= "</td>";
		$response->publish .= "<td>";
		if(current_user_can("edit_others_lrkeys")){
			if($key->ID!=0){
				$response->publish .= "<a href='post.php?post=" . $key->ID . "&action=edit'>";
			}
		}
		if($key->ID!=0){
			$response->publish .= $key->post_title;
		}else{
			$response->publish .= "Not signed";
		}
		if(current_user_can("edit_others_lrkeys")){
			if($key->ID!=0){
				$response->publish .= "</a>";
			}
		}
		$response->publish .= "</td>";
		$response->publish .= "<td>";
		if(current_user_can("edit_others_lrschemas")){
			$response->publish .= "<a href='post.php?post=" . $schema->ID . "&action=edit'>";
		}
		$response->publish .= $schema->post_title;
		if(current_user_can("edit_others_lrschemas")){
			$response->publish .= "</a>";
		}
		$response->publish .= "</td>";
		$response->publish .= "<td>";
		if(current_user_can("edit_users")){
			$response->publish .= "<a href='user-edit.php?user_id=" . $user . "'>";
		}
		$user = get_user_by("id", $user);
		$response->publish .= $user->data->user_nicename;
		if(current_user_can("edit_users")){
			$response->publish .= "</a>";
		}
		$response->publish .= "</td>";
		$response->publish .= "<td>" . $date . "</td>";
		$response->publish .= "</tr>";
		print_r(json_encode($response));

		global $wpdb;
		
		$wpdb->insert( 
			$wpdb->prefix . 'lrp_documents_history', 
			array( 
				'post' => $_POST['post'],
				'lrnode' => $_POST['node'],
				'lrdocid' => $lr_doc_id,
				'lrkey' => $_POST['key'],
				'lrschema' => $_POST['schema'],
				'lruser' => $_POST['user'],
				'lraction' => "update",
				'date_submitted' => time()
			), 
			array( 
				'%d', 
				'%d', 
				'%s', 
				'%d', 
				'%d', 
				'%d', 
				'%s',
				'%d' 
			) 
		);
		
	}
	
	
	function prepare_response($lr_doc_id, $node, $key, $schema, $user, $date){
			
		$user = $_POST['user'];
		
		if($_POST['single']){
			$response = new stdClass();
			$response->last =  "Last published to <a href='post.php?post=" . $_POST['node'] . "&action=edit'>" . $node->post_title . "</a> | <a href='https://" . get_post_meta($node->ID, "lrnode_url", true) . "/obtain?request_id=" . $lr_doc_id . "&by_doc_ID=T'>" . get_post_meta($node->ID, "lrnode_url", true) . "</a>) on " . $date;
			$response->publish = "<tr>";				
			$response->publish .= "<td>";
			$response->publish .= "<a href='https://" . get_post_meta($node->ID, "lrnode_url", true) . "/obtain?request_id=" . $lr_doc_id . "&by_doc_ID=T'>" . $lr_doc_id . "</a>";
			$response->publish .= "</td>";
			$response->publish .= "<td>";
			if(current_user_can("edit_others_lrnodes")){
				$response->publish .= "<a href='post.php?post=" . $node->ID . "&action=edit'>";
			}
			$response->publish .= $node->post_title;
			if(current_user_can("edit_others_lrnodes")){
				$response->publish .= "</a>";
			}
			$response->publish .= "</td>";
			$response->publish .= "<td>";
			$response->publish .= "Publish";
			$response->publish .= "</td>";
			$response->publish .= "<td>";
			if(current_user_can("edit_others_lrkeys")){
				if($key->ID!=0){
					$response->publish .= "<a href='post.php?post=" . $key->ID . "&action=edit'>";
				}
			}
			if($key->ID!=0){
				$response->publish .= $key->post_title;
			}else{
				$response->publish .= "Not signed";
			}
			if(current_user_can("edit_others_lrkeys")){
				if($key->ID!=0){
					$response->publish .= "</a>";
				}
			}
			$response->publish .= "</td>";
			$response->publish .= "<td>";
			if(current_user_can("edit_others_lrschemas")){
				$response->publish .= "<a href='post.php?post=" . $schema->ID . "&action=edit'>";
			}
			$response->publish .= $schema->post_title;
			if(current_user_can("edit_others_lrschemas")){
				$response->publish .= "</a>";
			}
			$response->publish .= "</td>";
			$response->publish .= "<td>";
			if(current_user_can("edit_users")){
				$response->publish .= "<a href='user-edit.php?user_id=" . $user . "'>";
			}
			$user = get_user_by("id", $user);
			$response->publish .= $user->data->user_nicename;
			if(current_user_can("edit_users")){
				$response->publish .= "</a>";
			}
			$response->publish .= "</td>";
			$response->publish .= "<td>" . $date . "</td>";
			$response->publish .= "</tr>";
			print_r(json_encode($response));
		}
		
		global $wpdb;
		
		$wpdb->insert( 
			$wpdb->prefix . 'lrp_documents_history', 
			array( 
				'post' => $_POST['post'],
				'lrnode' => $_POST['node'],
				'lrdocid' => $lr_doc_id,
				'lrkey' => $_POST['key'],
				'lrschema' => $_POST['schema'],
				'lruser' => $_POST['user'],
				'lraction' => "publish",
				'date_submitted' => time()
			), 
			array( 
				'%d', 
				'%d', 
				'%s', 
				'%d', 
				'%d', 
				'%d', 
				'%s',
				'%d' 
			) 
		);
		
	}
	
	
	
} 

$LearningRegistryPublisherAjax = new LearningRegistryPublisherAjax();
