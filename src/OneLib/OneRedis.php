<?php

namespace OneLib;
/**
 * @author Daniel.luo
 * @date [2016-10-09]
 */
class OneRedis {

    static $_instance = null ;

    var $oRedis = null ;

    var $connected = false ;

    var $host   = '127.0.0.1';

    var $port   = '6379';

    var $timeout = 3;

    /**
     * @return OneRedis
     */
    public function getInstance() {
        if ( self::$_instance === null ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
    /**
     * 设置服务器
     * @param   $host
     * @param   $port
     * @param integer $timeout
     */
    public function setServer( $host, $port , $timeout=3 ) {
        $this->host = $host ;
        $this->port = $port ;
        $this->timeout = $timeout ;
        return true;
    }
    /**
     * 初始化Redis
     */
    public function initRedis() {
        if ( !class_exists('Redis') ) {
            die('Redis Extension Needed!');
        }
        if ( $this->oRedis === null ) {
            $this->oRedis = new \Redis();
        }
        if ( $this->connected ) {
            return true;
        }
        $this->connected = $this->oRedis->connect( $this->host, $this->port, $this->timeout ) ;
        return $this->connected;
    }
    /**
     * [incr]
     * @param   $key
     * @param   $value
     * @return
     */
    public function incr( $key, $value ) {
        if ( !$this->initRedis() ) {
            return false ;
        }
        return $this->oRedis->incr($key, $value) ;
    }

    /**
     * set value
     */
    public function set( $key, $value, $timeout=0 ) {
        if ( !$this->initRedis() ) {
            return false ;
        }
        if ( $timeout > 0 ) {
            return $this->oRedis->setex( $key, $timeout, $value ) ;
        }
        return $this->oRedis->set( $key , $value ) ;
    }
    /**
     * get
     */
    public function get( $key ) {
        if ( !$this->initRedis() ) {
            return false ;
        }
        return $this->oRedis->get( $key ) ;
    }

    /**
     * @param $hashname
     * @param $field
     * @param $value
     */
    public function hSet( $hashname , $field, $value ) {
        if ( !$this->initRedis() ) {
            return false ;
        }
        return $this->oRedis->hset( $hashname, $field, $value ) ;
    }
    /**
     * hGet
     * @param   $hashname
     * @param   $field
     * @return
     */
    public function hGet( $hashname, $field ) {
        if ( !$this->initRedis() ) {
            return false ;
        }
        return $this->oRedis->hget( $hashname, $field ) ;
    }
    /**
     * hGetAll
     * @param   $hashname
     * @return
     */
    public function hGetAll( $hashname ) {
        if ( !$this->initRedis() ) {
            return false ;
        }
        return $this->oRedis->hgetall( $hashname ) ;
    }
    /**
     * [hMget]
     * @param   $hashname
     * @param   $field_list
     * @return
     */
    public function hMget( $hashname, $field_list ) {
        if ( !$this->initRedis() ) {
            return false ;
        }
        return $this->oRedis->hMget( $hashname, $field_list ) ;
    }
    /**
     * hMset
     * @param   $hashname
     * @param   $field_list
     * @return
     */
    public function hMset( $hashname, $field_list ) {
        if ( !$this->initRedis() ) {
            return false ;
        }
        return $this->oRedis->hMset( $hashname, $field_list ) ;
    }
    /**
     * hKeys
     * @param   $hashname
     * @return
     */
    public function hKeys( $hashname ) {
        if ( !$this->initRedis() ) {
            return false ;
        }
        return $this->oRedis->hKeys( $hashname ) ;
    }
    /**
     * hLen
     * @param   $hashname
     * @return
     */
    public function hLen( $hashname ) {
        if ( !$this->initRedis() ) {
            return false;
        }
        return $this->oRedis->hlen( $hashname ) ;
    }
    /**
     * [hIncrBy]
     * @param   $hashname
     * @param   $field
     * @param   $value
     * @return
     */
    public function hIncrBy( $hashname, $field, $value ) {
        if ( !$this->initRedis() ) {
            return false;
        }
        return $this->oRedis->hIncrBy( $hashname, $field, $value ) ;
    }

    //Redis List

    public function lRange( $key, $start, $len=-1 ) {
        if ( !$this->initRedis() ) {
            return false;
        }
        return $this->oRedis->lrange( $key, $start, $len ) ;
    }

    public function rPush( $key , $value ) {
        if ( !$this->initRedis() ) {
            return false;
        }
        return $this->oRedis->rpush( $key , $value ) ;
    }

    public function rPop( $key ) {
        if ( !$this->initRedis() ) {
            return false;
        }
        return $this->oRedis->rpop( $key ) ;
    }

    public function lPush( $key , $value ) {
        if ( !$this->initRedis() ) {
            return false;
        }
        return $this->oRedis->lpush( $key , $value ) ;
    }

    public function lPop( $key ) {
        if ( !$this->initRedis() ) {
            return false;
        }
        return $this->oRedis->lpop( $key ) ;
    }

    public function lLen( $key ) {
        if ( !$this->initRedis() ) {
            return false;
        }
        return $this->oRedis->llen( $key ) ;
    }



}

