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

$this->layout = 'main'; ?>
<script type="text/javascript" src="/js/phrase.js"></script>
<h2>Patient search</h2>
<div class="centralColumn">
	<p><strong>Find a patient.</strong> Either by hospital number or by personal details. You must know their surname.</p>
	<?php $this->renderPartial('//base/_messages'); ?>
	<?php if ($_SERVER['REQUEST_URI'] == '/patient/results/error') {?>
		<div id="patient-search-error" class="alertBox">
			Please enter either a valid hospital number or a firstname and lastname.
		</div>
	<?php }else if ($_SERVER['REQUEST_URI'] == '/patient/no-results') {?>
		<div id="patient-search-error" class="alertBox">
			Sorry, No patients found for that search.
		</div>
	<?php }else if ($_SERVER['REQUEST_URI'] == '/patient/no-results-pas') {?>
		<div id="pas-error" class="alertBox">
			Sorry, the PAS is down. Unable to search for patients.
		</div>
	<?php }else if ($_SERVER['REQUEST_URI'] == '/patient/no-results-address') {?>
		<div id="pas-address-error" class="alertBox">
			Sorry, the patient has no address defined in PAS and so cannot be loaded.
		</div>
	<?php }else{?>
		<div id="patient-search-error" class="alertBox" style="display: none;">
		</div>
	<?php }?>
	<?php
	$form=$this->beginWidget('CActiveForm', array(
		'id'=>'patient-search',
		'enableAjaxValidation'=>true,
		'focus'=>'#Patient_hos_num',
		'action' => Yii::app()->createUrl('patient/results')
	));?>
	<div id="search_patient_id" class="form_greyBox bigInput">
		<?php
			echo CHtml::label('Search by hospital number:', 'hospital_number');
			echo CHtml::textField('Patient[hos_num]', '', array('style'=>'width: 204px;'));
		?>
		<button type="submit" style="float: right; display: block;" class="classy blue tall" id="findPatient_id" tabindex="2"><span class="button-span button-span-blue">Find patient</span></button>
		<img class="loader" src="/img/ajax-loader.gif" alt="loading..." style="float: right; margin-right: 10px; margin-top: 9px; display: none;" />
		<?php //$this->endWidget();?>
	</div>
	<?php
	$this->renderPartial('/patient/_advanced_search');
	$this->endWidget();
	?>
</div><!-- .centralColumn -->
<div id="search-form" class="">
</div><!-- search-form -->
<div id="patient-list"></div>
<script type="text/javascript">
	$('#findPatient_id').click(function() {
		patient_search();
		return false;
	});

	$('#findPatient_details').click(function() {
		patient_search();
		return false;
	});

	function patient_search() {
		if (!$('#findPatient_id').hasClass('inactive')) {
			if (!$('#Patient_hos_num').val() && (!$('#Patient_last_name').val() || !$('#Patient_first_name').val())) {
				$('#patient-search-error').html('<h3>Please enter either a hospital number or a firstname and lastname.</h3>');
				$('#patient-search-error').show();
				$('#patient-list').hide();
				return false;
			}

			disableButtons();

			$('#patient-search').submit();
			return false;

			var postdata = $('#patient-search').serialize();

			$.ajax({
				'url': '<?php echo Yii::app()->createUrl('patient/results'); ?>',
				'type': 'POST',
				'data': postdata,
				'success': function(data) {
					try {
						arr = $.parseJSON(data);

						patientViewUrl = '<?php echo Yii::app()->createUrl('patient/view', array('id' => 'patientId')) ?>';

						if (!$.isEmptyObject(arr)) {
							if (arr.length == 1) {
								// One result, forward to the patient summary page
								patientViewUrl = patientViewUrl.replace(/patientId/, arr[0]['id']);

								window.location.replace(patientViewUrl);
							} else if (arr.length > 1) {
								// Multiple results, populate list
								if ($('#patient-adv-search div.ui-accordion-content:visible').length > 0) {
									// hide advanced search options, if visible
									$('#patient-adv-search').accordion('activate', 0);
								}

								content = '<br /><strong>' + arr.length + " results found</strong><p />\n";
								content += "<table><tr><th>Patient name</th><th>Date of Birth</th><th>Gender</th><th>NHS Number</th><th>Hospital Number</th></tr>\n";

								$.each(arr, function(index, value) {
									if (value['gender'] == 'M') {
										gender = 'Male';
									} else {
										gender = 'Female';
									}
									content += '<tr><td>' + value['first_name'] + ' ' + value['last_name'] + '</td><td>' + value['dob'] + '</td><td>' + gender;
									content += '</td><td>' + value['nhs_num'] + '</td><td>';
									content += '<a href="index.php?r=patient/view&id=' + value['id'];
									content += '">' + value['hos_num'] + "</a></td></tr>\n";
								});

								content += "</table>\n";

								$('#patient-list').html(content);

								$('#patient-search-error').hide();
								$('#patient-list').show();
							}
						} else {
							$('#patient-search-error').html('No patients found matching the selected options. Please choose different options and try again.');
							$('#patient-search-error').show();
							$('#patient-list').hide();
						}
					} catch (e) {
					}
				}
			});
		}

		return false;
	}
</script>
