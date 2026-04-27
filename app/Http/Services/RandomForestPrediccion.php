<?php

namespace App\Http\Services;

class RandomForestPrediccion
{
    public $filename, $input, $sample=[], $targets=[];

    public function __construct($filename, $input)
    {
        $this->filename = $filename;
        $this->input = $input;
    }

    public function predictResult()
    {
        $this->readFile();
    }

    public function readFile()
    {
        $file = fopen($this->filename, 'r');

        if($filename !== false)
        {
            while( ($data = fgetcsv($file)) !== false)
            {
                //$num = count($data);
                console.log($data);
            }
        }
    }
}