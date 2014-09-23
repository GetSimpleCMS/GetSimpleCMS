// in development
// getsimple tree expansion
// written for editpages, but can probably be expanded to support any
// list of elements with depth data in proper parent child order
//
var treeprefix               = 'tree-';
var treeparentclass          = treeprefix + 'parent';             // class for expander handles
var treeindentclass          = treeprefix + 'indent';             // class for expander handles
var treeexpanderclass        = treeprefix + 'expander';           // class for expander handles
var treeexpandedclass        = treeprefix + 'expander-expanded';  // class for expander when expanded
var treecollapsedclass       = treeprefix + 'expander-collapsed'; // class for expanded when collapsed
var nodecollapsedclass       = treeprefix + 'collapsed';          // class to control node visibility and data flag as collapsed
var nodeparentcollapsedclass = treeprefix + 'parentcollapsed';    // class to control children visibility while retaining collapse data
var depthprefix              = 'depth-';                          // class prefix for depth information
var datadepthattr            = 'depth';                           // data attribute name for depth information


function toggleRow(){
	var row = $(this).closest('.'+treeparentclass);
	// Debugger.log("toggle row " + $(row));

	var depth = getNodeDepth(row);
	if(depth < 0 ) return toggleTopAncestors(); // special handler to collapse all top level

	if($(row).hasClass(nodecollapsedclass)) expandRow(row);
	else collapseRow(row);
	$("table.striped").zebraStripe();	
}

function setExpander(elem){
	var expander = $(elem).find('.'+treeexpanderclass);
	$(expander).toggleClass(treecollapsedclass,$(elem).hasClass(nodecollapsedclass));
	$(expander).toggleClass(treeexpandedclass,!$(elem).hasClass(nodecollapsedclass));
}

function collapseRow(elem){
	$(elem).addClass(nodecollapsedclass);
	hideChildRows(elem);
	setExpander(elem);
}

function expandRow(elem){
	$(elem).removeClass(nodecollapsedclass);
	showChildRows(elem);
	setExpander(elem);
}

function hideChildRows(elem){
	var children = getChildrenByDepth(elem);
	children.each(function(i,elem){
		hideChildRow(elem);
	});
}

function hideChildRow(elem){
	$(elem).addClass(nodeparentcollapsedclass);
	// $(elem).animate({opacity: 0.1} , 100, function(){ $(this).addClass(nodeparentcollapsedclass);} ); // @todo custom callout
}

// not using recursion or tree walking here, this is likely faster
// obtains children by getting all siblings up until the first sibling of equal depth
// retains collapse states on parents
function showChildRows(elem){
	var children = getChildrenByDepth(elem);
	var startDepth = getNodeDepth(elem);
	children.each(function(i,elem){
		thisDepth   = getNodeDepth(elem);
		immediateChild = thisDepth == (startDepth + 1);

		// if immediate child just show it
		if(immediateChild){
			showChildRow(elem);
			return true;
		}

		// get actual parent of this child
		thisParent = getParentByDepth(elem);
		// if(!thisParent[0]) Debugger.log('parent not found');
		parentCollapsed = $(thisParent).hasClass(nodecollapsedclass);
		parentHidden    = $(thisParent).hasClass(nodeparentcollapsedclass);
		// Debugger.log(elem.id + ' | ' + $(thisParent).attr('id') + ' | ' + parentCollapsed + ' | ' + parentHidden);

		// show child only if parent is not hidden AND parent is not collapsed 
		if(!parentHidden && !parentCollapsed){
			showChildRow(elem);
		}

	});
}

function showChildRow(elem){
	$(elem).removeClass(nodeparentcollapsedclass);
	// $(elem).animate({opacity: 1}, 300); // todo: custom callout
}

function getNodeDepth(elem){
	return parseInt($(elem).data(datadepthattr),10);
}

function getChildrenByDepth(elem){
	// children are all nodes until nextsibling of equal OR LOWER depth
	var nextsibling = getNextSiblingByDepth(elem);
	var children    = elem.nextUntil(nextsibling);
	return children;
}

/**
 * get the first previous parentclass with lower depth
 */
function getParentByDepth(elem){
	var depth = getNodeDepth(elem) - 1;
	return $(elem).prevAll("."+treeparentclass+"[data-"+datadepthattr+"='" + (depth) + "']").first();
}

/**
 * get the next parent of equal or less depth
 */
function getNextSiblingByDepth(elem){
	var tagname      = getTagName(elem);
	var depth        = getNodeDepth(elem);
	var nextsiblings = elem.nextAll(tagname).filter(function(index){
								return getNodeDepth(this) <= depth;
							});
	return nextsiblings.first();
}

function addExpanders(elems,expander){
	if(expander === undefined) expander = '<span class="'+ treeexpanderclass + ' ' + treeexpandedclass +'"></span>';
	$(elems).each(function(i,elem){
		// Debugger.log($(elem));
		$(elem).removeClass("tree-indent").removeClass("indent-last").html(''); // remove any indentation here, now an expander
		$(expander).on('click',toggleRow).bind('selectstart dragstart', function(evt)
								{ evt.preventDefault(); return false; }).prependTo($(elem));
	});
}

function addIndents(elems){
	$(elems).each(function(i,elem){
		$('<span class="'+treeindentclass+'"></span>').prependTo($(elem));
	});
}

function toggleTopAncestors(){
	// @todo possible optimizations
	// could use a table css rule to hide all trs, unless classes depth-0 or something with specificty to override
	// would skip all iterations needed here, but would also require special css to toggle expanders
	// could also use a cache table for these using tr ids
	var rootcollapsed = $("#roottoggle").hasClass("rootcollapsed");

	// if(rootcollapsed) console.profile('expand all');
	// else console.profile('collapse all');

	// toggle label text
	var langstr = !rootcollapsed ? i18n('EXPAND_TOP') : i18n('COLLAPSE_TOP');
	$('#roottoggle .label').html(langstr);

	// hide all depth 0 children, do not change collpase data
	var depth = 0;
	$("#editpages tr[data-depth='" + depth + "']").each(function(i,elem){
					if(rootcollapsed) expandRow($(elem));
					else collapseRow($(elem));
	});

	$("#roottoggle").toggleClass("rootcollapsed");
	$('#roottoggle').toggleClass(nodecollapsedclass,!rootcollapsed);
	setExpander($('#roottoggle'));
	$("table.striped").zebraStripe();
	
	// console.profileEnd();
}


// add tree to editpages table
function addExpanderTableHeader(elem,expander,colspan){
	// Debugger.log($(elem));
	var rootcollapsed = $("#roottoggle").hasClass("rootcollapsed");
	var langstr = rootcollapsed ? i18n('EXPAND_TOP') : i18n('COLLAPSE_TOP');

	$('<tr id="roottoggle" class="tree-roottoggle nohighlight" data-depth="-1"><td colspan="'+colspan+'">'+expander+'<span class="label">'+ langstr +'</span></td></tr>').insertAfter(elem);
	// init expander
	$('#roottoggle').toggleClass("collapsed",rootcollapsed);
	setExpander($('#roottoggle'));
	$('#roottoggle .'+treeexpanderclass).on('click',toggleTopAncestors).bind('selectstart dragstart', function(evt)
								{ evt.preventDefault(); return false; });
	$('#roottoggle .label').on('click',toggleTopAncestors).bind('selectstart dragstart', function(evt)
								{ evt.preventDefault(); return false; });
}

$.fn.zebraStripe = function(){
	$("tbody tr:not(.tree-parentcollapsed)",$(this)).each(function(i,elem){
		if(i%2!=1) $(elem).addClass('odd').removeClass('even'); 
		else $(elem).addClass('even').removeClass('odd');
	});
};

/**
 * addTabletree
 * 
 * add gstree to tree ready table with data-depths and parent,indent classes
 * 
 * @param int minrows minumum rows needed to apply tree, else will skip tree creation
 * @param int mindepth minimum depth required to apply tree, else wil skip
 * @param int headerdepth minimum depth required to add the header expander controls
 */
$.fn.addTableTree = function(minrows,mindepth,headerdepth){
	// console.profile();
	// @todo for slide animations, temporarily insert tbody at start end of collapse range and animate it, use display:block on tbody

	// minrows      = 10;
	// mindepth     = 3;
	// headerdepth  = 3;
        
	var elem = this;
	if(!elem[0]){
		Debugger.log("gstree: table does not exist, skipping");
		return;
	}	

	// table is small if table has less rows that minrows
	var small = minrows !== undefined && $("tbody tr",elem).length < minrows;

	// table is shallow if table has depths less than mindepth
	var shallow = $("tbody tr[data-depth="+mindepth+"]",elem).length <= 0;

	// skip if no children
	if(!$("."+treeparentclass,elem)[0] || small || shallow){
		if(!small || !shallow) Debugger.log("gstree: insufficient depth, skipping");
		else Debugger.log("gstree: table too small, skipping");
		return;
	}

	// custom overrides for fontawesome icons for expander and collapse classes
	treeexpandedclass = 'fa-rotate-90';
	treecollapsedclass = '';
	var customexpander = '<i class="'+treeexpanderclass+' '+treeexpandedclass+' fa fa-play fa-fw"></i>';

	addIndents($('tr td:first-child',elem)); // preload indents for roots and childless
	addExpanders($('tr.tree-parent td:first-child .tree-indent:last-of-type',elem),customexpander); // add expanders to last indent
	$('tr td:first-child .tree-indent:last-of-type').html(''); // remove extra indentation
	
	// add the header expander controls
	var deep = headerdepth === undefined || $("tbody tr[data-depth="+headerdepth+"]",elem).length > 0;	
	if(deep) addExpanderTableHeader($('thead > tr:first',elem),customexpander,4);	
	
	$("table.striped").zebraStripe();

	// console.profileEnd();
};
