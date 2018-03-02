

Nestable
========

## We are writing a new readme! Till now, please continue read the source ;)

## PLEASE NOTE

~~I cannot provide any support or guidance beyond this README. If this code helps you that's great but I have no plans to develop Nestable beyond this demo (it's not a final product and has limited functionality). I cannot reply to any requests for help.~~.

**I'm picking up active developement for Nestable! Pull requests are welcome!**

* * *

### Drag & drop hierarchical list with mouse and touch compatibility (jQuery / Zepto plugin)

[**Try Nestable Demo**](http://dbushell.github.com/Nestable/)

Nestable is an experimental example and IS under active development. If it suits your requirements feel free to expand upon it!

## Usage

Write your nested HTML lists like so:
```html
<div class="dd">
    <ol class="dd-list">
        <li class="dd-item" data-id="1">
            <div class="dd-handle">Item 1</div>
        </li>
        <li class="dd-item" data-id="2">
            <div class="dd-handle">Item 2</div>
        </li>
        <li class="dd-item" data-id="3">
            <div class="dd-handle">Item 3</div>
            <ol class="dd-list">
                <li class="dd-item" data-id="4">
                    <div class="dd-handle">Item 4</div>
                </li>
                <li class="dd-item" data-id="5" data-foo="bar">
                    <div class="dd-handle">Item 5</div>
                </li>
            </ol>
        </li>
    </ol>
</div>
```
Then activate with jQuery like so:
```js
$('.dd').nestable({ /* config options */ });
```

### Events
`change`: For using an .on handler in jquery

The `callback` provided as an option is fired when elements are reordered or nested.
```js
$('.dd').nestable({
    callback: function(l,e){
        // l is the main container
        // e is the element that was moved
    }
});
```
### Methods

You can get a serialised object with all `data-*` attributes for each item.
```js
$('.dd').nestable('serialize');
```
The serialised JSON for the example above would be:
```json
[{"id":1},{"id":2},{"id":3,"children":[{"id":4},{"id":5,"foo":"bar"}]}]
```

### On the fly nestable generation

You can passed serialized JSON as an option if you like to dynamically generate a Nestable list:
```html
<div class="dd" id="nestable-json"></div>

<script>
var json = '[{"id":1},{"id":2},{"id":3,"children":[{"id":4},{"id":5,"foo":"bar"}]}]';
var options = {'json': json }
$('#nestable-json').nestable(options);
</script>
```
NOTE: serialized JSON has been expanded so that an optional "content" property can be passed which allows for arbitrary custom content (including HTML) to be placed in the Nestable item

Or do it yourself the old-fashioned way:
```html
<div class="dd" id="nestable3">
    <ol class='dd-list dd3-list'>
        <div id="dd-empty-placeholder"></div>
    </ol>
</div>

<script>
$(document).ready(function(){
    var obj = '[{"id":1},{"id":2},{"id":3,"children":[{"id":4},{"id":5}]}]';
    var output = '';
    function buildItem(item) {

        var html = "<li class='dd-item' data-id='" + item.id + "'>";
        html += "<div class='dd-handle'>" + item.id + "</div>";

        if (item.children) {

            html += "<ol class='dd-list'>";
            $.each(item.children, function (index, sub) {
                html += buildItem(sub);
            });
            html += "</ol>";

        }

        html += "</li>";

        return html;
    }

    $.each(JSON.parse(obj), function (index, item) {

        output += buildItem(item);

    });

    $('#dd-empty-placeholder').html(output);
    $('#nestable3').nestable();
});
</script>
```

### Configuration

You can change the follow options:

* `maxDepth` number of levels an item can be nested (default `5`)
* `group` group ID to allow dragging between lists (default `0`)
* `callback` callback function when an element has been changed (default `null`)

These advanced config options are also available:

* `contentCallback` The callback for customizing content (default `function(item) {return item.content || '' ? item.content : item.id;}`)
* `listNodeName` The HTML element to create for lists (default `'ol'`)
* `itemNodeName` The HTML element to create for list items (default `'li'`)
* `rootClass` The class of the root element `.nestable()` was used on (default `'dd'`)
* `listClass` The class of all list elements (default `'dd-list'`)
* `itemClass` The class of all list item elements (default `'dd-item'`)
* `dragClass` The class applied to the list element that is being dragged (default `'dd-dragel'`)
* `noDragClass` The class applied to an element to prevent dragging (default `'dd-nodrag'`)
* `handleClass` The class of the content element inside each list item (default `'dd-handle'`)
* `collapsedClass` The class applied to lists that have been collapsed (default `'dd-collapsed'`)
* `noChildrenClass` The class applied to items that cannot have children (default `'dd-nochildren'`)
* `placeClass` The class of the placeholder element (default `'dd-placeholder'`)
* `emptyClass` The class used for empty list placeholder elements (default `'dd-empty'`)
* `expandBtnHTML` The HTML text used to generate a list item expand button (default `'<button data-action="expand">Expand></button>'`)
* `collapseBtnHTML` The HTML text used to generate a list item collapse button (default `'<button data-action="collapse">Collapse</button>'`)
* `includeContent` Enable or disable the content in output (default `false`)
* `listRenderer` The callback for customizing final list output (default `function(children, options) { ... }` - see defaults in code)
* `itemRenderer` The callback for customizing final item output (default `function(item_attrs, content, children, options) { ... }` - see defaults in code)
* `json` JSON string used to dynamically generate a Nestable list. This is the same format as the `serialize()` output

**Inspect the [Nestable Demo](http://ramonsmit.github.io/Nestable/) for guidance.**

## Change Log

### 6th October 2014

* [zemistr] Created listRenderer and itemRenderer. Refactored build from JSON.
* [zemistr] Added support for adding classes via input data. (```[{"id": 1, "content": "First item", "classes": ["dd-nochildren", "dd-nodrag", ...] }, ... ]```)

### 3th October 2014

* [zemistr] Added support for additional data parameters.
* [zemistr] Added callback for customizing content.
* [zemistr] Added parameter "includeContent" for including / excluding content from the output data.
* [zemistr] Added fix for input data. (JSON string / Javascript object)

### 7th April 2014

* New pickup of repo for developement.

### 14th March 2013

* [tchapi] Merge Craig Sansam' branch [https://github.com/craigsansam/Nestable/](https://github.com/craigsansam/Nestable/) - Add the noChildrenClass option

### 13th March 2013

* [tchapi] Replace previous `change` behaviour with a callback

### 12th February 2013

* Merge fix from [jails] : Fix change event triggered twice.

### 3rd December 2012

* [dbushell] add no-drag class for handle contents
* [dbushell] use `el.closest` instead of `el.parents`
* [dbushell] fix scroll offset on document.elementFromPoint()

### 15th October 2012

* Merge for Zepto.js support
* Merge fix for remove/detach items

### 27th June 2012

* Added `maxDepth` option (default to 5)
* Added empty placeholder
* Updated CSS class structure with options for `listClass` and `itemClass`.
* Fixed to allow drag and drop between multiple Nestable instances (off by default).
* Added `group` option to enabled the above.

* * *

Original Author: David Bushell [http://dbushell.com](http://dbushell.com/) [@dbushell](http://twitter.com/dbushell/)

New Author     : Ramon Smit    [http://ramonsmit.nl](http://ramonsmit.nl) [@Ram0nSm1t](https://twitter.com/Ram0nSm1t/)

Contributors :

* Cyril [http://tchap.me](http://tchap.me), Craig Sansam
* Zemistr [http://zemistr.eu](http://zemistr.eu), Martin Zeman

Copyright © 2012 David Bushell / © Ramon Smit 2014 | BSD & MIT license
