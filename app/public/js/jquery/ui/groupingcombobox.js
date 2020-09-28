(function( $ ) {
	$.widget( "ui.groupingcombobox", {
		_create: function() {
			var self = this;
			this.element.hide();
			this.wrapper = $( "<span>" )
				.addClass( "ui-combobox" )
				.insertAfter( this.element );
			this._createAutocomplete();
			this._createShowAllButton();
			this._arrCurrObjItemsDef = [];
			this._currCollapseIDs = {};
		},
		_fnc_toggleGroup: function (uiGroup, blnForceVis) {
			var _elem = $(uiGroup);
			if (_elem.length <= 0) return false;
			var _grId = _elem.attr('id') || '';
			var _ul = $(_elem.parents('ul').get(0)) || false;
			var _self = this;
			var _blnForceVis = (typeof blnForceVis == 'boolean') ? blnForceVis : null;

			if ((_grId) && (_ul)) {
				var _bln = (typeof _blnForceVis == 'boolean') ? _blnForceVis : ((_grId in _self._currCollapseIDs) && (_self._currCollapseIDs[_grId] === true));
				if (_bln) {
					_self._currCollapseIDs[_grId] = false;
					_elem.removeClass('cls-groupState__collapse');
					$('li.cls-group' + _grId, _ul).each(function() {
						if ($(this).css('display') != '') {
							$(this).css('display', '');
							if ($('li[group="' + this.id + '"]', _ul).length > 0) $(this).trigger('evntGrHdr__visibilityChanged');
						}
					});
					//$('li.cls-group' + _grId, _ul).css('display', '');
				} else {
					_self._currCollapseIDs[_grId] = true;
					_elem.addClass('cls-groupState__collapse');
					$('li.cls-group' + _grId, _ul).each(function() {
						if ($(this).css('display') != 'none') {
							$(this).css('display', 'none');
							if ($('li[group="' + this.id + '"]', _ul).length > 0) $(this).trigger('evntGrHdr__visibilityChanged');
						}
					});
					//$('li.cls-group' + _grId, _ul).css('display', 'none');
				}
			}
			return false;
		},
		_createAutocomplete: function() {
			var selected = this.element.children( ":selected" ),
				value = selected.val() ? selected.text() : "";
			var _self = this;
			this.input = $( "<input>" )
				.appendTo( this.wrapper )
				.val( value )
				.attr( "title", "" )
				.addClass( "ui-combobox-input ui-widget ui-widget-content ui-corner-left" ) //ui-state-default 
				.autocomplete({
					delay: 0,
					minLength: 0,
					source: $.proxy( this, "_source" ),
					open: function(event) {
						$(this).autocomplete('widget').css('z-index', 5000);
						/*$('li > ul', $(this).autocomplete('widget')).each(function() {
							$(this).css('top', '0.5em').css('left', '1em');
						});*/
						var _prntGrCmb = $(this).data('ui-autocomplete')._parentGroupingCombo || false;
						if (typeof _prntGrCmb.setExpandGroup == 'function') _prntGrCmb.setExpandGroup.apply(_prntGrCmb);
						event.preventDefault();
						return false;
					}
				})
				.tooltip({
					tooltipClass: "ui-state-highlight",
					position: { my: "left+15 center", at: "right center" }
				});
			this.input.data('ui-autocomplete')._parentGroupingCombo = _self;
/*
			this.input.data('ui-autocomplete')._renderMenu = function( ul, items ) {
				var _atc = this;
				$.each( items, function( index, item ) {
					_atc._renderItemData( ul, item );
				});
				var _selGr = ($(items).filter(function(e) { return this.option.selected == true;}).attr('group') || '');
				if (_selGr.length > 0) _self.setExpandGroup(_selGr);
			};
*/
			this.input.data('ui-autocomplete')._renderItem = function (ul, item) {
				var _ul = ul;
				var _option = item.option || {};
				var _li = $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append( "<a>" + item.label + "</a>" );
				if (item.class) _li.addClass(item.class);

				var _grName = '';
				if (item.group) _grName = (item.group || '');
				if (_grName.trim().length > 0) {
					var _arrGr;
					if (_grName.indexOf(':') > 0) {
						_arrGr = _grName.split(':');
					} else {
						_arrGr = [_grName];
					}
					var _strGroupConcatID = '';
					for (var _i=0;_i<_arrGr.length;_i++) {
						var _gr = _arrGr[_i] || '';
						if (_gr.trim().length > 0) {
							_grName = _gr.trim();
							_id = _grName.toLowerCase().replace(/[^a-zA-Z0-9]/gi, '_');
							_newGrID = _strGroupConcatID + ('--' + _id);
							// add new li group header if not exists yet
							if ($('#' + _newGrID, ul).length == 0) {
								var _liGr = $('<li id="' + _newGrID + '" class="cls-combobox-group cls-group-title">')
									.attr('aria-label', ' ')
									.on('click', function () {
										_self._fnc_toggleGroup(this);
										return false;
									})
									.append('<a>' + _grName + '</a>')
								;
								if (! (_newGrID in _self._currCollapseIDs)) _self._currCollapseIDs[_newGrID] = (_self.options.is_default_collapse || false);
								if (_self._currCollapseIDs[_newGrID]) _liGr.addClass('cls-groupState__collapse');

								if (_strGroupConcatID.trim().length > 0) {
									_liGr.attr("group", _strGroupConcatID).addClass('cls-group' + _strGroupConcatID);
									if (_self._currCollapseIDs[_strGroupConcatID]) _liGr.css('display', 'none');
								}
/*
									var _nestedGroupParent = $('#' + _strGroupConcatID, ul);
									if (_nestedGroupParent.length > 0) {
										if ($('ul.' + _strGroupConcatID, _nestedGroupParent).length > 0) {
											_liGr.appendTo($('ul.' + _strGroupConcatID, _nestedGroupParent));
										} else {
											_liGr.appendTo($('<ul>')
													.addClass(_strGroupConcatID)
													.appendTo(_nestedGroupParent)
												);
										}
									}
								} else {
									_liGr.appendTo(ul);
*/
								_liGr.on('evntGrHdr__visibilityChanged', function() {
										if ($(this).is('.cls-combobox-group')) {
											var _isCurrVis = ((_self._currCollapseIDs) && (this.id in _self._currCollapseIDs) && (_self._currCollapseIDs[this.id] != false));
											var _strDtlDisp = (_isCurrVis && (! $(this).is('.cls-groupState__collapse'))) ? '' : 'none';
											$('li[group="' + this.id + '"]', _ul).each(function() {
												if ($(this).css('display') != _strDtlDisp) {
													$(this).css('display', _strDtlDisp);
													if (($('li[group="' + this.id + '"]', _ul).length > 0)) $(this).trigger('evntGrHdr__visibilityChanged');
												}
											});
										}
									})
									.appendTo(ul);
							}
							_strGroupConcatID = _newGrID;
						}
					}
					_strGroupConcatID = _strGroupConcatID.trim();
					if (_strGroupConcatID.length > 0) {
						_li.attr("group", _strGroupConcatID).addClass('cls-combobox-group cls-group-child cls-group' + _strGroupConcatID);
						if (_self._currCollapseIDs[_strGroupConcatID]) _li.css('display', 'none');
/*						
						var _nestedGroupParent = $('#' + _strGroupConcatID, ul);
						if (_nestedGroupParent.length > 0) {
							var _nestedParentUl = $('ul', _nestedGroupParent);
							if (_nestedParentUl.length > 0) {
								_li.appendTo(_nestedParentUl);
							} else {
								_li.appendTo($('<ul>').appendTo(_nestedGroupParent));
							}
						}
					} else {
						_li.appendTo(ul);
*/
					}
				}
				if (_option.selected) _li.addClass('cls-selected');
				_li.appendTo(ul);
				return _li;
			};
			/* ++ Inferno 20160401: add placeholder */
			if ($(this.element).attr('placeholder')) this.input.attr('placeholder', $(this.element).attr('placeholder'));
			/* -- Inferno 20160401: add placeholder */				
			this._on( this.input, {
				autocompleteselect: function( event, ui ) {
					if (typeof this.options.blnBeforeChange == 'function') {
						if (! this.options.blnBeforeChange(value, event, ui)) return false;
					}
					if (! ((typeof ui == 'object') && (typeof ui.item == 'object') && ('option' in ui.item)) ) return true;
					var _uiObj = {item: ui.item.option};
					ui.item.option.selected = true;
					this._trigger( "select", event, _uiObj);
					/* ++ Buff : Add for allow add new to ui.combobox */
					if (this.options.is_allow_add) {
						this._displayNew();
					}
					/* -- Buff : Add for allow add new to ui.combobox */
					if (typeof this.options.changed == 'function') {
						this.options.changed( ui.item.value, jQuery.Event( "combobox.change" ), _uiObj );
					}
				}
				,autocompletechange: "_checkSelectValue"
			});
		},
		_createShowAllButton: function() {
			var wasOpen = false;
			var input = this.input;
			this.btnButton = $( "<a>" )
				.attr( "tabIndex", -1 )
				.attr( "title", "Show All Items" )
				.tooltip()
				.appendTo( this.wrapper )
				.button({
					icons: {
						primary: "ui-icon-triangle-1-s"
					},
					text: false
				})
				.removeClass( "ui-corner-all" )
				.addClass( "ui-corner-right ui-combobox-toggle" )
				.mousedown(function(event) {
					wasOpen = input.autocomplete( "widget" ).is( ":visible" );
					return false;
				})
				.click(function(event) {
					if ( wasOpen ) {
						return false;
					}
					// Pass empty string as value to search for, displaying all results
					input.autocomplete( "search", "" );
					input.focus();
					return false;
				});
		},
		_source: function( request, response ) {
			var _term = (request.term)?$.ui.autocomplete.escapeRegex(request.term):'';
			var _matcher;
			var _blnHasTerm = false;
			if ((typeof _term == 'string') && (_term.trim().length > 0)) {
				_blnHasTerm = true;
				_matcher = new RegExp(_term, "i");
			}
			var _self = this;
			response(this.element.children( "option" ).map(function() {
				var _text = $( this ).text();
				var _new = $( this ).attr('new') || false;
				var _class = $( this ).attr('class') || '';
				var _group = $( this ).attr('group') || '';
				var _label = _text;
				var _blnIsValid = true;

				if (_blnHasTerm) {
					_label = _label.replace(_matcher, "<strong>" + request.term + "</strong>");
					_blnIsValid = _matcher.test(_text);
					if ((! _blnIsValid) && (_self.options.arr_add_matching) && (_self.options.arr_add_matching.length > 0)) {	
						for (var _i=0;_i < _self.options.arr_add_matching.length;_i++) {
							var _ea = _self.options.arr_add_matching[_i];
							var _str = '';
							switch (_ea.type) {
								case 'attr': 
									_str = $( this ).attr(_ea.data) || '';
									break;
							}
							if (_matcher.test(_str)) {
								_blnIsValid = true;
								break;
							}
						}
					}
				}
				var _objReturn = { label: _label, value: _text, is_new: _new, option: this };
				if (_class.trim() != '') _objReturn.class = _class;
				if (_group.trim() != '') _objReturn.group = _group;
				
				return _objReturn;
			}) );
		},
		_checkSelectValue: function( event, ui ) {
			var thisInput = this.input;
			var self = this;
			// Selected an item, nothing to do
			if ( ui.item ) {
				event.preventDefault();
				return false;
			}
			// Search for a match (case-insensitive)
			var value = this.input.val(),
				valueLowerCase = value.toLowerCase(),
				valid = false,
				_blnInterupt = false;
			if (valueLowerCase.trim().length == 0) {
				this.clearValue();
				return false;
			}
			this.element.children( "option" ).each(function() {
				if ($(this).text().toLowerCase() === valueLowerCase ) {
					event.preventDefault();
					this.selected = valid = true;
					if (typeof self.options.blnBeforeChange == 'function') {
						if (! self.options.blnBeforeChange(value, jQuery.Event( "combobox.beforechange" ), {item: this})) {
							_blnInterupt = true;
							return false;
						}
					}
					thisInput.val($(this).text());
					self._trigger( "select", event, { item: this });
					/* ++Buff : Add for allow add new to ui.combobox */
					// Case already added and select it again
					if (self.options.is_allow_add) {
						self._displayNew();
					}
					/* --Buff : Add for allow add new to ui.combobox */
					if (self.options.changed) {
						if (typeof self.options.changed == 'function') {
							self.options.changed(value, jQuery.Event( "combobox.change" ), { item: this });
						}
					}
					return false; //break out from loop each function
				}
			});
			if (_blnInterupt) return false;
			// Found a match, nothing to do
			if ( ! valid ) {
				/* ++Buff : Add for allow add new to ui.combobox */
				if (typeof this.options.blnBeforeChange == 'function') {
					if (! this.options.blnBeforeChange(value, jQuery.Event( "combobox.beforechange" ), ui)) return false;
				}
				if (this.options.is_allow_add) {
					var newOption = $('<option></option>').val(value).html(value).attr('new', true);
					newOption.selected = true;
					this.element.append(newOption);
					this._addNew();
					if (self.options.changed) {
						if (typeof self.options.changed == 'function') {
							self.options.changed(value, jQuery.Event( "combobox.change" ), { item: newOption });
						}
					}	
				} else {
					// Remove invalid value
					this.input
						.val( "" )
						.attr( "title", '"' + value + '"' + " didn't match any item" )
						.tooltip( "open" );
					this.element.val( "" );
					this._delay(function() {
						this.input.tooltip( "close" ).attr( "title", "" );
					}, 2500 );
					this.input.data( "ui-autocomplete" ).term = "";
				}
				/* --Buff : Add for allow add new to ui.combobox */
			}				
			event.preventDefault();
			return false;
		},
		/* ++Buff : Add for allow add new to ui.combobox */
		_displayNew: function() {
			this._clearNew();
			if (this.element.children( ":selected" ).attr('new')) {
				this._addNew();
			}
		},
		_clearNew: function() {
			this.input
				.removeClass('ui-combobox-newitem')
				.tooltip( "close" )
				.attr( "title", "")
			;
		},
		_addNew: function() {
			this.input
				.addClass('ui-combobox-newitem')
				.attr( "title", "Add new")
				.tooltip( "open" )
			;
		},
		/* --Buff : Add for allow add new to ui.combobox */
		_destroy: function() {
			this.wrapper.remove();
			this.element.show();
			return false;
		},
		clearValue: function() {
			var _formerTextValue = (this.input.val() || this.element.val() || '').trim();
			this.input.val("");
			this.element.val("");

			var _uiObj = {item: this.input};
			this._trigger('select', jQuery.Event("select"), _uiObj);
			if ((_formerTextValue != "") && (typeof this.options.changed == 'function')) {
				this.options.changed.apply(this, ["", jQuery.Event( "combobox.change" ), _uiObj]);
			}				
			return false;
		},
		setCollapseAll: function() {
			var _self = this;
			$('li.cls-group-title', this.input.autocomplete('widget')).each(function() {
				_self._fnc_toggleGroup(this, false);
			});
		},
		setExpandGroup: function(grName) {
			var _self = this;
			var _ul = this.input.autocomplete('widget');
			var _grId = (grName || '').trim();
			if (_grId.length == 0) {
				var _selected = _self.element.children(':selected') || false;
				if (_selected.length > 0) _grId = (_selected.attr('group') || '').trim();
			}
			var _arrGr;
			if (_grId.indexOf(':') > 0) {
				_arrGr = _grId.split(':');
			} else {
				_arrGr = [_grId];
			}
			var _eaId = '';
			for (var _i=0;_i<_arrGr.length;_i++) {
				_eaId += '--' + _arrGr[_i].trim().toLowerCase();
				_self._fnc_toggleGroup($('li#' + _eaId, _ul), true);
			}
		},
		setValue: function(value, fn_Callback) {
			var thisInput = this.input;
			var _self = this;
			var _formerTextValue = $(thisInput).val();
			this.element.children( "option" ).each(function() {
				if ($(this).val() == value) {
					this.selected = true;
					var _strText = $(this).text();
					$(thisInput).val(_strText);
					var _uiObj = {item: this, fnCallback: fn_Callback};
					_self._trigger('select', jQuery.Event("select"), _uiObj);
					if ((_formerTextValue != _strText) && (typeof _self.options.changed == 'function')) {
						_self.options.changed.apply(this, [_strText, jQuery.Event( "combobox.change" ), _uiObj]);
					}
					_self.options.is_default_collapse = true;
					if (typeof _self.setCollapseAll == 'function') _self.setCollapseAll.apply(_self);
					if (typeof _self.setExpandGroup == 'function') _self.setExpandGroup.apply(_self);
					return false;
				}
			});
		},
		setText: function(text, fn_Callback) {
			var _text = (typeof text == 'string')?text.trim():'';
			var thisInput = this.input;
			var _self = this;
			var _formerTextValue = $(thisInput).text();
			this.element.children( "option" ).each(function() {
				if ($(this).text() == _text) {
					this.selected = true;
					$(thisInput).val(_text);
					var _uiObj = {item: this, fnCallback: fn_Callback};
					_self._trigger('select', jQuery.Event("select"), _uiObj);
					if ((_formerTextValue != _text) && (typeof _self.options.changed == 'function')) {
						_self.options.changed.apply(this, [_text, jQuery.Event( "combobox.change" ), _uiObj]);
					}
					_self.options.is_default_collapse = true;
					if (typeof _self.setCollapseAll == 'function') _self.setCollapseAll.apply(_self);
					if (typeof _self.setExpandGroup == 'function') _self.setExpandGroup.apply(_self);
					return false;
				}
			});
		},
		setSelectOption: function(obj, fn_Callback) {
			var thisInput = this.input;
			var _self = this;
			var _formerTextValue = $(thisInput).val();
			this.element.children( "option" ).each(function() {
				if (this == obj) {
					this.selected = true;
					var _strText = $(this).text();
					$(thisInput).val(_strText);
					var _uiObj = {item: this, fnCallback: fn_Callback};
					_self._trigger('select', jQuery.Event("select"), _uiObj);
					if ((_formerTextValue != _strText) && (typeof _self.options.changed == 'function')) {
						_self.options.changed.apply(this, [_strText, jQuery.Event( "combobox.change" ), _uiObj]);
					}
					_self.options.is_default_collapse = true;
					if (typeof _self.setCollapseAll == 'function') _self.setCollapseAll.apply(_self);
					if (typeof _self.setExpandGroup == 'function') _self.setExpandGroup.apply(_self);
					return false;
				}
			});
		},
		setFocus: function() {
			this.input.focus();
			return false;
		},
		enable: function(is_enable) {
			this.input.attr('readonly', !is_enable);
			if (is_enable) {
				this.element.removeClass('ui-combobox-input-disabled');
				this.input.removeClass('ui-combobox-input-disabled');
				this.btnButton.css('display', '');
			} else {
				this.element.addClass('ui-combobox-input-disabled');
				this.input.addClass('ui-combobox-input-disabled');
				this.btnButton.css('display', 'none');
			}
			return false;
		},
		setChanged: function (fnc) {
			if (typeof fnc == 'function') this.options.changed = fnc;
		},
		addMatching: function (params, type) {
			var _type = type || 'attr';
			_type = _type.toLowerCase();
			if (typeof params == 'string') {
				if (! this.options.arr_add_matching) this.options.arr_add_matching = [];
				this.options.arr_add_matching.push({"type": _type, "data": params});
			} else if (Object.prototype.toString.call(params) === '[object Array]') {
				for (var _i=0;_i<params.length;_i++) {
					_ea = params[_i];
					this.options.arr_add_matching.push({"type": _type, "data": _ea});
				}
			}
		}
	});
	
	$.extend($.ui.combobox, {
		is_allow_add: false,
		is_default_collapse: false,
		arr_add_matching: [],
		changed: function(value, event, ui) {
			return false;
		},
		blnBeforeChange: function (value, event, ui) {
			return true;
		}
	});
})( jQuery );
