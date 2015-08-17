<?PHP

	class LearningRegistryPublisherSubmit{

		function update_initialise($url, $username, $https, $password, $auth = NULL, $oauthSignature = NULL, $data){
		
			require dirname(__FILE__) . "/LRphpLib/vendor/autoload.php";
			require dirname(__FILE__) . "/LRphpLib/Psr4AutoloaderClass.php";
	
			$this->loader = $loader;

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
			$this->LRConfig->setLoader($this->loader);
	
			$this->LR = new LearningRegistry\LearningRegistryServices\LearningRegistryUpdate($this->LRConfig);
			$this->LRDocument = new LearningRegistry\LearningRegistryDocuments\LearningRegistryReplaceDocument(array($url, $_REQUEST['lrdocid']));
			$this->LRDocument->emptyDocument($this->LR);
			return $this;
		
		}

		function initialise($url, $username, $https, $password, $auth = NULL, $oauthSignature = NULL){
		
			require dirname(__FILE__) . "/LRphpLib/vendor/autoload.php";
			require dirname(__FILE__) . "/LRphpLib/Psr4AutoloaderClass.php";
			
			$this->loader = $loader;

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
			
			$this->LRConfig->setLoader($this->loader);
			
			$this->LR = new LearningRegistry\LearningRegistryServices\LearningRegistryPublish($this->LRConfig);
			$this->LRDocument = new LearningRegistry\LearningRegistryDocuments\LearningRegistryDCMetadata($this->LR);
			$this->LRDocument->create();
			
			return $this;
			
		}
	
	}
	