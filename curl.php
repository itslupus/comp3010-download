<?php
    chdir('/home/user/comp3010');

    $tmpFile = tmpfile();
    $tmpFilePath = stream_get_meta_data($tmpFile)['uri'];
    
    $curl = curl_init();

//    curl_setopt($curl, CURLOPT_VERBOSE, 1);
    curl_setopt($curl, CURLOPT_COOKIEJAR, $tmpFilePath);
    curl_setopt($curl, CURLOPT_COOKIEFILE, $tmpFilePath);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux i586; rv:31.0) Gecko/20100101 Firefox/70.0');

    echo(">> LOGIN\n");
    curl_setopt($curl, CURLOPT_URL, 'https://universityofmanitoba.desire2learn.com/d2l/lp/auth/login/login.d2l');
    curl_setopt($curl, CURLOPT_NOBODY, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, array(
        'userName' => $_SERVER['argv'][1],
        'password' => $_SERVER['argv'][2]
    ));
    curl_exec($curl);

    echo(">> FETCHING MONDAY VIDEO HEADERS\n");
    $headers = getVideoHeaders('Monday');
    if (!isOldVideo('Monday', $headers[6])) {
        echo(">> FETCHING MONDAY VIDEO FILE\n");
        writeVideo(getVideoData('Monday'), $headers[6]);
    }

    echo("\n>> FETCHING WEDNESDAY VIDEO HEADERS\n");
    $headers = getVideoHeaders('Wednesday');
    if (!isOldVideo('Wednesday', $headers[6])) {
        echo(">> FETCHING WEDNESDAY VIDEO FILE\n");
        writeVideo(getVideoData('Wednesday'), $headers[6]);
    }

    echo("\n>> FETCHING FRIDAY VIDEO HEADERS\n");
    $headers = getVideoHeaders('Friday');
    if (!isOldVideo('Friday', $headers[6])) {
        echo(">> FETCHING FRIDAY VIDEO FILE\n");
        writeVideo(getVideoData('Friday'), $headers[6]);
    }

    curl_close($curl); 
    fclose($tmpFile);

	echo("\n>> ALL DONE\n");

    function getVideoHeaders($day) {
        global $curl;
        curl_setopt($curl, CURLOPT_URL, "https://universityofmanitoba.desire2learn.com/content/enforced3/345094-51510.202010/$day.mp4");
        curl_setopt($curl, CURLOPT_NOBODY, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        $result = curl_exec($curl);

        // 2 |=> Content-Type (video/mp4)
        // 3 |=> Content-Length (bytes)
        // 6 |=> Last-Modified
        return explode("\n", $result);
    }

    function getVideoData($day) {
        global $curl;
        curl_setopt($curl, CURLOPT_URL, "https://universityofmanitoba.desire2learn.com/content/enforced3/345094-51510.202010/$day.mp4");
        curl_setopt($curl, CURLOPT_NOBODY, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        return curl_exec($curl);
    }

    function isOldVideo($day, $lastModifedHeader) {
        $lowercaseDay = strtolower($day);
        $lastUpdateValue = explode('Last-Modified: ', $lastModifedHeader)[1];

        $modifiedFileName = "$lowercaseDay-last-update";
        if (file_exists(__DIR__ . "/$modifiedFileName")) {
            $modifiedFile = fopen(__DIR__ . "/$modifiedFileName", 'r');
            $localLastUpdate = fread($modifiedFile, filesize($modifiedFileName));
            fclose($modifiedFile);
            
            if ($localLastUpdate === $lastUpdateValue) {
                return true;
            }
        }

        $modifiedFile = fopen(__DIR__ . "/$modifiedFileName", 'w');
        fwrite($modifiedFile, $lastUpdateValue);
        fclose($modifiedFile);
        
        return false;
    }
    
    function writeVideo($data, $lastModifedHeader) {
        $lastUpdateValue = explode('Last-Modified: ', $lastModifedHeader)[1];
        $unixTime = strtotime($lastUpdateValue);

        $formatDate = date('Ymd', $unixTime);

        $videoFile = fopen(__DIR__ . "/comp3010-$formatDate.mp4", 'w');
        fwrite($videoFile, $data);
        fclose($videoFile);
    }
?>
