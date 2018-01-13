<?php

	$res = file_get_contents("/tf/formats.json.old");

	$formats = json_decode($res);

	foreach ( $formats->format as $format ) {
//		$format->key = base64_encode(linear_hangul(base64_decode($format->data)));
		$format->key = json_decode(base64_decode($format->data));
		unset($format->data);
		for ( $r = 0; $r < count($format->key); $r++ ) {
			for ( $c = 0; $c < count($format->key[$r]); $c++ ) {
				$format->key[$r][$c] = base64_encode(linear_hangul($format->key[$r][$c]));
			}
		}
	}

	foreach ( $formats->chars as $key => $value) {
		$formats->chars->{$key} = base64_encode(linear_hangul(base64_decode($value)));
	}

	$fp = fopen("/tf/formats2.json","w");
	if ($fp) {
		fwrite($fp, json_encode($formats));
		fclose($fp);
	}


function utf8_strlen($str) { return mb_strlen($str, 'UTF-8'); }
function utf8_charAt($str, $num) { return mb_substr($str, $num, 1, 'UTF-8'); }
function utf8_ord($ch) {
  $len = strlen($ch);
  if($len <= 0) return false;
  $h = ord($ch{0});
  if ($h <= 0x7F) return $h;
  if ($h < 0xC2) return false;
  if ($h <= 0xDF && $len>1) return ($h & 0x1F) <<  6 | (ord($ch{1}) & 0x3F);
  if ($h <= 0xEF && $len>2) return ($h & 0x0F) << 12 | (ord($ch{1}) & 0x3F) << 6 | (ord($ch{2}) & 0x3F);          
  if ($h <= 0xF4 && $len>3) return ($h & 0x0F) << 18 | (ord($ch{1}) & 0x3F) << 12 | (ord($ch{2}) & 0x3F) << 6 | (ord($ch{3}) & 0x3F);
  return false;
}

function linear_hangul($str) {
  $cho = array("ㄱ","ㄲ","ㄴ","ㄷ","ㄸ","ㄹ","ㅁ","ㅂ","ㅃ","ㅅ","ㅆ","ㅇ","ㅈ","ㅉ","ㅊ","ㅋ","ㅌ","ㅍ","ㅎ");
  $jung = array("ㅏ","ㅐ","ㅑ","ㅒ","ㅓ","ㅔ","ㅕ","ㅖ","ㅗ","ㅘ","ㅙ","ㅚ","ㅛ","ㅜ","ㅝ","ㅞ","ㅟ","ㅠ","ㅡ","ㅢ","ㅣ");
  $jong = array("","ㄱ","ㄲ","ㄳ","ㄴ","ㄵ","ㄶ","ㄷ","ㄹ","ㄺ","ㄻ","ㄼ","ㄽ","ㄾ","ㄿ","ㅀ","ㅁ","ㅂ","ㅄ","ㅅ","ㅆ","ㅇ","ㅈ","ㅊ","ㅋ"," ㅌ","ㅍ","ㅎ");
  $result = "";
  for ($i=0; $i<utf8_strlen($str); $i++) {
    $code = utf8_ord(utf8_charAt($str, $i)) - 44032;
    if ($code > -1 && $code < 11172) {        
      $cho_idx = $code / 588;      
      $jung_idx = $code % 588 / 28;  
      $jong_idx = $code % 28;
      $result .= $cho[$cho_idx].$jung[$jung_idx].$jong[$jong_idx];
    } else {
       $result .= utf8_charAt($str, $i);
    }
  }
  return $result;
}

function combine_hangul($str) {
  $hangul = array(
	array("ㄱ","ㄲ","ㄴ","ㄷ","ㄸ","ㄹ","ㅁ","ㅂ","ㅃ","ㅅ","ㅆ","ㅇ","ㅈ","ㅉ","ㅊ","ㅋ","ㅌ","ㅍ","ㅎ"),
  	array("ㅏ","ㅐ","ㅑ","ㅒ","ㅓ","ㅔ","ㅕ","ㅖ","ㅗ","ㅘ","ㅙ","ㅚ","ㅛ","ㅜ","ㅝ","ㅞ","ㅟ","ㅠ","ㅡ","ㅢ","ㅣ"),
  	array("","ㄱ","ㄲ","ㄳ","ㄴ","ㄵ","ㄶ","ㄷ","ㄹ","ㄺ","ㄻ","ㄼ","ㄽ","ㄾ","ㄿ","ㅀ","ㅁ","ㅂ","ㅄ","ㅅ","ㅆ","ㅇ","ㅈ","ㅊ","ㅋ"," ㅌ","ㅍ","ㅎ"),
  	array("ㅏ","ㅐ","ㅑ","ㅒ","ㅓ","ㅔ","ㅕ","ㅖ","ㅗ","ㅘ","ㅙ","ㅚ","ㅛ","ㅜ","ㅝ","ㅞ","ㅟ","ㅠ","ㅡ","ㅢ","ㅣ")
  );

  $result = "";

  $cnt = utf8_strlen($str);

  for ($i=0; $i<$cnt; $i++) {

    for ($j=0; $j < 4 && $i + $j < $cnt; $j++) {
        $hangul[$j] = array_search(utf8_charAt($str, $i + $j), $hangul[$j]);
    }
    if (0) {
	
    } else {
       $result .= utf8_charAt($str, $i);
    }
  }
  return $result;
}

?>


