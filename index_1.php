<?php
@ob_start();
session_start();
/*********************************************************************************
*          Project		:	iKOOP.com.my
********************************************************************************/
include("common.php");	
/*
print '
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>'.$emaNetis.'</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script>';

if (get_session("Cookie_groupID") == '') print 'history.forward();';
*/

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
';?>

<body data-topbar="colored">
    
    <!-- Begin page -->
    <div id="layout-wrapper">
        
        <header id="page-topbar">
            <div class="navbar-header">
                <div class="d-flex">

                    <!-- LOGO -->
                    <div class="navbar-brand-box">
                        <a href="index.php" class="logo logo-dark">
                            <span class="logo-sm">
                                <img src="assets/images/logo-sm-dark.png" alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="assets/images/logo-dark.png" alt="" height="43">
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

                    <button type="button" class="btn px-3 font-size-24 header-item waves-effect" id="vertical-menu-btn">
                        <i class="mdi mdi-menu"></i>
                    </button>

                </div>

                <div class="d-flex">
           
                    <?php if (get_session("Cookie_userID") <> "") { ?>
                    <!-- User -->
                    <div class="dropdown d-inline-block">
                        <a class="dropdown-item text-white" href="?vw=logout" onclick="return confirm('Are you sure?')" style="background-color:#35a989;">
                                <span><!--i class="mdi mdi-power font-size-16 align-middle me-2 text-white"></i--> [ Keluar ]</span></a>
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
                    <?php } ?>

                </div>
            </div>
        </header>
        
        <!-- ========== Left Sidebar Start ========== -->
        <div class="vertical-menu" style="background-color: #2b3a4a;" >

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
                        <?php include ("leftpanel.php"); ?>
                    </ul>
                </div>
                <!-- Sidebar -->
            </div>
        </div>
        <!-- Left Sidebar End -->
        
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

                <div class="page-content">
                    <div class="container-fluid">
                        
                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-flex align-items-center justify-content-between">
                                    <div class="page-title">
                                        <h4 class="mb-0 font-size-18">Sistem Koperasi</h4>
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item active" >Selamat datang, <?php if (get_session("Cookie_userID") <> "") { echo get_session("Cookie_fullName"); } else { echo "Pelawat"; } ?>!</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->
                        
                        <div class="page-content-wrapper">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        
                                    <?php
                                    
                                    if(@$_REQUEST['vw']==''){
                                        $_REQUEST['vw']="main";
                                        
                                        ?>
                                        <script type="text/javascript">
                                            //redirect to login
                                           //window.location.href="plog.php"; 
                                         </script>
                                        <?php
                                    
                                    }

                                        if(@$_REQUEST['vw']==''){ 
                                                include ("login.php");                                                
                                            }
                                            elseif(@$_REQUEST['vw']=='main'){                                                
                                                include ("mainpage.php");
                                            } else {  
                                                include @$_REQUEST['vw'].".php";
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
                        </div>
    
                    </div>
            </div>
            
                <footer class="footer ">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-12 text-center"> &COPY; 
                                <script>document.write(new Date().getFullYear())</script>  Hakcipta Terpelihara Alm Core Solutions Sdn Bhd <span class="d-none d-sm-inline-block"> <i class="mdi mdi-heart text-primary"></i> iKOOP.</span>
                            </div>

                        </div>
                    </div>
                </footer>
    </div>
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