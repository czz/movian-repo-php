# movian-repo-php
A simple php class to build movian repository json response


## How to use this class

Starting with Movian 6.0 supports multiple feed of plugins. For more information how to add new feeds to Movian see this article.

Note: There is no longer a central plugin repository hosted at this site. See this article for more info

The easiest way to publish plugins is to commit each of them to a public repo at github.

See https://github.com/andoma/movian-plugin-modarchive for an example how this should look.


Use it like this:
~~~
    $list = array("/relative_path_to_git_repo_1", "/relative_path_to_git_repo_2");

    // With cUrl Callback Function
    $mp = new MovianRepo( function($url) {
                              $ch =  curl_init($url);
                              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // returns empty string n failure
                              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                              curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                              curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                              curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                              curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
                              curl_setopt($ch, CURLOPT_USERAGENT,  MovianRepo::getUserAgent());
                              $result = curl_exec($ch);
                              return $result;
                           }
                         );
                         
    $json = $mp->build($list);
 
    // now you have the json string do what you whant, we echo it 
    header('Content-Type: application/json');
    echo $json;
~~~


