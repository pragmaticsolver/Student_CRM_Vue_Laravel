<?php

namespace App;

use Illuminate\Support\Facades\Storage;

class File
{
    private $file;

    private $user_id;

    private $time;

    private $disk;

    private $type;

    private $path;

    private $name;

    public function __construct()
    {
        $this->user_id = \Auth::user()->id;
        $this->time = date("M,d,Y H:i:s");
        $this->disk = 'public';
        $this->type = 'default';
        $this->path = 'images/';
    }

    public function setDisk($disk)
    {
        $this->disk = $disk;
        return $this;
    }

    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function setName($name = null)
    {
        $this->name = ($name) ? $name.'.'.$this->file->extension() : md5($this->user_id.$this->time).'.'.$this->file->extension();
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getContent($type = null)
    {
        if(!$type) $type = $this->type;

        switch($type) {
            case 'csv':
                return $this->_formatContentCSV();
            default:
                return file_get_contents($this->file);
        }
    }

    public function store()
    {
        $store = Storage::disk($this->disk)->put($this->path.$this->name, $this->getContent('default'));
        if(!$store) throw new \Exception(__('global.error-upload-file'));

        return $this;
    }

    private function _formatContentCSV()
    {
        $csvFile = file($this->file);

        $data = [];
        foreach ($csvFile as $line) {
            $data[] = str_getcsv($line);
        }

        return $data;
    }

    public function delete()
    {
        return Storage::disk($this->disk)->delete($this->path.$this->getName());
    }
}
