<?php

namespace InWeb\Admin\App;

use InWeb\Base\Entity;

trait WithContentFiles
{
    protected static function bootWithContentImages()
    {
        static::deleting(function (Entity $model) {
            \Storage::disk('public')->deleteDirectory($model->contentFilesPath());
        });
    }

    public function contentFilesPath()
    {
        return 'contents/' . class_basename($this) . '/' . $this->getKey();
    }
}
