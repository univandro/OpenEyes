<?php
class ProcedureControllerTest extends CDbTestCase
{
	public $fixtures = array(
		'procedures' => 'Procedure',
		'services' => 'Service',
		'subsections' => 'ServiceSubsection'
	);

	protected $controller;

	protected function setUp()
	{
		$this->controller = new ProcedureController('ProcedureController');
		parent::setUp();
	}
	
	public function testActionDetails_EmptySession_RendersNothing()
	{
		Yii::app()->session['Procedures'] = null;
		
		$mockController = $this->getMock('ProcedureController', array('renderPartial'),
			array('ProcedureController'));
		$mockController->expects($this->never())
			->method('renderPartial');
		$mockController->actionDetails();
	}
	
	public function testActionDetails_ValidSessionData_NonMatchingTerm_RendersNothing()
	{
		$session = array();
		foreach ($this->procedures as $procedure) {
			$session[$procedure['id']] = array(
				'term' => $procedure['term'],
				'short_format' => $procedure['short_format'],
				'duration' => $procedure['default_duration'],
			);
		}
		Yii::app()->session['Procedures'] = $session;
		
		$_GET['name'] = 'Bar Procedure - BZ1';
		
		$mockController = $this->getMock('ProcedureController', array('renderPartial'),
			array('ProcedureController'));
		$mockController->expects($this->never())
			->method('renderPartial');
		$mockController->actionDetails();
	}
	
	public function testActionDetails_NoSessionData_TermInDb_RendersAjaxPartial()
	{
		$session = array();
		Yii::app()->session['Procedures'] = $session;
		
		$procedure = $this->procedures['procedure1'];
		$_GET['name'] = "{$procedure['term']} - {$procedure['short_format']}";
		
		$data = array(
			'term' => $procedure['term'],
			'short_format' => $procedure['short_format'],
			'duration' => $procedure['default_duration'],
			'id' => $procedure['id'],
		);
		
		$mockController = $this->getMock('ProcedureController', array('renderPartial'),
			array('ProcedureController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('_ajaxProcedure', array('data' => $data), false, false);
		$mockController->actionDetails();
	}
	
	public function testActionDetails_ValidSessionData_MatchingTerm_RendersAjaxPartial()
	{
		$session = array();
		foreach ($this->procedures as $procedure) {
			$session[$procedure['id']] = array(
				'term' => $procedure['term'],
				'short_format' => $procedure['short_format'],
				'duration' => $procedure['default_duration'],
			);
		}
		Yii::app()->session['Procedures'] = $session;
		
		$procedure = $this->procedures['procedure1'];
		$_GET['name'] = "{$procedure['term']} - {$procedure['short_format']}";
		
		$data = array(
			'term' => $procedure['term'],
			'short_format' => $procedure['short_format'],
			'duration' => $procedure['default_duration'],
			'id' => $procedure['id'],
		);
		
		$mockController = $this->getMock('ProcedureController', array('renderPartial'),
			array('ProcedureController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('_ajaxProcedure', array('data' => $data), false, false);
		$mockController->actionDetails();
	}
	
	public function testActionSubsection_MissingService_RendersNothing()
	{
		$_GET = array();
		
		$mockController = $this->getMock('ProcedureController', array('renderPartial'),
			array('ProcedureController'));
		$mockController->expects($this->never())
			->method('renderPartial');
		$mockController->actionSubsection();
	}
	
	public function testActionSubsection_ValidService_RendersAjaxPartial()
	{
		$serviceId = $this->services['service1']['id'];
		$_GET['service'] = $serviceId;
		
		$subsections = ServiceSubsection::model()->findAllByAttributes(
			array('service_id' => $serviceId));
		
		$mockController = $this->getMock('ProcedureController', array('renderPartial'),
			array('ProcedureController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('_subsectionOptions', array('subsections' => $subsections), false, false);
		$mockController->actionSubsection();
	}
	
	public function testActionList_MissingSubsection_RendersNothing()
	{
		$_POST = array();
		
		$mockController = $this->getMock('ProcedureController', array('renderPartial'),
			array('ProcedureController'));
		$mockController->expects($this->never())
			->method('renderPartial');
		$mockController->actionList();
	}
	
	public function testActionList_ValidSubsection_NoExistingProcedures_RendersAjaxPartial()
	{
		$sectionId = $this->subsections['section1']['id'];
		$_POST['subsection'] = $sectionId;
		
		$criteria = new CDbCriteria;
		$criteria->select = 'id, term, short_format';
		$criteria->compare('service_subsection_id', $_POST['subsection']);
		
		$procedures = Procedure::model()->findAll($criteria);
		
		$mockController = $this->getMock('ProcedureController', array('renderPartial'),
			array('ProcedureController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('_procedureOptions', array('procedures' => $procedures), false, false);
		$mockController->actionList();
	}
	
	public function testActionList_ValidSubsection_WithExistingProcedures_RendersAjaxPartial()
	{
		$sectionId = $this->subsections['section1']['id'];
		$procedureName = "{$this->procedures['procedure1']['term']} - {$this->procedures['procedure1']['short_format']}";
		$_POST['subsection'] = $sectionId;
		$_POST['existing'] = array($procedureName);
		
		$criteria = new CDbCriteria;
		$criteria->select = 'id, term, short_format';
		$criteria->compare('service_subsection_id', $_POST['subsection']);
		$criteria->addNotInCondition("CONCAT_WS(' - ', term, short_format)", array($procedureName));
		
		$procedures = Procedure::model()->findAll($criteria);
		
		$mockController = $this->getMock('ProcedureController', array('renderPartial'),
			array('ProcedureController'));
		$mockController->expects($this->once())
			->method('renderPartial')
			->with('_procedureOptions', array('procedures' => $procedures), false, false);
		$mockController->actionList();
	}
}