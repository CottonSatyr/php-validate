<?php

/* 
 * validate.php
 * ------------------------------------------------------
 * Form fields validation library
 * created by Volodymyr Korotun, 11.2015
 * ------------------------------------------------------
 * it's used for validation of various form
 * ------------------------------------------------------
 * change log:
 * 
 * [17.11.2015]
 * >>> you can now use `+` to add different fields values
 *     for `common_be_different` checks.
 *     E.g. field1+field2@field3+field4 mean that value in
 *     field1 cincatinated with value in field2 must be
 *     different with value in field3 cincatinated with 
 *     value in field4
 * 
 * [17.11.2015]
 * >>> add common check `common_be_different` which don't 
 *     apply to any specific field. You should use `@` in 
 *     valid rule string instread of field name.
 * >>> add validation groups. Every validation is in one of
 *    the validation group. If some error occured all
 *    validation will be gathered into each group.
 * >>> add new type of check: at_least_4_digits.
 * 
 * [16.11.2015]
 * >>> add new type of check: one_value_only, must_be_different,
 *     parent_in_list_array
 * >>> add checking for array-based values such as multiple option
 */

    /*
     * 1) Validation groups.
     * --------------------------------------------------
     * each error than can occur during validation should
     * have certain group. In the end of check all validation
     * error messages will be get into the groups.
     * --------------------------------------------------
     *  text - is a group error text
     *  sort - is an order of error groups in error message
     */
    $valid_class = array(
                         'requiered'    => array('text' => 'you have not completed all mandatory fields!',
                                                 'sort' => 1),
                         'correct'      => array('text' => 'please pay attention to the correct format of the input!',
                                                 'sort' => 2),
                         'both_filled'  => array('text' => 'when connected optional fields either both fields or no field must be filled.',
                                                 'sort' => 3),
                         'unique'       => array('text' => 'values in the same fields must not be repeated!',
                                                 'sort' => 4),
                         'together'     => array('text' => 'values are illegal according to the previous entered values!',
                                                 'sort' => 5),
                         'invalid'      => array('text' => 'entered values are illegal!',
                                                 'sort' => 6)
                        );

    /*
     * 1) List of validations.
     * --------------------------------------------------
     * such as only number, value in list and so on.
     * We used error message here too.
     * %field% will be replaced with the field name soon.
     * %val1% will be replaced with value parameter soon.
     * %val2% will be replaced with value parameter soon.
     * --------------------------------------------------
     * If you create new validate rule you should add it's
     * name and error text here
     * --------------------------------------------------
     * Description of current checkings:
     * 
     *      must_be_filled      - field must contain any data.
     *      digits_only         - only digits must be used in
     *                            field.
     *      in_list_db          - entered value must be in 
     *                            concrete db table.column. DB
     *                            table and column are parameters.
     *      in_list_array       - check if eventered value is in
     *                            param1 list. If value is array than
     *                            each array value should be in param1 
     *                            list.
     *      valid_email         - field must be e-mail.
     *      valid_url           - field must be URL (e.g. 
     *                            start with http://).
     *      fields_together     - if checking field is filled than
     *                            field from parameter must 
     *                            be filled too.
     *      least_length        - entered value for field must
     *                            contain at least X characters.
     *      no_0_start          - there couldn't be 0 in the 
     *                            beginning of field value.
     *      one_value_only      - Only one value. No array could be
     *                            chosen.
     *      must_be_different   - entered value for field must be 
     *                            different with value in parameter
     *                            field.
     *      parent_in_list_array- entered values for field must match
     *                            the certain values for certain parent 
     *                            field. Only plain values are used.
     *                            No arrays!
     *      at_least_4_digits   - entered value must contain at least
     *                            4 digits! Only plain values are used.
     *                            No arrays!
     * 
     *      common_be_different - check which are not signed to any field.
     *                            It have to be signed to $ field name.
     *                            It make checks same as `must_be_different`
     *                            check.
     *      valid_zip           - entered value must be valid zip code
     *                            for selected country. If no country selected
     *                            then we check digits only.
     * 
     * the list of parameter which are used now:
     *    for `in_list` check:
     *       param1     - database table name for checking
     *       param2     - name of table field for checking
     *    for `in_list_array` check:
     *       param1     - list of avaliable values with `@` delimiter.
     *                    Also you can use `}` to fill the range.
     *                    E.g. 1}51
     *    for `fields_together` check:
     *       param1     - name of field which should be filled
     *                    with checking ones
     *    for `least_length` check:
     *       param1     - the least length of the field
     *    for `must_be_different` check:
     *       param1     - name of field which should be filled
     *                    with checking ones
     *    for `parent_in_list_array` check:
     *       param1     - list of checking values that depend on 
     *                    parent field's values. Could used list of values
     *                    with `@` delimiter. Also you can use `}` 
     *                    to fill the range. E.g. 1}51. Also plain values 
     *                    can used.
     *       param2     - name of parent field to be checked
     *       param3     - list of value of parent field to be checked
     *                    with `@` delimiter. Also you can use `}` 
     *                    to fill the range. E.g. 1}51. Also plain values 
     *                    can used.
     *    for `common_be_different` check:
     *       param1     - list of checking fields with `@` delimiter. You can 
     *                    join fields with `+` symbol to check values of 
     *                    several fields. E.g. field1+field2@field3+field4
     *                    mean that value in field1 concatinated with value
     *                    of field2 must be different with the value in field3 
     *                    concatinated with value of field4.
     * 
     */     
    $valid_group = array(
                          'must_be_filled'      => array('err_txt' => 'The field "%field%" must be filled!',
                                                         'group'   => 'requiered'),
                          'digits_only'         => array('err_txt' => 'Only digits must be used in field "%field%"!',
                                                         'group'   => 'correct'),
                          'in_list_db'          => array('err_txt' => 'There is invalid value for field "%field%"!',
                                                         'group'   => 'invalid'),
                          'in_list_array'       => array('err_txt' => 'There is invalid value for field "%field%"!',
                                                         'group'   => 'invalid'),
                          'valid_email'         => array('err_txt' => 'Entered value in the field "%field%" is not a valid e-mail address!',
                                                         'group'   => 'correct'),
                          'valid_url'           => array('err_txt' => 'Entered value in the field "%field%" is not a valid URL!',
                                                         'group'   => 'correct'),
                          'fields_together'     => array('err_txt' => 'You should fill "%val1%" field if there is some data in "%field%" field!',
                                                         'group'   => 'both_filled'),
                          'least_length'        => array('err_txt' => 'Entered value in the field "%field%" must be at least %val1% characters!',
                                                         'group'   => 'correct'),
                          'no_0_start'          => array('err_txt' => 'Entered value in the field "%field%" could not start with 0!',
                                                         'group'   => 'correct'),
                          'one_value_only'      => array('err_txt' => 'Only one value for the field "%field%" could be selected!',
                                                         'group'   => 'invalid'),
                          'must_be_different'   => array('err_txt' => 'The value for field "%field%" must be different with "%val1%" field value!',
                                                         'group'   => 'unique'),
                          'parent_in_list_array'=> array('err_txt' => 'Entered value for field "%field%" are illegal for entered value in the field "%val2%"!',
                                                         'group'   => 'together'),
                          'at_least_4_digits'   => array('err_txt' => 'Entered value for field "%field%" must contain at least 4 digits!',
                                                         'group'   => 'correct'),
                          'common_be_different' => array('err_txt' => 'The values entered in fields "%field%" and "%val1%" must be different!',
                                                         'group'   => 'unique'),
                          'valid_zip'         => array('err_txt' => 'Entered value in the field "%field%" is not a valid for selected country!',
                                                         'group'   => 'correct')
                        );
    
    
    
    /*
     * 2) List of binds between form field and checking in text form
     * --------------------------------------------------
     * filling rule:
     * '<FIELD NAME 1>|[ERROR FIELD NAME 1] -> <VALIDATION NAME 1>|[PARAM 1]|[PARAM 2]|...|[PARAM N]#<VALIDATION NAME 2>|[PARAM 1]|[PARAM 2]|...|[PARAM N]#...#<VALIDATION NAME N>|[PARAM 1]|[PARAM 2]|...|[PARAM N] <- '
     * where
     *    <FIELD NAME X>        - name of field which are getting from POST/GET form;
     *    [ERROR FIELD NAME 1]  - name of field which used in error message. If empty
     *                            then original field name will be used;
     *    <VALIDATION NAME Y>   - name of validation from 1);
     *    <PARAM Z>             - name of parameter which could be needed in validation;
     *    
     * field name and it`s error name are separate with `|` symbol
     * all validation of field must be quoted by `->` and `<-` strings.
     * validations of field are separated with `#` symbol.
     * You can use the 
     * 
     * parameters of validation are separated with `|` symbol.
     * 
     * the list of parameter which are used now:
     *    for `in_list_db` check:
     *       param1     - database table name for checking
     *       param2     - name of table field for checking
     *    for `in_list_array` check:
     *       param1     - list of avaliable values with `@` delimiter.
     *                    Also you can use `}` to fill the range.
     *                    E.g. 1}51
     *    for `fields_together` check:
     *       param1     - name of field which should be filled
     *                    with checking ones
     *    for `least_length` check:
     *       param1     - the least length of the field
     *    for `must_be_different` check:
     *       param1     - name of field which should be filled
     *                    with checking ones
     *    for `parent_in_list_array` check:
     *       param1     - checking value
     *       param2     - name of parent field to be checked
     *       param3     - list of value of parent field to be checked
     *                    with `@` delimiter. Also you can use `}` 
     *                    to fill the range. E.g. 1}51
     * 
     * example is below:
     *
     * $valid_rules_text = 'group|Group->must_be_filled#in_list|`contact_groups`|`id`<-'.
     *                     'salutation|Salutation->must_be_filled#in_list|`salutations`|`id`<-'.
     *                     'zip->must_be_filled#digits_only<-'.
     *                     'company_name|Company name->must_be_filled<-'.
     *                     'phone_1|Phone number 1->no_0_start#least_length|6#fields_together|phone_1_country_code<-'.
     *                     'phone_1_country_code|Country code of phone number 1->in_list|`country_codes`|`id`#fields_together|phone_1<-'.
     *                     'email_1|Email 1->must_be_filled#valid_email<-'.
     *                     'website->must_be_filled#valid_url<-'; 
     */
    
    /*
     * List of existings check errors.
     * --------------------------------------------------
     * It's used to create readable error text soon
     */
    $error = array();    
    
    /*
     * Function which used in checking code. 
     * You should run this function in external code
     * --------------------------------------------------
     * $valid_rules_text    - list of fields which are bind to 
     *                        validations 2)
     * &$fields_err          - list of fields that didn't pass
     *                        the checks (out)
     * return               - error or fine message
     */
    function InitCheck($valid_rules_text,&$fields_err)
    {
        global $error;
        global $valid_group;
        global $valid_class;
        
        /*
         * Transform list of binds between form field and 
         * checking in array form from text form
         * --------------------------------------------------
         * $text            - text version of binding 
         * &$out_val_arr    - array version of binding (out)
         * &$out_names_arr  - array with field names which will be
         *                    used in error message    (out) 
         * --------------------------------------------------
         * additional function
         */
        function Rules_text_array($text,&$out_val_arr,&$out_names_arr)
        {
            $res = array();
            $f_name_prev = '';
            
            // getting list of field to validate with details
            $fields_arr = explode('<-', $text);
            
            // run throw all fields
            for ($ii=0;$ii<count($fields_arr)-1;$ii++)
            {                                
                $f_name_tmp = substr($fields_arr[$ii], 0, strpos($fields_arr[$ii], '->'));
                // check if there error alias
                if (strpos($f_name_tmp, '|') > 0)
                {
                    $f_name = substr($f_name_tmp, 0, strpos($f_name_tmp, '|')); 
                    $f_name_alt = substr($f_name_tmp, strpos($f_name_tmp, '|')+1);
                }
                else 
                {
                    $f_name = $f_name_tmp;
                    $f_name_alt = $f_name;
                }
                
                if ($f_name_prev <> $f_name)
                    $row = array();
                
                // filling $out_names_arr array
                $out_names_arr[$f_name] = $f_name_alt;                                

                $val_text = substr($fields_arr[$ii], strpos($fields_arr[$ii], '->')+2); 
                // run throw all validation of current field
                $val_arr = explode('#', $val_text);   
                
                #var_dump($f_name_alt);var_dump($val_arr);
                
                for ($jj=0;$jj<count($val_arr);$jj++)
                {      
                    $subrow = array();
                    $param_arr = array();
                    if (strpos($val_arr[$jj], '|') > 0)
                    {
                        // we have parameters for this validation
                        $val_name = substr($val_arr[$jj], 0, strpos($val_arr[$jj], '|'));
                        $param_text = substr($val_arr[$jj], strpos($val_arr[$jj], '|')+1);
                        $param_arr = explode('|', $param_text);
                    }   
                    else 
                    {
                        $val_name = $val_arr[$jj];                    
                    }                    
                    // adding validation name to result
                    $subrow['valid'] = $val_name;
                    // addining paramteres to result
                    if (count($param_arr) > 0)
                        for ($mm=0;$mm<count($param_arr);$mm++)
                            $subrow['param'.($mm+1)] = $param_arr[$mm];                   

                    $row[] = $subrow;
                    #var_dump($f_name_alt);var_dump($subrow);
                }
                $res[$f_name] = $row;
                $f_name_prev = $f_name;
            }
            // filling validation array
            $out_val_arr = $res;
        }
        
        /*
         * Function makes check if entered error is already
         * in our error list
         * --------------------------------------------------
         * $field       - name of form field
         * $error_name  - name of check
         * return       - 1/0
         * --------------------------------------------------
         * additional function
         * --------------------------------------------------
         * we checked if some error is already in error array
         * we use it for cases when two errors occured and
         * don't need to show them both, only more important 
         * to show
         */
        function CheckErrorIn($field, $error_name)
        {
            global $error;  
            
            if (isset($error[$field]))
            {
                $check = 0;
                foreach($error[$field] as $k => $v)
                {
                    if (isset($v['name']) && $v['name'] == $error_name)
                        $check = 1;
                }
                if ($check == 1)
                    return 1;
            }
            return 0;
        }
        
        /*
         * List of binds between form field and checking in array form
         */
        $field_valid_rules = array();  
        $field_names = array();
        if (isset($valid_rules_text))
            Rules_text_array($valid_rules_text,$field_valid_rules,$field_names); 
        /*
         * run the checks through the list of fields
         */
        foreach($field_valid_rules as $field => $check_list)
        {
            /*
             * now run throw the chech lists for every field
             */
            foreach($check_list as $k => $v)
            {
                /*
                 * now run throw the chech lists for every field
                 * if we have a problem - we fill error array with parameters
                 * to show it to the user
                 */
                global $$field; 
                
                if (!MakeCheck($$field,$v,$field))
                {  
                    $tmp_row = array();
                    $tmp_row['name'] = $v['valid'];
                    // add param1 to the error list for error message
                    if (isset($v['param1']))
                        // we try to add correct field name if param1 is field name
                        $tmp_row['param1'] = $v['param1'];
                    // add param2 to the error list for error message
                    if (isset($v['param2']))
                        // we try to add correct field name if param1 is field name
                        $tmp_row['param2'] = $v['param2'];        
                    $error[$field][] = $tmp_row;
                }
            }
        }
        /*
         * now run a check for a common list
         */
//        foreach($common_valid_rules as $field => $check_list)
//        {
//            null;
//        }

        // create error message if we have some errors
        if (count($error) > 0)
        {   
            /*
             * Function create error message for common `@`
             * validation
             * --------------------------------------------------
             * $params      - list of parameters of common
             *                validation
             * $fields_err  - list of highligted fields (out)
             * return       - two dimension array with list 
             *                of matched fields. 
             *                E.g. if there is the same values
             *                for field1 and field2 and also
             *                the same field3 and field4 (but
             *                it's different to previous pair)
             *                the result would be:
             *                     [0] => [field1,field2],
             *                     [1] => [field3,field4] 
             * --------------------------------------------------
             * additional function
             * --------------------------------------------------
             * There is some different algoryth of making
             * message in common checks. We have to create a
             * message due to a parameter list
             */
            function GetMessageCommonCheck($params,&$fields_err)
            {
                global $field_names;

                $res_mess = '';
                
                $fields_val_tmp = explode('@',$params);
                /*
                 * we get array with [field value]->list of fields that 
                 * contain that value
                 */           
                foreach($fields_val_tmp as $k => $v)
                {
                    if (strpos($v,'+') > 0)
                    {
                        /*
                         * we should checked the joined values
                         */
                        $fields_several_tmp = explode('+',$v);
                        $tmp = '';
                        foreach($fields_several_tmp as $k_sev => $v_sev)
                        {
                            global $$v_sev;
                            $tmp .= $$v_sev;
                        }
                    }
                    else 
                    {
                        global $$v;  
                        $tmp = $$v;
                    }
                    if (strlen($tmp) > 0)
                        $fields_val_uniq[$tmp][] = $v;
                }
                
                /*
                 * if some value contain more than 1 field we
                 * have validation error
                 */
                foreach($fields_val_uniq as $k => $v)
                {
                    if (count($v) > 1)
                    {
                        $tmp = '';
                        for($ii=0;$ii < count($v);$ii++)
                        {                            
                            if (strpos($v[$ii],'+') > 0)
                            {
                                $fields_several_tmp = explode('+',$v[$ii]);
                                $field_name_set = '[';
                                foreach($fields_several_tmp as $k_sev => $v_sev)
                                {
                                    $field_name_set .= '"'.((isset($field_names[$v_sev]) && strlen($field_names[$v_sev]) > 0)?$field_names[$v_sev]:$v_sev).'" plus ';
                                    /*
                                     * also we fill the out parameter with the list of fields 
                                     * that didn't pass through the checks.
                                     */
                                    $fields_err[$v_sev] = 'has-error'; 
                                }
                                $field_name_set = rtrim($field_name_set,' plus ').']';
                            }
                            else 
                            {
                                $field_name_set = '"'.((isset($field_names[$v[$ii]]) && strlen($field_names[$v[$ii]]) > 0)?$field_names[$v[$ii]]:$v[$ii]).'"';
                                /*
                                 * also we fill the out parameter with the list of fields 
                                 * that didn't pass through the checks.
                                 */
                                $fields_err[$v[$ii]] = 'has-error'; 
                            }
                            
                            
                            if ($ii+1 <> count($v))
                                $tmp .= ', '.$field_name_set;
                                else $tmp .= ' and '.$field_name_set;
                               
                              
                        }
                        $res_mess .= 'There is the same values for a fields '.ltrim($tmp,', ').' that should be different!<br>';
                    }    
                }
                
                return $res_mess;
            }
            
            $err_text = '';
            foreach ($error as $fields => $err_list)
            {                                               
                foreach($err_list as $k => $v)
                {
                    
                    
                    /*
                     * different message for common checks
                     */
                    if ($v['name'] == 'common_be_different')    
                    {
                        /*
                         * also we fill the out parameter with the list of fields 
                         * that didn't pass through the checks.
                         */
                        $tmp_text = GetMessageCommonCheck($v['param1'],$fields_err);
                    }
                    /*
                     * standart message for field checks
                     */
                    else 
                    {
                        /*
                         * fill the out parameter with the list of fields that didn't
                         * pass through the checks.
                         * 
                         * but if it's parent check we should shown error not in primary
                         * field but in parent. E.g. if we entered `phone#` and didn't
                         * enter `phone# code`. The check is used for phone# but we should
                         * highlight the `phone# code` field.
                         */ 
                        if ($v['name'] == 'fields_together')
                            $fields_err[$v['param1']] = 'has-error';                           
                            else $fields_err[$fields] = 'has-error';
                            
                        $tmp_text = str_replace('%field%',$field_names[$fields],$valid_group[$v['name']]['err_txt']);
                        if (isset($v['param1']))
                            $tmp_text = str_replace('%val1%',isset($field_names[$v['param1']])?$field_names[$v['param1']]:$v['param1'],$tmp_text);
                        if (isset($v['param2']))
                            $tmp_text = str_replace('%val2%',isset($field_names[$v['param2']])?$field_names[$v['param2']]:$v['param2'],$tmp_text);
                    }
                    /*
                     * fill errors by groups array
                     */                    
                    $err_group[$valid_group[$v['name']]['group']][] = $tmp_text;
                }
            }
            
            /*
             * make sort for error groups
             */
            uksort($err_group,
                function($a,$b)
                {
                    global $valid_class;
                    if ($valid_class[$a]['sort'] < $valid_class[$b]['sort'])
                        return 0;
                        else return 1;
                }
            );
            
            /*
             * use errors by groups array to make correct error message
             */
            foreach ($err_group as $key => $group)
            {
                
                $err_text .= '<div class="alert alert-warning fade in margin-top-20">'
                           . '<a href="#" class="close" data-dismiss="alert" aria-label="'.$lang_global['alert_close'].'" title="'.$lang_global['alert_close'].'">&times;</a>'
                           . '<strong>ERROR</strong> - '.$valid_class[$key]['text'].''
                           . '<ul class="error_ul">';
                foreach ($group as $k => $err)
                {
                    $err_text .= '<li>'.$err.'</li>';
                }
                $err_text .= '</ul></div>';
                
                
                
//                $err_text .= '<div class="alert"><div class="alert_message"><b>ERROR</b> - '.$valid_class[$key]['text'].'</div><ul>';
//                foreach ($group as $k => $err)
//                {
//                    $err_text .= '<li class="alert_message_det">'.$err.'</li>';
//                }
//                $err_text .= '</ul></div>';
            }                        
        }
        else $err_text = true;
        
        return $err_text;
    }
        
    /*
     * 3) General checking function.
     * --------------------------------------------------
     * all validations from list above 1) must be
     * coded here. It's a core of checking
     * --------------------------------------------------
     * $val     - value of form field, which is checked
     * $cond    - check for concrete form field
     * $field   - name of form field
     * return   - 1/0 in case of passing validation / or not
     */ 
    function MakeCheck($val, $cond, $field)
    {
        global $dbh;
        global $error;    
        
        /*
         *  checks
         */
        /*
         *  must_be_filled
         */        
        if ($cond['valid'] == 'must_be_filled')
        {
            /*
             * different type of checks due to $val type: array or not
             */
            if (is_array($val))
            {
                $local_check = 1;
                foreach ($val as $k => $v)
                {
                    if (strlen($v) == 0)
                       $local_check *= 0;
                       else $local_check *= 1;
                }
                return $local_check;
            }
            else if (strlen($val) == 0)
                    return 0;
                    else return 1;
        }
        
        /*
         *  in_list_db
         */
        if ($cond['valid'] == 'in_list_db')
        {
            /*
             * if there is empty value we don't need to make this check
             * because empty field check will be raised
             */ 
            $check = CheckErrorIn($field,'must_be_filled');
            
            if ($check == 1)
               return 1;
            
            /*
             * different type of checks due to $val type: array or not
             */
            if (is_array($val))
            {
                $db_param = implode(',', $val);
                $db_cnt = count($val);
                $query = 'select count(1) as cnt from '.$cond['param1'].' where '.$cond['param2'].' in ('.$db_param.')';
            }
            else 
            {    
                $query = 'select count(1) as cnt from '.$cond['param1'].' where '.$cond['param2'].' = '.$val;
                $db_cnt = 1;
            }
            
            $qres = $dbh->prepare($query);
            $qres->execute();
            
            // looking into db
            foreach($qres as $tmp)
            {     
                if ($tmp['cnt'] <> $db_cnt)
                   return 0;
                   else return 1;
            }
        }
        
        /*
         *  in_list_array
         */
        if ($cond['valid'] == 'in_list_array')
        {
            /*
             * if there is empty value we don't need to make this check
             * because empty field check will be raised
             */ 
            $check = CheckErrorIn($field,'must_be_filled');
            
            if ($check == 1)
               return 1;
            
            /*
             * different type of checks due to $val type: array or not
             */
            if (is_array($val))
            {                
                /*
                 * for array $val every value should be in list!
                 */
                $local_check = 1;
                
                if (strpos($cond['param1'], '@') > 0)
                {
                    $avail_arr = explode('@',$cond['param1']);
                    foreach ($val as $k => $v)
                    {
                        if (!in_array($v,$avail_arr))
                           $local_check *= 0;
                           else $local_check *= 1;
                    }
                }
                else if (strpos($cond['param1'], '}') > 0)
                {
                    $avail_arr = explode('}',$cond['param1']);
                    foreach ($val as $k => $v)
                    {
                        if ($v >= $avail_arr[0] && $v <= $avail_arr[1])
                           $local_check *= 1;
                           else $local_check *= 0;
                    }
                }
                /*
                 * strange things happened!
                 */
                else return 0;
                
                return $local_check;
            }
            else 
            {
                if (strpos($cond['param1'], '@') > 0)
                {
                    $avail_arr = explode('@',$cond['param1']);
                    if (!in_array($val,$avail_arr))
                        return 0;
                        else return 1;            
                }   
                else if (strpos($cond['param1'], '}') > 0)
                {
                    $avail_arr = explode('}',$cond['param1']);
                    if ($val >= $avail_arr[0] && $val <= $avail_arr[1])
                        return 1;
                        else return 0; 
                }
                /*
                 * strange things happened!
                 */
                else return 0;
            }        
        }
        
        /*
         *  digits only
         */
        if ($cond['valid'] == 'digits_only')
        {
            /*
             * if this field is empty no need to check anything
             */
            if (is_array($val))
               $local_check = isset($val[0]);
               else $local_check = strlen($val);
               
            if ($local_check > 0)
            {            
                /*
                 * different type of checks due to $val type: array or not
                 */
                if (is_array($val))
                {
                    $local_check = 1;
                    foreach ($val as $k => $v)
                    {
                        if (preg_match("|^[\d]+$|", $v))
                           $local_check *= 1;
                           else $local_check *= 0;
                    }
                    return $local_check;
                }
                else if (preg_match("|^[\d]+$|", $val))
                        return 1;
                        else return 0;
            }
            return 1;
        }
        
        /*
         *  valid email
         */
        if ($cond['valid'] == 'valid_email')
        { 
            /*
             * if this field is empty no need to check anything
             */
            if (is_array($val))
               $local_check = isset($val[0]);
               else $local_check = strlen($val);
               
            if ($local_check > 0)
            {            
                /*
                 * different type of checks due to $val type: array or not
                 */
                if (is_array($val))
                {
                    $local_check = 1;
                    foreach ($val as $k => $v)
                    {
                        if (filter_var($v, FILTER_VALIDATE_EMAIL))
                           $local_check *= 1;
                           else $local_check *= 0;
                    }
                    return $local_check;
                }
                else if (filter_var($val, FILTER_VALIDATE_EMAIL))
                        return 1;
                        else return 0;
            }
            else return 1;
        }
        
        /*
         *  valid URL
         */
        if ($cond['valid'] == 'valid_url')
        {
            /*
             * if this field is empty no need to check anything
             */
            if (is_array($val))
               $local_check = isset($val[0]);
               else $local_check = strlen($val);
               
            if ($local_check > 0)
            {
                /*
                 * different type of checks due to $val type: array or not
                 */
                if (is_array($val))
                {
                    $local_check = 1;
                    foreach ($val as $k => $v)
                    {
                        if (filter_var($v, FILTER_VALIDATE_URL))
                           $local_check *= 1;
                           else $local_check *= 0;
                    }
                    return $local_check;
                }
                else if (filter_var($val, FILTER_VALIDATE_URL))
                        return 1;
                        else return 0;
            }
            else return 1;
        }
        
        /*
         * There should at least X characters
         */
        if ($cond['valid'] == 'least_length')
        {
            /*
             * different type of checks due to $val type: array or not
             */
            if (is_array($val))
            {
                $local_check = 1;
                foreach ($val as $k => $v)
                {
                    if (strlen($v) > 0 && strlen($val) < $cond['param1'])
                       $local_check *= 0;
                       else $local_check *= 1;
                }
                return $local_check;
            }
            else if (strlen($val) > 0 && strlen($val) < $cond['param1'])
                    return 0;
                    else return 1;            
        }
        
        /*
         * There couldn't be 0 at beginning
         */
        if ($cond['valid'] == 'no_0_start')
        {
            /*
             * different type of checks due to $val type: array or not
             */
            if (is_array($val))
            {
                $local_check = 1;
                foreach ($val as $k => $v)
                {
                    if (strlen($v) > 0 && substr($v,0,1) == '0')
                       $local_check *= 0;
                       else $local_check *= 1;
                }
                return $local_check;
            }
            else if (strlen($val) > 0 && substr($val,0,1) == '0')
                    return 0;
                    else return 1;            
        } 
        
        /*
         * if our checking field is filled than parameter field must
         * be filled too
         */
        if ($cond['valid'] == 'fields_together')
        {            
            /*
             * different type of checks due to $val type: array or not
             */
            if (is_array($val))
               $local_check = isset($val[0]);
               else $local_check = strlen($val);
               
            if ($local_check > 0)
            {
                global $$cond['param1'];
                if (strlen($$cond['param1']) > 0)
                    return 1;
                    else return 0;
            }        
            else return 1;
        }
        
        /*
         * No several values could be chosen. Only one value for a field
         */
        if ($cond['valid'] == 'one_value_only')
        {            
            /*
             * different type of checks due to $val type: array or not
             */
            if (is_array($val))
               if (count($val) == 1)
                  return 1;
                  else return 0;
               else return 1;
        }
        
        /*
         * if our checking field is filled than parameter field must
         * be different
         */
        if ($cond['valid'] == 'must_be_different')
        {            
            if (is_array($val))
               $tmp = isset($val[0]);
               else $tmp = strlen($val);
               
            if ($tmp > 0)
            {
                global $$cond['param1'];
                /*
                 * different type of checks due to $val type: array or not
                 */
                if (is_array($val))
                {
                    $local_check = 1;
                    foreach ($val as $k => $v)
                    {
                        if ($$cond['param1'] <> $v)
                           $local_check *= 1;
                           else $local_check *= 0;
                    }
                    return $local_check;
                }
                else if ($$cond['param1'] <> $val)
                        return 1;
                        else return 0;
             }
            else return 1;
        }  
        
        /*
         * Values for checking field must depend on
         * certaon values of parent field e.g.
         * If the country is germany (value 1),     <- checking field/value = country/1
         * the state can be 1-16                    <- parent field/values = state/1-16
         */
        if ($cond['valid'] == 'parent_in_list_array')
        {
            /*
             * This check is used for plain values only (no arrays)
             */
            if (is_array($val))
               return 0;
            
            /*
             * parent field value
             */
            global $$cond['param2'];
            
            /*
             * create list of checking values for checking field
             */
            if (strpos($cond['param1'], '@') > 0)
            {
                $avail_arr = explode('@',$cond['param1']); 
                if (in_array($val,$avail_arr))
                    $check_val = 1;
                    else $check_val = 0;
            }
            else if (strpos($cond['param1'], '}') > 0)
            {
                $avail_arr = explode('}',$cond['param1']);
                if ($val >= $avail_arr[0] && $val <= $avail_arr[1])
                    $check_val = 1;
                    else $check_val = 0;
            } else 
            {
                if ($val == $cond['param1'])
                    $check_val = 1;
                    else $check_val = 0;
            }            
                        
            /*
             * create list of checking values for parent field
             */
            if (strpos($cond['param3'], '@') > 0)
            {
                $avail_arr = explode('@',$cond['param3']); 
                if (in_array($$cond['param2'],$avail_arr))
                    $parent_val = 1;
                    else $parent_val = 0;
            }
            else if (strpos($cond['param3'], '}') > 0)
            {
                $avail_arr = explode('}',$cond['param3']);
                if ($$cond['param2'] >= $avail_arr[0] && $$cond['param2'] <= $avail_arr[1])
                    $parent_val = 1;
                    else $parent_val = 0;
            } else 
            {
                if ($$cond['param2'] == $cond['param3'])
                    $parent_val = 1;
                    else $parent_val = 0;
            }           
            
            /*
             * if parent values are ok then if checked value is ok - there is no error
             */
            if ($parent_val)
                if ($check_val)
                    return 1;
                    else return 0;
                else return 1;            
        }
        
        /*
         * Entered value should contain at least 4 digits!
         */
        if ($cond['valid'] == 'at_least_4_digits')
        { 
            /*
             * This check is used for plain values only (no arrays)
             */
            if (is_array($val))
               return 0;                        
                        
            if (strlen($val) > 0) 
            {
                if (strlen($val) >= strlen(preg_replace("[\d]",'',$val))+4)
                    return 1;
                    else return 0;                    
            }        
            else return 1;    
        }
        
        /*
         * The values in fields which are listed in parameters
         * must be different
         */
        if ($cond['valid'] == 'common_be_different')
        { 
            /*
             * This check is used for plain values only (no arrays)
             */
            if (is_array($val))
               return 0;            
            
            $fields_val_tmp = explode('@',$cond['param1']);
            /*
             * we get array with [field value]->list of fields that 
             * contain that value
             */           
            foreach($fields_val_tmp as $k => $v)
            {
                if (strpos($v,'+') > 0)
                {
                    /*
                     * we should checked the joined values
                     */
                    $fields_several_tmp = explode('+',$v);
                    $tmp = '';
                    foreach($fields_several_tmp as $k_sev => $v_sev)
                    {
                        global $$v_sev;
                        $tmp .= $$v_sev;
                    }
                }
                else
                {
                    global $$v;
                    $tmp = $$v;
                }
                
                /*
                 * now check for empty value
                 */
                if (strlen($tmp) > 0)
                    $fields_val_uniq[$tmp][] = $v;
            }
            
            if (!isset($fields_val_uniq))
                return 1;
            
            /*
             * if some value contain more than 1 field we
             * have validation error
             */                        
            foreach($fields_val_uniq as $k => $v)
            {
                if (count($v) > 1)
                    return 0;
            }
            return 1;
        }
        
        /*
         * Entered values must be zip code
         */
        if ($cond['valid'] == 'valid_zip')
        { 
            if (strlen($val) == 0)
                return 1;    
            
            global $country;
            global $country_array;
            /*
             * If there is no country selection just check digits only
             */
            if (!isset($country) || strlen($country) == 0)
                if (preg_match("|^[\d]+$|", $val))
                    return 1;
                    else return 0;
            else 
            {
                if ($country_array[$country] == 'Germany')
                    $check_rule = '\d{5}';
                else if ($country_array[$country] == 'Austria')
                    $check_rule = '\d{4}';
                else if ($country_array[$country] == 'Suiss')
                    $check_rule = '\d{4}';
                else $check_rule = '[\d]+';
            }        
                           
            /*
             * check
             */
            if (preg_match('|^'.$check_rule.'$|', $val))
               return 1;
               else return 0;
        }
        
        
    }
    
?>
