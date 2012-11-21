<?php
class PlanningController extends Zend_Controller_Action
{
	public function indexAction() {$this->_helper->actionStack('header','index','default',array());}

	public function planifierAction()
	{
		$this->_helper->actionStack('header','index','default',array());
		
		$tableVol = new Table_Vol();
		$ListeVol = $tableVol->get_LstVolNonPlanifier(4); //Les vols non planifiés des 4 dernières semaines
		
		if(count($ListeVol) > 0)
		{
			foreach($ListeVol as $key => $val)
			{
				$dateDepart = new Zend_Date($val['dateHeureDepartPrevueVol']);
				$ListeVol[$key]['dateHeureDepartPrevueVol'] = DateFormat_View($dateDepart);
				
				$dateArriver = new Zend_Date($val['dateHeureArriveePrevueVol']);
				$ListeVol[$key]['dateHeureArriveePrevueVol'] = DateFormat_View($dateArriver);
			}
		}
		
		//echo '<pre>';print_r($ListeVol);echo '</pre>';exit;
		$this->view->ListeVol = $ListeVol;
	}
}