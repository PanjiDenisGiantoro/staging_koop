<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	member.php
 *          Date 		: 	
 *********************************************************************************/
include("header.php");

$sSQL = "";
$sWhere = " a.userID = b.userID AND b.status <> 0";

if ($ID) {
  $sWhere .= " AND b.userID = " . tosql($ID, "Text");
}
$sWhere = " WHERE (" . $sWhere . ")";
$sSQL = "SELECT DISTINCT a.*, b.* FROM users a, userdetails b";
$sSQL = $sSQL . $sWhere . " order by CAST( b.memberID AS SIGNED INTEGER ) desc";
$GetMember = &$conn->Execute($sSQL);
$GetMember->Move($StartRec - 1);

$totalFees = number_format(getFees($GetMember->fields(userID), $yr), 2);
$totalSharesTK = number_format(getSharesterkini($GetMember->fields(userID), $yr), 2);
$totalDepo = number_format(getDepoKhasAll($GetMember->fields(userID), $yr), 2);

print '<tr valign="top">
<td valign="top">
<div class="card-group">
  <div class="card bg-soft-info">
    <center><img class="card-img-top mt-3" style="width: 3rem; height: 3rem;" src="images/fee.png" alt="Picture is missing"></center>
    <div class="card-body">
      <h5 class="card-title" align="center">YURAN</h5>
      <h2 class="card-text" align="center"><font color="black">RM&nbsp;' . $totalFees . '</font></h2>
      </div>
  </div>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <!--div class="card bg-soft-warning">
  <center><img class="card-img-top mt-3" style="width: 3rem; height: 3rem;" src="images/saving.png" alt="Picture is missing"></center>
    <div class="card-body">
      <h5 class="card-title" align="center">SIMPANAN</h5>
      <h2 class="card-text" align="center"><font color="black">RM&nbsp;' . $totalDepo . '</font></h2>
      </div>
  </div-->
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <div class="card bg-soft-primary">
    <center><img class="card-img-top mt-3" style="width: 3rem; height: 3rem;" src="images/share.png" alt="Picture is missing"></center>
    <div class="card-body">
      <h5 class="card-title" align="center">SYER</h5>
      <h2 class="card-text" align="center"><font color="black">RM&nbsp;' . $totalSharesTK . '</font></h2>
      </div>
  </div>
</div>
</td></tr>';
