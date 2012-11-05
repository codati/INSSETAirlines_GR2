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
}
