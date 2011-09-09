<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'lettertemplate-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'phrase'); ?>
		<?php echo $form->textField($model,'phrase',array('size'=>60,'maxlength'=>2047)); ?>
		<?php echo $form->error($model,'phrase'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'to'); ?>
		<?php echo $form->dropDownList($model,'to',$model->getContactTypeOptions(),array()); ?>
		<?php echo $form->error($model,'to'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'cc'); ?>
		<?php echo $form->dropDownList($model,'cc',$model->getContactTypeOptions(),array()); ?>
		<?php echo $form->error($model,'cc'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->