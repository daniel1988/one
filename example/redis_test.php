<?php
include dirname(__FILE__) . '/../src/OneLib/OneRedis.php' ;

use \OneLib\OneRedis;

$redis = OneRedis::getInstance();
// $key = 'foo' ;
// $result = $redis->set( $key , '1111', 3 ) ;
// $value = $redis->get( $key ) ;
// var_dump( $result , $value ) ;

// $hashname = 'hash_foo';
// $field      = rand(1,10);
// $value      = rand(10,1000);

// $result = $redis->hSet( $hashname, $field, $value ) ;
// $value = $redis->hGet( $hashname, $field ) ;
// $hash_values = $redis->hGetAll( $hashname ) ;
// var_dump( $result, $value , $hash_values, $redis->hLen( $hashname ) ) ;
$hashname = 'm_hash_name';
$field_value_set = [
    'aa'    => 11,
    'bb'    => 22,
    'cc'    => 33
];

// var_dump( $redis->hMset( $hashname, $field_value_set) , $redis->hMget( $hashname, array_keys( $field_value_set ) ) );

// var_dump( $redis->hKeys( $hashname ) ) ;


// var_dump( $redis->hIncrBy( 'hash_foo', 'foo', -1) ) ;
//
$key = 'stack_foo_key';

// $result = $redis->lPush( $key, rand(1,10) ) ;
// $result = $redis->rPop( $key ) ;
$result = $redis->rPush( $key, rand(1,10) ) ;

$list = $redis->lRange($key, 0, -1);

var_dump( $result,$list  ) ;