<?
	//if ($pagenr<3)
	$i = $pagenr-4;
	if ($i<=0)
		$i=1;
		
	$pageDisplay = 9;
	$limit = $i+$pageDisplay;
	if ($limit>=$num_pages)
		$limit = $num_pages;
	$nextPage = $pagenr+1;
	$prevPage = $pagenr-1;
	
	print '<a href="#" onClick="sortby(\''.$by.'\',\''.$fieldsort.'\',\'1\')"><<</a>&nbsp;';
	
	if ($prevPage<1) 
	echo " ";
	else
	print '<a href="#" onClick="sortby(\''.$by.'\',\''.$fieldsort.'\',\''.$prevPage.'\')"><</a>&nbsp;';
	
	while ($i < $limit+1) {
	if ($pagenr==$i)
	echo "<strong>".$i."</strong>&nbsp;";
	else
	print '<a href="#" onClick="sortby(\''.$by.'\',\''.$fieldsort.'\',\''.$i.'\')">'.$i.'</a>&nbsp;';
	$i = $i + 1;
	}
	
	if ($nextPage>$num_pages)
	echo " ";
	else
	print '<a href="#" onClick="sortby(\''.$by.'\',\''.$fieldsort.'\',\''.$nextPage.'\')">></a>&nbsp;';
	
	print '<a href="#" onClick="sortby(\''.$by.'\',\''.$fieldsort.'\',\''.$num_pages.'\')">>></a>&nbsp;';

     ?>
