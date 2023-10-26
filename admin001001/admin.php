<? 
include('header.php');
// check admin access
// if (!$action->admin()->access) {
//     echo "<script type='text/javascript'>window.location.href = 'panel.php';</script>";
//     return 0;
// }

// ----------- urls ----------------------------------------------------------------------------------------------------
// main url for add , edit
$main_url = "admin.php";
// main url for remove , change status
$list_url = "admin-list.php";
// ----------- urls ----------------------------------------------------------------------------------------------------

// ----------- get data from database when action is edit --------------------------------------------------------------
$edit = false;
if (isset($_GET['edit'])) {
    $edit = true;
    $id = $action->request('edit');
    $result = $connection->query("SELECT * FROM tbl_admin WHERE `id`='$id' AND `access`='0' ");
    if (!$action->result($result)) return false;
    if (!$result->num_rows)
    { 
        $_SESSION['error'] = 1;
        header("Location: admin-list.php");
        return ;
    };
    $row = $result->fetch_object();
}
// ----------- get data from database when action is edit --------------------------------------------------------------

// ----------- check error ---------------------------------------------------------------------------------------------
$error = false;
if (isset($_SESSION['error'])) {
    $error = true;
    $error_val = $_SESSION['error'];
    unset($_SESSION['error']);
}
// ----------- check error ---------------------------------------------------------------------------------------------

// ----------- add or edit ---------------------------------------------------------------------------------------------
if (isset($_POST['submit'])) {

    // get fields
    $first_name = $action->request('first_name');
    $last_name = $action->request('last_name');
    $username = $action->request('username');
    $password = $action->request('password');
    $salt = 'jhsdhowekgwds1';
    $password = md5($salt.$password);
    $access = 0;
    $status = (int)$action->request('status');


    if($username=="" || ($password =="" && !$edit)){
        $_SESSION['error'] = 1;
        echo "<script>window.location.href = '$main_url?edit=$command[0]';</script>";
    }
    if($username=="" || ($password =="" && !$edit)){
        $_SESSION['error'] = 1;
        echo "<script>window.location.href = '$main_url?edit=$command[0]';</script>";
    }


    // send query
    $add_list = 
    [
        ['first_name',$first_name],
        ['last_name',$last_name],
        ['username',$username],
        ['password',$password],
        ['status',$status],
        ['last_login', 0],
        ['login_session',$action->get_token(32)],
        ['access',$access]
    ];
    $edit_list =
    [
        ['first_name',$first_name],
        ['last_name',$last_name],
        ['username',$username],
        ['status',$status]
    ];
    if ($edit) 
    {
        if($password!=""){array_push($edit_list, ['password',$password]);}
        $command = $action->edit_row($id, 'admin', $edit_list);

    } else 
    {
        $command = $action->add_row( $add_list, "admin");
    }


    // check errors
    if (!$command[1] && !$edit) 
    {
        $_SESSION['error'] = 1;
        echo "<script type='text/javascript'>window.location.href = '$main_url';</script>";
        return 0;
    }
    elseif(!$command[1])
    {
        $_SESSION['error'] = 1;
        echo "<script>window.location.href = '$main_url?edit=$command[0]';</script>";
    }
    else
    {
        $_SESSION['error'] = 0;
        echo "<script>window.location.href = '$main_url?edit=$command[0]';</script>";
    }
    return;
    // bye bye :)

}
// ----------- add or edit ---------------------------------------------------------------------------------------------

// ----------- start html :) ------------------------------------------------------------------------------------------
 ?>

<div class="page-wrapper">

    <div class="row page-titles">

        <!-- ----------- start title --------------------------------------------------------------------------- -->
        <div class="col-md-12 align-self-center text-right">
            <?php if (!isset($_GET['action'])&& !$edit) { ?>
                <h3 class="text-primary"><?= $lang['add_manager'] ?></h3>
            <?php } else { ?>
                <h3 class="text-primary"><?= $lang['edit_manager'] ?></h3>
            <?php } ?>
        </div>
        <!-- ----------- end title ----------------------------------------------------------------------------- -->

        <!-- ----------- start breadcrumb ---------------------------------------------------------------------- -->
        <div class="col-md-12 align-self-center text-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="panel.php">
                        <i class="fa fa-dashboard"></i>
                        خانه
                    </a>
                </li>
                <li class="breadcrumb-item"><a href="user-list.php"><?= $lang['managers'] ?></a></li>
                <?php if ($edit) { ?>
                    <li class="breadcrumb-item"><a href="javascript:void(0)"><?= $lang['add']; ?></a></li>
                <?php } else { ?>
                    <li class="breadcrumb-item"><a href="javascript:void(0)"><?= $lang['edit'] ?></a></li>
                <?php } ?>
            </ol>
        </div>
        <!-- ----------- end breadcrumb ------------------------------------------------------------------------ -->

    </div>

    <!-- ----------- start main container ---------------------------------------------------------------------- -->
    <div class="container-fluid">

        <!-- ----------- start error list ---------------------------------------------------------------------- -->
        <? if ($error) {
            if ($error_val) { ?>
                <div class="alert alert-danger">
                   <?= $lang['unsuccessful_operation'] ?>
                </div>
            <? } else { ?>
                <div class="alert alert-info text-right">
                <?= $lang['successful_operation'] ?>
            </div>
            <? }
        } ?>
        <!-- ----------- end error list ------------------------------------------------------------------------ -->

        <div class="row">
            <div class="col-lg-6">

                <!-- ----------- start history ----------------------------------------------------------------- -->
                <? if ($edit) { ?>
                    <div class="row m-b-0">
                        <div class="col-lg-6">
                            <p class="text-right m-b-0">
                              <?= $lang['registration_date'] ?>:
                                <?= $action->time_to_shamsi($row->date_c) ?>
                            </p>
                        </div>
                        <? if ($row->date_m) { ?>
                            <div class="col-lg-6">
                                <p class="text-right m-b-0">
                                    <?= $lang['last_edit'] ?>:
                                    <?= $action->time_to_shamsi($row->date_m) ?>
                                </p>
                            </div>
                        <? } ?>
                    </div>
                <? } ?>
                <!-- ----------- end history ------------------------------------------------------------------- -->

                <!-- ----------- start row of fields ----------------------------------------------------------- -->
                <div class="card">
                    <div class="card-body">
                        <div class="basic-form">
                            <form action="" method="post" enctype="multipart/form-data">

                                <div class="form-group">
                                    <label class='right-label'><?= $lang['name'] ?>  : </label>
                                    <input type="text" name="first_name" class="form-control input-default "
                                           placeholder="<?= $lang['name'] ?>"
                                           value="<?= ($edit) ? $row->first_name : "" ?>" required>
                                </div>

                                <div class="form-group">
                                    <label class='right-label'><?= $lang['last_name'] ?>  : </label>  
                                    <input type="text" name="last_name" class="form-control input-default "
                                           placeholder="<?= $lang['last_name'] ?>"
                                           value="<?= ($edit) ? $row->last_name : "" ?>" required>
                                </div>

                                <div class="form-group">
                                    <label class='right-label'> <?=$lang['user_name'] ?> : </label>
                                    <input type="text" name="username" class="form-control input-default "
                                           placeholder="<?=$lang['user_name']?>"
                                           value="<?= ($edit) ? $row->username : "" ?>" required>
                                </div>

                                <?
                                if($edit){?>
                                    <label><?= $lang['admin_text'] ?></label></label>
                                <?}?>

                                <div class="form-group">
                                    <label class='right-label'>  <?= $lang['password'] ?> : </label>
                                    <input type="text" name="password" class="form-control input-default "
                                           placeholder="<?= $lang['password'] ?>"
                                           value="">
                                </div>

                                <div class="form-actions">

                                    <label class="float-right">
                                        <input type="checkbox" class="float-right m-1" name="status" value="1"
                                            <? if ($edit && $row->status) echo "checked"; ?> >
                                        <?= $lang['enable'] ?>
                                    </label>

                                    <button type="submit" name="submit" class="btn btn-success sweet-success">
                                        <i class="fa fa-check"></i> <?= $lang['add'] ?>
                                    </button>

                                    <a href="<?= $list_url ?>"><span name="back" class="btn btn-inverse"><?= $lang['back_to_list'] ?></span></span></a>
                                    
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- ----------- end row of fields ----------------------------------------------------------- -->

            </div>
        </div>
    </div>
    <!-- ----------- end main container ------------------------------------------------------------------------ -->

</div>
<? include('footer.php'); ?>
// ----------- end html :) ---------------------------------------------------------------------------------------------

