<!DOCTYPE html>

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
     * read about the rule inside the script
     */
    require('validate.php');
    
    /*
     * 3. Add validation rule
     */
    $valid_rules_text = 
/*
 * The following things should be verified:
 * 1) Is group filled out? (required!)
 * 2) Do all of the selected groups [multiple select] exist? (only 1,2,3 are valid groups!)
 */
  'group->must_be_filled#in_list_array|1@2@3<-'
/*
 * 3) Is "salutation" filled out? (required)
 * 4) Does the salutation exist? (only 1,2,3 is a valid salutation!)
 * 5) Is only 1 salutation selected? (and not 2 or more, because this is not a multiple select!)*
 */ 
 .'salutation->must_be_filled#in_list_array|1@2@3#one_value_only<-'
/*
 * 6) Is "street" filled out? required!
 */
 .'street->must_be_filled<-'           
/* 
 * 7) Is "number" filled out? required!
 */ 
 .'number->must_be_filled<-'    
/* 
 * 8) Is "zip" filled out? required!
 */ 
 .'zip->must_be_filled<-' 
/* 
 * 9) Is "city" filled out? required!
 */ 
 .'city->must_be_filled<-'             
/* 
 * 10) Is country filled out? (required!)
 * 11) Is only 1 country selected? (and not 2 or more, because this is not a multiple select!)
 * 12) Does the country exist? (only 1,2,3 is a valid country!)
 */ 
 .'country->must_be_filled#one_value_only#in_list_array|1@2@3<-'
/* 
 * 10) Is state filled out? (required!)
 * 11) Is only 1 state selected? (and not 2 or more, because this is not a multiple select!)
 * 12) Does the state exist? (one 1 - 51 is a valid state!)
 * 13) Does the state match the country? If the country is germany, the state can be 1-16, if the country is Austria, the state can be 17-25, and if the country is Suiss, the state can be 26-51
 */ 
 .'state->must_be_filled#one_value_only#in_list_array|1}51#parent_in_list_array|1}16|state|1#parent_in_list_array|17}25|state|2#parent_in_list_array|26}51|state|3<-'
/* 
 * 14) Is the "phone 1 country code" (the dropdown) filled out? (required!)
 */ 
 .'phone_1_country_code|Country code for phone 1->must_be_filled<-'  
/* 
 * 15) Is "phone 1" (the text input) filled out? (required!)
 * 27) "phone 1" and "phone 2" can not have the same values!
 * 15.1) At least 4 digits!
 */ 
 .'phone_1|phone 1->must_be_filled#must_be_different|phone_2#at_least_4_digits<-' 
/* 
 * 16) "phone 2 country code" (the dropdown) is optional. BUT, if "phone 2" (the text field) was filled out, then the "phone 2 country code" (the dropdown) is a required field!!
 */ 
 .'phone_2_country_code|Country code for phone 2->fields_together|phone_2<-'
/* 
 * 17) "phone 2" (the text field) is optional. BUT, if "phone 2 country code" (the dropdown) was filled out, then the "phone 2" (the text field) is a required field!!
 * 17.1) At least 4 digits!
 */ 
 .'phone_2|phone 2->fields_together|phone_2_country_code#at_least_4_digits<-'
/* 
 * 18) "fax country code" (the dropdown) is optional. BUT, if "fax" (the text field) was filled out, then the "fax country code" (the dropdown) is a required field!!
 */ 
 .'fax_country_code|Country code for fax->fields_together|fax<-'
/* 
 * 19) "fax" (the text field) is optional. BUT, if "fax country code" (the dropdown) was filled out, then the "fax" (the text field) is a required field!!
 * 19.1) At least 4 digits.
 */
 .'fax->fields_together|fax_country_code#at_least_4_digits<-'
/* 
 * 20) "email 1" has to be filled out (required!)
 * 21) "email 1" has to be a valid email!
 * 26) part) "email 1", and "email 2" can not have the same values!
 */
 .'email_1|email 1->must_be_filled#valid_email#must_be_different|email_2#must_be_different|email_2<-'
/*  
 * 22) "email 2" is optional. BUT, if "email 2" was filled out, then it has to be a valid e-mail. 
 * 26) part) "email 2", and "email 3" can not have the same values!
 */ 
 .'email_2|email 2->valid_email#must_be_different|email_3<-'      
/* 
 * 23) "email 3" is optional. BUT, if "email 3" was filled out, then it has to be a valid e-mail. 
 */ 
 .'email_3|email 3->valid_email<-'
/* 
 * 24) Website is required!
 * 25) Website has to be a valid website!
 */ 
 .'website->must_be_filled#valid_url<-'
/* 
 * 27) "phone 1" and "phone 2" can not have the same values!
 */            
;
    /*
     * valid rule without comments:
     */
    $valid_rules_text = 'group->must_be_filled<-'
                       .'group->in_list_array|1@2@3<-'
                       .'salutation->must_be_filled<-'
                       .'salutation->in_list_array|1@2@3<-'
                       .'salutation->one_value_only<-'
                       .'street->must_be_filled<-'           
                       .'number->must_be_filled<-'    
                       .'zip->must_be_filled<-'
                       .'zip->valid_zip<-'
                       .'city->must_be_filled<-'             
                       .'country->must_be_filled<-'
                       .'country->one_value_only<-'
                       .'country->in_list_array|1@2@3<-'
                       .'state->must_be_filled<-'
                       .'state->one_value_only<-'
                       .'state->in_list_array|1}51<-'
                       .'state->parent_in_list_array|1}16|country|1<-'
                       .'state->parent_in_list_array|17}25|country|2<-'
                       .'state->parent_in_list_array|26}51|country|3<-'
                       .'phone_1_country_code|Country code for phone 1->must_be_filled<-'  
                       .'phone_1|Phone 1->must_be_filled<-'
                       .'phone_1|Phone 1->at_least_4_digits<-'
                       .'phone_1|Phone 1->no_0_start<-'
                       .'phone_2_country_code|Country code for phone 2->fields_together|phone_2<-'
                       .'phone_2|Phone 2->fields_together|phone_2_country_code<-'
                       .'phone_2|Phone 2->at_least_4_digits<-'
                       .'phone_2|Phone 2->no_0_start<-'
                       .'fax_country_code|Country code for fax->fields_together|fax<-'
                       .'fax->fields_together|fax_country_code<-'
                       .'fax->at_least_4_digits<-'
                       .'fax->no_0_start<-'
                       .'email_1|email 1->must_be_filled<-'
                       .'email_1|email 1->valid_email<-'
                       .'email_2|email 2->valid_email<-'
                       .'email_3|email 3->valid_email<-'
                       .'email_4|email 4->valid_email<-'
                       .'email_5|email 5->valid_email<-'
                       .'email_6|email 6->valid_email<-'
                       .'website->must_be_filled<-'
                       .'website->valid_url<-'
                       .'$->common_be_different|email_1@email_2@email_3@email_4@email_5@email_6<-'
                       .'$->common_be_different|phone_1_country_code+phone_1@phone_2_country_code+phone_2<-'; 
    
    /*
     * 4. Getting fields values + err_text for validation message
     */
    isset($_POST['group']) ? $group = $_POST['group'] : $group = array();
    isset($_POST['salutation']) ? $salutation = $_POST['salutation'] : $salutation = '';
    isset($_POST['co']) ? $co = $_POST['co'] : $co = '';
    isset($_POST['street']) ? $street = $_POST['street'] : $street = '';
    isset($_POST['number']) ? $number = $_POST['number'] : $number = '';
    isset($_POST['snd_address_line']) ? $snd_address_line = $_POST['snd_address_line'] : $snd_address_line = '';
    isset($_POST['zip']) ? $zip = $_POST['zip'] : $zip = '';
    isset($_POST['city']) ? $city = $_POST['city'] : $city = '';
    isset($_POST['country']) ? $country = $_POST['country'] : $country = '';
    isset($_POST['state']) ? $state = $_POST['state'] : $state = '';
    isset($_POST['comment']) ? $comment = $_POST['comment'] : $comment = '';
    isset($_POST['phone_1_country_code']) ? $phone_1_country_code = $_POST['phone_1_country_code'] : $phone_1_country_code = '';
    isset($_POST['phone_1']) ? $phone_1 = $_POST['phone_1'] : $phone_1 = '';    
    isset($_POST['phone_2_country_code']) ? $phone_2_country_code = $_POST['phone_2_country_code'] : $phone_2_country_code = '';
    isset($_POST['phone_2']) ? $phone_2 = $_POST['phone_2'] : $phone_2 = '';    
    isset($_POST['fax_country_code']) ? $fax_country_code = $_POST['fax_country_code'] : $fax_country_code = '';
    isset($_POST['fax']) ? $fax = $_POST['fax'] : $fax = '';
    isset($_POST['email_1']) ? $email_1 = $_POST['email_1'] : $email_1 = '';
    isset($_POST['email_2']) ? $email_2 = $_POST['email_2'] : $email_2 = '';
    isset($_POST['email_3']) ? $email_3 = $_POST['email_3'] : $email_3 = '';
    isset($_POST['email_4']) ? $email_4 = $_POST['email_4'] : $email_4 = '';
    isset($_POST['email_5']) ? $email_5 = $_POST['email_5'] : $email_5 = '';
    isset($_POST['email_6']) ? $email_6 = $_POST['email_6'] : $email_6 = '';    
    isset($_POST['website']) ? $website = $_POST['website'] : $website = 'http://';        
    $err_text = '';
    
    /*
     * select lists
     * a) group
     * b) salutaion
     * c) country
     * d) state
     * e) phone country codes
     * 
     * But they all must be selected from database!
     */
    $group_array = array('' => '---',
                         '1'  => 'First',
                         '2'  => 'Second',
                         '3'  => 'Third',
                         '4'  => 'Don`t exist'
    );
    $salutation_array = array('' => '---',
                              '1'  => 'Mr.',
                              '2'  => 'Mrs.',
                              '3'  => 'Company',
                              '4'  => 'Don`t exist'
                    
    );
    $country_array = array('' => '---',
                          '1'  => 'Germany',
                          '2'  => 'Austria',
                          '3'  => 'Suiss',
                          '4'  => 'Don`t exist'
        
    );
    $state_array = array('' => array('name' => '---', 'class' => ''),
                         '1' => array('name' => 'Baden-Württemberg', 'class' => '1'),
                         '2' => array('name' => 'Bayern', 'class' => '1'),
                         '3' => array('name' => 'Berlin', 'class' => '1'),
                         '4' => array('name' => 'Brandenburg', 'class' => '1'),
                         '5' => array('name' => 'Bremen', 'class' => '1'),
                         '6' => array('name' => 'Hamburg', 'class' => '1'),
                         '7' => array('name' => 'Hessen', 'class' => '1'),
                         '8' => array('name' => 'Mecklenburg-Vorpommern', 'class' => '1'),
                         '9' => array('name' => 'Niedersachsen', 'class' => '1'),
                         '10' => array('name' => 'Nordrhein-Westfalen', 'class' => '1'),
                         '11' => array('name' => 'Rheinland-Pfalz', 'class' => '1'),
                         '12' => array('name' => 'Saarland', 'class' => '1'),
                         '13' => array('name' => 'Sachsen', 'class' => '1'),
                         '14' => array('name' => 'Sachsen-Anhalt', 'class' => '1'),
                         '15' => array('name' => 'Schleswig-Holstein', 'class' => '1'),
                         '16' => array('name' => 'Thüringen', 'class' => '1'),
                         '17' => array('name' => 'Burgenland', 'class' => '2'),
                         '18' => array('name' => 'Kärnten', 'class' => '2'),
                         '19' => array('name' => 'Niederösterreich', 'class' => '2'),
                         '20' => array('name' => 'Oberösterreich', 'class' => '2'),
                         '21' => array('name' => 'Salzburg', 'class' => '2'),
                         '22' => array('name' => 'Steiermark', 'class' => '2'),
                         '23' => array('name' => 'Tirol', 'class' => '2'),
                         '24' => array('name' => 'Vorarlberg', 'class' => '2'),
                         '25' => array('name' => 'Wien', 'class' => '2'),
                         '26' => array('name' => 'Aargau', 'class' => '3'),
                         '27' => array('name' => 'Appenzell Ausserrhoden', 'class' => '3'),
                         '28' => array('name' => 'Appenzell Innerrhoden', 'class' => '3'),
                         '29' => array('name' => 'Basel-Landschaft', 'class' => '3'),
                         '30' => array('name' => 'Basel-Stadt', 'class' => '3'),
                         '31' => array('name' => 'Bern', 'class' => '3'),
                         '32' => array('name' => 'Freiburg', 'class' => '3'),
                         '33' => array('name' => 'Genf', 'class' => '3'),
                         '34' => array('name' => 'Glarus', 'class' => '3'),
                         '35' => array('name' => 'Graubünden', 'class' => '3'),
                         '36' => array('name' => 'Jura', 'class' => '3'),
                         '37' => array('name' => 'Luzern', 'class' => '3'),
                         '38' => array('name' => 'Neuenburg', 'class' => '3'),
                         '39' => array('name' => 'Nidwalden', 'class' => '3'),
                         '40' => array('name' => 'Obwalden', 'class' => '3'),
                         '41' => array('name' => 'Schaffhausen', 'class' => '3'),
                         '42' => array('name' => 'Schwyz', 'class' => '3'),
                         '43' => array('name' => 'Solothurn', 'class' => '3'),
                         '44' => array('name' => 'St. Gallen', 'class' => '3'),
                         '45' => array('name' => 'Tessin', 'class' => '3'),
                         '46' => array('name' => 'Thurgau', 'class' => '3'),
                         '47' => array('name' => 'Uri', 'class' => '3'),
                         '48' => array('name' => 'Waadt', 'class' => '3'),
                         '49' => array('name' => 'Wallis', 'class' => '3'),
                         '50' => array('name' => 'Zug', 'class' => '3'),
                         '51' => array('name' => 'Zürich', 'class' => '3'),
                         '52' => array('name' => 'Don`t exist', 'class' => '3')               
    );
    $phone_country_code_array = array('' => '- - - ',
                                     '1' => 'DE +49',
                                     '2' => 'AT +43',
                                     '3' => 'CH +41'
            
    );
    
    /*
     * get previous selected values
     */
    //----- group
    $group_text = '<select class="form-control" name="group[]" multiple>';
    foreach($group_array as $k => $v)
    {
        $group_text .= '<option '.(in_array($k,$group)?'selected':'').' value="'.$k.'">'.$v.'</option>';
    }
    $group_text .= '</select>';
    //----- salutation
    $salutation_text = '<select class="form-control" name="salutation">';
    foreach($salutation_array as $k => $v)
    {
        $salutation_text .= '<option '.($k==$salutation?'selected':'').' value="'.$k.'">'.$v.'</option>';
    }
    $salutation_text .= '</select>';
    //----- country
    $country_text = '<select class="form-control" name="country">';
    foreach($country_array as $k => $v)
    {
        $country_text .= '<option '.($k==$country?'selected':'').' value="'.$k.'">'.$v.'</option>';
    }
    $country_text .= '</select>';
    //----- state
    $state_text = '<select class="form-control" name="state">';
    foreach($state_array as $k => $v)
    {
        $state_text .= '<option '.($k==$state?'selected':'').' value="'.$k.'" '.(strlen($v['class'])>0?'class="'.$v['class'].'"':'').'>'.$v['name'].'</option>';
    }
    $state_text .= '</select>';
    //----- phone code 1
    $phone_1_country_code_text = '<select class="form-control" name="phone_1_country_code">';
    foreach($phone_country_code_array as $k => $v)
    {
        $phone_1_country_code_text .= '<option '.($k==$phone_1_country_code?'selected':'').' value="'.$k.'">'.$v.'</option>';
    }
    $phone_1_country_code_text .= '</select>';
    //----- phone code 2
    $phone_2_country_code_text = '<select class="form-control" name="phone_2_country_code">';
    foreach($phone_country_code_array as $k => $v)
    {
        $phone_2_country_code_text .= '<option '.($k==$phone_2_country_code?'selected':'').' value="'.$k.'">'.$v.'</option>';
    }
    $phone_2_country_code_text .= '</select>';
    //----- fax code
    $fax_country_code_text = '<select class="form-control" name="fax_country_code">';
    foreach($phone_country_code_array as $k => $v)
    {
        $fax_country_code_text .= '<option '.($k==$fax_country_code?'selected':'').' value="'.$k.'">'.$v.'</option>';
    }
    $fax_country_code_text .= '</select>';
    
    
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
        $err_text = InitCheck($valid_rules_text,$fields_err);
        
        if ($err_text === true)
            $err_text = '';
    }

?>

<html lang="de">
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
    <form method="POST" action="form_bootstrap.php">
        <input name="submit" type="hidden" value="1">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <p>
                    <strong>Stammdaten</strong>                    
                </p>                
                <div class="form-group">
                    <!-- group -->
                    <div class="row">
                        <div class="col-md-2 <?=isset($fields_err['group'])?$fields_err['group']:'';?>">
                            <label class="control-label">Group</label>
                        </div>
                        <div class="col-md-10 <?=isset($fields_err['group'])?$fields_err['group']:'';?>">
                            <?=$group_text;?>
                        </div>
                    </div>
                    <!-- salutation -->
                    <div class="row">
                        <div class="col-md-2 <?=isset($fields_err['salutation'])?$fields_err['salutation']:'';?>">
                            <label class="control-label">Salutation</label>
                        </div>
                        <div class="col-md-10 <?=isset($fields_err['salutation'])?$fields_err['salutation']:'';?>">
                            <?=$salutation_text;?>
                        </div>
                    </div>
                    <!-- C/O -->
                    <div class="row">
                        <div class="col-md-2 <?=isset($fields_err['co'])?$fields_err['co']:'';?>">
                            <label class="control-label">C/O</label>
                        </div>
                        <div class="col-md-10 <?=isset($fields_err['co'])?$fields_err['co']:'';?>">
                            <input class="form-control" type="text" name="co" value="<?=$co;?>">                            
                        </div>
                    </div>
                    <!-- Street -->
                    <div class="row">
                        <div class="col-md-2 <?=isset($fields_err['street'])?$fields_err['street']:'';?>">
                            <label class="control-label">Street</label>
                        </div>
                        <div class="col-md-10 <?=isset($fields_err['street'])?$fields_err['street']:'';?>">
                            <input type="text" name="street" class="form-control" value="<?=$street;?>">
                        </div>
                    </div>
                    <!-- Number -->
                    <div class="row">
                        <div class="col-md-2 <?=isset($fields_err['number'])?$fields_err['number']:'';?>">
                            <label class="control-label">Number</label>
                        </div>
                        <div class="col-md-10 <?=isset($fields_err['number'])?$fields_err['number']:'';?>">
                            <input type="text" name="number" class="form-control" value="<?=$number;?>">                            
                        </div>
                    </div>
                    <!-- 2nd address line -->
                    <div class="row">
                        <div class="col-md-2 <?=isset($fields_err['snd_address_line'])?$fields_err['snd_address_line']:'';?>">
                            <label class="control-label">2nd addr.line</label>
                        </div>
                        <div class="col-md-10 <?=isset($fields_err['snd_address_line'])?$fields_err['snd_address_line']:'';?>">
                            <input type="text" name="snd_address_line" class="form-control" value="<?=$snd_address_line;?>">
                        </div>
                    </div>
                    <!-- ZIP -->
                    <div class="row">
                        <div class="col-md-2 <?=isset($fields_err['zip'])?$fields_err['zip']:'';?>">
                            <label class="control-label">ZIP</label>
                        </div>
                        <div class="col-md-10 <?=isset($fields_err['zip'])?$fields_err['zip']:'';?>">
                            <input type="text" name="zip" class="form-control" value="<?=$zip;?>">
                        </div>
                    </div>
                    <!-- City -->
                    <div class="row">
                        <div class="col-md-2 <?=isset($fields_err['city'])?$fields_err['city']:'';?>">
                            <label class="control-label">City</label>
                        </div>
                        <div class="col-md-10 <?=isset($fields_err['city'])?$fields_err['city']:'';?>">
                            <input type="text" name="city" class="form-control" value="<?=$city;?>">
                        </div>
                    </div>
                    <!-- Country -->
                    <div class="row">
                        <div class="col-md-2 <?=isset($fields_err['country'])?$fields_err['country']:'';?>">
                            <label class="control-label">Country</label>
                        </div>
                        <div class="col-md-10 <?=isset($fields_err['country'])?$fields_err['country']:'';?>">
                            <?=$country_text;?>
                        </div>
                    </div>
                    <!-- State -->
                    <div class="row">
                        <div class="col-md-2 <?=isset($fields_err['state'])?$fields_err['state']:'';?>">
                            <label class="control-label">State</label>
                        </div>
                        <div class="col-md-10 <?=isset($fields_err['state'])?$fields_err['state']:'';?>">
                            <?=$state_text;?>
                            <!--<select class="form-control" name="state"><option value="">---</option><option value="1" class="1">Baden-W?rttemberg</option><option value="2" class="1">Bayern</option><option value="3" class="1">Berlin</option><option value="4" class="1">Brandenburg</option><option value="5" class="1">Bremen</option><option value="6" class="1">Hamburg</option><option value="7" class="1">Hessen</option><option value="8" class="1">Mecklenburg-Vorpommern</option><option value="9" class="1">Niedersachsen</option><option value="10" class="1">Nordrhein-Westfalen</option><option value="11" class="1">Rheinland-Pfalz</option><option value="12" class="1">Saarland</option><option value="13" class="1">Sachsen</option><option value="14" class="1">Sachsen-Anhalt</option><option value="15" class="1">Schleswig-Holstein</option><option value="16" class="1">Th?ringen</option><option value="17" class="2">Burgenland</option><option value="18" class="2">K?rnten</option><option value="19" class="2">Nieder?sterreich</option><option value="20" class="2">Ober?sterreich</option><option value="21" class="2">Salzburg</option><option value="22" class="2">Steiermark</option><option value="23" class="2">Tirol</option><option value="24" class="2">Vorarlberg</option><option value="25" class="2">Wien</option><option value="26" class="3">Aargau</option><option value="27" class="3">Appenzell Ausserrhoden</option><option value="28" class="3">Appenzell Innerrhoden</option><option value="29" class="3">Basel-Landschaft</option><option value="30" class="3">Basel-Stadt</option><option value="31" class="3">Bern</option><option value="32" class="3">Freiburg</option><option value="33" class="3">Genf</option><option value="34" class="3">Glarus</option><option value="35" class="3">Graub?nden</option><option value="36" class="3">Jura</option><option value="37" class="3">Luzern</option><option value="38" class="3">Neuenburg</option><option value="39" class="3">Nidwalden</option><option value="40" class="3">Obwalden</option><option value="41" class="3">Schaffhausen</option><option value="42" class="3">Schwyz</option><option value="43" class="3">Solothurn</option><option value="44" class="3">St. Gallen</option><option value="45" class="3">Tessin</option><option value="46" class="3">Thurgau</option><option value="47" class="3">Uri</option><option value="48" class="3">Waadt</option><option value="49" class="3">Wallis</option><option value="50" class="3">Zug</option><option value="51" class="3">Z?rich</option><option value="52" class="3">Don't exist</option></select>-->
                        </div>
                    </div>                    
                </div>    
                <div class="form-group">
                    <!-- Comment -->
                    <div class="row">
                        <div class="col-md-2 <?=isset($fields_err['comment'])?$fields_err['comment']:'';?>">
                            <label class="control-label">Comment</label>
                        </div>
                        <div class="col-md-10 <?=isset($fields_err['comment'])?$fields_err['comment']:'';?>">
                            <textarea class="form-control" name="comment"><?=$comment;?></textarea>                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">    
                <p>
                    <strong>Kontaktdaten</strong>                    
                </p>                
                <div class="form-group">
                    <!-- Phone 1 -->
                    <div class="row">
                        <div class="col-md-2 <?=isset($fields_err['phone_1_country_code']) || isset($fields_err['phone_1'])?'has-error':'';?>">
                            <label class="control-label">Phone 1</label>
                        </div>
                        <div class="col-md-3 <?=isset($fields_err['phone_1_country_code'])?$fields_err['phone_1_country_code']:'';?>">
                            <?=$phone_1_country_code_text;?>
                            <!--<select class="form-control" name="phone_1_country_code"><option value="">- - -</option><option value="1">DE +49</option><option value="2">AT +43</option><option value="3">CH +41</option></select>-->
                        </div>
                        <div class="col-md-7 <?=isset($fields_err['phone_1'])?$fields_err['phone_1']:'';?>">
                            <input class="form-control" type="text" name="phone_1" value="<?=$phone_1;?>">
                        </div>
                    </div>
                    <!-- Phone 2 -->
                    <div class="row">
                        <div class="col-md-2 <?=isset($fields_err['phone_2_country_code']) || isset($fields_err['phone_2'])?$fields_err['group']:'';?>">
                            <label class="control-label">Phone 2</label>
                        </div>
                        <div class="col-md-3 <?=isset($fields_err['phone_2_country_code'])?$fields_err['phone_2_country_code']:'';?>">
                            <?=$phone_2_country_code_text;?>
                            <!--<select class="form-control" name="phone_2_country_code"><option value="">- - -</option><option value="1">DE +49</option><option value="2">AT +43</option><option value="3">CH +41</option></select>-->
                        </div>
                        <div class="col-md-7 <?=isset($fields_err['phone_2'])?$fields_err['phone_2']:'';?>">
                            <input class="form-control" type="text" name="phone_2" value="<?=$phone_2;?>">
                        </div>
                    </div>
                    <!-- Fax -->
                    <div class="row">
                        <div class="col-md-2 <?=isset($fields_err['fax_country_code'])||isset($fields_err['fax'])?$fields_err['group']:'';?>">
                            <label class="control-label">Fax</label>
                        </div>
                        <div class="col-md-3 <?=isset($fields_err['fax_country_code'])?$fields_err['fax_country_code']:'';?>">
                            <?=$fax_country_code_text;?>
                            <!--<select class="form-control" name="fax_country_code"><option value="">- - -</option><option value="1">DE +49</option><option value="2">AT +43</option><option value="3">CH +41</option></select>-->
                        </div>
                        <div class="col-md-7 <?=isset($fields_err['fax'])?$fields_err['fax']:'';?>">
                            <input class="form-control" type="text" name="fax" value="<?=$fax;?>">
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
                    <!-- E-Mail 4 -->
                    <div class="row">
                        <div class="col-md-2 <?=isset($fields_err['email_4'])?$fields_err['email_4']:'';?>">
                            <label class="control-label">E-Mail 4</label>
                        </div>
                        <div class="col-md-10 <?=isset($fields_err['email_4'])?$fields_err['email_4']:'';?>">
                            <input class="form-control" type="text" name="email_4" value="<?=$email_4;?>">
                        </div>
                    </div>
                    <!-- E-Mail 5 -->
                    <div class="row">
                        <div class="col-md-2 <?=isset($fields_err['email_5'])?$fields_err['email_5']:'';?>">
                            <label class="control-label">E-Mail 5</label>
                        </div>
                        <div class="col-md-10 <?=isset($fields_err['email_5'])?$fields_err['email_5']:'';?>">
                            <input class="form-control" type="text" name="email_5" value="<?=$email_5;?>">
                        </div>
                    </div>
                    <!-- E-Mail 6 -->
                    <div class="row">
                        <div class="col-md-2 <?=isset($fields_err['email_6'])?$fields_err['email_6']:'';?>">
                            <label class="control-label">E-Mail 6</label>
                        </div>
                        <div class="col-md-10 <?=isset($fields_err['email_6'])?$fields_err['email_6']:'';?>">
                            <input class="form-control" type="text" name="email_6" value="<?=$email_6;?>">
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
                    <button type="submit" class="btn btn-lg btn-primary btn-block margin-top-35">Save</button>
                </div>     
            </div>    
        </div>    
    </form> 
    </div>
</body>
</html>
