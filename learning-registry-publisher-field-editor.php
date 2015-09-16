<?PHP

	class LearningRegistryPublisherFieldEditor{
	
		function __construct(){
			add_action( 'admin_head', array($this, 'prepare_editor') );	  	  
			add_action( "draft_lrfield", array($this, "save_field") );
			add_action( "publish_lrfield", array($this, "save_field") );
			add_filter( 'quicktags_settings', array($this, 'remove_buttons'));
			add_action( 'edit_form_after_title', array($this, 'edit_form_after_title') );
			add_filter( 'enter_title_here', array($this, 'custom_enter_title') );
		}
		
		function custom_enter_title( $input ) {
			global $post_type;

			if ( is_admin() && 'lrfield' == $post_type )
				return __( 'Enter your field name here', 'LearningRegistryPublisher' );

			return $input;
		}
		
		function edit_form_after_title() {
			if(isset($_GET['post_type'])){
				if($_GET['post_type']=="lrfield"){
					echo "<p>What ever the title is, will be replaced by what you type below when you use %NAME OF FIELD% in a schema - where NAME OF FIELD is the title of the document</p>";				
					echo "<p>The following are reserved LINK, TITLE, CONTENT, KEYWORDS and DATE</p>";				
				}
			}else{
				global $post;
				if(isset($post)){	
					if($post->post_type=="lrfield"){
						echo "<p>What ever the title is, will be replaced by what you type below when you use %NAME OF FIELD% in a schema - where NAME OF FIELD is the title of the document</p>";
						echo "<p>The following are reserved LINK, TITLE, CONTENT, KEYWORDS and DATE</p>";				
					}
				}
			}
		}
		
		function remove_buttons( $qt  ) {
			if(isset($_GET['post_type'])){
				if($_GET['post_type']=="lrfield"){
					$qt['buttons'] = ',';
				}
			}else{
				global $post;
				if($post->post_type=="lrfield"){
					$qt['buttons'] = ',';	
				}
			}
			return $qt;
		}
		
		
		function prepare_editor( ) {
			if(isset($_GET['post_type'])){
				if($_GET['post_type']=="lrfield"){
					remove_action( 'media_buttons', 'media_buttons' );
					add_filter( 'user_can_richedit' , '__return_false', 50 );				
				}
			}else{
				global $post;
				if(isset($post)){	
					if($post->post_type=="lrfield"){
						remove_action( 'media_buttons', 'media_buttons' );
						add_filter( 'user_can_richedit' , '__return_false', 50 );	
					}
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
			<?PHP
		
		}
		
		function test_field($post_id){
		
			if(!get_post_meta($post->ID,"lrfield_tested",true)){
				echo "testing the node then";
				update_post_meta($post_id, "lrfield_test_results", "test results are as follows");
			}
		
		}
		
		function test_results(){
			
			global $post;
			if($post->post_type=="lrfield"){
				echo "<div>" . get_post_meta($post->ID, "lrfield_test_results", single) . "</div>";
			}
			
		}
		
		function save_field($post_id){
			
			update_post_meta($post_id, "lrnode_url", $_POST['lrnode_url']);
			update_post_meta($post_id, "lrnode_port", $_POST['lrnode_port']);
			update_post_meta($post_id, "lrnode_username", $_POST['lrnode_username']);
			update_post_meta($post_id, "lrnode_password", $_POST['lrnode_password']);
		
		}
	
	}
	
	$LearningRegistryPublisherFieldEditor = new LearningRegistryPublisherFieldEditor();