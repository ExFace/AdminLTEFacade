$( document ).ready(function() {
	pinnedObjectsRefresh('#exf-pinned-list', '#exf-pinned-counter');
});

function pinnedObjectsRefresh(containerSelector, counterSelector){
	$(containerSelector).empty();
	$.post(
			pinnedObjectsBaseUrl() + "&action=exface.Core.ObjectBasketFetch", 
		function( data ) {
			pinnedObjectsMenu(data, containerSelector, counterSelector);
		},
		'json'
	);
}

function pinnedObjectsRemoveObject(objectId, containerSelector, counterSelector){
	$(containerSelector).empty();
	$.post(
		pinnedObjectsBaseUrl() + "&action=exface.Core.ObjectBasketRemove&object=" + objectId, 
		function( data ) {
			pinnedObjectsMenu(data, containerSelector, counterSelector);
		},
		'json'
	);
}

function pinnedObjectsBaseUrl(){
	return "exface/exface.php?exftpl=exface.AdminLteTemplate&resource="+getPageId();
}

function pinnedObjectsMenu(data, containerSelector, counterSelector){
	var total = 0;
	for (var i=0; i<data.length; i++){
		var rowObjCount = Object.keys(data[i]['instances']).length;
		total = total + rowObjCount;
		var btnRemove = '<a class="pull-left" href="javascript:pinnedObjectsRemoveObject(\'' + data[i]['object_id'] + '\',\'' + containerSelector + '\',\'' + counterSelector + '\');"><i class="fa fa-times" aria-hidden="true"></i></a>';
		var row = $('<li><span class="menu-actions pull-right">'+btnRemove+'</span><a href="#">' + rowObjCount + 'x ' + data[i]['object_name'] + '</a></li>');
		row.children('a').click({object: data[i]}, function(e){pinnedObjectsModalShow($('#pinned-modal'), e.data.object)});
		$(containerSelector).append( row );
	}
	$(counterSelector).text(total);
}

function pinnedObjectsModalShow(modalElement, data){
	modalElement.find('.modal-title').text(data.object_name);
	if (data.instances){
		var table = $('<table class="table table-bordered table-striped"><tbody></tbody></table>');
		var i = 0;
		for (var uid in data.instances){
			i++;
			table.children('tbody').append($('<tr><td><input type="checkbox" checked value="'+uid+'" class="row-selector" /></td><td>'+uid+'</td></tr>'));
		}
		modalElement.find('.modal-body').empty().append(table);
	}
	if (Array.isArray(data.object_actions)){
		footer = modalElement.find('.modal-footer');
		footer.empty().append($('<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>'));
		for (var i = 0; i<data.object_actions.length; i++){
			$('<button type="button" class="btn btn-default pull-left">'+data.object_actions[i]['name']+'</button>')
			.click({action: data.object_actions[i].alias}, function(e){
				var requestData = {};
				var requestAction = e.data.action;
				requestData.oId = data['object_id'];
				requestData.rows = [];
				modalElement.find('tbody input.row-selector:checked').each(function(index, elem){
					requestData.rows.push(data.instances[$(elem).val()]);
				});
				$.post(
					pinnedObjectsBaseUrl() + "&action=exface.Core.ObjectBasketCallAction&object=" + data['object_id'] + "&basketAction=" + requestAction + "&data=" + JSON.stringify(requestData), 
					function( data ) {
						modalElement.find('.modal-body').empty().append(data);
					}
				);
			})
			.appendTo(footer);
		}
	}
	modalElement.modal('show');
}

function getPageId(){
	return $("meta[name='page_id']").attr("content");
}

function adminlteCreateDialog(parentElement, id, title, content){
	var dialog = $(' \
		<div class="modal" id="'+id+'"> \
			<div class="modal-dialog"> \
				<div class="modal-content"> \
					<div class="modal-header"> \
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> \
						<h4 class="modal-title">'+title+'</h4> \
					</div> \
					<div class="modal-body"> \
						' + content + ' \
					</div> \
				</div><!-- /.modal-content --> \
			</div><!-- /.modal-dialog --> \
		</div><!-- /.modal -->').resize();
	parentElement.append(dialog);
	$('#'+id).modal('show');
}