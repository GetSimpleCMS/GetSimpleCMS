<div class="manager-wrapper">
	<h3 class="menuglava" >[[lang/items_header]]</h3>
	[[catselector]]
	[[itemfilter]]
	<div class="highlight">
		<form method="post" id="itemList">
		<table id="im-itemlist-table" class="highlight">
			<thead>
				<tr>
					<th>&nbsp;</th>
					<th>[[lang/position_table]]</th>
					<th>[[lang/name_table]]</th>
					<th>[[lang/created_table]]</th>
					<th>[[lang/updated_table]]</th>
					<th>[[lang/active_table]]</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="im-itemlist-body">
				[[content]]
			</tbody>
		</table>
		</form>
	</div>
	<div id="im-info-row" >
		<p>[[pagination]]</p>
		<p>[[category]]: <strong>[[count]]</strong><span>[[lang/items]]</span></p>
	</div>
	<script>

		// positions
		var oldpos = new Array();
		var i=0;

		$(document).ready(function() {

			$('#filterarea select').on('change', function() {
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

			$('#filtervalue').blur(function() {
				var num = $('.active').children().attr('id');
				$.getList(num);
				return false;
			});


			$('#filtervalue').keypress(function (e) {
				if(e.which == 13) {
					var num = $('.active').children().attr('id');
					$.getList(num);
					return false;
				}
			});

			$('.im-pos').dblclick(function (e) {
				e.stopPropagation();
				var currentPosField = $(this).children('.position');
				var currentEle = $(this).children('.index');
				var value = currentEle.html();
				updateVal(currentEle, value, currentPosField);
			});



			$.indexPos = function() {
				$('#im-itemlist-table tbody tr').each(function(i,tr) {
					$(tr).find('.position').each(function(k, elem) {
						oldpos[i] = $(elem).val();
						i++;
					});
				});
			}


			$('#im-itemlist-table tbody tr').each(function(i,tr) {
				$.indexPos();
			});

			$('#im-itemlist-table').sortable({
				items:"tr.sortable", handle:'td',
				update:function(e,ui) {
					$('#im-itemlist-table tbody tr').each(function(i,tr) {
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

			$.getList = function(num) {
				var ftr = $('#filterby').val();
				var opt = $('#option').val();
				var ordf = $('#filterbyfield').val();
				var flr = $('#filter').val();
				var flrv = $('#filtervalue').val();

				var $form = $('#itemList');
				jsonObj = [];

				$form.find('input.position').each(function() {
					var id = $(this).attr('name');
					var position = $(this).val();
					item = {}
					item ['id'] = id;
					item ['position'] = position;
					jsonObj.push(item);
				});

				$.post("load.php?id=imanager&getitemlist="+num+"&filterby="+ftr+"&option="+opt+"&filterbyfield="+ordf+"&filter="+flr+"&filtervalue="+flrv,
						{ page: [[page]],positions: jsonObj },
						function(data, status){
							if(status = 'success' && data) $('#im-itemlist-body').html(data);
						});
			}

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
				//currentPosField.val(el.val().trim());
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

			$('#im-itemlist-table tbody tr').each(function(i,tr) {
				$(tr).find('.position').each(function(k, elem) {
					oldpos[i] = $(elem).val();
					i++;
				});
			});
		});

	</script>
</div>
