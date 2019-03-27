<?php


namespace App\Helpers;

use App\User;
use Illuminate\Support\Facades\File;

class FileHelper {

    public static function bytesToHuman($bytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public static function emailToImage($email){
        $user = User::whereEmail($email)->first();
        return ($user) ? "/$user->pic" : "https://www.gravatar.com/avatar/". md5(strtolower(trim($email)));
    }

    public static function saveFile($file,$subFolder){

        $result = self::getAndCreatePath($file->getClientOriginalName(),$subFolder);

        if($result['success']){
            $file->move($result['path'], $result['name']);
            return $result['path'].'/'.$result['name'];
        }

        return null;
    }

    public static function getAndCreatePath($name,$subFolder){
        $name = date('U').'_'.$name;
        $path = "uploads/$subFolder/";
        $path = $path . date('Y') . '/' .date('F');

        if(!File::exists($path)) {
            $fileSaved = File::makeDirectory($path, 0775, true);
        }else{
            $fileSaved = true;
        }

        return ['success' => $fileSaved,'name' =>$name,'path'=>$path];
    }

    public static function saveImageFromBase64($base64,$name,$subFolder){
        $results = self::getAndCreatePath($name,$subFolder);
        $imagePath = $results['path'].'/'.$results['name'];
        file_put_contents($imagePath, base64_decode($base64));
        return $imagePath;
    }

    public static function deleteFileIfNotDefault($file,$default = null){
        if(File::exists($file) && $file != $default){
            File::delete($file);
        }
    }
}