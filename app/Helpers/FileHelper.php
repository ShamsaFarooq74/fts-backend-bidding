<?php
namespace App\Helpers;
use Illuminate\Http\UploadedFile;

class FileHelper{

    public static function uploadFile(UploadedFile $file, $destinationPath)
    {
        $fileName = date("dmyHis.") . gettimeofday()["usec"] . '_' . $file->getClientOriginalName();
        $file->move(public_path($destinationPath), $fileName);
        return $fileName;
    }
    function getImageUrl($image_name,$type ='') {
        $domain = 'http://fts-taxi-bidding-backend.test';
        if($type == 'ModelThumbnail')
        {
            return $domain.'/images/settings/vehicle-img/'.$image_name;
        }
        elseif($type == 'dummyImg')
        {
            return $domain.'/images/dummy-img/'.$image_name;
        }
    }
    
}
