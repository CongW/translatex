<?php
    function webpage2txt($url) {

       $ch = curl_init($url); // initialize curl with given url
       curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]); // set  useragent
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // write the response to a variable
       curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE); // follow redirects if any
       curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); // max. seconds to execute
       curl_setopt($ch, CURLOPT_FAILONERROR, 1); // stop when it encounters an error
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
       $text = curl_exec($ch);

       $dom = new DOMDocument;
       libxml_use_internal_errors(true);
       $dom->loadHTML($text);
       $paras = $dom->getElementsByTagName('p');
       $result = '';


    	for ($i = 0; $i < $paras->length; $i++) {
           $result = $result . $paras->item($i)->nodeValue . "\n\n";
        }
        return $result;
    }

    $import_type = 'importbyurl';
    $url_arg = getopt("u:");
    $url = $url_arg["u"];

    if($import_type == 'importbyurl')
    {
        $content = webpage2txt($url);
        echo 'Original Article:' . "\r\n" . $content;
        $ini_array = parse_ini_file("translate_config.ini");
        $apiKey = $ini_array['google_translate_api_key'];
        $url = 'https://www.googleapis.com/language/translate/v2?key=' . $apiKey . 
        	'&source=en&target=zh-CN&format=text&q=' 
        	. rawurlencode($content);

        $handle = curl_init($url);
       	curl_setopt($handle, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]); // set  useragent
       	curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE); // write the response to a variable
       	curl_setopt($handle, CURLOPT_FOLLOWLOCATION, TRUE); // follow redirects if any
       	curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30); // max. seconds to execute
       	curl_setopt($handle, CURLOPT_FAILONERROR, 1); // stop when it encounters an error
       	curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($handle); 
        $responseDecoded = json_decode($response, true);
    	curl_close($handle);

    	echo 'Translation: ' . "\r\n" . $responseDecoded['data']['translations'][0]['translatedText'];
    }
?>
