<?php

/*
	Plugin Name: Learning Registry Publisher
	Description: A tool for publishing to the Learning Registry on WordPress
	Version: 0.1
*/

require_once("learning-registry-publisher-user-fields.php");
require_once("learning-registry-publisher-field.php");
require_once("learning-registry-publisher-field-editor.php");
require_once("learning-registry-publisher-node.php");
require_once("learning-registry-publisher-node-editor.php");
require_once("learning-registry-publisher-schema.php");
require_once("learning-registry-publisher-schema-editor.php");
require_once("learning-registry-publisher-key.php");
require_once("learning-registry-publisher-key-editor.php");
require_once("learning-registry-publisher-posts-management.php");
require_once("learning-registry-publisher-ajax.php");
require_once("learning-registry-publisher-submit.php");
require_once("learning-registry-publisher-management-page.php");
require_once("learning-registry-publisher-unsubmitted-management-page.php");

class LearningRegistryPublisher{

	var $AdministratorCapabilities = array(
								'LearningRegistryPublisherManage', 
								'LearningRegistryPublisherSchema', 
								'LearningRegistryPublisherKeys', 
								'LearningRegistryPublisherHistory',
								'LearningRegistryPublisherManageDocument',
								'edit_lrnode',
								'read_lrnode',
								'delete_lrnode',
								'edit_lrnodes',
								'edit_others_lrnodes',
								'publish_lrnodes',
								'read_private_lrnodes',
								'delete_lrnodes',
								'delete_private_lrnodes',
								'delete_published_lrnodes',
								'delete_others_lrnodes',
								'edit_private_lrnodes',
								'edit_published_lrnodes',
								'edit_lrnodes',
								'edit_lrkey',
								'read_lrkey',
								'delete_lrkey',
								'edit_lrkeys',
								'edit_others_lrkeys',
								'publish_lrkeys',
								'read_private_lrkeys',
								'delete_lrkeys',
								'delete_private_lrkeys',
								'delete_published_lrkeys',
								'delete_others_lrkeys',
								'edit_private_lrkeys',
								'edit_published_lrkeys',
								'edit_lrkeys',
								'edit_lrschema',
								'read_lrschema',
								'delete_lrschema',
								'edit_lrschemas',
								'edit_others_lrschemas',
								'publish_lrschemas',
								'read_private_lrschemas',
								'delete_lrschemas',
								'delete_private_lrschemas',
								'delete_published_lrschemas',
								'delete_others_lrschemas',
								'edit_private_lrschemas',
								'edit_published_lrschemas',
								'edit_lrschemas',
								'edit_lrfield',
								'read_lrfield',
								'delete_lrfield',
								'edit_lrfields',
								'edit_others_lrfields',
								'publish_lrfields',
								'read_private_lrfields',
								'delete_lrfields',
								'delete_private_lrfields',
								'delete_published_lrfields',
								'delete_others_lrfields',
								'edit_private_lrfields',
								'edit_published_lrfields',
								'edit_lrfields',
								'LearningRegistryPublisherOverrideDefaults'
							);			
	
	var $FieldCapabilities = array(
								'edit_lrfield',
								'read_lrfield',
								'delete_lrfield',
								'edit_lrfields',
								'edit_others_lrfields',
								'publish_lrfields',
								'read_private_lrfields',
								'delete_lrfields',
								'delete_private_lrfields',
								'delete_published_lrfields',
								'delete_others_lrfields',
								'edit_private_lrfields',
								'edit_published_lrfields',
								'edit_lrfields'
							);
						
	var $SchemaCapabilities = array(
								'edit_lrschema',
								'read_lrschema',
								'delete_lrschema',
								'edit_lrschemas',
								'edit_others_lrschemas',
								'publish_lrschemas',
								'read_private_lrschemas',
								'delete_lrschemas',
								'delete_private_lrschemas',
								'delete_published_lrschemas',
								'delete_others_lrschemas',
								'edit_private_lrschemas',
								'edit_published_lrschemas',
								'edit_lrschemas'
							);
							
	var $KeyCapabilities = array(
								'edit_lrkey',
								'read_lrkey',
								'delete_lrkey',
								'edit_lrkeys',
								'edit_others_lrkeys',
								'publish_lrkeys',
								'read_private_lrkeys',
								'delete_lrkeys',
								'delete_private_lrkeys',
								'delete_published_lrkeys',
								'delete_others_lrkeys',
								'edit_private_lrkeys',
								'edit_published_lrkeys',
								'edit_lrkeys'
							);
							
	var $AuthorCapabilities = array(
								'LearningRegistryPublisherHistory'
							);
							
	function __construct(){
		
		$this->EditorCapabilities = array_merge($this->AuthorCapabilities, $this->SchemaCapabilities, $this->FieldCapabilities); 
	
	}

	function activate(){
		
		global $wpdb;
		
		if(!get_option("lrp_database_establish")){

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

			$table_name = $wpdb->prefix . "lrp_documents_history";

			$sql = "CREATE TABLE " . $table_name . " (
				  id bigint(20) NOT NULL AUTO_INCREMENT,
				  post bigint(20),
				  lrnode bigint(20),
				  lrkey bigint(20),
				  lrschema bigint(20),
				  lruser bigint(20),
				  lraction varchar(100),
				  lrdocid varchar(100),
				  date_submitted bigint(20),
				  UNIQUE KEY id(id)
				);";
			
			dbDelta($sql);
				
			add_option("lrp_database_establish", 1);
			
		}
		
		$get_users = get_users();
		
		foreach ( $get_users as $user )
		{
			if(in_array("administrator", $user->roles)){
				$user = new WP_User( $user->data->ID );
				foreach ( $this->AdministratorCapabilities as $capability ){
					$user->add_cap( $capability );
				}
			}
			if(in_array("editor", $user->roles)){
				$user = new WP_User( $user->data->ID );
				foreach ( $this->EditorCapabilities as $capability ){
					$user->add_cap( $capability );
				}
			}
			if(in_array("author", $user->roles)){
				$user = new WP_User( $user->data->ID );
				foreach ( $this->AuthorCapabilities as $capability ){
					$user->add_cap( $capability );
				}
			}
		}

	}

}

$LearningRegistryPublisher = new LearningRegistryPublisher();

register_activation_hook( __FILE__, array($LearningRegistryPublisher,'activate'));