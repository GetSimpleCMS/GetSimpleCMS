/**
 * Floating-Tools
 * Author: Philipp Stracker (2013)
 * Project page: http://stracker-phil.github.com/Floating-Tools/
 */
(function() {

	var floatingtools = function() {
		this.dom = null;  // the main toolbar-object
		this.toolbars = [];
		this.is_visible = false;

		this.hide_on_blur = true; //!TODO: Make this an configuration option

		this.toolbarsize = false;
		this.editoroffset = false;
		this.mousepos = {x:0, y:0};
	};




	CKEDITOR.plugins.add( 'floating-tools', {
		requires: 'toolbar',


		init: function( editor ) {


			/**
			 * Create the UI elements required by this plugin
			 * UI is the floating toolbar
			 * Many parts of this function are taken from the toolbar plugin
			 */
			editor.on( 'uiSpace', function( event ) {
				// Create toolbar only once...
				event.removeListener();

				editor.floatingtools = new floatingtools();

				var labelId = CKEDITOR.tools.getNextId();

				var output = [
					// Did not find a nicer way to include the CSS required for the toolbar...
					'<style>',
					'.pos-relative {position:relative}',
					'.cke_floatingtools{',
						'position:absolute;',
						'left:0;',
						'top:-500px;',
						'padding: 5px 0 0 6px;',
						'border:1px solid #b1b1b1;',
						'border-radius:3px;',
						'box-shadow: 0 1px 10px rgba(0,0,0,0.3);',
						'transition:opacity .1s;-o-transition:opacity .1s;-moz-transition:opacity .1s;-webkit-transition:opacity .1s;',
					'}',
					'</style>',
					'<span id="', labelId, '" class="cke_voice_label">', editor.lang.toolbar.toolbars, '</span>',
					'<span id="' + editor.ui.spaceId( 'floatingtools' ) + '" class="cke_floatingtools cke_top" role="group" aria-labelledby="', labelId, '" onmousedown="return false;">' ];

				var groupStarted, pendingSeparator;
				var toolbars = editor.floatingtools.toolbars,
					toolbar = getFloatingToolbarConfig( editor );


				// Build the toolbar
				for ( var r = 0; r < toolbar.length; r++ ) {
					var toolbarId,
						toolbarObj = 0,
						toolbarName,
						row = toolbar[ r ],
						items;

					// It's better to check if the row object is really
					// available because it's a common mistake to leave
					// an extra comma in the toolbar definition
					// settings, which leads on the editor not loading
					// at all in IE. (#3983)
					if ( !row )
						continue;

					if ( groupStarted ) {
						output.push( '</span>' );
						groupStarted = 0;
						pendingSeparator = 0;
					}

					if ( row === '/' ) {
						output.push( '<span class="cke_toolbar_break"></span>' );
						continue;
					}

					items = row.items || row;

					// Create all items defined for this toolbar.
					for ( var i = 0; i < items.length; i++ ) {
						var item = items[ i ],
							canGroup;

						if ( item ) {
							if ( item.type == CKEDITOR.UI_SEPARATOR ) {
								// Do not add the separator immediately. Just save
								// it be included if we already have something in
								// the toolbar and if a new item is to be added (later).
								pendingSeparator = groupStarted && item;
								continue;
							}

							canGroup = item.canGroup !== false;

							// Initialize the toolbar first, if needed.
							if ( !toolbarObj ) {
								// Create the basic toolbar object.
								toolbarId = CKEDITOR.tools.getNextId();
								toolbarObj = { id: toolbarId, items: [] };
								toolbarName = row.name && ( editor.lang.toolbar.toolbarGroups[ row.name ] || row.name );

								// Output the toolbar opener.
								output.push( '<span id="', toolbarId, '" class="cke_toolbar"', ( toolbarName ? ' aria-labelledby="' + toolbarId + '_label"' : '' ), ' role="toolbar">' );

								// If a toolbar name is available, send the voice label.
								toolbarName && output.push( '<span id="', toolbarId, '_label" class="cke_voice_label">', toolbarName, '</span>' );

								output.push( '<span class="cke_toolbar_start"></span>' );

								// Add the toolbar to the "editor.toolbox.toolbars"
								// array.
								var index = toolbars.push( toolbarObj ) - 1;

								// Create the next/previous reference.
								if ( index > 0 ) {
									toolbarObj.previous = toolbars[ index - 1 ];
									toolbarObj.previous.next = toolbarObj;
								}
							}

							if ( canGroup ) {
								if ( !groupStarted ) {
									output.push( '<span class="cke_toolgroup" role="presentation">' );
									groupStarted = 1;
								}
							} else if ( groupStarted ) {
								output.push( '</span>' );
								groupStarted = 0;
							}

							function addItem( item ) {
								var itemObj = item.render( editor, output );
								index = toolbarObj.items.push( itemObj ) - 1;

								if ( index > 0 ) {
									itemObj.previous = toolbarObj.items[ index - 1 ];
									itemObj.previous.next = itemObj;
								}

								itemObj.toolbar = toolbarObj;

								// No need for keyboard handlers, the toolbar is only accessibly by mouse
								/*
								itemObj.onkey = itemKeystroke;

								// Fix for #3052:
								// Prevent JAWS from focusing the toolbar after document load.
								itemObj.onfocus = function() {
									if ( !editor.toolbox.focusCommandExecuted )
										editor.focus();
								};
								*/
							}

							if ( pendingSeparator ) {
								addItem( pendingSeparator );
								pendingSeparator = 0;
							}

							addItem( item );

						}
					}

					if ( groupStarted ) {
						output.push( '</span>' );
						groupStarted = 0;
						pendingSeparator = 0;
					}

					if ( toolbarObj )
						output.push( '<span class="cke_toolbar_end"></span></span>' );

				}


				output.push( '</span>' );
				event.data.html += output.join( '' );
			});



			/**
			 * Do the magic: Attach eventhandlers to see if text is selected
			 * When text is selected then show the floating toolbar, else hide it
			 */
			editor.on('contentDom', function( event ) {

				unfocus_toolbar();

				/**
				 * Attach an eventhandler to the mouse-up event
				 */
				editor.document.on('mouseup', function( mouse_event ) {
					// When user right-clicks, ctrl-clicks, etc. then do not show the toolbar
					data = mouse_event.data.$;
					if (data.button !== 0 || data.ctrlKey || data.altKey || data.shiftKey) return true;

					// When the user clears the selection by single-clicking in the editor then this event is fired before the selection is removed
					// So we add a short delay to give the browser a chance to remove the selection before we do anything
					setTimeout( function() {
						if (is_text_selected()) {
							// Save the current mouse-position
							set_mousepos (mouse_event.data.$);
							// when there is text selected after mouse-up: show the toolbar
							editor.execCommand('showFloatingTools');
						} else {
							// when no text is selected then hide the toolbar
							editor.execCommand('hideFloatingTools');
						}
					}, 100);
				});


				/**
				 * On keypress we will always hide the toolbar
				 * The toolbar is only accessible via mouse
				 */
				editor.document.on('keyup', function( key_event ) {
					editor.execCommand('hideFloatingTools');
				});


				/**
				 * On blur hide the toolbar (editor looses focus)
				 */
				editor.on('blur', function( e ) {
					if (editor.floatingtools.hide_on_blur) {
						hide_toolbar();
					}
				});


				/**
				 * Attach the mouse-over event to the toolbar.
				 * When cursor is above the toolbar then set opacity to 1
				 */
				toolbar = get_element();
				toolbar.on('mouseover', function( mouse_event ) {
					focus_toolbar();
				});


				/**
				 * When the mouse moves out of the toolbar then make it transparent again
				 */
				toolbar.on('mouseout', function( mouse_event ) {
					unfocus_toolbar();
				})

				editor.container.addClass('pos-relative');
				console.log(editor.container);
			});



			/**
			 * Display the floating toolbar
			 */
			editor.addCommand( 'showFloatingTools', {
				exec : function( editor ) {
					if (is_text_selected()) {
						toolbar = get_element();
						unfocus_toolbar();
						toolbar.show();

						// Get the size of the toolbar
						size = get_toolbar_size()
						// Get the offset of the editor
						offset = get_editor_offset();
						// Get the mouse position
						pos = get_mousepos();

						// Calculate the position for the toolbar
						toolpos = calculate_position(pos, size, offset);

						toolbar.setStyles({
							'left' : toolpos.x + 'px',
							'top' : toolpos.y + 'px'
						});
						editor.floatingtools.is_visible = true;
					}
				}
			});


			/**
			 * Hide the floating toolbar
			 */
			editor.addCommand( 'hideFloatingTools', {
				exec : function( editor ) {
					hide_toolbar();
				}
			});


			/**
			 * ===== Behind the scenes. Getters, setters, calculation, etc.
			 */


			hide_toolbar = function() {
				if (false != editor.floatingtools.is_visible) {
					toolbar = get_element();
					toolbar.hide();
					editor.floatingtools.is_visible = false;
				}
			}


			/**
			 * Store the current mouse-position, so we can position the toolbar near the cursor
			 */
			set_mousepos = function(data) {
				editor.floatingtools.mousepos = {
					left: data.clientX,
					top: data.clientY
				};
			}



			/**
			 * Store the current mouse-position, so we can position the toolbar near the cursor
			 */
			get_mousepos = function() {
				return editor.floatingtools.mousepos;
			}


			/**
			 * Returns the main toolbar-object (the parent of all items in the floating-toolbar)
			 */
			get_element = function() {
				if (! editor.floatingtools.dom) {
					var dom_id = editor.ui.spaceId( 'floatingtools' );
					editor.floatingtools.dom = CKEDITOR.document.getById( dom_id );
				}
				return editor.floatingtools.dom;
			}



			/**
			 * Returns the offset of the editor area (effectively the height of the top-toolbar)
			 */
			get_editor_offset = function() {
				if (! editor.floatingtools.editoroffset) {
					var editor_id = editor.ui.spaceId( 'contents' );
					var obj = CKEDITOR.document.getById( editor_id );
					editor.floatingtools.editoroffset = {
						left:   obj.$.offsetLeft,
						top:    obj.$.offsetTop,
						width:  obj.$.offsetWidth,
						height: obj.$.offsetHeight
					};
				}
				return editor.floatingtools.editoroffset;
			}


			/**
			 * Calculates the position for the toolbar
			 */
			calculate_position = function(pos, toolbar_size, offset) {
				toolpos = {
					x: pos.left + offset.left - (toolbar_size.width/2),
					y: pos.top + offset.top - (toolbar_size.height + 20)
				}

				// make sure toolbar does not extend out of the left CKEditor border
				if (toolpos.x < offset.left + 2) toolpos.x = offset.left + 2;

				// make sure toolbar does not extend out of the right CKEditor border
				if (pos.left + (toolbar_size.width/2) >= offset.left + offset.width-2 )
					toolpos.x = offset.left + offset.width - toolbar_size.width - 2;

				// Make sure toolbar does no go into the top toolbar area
				if (toolpos.y < offset.top) toolpos.y = offset.top;

				// make sure toolbar does not cover the mouse-cursor when text in the top line is selected
				if (offset.top+pos.top > toolpos.y
				&& offset.top+pos.top < toolpos.y+toolbar_size.height)
					toolpos.y = offset.top + pos.top + 24; // display toolbar below the cursor

				return toolpos;
			}


			/**
			 * Returns the size of the floating toolbar
			 */
			get_toolbar_size = function() {
				if (! editor.floatingtools.toolbarsize) {
					var obj = get_element();
					editor.floatingtools.toolbarsize = {
						width: obj.$.offsetWidth,
						height: obj.$.offsetHeight
					};
				}
				return editor.floatingtools.toolbarsize;
			}


			/**
			 * Check if text is selected.
			 * Retrns true when there is at least 1 character selected in the editor
			 */
			is_text_selected = function () {
				var text = editor.getSelection().getSelectedText();
				return text != '';
			}


			/**
			 * Make the toolbar opaque
			 */
			focus_toolbar = function() {
				obj = get_element();
				obj.setOpacity(1);
			}


			/**
			 * Make the toolbar transparent
			 */
			unfocus_toolbar = function() {
				obj = get_element();
				obj.setOpacity(0.25);
			},


			/**
			 * Get the plugin configuration.
			 * Kidnapped from the toolbar-plugin...
			 */
			getFloatingToolbarConfig = function( editor ) {
				var removeButtons = editor.config.removeButtons;

				removeButtons = removeButtons && removeButtons.split( ',' );

				function buildToolbarConfig() {
					// Take the base for the new toolbar, which is basically a toolbar
					// definition without items.
					var toolbar = getPrivateFloatingToolbarGroups( editor );
					return populateToolbarConfig( toolbar );

				}

				// Returns an object containing all toolbar groups used by ui items.
				function getItemDefinedGroups() {
					var groups = {},
						itemName, item, itemToolbar, group, order;

					for ( itemName in editor.ui.items ) {
						item = editor.ui.items[ itemName ];
						itemToolbar = item.toolbar || 'others';
						if ( itemToolbar ) {
							// Break the toolbar property into its parts: "group_name[,order]".
							itemToolbar = itemToolbar.split( ',' );
							group = itemToolbar[ 0 ];
							order = parseInt( itemToolbar[ 1 ] || -1, 10 );

							// Initialize the group, if necessary.
							groups[ group ] || ( groups[ group ] = [] );

							// Push the data used to build the toolbar later.
							groups[ group ].push( { name: itemName, order: order} );
						}
					}

					// Put the items in the right order.
					for ( group in groups ) {
						groups[ group ] = groups[ group ].sort( function( a, b ) {
							return a.order == b.order ? 0 :
								b.order < 0 ? -1 :
								a.order < 0 ? 1 :
								a.order < b.order ? -1 :
								1;
						});
					}

					return groups;
				}

				function fillGroup( toolbarGroup, uiItems ) {
					if ( uiItems.length ) {
						if ( toolbarGroup.items )
							toolbarGroup.items.push( editor.ui.create( '-' ) );
						else
							toolbarGroup.items = [];

						var item, name;
						while ( ( item = uiItems.shift() ) ) {
							name = typeof item == 'string' ? item : item.name;

							// Ignore items that are configured to be removed.
							if ( !removeButtons || CKEDITOR.tools.indexOf( removeButtons, name ) == -1 ) {
								item = editor.ui.create( name );

								if ( !item )
									continue;

								if ( !editor.addFeature( item ) )
									continue;

								toolbarGroup.items.push( item );
							}
						}
					}
				}

				function populateToolbarConfig( config ) {
					var toolbar = [],
						i, group, newGroup;

					for ( i = 0; i < config.length; ++i ) {
						group = config[ i ];
						newGroup = {};

						if ( group == '/' )
							toolbar.push( group );
						else if ( CKEDITOR.tools.isArray( group) ) {
							fillGroup( newGroup, CKEDITOR.tools.clone( group ) );
							toolbar.push( newGroup );
						}
						else if ( group.items ) {
							fillGroup( newGroup, CKEDITOR.tools.clone( group.items ) );
							newGroup.name = group.name;
							toolbar.push( newGroup);
						}
					}

					return toolbar;
				}

				var toolbar = editor.config.floatingtools;

				// If it is a string, return the relative "toolbar_name" config.
				if ( typeof toolbar == 'string' )
					toolbar = editor.config[ 'floatingtools_' + toolbar ];

				return ( editor.toolbar = toolbar ? populateToolbarConfig( toolbar ) : buildToolbarConfig() );
			},


			/**
			 * Return the default toolbar configuration.
			 */
			getPrivateFloatingToolbarGroups = function( editor ) {
				return editor._.floatingToolsGroups || ( editor._.floatingToolsGroups = [
					{ name: 'styles',    items: [ 'Font','FontSize' ]},
					{ name: 'format',    items: [ 'Bold','Italic' ]},
					{ name: 'paragraph', items: [ 'JustifyCenter','Outdent','Indent','NumberedList','BulletedList' ]}
				]);
			}

		}

	} );


})();



