<?php

require_once "../functions/database.php";
$database = new DB();
$action = new Action();

if ($action->guest()) {
    return 0;
}

if(isset($_GET['selected'])){
    $selected = $_GET['selected'];
}

$result = $action->rows_list('coch');
$rowcount=$result->num_rows;
if($rowcount>0){
    
    while($crow=$result->fetch_object()){
        $a = "<option ";
        $a .= "value='".$crow->id."'";
        if($selected == $crow->id){
            $a .= "selecteed";
        }
        $a .=">".$crow->fullname;
        $a .= "</option>";
        echo $a;



    }
    
}else{echo"<option value='-1' hidden>".$lang['no_coach']."</option>";}


?>