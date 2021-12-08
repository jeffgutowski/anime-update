<?php
function csvToArray($filename = '', $delimiter = ',')
{
    $header = null;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== false) {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            if (!$header) {
                $header = $row;
            } else {
                $data[] = array_combine($header, $row);
            }
        }
        fclose($handle);
    }
    return $data;
}

function csvToObject($filename = '', $delimiter = ',')
{
    $header = null;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== false) {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            if (!$header) {
                // object keys cannot have dashes (-) because of syntax
                foreach ($row as $key => $item) {
                    $row[$key] = str_replace('-', '_', $item);
                }
                $header = $row;
            } else {
                $data[] = array_combine($header, $row);
            }
        }
        fclose($handle);
    }
    return json_decode(json_encode($data));
}