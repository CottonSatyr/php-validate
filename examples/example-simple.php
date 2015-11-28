<?php
    /*
     * 1. Init db connection for DB checks
     * uncomment if you are going to use database checks
     * leave as is if there is no database checks
     */
     
    /*
     * $host = '';
     * $login = '';
     * $pass = '';
     * $dbname = '';
     * $dbh = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8', $login, $pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    */

    /*
     * 2. Add validation script
     * read about the rules inside the script
     */
    require('../validate.php');
    
    /*
     * 3. Add validation rule
     */
    $valid_rules_text = 'login->must_be_filled<-'
                       .'password->must_be_filled<-'
                       .'email_1|email 1->must_be_filled<-'
                       .'email_1|email 1->valid_email<-'
                       .'email_2|email 2->valid_email<-'
                       .'email_3|email 3->valid_email<-'
                       .'website->valid_url<-'
                       .'$->common_be_different|email_1@email_2@email_3<-'; 
    
    /*
     * 4. Getting fields values + err_text for validation message
     */
    isset($_POST['login']) ? $login = $_POST['login'] : $login = '';
    isset($_POST['password']) ? $password = $_POST['password'] : $password = '';
    isset($_POST['email_1']) ? $email_1 = $_POST['email_1'] : $email_1 = '';
    isset($_POST['email_2']) ? $email_2 = $_POST['email_2'] : $email_2 = '';
    isset($_POST['email_3']) ? $email_3 = $_POST['email_3'] : $email_3 = '';   
    isset($_POST['website']) ? $website = $_POST['website'] : $website = 'http://';        
    $err_res = '';
    
    /*
     * 5. Check if there was submit clicking. If so - start checking
     */ 
    if((isset($_POST['submit'])) && ($_POST['submit'] != ''))
    {
        /*
         * list with field that didn't pass through the checks
         */
        $fields_err = array();
        /*
         * run check
         */
        $err_res = InitCheck($valid_rules_text,$fields_err);
        
        if ($err_res === true)
            $err_text = 'Validation passed successfully!';
            else 
            {
                /*
                 * create nice error message
                 */
                foreach ($err_res as $key => $group)
                {
                    $err_text .= '<div class="alert alert-warning fade in margin-top-20">'
                               . '<a href="#" class="close" data-dismiss="alert" aria-label="'.$lang_global['alert_close'].'" title="'.$lang_global['alert_close'].'">&times;</a>'
                               . '<strong>ERROR</strong> - '.$key
                               . '<ul class="error_ul">';
                    foreach ($group as $k => $err)
                    {
                        $err_text .= '<li>'.$err.'</li>';
                    }
                    $err_text .= '</ul></div>';       
                }
            }
    }
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title></title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">

<style type="text/css">

td { vertical-align:top; }
.title { font-weight:bold; }


.alert {
    width: 100%;
    background-color: #fcf8e3;
    padding: 10px;
    margin: 10px;
    border: 1px solid #faebcc;
}

.alert_message {
    
}

.alert_message_det {
    color: #8a6d3b;
}
</style>

</head>

<body class="width: 100%;">
    <br>
    <div class="container">
        <div class="row">
            <?=$err_text;?>
        </div>    
    <!-- TOTAL STYPE CHANGE -->
    <form method="POST" action="example-simple.php">
        <input name="submit" type="hidden" value="1">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <p>
                    <strong>Register page</strong>                    
                </p>                
                <div class="form-group">
                    <!-- login -->
                    <div class="row">
                        <div class="col-md-2 <?=isset($fields_err['login'])?$fields_err['login']:'';?>">
                            <label class="control-label">Login</label>
                        </div>
                        <div class="col-md-10 <?=isset($fields_err['login'])?$fields_err['login']:'';?>">
                            <input class="form-control" type="text" name="login" value="<?=$login;?>">                            
                        </div>
                    </div>
                    <!-- pass -->
                    <div class="row">
                        <div class="col-md-2 <?=isset($fields_err['password'])?$fields_err['password']:'';?>">
                            <label class="control-label">Password</label>
                        </div>
                        <div class="col-md-10 <?=isset($fields_err['password'])?$fields_err['password']:'';?>">
                            <input type="password" name="password" class="form-control" value="<?=$password;?>">
                        </div>
                    </div>
                </div>    
                <div class="form-group">      
                    <!-- E-Mail 1 -->
                    <div class="row">
                        <div class="col-md-2 <?=isset($fields_err['email_1'])?$fields_err['email_1']:'';?>">
                            <label class="control-label">E-Mail 1</label>
                        </div>
                        <div class="col-md-10 <?=isset($fields_err['email_1'])?$fields_err['email_1']:'';?>">
                            <input class="form-control" type="text" name="email_1" value="<?=$email_1;?>">
                        </div>
                    </div>
                    <!-- E-Mail 2 -->
                    <div class="row">
                        <div class="col-md-2 <?=isset($fields_err['email_2'])?$fields_err['email_2']:'';?>">
                            <label class="control-label">E-Mail 2</label>
                        </div>
                        <div class="col-md-10 <?=isset($fields_err['email_2'])?$fields_err['email_2']:'';?>">
                            <input class="form-control" type="text" name="email_2" value="<?=$email_2;?>">
                        </div>
                    </div>
                    <!-- E-Mail 3 -->
                    <div class="row">
                        <div class="col-md-2 <?=isset($fields_err['email_3'])?$fields_err['email_3']:'';?>">
                            <label class="control-label">E-Mail 3</label>
                        </div>
                        <div class="col-md-10 <?=isset($fields_err['email_3'])?$fields_err['email_3']:'';?>">
                            <input class="form-control" type="text" name="email_3" value="<?=$email_3;?>">
                        </div>
                    </div>
                </div>    
                <div class="form-group">    
                    <!-- Website -->
                    <div class="row">
                        <div class="col-md-2 <?=isset($fields_err['website'])?$fields_err['website']:'';?>">
                            <label class="control-label">Website</label>
                        </div>
                        <div class="col-md-10 <?=isset($fields_err['website'])?$fields_err['website']:'';?>">
                            <input class="form-control" type="text" name="website" value="<?=$website;?>">
                        </div>
                    </div>
                </div>                     
                <div class="form-group">
                    <button type="submit" class="btn btn-lg btn-primary btn-block margin-top-35">Register</button>
                </div>     
            </div>    
        </div>    
    </form> 
    </div>
</body>
</html>
