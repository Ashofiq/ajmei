<?php

namespace App\Http\Controllers\Image;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function upload($data)
    {   
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'http://image.globalerpserver.com/uploader',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            // CURLOPT_POSTFIELDS => array('image'=> new CURLFILE(''),'imageType' => 'product', 'sourceType' => 'base64'),
            CURLOPT_POSTFIELDS => ['image' => $data, 'imageType' => 'ajmeri', 'sourceType' => 'base64'],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function upload_image(Request $request)
    {
        $baseImageData = $request->baseImage;
        if ($baseImageData == NULL) {
           return '';
        }

        $resImage = [];
        foreach ($baseImageData as $key => $value) {
            $resImage[] = $this->upload($value);
        }
        return $resImage;
    }
}
