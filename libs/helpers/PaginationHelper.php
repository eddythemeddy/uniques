<?php

Class PaginationHelper {

	public function html($numpages) {

		$url = $_SERVER['REQUEST_URI'];
		$queryUrl = parse_url($url);

		if($numpages <= 1) {
			return '';
		}

        if (!isset($_GET['page'])){
        	$queryUrl['query'] = '1';
            $page = 1;
        } else {
            $page = $_GET['page'];
            if($page < 1) $page = 1;
        }

		$queries = parse_str($queryUrl['query'], $params);
		unset($params['page']);
		$html = '<ul>';
		$html .= '<li class="prev disabled">';
		$prevPage = $page - 1;
		$prevPageParams = $params;
		$prevPageParams['page'] = $prevPage;
		$prevPageUrl = http_build_query($prevPageParams);
		$prevPageUrl = '?' . rawurldecode($prevPageUrl);
		$prevPageUrl = 'href="' . $prevPageUrl . '"';
		if($page == 1) { $prevPageUrl = ''; }
		$html .= '<a ' . $prevPageUrl . '><</a></li>';
		
		for($x = 0; $x < $numpages; $x++): 
			$pageNum = $x + 1;
			$currParams = $params;
			$currParams['page'] = $pageNum;
			$currUrl = http_build_query($currParams);
			$currUrl = '?' . rawurldecode($currUrl);
			$class = '';
			if($page == $pageNum) { $class = ' class="active"';}
           	$html .= '<li'.$class.'><a href="'.$currUrl.'">' . $pageNum . '</a></li>';
        endfor;
		$html .= '<li class="next disabled">';

		$nextPage = $page + 1;
		$nextPageParams = $params;
		$nextPageParams['page'] = $nextPage;
		unset($nextPageParams[1]);
		$nextPageUrl = http_build_query($nextPageParams);
		$nextPageUrl = '?' . rawurldecode($nextPageUrl);
		$nextPageUrl = 'href="' . $nextPageUrl . '"';
		if($page == $numpages) { $nextPageUrl = ''; }
			$html .= '<a ' . $nextPageUrl . '>></a></li>';

		$html .= '</ul>';
		
		return $html;
	}
}