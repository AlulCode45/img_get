<?php
system('clear');
function logo(){
   echo "  ####   Tools: Image Downloader\n";
   echo "  ####   Author: CapthaCode404\n";
   echo "  ####   Team : DeveloperSec45\n";
   echo " ###### \n";
   echo "  #### \n";
   echo "   ## \n";
   echo "\n**********\n
   ";
}   
function downloadGambar($img_url, $dir_location='') {
    $returns = array();
    if (!empty($dir_location) AND !is_dir($dir_location)) {
        if(!mkdir($dir_location, 0777, true)) {
            $returns['status'] = 'error';
            $returns['message'] = 'gagal membuat folder '.$dir_location;
            return $returns;
        }
    }
    
    $url = filter_var($img_url, FILTER_SANITIZE_URL);    
    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
        $returns['status'] = 'error';
        $returns['message'] = 'URL tidak valid';
        return $returns;
    }
    $path = parse_url($img_url, PHP_URL_PATH);
    $file_name = basename($path);
    $file_ext = pathinfo($img_url, PATHINFO_EXTENSION);//ext
    $file_ext = strtolower($file_ext);
    
    if (empty($file_name)) {
        $returns['status'] = 'error';
        $returns['message'] = 'Nama file tidak valid';
        return $returns;
    }
    
    if (strpos($file_ext, '?')!==false) {
        $file_ext = substr($file_ext,0,strpos($file_ext, '?'));
    }
    
    if ($file_ext==='png' OR $file_ext==='jpg' OR $file_ext==='jpeg' OR $file_ext==='gif') {
        //$file_name = $file_name;
    } else {
        if (!empty($file_ext)) {      
            $file_ext = '';            
        }
    }
    
    $ch = curl_init ();
    curl_setopt($ch, CURLOPT_URL,$img_url);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    //curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    $raw = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close ($ch);
    
    if (!empty($curl_error) OR $http_status!=200) {
        $returns['status'] = 'error';
        $returns['message'] = 'http status: '.$http_status.' '.$curl_error;
        return $returns;
    }
    if (empty($file_ext)) {
        $file_name_temp = str_replace('.', '', uniqid(rand(100,999),true));
        $saveto = $dir_location.$file_name_temp;
        if (file_exists($saveto)) {
            @unlink($saveto);
        }
        
        @file_put_contents($saveto, $raw);
        
        $is_image = false;
        switch (exif_imagetype($saveto)) {
            case IMAGETYPE_GIF:
              rename($saveto, $saveto . '.gif'); $saveto = $saveto . '.gif'; $file_name = $file_name_temp . '.gif'; $is_image = true; break;
            case IMAGETYPE_JPEG:
              rename($saveto, $saveto . '.jpg'); $saveto = $saveto . '.jpg'; $file_name = $file_name_temp . '.jpg'; $is_image = true; break;
            case IMAGETYPE_PNG:
              rename($saveto, $saveto . '.png'); $saveto = $saveto . '.png'; $file_name = $file_name_temp . '.png'; $is_image = true; break;
        }
        
        if (!$is_image) {
            @unlink($saveto);
            $returns['status'] = 'error';
            $returns['message'] = 'File bukan gambar';
            return $returns;
        }
    } else {
    
        $saveto = $dir_location.$file_name;
        if (file_exists($saveto)) {
            @unlink($saveto);
        }
        
        @file_put_contents($saveto, $raw);
        $is_image = false;
        switch (exif_imagetype($saveto)) {
            case IMAGETYPE_GIF:
              $is_image = true; break;
            case IMAGETYPE_JPEG:
              $is_image = true; break;
            case IMAGETYPE_PNG:
              $is_image = true; break;
        }
        
        if (!$is_image) {
            @unlink($saveto);
            $returns['status'] = 'error';
            $returns['message'] = 'File bukan gambar';
            return $returns;
        }
    }
    
    if (!is_file($saveto)) {
        $returns['status'] = 'error';
        $returns['message'] = 'Gagal simpan gambar';
        return $returns;
    }
    
    $returns['status'] = 'ok';
    $returns['message'] = 'success';
    $returns['img_url'] = $img_url;
    $returns['dir_location'] = $dir_location;    
    $returns['img_name'] = $file_name;
    $returns['saveto'] = $saveto;
    
    return $returns;
}
$dir_location = 'down_gambar/';
logo();
echo '';
echo "Your Url Image : ";
$img_url = trim(fgets(STDIN));
$downloadGambar = downloadGambar($img_url, $dir_location);
print_r($downloadGambar);
if (isset($downloadGambar['saveto'])) {
    echo "";
}