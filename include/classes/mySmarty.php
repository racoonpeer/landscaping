<?php
/**
 * WEBlife CMS
 * Created on 26.11.2010, 17:20:59
 * Developed by http://weblife.ua/
 */
defined('WEBlife') or die('Restricted access'); // no direct access

//Include Require Smarty Class
require(WLCMS_SMARTY_DIR."Smarty.class.php");

/**
 * Description of mySmarty class
 * @author WebLife
 * @copyright 2010
 * @version $Id: MySmarty.php 03 2011-09-28 13:38:40 $
 */
class mySmarty extends Smarty {
   /**
     * Template name
     *
     * @var  string
     */
    private $tplname = '';
   /**
     * Template type [admin (backend) | site(frontend)]
     *
     * @var  string
     */
    private $tpltype = '';

    /**
     * Class constructor, initializes basic smarty properties
     *
     * @param   string  $tplname
     * @param   bool    $debug
     * @param   mixed   $error_reporting
     * @param   bool    $force_compile
     * @param   bool    $caching
     * @return  void
     */
    public function __construct($tplname, $debug=false, $error_reporting=null, $force_compile=1, $caching=0) {
        parent::__construct();
        
        $this->setTemplatePath($tplname, strtolower(WLCMS_ZONE));
        
        $this->compile_dir  = 'temp'.DS.'tpl_c';
        $this->cache_dir    = 'temp'.DS.'cache';

        $this->debugging        = (bool)$debug;
        $this->caching          = (bool)$caching;
        $this->force_compile    = (bool)$force_compile;
        $this->compile_check    = true;
        
        // use sub dirs for compiled/cached files?
//        $this->use_sub_dirs     = true;
        // flag if {block} tag is compiled for template inheritance
//        $this->inheritance = true;
        $this->cache_lifetime   = 753;
        $this->left_delimiter   = "<{";
        $this->right_delimiter  = "}>";
        $this->error_reporting  = $error_reporting;

        if($this->force_compile) $this->caching = false;

        if ($this->debugging) {// Debug console hav two option 1 - show debug window or 0 - none show
            $this->assign('$_GET', $_GET);
            $this->assign('$_POST', $_POST);
            $this->assign('$_SESSION', $_SESSION);
            $this->assign('$_SERVER', $_SERVER);
        }// End Debug console                            
    }
    
    
    /**
     * Function setCompileCheck
     *
     * @param   bool  $compile_check
     * @return  void
     */
    public function setCompileCheck($compile_check) {
        $this->compile_check = (bool)$compile_check;
    }
    
    
    /**
     * Function setCacheLifeTime
     *
     * @param   int  $cache_lifetime
     * @return  void
     */
    public function setCacheLifeTime($cache_lifetime) {
        $this->cache_lifetime = (int)$cache_lifetime;
    }
    
    
    /**
     * Function setTemplateName
     *
     * @param   string  $tplname
     * @return  void
     */
    public function setTemplateName($tplname) {
        $this->tplname = (string) trim($tplname);
    }
    
    
    /**
     * Function setTemplateType
     *
     * @param   string  $tpltype
     * @return  void
     */
    public function setTemplateType($tpltype) {
        $this->tpltype = (string) trim($tpltype);
    }
    
    
    /**
     * Function setTemplatePath
     *
     * @param   string  $tplname
     * @param   string  $tpltype
     * @return  void
     */
    public function setTemplatePath($tplname, $tpltype) {
        $this->setTemplateName($tplname);
        $this->setTemplateType($tpltype);
        $this->template_dir = (string) 'tpl'.DS.$this->tpltype.DS.$this->tplname.DS;
    }

    /**
    * Function clearAllCompiledFiles
    * Empty Compiled folder
    * 
    * @param integer $exp_time expiration time
    * @return integer number of compiled files deleted
    */
    function clearAllCompiledFiles($exp_time = null){
        // clear out all cache files
        return $this->clearCompiledTemplate(null, null, $exp_time);
    }
    
    
    /**
    * Function clearCachedFiles
    * Empty cache for a specific template or all caching files
    * 
    * @param string $template_name template file name
    * @param string $cache_id cache id
    * @param string $compile_id compile id
    * @param integer $exp_time expiration time
    * @param string $type resource type
    * @return integer number of cache files deleted
    */
    public function clearCachedFile($template_name, $cache_id = null, $compile_id = null, $exp_time = null, $type = null) {
        $template_name = (string) trim($template_name);
        // clear cache for $tpl_file_name.tpl
        return $template_name ? $this->clearCache($template_name, $cache_id, $compile_id, $exp_time, $type) : false;
    }

    /**
    * Function clearAllCachedFiles
    * Empty cache folder
    * 
    * @param integer $exp_time expiration time
    * @param string $type resource type
    * @return integer number of cache files deleted
    */
    function clearAllCachedFiles($exp_time = null, $type = null){
        // clear out all cache files
        return $this->clearAllCache($exp_time, $type);
    }

}
