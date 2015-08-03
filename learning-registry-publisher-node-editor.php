<?PHP

	class LearningRegistryPublisherNodeEditor{
	
		function __construct(){
			add_action( 'admin_head', array($this, 'prepare_editor') );	  	  
			add_action( "draft_lrnode", array($this, "save_node") );
			add_action( "publish_lrnode", array($this, "save_node") );
			add_action( "trash_lrnode", array($this, "trash_node") );
			add_filter( 'enter_title_here', array($this, 'custom_enter_title') );
		}
		
		function custom_enter_title( $input ) {
			global $post_type;

			if ( is_admin() && 'lrnode' == $post_type )
				return __( 'Enter your node name here', 'LearningRegistryPublisher' );

			return $input;
		}
		
		function prepare_editor( ) {
			if(isset($_GET['post_type'])){
				if($_GET['post_type']=="lrnode"){
					add_meta_box( "lrnode", __("Add a new Learning Registry Node"), array($this, "editor_meta_box"), "lrnode");
				}
			}else{
				global $post;
				if($post->post_type=="lrnode"){
					add_meta_box( "lrnode", __("Edit an existing Learning Registry Node"), array($this, "editor_meta_box"), "lrnode");
				}
			}
		}
		
		function editor_meta_box(){
		
			global $post; 
			
			?>
			<p>Node URL</p>
			<input type="text" name="lrnode_url" style="width:100%" value="<?PHP echo get_post_meta($post->ID, "lrnode_url", true); ?>" />
			<p>Node Port</p>
			<input type="text" name="lrnode_port" style="width:100%" value="<?PHP echo get_post_meta($post->ID, "lrnode_port", true); ?>" />
			<p>Username</p>
			<input type="text" name="lrnode_username" style="width:100%" value="<?PHP echo get_post_meta($post->ID, "lrnode_username", true); ?>" />
			<p>Password</p>
			<input type="password" name="lrnode_password" style="width:100%" value="<?PHP echo get_post_meta($post->ID, "lrnode_password", true); ?>" />
			<p>OAuth Signature - without this basic auth is used</p>
			<input type="text" name="lrnode_oauthsig" style="width:100%" value="<?PHP echo get_post_meta($post->ID, "lrnode_oauthsig", true); ?>" />
			<p>Document Owner</p>
			<input type="text" name="lrnode_docowner" style="width:100%" value="<?PHP echo get_post_meta($post->ID, "lrnode_docowner", true); ?>" />
			<p>Document Curator</p>
			<input type="text" name="lrnode_doccurator" style="width:100%" value="<?PHP echo get_post_meta($post->ID, "lrnode_doccurator", true); ?>" />
			<p>Document Submitter</p>
			<input type="text" name="lrnode_docsubmitter" style="width:100%" value="<?PHP echo get_post_meta($post->ID, "lrnode_docsubmitter", true); ?>" />
			<p>Document Submitter type</p>
			<select name="lrnode_docsubmittertype">
				<option <?PHP if(get_post_meta($post->ID, "lrnode_docsubmittertype", true)=="anonymous"){ echo " selected='true' "; } ?> value="anonymous">Anonymous</option>
				<option <?PHP if(get_post_meta($post->ID, "lrnode_docsubmittertype", true)=="user"){ echo " selected='true' "; } ?> value="user">User</option>
				<option <?PHP if(get_post_meta($post->ID, "lrnode_docsubmittertype", true)=="agent"){ echo " selected='true' "; } ?> value="agent">Agent</option>
			</select>
			<?PHP
				$https = get_post_meta($post->ID, "lrnode_https", true); 
				$sign = get_post_meta($post->ID, "lrnode_sign", true); 
			?>
			<p>Use HTTPS <input type="checkbox" name="lrnode_https" <?PHP if(($https)){ echo " checked "; } ?>  /></p>
			<p>Sign documents <input type="checkbox" name="lrnode_sign"  <?PHP if(($sign)){ echo " checked "; } ?>  /></p>
			<p>Default Node <input type="checkbox" name="lrnode_default"  <?PHP if(get_option("lrnode_default")==$post->ID){ echo " checked "; } ?>  /></p>
			<?PHP
		
		}
		
		function save_node($post_id){
			
			update_post_meta($post_id, "lrnode_url", $_POST['lrnode_url']);
			update_post_meta($post_id, "lrnode_port", $_POST['lrnode_port']);
			update_post_meta($post_id, "lrnode_username", $_POST['lrnode_username']);
			update_post_meta($post_id, "lrnode_password", $_POST['lrnode_password']);
			update_post_meta($post_id, "lrnode_oauthsig", $_POST['lrnode_oauthsig']);
			update_post_meta($post_id, "lrnode_docowner", $_POST['lrnode_docowner']);
			update_post_meta($post_id, "lrnode_doccurator", $_POST['lrnode_doccurator']);
			update_post_meta($post_id, "lrnode_docsubmitter", $_POST['lrnode_docsubmitter']);
			update_post_meta($post_id, "lrnode_docsubmittertype", $_POST['lrnode_docsubmittertype']);
			update_post_meta($post_id, "lrnode_https", $_POST['lrnode_https']);
			update_post_meta($post_id, "lrnode_sign", $_POST['lrnode_sign']);
			if($_POST['lrnode_default']=="on"){
				update_option("lrnode_default", $post_id);
			}
		
		}
		
		function trash_node($post_id){
			if(get_option("lrnode_default")==$post_id){
				delete_option("lrnode_default");
			}
		}
	
	}
	
	$LearningRegistryPublisherNodeEditor = new LearningRegistryPublisherNodeEditor();