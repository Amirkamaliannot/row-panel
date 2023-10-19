<?
include('../functions/database.php');
$action = new Action();

@$action->get_login_by_COOKIE($_COOKIE['uname']);
// check admin access
if ($action->auth()) {
    echo "<script type='text/javascript'>window.location.href = 'panel.php';</script>";
    return 0;
}



if(isset($_COOKIE['lang_panel'])){

    $lang_coockie=$_COOKIE['lang_panel']; 
    if($lang_coockie=="fa"){
    
        require_once "../functions/lang/fa.php";

    }else if($lang_coockie="en"){

        include "../functions/lang/en.php";
    }

}else{

    include "../functions/lang/fa.php";

}


// ----------- check error ---------------------------------------------------------------------------------------------
$error = 0;
if (isset($_SESSION['error'])) {
    $error = 1;
    $error_val = $_SESSION['error'];
    unset($_SESSION['error']);
}
// ----------- check error ---------------------------------------------------------------------------------------------

// ----------- check login ---------------------------------------------------------------------------------------------
if (isset($_POST['sub1'])) {

    // get fields
    $user = $action->request('user');
    $pass = $action->request('pass');
    $remember_me = @(int)$action->request('remember_me');
    // send query
    $command = $action->admin_login($user, $pass, $remember_me);

    // check errors
    if (!$command) {
        $_SESSION['error'] = 1;
        echo "<script>window.location.href = 'index.php';</script>";
    }

    // bye bye :)
    echo "<script>window.location.href = 'panel.php';</script>";
}
// ----------- check login ---------------------------------------------------------------------------------------------
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<!-- ----------- start head ---------------------------------------------------------------------------------------- -->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title><?= $lang['login_to_operation'] ?></title>
    <!-- Bootstrap Core CSS -->
    <link href="../assets/css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/helper.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
</head>
<!-- ----------- end head ------------------------------------------------------------------------------------------ -->

<body class="fix-header fix-sidebar">
<!-- Preloader - style you can find in spinners.css -->
<div class="preloader">
    <svg class="circular" viewBox="25 25 50 50">
        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
    </svg>
</div>

<div id="main-wrapper">

    <div class="unix-login">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-4">
                    <div class="login-content card">
                        <div class="login-form">
                            <h4><?= $lang['login'] ?></h4>

                            <!-- ----------- start error list ------------------------------------------------------ -->
                            <? if ($error) {
                                if ($error_val) { ?>
                                    <div class="alert alert-danger">
                                        <?= $lang['index_text'] ?>
                                    </div>
                                <? }
                            } ?>
                            <!-- ----------- end error list -------------------------------------------------------- -->

                            <!-- ----------- start login form ------------------------------------------------------ -->
                            <form action="" method="POST">

                                <div class="form-group">
                                    <label><?= $lang['user_name'] ?></label>
                                    <input type="text" class="form-control" name="user" placeholder="<?= $lang['user_name'] ?> ">
                                </div>

                                <div class="form-group">
                                    <label><?= $lang['password'] ?></label>
                                    <input type="password" class="form-control" name="pass" placeholder="<?= $lang['password'] ?>">
                                </div>

                                <div class="checkbox">
                                    <label>
                                        <input value="1" name="remember_me" type="checkbox" checked> <?= $lang['remember_me'] ?>
                                    </label>
                                </div>

                                <button type="submit" name="sub1" class="btn btn-primary btn-flat m-b-30 m-t-30">
                                <?= $lang['login'] ?>
                                </button>

                            </form>
                            <!-- ----------- end login form -------------------------------------------------------- -->


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ----------- start scripts ------------------------------------------------------------------------------------- -->
<!-- All Jquery -->
<script src="../assets/js/lib/jquery/jquery.min.js"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="../assets/js/lib/bootstrap/js/popper.min.js"></script>
<script src="../assets/js/lib/bootstrap/js/bootstrap.min.js"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="../assets/js/jquery.slimscroll.js"></script>
<!--Menu sidebar -->
<script src="../assets/js/sidebarmenu.js"></script>
<!--stickey kit -->
<script src="../assets/js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>
<!--Custom JavaScript -->
<script src="../assets/js/scripts.js"></script>
<!-- ----------- end scripts --------------------------------------------------------------------------------------- -->

</body>
</html>