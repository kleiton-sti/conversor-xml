<?php


function sanitizeString(string $data = null)
{
    if(is_null($data)){
        return $data;
    }
   
    $cleanData = strip_tags($data);
    $cleanData = trim($cleanData);
    $cleanData = removerAcentos($cleanData);
    $cleanData = strtoupper($cleanData);
    return $cleanData;
}

function removerAcentos(String $data = null){
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(Ç)/","/(ç)/"),explode(" ","a A e E i I o O u U n N C c"),$data);
}

function sanitizeEmail (String $data = null)
{
    $sanitizedEmail = filter_var($data, FILTER_SANITIZE_EMAIL);
    return $sanitizedEmail;
}

function converteDataParaBr($data)
{
    return implode('/', array_reverse(explode('-', $data))) ? : '';
}

?>