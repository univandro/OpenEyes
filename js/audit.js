
function AuditLog() {if (this.init) this.init.apply(this, arguments); }

AuditLog.prototype = {
	init : function() {
		this.refresh_rate = 1000;
		this.data = $('#auditListData');
		this.run = true;

		setTimeout('auditLog.refresh();',this.refresh_rate);
	},
	refresh : function() {
		if (!this.run) {
			this.running = false;
			return;
		}

		this.running = true;

		var last_id = $('#auditListData').children('li').attr('id').match(/[0-9]+/);

		$.ajax({
			'type': 'GET',
			'url': '/audit/updateList?last_id='+last_id+'&site_id='+$('#previous_site_id').val()+'&firm_id='+$('#previous_firm_id').val()+'&user_id='+$('#previous_user_id').val()+'&action='+$('#previous_action').val()+'&target_type='+$('#previous_target_type').val()+'&date_from='+$('#previous_date_from').val()+'&date_to='+$('#previous_date_to').val()+'&hos_num='+$('#previous_hos_num').val(),
			'success': function(html) {
				if ($.trim(html).length >0) {
					auditLog.data.html(html + auditLog.data.html());

					auditLog.lines = [];

					auditLog.data.children('li').map(function() {
						if (!$(this).attr('class').match(/auditextra/) && $(this).is(':hidden')) {
							auditLog.lines.push($(this));
						}
					});

					auditLog.showLines();
				} else {
					setTimeout('auditLog.refresh();',auditLog.refresh_rate);
				}
			}
		});
	},
	showLines : function() {
		if (this.lines.length == 0) {
			setTimeout('auditLog.refresh();',this.refresh_rate);
		} else {
			var line = this.lines.pop();

			var even = $('#auditListData').children('li:visible').attr('class').match(/Even/);

			if (even) {
				line.attr('class',line.attr('class').replace(/Even/,'Odd'));
			} else {
				line.attr('class',line.attr('class').replace(/Odd/,'Even'));
			}

			line.slideToggle('fast',function() {
				var last_extra = auditLog.data.children('li').last();
				if (!last_extra.is(':hidden')) {
					last_extra.slideToggle('fast',function() {
						$(this).remove();
						auditLog.data.children('li').last().slideToggle('fast',function() {
							$(this).remove();
							auditLog.showLines();
						});
					});
				} else {
					last_extra.remove();
					auditLog.data.children('li').last().slideToggle('fast',function() {
						$(this).remove();
						auditLog.showLines();
					});
				}
			});
		}
	},
	loadItems : function() {
		if (this.running) {
			setTimeout('auditLog.loadItems()',50);
			return;
		}

		$.ajax({
			'url': '/audit/search',
			'type': 'POST',
			'data': $('#auditList-filter').serialize(),
			'success': function(data) {
				var s = data.split('<!-------------------------->');

				$('.loader').hide();
				$('#searchResults').html(s[0]);
				$('div.pagination').html(s[1]).show();

				return false;
			}
		});
	}
}

$(document).ready(function() {
	$('a[id^="detail"]').die('click').live('click',function() {
		var id = $(this).attr('id').match(/[0-9]+/);
		if ($('tr.auditextra'+id).is(':hidden')) {
			$('tr.auditextra'+id).show();
		} else {
			$('tr.auditextra'+id).hide();
		}
		return false;
	});

	$('a.auditItem').die('click').live('click',function() {
		var id = $(this).attr('id').match(/[0-9]+/);
		$('li.auditextra'+id).slideToggle('fast');
		return false;
	});

	$('a.showData').die('click').live('click',function() {
		var id = $(this).attr('id').match(/[0-9]+/);
		var data = $(this).next('input').val();
		$('#dataspan'+id).html(data);
		return false;
	});

	$('a.changePage').die('click').live('click',function() {
		$('#page').val($(this).attr('id').match(/[0-9]+/));

		$('.loader').show();

		auditLog.run = false;
		setTimeout('auditLog.loadItems()',50);

		return false;
	});
});

if (window.auditLog == undefined) {
	var auditLog = new AuditLog;
}
