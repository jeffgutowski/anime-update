<?php

function saveS3(\Intervention\Image\Image $image, $destionation_path, $extension = "jpg", $old_img_url_path = null)
{
    $check = true;
    // check to see if file already exists on S3, if so generate a different uuid
    while($check) {
        $newfilename = \Illuminate\Support\Str::uuid()->toString().'.'.$extension;
        $destination_path = env('S3_DESTINATION').$destionation_path;
        $check = file_exists(env('S3_BUCKET_URL').$destination_path.'/'.$newfilename);
    }

    \Storage::disk("s3")->put($destination_path.'/'.$newfilename, $image->stream());

    // delete old file
    if (!is_null($old_img_url_path)) {
        \Storage::disk("s3")->delete(str_replace(env('S3_BUCKET_URL'), '', $old_img_url_path));
    }
    return env('S3_BUCKET_URL').$destination_path.'/'.$newfilename;
}
