<?php

	function createPagination($table_name, $_adjacents, $_limit, $_page, $_url){
		include('db.php');
		
		/*How many records you want to show in a single page.*/
		$limit = $_limit;
		
		/*How may adjacent page links should be shown on each side of the current page link.*/
		$adjacents = $_adjacents;
		
		/*Get total number of records */
		$sql = "SELECT COUNT(*) 'total_rows' FROM $table_name";
		$res = mysqli_fetch_object(mysqli_query($con, $sql));
		$total_rows = $res->total_rows;
		
		/*Get the total number of pages.*/
		$total_pages = ceil($total_rows / $limit);
				
		$page = $_page;
		$offset = $limit * ($page-1);
	

		$query  = "select * from `posts` limit $offset, $limit";
		$result = mysqli_query($con, $query);
		
		$resultData = [];
		if(mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_object($result)) {
				$resultData[] = $row;
			}
		}

		//Checking if the adjacent plus current page number is less than the total page number.
		//If small then page link start showing from page 1 to upto last page.
		if($total_pages <= (1+($adjacents * 2))) {
			$start = 1;
			$end   = $total_pages;
		} else {
			if(($page - $adjacents) > 1) {				   //Checking if the current page minus adjacent is greateer than one.
				if(($page + $adjacents) < $total_pages) {  //Checking if current page plus adjacents is less than total pages.
					$start = ($page - $adjacents);         //If true, then we will substract and add adjacent from and to the current page number  
					$end   = ($page + $adjacents);         //to get the range of the page numbers which will be display in the pagination.
				} else {								   //If current page plus adjacents is greater than total pages.
					$start = ($total_pages - (1+($adjacents*2)));  //then the page range will start from total pages minus 1+($adjacents*2)
					$end   = $total_pages;						   //and the end will be the last page number that is total pages number.
				}
			} else {									   //If the current page minus adjacent is less than one.
				$start = 1;                                //then start will be start from page number 1
				$end   = (1+($adjacents * 2));             //and end will be the (1+($adjacents * 2)).
			}
		}
		//If you want to display all page links in the pagination then
		//uncomment the following two lines
		//and comment out the whole if condition just above it.
		/*$start = 1;
		$end = $total_pages;*/
			
		// -- Pagination Bootstrap -- //
		if($total_pages > 1) { 
			$pagination = '';
			
			// Link of the first page
			$pagination .= "<ul class='pagination pagination-sm justify-content-center'>
								<li class='page-item " . ($page <= 1 ?  'disabled' : '') . "'>
									<a class='page-link' href=". $_url ."?page=1" . ">&lt;&lt;</a>
								</li>";
								
			// Link of the previous page
			$pagination .= "<li class='page-item ".($page <= 1 ? 'disabled' : '') ."'>
								<a class='page-link' href=". $_url ."?page=" . ($page>1 ? $page-1 : 1) .">&lt;</a>
							</li>";
			
			// Links of the pages with page number
			for($i=$start; $i<=$end; $i++) { 
				$pagination .= "<li class='page-item " . ($i == $page ? 'active' : '') ."'>
									<a class='page-link' href=". $_url ."?page=". $i .">". $i . "</a>
								</li>";
			}
			
			// Link of the next page
			$pagination .= "<li class='page-item ". ($page >= $total_pages ? 'disabled' : '') . "'>
								<a class='page-link' href=". $_url ."?page=" . ($page < $total_pages ? $page+1 : $total_pages). ">&gt;</a>
							</li>";
							
			// Link of the last page
			$pagination .= "<li class='page-item ". ($page >= $total_pages ? 'disabled' : '') . "'>
								<a class='page-link' href=". $_url ."?page=". $total_pages. ">&gt;&gt;</a>
							</li>
						</ul>";
			}
		// -- End of Pagination Bootstrap -- //
		
		mysqli_close($con);
		
		return [
			'resultData' => $resultData, 
			'pagination' => $pagination, 
		];
	}
	
	// Calling pagination function
	// function createPagination($table_name, $adjacents, $limit, $page, $url)
	
	$page = isset($_GET['page']) && $_GET['page'] != "" ? $_GET['page'] : 1;
	
	$hasil = createPagination('posts', 2, 3, $page, 'http://localhost/pagination/');
	
?>
	

<!-- DOCTYPE -->
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Bootstrap pagination in PHP and MySQL</title>
    <meta charset="utf-8">
    <!-- Viewport Meta Tag -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<script src="bootstrap/js/jquery.min.js"></script>
	<script src="bootstrap/js/bootstrap.min.js"></script>
  </head>
  <body>
  	
	<style>
	.post-title { font-size:20px; }
	.mtb-margin-top { margin-top: 20px; }
	.top-margin { border-bottom:2px solid #ccc; margin-bottom:20px; display:block; font-size:1.3rem; line-height:1.7rem;}
	</style>
	<div class="container-fluid mtb-margin-top">
		<div class="row">
			<div class="col-md-12">
				<h1 class="top-margin">Bootstrap pagination in PHP and MySQL</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<!-- //// Content space here //// -->
				<?php 
					foreach($hasil['resultData'] as $key => $value){
						echo '<h1 class="post-title"><a href="'.$value->link.'" target="_blank">'.$value->title.'</a></h1>';
						echo '<p>'.$value->content.'</p>';
					}
				?>
				<br>
				<?php
					echo $hasil['pagination'];
				?>
 			</div>
 		</div>
     </div> 
</body>
</html>
