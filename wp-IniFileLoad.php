<?php
/*
Plugin Name: wp-IniFileLoad
Plugin URI: http://www.belive.jp/archives/wp-IniFileLoad-1_0/
Description: wp-IniFileLoad is a simple plug-in. This plug in offers functions to load necessary common data from IniFile in your WordPress.
Author: Masarki Kondo
Version: 1.1.1
Author URI: http://www.belive.jp/
Update: 2008.03.12 INI data came to have cache. And I added function wp_clear_inidata and wp_reload_inifile.
Update: 2008.04.11 I did not limit a double associative array to a "data" section.
*/

/**
    @define : Common Theme
**/
if( !defined('COMMON_TEMPLATEPATH') && function_exists('get_theme_root'))
    define('COMMON_TEMPLATEPATH', get_theme_root() . '/common');

/**
    @class : WordPress Load Initialize Files Class
**/
class wp_load_inifile_class {

    // class variable
    var $roots = array( COMMON_TEMPLATEPATH, TEMPLATEPATH); // Data Root Path
    var $path = array();  // Real path of loading INI file 
    var $cache = array();  // INI Data cache
    
    /**
        @function : Constractor for PHP 4
        @param : addpath Load IniFiles Path
        @return : success=true / fault=false
    **/
    function wp_load_inifile_class($addpath=false){
        $this->check_template_path();
        if( is_string($addpath) && file_exists( $addpath))
            $this->roots[] = $addpath;
        return true;
    }

    /**
        @function : Constractor
        @param : addpath Load IniFiles Path
        @return : success=true / fault=false
    **/
    function __construct($addpath=false){
        return $this->wp_load_inifile_class($addpath);
    }

    /**
        @function : TEMPLATEPATH check
        @return : void
    **/
    function check_template_path(){
        foreach( $this->roots as $key => $root){
            if(( $root == 'TEMPLATEPATH') && function_exists('get_template_directory'))
                $this->roots[ $key] = get_template_directory();
        }
    }

    /**
        @function : no display roots for PHP5
        @return : void
    **/
    function __set_state(){
        $this->roots = false;
    }

    /**
        @function : Check Inifile Path
        @param : filename Load IniFiles Name
        @return : success=path / fault=false
    **/
    function check_inifile( $filename = false){
        if( !$filename) return false;
        if( isset( $this->path[$filename])) return $this->path[$filename];
        if( file_exists( $filename)) return $filename;
        $res = false;
        foreach( $this->roots as $root){
            $path = $root . '/' . $filename;
            $res = file_exists( $path) ? $path : $res;
        }
        $this->path[$filename] = $res;
        return $this->path[$filename];
    }

    /**
        @function : Load inifile
        @param : filename Load IniFiles Name
        @return : success=data / fault=false
    **/
    function load_inifile( $filename = false){
        $path = $this->check_inifile( $filename);
        if( !$path) return false;

        // IniFile Load and Cash
        if( is_array( $this->cache[$filename])) return $this->cache[$filename];
        $ini = parse_ini_file( $path, true);
        
        // create_function
        $funcs = false;
        if( is_array( $ini['function'])){
            $funcs = array();
            foreach( $ini['function'] as $fkey => $fsrc){
                if( $a_func = create_function( '$data', $fsrc))
                    $funcs[ $fkey] = $a_func;
            }
        }

        // data call
        $opts = array();
        $datas = array();
        foreach( $ini as $key => $val){
            $sects = array();
            if( preg_match( '/^data\s*(.*)\s*$/i', $key, $sects)){
                $a_sect = trim($sects[1]);
                if( is_array( $funcs)){
                    // change data with function
                    foreach( $val as $akey => $aval){
                        $temps = array('_SECTION' => $a_sect);
                        $temps['_KEY'] = $akey;
                        $temps['_VALUE'] = $aval;
                        reset( $funcs);
                        foreach( $funcs as $fkey => $func){
                            $temps[ $fkey] = $func( $temps);
                        }
                        $datas[] = $temps;
                    }
                }
                else
                if( empty( $a_sect)){
                    $datas[] = $val;
                }
                else
                {
                    $datas[$a_sect] = $val;
                }
            }
            else
            if( $key != 'function')
            {
                $sects = preg_split('/\s+/', $key, 2);
                $sects[0] = trim( $sects[0]);
                $sects[1] = trim( $sects[1]);
                if( empty( $sects[1])){
                    $opts[$sects[0]] = $val;
                }
                else
                {
                    $opts[$sects[0]][$sects[1]] = $val;
                }
            }
        }
        $this->cache[$filename] = array( 'data' => $datas, 'option' => $opts);
        return $this->cache[$filename];
    }

    /**
        @function : Clear cache
        @param : filename IniFiles Name to clear cache
                 default = 'all';
        @return : success=filename / fault=false
    **/
    function clear_cache( $filename = 'all'){
        if( $filename == 'all'){
            $this->cache = array();
            $this->path = array();
            return 'all';
        }
        else
        if( is_array( $this->cache[$filename])){
            unset($this->cache[$filename]);
            unset($this->path[$filename]);
            return $filename;
        }
        else
            return false;
    }

}

if( !is_object( $GLOBALS['WP_IniFile'])){
    $GLOBALS['WP_IniFile'] = new wp_load_inifile_class();

    /**
        @function : Load inifile
        @param : filename Load IniFiles Name
        @return : success=data / fault=false
    **/
    if( !function_exists( 'wp_load_inifile')){
        function wp_load_inifile( $filename = false, $path=false){
            if( !is_object($GLOBALS['WP_IniFile']))
                $GLOBALS['WP_IniFile'] = new wp_load_inifile_class($path);
            return $GLOBALS['WP_IniFile']->load_inifile($filename);
        }
    }

    /**
        @function : Clear inifile data
        @param : filename IniFiles Name to clear cache
        @return : success=filename / fault=false
    **/
    if( !function_exists( 'wp_clear_inidata')){
        function wp_clear_inidata( $filename = 'all'){
            if( !is_object($GLOBALS['WP_IniFile'])) return false;
            return $GLOBALS['WP_IniFile']->clear_cache($filename);
        }
    }

    /**
        @function : Reload inifile
        @param : filename Load IniFiles Name
        @return : success=data / fault=false
    **/
    if( !function_exists( 'wp_reload_inifile')){
        function wp_reload_inifile( $filename = false, $path=false){
            if( !$filename) return false;
            if( wp_clear_inidata( $filename)){
                return wp_load_inifile( $filename, $path);
            }
            return false;
        }
    }

}
?>
