<?php
$root_path = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT']);
$_serverHost = $_SERVER['HTTP_HOST'];
$_mainHost = '.'.hostName("http://".$_serverHost);
define('URL_CALLBACK', $_SERVER['HTTP_HOST'].'/My/snsCallBack?type=');
if(substr($root_path, -1, 1) != '/') $root_path .= '/';
return array(
    'DB_TYPE'       => 'mysql',
    'DB_HOST'       => '127.0.0.1',
    'DB_NAME'       => '',
    'DB_USER'       => '',
    'DB_PWD'        => '',
    'DB_PORT'       => '3306',
    'DB_CHARSET'    => 'utf8',
    'DB_PREFIX'     => 'ton_',
    'COOKIE_STR'    => '',
    'MAIN_DOMAIN'   => $_mainHost,
    'COOKIE_DOMAIN' => $_mainHost,
    'APP_SUB_DOMAIN_DEPLOY' =>  true,   // 是否开启子域名部署
    'APP_SUB_DOMAIN_RULES'  =>  array(
            'admin.gelindun.cc'  => 'Admin'
    ), // 子域名部署规则
    'APP_DOMAIN_SUFFIX'     =>  'cc', // 域名后缀 如果是com.cn net.cn 之类的后缀必须设置    
    'MULTI_MODULE'          =>  true, 
    'MODULE_ALLOW_LIST'    =>   array('Home','Admin'),
    'DEFAULT_MODULE'       =>   'Home',

    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__PUBLIC__' => __ROOT__ . '/Public',
        '__STATIC__' => __ROOT__ . '/Public/static'
    ),
    'WWW_ROOT_PATH'     => $root_path,
    'DATA_UPLOADS'      => 'Uploadfiles/',
    'CLEAN_FORM'        => 'clean_form',

    'DEFAULT_THEME'     => 'default',
    'TMPL_L_DELIM'      => '{{',
    'TMPL_R_DELIM'      => '}}',
    'TAGLIB_BEGIN'      => '{{',
    'TAGLIB_END'        => '}}',
    'TMPL_TEMPLATE_SUFFIX'  => '.tpl.html',
    'URL_HTML_SUFFIX'   => 'html',
    'URL_MODEL'         => 2,
    
    'DEFAULT_FILTER'    =>  'htmlspecialchars',
    
    'APP_STATUS'        => false,
    'SHOW_PAGE_TRACE'   => false,
    
    'LANG_SWITCH_ON'    => true,
    'LANG_AUTO_DETECT'  => true,
    'DEFAULT_LANG'      => 'zh-cn',
    'LANG_LIST'         => 'zh-cn,en-us',
    'VAR_LANGUAGE'      => 'l',
    'DATA_CACHE_PATH'   => $root_path . 'Cache/',
    
);