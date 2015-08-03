<?PHP

	class LearningRegistryPublisherSchemaEditor{
	
		function __construct(){
			add_action( 'admin_head', array($this, 'prepare_editor') );	  	  
			add_action( "draft_lrschema", array($this, "save_schema") );
			add_action( "publish_lrschema", array($this, "save_schema") );
			add_action( "trash_lrschema", array($this, "trash_schema") );
			add_filter( 'quicktags_settings', array($this, 'remove_buttons'));
			add_action( 'admin_print_footer_scripts',  array($this, 'fields_enable'));
			add_filter( 'enter_title_here', array($this, 'custom_enter_title') );
		}
		
		function custom_enter_title( $input ) {
			global $post_type;

			if ( is_admin() && 'lrschema' == $post_type )
				return __( 'Enter your Schema name here', 'LearningRegistryPublisher' );

			return $input;
		}
		
		function fields_enable(){
		
			if(isset($_GET['post_type'])){
			
				if($_GET['post_type']=="lrschema"){
			  
					echo $this->show_fields();
					
				}
				
			}else{
			
				if(isset($_GET['post'])){
				
					$post = get_post($_GET['post']);
					
					if($post->post_type == "lrschema"){
					
						echo $this->show_fields();
					
					}
				
				}
				
			}
			
		}
		
		function show_fields(){
		
			$content = '<script type="text/javascript">';
			
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
				$content .= "QTags.addButton( 'btn_lr_" . str_replace(" ", "_", $field->post_title) . "', '" . $field->post_title . "', '%" . strtoupper($field->post_title) . "%', '' );"; 								
			}
			
			$content .= "</script>";
			  
			return $content;
			  
		}
		
		function remove_buttons( $qt  ) {
			if(isset($_GET['post_type'])){
				if($_GET['post_type']=="lrschema"){
					$qt['buttons'] = ',';
				}
			}else{
				global $post;
				if($post->post_type=="lrschema"){
					$qt['buttons'] = ',';	
				}
			}
			return $qt;
		}
		
		
		function prepare_editor( ) {
			if(isset($_GET['post_type'])){
				if($_GET['post_type']=="lrschema"){
					remove_action( 'media_buttons', 'media_buttons' );
					add_filter( 'user_can_richedit' , '__return_false', 50 );
					add_meta_box( "lrschema", __("Schema Settings"), array($this, "editor_meta_box"), "lrschema");				
				}
			}else{
				global $post;
				if($post->post_type=="lrschema"){
					remove_action( 'media_buttons', 'media_buttons' );
					add_filter( 'user_can_richedit' , '__return_false', 50 );	
					add_meta_box( "lrschema", __("Schema Settings"), array($this, "editor_meta_box"), "lrschema");
				}
			}
		}
		
		function editor_meta_box(){
		
			global $post; 
			
			?>
			<p>Payload Schema</p>
			<input type="text" name="lrschema_payload" value="<?PHP echo get_post_meta($post->ID, "lrschema_payload", true); ?>" />
			<p>Default Schema <input type="checkbox" name="lrschema_default"  <?PHP if(get_option("lrschema_default")==$post->ID){ echo " checked "; } ?>  /></p>
			<?PHP
		
		}
		
		function save_schema($post_id){
			update_post_meta($post_id, "lrschema_payload", $_POST['lrschema_payload']);
			if($_POST['lrschema_default']=="on"){
				update_option("lrschema_default", $post_id);
			}
		
		}
		
		function trash_schema($post_id){
			if(get_option("lrschema_default")==$post_id){
				delete_option("lrschema_default");
			}
		}
	
	}
	
	$LearningRegistryPublisherSchemaEditor = new LearningRegistryPublisherSchemaEditor();