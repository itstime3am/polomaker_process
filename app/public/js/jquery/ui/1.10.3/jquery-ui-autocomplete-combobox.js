	(function( $ ) {
		$.widget( "ui.combobox", {
			_create: function() {
				var self = this;
				this.element.hide();
				this.wrapper = $( "<span>" )
					.addClass( "ui-combobox" )
					.insertAfter( this.element );

				this._createAutocomplete();
				this._createShowAllButton();
			},
			_createAutocomplete: function() {
				var selected = this.element.children( ":selected" ),
					value = selected.val() ? selected.text() : "";

				// don't really need this, but in case I did, I could store it and chain
				//var oldFn = $.ui.autocomplete.prototype._renderItem;

				$.ui.autocomplete.prototype._renderItem = function( ul, item) {
					var re = new RegExp("^" + $.ui.autocomplete.escapeRegex(this.term)) ;
					var t = item.label.replace(re, "<strong>" + //"<span style='font-weight:bold;color:blue;'>" + 
						this.term + 
						"</strong>");//"</span>");
					var li = $( "<li></li>" )
						.data( "item.autocomplete", item )
						.append( "<a>" + t + "</a>" );
					if (item.is_new) {
						li.addClass('combobox-newitem');
					}
					li.appendTo( ul );
					return li;
				};
				
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
							event.preventDefault();
							return false;
						}
					})
					.tooltip({
						tooltipClass: "ui-state-highlight",
						position: { my: "left+15 center", at: "right center" }
					});
				this._on( this.input, {
					autocompleteselect: function( event, ui ) {
						if (typeof this.options.blnBeforeChange == 'function') {
							if (! this.options.blnBeforeChange(value, event, ui)) return false;
						}
						ui.item.option.selected = true;
						this._trigger( "select", event, {
							item: ui.item.option
						});
						/* ++ Buff : Add for allow add new to ui.combobox */
						if (this.options.is_allow_add) {
							this._displayNew();
						}
						/* -- Buff : Add for allow add new to ui.combobox */
						if (typeof this.options.changed == 'function') {
							this.options.changed( ui.item.value, event, ui );
						}
					},
					autocompletechange: "_checkSelectValue"
					/*Buff Add case use with alert escepe page to check again after normal autocomplete event */
					/*change: function( event, ui) {
						this._checkSelectValue( event, this.element );
					}
					,focus: function ( event ) {
						event.stopPropagation();
						event.preventDefault();
						event.relatedTarget = this.input;
						return false;
					},
					,blur: function ( event ) {
						event.stopPropagation();
						event.preventDefault();
						event.relatedTarget = this.input;
						return false;
					}*/
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
				var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
				response( this.element.children( "option" ).map(function() {
					var text = $( this ).text();
					var isNew = $( this ).attr('new');
					if ( (! $(this).attr('disabled')) && this.value && ( !request.term || matcher.test(text) ) )
						return {
							label: text.replace(
								new RegExp(
									"(?![^&;]+;)(?!<[^<>]*)(" +
									$.ui.autocomplete.escapeRegex(request.term) +
									")(?![^<>]*>)(?![^&;]+;)", "gi"
								), "<strong>$1</strong>" ),
							value: text,
							is_new: isNew,
							option: this
						};
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
				this.element.children( "option" ).each(function() {
					if ( $( this ).text().toLowerCase() === valueLowerCase ) {
						event.preventDefault();
						this.selected = valid = true;
						if (typeof self.options.blnBeforeChange == 'function') {
							if (! self.options.blnBeforeChange(value, event, ui)) {
								_blnInterupt = true;
								return false;
							}
						}
						thisInput.val($( this ).text());
						self._trigger( "select", event, {
							item: this
						});
						/* ++Buff : Add for allow add new to ui.combobox */
						// Case already added and select it again
						if (self.options.is_allow_add) {
							self._displayNew();
						}
						/* --Buff : Add for allow add new to ui.combobox */
						return false; //break out from loop each function
					}
				});
				if (_blnInterupt) return false;
				// Found a match, nothing to do
				if ( ! valid ) {
					/* ++Buff : Add for allow add new to ui.combobox */
					if (typeof this.options.blnBeforeChange == 'function') {
						if (! this.options.blnBeforeChange(value, event, ui)) return false;
					}
					if (this.options.is_allow_add) {
						var newOption = $('<option></option>').val(value).html(value).attr('new', true).attr('selected', 'selected');
						this.element.append(newOption);
						this._addNew();
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
				
				if (self.options.changed) {
					if (typeof self.options.changed == 'function') {
						self.options.changed( value, event, ui );
					}
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
					.attr( "title", "")
					//.tooltip( "close" )
				;
			},
			_addNew: function() {
				this.input
					.addClass('ui-combobox-newitem')
					.attr( "title", "Add new")
					//.tooltip( "open" )
				;
			},
			/* --Buff : Add for allow add new to ui.combobox */
			_destroy: function() {
				this.wrapper.remove();
				this.element.show();
				return false;
			},
			clearNewOption: function() {
				this._clearNew();
				$('option[new]', this.element).each(function() {
					$(this).remove();
				});
				return false;
			},
			clearValue: function() {
				this._clearNew();
				this.input.val("");
				this.element.val( "" );
				return false;
			},
			setValue: function(value, fn_Callback) {
				var thisInput = this.input;
				var self = this;
				var _isDone = false;
				this.element.children( "option" ).each(function() {
					if ($(this).val() == value) {
						this.selected = true;
						_isDone = true;
						$(thisInput).val($( this ).text());
						self._trigger('select', jQuery.Event("select"), {
							item: this, 
							fnCallback: fn_Callback
						});
						return false;
					}
				});
				if ((! _isDone) && (this.options.is_allow_add)) {
					$(thisInput).val(value);
					this._checkSelectValue(jQuery.Event("setValue"), this.element )
				}
				return false;
			},
			setFocus: function() {
				this.input.focus();
				return false;
			},
			enable: function(is_enable) {
				this.input.attr('readonly', !is_enable);
				if (is_enable) {
					this.input.removeClass('ui-combobox-input-disabled');
					this.btnButton.css('display', '');
				} else {
					this.input.addClass('ui-combobox-input-disabled');
					this.btnButton.css('display', 'none');
				}
				return false;
			}
		});
		
		$.extend($.ui.combobox, {
			is_allow_add: false,
			changed: function(value, event, ui) {
				return false;
			},
			blnBeforeChange: function (value, event, ui) {
				return true;
			}
		});
	})( jQuery );
