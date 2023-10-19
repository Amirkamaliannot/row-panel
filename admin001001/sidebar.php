<?
// require_once "functions/database.php";
// $action = new Action();
?>

<!-- ----------- start sidebar ------------------------------------------------------------------------------------- -->
<ul id="sidebarnav">

    <li class="nav-label">| <?= $lang['panel'] ?></li>

    <li>
        <a class="has-arrow" href="panel.php" aria-expanded="false">
            <i class="fa fa-dashboard"></i>
            <span class="hide-menu"><?= $lang['dashboard'] ?></span>
        </a>
    </li>
    <hr class="m-0">



    <!-- <hr class="m-0">
<? if ($action->admin()->access) { ?>
    <li class="nav-label">| <?= $lang['operation'] ?></li>
    <li>
        <a class="has-arrow" href="admin-list.php" aria-expanded="false">
            <i class="fa fa-users"></i>
            <span class="hide-menu"><?= $lang['managers'] ?></span>
        </a>
    </li>

    <? } ?>

    <hr class="m-0"> -->

</ul>
<!-- ----------- end sidebar --------------------------------------------------------------------------------------- -->
