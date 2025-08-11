<?
print '
<form action="'.$page.'" method="post" name="frmSort">
<input type="hidden" name="by" value="">
<input type="hidden" name="fieldsort" value="">
<input type="hidden" name="sql" value="'.$part_sql.'">
<input type="hidden" name="sqlPaging" value="'.$sqlPaging.'">
<input type="hidden" name="pagenr" value="">
</form>
</body>
<script>
function sortby(sortby, fieldsort, pagenr) {
	//alert(sortby);
	//alert(fieldsort);
	//alert(pagenr);
	document.frmSort.fieldsort.value = fieldsort;
	document.frmSort.by.value = sortby;
	document.frmSort.pagenr.value = pagenr;
	document.frmSort.submit();
}
</script>';
?>