<?php

namespace App\Services;

use App;
use Auth;
use DB;
use File;
use Illuminate\Support\MessageBag;

abstract class Service
{
    /*
    |--------------------------------------------------------------------------
    | Base Service
    |--------------------------------------------------------------------------
    |
    | Base service, setting up error handling.
    |
    */

    /**
     * Errors.
     *
     * @var Illuminate\Support\MessageBag
     */
    protected $errors = null;
    protected $cache = [];
    protected $user = null;

    /**
     * Default constructor.
     */
    public function __construct()
    {
        $this->callMethod('beforeConstruct');
        $this->resetErrors();
        $this->callMethod('afterConstruct');
    }

    /**
     * Return if an error exists.
     *
     * @return bool
     */
    public function hasErrors()
    {
        return $this->errors->count() > 0;
    }

    /**
     * Return if an error exists.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function hasError($key)
    {
        return $this->errors->has($key);
    }

    /**
     * Return errors.
     *
     * @return Illuminate\Support\MessageBag
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * Return errors.
     *
     * @return array
     */
    public function getAllErrors()
    {
        return $this->errors->unique();
    }

    /**
     * Return error by key.
     *
     * @param mixed $key
     *
     * @return Illuminate\Support\MessageBag
     */
    public function getError($key)
    {
        return $this->errors->get($key);
    }

    /**
     * Empty the errors MessageBag.
     */
    public function resetErrors()
    {
        $this->errors = new MessageBag();
    }

    public function remember($key = null, $fn = null)
    {
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        return $this->cache[$key] = $fn();
    }

    public function forget($key = null)
    {
        unset($this->cache[$key]);
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function user()
    {
        return $this->user ? $this->user : Auth::user();
    }

    // 1. Old image exists, want to move it to a new location.
    // 2. Given new image, want to upload it to new location.
    //    (old image may or may not exist)
    // 3. Nothing happens (no changes required)
    public function handleImage($image, $dir, $name, $oldName = null, $copy = false)
    {
        if (!$oldName && !$image) {
            return true;
        }

        if (!$image) {
            // Check if we're moving an old image, and move it if it does.
            if ($oldName) {
                return $this->moveImage($dir, $name, $oldName, $copy);
            }
        } else {
            // Don't want to leave a lot of random images lying around,
            // so move the old image first if it exists.
            if ($oldName) {
                $this->moveImage($dir, $name, $oldName, $copy);
            }

            // Then overwrite the old image.
            return $this->saveImage($image, $dir, $name, $copy);
        }

        return false;
    }

    public function deleteImage($dir, $name)
    {
        unlink($dir.'/'.$name);
    }

    /**
     * Calls a service method and injects the required dependencies.
     *
     * @param string $methodName
     *
     * @return mixed
     */
    protected function callMethod($methodName)
    {
        if (method_exists($this, $methodName)) {
            return App::call([$this, $methodName]);
        }
    }

    /**
     * Add an error to the MessageBag.
     *
     * @param string $key
     * @param string $value
     */
    protected function setError($key, $value)
    {
        $this->errors->add($key, $value);
    }

    /**
     * Add multiple errors to the message bag.
     *
     * @param Illuminate\Support\MessageBag $errors
     */
    protected function setErrors($errors)
    {
        $this->errors->merge($errors);
    }

    /**
     * Commits the current DB transaction and returns a value.
     *
     * @param mixed $return
     *
     * @return mixed $return
     */
    protected function commitReturn($return = true)
    {
        DB::commit();

        return $return;
    }

    /**
     * Rolls back the current DB transaction and returns a value.
     *
     * @param mixed $return
     *
     * @return mixed $return
     */
    protected function rollbackReturn($return = false)
    {
        DB::rollback();

        return $return;
    }

    /**
     * Returns the current field if it is numeric, otherwise searches for a field if it is an array or object.
     *
     * @param mixed  $data
     * @param string $field
     *
     * @return mixed
     */
    protected function getNumeric($data, $field = 'id')
    {
        if (is_numeric($data)) {
            return $data;
        } elseif (is_object($data)) {
            return $data->$field;
        } elseif (is_array($data)) {
            return $data[$field];
        } else {
            return 0;
        }
    }

    // Moves an old image within the same directory.
    private function moveImage($dir, $name, $oldName, $copy = false)
    {
        if ($copy) {
            File::copy($dir.'/'.$oldName, $dir.'/'.$name);
        } else {
            File::move($dir.'/'.$oldName, $dir.'/'.$name);
        }

        return true;
    }

    // Moves an uploaded image into a directory, checking if it exists.
    private function saveImage($image, $dir, $name, $copy = false)
    {
        if (!file_exists($dir)) {
            // Create the directory.
            if (!mkdir($dir, 0755, true)) {
                $this->setError('error', 'Failed to create image directory.');

                return false;
            }
            chmod($dir, 0755);
        }
        if ($copy) {
            File::copy($image, $dir.'/'.$name);
        } else {
            File::move($image, $dir.'/'.$name);
        }
        chmod($dir.'/'.$name, 0755);

        return true;
    }
}
