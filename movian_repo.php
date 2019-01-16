<?php

/*
 * Movian Repo Class for github
 *
 * Author czz78
 */
class MovianRepo {

    private $_URL= "https://github.com";
    private $_APIURL= "https://api.github.com/repos";
    private $_RAWURL = "https://raw.githubusercontent.com";
    private $_MASTERBRANCH = "/branches/master";

    private $_USERAGENT = "Movian Repo";  // http://developer.github.com/v3/#user-agent-required

    private $_VERSION = 1;

    /*
     * Contruct function
     * @version int Version of plugin json
     */
    function __construct($version = 1) {

       $this->_VERSION = intval($version);

    }


    /*
     *  Get master branch sha
     *  @repo_path string Relative path of github repository
     *  @return string Sha string
     */
    private function _getSha($repo_path) {

        $url = $this->_APIURL . $repo_path . $this->_MASTERBRANCH;

        $ch =  curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // returns empty string n failure
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($ch, CURLOPT_USERAGENT, $this->_USERAGENT);
        $result = curl_exec($ch);

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
    private function _getPluginJson($repo_path, $sha) {

        $url = $this->_RAWURL . $repo_path . "/" . $sha . "/plugin.json";

        $ch =  curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // returns empty string n failure
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($ch, CURLOPT_USERAGENT, $this->_USERAGENT);
        $result = curl_exec($ch);

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
    private function _getIcon($repo_path, $sha, $icon_name) {
        return $this->_RAWURL . $repo_path . "/" . $sha . "/" . $icon_name;
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

                $plugin_json = $this->_getPluginJson($el,$sha);
                $plugin_json->downloadURL = $this->_URL . $el . "/archive/" . $sha . ".zip";
                $plugin_json->icon = $this->_getIcon($el, $sha, $plugin_json->icon);
                array_push($res['plugins'],$plugin_json);

            }

        }
        return json_encode($res);

    }

}


/*
 * Test function
 */
function movian_repo_test($list) {
    $mp = new MovianRepo();
    return $mp->build($list);
}

//header('Content-Type: application/json');
//echo movian_repo_test(array("/czz/movian-plugin-zooqle"));

