<?php
// 전화번호의 숫자만 취한 후 중간에 하이픈(-)을 넣는다.
function custom_telstr($tel)
{
    $tel = preg_replace("/[^0-9]/", "", $tel);
    // 숫자 이외 제거
    if (substr($tel, 0, 2) == '02')
        return preg_replace("/([0-9]{2})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $tel);
    else if (strlen($tel) == '8' && (substr($tel, 0, 2) == '15' || substr($tel, 0, 2) == '16' || substr($tel, 0, 2) == '18'))
        // 지능망 번호이면  return
        return preg_replace("/([0-9]{4})([0-9]{4})$/", "\\1-\\2", $tel);
    else
        return preg_replace("/([0-9]{3})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $tel);
}