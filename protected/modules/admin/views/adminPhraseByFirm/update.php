<?php
$this->breadcrumbs=array(
        'Phrase By Firm'=>array('index'),
        $model->section->name => array('firmIndex', 'section_id'=>$model->section->id),
        $model->firm->name => array('phraseIndex', 'firm_id'=>$model->firm->id, 'section_id'=>$model->section->id),
        $model->name->name=>array('view','id'=>$model->id),
        'Update',
);

$this->menu=array(
	array('label'=>'List PhraseByFirm', 'url'=>array('index')),
	array('label'=>'Create PhraseByFirm', 'url'=>array('create')),
	array('label'=>'View PhraseByFirm', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage PhraseByFirm', 'url'=>array('admin')),
);
?>

<h1>Update PhraseByFirm <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>