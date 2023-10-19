<?
// ----------- start config methods ------------------------------------------------------------------------------------
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

session_start();
include('jdf.php');
date_default_timezone_set("Asia/Tehran");
// ----------- end config methods --------------------------------------------------------------------------------------

// ----------- start DB class ------------------------------------------------------------------------------------------
class DB
{

    // ----------- properties
    protected $_DB_HOST = 'localhost';
    protected $_DB_USER = 'root';
    protected $_DB_PASS = '';
    protected $_DB_NAME = 'database';
    protected $connection;

    // ----------- constructor
    public function __construct()
    {
        $this->connection = mysqli_connect($this->_DB_HOST, $this->_DB_USER, $this->_DB_PASS, $this->_DB_NAME);
        if ($this->connection) {
            $this->connection->query("SET NAMES 'utf8'");
            $this->connection->query("SET CHARACTER SET 'utf8'");
            //$this->connection->query("SET character_setconnectionection = 'utf8'");
        }
    }

    // ----------- for return connection
    public function connect()
    {
        return $this->connection;
    }

}

// ----------- end DB class --------------------------------------------------------------------------------------------

// ----------- start Action class --------------------------------------------------------------------------------------
class Action
{

    // ----------- properties
    public $connection;


    public $public_media_path;
    public $private_media_path;
    public $domain ;
    // ----------- constructor
    public function __construct()
    {
        $db = new DB();
        $this->connection = $db->connect();


        $this->domain == $_SERVER['HTTP_HOST'];
        $this->public_media_path = $this->get_public_media_path();
        $this->private_media_path = $this->get_private_media_path();
    }

    // ----------- start main methods ----------------------------------------------------------------------------------



    // ----------- get current page url
    public function get_public_media_path()
    {
        $root = $_SERVER['DOCUMENT_ROOT']; // Get the document root of the server
        $results = glob($root . '/shimi/media', GLOB_ONLYDIR); // Search for 'media' folder in all directories
        if (!empty($results)) {
          $media_folder_path = reset($results); // Get the first result
          return $media_folder_path;
        } else {
          return false;
        }

    }
    public function get_private_media_path()
    {
        $root = $_SERVER['DOCUMENT_ROOT']; // Get the document root of the server
        $results = glob($root . '/shimi/functions/images/', GLOB_ONLYDIR); // Search for 'media' folder in all directories
        if (!empty($results)) {
          $media_folder_path = reset($results); // Get the first result
          return $media_folder_path;
        } else {
          return false;
        }

    }

    public function url()
    {
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        return $url;
    }

    // ----------- for check result of query
    public function result($result)
    {
        if (!$result) {
            $errorno = mysqli_errno($this->connection);
            $error = mysqli_error($this->connection);
            echo "Error NO : $errorno";
            echo "<br>";
            echo "Error Message : $error";
            echo "<hr>";
            return false;
        }
        return true;
    }

    // ----------- count of table's field
    public function table_cunter($table)
    {
        $result = $this->connection->query("SELECT * FROM `$table` ");
        if (!$this->result($result)) return false;
        return $result->num_rows;
    }

    // ----------- get all fields in table
    public function table_list($teble)
    {
        $id = $this->admin()->id;
        $result = $this->connection->query("SELECT * FROM `$teble` ORDER BY `id` DESC");
        if (!$this->result($result)) return false;
        return $result;
    }

    // ----------- change status of field
    public function change_status($table, $id, $status)
    {
        $now = time();
        $result = $this->connection->query("UPDATE `$table` SET 
        `status`='$status',
        `date_m`='$now'
        WHERE `id` ='$id'");
        if (!$this->result($result)) return false;
        return $id;
    }

    // ----------- get data from table
    public function get_data($table, $id)
    {
        $result = $this->connection->query("SELECT * FROM `$table` WHERE id='$id'");
        if (!$this->result($result)) return false;
        $row = $result->fetch_object();
        return $row;
    }

    // ----------- remove data from table
    public function remove_data($table, $id)
    {
        $result = $this->connection->query("DELETE FROM `$table` WHERE id='$id'");
        if (!$this->result($result)) return false;
        return true;
    }

    // ----------- clean strings (to prevent sql injection attacks)
    public function clean($string, $status = true)
    {
        if ($status)
            $string = htmlspecialchars($string);
        $string = stripslashes($string);
        $string = strip_tags($string);
        $string = mysqli_real_escape_string($this->connection, $string);
        return $string;
    }

    // ----------- for clean and get requests
    public function request($name, $status = true)
    {
        $str = $this->convert_num($_REQUEST[$name]);
        return $this->clean($str, $status);
    }
    
    public function convert_num($string) {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    
        $num = range(0, 9);
        $convertedPersianNums = str_replace($persian, $num, $string);
        $englishNumbersOnly = str_replace($arabic, $num, $convertedPersianNums);
        
        return $englishNumbersOnly;
    }

    // ----------- for get and convert date
    public function request_date($name)
    {
        $name = $this->request('birthday', false);
        $name = $this->shamsi_to_miladi($name);
        return strtotime($name);
    }

    // ----------- convert timestamp to shamsi date
    public function time_to_shamsi($timestamp )
    {

        return $this->miladi_to_shamsi(date('Y-m-d', $timestamp));
    }

    // ----------- convert shamsi date to miladi date
    public function shamsi_to_miladi($date)
    {
        $pieces = explode("/", $date);
        $day = $pieces[2];
        $month = $pieces[1];
        $year = $pieces[0];
        $b = jalali_to_gregorian($year, $month, $day, $mod = '-');
        $f = $b[0] . '-' . $b[1] . '-' . $b[2];
        return $f;
    }

    // ----------- convert miladi date to shamsi date
    public function miladi_to_shamsi($date)
    {
        $pieces = explode("-", $date);
        $year = $pieces[0];
        $month = $pieces[1];
        $day = $pieces[2];
        $b = gregorian_to_jalali($year, $month, $day, $mod = '-');
        $f = $b[0] . '/' . $b[1] . '/' . $b[2];
        return $f;
    }
    public function condate($date){
        $pieces = explode("/", $date);
        $day=$pieces[2];
        $month=$pieces[1];
        $year=$pieces[0];
        $b=jalali_to_gregorian($year,$month,$day,$mod='-');
        $f=$b[0].'-'.$b[1].'-'.$b[2];
        return $f;
    }

    // ----------- for send sms to mobile number
    public function send_sms($mobile, $textMessage)
    {
        return;
        $webServiceURL = "";
        $webServiceSignature = "";
        $webServiceNumber = "";
        $textMessage = mb_convert_encoding($textMessage, "UTF-8");
        $parameters['signature'] = $webServiceSignature;
        $parameters['toMobile'] = $mobile;
        $parameters['smsBody'] = $textMessage;
        $parameters['retStr'] = ""; // return reference send status and mobile and report code for delivery
        try {
            $con = new SoapClient($webServiceURL);
            $responseSTD = (array)$con->Send($parameters);
            $responseSTD['retStr'] = (array)$responseSTD['retStr'];
        } catch (SoapFault $ex) {
            echo $ex->faultstring;
        }
    }

    // ----------- create random token
    public function get_token($length)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet);
        for ($i = 0; $i < $length; $i++) 
        {
            $token .= $codeAlphabet[rand(0, $max - 1)];
        }
        return $token;
    }

    // ----------- end main methods ------------------------------------------------------------------------------------

    // ----------- start ADMINS ----------------------------------------------------------------------------------------
    // ----------- for login admin
    public function admin_login($user, $pass, $remember_me){

        $salt = 'jhsdhowekgwds1';;
		$hachpass = md5($salt.$pass);

        $result = $this->connection->query("SELECT * FROM `tbl_admin` WHERE `username`='$user' AND `password`='$hachpass' AND `status`=1");
        $result2 = $this->connection->query("SELECT * FROM `tbl_admin` WHERE `username`='$user'");
        if (!$this->result($result) || !$this->result($result2)) return false;

        $is_user_exist = $result2->num_rows;
        $is_pass_true = $result->num_rows;

        if ($is_pass_true) {
            $row = $result->fetch_object();
            if($this->add_admin_log($row->id, 1,1)){
                $this->admin_update_last_login($row->id);
                $_SESSION['admin_id'] = $row->id;
                $_SESSION['admin_access'] = $row->access;

                if($remember_me){

                    //set login cookie
                    $token = $this->change_admin_token($row->id);
                    setcookie("uname", $token,time()+3600*24*365,'/',$_SERVER['HTTP_HOST']);
                }

                return true;
            }
        }elseif($is_user_exist){
            $row = $result2->fetch_object();
            $this->add_admin_log($row->id, 0,1);
        }
        return false;
    }



    public function add_admin_log($admin_id, $status, $action)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $add_list = 
        [
            ['admin_id',$admin_id ],
            ['ip',$ip],
            ['status',$status],
            ['action',$action],
        ];
        $command = $this->add_row( $add_list, "admin_log");
        return $command[1];
    }





    // ----------- for check access (admin access)
    public function change_admin_token($admin_id){

        $token = $this->get_token(32);
        $edit_list = 
        [
            ['login_session', $token],
        ];
        $res = $this->edit_row($admin_id, 'admin', $edit_list);

        if($res[0]){
            return $token;
        }else{
            return false;
        }

    }
    // ----------- for check access (admin access)
    public function auth()
    {
        if (isset($_SESSION['admin_id']) && isset($_SESSION['admin_access']))
            return true;
        return false;
    }



    public function get_login_by_COOKIE($cookie){

        $cookie = $this->clean($cookie);
        $result = $this->connection->query("SELECT * FROM `tbl_admin` WHERE `login_session`='$cookie' and `login_session` != '' ");
        if (!$this->result($result)) return false;
        if($result->num_rows){

            $admin = $result->fetch_object();
            $_SESSION['admin_id'] = $admin->id ;
            $_SESSION['admin_access'] = $admin->access;
            return $result->fetch_object()->id;
        }else{
            return false;
        }
    }


    // ----------- for check access (guest access)
    public function guest()
    {
        if (isset($_SESSION['admin_id']) && isset($_SESSION['admin_access']))
            return false;
        return true;
    }


    // ----------- update last login of admin (logged)
    public function admin_update_last_login($id)
    {
        $now = strtotime(date('Y-m-d H:i:s'));
        $result = $this->connection->query("UPDATE `tbl_admin` SET `last_login`='$now' WHERE `id`='$id'");
        if (!$this->result($result)) return false;
        return true;
    }



    // ----------- update profile (logged admin)

    public function admin_profile_edit($username, $first_name, $last_name)
    {

        if($username=="")return false;
        $admin_id = $_SESSION['admin_id'];
        $now = time();
        $result = $this->connection->query("UPDATE `tbl_admin` SET 
        `username`='$username',
        `first_name`='$first_name',
        `last_name`='$last_name',
        `date_m`='$now'
        WHERE `id` ='$admin_id'");    
        if (!$this->result($result)) return false;
        return $admin_id;
    }

    public function change_admin_password($new_password, $oldpass)
    {
        $admin_id = $_SESSION['admin_id'];
        $oldpass_ = $this->get_data('tbl_admin', $admin_id)->password;

        $salt = 'jhsdhowekgwds1';;
		$oldpass = md5($salt.$oldpass);
        if($oldpass != $oldpass_){
            return false;
        }

        $salt = 'jhsdhowekgwds1';
		$pass = md5($salt.$new_password);

        $edit_list = 
        [
            ['password',$pass],
        ];
        $res = $this->edit_row($admin_id, 'admin', $edit_list);

        if($res[0])return true; else return false;
    }


    // ----------- for show all admins
    public function admin_list()
    {
        $id = $this->admin()->id;
        $result = $this->connection->query("SELECT * FROM `tbl_admin` WHERE NOT `id`='$id' ORDER BY `id` DESC");
        if (!$this->result($result)) return false;
        return $result;
    }

    // ----------- add an admin
    public function admin_add($first_name, $last_name, $phone, $username, $password, $status, $access)
    {
        $now = time();
        $result = $this->connection->query("INSERT INTO `tbl_admin`
        (`first_name`,`last_name`,`phone`,`username`,`password`,`access`,`status`,`date_c`) 
        VALUES
        ('$first_name','$last_name','$phone','$username','$password','$access','$status','$now')");
        if (!$this->result($result)) return false;
        return $this->connection->insert_id;
    }

    // ----------- update admin's detail
    public function admin_edit($id, $first_name, $last_name, $phone, $username, $password, $status, $access)
    {
        $now = time();
        $result = $this->connection->query("UPDATE `tbl_admin` SET 
        `first_name`='$first_name',
        `last_name`='$last_name',
        `phone`='$phone',
        `username`='$username',
        `password`='$password',
        `access`='$access',
        `status`='$status',
        `date_m`='$now'
        WHERE `id` ='$id'");
        if (!$this->result($result)) return false;
        return $id;
    }

    // ----------- remove admin
    public function admin_remove($id)
    {
        if ($this->admin_get($id)->access) return false;
        $result = $this->connection->query("DELETE FROM tbl_admin_log WHERE admin_id = $id;");
        $result = $this->connection->query("DELETE FROM tbl_admin WHERE id = $id;");
        if (!$this->result($result)) return false;
        return true;
    }

    // ----------- change admin's status
    public function admin_status($id)
    {
        if ($this->admin_get($id)->access) return false;
        $status = $this->admin_get($id)->status;
        // $status = !$status;
        if ($status){
            $status=0;
        }else{
            $status=1;
        }
        return $this->change_status('tbl_admin', $id, $status);
    }



    // ----------- get admin's data
    public function admin_get($id)
    {
        return $this->get_data("tbl_admin", $id);
    }

    // ----------- get admin's data (logged)
    public function admin()
    {
        $id = $_SESSION['admin_id'];
        return $this->get_data("tbl_admin", $id);
    }

    // ----------- count of admin
    public function admin_counter()
    {
        return $this->table_cunter("tbl_admin");
    }

    // ----------- end ADMINS ------------------------------------------------------------------------------------------



    // ----------- end USERS -------------------------------------------------------------------------------------------
   
    // ----------- start post , rate , comment , category-----------------------------------------------------------------------------------------


    public function rows_list($type="post")
        //===$type can be 'post' , 'comment' , 'rate', 'cat'...===//
    {
        $result = $this->connection->query("SELECT * FROM `tbl_$type` ORDER BY `id` DESC");
        if (!$this->result($result)) return false;
        return $result;
    }




    public function post_remove($id , $type="post")
        //===$type can be 'post' , 'comment' , 'rate'===//
    {
        return $this->remove_data("tbl_".$type, $id);
    }

    public function comment_or_cat_status($id , $type="comment")
    {
        $status = $this->get_data('tbl_'.$type ,$id)->status;
        if ($status){
            $status=0;
        }else{
            $status=1;
        }

        return $this->change_status('tbl_'.$type, $id, $status);
    }


    public function edit_row($id ,$tbl, $columns, $check=true)
    {
        if (!$check) return false;
        $now = time();
        $query = "UPDATE `tbl_$tbl` SET ";
        foreach ($columns as $key => $column)
        {
            $query .= "`$column[0]` = '$column[1]' ,";
        }
        $query .= "`date_m`= '$now' WHERE `id` =$id";
        $result = $this->connection->query($query);
        if (!$this->result($result)) return [$id, false];
        return [$id, true];
    }

    public function add_row($columns,$tbl,$check=true)
    {
        if (!$check) return false;
        $now = time();
        $query ="INSERT INTO `tbl_$tbl`( ";
        foreach($columns as $column)
        {
            $query .= " `$column[0]`,";
        }
        $query.=" `date_c`) VALUES (";
        foreach($columns as $column)
        {
            $query .= " '$column[1]',";
        }
        $query.=" '$now' )";

        $result = $this->connection->query($query);
        if (!$this->result($result)) return [1,false];
        return [$this->connection->insert_id,true];
    }


    //type can be 1,0 for add or delete


    function api_query($url)
    {
        $header =array(
            'Accepts:application/json',
            // 'X-CMC_PRO_API_KEY:c2f57b68-ab4a-4ca9-8906-d283d300ed3a'
            'X-CMC_PRO_API_KEY:b090af03-a20e-4eb3-8251-a2cf1e12b6d8'
        );
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        return curl_exec($curl);
    }

    public function getImageResized($image, $newWidth, $newHeight) {
        $newImg = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($newImg, false);
        imagesavealpha($newImg, true);
        $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
        imagefilledrectangle($newImg, 0, 0, $newWidth, $newHeight, $transparent);
        $src_w = imagesx($image);
        $src_h = imagesy($image);
        imagecopyresampled($newImg, $image, 0, 0, 0, 0, $newWidth, $newHeight, $src_w, $src_h);
        return $newImg;
    }


    public function save_img($uploadfile,$type ,$admin=false)
    {
        if($admin){
            // $path = $_SERVER['DOCUMENT_ROOT']."/shimi/101001admin/images/$type/";
            $path = $this->private_media_path."/$type/";
        }else{
            // $path = $_SERVER['DOCUMENT_ROOT']."/shimi/media/$type/";
            $path = $this->public_media_path."/$type/";
            // $path = 'media/'."$type/";
        }

        if(!is_dir($path)){
            mkdir($path, 0755);
        };
        $tempname = $_FILES[$uploadfile]["tmp_name"];
        $filename = $this->get_token(32);
        // $filename = bin2hex(random_bytes(16));
        $small_folder = $path. $filename . '.png';
        $folder = $path. $filename . '_large.png';

        if($tempname == '')return false;
    
        if(@is_array(getimagesize($tempname))){
            move_uploaded_file($tempname, $folder);
            $type = exif_imagetype($folder);
            switch(image_type_to_mime_type($type)){  
                case 'image/jpeg' :
                    $original = imagecreatefromjpeg($folder);
                    break;
                case 'image/png' : 
                    $original = imagecreatefrompng($folder);
                    break;
                case 'image/gif' :
                    $original = imagecreatefromgif($folder);
                    break;
            }
            
            $resized = $this->getImageResized($original,200, 200);
            // $resized = imagecreatetruecolor(200, 200);
            // imagecopyresampled($resized, $original, 0, 0, 0, 0, 200, 200, $width, $height);
            imagepng($resized, $small_folder);
            $image_check = true;
        }else{
            $image_check = false;
        };
        return [$image_check,$filename.".png"];
    }

    public function save_pdf($uploadfile,$type ,$admin=false)
    {
        if($admin){
            // $path = $_SERVER['DOCUMENT_ROOT']."/shimi/101001admin/images/$type/";
            $path = $this->private_media_path."/$type/";
        }else{
            // $path = $_SERVER['DOCUMENT_ROOT']."/shimi/media/$type/";
            $path = $this->public_media_path."/$type/";
        }

        if(!is_dir($path)){
            mkdir($path, 0755);
        };

        $filename = $filename = $this->get_token(32);
        // $filename = bin2hex(random_bytes(16));
        $folder = $path. $filename . '.png';

        if (isset($_FILES[$uploadfile])) {
            $file = $_FILES[$uploadfile];
            $file_name = $file['name'];
            $file_tmp = $file['tmp_name'];
            $file_size = $file['size'];
            $file_error = $file['error'];

            if($file_tmp == '')return false;
        
            $file_ext = explode('.', $file_name);
            $file_ext = strtolower(end($file_ext));
        
            $allowed = array('pdf');
        
            if (in_array($file_ext, $allowed)) {
                if ($file_error === 0) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime_type = finfo_file($finfo, $file_tmp);
    
                    if ($mime_type === "application/pdf") {
                        $file_name_new = $filename.'.pdf';
                        $file_destination = $path . $file_name_new;
    
                        if (move_uploaded_file($file_tmp, $file_destination)) {
                            echo "File uploaded successfully";
                            return $file_name_new;
                            finfo_close($finfo);
                        }
                    } else {
                        echo "File is not a PDF";
                        finfo_close($finfo);
                        return false;
                    }
                }else{return false;}
            }else{return false;}
        }else{return false;}
    }

    public function save_file_user($uploadfile, $type, $admin = false)
    {
        $allowed_extensions = array('pdf', 'zip', 'rar', 'jpg', 'jpeg', 'png');
    
        if ($admin) {
            $path = $this->private_media_path . "/$type/";
        } else {
            $path = $this->public_media_path . "/$type/";
        }
    
        if (!is_dir($path)) {
            mkdir($path, 0755);
        };
    
        $filename = $this->get_token(32);
    
        if (isset($_FILES[$uploadfile])) {
            $file = $_FILES[$uploadfile];
            $file_name = $file['name'];
            $file_tmp = $file['tmp_name'];
            $file_error = $file['error'];
    
            if ($file_tmp == '') {
                return false;
            }
    
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $file_ext = strtolower($file_ext);
    
            if (in_array($file_ext, $allowed_extensions)) {
                if ($file_error === 0) {
                    $file_name_new = $filename . '.' . $file_ext;
                    $file_destination = $path . $file_name_new;
    
                    if (move_uploaded_file($file_tmp, $file_destination)) {
                        echo "File uploaded successfully";
                        return $file_name_new;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    

    public function phone_format_check($phone){
        $justNums = preg_replace("/[^0-9]/", '', $phone);

        if(strlen($justNums) != strlen($phone))return ['format'=>false,'phone'=>$justNums];

        if (strlen($justNums) == 11) $justNums = preg_replace("/^0/", '',$justNums);
        $isPhoneNum = (strlen($justNums) == 10) ? true : false;
        return ['format'=>$isPhoneNum,'phone'=>'0'.$justNums];
    }




    public function add_option($key, $name)
    {
        $result = $this->connection->query("SELECT * FROM `tbl_option` WHERE `key_` = '$key'");
        if($result->num_rows){
            $option_id = $result->fetch_object()->id;
            $edit_list =
            [
                ['value',$name],

            ];
            $res = $this->edit_row($option_id, 'option', $edit_list);

        }else{

            $add_list = 
            [
                ['key_', $key],
                ['value', $name ]
            ];
            $res = $this->add_row($add_list, 'option', $check=true);
        }
    }

    public function is_option_exist($key)
    {
        $result = $this->connection->query("SELECT * FROM `tbl_option` WHERE`key_` = '$key' and `value` != '' ");
        if($result->num_rows){
            return $result->fetch_object()->value;
        }else{
            return false;
        }
    }

    public function delete_option($key)
    {
        $result = $this->connection->query("DELETE FROM `tbl_option` WHERE `key_` = '$key'");
        return $result;
    }


}



