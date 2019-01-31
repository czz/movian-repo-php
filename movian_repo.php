<?php

/*
 * Movian Repo Class for github
 *
 * Author czz78
 */
class MovianRepo {

    public static $_USERAGENT = "Movian Repo";  // http://developer.github.com/v3/#user-agent-required

    private $_VERSION = 1;

    private $_URL= "https://github.com";
    private $_APIURL= "https://api.github.com/repos";
    private $_BRANCH = "master";

    private $_CB;


    /*
     * Contruct function
     * @version int Version of plugin json
     */
    function __construct($func , $version = 1) {

       $this->_VERSION = intval($version);

       $this->setCB($func);
    }


    /*
     * Static function to retrieve USERAGENT
     * @return string _USERAGENT
     */
    public static function getUserAgent(){
        return self::$_USERAGENT;
    }


    /*
     * Set a Callback for Url requests
     */
    private function setCB($func){
        $this->_CB = &$func;
    }


    /*
     * Get url with callback function
     * @return result of function cb
     */
    private function getUrl($url){
        $func = &$this->_CB;
        return $func($url);
    }


    /*
     *  Get master branch sha
     *  @repo_path string Relative path of github repository
     *  @return string Sha string
     */
    private function _getSha($repo_path) {

        $url = $this->_APIURL . $repo_path . "/branches/" . $this->_BRANCH;

        $result = $this->getUrl($url);

        if (!empty($result)){

            $result = json_decode($result);

            if (property_exists($result,"commit") && property_exists($result->commit,"sha") && !empty($result->commit->sha)){
               return $result->commit->sha;
            }

        }

        return false;

    }


    /*
     *  Get plugin.json
     *  @repo_path string Relative path of plugin.json
     *  @return object of plugin.json in php
     */
    private function _getPluginJson($repo_path) {

        $url = $this->_APIURL . $repo_path . "/contents/plugin.json?ref=" . $this->_BRANCH;

        $result = $this->getUrl($url);

        if(empty($result)){
           return false;
        }

        $result = json_decode($result);
        $url = $result->download_url;


        $result = $this->getUrl($url);

        if(!empty($result)){

           $result = json_decode($result);
           return $result;

        }

        return false;

    }



    /*
     *  Get icon url
     *  @repo_path string Relative path of icon
     *  @sha string Sha string
     *  @icon_name string Icon name
     *  @return string Icon url
     */
    private function _getIcon($repo_path, $icon_name) {


        $url = $this->_APIURL . $repo_path . "/contents/" . $icon_name . "?ref=" . $this->_BRANCH;
        $result = $this->getUrl($url);

        if(empty($result)){
           return false;
        }

        $result = json_decode($result);

        return $result->download_url;

    }


    /*
     * Build repository json string
     * @list array List of plugin repository on github
     * @return Json repository string
     */
    function build($list) {

        $res = array("version" => $this->_VERSION, "plugins" => array());


        foreach($list as $el) {

            $sha = $this->_getSha($el);

            if ($sha === false) {
                return false;
            }
            else {

                $plugin_json = $this->_getPluginJson($el);
                $plugin_json->downloadURL = $this->_URL . $el . "/archive/" . $sha . ".zip";
                $plugin_json->icon = $this->_getIcon($el, $plugin_json->icon);
                array_push($res['plugins'],$plugin_json);

            }

        }
        return json_encode($res);

    }

}

