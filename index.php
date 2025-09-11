<?php
@ob_start();
session_start();
// $useragent=$_SERVER['HTTP_USER_AGENT'];
/*********************************************************************************
 *          Project      :   iKOOP.com.my
 ********************************************************************************/
include("common.php");


if (@$_REQUEST['vw'] == '') {
    $_REQUEST['vw'] = "main";
}


$coopName = dlookup("setup", "name", "setupID=" . tosql(1, "Text"));
print '
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
';

print '
<meta name="Keywords"  content="">
<meta name="Description" content="">
<meta name="GENERATOR" content="">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0"> 
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="assets/images/favicon.png">

<link href="assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
<link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />        
<link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />        
<link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
<link href="assets/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" />
        
</head>
'; ?>

<body data-topbar="colored">

    <!-- Begin page -->
    <div id="layout-wrapper">

        <header id="page-topbar" style="background-color: #ffffff; box-shadow: 0 1px 6px rgba(0, 0, 0, 0.1);">
            <div class="navbar-header">
                <div class="d-flex">

                    <!-- LOGO -->
                    <div class="navbar-brand-box" style="background-color: #2b3a4a;">
                        <a href="index.php" class="logo logo-dark">
                            <span class="logo-sm">
                                <img src="assets/images/logo-sm-dark.png" alt="" height="30">
                            </span>
                            <span class="logo-lg">
                                <img src="assets/images/logo-dark.png" alt="" height="45">
                            </span>
                        </a>

                        <a href="index.php" class="logo logo-light">
                            <span class="logo-sm">
                                <img src="assets/images/logo-sm-light.png" alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="assets/images/logo-light.png" alt="" height="24">
                            </span>
                        </a>
                    </div>

                    <!-- Menu Icon -->
                    <?php
                    if (!preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
                    ?>
                        <button type="button" class="btn px-3 font-size-24 header-item waves-effect" id="vertical-menu-btn">
                            <i class="mdi mdi-menu" style="color: black;"></i>
                        </button>
                    <?php } ?>
                </div>

                <div class="d-flex">

                    <?php
                    if (get_session("Cookie_userID") <> "") {
                    ?>
                        <!-- User -->
                        <button class="dropdown-item" onClick="window.location.href='index.php?page=main'" style="background-color:#ffffff;">
                            <i class="mdi mdi-bell"></i> | Notis
                        </button>
                        
                        <button class="dropdown-item" onClick="window.location.href='index.php?page=main'" style="background-color:#ffffff;">
                            <i class="fa fa-home"></i>
                            <?php
                            if (!preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) { ?>| Utama<?php } ?></button>
                        <a class="dropdown-item" href="?vw=logout" onclick="return confirm('Are you sure?')" style="background-color:#ffffff;">

                            <?php
                            if (!preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) { ?>
                                <i class="mdi mdi-logout"><!--i class="mdi mdi-power font-size-16 align-middle me-2 text-white"></i--></i> | Log Keluar <?php } else { ?><i class="mdi mdi-window-close"></i><?php } ?></a>
                        <!--button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded-circle header-profile-user" src="assets/images/users/avatar-4.jpg"
                                alt="Header Avatar">
                        </button-->

                        <!--div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item text-primary" href="?vw=logout" onclick="return confirm('Are you sure?')"><i
                                    class="mdi mdi-power font-size-16 align-middle me-2 text-primary"></i>
                                <span>Logout</span></a>
                        </div-->
                </div>
            <?php
                    }
            ?>

            </div>
    </div>
    </header>

    <!-- ========== Left Sidebar Start ========== -->
    <div class="vertical-menu" style="background-color: #2b3a4a;">

        <div data-simplebar class="h-100">
            <div class="user-details">
                <div class="d-flex">
                    <div class="me-2">
                        <img src="assets/images/users/avatar-4.jpg" alt="" class="avatar-md rounded-circle">
                    </div>
                    <div class="user-info w-100">
                        <div class="dropdown">
                            <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Donald Johnson
                                <i class="mdi mdi-chevron-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="javascript:void(0)" class="dropdown-item"><i class="mdi mdi-account-circle text-muted me-2"></i>
                                        Profile<div class="ripple-wrapper me-2"></div>
                                    </a></li>
                                <li><a href="javascript:void(0)" class="dropdown-item"><i class="mdi mdi-cog text-muted me-2"></i>
                                        Settings</a></li>
                                <li><a href="javascript:void(0)" class="dropdown-item"><i class="mdi mdi-lock-open-outline text-muted me-2"></i>
                                        Lock screen</a></li>
                                <li><a href="javascript:void(0)" class="dropdown-item"><i class="mdi mdi-power text-muted me-2"></i>
                                        Logout</a></li>
                            </ul>
                        </div>

                        <p class="text-white-50 m-0">Administrator</p>
                    </div>
                </div>
            </div>


            <!--- Sidemenu -->
            <div id="sidebar-menu">

                <!-- Left Menu Start -->
                <ul class="metismenu list-unstyled" id="side-menu">
                    <?php include("leftpanel-new.php"); ?>
                </ul>
            </div>
            <!-- Sidebar -->
        </div>
    </div>
    <!-- Left Sidebar End -->

    <?php
    if (!isset($pic)) $pic = dlookup("userdetails", "picture", "userID=" . tosql(get_session("Cookie_userID"), "Number"));
    if (@$_REQUEST['vw'] != '') { ?>
        <!-- ============================================================== -->
        <!-- Start right Content here -->

        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-flex align-items-center justify-content-between" style="background-image: none; box-shadow: none;">
                                <tr>
                                    <td>
                                        <div class="page-title">
                                            <h4 class="mb-0 font-size-18" style="color: black;"><? print $coopName; ?></h4>
                                            <ol class="breadcrumb">
                                                <li class="breadcrumb-item active" style="color: black;"><?php if (get_session("Cookie_userID") <> "") {
                                                                                                                echo get_session("Cookie_fullName");

                                                                                                                // if (get_session("Cookie_groupID") <> 2 && get_session("Cookie_groupID") <> 1) {
                                                                                                                //     if (!empty($pic)) {
                                                                                                                //         echo ' <span class="badge badge-soft-primary">Verified</span>';
                                                                                                                //     } else {
                                                                                                                //         echo ' <span class="badge badge-soft-danger">Not Verified</span>';
                                                                                                                //     }
                                                                                                                // }
                                                                                                            } else {
                                                                                                                echo "";
                                                                                                            } ?></li>
                                            </ol>
                                        </div>
                                    </td>
                                    <!-- <td>
                                                    <img src="money.png" alt="..." width="100px" height="100px">
                                                </td> -->
                                </tr>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="page-content-wrapper">

                        <?php if (@$_GET['mbx'] != '') { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <?php if (@$_GET['mbx'] == 1) { ?>
                                                <div class="row">
                                                    <div class="col-xl-3 col-sm-6">
                                                        <div class="card mini-stat bg-primary">
                                                            <div class="card-body mini-stat-img">
                                                                <div class="mini-stat-icon">
                                                                    <i class="mdi mdi-cube-outline float-end"></i>
                                                                </div>
                                                                <div class="text-white">
                                                                    <h6 class="text-uppercase mb-3 font-size-16 "><a class="text-white" href="?vw=main&mn=4">Laman Utama/buletin</a></h6>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-3 col-sm-6">
                                                        <div class="card mini-stat bg-primary">
                                                            <div class="card-body mini-stat-img">
                                                                <div class="mini-stat-icon">
                                                                    <i class="mdi mdi-buffer float-end"></i>
                                                                </div>
                                                                <div class="text-white">
                                                                    <h6 class="text-uppercase mb-3 font-size-16 "><a class="text-white" href="?vw=profile&mn=4">Tukar Katalaluan</a></h6>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-3 col-sm-6">
                                                        <div class="card mini-stat bg-primary">
                                                            <div class="card-body mini-stat-img">
                                                                <div class="mini-stat-icon">
                                                                    <i class="mdi mdi-tag-text-outline float-end"></i>
                                                                </div>
                                                                <div class="text-white">
                                                                    <h6 class="text-uppercase mb-3 font-size-16 "><a class="text-white" href="?vw=memberUpdate&mn=4">Kemaskini Profil</a></h6>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-3 col-sm-6">
                                                        <div class="card mini-stat bg-primary">
                                                            <div class="card-body mini-stat-img">
                                                                <div class="mini-stat-icon">
                                                                    <i class="mdi mdi-tag-text-outline float-end"></i>
                                                                </div>
                                                                <div class="text-white">
                                                                    <h6 class="text-uppercase mb-3 font-size-16 "><a class="text-white" href="?vw=manual&mn=4">Manual Bantuan</a></h6>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <!-- end row -->
                                            <?php } elseif (@$_GET['mbx'] == 2) { ?>
                                                <div class="row">
                                                    <div class="col-xl-3 col-sm-6">
                                                        <div class="card mini-stat bg-primary">
                                                            <div class="card-body mini-stat-img">
                                                                <div class="mini-stat-icon">
                                                                    <i class="mdi mdi-account-details-outline float-end"></i>
                                                                </div>
                                                                <div class="text-white">
                                                                    <h6 class="text-uppercase mb-3 font-size-16 "><a class="text-white" href="?vw=memberSahAnggota&mn=1">Saksi Keanggotaan</a></h6>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-3 col-sm-6">
                                                        <div class="card mini-stat bg-primary">
                                                            <div class="card-body mini-stat-img">
                                                                <div class="mini-stat-icon">
                                                                    <i class="mdi mdi-alert-circle-check float-end"></i>
                                                                </div>
                                                                <div class="text-white">
                                                                    <h6 class="text-uppercase mb-3 font-size-16 "><a class="text-white" href="?vw=memberApplyT&mn=1">Mohon Berhenti</a></h6>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-3 col-sm-6">
                                                        <div class="card mini-stat bg-primary">
                                                            <div class="card-body mini-stat-img">
                                                                <div class="mini-stat-icon">
                                                                    <i class="mdi mdi-account-cancel float-end"></i>
                                                                </div>
                                                                <div class="text-white">
                                                                    <h6 class="text-uppercase mb-3 font-size-16 "><a class="text-white" href="?vw=memberStatusT&mn=1">Status Berhenti</a></h6>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- end row -->
                                            <?php } elseif (@$_GET['mbx'] == 3) { ?>
                                                <div class="row">
                                                    <div class="col-xl-3 col-sm-6">
                                                        <div class="card mini-stat bg-primary">
                                                            <div class="card-body mini-stat-img">
                                                                <div class="mini-stat-icon">
                                                                    <i class="mdi mdi-cube-outline float-end"></i>
                                                                </div>
                                                                <div class="text-white">
                                                                    <h6 class="text-uppercase mb-3 font-size-16 "><a class="text-white" href="?vw=biayaEdit&mn=3">Info Gaji</a></h6>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-3 col-sm-6">
                                                        <div class="card mini-stat bg-primary">
                                                            <div class="card-body mini-stat-img">
                                                                <div class="mini-stat-icon">
                                                                    <i class="mdi mdi-buffer float-end"></i>
                                                                </div>
                                                                <div class="text-white">
                                                                    <h6 class="text-uppercase mb-3 font-size-16 "><a class="text-white" href="?vw=loanApply&mn=3">Mohon Baru</a></h6>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-3 col-sm-6">
                                                        <div class="card mini-stat bg-primary">
                                                            <div class="card-body mini-stat-img">
                                                                <div class="mini-stat-icon">
                                                                    <i class="mdi mdi-tag-text-outline float-end"></i>
                                                                </div>
                                                                <div class="text-white">
                                                                    <h6 class="text-uppercase mb-3 font-size-16 "><a class="text-white" href="?vw=loanInProcess&mn=3">Dalam Proses</a></h6>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-3 col-sm-6">
                                                        <div class="card mini-stat bg-primary">
                                                            <div class="card-body mini-stat-img">
                                                                <div class="mini-stat-icon">
                                                                    <i class="mdi mdi-tag-text-outline float-end"></i>
                                                                </div>
                                                                <div class="text-white">
                                                                    <h6 class="text-uppercase mb-3 font-size-16 "><a class="text-white" href="?vw=loanApproved&mn=3">Diluluskan</a></h6>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- end row -->
                                                <div class="row">
                                                    <div class="col-xl-3 col-sm-6">
                                                        <div class="card mini-stat bg-primary">
                                                            <div class="card-body mini-stat-img">
                                                                <div class="mini-stat-icon">
                                                                    <i class="mdi mdi-tag-text-outline float-end"></i>
                                                                </div>
                                                                <div class="text-white">
                                                                    <h6 class="text-uppercase mb-3 font-size-16 "><a class="text-white" href="?vw=loanOthers&mn=3">Lain-lain Status</a></h6>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- end row -->
                                            <?php } elseif (@$_GET['mbx'] == 4) { ?>
                                                <div class="row">
                                                    <div class="col-xl-3 col-sm-6">
                                                        <div class="card mini-stat bg-primary">
                                                            <div class="card-body mini-stat-img">
                                                                <div class="mini-stat-icon">
                                                                    <i class="mdi mdi-cube-outline float-end"></i>
                                                                </div>
                                                                <div class="text-white">
                                                                    <h6 class="text-uppercase mb-3 font-size-16 text-white"><a class="text-white" href="?vw=bayaranOnline&mn=9">Bayaran Atas Talian</a></h6>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- end row -->
                                            <?php } elseif (@$_GET['mbx'] == 5) { ?>
                                                <div class="row">
                                                    <div class="col-xl-3 col-sm-6">
                                                        <div class="card mini-stat bg-primary">
                                                            <div class="card-body mini-stat-img">
                                                                <div class="mini-stat-icon">
                                                                    <i class="mdi mdi-account-box-multiple-outline float-end"></i>
                                                                </div>
                                                                <div class="text-white">
                                                                    <h6 class="text-uppercase mb-3 font-size-16 "><a class="text-white" href="?vw=memberStmtN&mn=10">Senarai Penyata</a></h6>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- end row -->
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>


                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body">

                                            <?php


                                            if (@$_REQUEST['vw'] == '') {
                                                include("login.php");
                                            } elseif (@$_REQUEST['vw'] == 'main') {
                                                include("mainpage.php");
                                            } else {
                                                include @$_REQUEST['vw'] . ".php";
                                            }
                                            /*
                                             alert ("$msgSelect");
                                    gopage("$sActionFileName",1000);
                                             
                                             alert ("Tukar kata laluan berjaya dikemaskinikan");
                                    gopage("index.php",1000);
                                             
                                            <div class="row">
                                                <label class="col-md-1 col-form-label"></label>
                                                <label class="col-md-3 col-form-label"></label>
                                                <label class="col-md-1 col-form-label"><center> : </center></label>
                                                <label class="col-md-5 col-form-label"></label>
                                                <label class="col-md-2 col-form-label"></label>
                                            </div>

<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                    </button>
                                                    <strong>Oh snap!</strong> Change a few things up and try submitting
                                                    again.
                                                </div> 
                                                    */


                                            /*
                                            if (get_session("Cookie_groupID") == ''){
                                            print'
                                            <frameset rows="82,*,22" cols="*" frameborder="NO" border="0" framespacing="0">
                                            <frame src="header2.php" name="topFrame" scrolling="NO" noresize title="topFrame" >
                                            <frameset cols="1600,*" frameborder="no" border="3" framespacing="0">';
                                            print '<frame src="mainpage.php" name="mainFrame"  title="mainFrame">';
                                            }
                                            if (get_session("Cookie_groupID") !== '') {
                                            print'
                                            <frameset rows="82,*,22" cols="*" frameborder="NO" border="0" framespacing="0">
                                            <frame src="header2.php" name="topFrame" scrolling="NO" noresize title="topFrame" >
                                            <frameset cols="230,*" frameborder="no" border="3" framespacing="0">
                                            <frame src="leftpanel.php" name="leftFrame"  noresize title="leftFrame">';
                                            print '<frame src="mainpage.php" name="mainFrame"  title="mainFrame">';
                                            }
                                            print '
                                            </frameset>
                                            <frame src="footer2.php" name="footerFrame" scrolling="NO" noresize title="footerFrame" >
                                            </frameset>
                                            </html>'; */
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Col -->

                            </div>
                        <?php } ?>
                    </div>

                </div>
            </div>
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <font size="1px">Hakcipta Terpelihara. Copyright &COPY;
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> ALM CORE SOLUTIONS SDN BHD. 200901001252 (844177-D)
                                <img src="assets/images/logo-ori.png" width="30">
                            </font>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    <?php } ?>

    <?php
    if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
    ?>

        <nav class="navbar-dark bg-dark fixed-bottom" style="background-color: #2b3a4a!important;">

            <div class="navbar-nav" style="height: 74px;">
                <div class="row" style="height: 74px;">
                    <table>
                        <tr style="vertical-align: middle;">
                            <td class="col-2 text-center">
                                <a class="nav-link" href="index.php?mbx=1" title="Profail"><i class="mdi mdi-account-box h2" style="color: #f8f8f8;font-size: 34px;"></i>
                                    <br><span style="font-size: 12px;font-family: arial;">Profail</span></a>
                            </td>
                            <td class="col-2 text-center">
                                <a class="nav-link" href="index.php?mbx=2" title="Anggota"><i class="mdi mdi-book-account-outline h2" style="color: #f8f8f8;font-size: 34px;"></i>
                                    <br><span style="font-size: 12px;font-family: arial;">Anggota</span></a></a>
                            </td>
                            <td class="col-2 text-center">
                                <a class="nav-link" href="index.php?mbx=3" title="Pembiayaan"><i class="mdi mdi-account-cash h2" style="color: #f8f8f8;font-size: 34px;"></i>
                                    <br><span style="font-size: 12px;font-family: arial;">Pembiayaan</span></a></a>
                            </td>
                            <td class="col-2 text-center">
                                <a class="nav-link" href="index.php?mbx=4" title="Pembayaran"><i class="mdi mdi-currency-usd h2" style="color: #f8f8f8;font-size: 34px;"></i>
                                    <br><span style="font-size: 12px;font-family: arial;">Pembayaran</span></a></a>
                            </td>
                            <td class="col-2 text-center">
                                <a class="nav-link" href="index.php?mbx=5" title="Penyata"><i class="mdi mdi-ballot-outline h2" style="color: #f8f8f8;font-size: 34px;"></i>
                                    <br><span style="font-size: 12px;font-family: arial;">Penyata</span></a></a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </nav>
    <?php } ?>

    </div>
    <!-- END layout-wrapper -->

    <!-- JAVASCRIPT -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/jquery-sparkline/jquery.sparkline.min.js"></script>
    <script src="assets/plugins/bootstrap-sweetalert/sweetalert.min.js"></script>
    <script src="assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>

    <!-- Peity JS -->
    <script src="assets/libs/peity/jquery.peity.min.js"></script>

    <script src="assets/libs/morris.js/morris.min.js"></script>

    <script src="assets/libs/raphael/raphael.min.js"></script>

    <!-- Dashboard init JS -->
    <script src="assets/js/pages/dashboard.init.js"></script>

    <!-- App js -->
    <script src="assets/js/app.js"></script>

</body>

</html>