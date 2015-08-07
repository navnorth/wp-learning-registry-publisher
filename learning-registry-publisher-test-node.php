<?PHP

	class LearningRegistryPublisherTestNode{

		function test_node($url){
		
			require dirname(__FILE__) . "/lr_library/vendor/autoload.php";
			require dirname(__FILE__) . "/lr_library/Psr4AutoloaderClass.php";

			$config = array();
			$config['url'] = $url;
			$config['https'] = 1;
			$config['auth'] = "basic";
			
			$this->LRConfig = new LearningRegistry\LearningRegistryConfig($config);
			$this->LR = new LearningRegistry\LearningRegistryServices\LearningRegistryServices($this->LRConfig);
			return $this;
		
		}

	}
	