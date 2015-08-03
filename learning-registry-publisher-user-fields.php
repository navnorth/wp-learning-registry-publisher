<?php

class LearningRegistryPublisherUserProfile{
	
	public function __construct() {	
		
		add_action( 'edit_user_profile', array($this, 'UserFields'));
		add_action( 'show_user_profile', array($this, 'UserFields'));
		add_action( 'edit_user_profile_update', array($this, 'UpdateUserFields'));
		add_action( 'personal_options_update', array($this, 'UpdateUserFields'));
		add_action( 'user_register', array($this, 'UserAdded'), 10, 1 );
 
	}

	public function UserAdded( $user_id ) {

		$user = new WP_User( $user_id );
		
		$LearningRegistryPublisher = new LearningRegistryPublisher();
		
		if(in_array("administrator", $user->roles)){
			foreach ( $LearningRegistryPublisher->AdministratorCapabilities as $capability ){
				$user->add_cap( $capability );
			}
		}
		
		if(in_array("editor", $user->roles)){
			foreach ( $LearningRegistryPublisher->EditorCapabilities as $capability ){
				$user->add_cap( $capability );
			}
		}
		
		if(in_array("author", $user->roles)){
			foreach ( $LearningRegistryPublisher->AuthorCapabilities as $capability ){
				$user->add_cap( $capability );
			}
		}

	}

	public function UpdateUserFields($user_id) {
	
		if ( current_user_can('edit_user', $user_id) ){
			
			if ( current_user_can('LearningRegistryPublisherManage', $user_id) ){
			
				$user = new WP_User( $user_id );
				
				$LearningRegistryPublisher = new LearningRegistryPublisher();
		
				foreach ( $LearningRegistryPublisher->AdministratorCapabilities as $capability ){
					$user->remove_cap( $capability );
				}
		
				if(isset($_POST['LearningRegistryPublisherManage'])){ 
					$user->add_cap( 'LearningRegistryPublisherManage' );
					foreach ( $LearningRegistryPublisher->AdministratorCapabilities as $capability ){
						$user->add_cap( $capability );
					}
				}else{
					$user->remove_cap( 'LearningRegistryPublisherManage' );
				}
				
				if(isset($_POST['LearningRegistryPublisherSchema'])){ 
					$user->add_cap( 'LearningRegistryPublisherSchema' );
					foreach ( $LearningRegistryPublisher->SchemaCapabilities as $capability ){
						$user->add_cap( $capability );
					}
				}else{
					$user->remove_cap( 'LearningRegistryPublisherSchema' );
					foreach ( $LearningRegistryPublisher->SchemaCapabilities as $capability ){
						$user->remove_cap( $capability );
					}
				}
				
				if(isset($_POST['LearningRegistryPublisherKeys'])){ 
					$user->add_cap( 'LearningRegistryPublisherKeys' );
					foreach ( $LearningRegistryPublisher->KeyCapabilities as $capability ){
						$user->add_cap( $capability );
					}
				}else{
					$user->remove_cap( 'LearningRegistryPublisherKeys' );
					foreach ( $LearningRegistryPublisher->KeyCapabilities as $capability ){
						$user->remove_cap( $capability );
					}
				}
				
				if(isset($_POST['LearningRegistryPublisherHistory'])){ 
					$user->add_cap( 'LearningRegistryPublisherHistory' );
				}else{
					$user->remove_cap( 'LearningRegistryPublisherHistory' );
				}
				
				if(isset($_POST['LearningRegistryPublisherOverrideDefaults'])){ 
					$user->add_cap( 'LearningRegistryPublisherOverrideDefaults' );
				}else{
					$user->remove_cap( 'LearningRegistryPublisherOverrideDefaults' );
				}
				
				if(isset($_POST['LearningRegistryPublisherManageDocument'])){ 
					$user->add_cap( 'LearningRegistryPublisherManageDocument' );
				}else{
					$user->remove_cap( 'LearningRegistryPublisherManageDocument' );
				}
					
			}
			
		}
	
	}
	
	public function UserFields($user){
			
		?>
			<h3><?PHP _e("Manage Learning Registry Options", "learning-registry-publisher"); ?></h3>
			<p><?PHP _e("Use the options below to configure the Learning Registry Publisher Options for this user", "learning-registry-publisher"); ?></p>
			<table class="form-table">
			<tr>
				<th>
					<label><?php _e('Manage Learning Registry Options', "learning-registry-publisher"); ?></label>
				</th>
				<td>
					<input type="checkbox" name="LearningRegistryPublisherManage" <?PHP 
																	$user_data = new WP_User( $user->data->ID ); 
																	if($user_data->has_cap( 'LearningRegistryPublisherManage' )){
																		echo "checked ";
																	}
																	
																	if(!current_user_can('LearningRegistryPublisherManage')){
																		echo " disabled ";
																	}
																	
																?> />
					<br><span class="description"><?php _e('User can manage all Learning Registry Publisher Options', "learning-registry-publisher"); ?></span>
				</td>
			</tr>
			<tr>
				<th>
					<label><?php _e('Manage Learning Registry Schema', "learning-registry-publisher"); ?></label>
				</th>
				<td>
					<input type="checkbox" name="LearningRegistryPublisherSchema" <?PHP 
																	$user_data = new WP_User( $user->data->ID ); 
																	if($user_data->has_cap( 'LearningRegistryPublisherSchema' )){
																		echo " checked ";
																	}
																	
																	if(!current_user_can('LearningRegistryPublisherManage')){
																		echo " disabled ";
																	}
																	
																?> />
					<br><span class="description"><?php _e('User can manage Learning Registry Publisher Schema', "learning-registry-publisher"); ?></span>
				</td>
			</tr>
			<tr>
				<th>
					<label><?php _e('Manage Learning Registry PGP Keys', "learning-registry-publisher"); ?></label>
				</th>
				<td>
					<input type="checkbox" name="LearningRegistryPublisherKeys" <?PHP 
																	$user_data = new WP_User( $user->data->ID ); 
																	if($user_data->has_cap( 'LearningRegistryPublisherKeys' )){
																		echo "checked ";
																	}
																	
																	if(!current_user_can('LearningRegistryPublisherManage')){
																		echo " disabled ";
																	}
																	
																?> />
					<br><span class="description"><?php _e('User can manage Learning Registry Publisher PGP Keys', "learning-registry-publisher"); ?></span>
				</td>
			</tr>
			<tr>
				<th>
					<label><?php _e('Manage Learning Registry Document History', "learning-registry-publisher"); ?></label>
				</th>
				<td>
					<input type="checkbox" name="LearningRegistryPublisherHistory" <?PHP 
																	$user_data = new WP_User( $user->data->ID ); 
																	if($user_data->has_cap( 'LearningRegistryPublisherHistory' )){
																		echo "checked ";
																	}
																	
																	if(!current_user_can('LearningRegistryPublisherManage')){
																		echo " disabled ";
																	}
																	
																?> />
					<br><span class="description"><?php _e('User can see Learning Registry Publisher History', "learning-registry-publisher"); ?></span>
				</td>
			</tr>
			<tr>
				<th>
					<label><?php _e('Manage Learning Registry Override Defaults', "learning-registry-publisher"); ?></label>
				</th>
				<td>
					<input type="checkbox" name="LearningRegistryPublisherOverrideDefaults" <?PHP 
																	$user_data = new WP_User( $user->data->ID ); 
																	if($user_data->has_cap( 'LearningRegistryPublisherOverrideDefaults' )){
																		echo "checked ";
																	}
																	
																	if(!current_user_can('LearningRegistryPublisherManage')){
																		echo " disabled ";
																	}
																	
																?> />
					<br><span class="description"><?php _e('User can override defaults with the Learning Registry Publisher', "learning-registry-publisher"); ?></span>
				</td>
			</tr>
			<tr>
				<th>
					<label><?php _e('Manage Learning Registry Manage Documents', "learning-registry-publisher"); ?></label>
				</th>
				<td>
					<input type="checkbox" name="LearningRegistryPublisherManageDocument" <?PHP 
																	$user_data = new WP_User( $user->data->ID ); 
																	if($user_data->has_cap( 'LearningRegistryPublisherManageDocument' )){
																		echo "checked ";
																	}
																	
																	if(!current_user_can('LearningRegistryPublisherManage')){
																		echo " disabled ";
																	}
																	
																?> />
					<br><span class="description"><?php _e('User can manage Documents with the Learning Registry Publisher', "learning-registry-publisher"); ?></span>
				</td>
			</tr>
			</table>
		<?php
	
	}

} 

$LearningRegistryPublisherUserProfile = new LearningRegistryPublisherUserProfile();
