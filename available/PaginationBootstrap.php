<?php
return function ($template, $context, $args, $source) {
	ob_start();
	$pagination = $context->get('pagination');
	$metadata = $context->get('metadata');
	$baseUrl = '/' . $metadata['collection'] . '/' . $metadata['method'] . '/' . $pagination['limit'] . '/';
	//if ($pagination['pageCount'] == 1) {
	//	return '';
	//}
	$startPage = $pagination['page'] - 4;
	$endPage = $pagination['pageCount'] + 4;

	if ($startPage <= 0) {
    	$endPage -= ($startPage - 1);
    	$startPage = 1;
	}
	if ($endPage > $pagination['pageCount']) {
    	$endPage = $pagination['pageCount'];
	}

	echo '
		<div class="pagination">
			<ul>';
	if ($startPage > 1) {
		echo '
				<li>
					<a href="', $baseUrl, ($pagination['page'] - 1), '">&laquo;</a>
				</lii>';
	}

	for ($i = $startPage; $i <= $endPage; $i++) {
		$active = '';
		if ($i == $pagination['page']) {
			$active = ' active';
		}
		echo '
				<li>
					<a href="', $baseUrl, $i, '" class="', $active, '">', $i, '</a>
				</li>';
	}
	if ($endPage < $pagination['pageCount']) {
		echo '
				<li>
					<a href="', $baseUrl, ($pagination['page'] - 1), '">&raquo;</a>
				</li>';
	}
	echo '
			</ul>
		</div>';

	$buffer = ob_get_clean();
	return $buffer;
};