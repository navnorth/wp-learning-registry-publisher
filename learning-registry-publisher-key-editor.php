<?PHP

	class LearningRegistryPublisherKeyEditor{
	
		function __construct(){
			add_action( 'admin_head', array($this, 'prepare_editor') );	  	  
			add_filter( 'quicktags_settings', array($this, 'remove_buttons'));
			add_filter( 'enter_title_here', array($this, 'custom_enter_title') );
			add_action( "draft_lrkey", array($this, "save_key") );
			add_action( "publish_lrkey", array($this, "save_key") );
			add_action( "trash_key", array($this, "trash_key") );
		}
		
		function custom_enter_title( $input ) {
			global $post_type;

			if ( is_admin() && 'lrkey' == $post_type )
				return __( 'Enter your key name here', 'LearningRegistryPublisher' );

			return $input;
		}
		
		function prepare_editor( ) {
			if(isset($_GET['post_type'])){
				if($_GET['post_type']=="lrkey"){
					remove_action( 'media_buttons', 'media_buttons' );
					add_filter( 'user_can_richedit' , '__return_false', 50 );	
					add_meta_box( "lrkey", __("Key Password"), array($this, "editor_meta_box"), "lrkey");			
				}
			}else{
				global $post;
				if($post->post_type=="lrkey"){
					remove_action( 'media_buttons', 'media_buttons' );
					add_filter( 'user_can_richedit' , '__return_false', 50 );				
					add_meta_box( "lrkey", __("Key Passphrase"), array($this, "editor_meta_box"), "lrkey");
				}
			}
		}
		
		function editor_meta_box(){
		
			global $post; 
			
			?>
			<p>Passphrase</p>
			<input type="password" style="width:100%" name="lrkey_passphrase" value="<?PHP echo get_post_meta($post->ID, "lrkey_passphrase", true); ?>" />
			<p>Public Key URL</p>
			<input type="text" name="lrkey_url" style="width:100%" value="<?PHP echo get_post_meta($post->ID, "lrkey_url", true); ?>" />
			<p>Signer email</p>
			<input type="text" name="lrkey_signer" style="width:100%" value="<?PHP echo get_post_meta($post->ID, "lrkey_signer", true); ?>" />
			<p>Default Key <input type="checkbox" name="lrkey_default"  <?PHP if(get_option("lrkey_default")==$post->ID){ echo " checked "; } ?>  /></p>
			<?PHP
		
		}
		
		function remove_buttons( $qt  ) {
			if(isset($_GET['post_type'])){
				if($_GET['post_type']=="lrkey"){
					$qt['buttons'] = ',';
				}
			}else{
				global $post;
				if($post->post_type=="lrkey"){
					$qt['buttons'] = ',';	
				}
			}
			return $qt;
		}
		
		function save_key($post_id){
			update_post_meta($post_id, "lrkey_passphrase", $_POST['lrkey_passphrase']);
			update_post_meta($post_id, "lrkey_url", $_POST['lrkey_url']);
			update_post_meta($post_id, "lrkey_signer", $_POST['lrkey_signer']);
			if($_POST['lrkey_default']=="on"){
				update_option("lrkey_default", $post_id);
			}
		}
		
		function trash_key($post_id){
			if(get_option("lrkey_default")==$post_id){
				delete_option("lrkey_default");
			}
		}
		
	
	}
	
	$LearningRegistryPublisherKeyEditor = new LearningRegistryPublisherKeyEditor();