<?php

$url = 'https://api.cellvoz.com/v2/sms/single?apiKey=77ccdef01145863b7bf40252afde5da7023f621b&account=00486966949&password=Juryzu57&message=PruebacellVOz&number=3186337855&type=1';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
curl_close($ch);

echo $res;
