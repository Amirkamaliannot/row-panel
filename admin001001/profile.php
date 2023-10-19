<? 

include('header.php');

// ----------- get data from database  --------------------------------------------------------------
$id = $_SESSION['admin_id'];
$result = $connection->query("SELECT * FROM tbl_admin WHERE id ='$id'");
if (!$action->result($result)) return false;
if (!$result->num_rows)echo "<script>window.location.href = 'index.php';</script>";
$row = $result->fetch_object();
// ----------- get data from database when action is edit --------------------------------------------------------------

// ----------- check error ---------------------------------------------------------------------------------------------
$error = false;
if (isset($_SESSION['error'])) {
    $error = true;
    $error_val = $_SESSION['error'];
    unset($_SESSION['error']);
}
// ----------- check error ---------------------------------------------------------------------------------------------

// ----------- edit ---------------------------------------------------------------------------------------------
if (isset($_POST['submit'])) {

    // get fields
    $last_name = $action->request('last_name');
    $first_name = $action->request('first_name');

    $username = $action->request('username');

    $password = $action->request('password_');
    $oldpass = $action->request('old_password');

    // send query
    $command = $action->admin_profile_edit($username, $first_name, $last_name);

    if($command[0] && $password != '' && $oldpass != ''){

        $command = [$action->change_admin_password($password, $oldpass)];
    }

    
    $_SESSION['error'] = !$command[0];

    // bye bye :)
    echo "<script>window.location.href = 'profile.php';</script>";
    return;

}
// ----------- add or edit ---------------------------------------------------------------------------------------------

// ----------- start html :) ------------------------------------------------------------------------------------------
 ?>

<div class="page-wrapper">

    <div class="row page-titles">

        <!-- ----------- start title --------------------------------------------------------------------------- -->
        <div class="col-md-12 align-self-center text-right">
            <h3 class="text-primary">
            <?= $lang['your_specifications'] ?>
                |
                <?= $row->username ?>
            </h3>
        </div>
        <!-- ----------- end title ----------------------------------------------------------------------------- -->

        <!-- ----------- start breadcrumb ---------------------------------------------------------------------- -->
        <div class="col-md-12 align-self-center text-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="panel.php">
                        <i class="fa fa-dashboard"></i>
                        <?= $lang['home'] ?>
                    </a>
                </li>
                <li class="breadcrumb-item"><a href="user-list.php"><?= $lang['profile'] ?></a></li>
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
                  <?= $lang['unsuccessful_operation']  ?>.
                </div>
            <? } else { ?>
                <div class="alert alert-info text-right">
                <?= $lang['successful_operation']  ?>.
                </div>
            <? }
        } ?>
        <!-- ----------- end error list ------------------------------------------------------------------------ -->

        <div class="row">
            <div class="col-lg-6">

                <!-- ----------- start history ----------------------------------------------------------------- -->
                <div class="row m-b-0">
                    <div class="col-lg-6">
                        <p class="text-right m-b-0">
                        <?= $lang['add_history']  ?>:
                            <?= $action->time_to_shamsi($row->date_c) ?>
                        </p>
                    </div>
                    <? if ($row->date_m) { ?>
                        <div class="col-lg-6">
                            <p class="text-right m-b-0">
                            <?= $lang['last_edit']  ?>:
                                <?= $action->time_to_shamsi($row->date_m) ?>
                            </p>
                        </div>
                    <? } ?>
                </div>
                <!-- ----------- end history ------------------------------------------------------------------- -->

                <!-- ----------- start row of fields ----------------------------------------------------------- -->
                <div class="card">
                    <div class="card-body">
                        <div class="basic-form">
                            <form action="" method="post" enctype="multipart/form-data">

                                <div class="form-group">
                                    <input type="text" name="username" class="form-control input-default " autocomplete="new-password-field"
                                           placeholder="   <?= $lang['user_name']  ?>" value="<?= $row->username  ?>">
                                </div>
                                <div class="form-group">
                                    <input type="text" name="first_name" class="form-control input-default " autocomplete="new-password-field"
                                           placeholder="  <?= $lang['name']  ?>" value="<?= $row->first_name  ?>">
                                </div>

                                <div class="form-group">
                                    <input type="text" name="last_name" class="form-control input-default " autocomplete="new-password-field"
                                           placeholder=" <?= $lang['last_name']  ?>" value="<?= $row->last_name  ?>">
                                </div>



                               <?= $lang['admin_text'] ?>
                                <div class="form-group">
                                    <input type="password" name="old_password" class="form-control input-default "
                                           placeholder="<?= $lang['old_password'] ?>" >
                                </div>
                                <div class="form-group">
                                    <input type="password" name="password_" class="form-control input-default "
                                           placeholder="  <?= $lang['new_password'] ?>">
                                </div>
                                <script>
                                    $("input[name='old_password']").attr('value') = '';
                                    $("input[name='password_']").attr('value') = '';
                                </script>

                                <div class="form-actions">
                                    <button type="submit" name="submit" class="btn btn-success sweet-success">
                                        <i class="fa fa-check"></i> <?= $lang['add'] ?>
                                    </button>
                                    <a href="panel.php"><span name="back" class="btn btn-inverse"><?= $lang['back'] ?></span></a>
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
<script>

</script>
<? include('footer.php'); ?>
<!-- // ----------- end html :) --------------------------------------------------------------------------------------------- -->

