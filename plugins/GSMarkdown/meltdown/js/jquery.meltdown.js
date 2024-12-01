/*global jQuery, addResizeListener*/

/*
 * Meltdown (Markup Extra Live Toolbox)
 * Version: 0.2 (??-APR-2014)
 * Requires: jQuery v1.7.2 or later (1.9.1 recommended)
 */

(function ($, window, document, undefined) {
    'use strict';

    var ver = '0.2',
        plgName = 'meltdown',
        dbg = true,
        isIE8 = document.all && !document.addEventListener,  // From: http://tanalin.com/en/articles/ie-version-js/
        jqueryRequired = [1, 8, 0],
        jqueryCurrent  = $.fn.jquery.split(' ')[0].split('.'), // first split to get rid of any amd related extras
        isOldjQuery = false,
        doc = $(document),
        body = $("body");

    for (var i = 0; i < jqueryRequired.length; i++) {
        var required = jqueryRequired[i],
            current = parseInt(jqueryCurrent[i], 10);
        if (required > current) {
            isOldjQuery = true;
            break;
        } else if (current > required) {
            break;
        }
    }

    function debug(msg) {
        if (window.console && dbg) {
            window.console.log(msg);
        }
    }

    // Used to test the bottom offset of elements:
    var bottomPositionTest = $('<div style="bottom: 0;" />');

    // Helper for users that want to change the controls (For usage, see: $.meltdown.defaults.controls below)
    var controlsGroup = function(name, label, controls) {
        controls.name = name;
        controls.label = label;
        return controls;
    };

    $.meltdown = {
        version: ver,

        // Expose publicly:
        controlsGroup: controlsGroup,

        // Default meltdown options:
        defaults: {
            // Use $.meltdown.controlsGroup() to make groups and subgroups of controls.
            // The available control names come from the keys of $.meltdown.controlDefs (see below)
            controls: controlsGroup("", "", [
                "preview",
                "bold",
                "italics",
                "ul",
                "ol",
                "|",
                "table",
                controlsGroup("h", "Headers", ["h1", "h2", "h3", "h4", "h5", "h6"]),
                "|",
                controlsGroup("kitchenSink", "Kitchen Sink", [
                    "link",
                    "img",
                    "blockquote",
                    "codeblock",
                    "code",
                    "footnote",
                    "hr"
                ]),
                "fullscreen",
                "sidebyside"
            ]),

            // If true, goes directly in fullscreen mode:
            fullscreen: false,

            // Should the preview be visible by default ?
            openPreview: false,

            // A CSS height or "editorHeight" or "auto" (to let the height adjust to the content).
            previewHeight: "editorHeight",

            // If true, when the preview is toggled it will (un)collapse resulting in the total height of the wrap to change.
            // Set this to false if you want the editor to expand/shrinkin the opposite way of the preview.
            // Setting this to false can be useful if you want to restrict or lock the total height.
            previewCollapses: true,

            // If true, editor and preview will be displayed side by side instead of one on the other.
            sidebyside: false,

            // If true, when the preview is fully scrolled it will stay scrolled while typing.
            // Very convenient when typing/adding text at the end of the editor.
            autoScrollPreview: true,

            // Duration of the preview toggle animation:
            previewDuration: 400,

            // The parser. The function takes a string and returns an html formatted string.
            // Set this to false to use an _identity_ function (for a direct HTML "parser").
            parser: window.Markdown
        },

        // Definitions for the toolbar controls:
        controlDefs: {
            bold: {
                label: "B",
                altText: "Bold",
                before: "**",
                after: "**"
            },
            italics: {
                label: "I",
                altText: "Italics",
                before: "*",
                after: "*"
            },
            ul: {
                label: "UL",
                altText: "Unordered List",
                preselectLine: true,
                before: "* ",
                placeholder: "Item\n* Item",
                isBlock: true
            },
            ol: {
                label: "OL",
                altText: "Ordered List",
                preselectLine: true,
                before: "1. ",
                placeholder: "Item 1\n2. Item 2\n3. Item 3",
                isBlock: true
            },
            table: {
                label: "Table",
                altText: "Table",
                before: "First Header  | Second Header\n------------- | -------------\nContent Cell  | Content Cell\nContent Cell  | Content Cell\n",
                isBlock: true
            },
            link: {
                label: "Link",
                altText: "Link",
                before: "[",
                placeholder: "Example link",
                after: "](http:// \"Link title\")"
            },
            img: {
                label: "Image",
                altText: "Image",
                before: "![Alt text](",
                placeholder: "http://",
                after: ")"
            },
            blockquote: {
                label: "Blockquote",
                altText: "Blockquote",
                preselectLine: true,
                before: "> ",
                placeholder: "Quoted text",
                isBlock: true
            },
            codeblock: {
                label: "Code Block",
                altText: "Code Block",
                preselectLine: true,
                before: "~~~\n",
                placeholder: "Code",
                after: "\n~~~",
                isBlock: true
            },
            code: {
                label: "Code",
                altText: "Inline Code",
                before: "`",
                placeholder: "code",
                after: "`"
            },
            footnote: {
                label: "Footnote",
                altText: "Footnote",
                before: "[^1]\n\n[^1]:",
                placeholder: "Example footnote",
                isBlock: true
            },
            hr: {
                label: "HR",
                altText: "Horizontal Rule",
                before: "----------",
                placeholder: "",
                isBlock: true
            },
            fullscreen: {
                label: "Fullscreen",
                altText: "Toggle fullscreen",
                click: function(meltdown /*, def, control, execAction */) {
                    meltdown.toggleFullscreen();
                }
            },
            sidebyside: {
                label: "Sidebyside",
                altText: "Toggle sidebyside",
                click: function(meltdown /*, def, control, execAction */) {
                    meltdown.toggleSidebyside();
                }
            },
            preview: {
                label: "Preview",
                altText: "Toggle preview",
                click: function(meltdown /*, def, control, execAction */) {
                    meltdown.togglePreview();
                }
            }
        }
    };

    // Add h1...h6 control definitions to $.meltdown.controlDefs:
    (function(controlDefs) {
        for (var pounds = "", i = 1; i <= 6; i++) {
            pounds += "#";
            controlDefs['h' + i] = {
                label: "H" + i,
                altText: "Header " + i,
                preselectLine: true,
                before: pounds + " "
            };
        }
    })($.meltdown.controlDefs);


    function addControlEventHandler(meltdown, def, control) {
        var editor = meltdown.editor,
            execAction = function () {
                var text = editor.val(),
                    selection = editor.getSelection(),
                    before = def.before || "",
                    placeholder =  def.placeholder || "",
                    after = def.after || "";

                // Extend selection if needed:
                if (def.preselectLine) {
                    var lineStart = text.lastIndexOf('\n', selection.start) + 1,
                        lineEnd = text.indexOf('\n', selection.end);

                    if (lineEnd === -1) {
                        lineEnd = text.length;
                    }
                    editor.setSelection(lineStart, lineEnd);
                    selection = editor.getSelection();
                }

                // placeholder is only used if there is no selected text:
                if (selection.length > 0) {
                    placeholder = selection.text;
                }

                // isBlock means that there should be empty line before and after the selection:
                if (def.isBlock) {
                    for (var i = 0; i < 2; i++) {
                        var charBefore = text.charAt(selection.start - 1 - i),
                            charAfter = text.charAt(selection.end + i);

                        if (charBefore !== "\n" && charBefore !== "") {
                            before = "\n" + before;
                        }
                        if (charAfter !== "\n" && charAfter !== "") {
                            after = after + "\n";
                        }
                    }
                }

                // Insert placeholder:
                if (selection.text !== placeholder) {
                    editor.replaceSelectedText(placeholder, "select");
                }
                // Insert before and after selection:
                editor.surroundSelectedText(before, after, "select");
            };

        control.click(function (e) {
            if (!control.hasClass('disabled')) {
                if (def.click) {
                    def.click(meltdown, def, control, execAction);
                } else {
                    execAction();
                }
                meltdown.update();
            }
            editor.focus();
            e.preventDefault();
        });
    }

    function addGroupClickHandler(control) {
        control.on('click', function () {
            control.siblings('li').removeClass(plgName + '_controlgroup-open').children('ul').hide();
            control.toggleClass(plgName + '_controlgroup-open').children('ul').toggle();
        });
    }

    function buildControls(meltdown, controlsGroup, subGroup) {
        var controlList = $('<ul />');
        if (subGroup) {
            controlList.css("display", "none");
            controlList.addClass(plgName + "_controlgroup-" + controlsGroup.name + " " + plgName + '_controlgroup-dropdown');
        } else {
            controlList.addClass("meltdown_controls");
        }

        for (var i = 0; i < controlsGroup.length; i++) {
            var controlName = controlsGroup[i],
                control = $('<li />'),
                span = $('<span />').appendTo(control);
            if ($.type(controlName) === "string") {
                if (controlName === "|") {  // Separator
                    controlList.append(control.addClass(plgName + '_controlsep ' + plgName + '_controlbutton'));
                    continue;
                }
                var def = $.meltdown.controlDefs[controlName];
                if (def === undefined) {
                    debug("Control not found: " + controlName);
                    continue;
                }
                control.addClass(plgName + "_control-" + controlName + " " + plgName + '_control ' + plgName + '_controlbutton ' + (def.styleClass || ""));
                span.text(def.label).attr("title", def.altText);
                addControlEventHandler(meltdown, def, control);

            } else if ($.isArray(controlName)) {
                control.addClass(plgName + "_controlgroup-" + controlName.name + " " + plgName + '_controlgroup ' + plgName + '_controlbutton');
                span.text(controlName.label).append('<i class="meltdown-icon-caret-down" />');
                addGroupClickHandler(control);
                control.append(buildControls(meltdown, controlName, true));
            }
            controlList.append(control);
        }

        return controlList;
    }

    function addWarning(meltdown, element) {
        element.click(function(e) {
            var warning = $('<div class"' + plgName + '_warning"/>').html('<center><b>The preview area is a tech preview feature</b></center><br/>'
                                                                         + 'Live previews <b>can</b> cause the browser tab to stop responding.<br/><br/>'
                                                                         + 'There is a <a target="_blank" href="https://github.com/iphands/Meltdown/issues/1">known issue</a> with <a target="_blank" href="https://github.com/tanakahisateru/js-markdown-extra#notice">one of the libraries</a> used to generate the live preview.<br/><br/>'
                                                                         + 'This warning will be removed when the issue is resolved.<br/><br/>'
                                                                         + '<center><i>Click to continue.</i></center>').css({background: "#fdd", cursor: "pointer"});
            warning.on("click", function(e) {
                if (!$(e.target).is("a, a *")) {    // Ignore clicks on links
                    meltdown.update(true);
                }
            });
            meltdown.preview.empty().append(warning);
            e.preventDefault();
        });
    }

    // Setup event handlers for the resize handle:
    function setupResizeHandle(resizeHandle, firstElem, lastElem, vertical, meltdown) {
        resizeHandle.addClass("meltdown_resizehandle-" + (vertical ? "vert" : "horiz"));
        var propName = vertical ? "height" : "width",
            pageName = vertical ? "pageY" : "pageX",
            lastEditorPercentName = vertical ? "lastEditorPercentHeight" : "lastEditorPercentWidth",
            minSize = vertical ? 15 : 60;

        var startPos, minPos, maxPos, originalFirstElemSize, originalLastElemSize,
            moveEventHandler = function(e) {
                var delta = Math.min(Math.max(e[pageName] , minPos), maxPos) - startPos,
                    firstElemSize = originalFirstElemSize + delta,
                    lastElemSize = originalLastElemSize - delta;
                firstElem[propName](firstElemSize);
                lastElem[propName](lastElemSize);
                if (!vertical) {
                    firstElem[0].style.maxWidth = firstElemSize + "px";
                    lastElem[0].style.maxWidth = lastElemSize + "px";
                }

                var editorElem = vertical ? meltdown.editor[0] : meltdown.editorWrap[0],
                    editorSize = firstElem[0] === editorElem ? firstElemSize : lastElemSize;
                meltdown[lastEditorPercentName] = editorSize / (firstElemSize + lastElemSize);
            };

        // Init dragging handlers only on mousedown:
        resizeHandle.on("mousedown", function(e) {
            if (meltdown.isSidebyside() === vertical) {
                return;
            }
            // Sort elems in document order:
            var elems = firstElem.add(lastElem);
            // The first elem is assumed to be before resizeHandle, and the last is after:
            firstElem = elems.first();
            lastElem = elems.last();

            // Init dragging properties:
            startPos = e[pageName];
            originalFirstElemSize = firstElem[propName]();
            originalLastElemSize = lastElem[propName]();
            minPos = startPos - originalFirstElemSize + minSize;
            maxPos = startPos + originalLastElemSize - minSize;

            // Setup event handlers:
            doc.on("mousemove", moveEventHandler).one("mouseup", function() {
                doc.off("mousemove", moveEventHandler);
                body.removeClass("unselectable");
                meltdown.editor.focus();
            });
            // Prevent text selection while dragging:
            body.addClass("unselectable");
        });
    }

    function debounce(func, wait, returnValue) {
        var context, args, timeout,
            exec = function() {
                func.apply(context, args);
            };
        return function() {
            context = this;
            args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(exec, wait);
            return returnValue;
        };
    }

    // Return true, false or undefined.
    // If newState is undefined or not a boolean, return !state (this is the toggle action)
    // If newState === state, return newState or if force, return undefined (to tell that no state change is required)
    function checkToggleState(newState, state, force) {
        if (newState !== true && newState !== false) {
            return !state;
        }
        if (newState === state) {
            return force ? newState : undefined;
        }
        return newState;
    }

    function splitSize(availableSize, firstPercentSize, minSize) {
        var firstSize = Math.round(firstPercentSize * availableSize),
            lastSize = availableSize - firstSize;
        if (firstSize < minSize) {
            lastSize -= minSize - firstSize;
            firstSize = minSize;
        } else if (lastSize < minSize) {
            firstSize -= minSize - lastSize;
            lastSize = minSize;
        }
        return {firstSize: firstSize, lastSize: lastSize};
    }


    // Meltdown base class:
    var Meltdown = $.meltdown.Meltdown = function(elem) {
        this.element = $(elem);
    };

    // The Meltdown methods.
    // Methods are publicly available: elem.meltdown("methodName", args...)
    $.meltdown.methods = $.extend(Meltdown.prototype, {
        _init: function(userOptions) {
            var self = this,
                _options = this._options = $.extend({}, $.meltdown.defaults, userOptions);

            this._lastUpdateText = "";

            // If parser is false, use a HTML parser (ie. directly use the text as the HTML source)
            this.parser = _options.parser || function(text) {
                return text;
            };

            this.editorPreInitOuterWidth = this.element.outerWidth();

            // Setup everything detached from the document:
            this.wrap = $('<div class="' + plgName + '_wrap previewopen" />');
            this.topmargin = $('<div class="' + plgName + '_topmargin"/>').appendTo(this.wrap);
            this.bar =  $('<div class="meltdown_bar"></div>').appendTo(this.wrap);
            this.editorWrap =  $('<div class="' + plgName + '_editor-wrap" />').appendTo(this.wrap);
            this.editorDeco =  $('<div class="' + plgName + '_editor-deco" />').appendTo(this.editorWrap);
            this.editor = this.element.addClass("meltdown_editor");
            this.previewWrap =  $('<div class="' + plgName + '_preview-wrap" />').appendTo(this.wrap);
            this.resizeHandle = $('<div class="' + plgName + '_resizehandle"><span></span></div>').appendTo(this.previewWrap);
            this.previewHeader =  $('<span class="' + plgName + '_preview-header">Preview Area (<a class="meltdown_techpreview" href="https://github.com/iphands/Meltdown/issues/1">Tech Preview</a>)</span>').appendTo(this.previewWrap);
            this.preview =  $('<div class="' + plgName + '_preview" />').appendTo(this.previewWrap);
            this.bottommargin = $('<div class="' + plgName + '_bottommargin"/>').appendTo(this.wrap);

            // Setup meltdown sizes:
            this.wrap.outerWidth(this.editorPreInitOuterWidth); // jQuery 1.8+ (undocumented: http://bugs.jquery.com/ticket/10877)
            if (isOldjQuery) this.wrap.width(this.editorPreInitOuterWidth); // Good enough.
            var previewHeight = _options.previewHeight;
            if (previewHeight === "editorHeight") {
                previewHeight = this.editor.height();
            }
            this.preview.height(previewHeight);

            // Build toolbar:
            this.controls = buildControls(this, _options.controls).appendTo(this.bar);
            addWarning(this, this.previewHeader.find(".meltdown_techpreview"));

            // editorDeco's CSS need a bit of help:
            this.editor.focus(function() {
                self.editorDeco.addClass("focus");
            }).blur(function() {
                self.editorDeco.removeClass("focus");
            });

            // Need to put a div in the wrap to allow absolute positioning for child elements.
            // Bug in FF < 31: https://bugzilla.mozilla.org/show_bug.cgi?id=63895
            this.previewWrap2 = $('<div class="' + plgName + '_preview-wrap2"></div>').appendTo(this.previewWrap);
            this.previewWrap2.append(this.resizeHandle, this.previewHeader, this.preview);
            setupResizeHandle(this.resizeHandle, this.editor, this.preview, true, this);
            setupResizeHandle(this.resizeHandle, this.editorWrap, this.previewWrap, false, this);

            // Setup update:
            this.debouncedUpdate = debounce(this.update, 350, this);
            this.editor.on('keyup', $.proxy(this.debouncedUpdate, this));

            // Store datas needed by fullscreen mode:
            this.fullscreenData = {};

            // Insert meltdown in the document:
            this.editor.after(this.wrap).appendTo(this.editorDeco);
            this._checkToolbarOverflowedControls();

            // Setup display state (preview open and _heightsManaged):
            this._previewCollapses = _options.previewCollapses;
            this.togglePreview(true, 0, true, !_options.openPreview);   // Do not update the preview if !_options.openPreview
            if (!this.isPreviewCollapses() && _options.previewHeight === "auto") {
                this.preview.height("+=0"); // If !_previewCollapses, we cannot have a dynamic height.
            }
            this._checkHeightsManaged("", undefined, true); // Set CSS height of wrap.

            // Define the wrap min height from the editor and the preview min heights:
            var wrapHeight = this.wrap.height(),
                minWrapHeights = parseFloat(this.editor.css("minHeight")) + parseFloat(this.preview.css("minHeight")),
                editorHeight = this.editor.height();
            previewHeight = this.preview.height();
            this.wrap.css("minHeight", wrapHeight - editorHeight - previewHeight + minWrapHeights);

            // Setup editor and preview resizing when wrap is resized:
            this.lastWrapWidth = this.wrap.width();
            this.lastWrapHeight = wrapHeight;
            this.lastEditorPercentWidth = 0.5;
            this.lastEditorPercentHeight = editorHeight / (editorHeight + previewHeight);
            addResizeListener(this.wrap[0], $.proxy(this._wrapResizeListener, this));

            // Now that all measures were made, we can close the preview if needed:
            if (!_options.openPreview) {
                this.togglePreview(false, 0);
            }
            // And set the sidebyside and fullscreen modes:
            this.toggleSidebyside(_options.sidebyside, true);
            if (_options.fullscreen) {
                this.toggleFullscreen(_options.fullscreen);
            }

            return this;    // Chaining
        },
        options: function(name, value) {
            if (arguments.length === 1) {
                return this._options[name];
            } else if (arguments.length > 1) {
                this._options[name] = value;
                return this;    // Chaining
            }
        },
        update: function(force) {
            return this.updateWith(this.editor.val(), force);
        },
        updateWith: function(text, force) {
            if (force === true || (this.isPreviewOpen() && text !== this._lastUpdateText)) {
                // If the preview is scrolled to the bottom, keept it scrolled after update:
                var previewNode = this.preview[0],
                    scrolledToBottom = previewNode.scrollHeight - previewNode.scrollTop === previewNode.clientHeight;
                this.preview.html(this.parser(text));
                if (scrolledToBottom) {
                    previewNode.scrollTop = previewNode.scrollHeight;
                }
                this._lastUpdateText = text;
            }
            return this;    // Chaining
        },
        isPreviewOpen: function() {
            return this.wrap.hasClass("previewopen");
        },
        togglePreview: function(open, duration, force, noUpdate) {
            open = checkToggleState(open, this.isPreviewOpen(), force);
            if (open === undefined) {
                return this;    // Chaining
            }
            if (duration === undefined) {
                duration = this._options.previewDuration;
            }

            // Function to resize the editor when the preview is resized:
            var self = this,
                editorHeight = this.editor.height(),
                previewWrapHeightStart = open ? 0 : this.previewWrap.outerHeight(),
                availableHeight = editorHeight + previewWrapHeightStart,
                progress = this._isHeightsManaged() ? function(/* animation, progress */) {
                    self.editor.height(availableHeight - self.previewWrap.outerHeight());
                } : $.noop,
                editorWrapWidth = this.editorWrap.width(),
                previewWrapWidth = open ? 0 : this.previewWrap.width(),
                sidebysideStep = function (now /*, fx */) {
                    self.previewWrap[0].style.maxWidth = now + "px";
                    var newEditorWrapWidth = editorWrapWidth + (previewWrapWidth - now);
                    self.editorWrap.width(newEditorWrapWidth);
                    self.editorWrap[0].style.maxWidth = newEditorWrapWidth + "px";
                },
                        unsetPreviewWrapDisplay = function() {
                            self.previewWrap.css("display", "");
                        };

            if (open) {
                this.wrap.addClass("previewopen");
                if (!noUpdate) {
                    this.update();
                }
                if (this.isSidebyside()) {
                    this.previewWrap.stop().animate({
                        width: "show"
                    }, {
                        duration: duration,
                        step: sidebysideStep,
                        start: function(fx) {   // jQuery 1.8+
                            var sizes = splitSize(self.wrap.width(), self.lastEditorPercentWidth, 60);
                            fx.tweens[0].end = sizes.lastSize;
                            unsetPreviewWrapDisplay();  // Why jQuery sets this to "block" ?
                        },
                        complete: unsetPreviewWrapDisplay   // Why jQuery sets this to "block" ?
                    });
                } else {
                    var previewWrapHeightUsed = this.previewWrap.outerHeight();
                    // Check that preview is not too big:
                    if (this._heightsManaged && previewWrapHeightUsed > editorHeight - 15) {
                        this.preview.height("-=" + (previewWrapHeightUsed - (editorHeight - 15)));
                    }
                    if (!isOldjQuery) {
                        this.previewWrap.stop().slideDown({
                            duration: duration,
                            progress: progress, // jQuery 1.8+
                            start: unsetPreviewWrapDisplay, // Why jQuery sets this to "block" ?    // jQuery 1.8+
                            complete: unsetPreviewWrapDisplay   // Why jQuery sets this to "block" ?
                        });
                    } else {
                        if (this._heightsManaged) {
                            this.editor.height("-=" + previewWrapHeightUsed);
                        }
                        this.previewWrap.stop().show();
                        unsetPreviewWrapDisplay();  // Why jQuery sets this to "block" ?
                    }
                }
            } else {
                if (this.isSidebyside()) {
                    this.previewWrap.stop().animate({
                        width: "hide"
                    }, {
                        duration: duration,
                        step: sidebysideStep,
                        complete: function() {
                            self.previewWrap.css("max-width", "");
                        }
                    });
                } else {
                    if (!isOldjQuery && this.previewWrap.is(":visible") && duration > 0) {  // slideUp() doesn't work on hidden elements.
                        this.previewWrap.stop().slideUp({
                            duration: duration,
                            progress: progress  // jQuery 1.8+
                        });
                    } else {
                        this.previewWrap.stop().hide();
                        if (this._heightsManaged) {
                            this.editor.height(availableHeight);
                        }
                    }
                }
                this.wrap.removeClass("previewopen");
            }

            return this;    // Chaining
        },
        isFullscreen: function() {
            return this.wrap.hasClass('fullscreen');
        },
        toggleFullscreen: function(full) {
            full = checkToggleState(full, this.isFullscreen());
            if (full === undefined) {
                return this;    // Chaining
            }

            var data = this.fullscreenData;
            if (full) {
                data.originalWrapHeight = this.wrap.height();
                data.availableHeight = this.editor.height() + this.preview.height();
                // Keep height in case it is "auto" or "" or whatever:
                data.originalWrapStyleHeight = this.wrap[0].style.height;
                this._checkHeightsManaged("fullscreen", true);

                this.wrap.addClass('fullscreen');
                var self = this;
                doc.on("keypress." + plgName + ".fullscreenEscKey", function(e) {
                    if (e.keyCode === 27) { // Esc key
                        self.toggleFullscreen(false);
                    }
                });
            } else {
                doc.off("keypress." + plgName + ".fullscreenEscKey");
                this.wrap.removeClass('fullscreen');

                if (this._isHeightsManaged()) {
                    this._adjustHeights(data.originalWrapHeight);
                    this.lastWrapHeight = data.originalWrapHeight;
                } else {
                    var sizes = splitSize(data.availableHeight, this.lastEditorPercentHeight, 15);
                    this.editor.height(sizes.firstSize);
                    this.preview.height(sizes.lastSize);
                }
                this._checkHeightsManaged("fullscreen", false);
                this.wrap[0].style.height = data.originalWrapStyleHeight;
            }
            this._wrapResizeListener();

            return this;    // Chaining
        },
        isSidebyside: function() {
            return this.wrap.hasClass('sidebyside');
        },
        toggleSidebyside: function(sidebyside, force) {
            sidebyside = checkToggleState(sidebyside, this.isSidebyside(), force);
            if (sidebyside === undefined) {
                return this;    // Chaining
            }

            var isPreviewOpen = this.isPreviewOpen(),
                originalBottommarginTop = this.bottommargin.offset().top;
            if (sidebyside) {
                this.wrap.addClass("sidebyside");
                this._adjustWidths(this.wrap.width());
                if (!isPreviewOpen) {
                    this.togglePreview(true, 0, false, true);
                }
                var editorBottom = bottomPositionTest.appendTo(this.editorWrap).offset().top,
                    previewBottom = bottomPositionTest.appendTo(this.previewWrap).offset().top;
                bottomPositionTest.detach();
                if (!isPreviewOpen) {
                    this.togglePreview(false, 0, false, true);
                }
                var diffHeights = editorBottom - previewBottom;
                this.preview.height("+=" + diffHeights);

                var deltaWrapHeight = originalBottommarginTop - this.bottommargin.offset().top;
                this.editor.height("+=" + deltaWrapHeight);
                this.preview.height("+=" + deltaWrapHeight);
                this._checkHeightsManaged("sidebyside", true);
            } else {
                if (!isPreviewOpen) {
                    this.togglePreview(true, 0, false, true);
                }
                var originalWrapHeight = this.wrap.height();
                this.editorWrap.css("width", "");
                this._checkHeightsManaged("sidebyside", false);
                this.editorWrap.css({width: "", maxWidth: ""});
                this.previewWrap.css({width: "", maxWidth: ""});
                this.wrap.removeClass("sidebyside");

                var deltaBottommarginTop = this.bottommargin.offset().top - originalBottommarginTop;
                this.lastWrapHeight = originalWrapHeight + deltaBottommarginTop;
                this._adjustHeights(originalWrapHeight);
                this.lastWrapHeight = originalWrapHeight;
                if (!isPreviewOpen) {
                    this.togglePreview(false, 0, false, true);
                }
            }

            return this;    // Chaining
        },
        isPreviewCollapses: function() {
            return this._previewCollapses;
        },
        togglePreviewCollapses: function(previewCollapses, force) {
            previewCollapses = checkToggleState(previewCollapses, this._previewCollapses, force);
            if (previewCollapses === undefined) {
                return this;    // Chaining
            }

            this._previewCollapses = previewCollapses;
            this._checkHeightsManaged();

            return this;    // Chaining
        },
        _isHeightsManaged: function() {
            return this._heightsManaged;
        },
        _toggleHeightsManaged: function(manage, force) {
            manage = checkToggleState(manage, this._heightsManaged, force);
            if (manage === undefined) {
                return this;    // Chaining
            }

            if (manage) {
                this.wrap.height("+=0").addClass("heightsManaged");
            } else {
                this.wrap.height("auto").removeClass("heightsManaged");
            }
            this._heightsManaged = manage;

            return this;    // Chaining
        },
        _checkHeightsManaged: function(change, value, force) {
            var previewCollapses = change === "previewCollapses" ? value : this._previewCollapses,
                fullscreen = change === "fullscreen" ? value : this.isFullscreen(),
                sidebyside = change === "sidebyside" ? value : this.isSidebyside(),
                manage = !previewCollapses || fullscreen || sidebyside;
            if (force || manage !== this._heightsManaged) {
                this._toggleHeightsManaged(manage, force);
            }
        },
        _wrapResizeListener: function() {
            var newWidth = this.wrap.width(),
                newHeight = this.wrap.height();
            if (newWidth !== this.lastWrapWidth) {
                this._checkToolbarOverflowedControls();
                this._adjustWidths(newWidth);
                this.lastWrapWidth = newWidth;
            }
            if (newHeight !== this.lastWrapHeight) {
                if (this._heightsManaged) {
                    this._adjustHeights(newHeight);
                } else {
                    var editorHeight = this.editor.height();
                    this.lastEditorPercentHeight = editorHeight / (editorHeight + this.preview.height());
                }
                this.lastWrapHeight = newHeight;
            }
        },
        // When the wrap height changes, this will resize the editor and the preview,
        // keeping the height ratio between them.
        _adjustHeights: function(wrapHeight) {
            // To avoid document reflow, we only set the values at the end.
            var sizes;
            if (this.isSidebyside()) {
                var deltaHeight = wrapHeight - this.lastWrapHeight;
                sizes = {
                    firstSize: this.editor.height() + deltaHeight,
                    lastSize: this.preview.height() + deltaHeight
                };
            } else {
                var isPreviewOpen = this.isPreviewOpen(),
                    editorHeight = this.editor.height(),
                    previewHeight = isPreviewOpen ? this.preview.height() : 0,
                    availableHeight = editorHeight + previewHeight + (wrapHeight - this.lastWrapHeight);
                sizes = splitSize(availableHeight, this.lastEditorPercentHeight, 15);
                if (!isPreviewOpen) {
                    // Keep the previewHeight for when the preview will slide down again.
                    // But allow editorHeight to take the whole available height:
                    sizes.firstSize = editorHeight + (wrapHeight - this.lastWrapHeight);
                }
            }
            this.editor.height(sizes.firstSize);
            this.preview.height(sizes.lastSize);

            return this;    // Chaining
        },
        _adjustWidths: function(wrapWidth) {
            if (this.isSidebyside()) {
                var sizes = splitSize(wrapWidth, this.lastEditorPercentWidth, 60);
                if (!this.isPreviewOpen()) {
                    sizes.firstSize += sizes.lastSize;
                }
                this.editorWrap.width(sizes.firstSize);
                this.previewWrap.width(sizes.lastSize);
                this.editorWrap[0].style.maxWidth = sizes.firstSize + "px";
                this.previewWrap[0].style.maxWidth = sizes.lastSize + "px";
            }

            return this;    // Chaining
        },
        // Call this to manage controls that are overflowing the toolbar
        // when its width changes:
        _checkToolbarOverflowedControls: function() {
            var controls = this.controls.children(),
                control = $(controls[0]),
                defaultTop = control.position().top,
                foundOverflowed = false;

            // First we look for overflowed controls:
            for (var i = controls.length - 1; i > 1; i--) {
                control = $(controls[i]);
                if (control.hasClass("overflowedControl")) {
                    continue;
                }
                else if (control.position().top <= defaultTop) {
                    break;
                }
                control.addClass("overflowedControl");
                foundOverflowed = true;
            }

            // If no new overflowed control was found,
            // then look for controls that are no more overflowed:
            if (!foundOverflowed) {
                for (; i < controls.length; i++) {
                    control = $(controls[i]);
                    if (!$(controls[i]).hasClass("overflowedControl")) {
                        continue;
                    }
                    // Test if it would overflow:
                    control.removeClass("overflowedControl");
                    if (control.position().top > defaultTop) {
                        control.addClass("overflowedControl");
                        break;
                    }
                }
            }

            return this;    // Chaining
        }
    });

    // THE $(...).meltdown() function:
    // Inspired by: http://api.jqueryui.com/jQuery.widget/
    $.fn.meltdown = function (arg) {
        // Get method name and method arguments:
        var methodName = $.type(arg) === "string" ? arg : "_init",
            args = Array.prototype.slice.call(arguments, methodName === "_init" ? 0 : 1);

        // Dispatch method call:
        for (var elem, meltdown, returnValue, i = 0; i < this.length; i++) {
            elem = this[i];
            // Get the Meltdown object or create it:
            meltdown = $.data(elem, "Meltdown");
            if (methodName === "_init") {
                if (meltdown) continue; // Don't re-create it.
                meltdown = new Meltdown(elem);
                $.data(elem, "Meltdown", meltdown);
            }
            // Call the method:
            returnValue = meltdown[methodName].apply(meltdown, args);
            // If the method is a getter, return the value
            // (See: http://bililite.com/blog/2009/04/23/improving-jquery-ui-widget-getterssetters/)
            if (returnValue !== meltdown) {
                return returnValue;
            }
        }

        return this;    // Chaining
    };


    if (isIE8||true) {
        // Fixing the textarea deselection on click:
        // (http://stackoverflow.com/questions/3558939/javascript-get-selected-text-from-textarea-in-ie8)
        var oldBuildControls = buildControls;
        buildControls = function() {
            var ret = oldBuildControls.apply(this, arguments);
            ret.find("span").attr("unselectable", "on");
            return ret;
        };
    }

    if (isOldjQuery) {
        $.meltdown.controlDefs.sidebyside.styleClass = "disabled";
        $.meltdown.controlDefs.sidebyside.altText = "Disabled: requires jQuery 1.8+";
        Meltdown.prototype.toggleSidebyside = function() {
            debug("Requires jQuery 1.8+");
            return this;
        };
    }

}(jQuery, window, document));
