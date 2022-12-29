<div class="manager-wrapper">
<h3 class="menuglava">[[lang/add_category]]</h3>
<form id="categoryList" action="load.php?id=imanager&category&category_edit" method="post" accept-charset="utf-8">
<div>
	<p><label for="page-url">[[lang/add_new_category]]:</label>
	<input class="text-fields-left text" id="im-catinput" type="text" name="new_category" value="" /></p>
	[[filter]]
	[[header]]
	<table id="im-catlist" class="highlight">
		<thead>
		<tr>
			<th>&nbsp;</th>
			<th>[[lang/position_table]]</th>
			<th>[[lang/name_table]]</th>
			<th>[[lang/items_table]]</th>
			<th>&nbsp;</th>
		</tr>
		</thead>
		<tbody id="im-catlist-body">
			[[value]]
		</tbody>
	</table>
	<div id="im-info-row" >
		<p>[[pagination]]</p>
	</div>
	<p class="im-buttonwrapper"><span><input class="submit" type="submit" name="category_edit" value="[[lang/add_category_submit]]" /></span></p>
</div>
</form>
</div>
<script>

	// positions
	var oldpos = new Array();
	var i=0;

	$.getList = function(num) {
		var ftr = $('#filterby').val();
		var opt = $('#option').val();

		var form = $('#categoryList');
		jsonObj = [];

		form.find('input.position').each(function(index) {
			var elem = $( this );
			var id = elem.attr('name');
			var position = elem.val();
			//console.log( index + ": " + elem.val() );
			//alert(position);

			item = {}
			item ['id'] = id;
			item ['position'] = position;
			jsonObj.push(item);
		});

		$.post("load.php?id=imanager&category&getcatlist="+num+"&filterby="+ftr+"&option="+opt,
				{ page: [[page]],positions: jsonObj },
				function(data, status) {
					if(status = 'success' && data) $('#im-catlist-body').html(data);
				});
	}

	$(document).ready(function() {
		$('select').on('change', function() {
			var num = $('.active').children().attr('id');
			$.getList(num);
			return false;
		});

		$('.switchNumber').click(function(){
			var num = $(this).attr('id');
			$('.active').removeClass('active');
			$(this).parent().addClass('active');
			$.getList(num);
			return false;
		});

		$('.im-pos').dblclick(function (e) {
			e.stopPropagation();
			var currentPosField = $(this).children('.position');
			var currentEle = $(this).children('.index');
			var value = currentEle.html();
			updateVal(currentEle, value, currentPosField);
		});

		$.indexPos = function() {
			$('#im-catlist tbody tr').each(function(i,tr) {
				$(tr).find('.position').each(function(k, elem) {
					oldpos[i] = $(elem).val();
					i++;
				});
			});
		}


		$('#im-catlist tbody tr').each(function(i,tr) {
			$.indexPos();
		});

		$('#im-catlist').sortable({
			items:"tr.sortable", handle:'td',
			update:function(e,ui) {
				$('#im-catlist tbody tr').each(function(i,tr) {
					$(tr).find('.position').each(function(k,elem) {
						if($(elem).val()  != oldpos[i]){
							$(elem).val(oldpos[i]);
							$(tr).find('.index').text(oldpos[i]);
							$(tr).find('.index').stop().css('color', '#000000').animate({ color: '#777777'}, 1500);
						}
					});
				});

				var num = $('.active').children().attr('id');
				$.getList(num);
				$.indexPos();
			}
		});
	});

	function updateVal(currentEle, value, currentPosField) {
		$(document).off('click');
		$(currentEle).html('<input class="dyn-pos" onkeypress="return event.keyCode != 13;" type="number" value="' + value + '" />');

		var el = $('.dyn-pos');
		var value = el.val();
		var num = $('.active').children().attr('id');

		if (value.length != 0) {
			el.selectionStart = value.length;
			el.selectionEnd = value.length;
			el.focus();
		}

		el.keyup(function (event) {
			if (event.keyCode == 13) {
				$(currentEle).html(el.val().trim());
				currentPosField.val(el.val().trim());
				$.getList(num);
			}
		});

		$(document).click(function () {
			$(currentEle).html(currentPosField.val());
			$(currentEle).stop().css('color', '#CF3805').animate({ color: '#777777'}, 1500);
		});
	}

	$(document).ajaxComplete(function(){
		$('.im-pos').dblclick(function (e) {
			e.stopPropagation();
			var currentPosField = $(this).children('.position');
			var currentEle = $(this).children('.index');
			var value = currentEle.html();
			updateVal(currentEle, value, currentPosField);
		});

		$('#im-catlist tbody tr').each(function(i,tr) {
			$(tr).find('.position').each(function(k, elem) {
				oldpos[i] = $(elem).val();
				i++;
			});
		});
	});

</script>