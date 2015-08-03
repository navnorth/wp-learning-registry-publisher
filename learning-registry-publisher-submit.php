<?PHP

	class LearningRegistryPublisherSubmit{

		function update_initialise($url, $username, $https, $password, $auth = NULL, $oauthSignature = NULL, $data){
		
			require dirname(__FILE__) . "/lr_library/vendor/autoload.php";
			require dirname(__FILE__) . "/lr_library/Psr4AutoloaderClass.php";

			$config = array();
			$config['url'] = $url;
			$config['username'] = $username;
			if($https=="on"){
				$config['https'] = 1;
			}
			$config['password'] = $password;
			if(!$auth){
				$config['auth'] = "basic";
			}else{
				$config['auth'] = "oauth";
				$config['oauthSignature'] = $oauthSignature;
			}
			
			$this->LRConfig = new LearningRegistry\LearningRegistryConfig($config);
			$this->LR = new LearningRegistry\LearningRegistryServices\LearningRegistryUpdate($this->LRConfig);
			$this->LRDocument = new LearningRegistry\LearningRegistryDocuments\LearningRegistryReplaceDocument($data);
			return $this;
		
		}

		function initialise($url, $username, $https, $password, $auth = NULL, $oauthSignature = NULL){
		
			require dirname(__FILE__) . "/lr_library/vendor/autoload.php";
			require dirname(__FILE__) . "/lr_library/Psr4AutoloaderClass.php";

			$config = array();
			$config['url'] = $url;
			$config['username'] = $username;
			if($https=="on"){
				$config['https'] = 1;
			}
			$config['password'] = $password;
			if(!$auth){
				$config['auth'] = "basic";
			}else{
				$config['auth'] = "oauth";
				$config['oauthSignature'] = $oauthSignature;
			}

			$this->LRConfig = new LearningRegistry\LearningRegistryConfig($config);
			$this->LR = new LearningRegistry\LearningRegistryServices\LearningRegistryPublish($this->LRConfig);
			$this->LRDocument = new LearningRegistry\LearningRegistryDocuments\LearningRegistryDCMetadata($this->LR);
			$this->LRDocument->create();
			
			return $this;
			
		}
	
	}
	