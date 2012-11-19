<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	public function run()
	{
		parent::run();
	}
	protected function _initSession()
	{
		$session = new Zend_Session_Namespace('inssetAirlines',true);
		return $session;
	}
	protected function _initConfig()
	{
		Zend_Registry::set('config', new Zend_Config($this->getOptions()));                
	}
	protected function _initDb()
	{
            $db = Zend_Db::factory(Zend_Registry::get('config')->database);
           
            Zend_Db_Table_Abstract::setDefaultAdapter($db);
            Zend_Registry::set('db',$db);
	}
	
	/**
	 * Charge tous les modèles du projet automatiquement.
	 */
	protected function _initModel()
	{
		//On ajoute une ressource à l'autoloader pointant vers le dossier Application
		//On lui met pas de namespace (pour n'avoir qu'un seul préfixe)
		//Si on indique un namespace AA, il faudra faire AA_Table_xxx alors qu'on cherche à faire que Table_xxx
		$resourceLoader = new Zend_Loader_Autoloader_Resource(array(
			'basePath'  => APPLICATION_PATH,
			'namespace' => ''
		));
		
		//On ajoute le préfixe "Table_" à l'autoloader, 
		//et on lui dit que c'est une ressource de type model se trouvant dans le dossier "models/"
		$resourceLoader->addResourceTypes(array(
			'model' => array(
				'path'  => 'models/',
				'namespace' => 'Table'
			)
		));
	}
}
