<?php
include '../libs/klein.php';
include '../libs/idiorm.php';

respond('GET', '/', function ($request, $response, $app) {
	$response->render('../views/index.phtml');
});

respond('GET', '/lists', function ($request, $response, $app) {
	$lists = ORM::for_table('lists')->where('active', 1)->find_many();
	$response->lists = $lists;
	$response->render('../views/lists.phtml');
});

respond('GET', '/edit/[i:id]', function ($request, $response, $app) {
	$list = ORM::for_table('lists')->find_one($request->id);
	$list->items = ORM::for_table('list_items')->where('list_id', $request->id)->find_many();
	$response->render( '../views/list.phtml', array('list' => $list, 'action' => 'edit') );
});

respond('GET', '/view/[i:id]', function ($request, $response, $app) {
	$list = ORM::for_table('lists')->find_one($request->id);
	$list->items = ORM::for_table('list_items')->where('list_id', $request->id)->find_many();
	$response->render( '../views/list.phtml', array('list' => $list, 'action' => 'view') );
});

respond('POST', '/ajax', function ($request, $response, $app) {
	$server_response = array('status' => 0);
	$action = $request->param('action');
	switch ($action) {
		case 'new_list':
			$newList = ORM::for_table('lists')->create();
			$title = $request->param('title');

			if ($title !== null) {
				$newList->title = $title;
				$newList->created_on = date('Y-m-d');
				$newList->save();
				$server_response['status'] = 1;
				$server_response['list'] = $newList->id();
			}
			break;

		case 'new_item':
			$newListItem = ORM::for_table('list_items')->create();
			$item = $request->param('item');
			if ($item !== null && trim($item)) {
				$newListItem->item = $item;
				$newListItem->list_id = $request->param('list_id');
				$newListItem->save();
				$server_response['status'] = 1;
				$server_response['item'] = $newListItem->id();
			}
			break;

		case 'finish_item':
			$finsihedListItem = ORM::for_table('list_items')->find_one($request->param('item'));
			$finsihedListItem->status = 1;
			$finsihedListItem->save();
			$server_response['status'] = 1;
			break;	

		case 'delete_item':
			$deletedListItem = ORM::for_table('list_items')->find_one($request->param('item'));
			$deletedListItem->delete();
			$server_response['status'] = 1;
			break;
		
		default:
			# code...
			break;
	}	
	echo json_encode($server_response);
});

dispatch();