<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

$this->breadcrumbs=array(
	'Letter Templates'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Letter Template', 'url'=>array('index')),
	array('label'=>'Create Letter Template', 'url'=>array('create')),
	array('label'=>'Update Letter Template', 'url'=>array('update', 'id'=>$model->id)),
);
?>

<h1>View Letter Template #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'phrase',
                array(
                        'name' => 'subspecialty_id',
                        'value' => CHtml::encode($model->getSubspecialtyText())
                ),
                array(
                        'name' => 'to',
                        'value' => CHtml::encode($model->getToText())
                ),
                array(
                        'name' => 'cc',
                        'value' => CHtml::encode($model->getCcText())
                ),
	),
)); ?>
