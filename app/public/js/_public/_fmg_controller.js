$(function() {
	/* ++ upload procedures */
	var _frmUpload, _progressCntr, _bar, _percent;
	if ($('#frm_upload_image').length <= 0) {
		_frmUpload = $('<form id="frm_upload_image" action="upload_temp_image" method="post" enctype="multipart/form-data"><input type="hidden" id="element_id" name="element_id"/></form>');

		_progressCntr = $('<div>').addClass('cls-upload-progress').css('display', 'none');
		_bar = $('<div>').addClass('cls-upload-bar')
		_percent = $('<div>').addClass('cls-upload-percent')
		_progressCntr.append(_bar).append(_percent);
		$('body').append(_frmUpload).append(_progressCntr);
	} else {
		_frmUpload = $($('#frm_upload_image')[0]);
		if ($('.cls-upload-progress').length > 0) {
			_progressCntr = $($('.cls-upload-progress')[0]);
			_bar = $('.cls-upload-bar');
			_percent = $('.cls-upload-percent');
		}
	}
	_frmUpload.ajaxForm({
		beforeSend: function() {
			var _disp = '0%';
			if (_bar) _bar.width(_disp)
			if (_percent) _percent.html(_disp);
		}
		, uploadProgress: function(event, position, total, percentComplete) {
			var _disp = percentComplete + '%';
			if (_bar) _bar.width(_disp)
			if (_percent) _percent.html(_disp);
		}
		, success: function() {
			var _disp = '100%';
			if (_bar) _bar.width(_disp)
			if (_percent) _percent.html(_disp);
		}
		, complete: function(xhr) {
			var _data = $.parseJSON(xhr.responseText);
			if ((_data) && (_data.files) && (_data.files.length > 0)) {
				_elemId = _data.files[0]['id'];
				_setElemValue($('#' + _elemId), _data.files[0]);
				_div = $('#' + _elemId).parents('div').get(0);
				$(_div).css('background-image', 'url(' + _data.files[0].url + ')'); //.attr('src', data.files[0].url); //thumbnailUrl
				_hdn = $(_div).find('input[type="hidden"]').get(0);
				$(_hdn).val(_data.files[0].name);
			} else {
				var _strErr = ((_data) && (_data.error)) ? _data.error : '';
				if (_strErr != '') alert(_strErr.replace(/\<\/?p\>/g, ''));
			}
			if (_bar) _bar.width('0px');
			if (_percent) _percent.html('0%');
			if (_progressCntr) _progressCntr.detach().insertBefore(_frmUpload).css('display', 'none');
		}
	});
	/* -- upload procedures */

	/* ++ stop propagation lower layer control events */
	$('div.display-upload').on('click', 'input[type="file"]', function (e) { e.stopImmediatePropagation(); });
	$('div.display-upload').on('click', 'fmg-controller', function (e) { e.stopImmediatePropagation(); });
	/* -- stop propagation lower layer control events */

	/* ++ remove image controller */
	$('div.display-upload').on('click', 'input[type=checkbox].fmg-no-image', function(e) {
		e.stopImmediatePropagation();
		var _prntCtrlSet = $($(this).parents('div.display-upload').get(0));
		if (_prntCtrlSet.length <= 0) return false;
		var _elemFmg = $($('.user-input, .data-container', _prntCtrlSet).get(0));
		var _elemHdn = $($('input[type="hidden"].fmg-value', _prntCtrlSet).get(0));
		if (_elemFmg.length <= 0) return false;
		if (getValue(this, '0') == 1) {
			if ((_prntCtrlSet.css('background-image') || 'none') == 'none') return false;
			_elemFmg.attr('disabled', 'disabled').addClass('clsCtrl-deactivate');
			_prntCtrlSet.addClass('clsCtrl-deactivate');
			var _bg = _prntCtrlSet.css('background-image') || 'none';
			if (_bg != 'none') {
				//check if value never change from another action, mark as it changed by control to return state if user reactivated
				_prntCtrlSet.css('background-image', '').attr('old_bg', _bg);
				if (! _prntCtrlSet.hasClass('clsCtrl-valueChanged')) {
					_prntCtrlSet.addClass('clsCtrl-valueChanged').attr('ctrl_changeValue', 'ctrl_changeValue');
					_elemFmg.addClass('clsCtrl-valueChanged');
				}
			}
		} else {
			_elemFmg.removeAttr('disabled').removeClass('clsCtrl-deactivate');
			_prntCtrlSet.removeClass('clsCtrl-deactivate');
			var _oldBg = _prntCtrlSet.attr('old_bg') || 'none';
			if (_oldBg != 'none') {
				_prntCtrlSet.css('background-image', _oldBg).removeAttr('old_bg');
				//check if value changed because of deactivate controller, return it value and remove change stated
				if (_prntCtrlSet.is('[ctrl_changeValue]')) {
					_prntCtrlSet.removeClass('clsCtrl-valueChanged').removeAttr('ctrl_changeValue');
					_elemFmg.removeClass('clsCtrl-valueChanged');
				}
			}
		}
	});
	/* -- remove image controller */
		
	$('*').on('click', 'div.display-upload', function (e) {
		e.preventDefault();
		e.stopImmediatePropagation();
		var _elem = $(this).find('input[type="file"]');
		if (_elem.length > 0) {
			if (_elem.prop('disabled')) return false;
			_elem.trigger('click');
		}
		return false;
	});
	$('div.display-upload').on('change', 'input[type="file"]', function (e) {
		e.preventDefault();
		var _elmImg = $(this);
		if (! _elmImg.val()) return false;
		if (_progressCntr) _progressCntr.detach().insertBefore(_elmImg).css('display', 'block');
		$("#element_id", _frmUpload).val(_elmImg.attr('id'));
		_tmp = _elmImg.clone();
		_tmp.insertBefore(_elmImg);
		_frmUpload.append(_elmImg.detach()).submit();
		_elmImg.detach()
		return false;
	});
});