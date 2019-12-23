<?php namespace App\Services;

use App\Services\Service;

use File;
use Config;

class FileManager extends Service
{
    public function createDirectory($dir) 
    {
        if(file_exists($dir)) $this->setError('Folder already exists.');
        else {
            // Create the directory.
            if (!mkdir($dir, 0755, true)) {
                $this->setError('Failed to create folder.');
                return false;
            }
            chmod($dir, 0755);
        }
        return true;
    }

    public function deleteDirectory($dir) 
    {
        if(!file_exists($dir)) {
            $this->setError('error', 'Directory does not exist.');
            return false;
        }
        $files = array_diff(scandir($dir), array('.', '..'));
        if(count($files)) {
            $this->setError('error', 'Cannot delete a folder that contains files.');
            return false;
        }
        rmdir($dir);
        return true;
    }

    public function renameDirectory($dir, $oldName, $newName) 
    {
        if(!file_exists($dir . '/' . $oldName)) {
            $this->setError('error', 'Directory does not exist.');
            return false;
        }
        $files = array_diff(scandir($dir . '/' . $oldName), array('.', '..'));
        if(count($files)) {
            $this->setError('error', 'Cannot delete a folder that contains files.');
            return false;
        }
        rename($dir . '/' . $oldName, $dir . '/' . $newName);
        return true;
    }

    public function uploadFile($file, $dir, $name, $isFileManager = true)
    {
        $directory = public_path(). ($isFileManager ? '/files'.($dir ? '/'.$dir : '') : '/images');
        if(!file_exists($directory))
        {
            $this->setError('error', 'Folder does not exist.');
        }
        File::move($file, $directory . '/' . $name);
        chmod($directory . '/' . $name, 0755);
        
        return true;
    }

    public function uploadCss($file)
    {
        File::move($file, public_path() . '/css/custom.css');
        chmod(public_path() . '/css/custom.css', 0755);
        
        return true;
    }

    public function deleteFile($path)
    {
        if(!file_exists($path)) {
            $this->setError('error', 'File does not exist.');
            return false;
        }
        unlink($path);
        return true;
    }

    public function moveFile($oldDir, $newDir, $name)
    {
        if(!file_exists($oldDir . '/' . $name)) {
            $this->setError('error', 'File does not exist.');
            return false;
        }
        else if(!file_exists($newDir)) {
            $this->setError('error', 'Destination does not exist.');
            return false;
        }
        rename($oldDir . '/' . $name, $newDir . '/' . $name);
        return true;
    }

    public function renameFile($dir, $oldName, $newName)
    {
        if(!file_exists($dir . '/' . $oldName)) {
            $this->setError('error', 'File does not exist.');
            return false;
        }
        rename($dir . '/' . $oldName, $dir . '/' . $newName);
        return true;
    }
}