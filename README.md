# php-validate
php-based script for various form validation
you need to include validate.php file, define validation rule
and use specific function. You will get the array or text with
error (if there was some validation error) as a result.


 * 
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
 *
