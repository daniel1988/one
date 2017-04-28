<?php

include dirname(__FILE__) . '/../src/OneLib/Cli.php' ;
include dirname(__FILE__) . '/../src/OneLib/String.php' ;
include dirname(__FILE__) . '/../src/OneLib/Verify.php' ;

use \OneLib\String;
use \OneLib\Verify;

$name = 'daniel.luo';

var_dump( String::mask_name( $name ) ) ;


$phone = '13040804608';
var_dump( String::mask_phone( $phone ) ) ;

var_dump( Verify::is_phone_number( $phone ) );

var_dump( Verify::is_cn_mobile( $phone ) );

var_dump( Verify::is_email( '453465565@qq.com' ) );