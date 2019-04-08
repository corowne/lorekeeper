<?php namespace App\Services;

use App;
use Auth;
use DB;
use Request;
use Illuminate\Support\MessageBag;

abstract class Service {

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
     * Calls a service method and injects the required dependencies.
     * @param string $methodName
     * @return mixed
     */
    protected function callMethod($methodName)
    {
        if(method_exists($this, $methodName)) return App::call([$this, $methodName]);
    }

    /**
     * Return if an error exists. 
     * @return bool
     */
    public function hasErrors()
    {
        return $this->errors->count() > 0;
    }

    /**
     * Return if an error exists. 
     * @return bool
     */
    public function hasError($key)
    {
        return $this->errors->has($key);
    }

    /**
     * Return errors. 
     * @return Illuminate\Support\MessageBag
     */
    public function errors()
    {
        return $this->errors;
    }
    /**
     * Return errors. 
     * @return array
     */
    public function getAllErrors()
    {
        return $this->errors->unique();
    }

    /**
     * Return error by key. 
     * @return Illuminate\Support\MessageBag
     */
    public function getError($key)
    {
        return $this->errors->get($key);
    }

    /**
     * Empty the errors MessageBag.
     * @return void
     */
    public function resetErrors()
    {
        $this->errors = new MessageBag();
    }

    /**
     * Add an error to the MessageBag.
     * @param string $key
     * @param string $value
     * @return void
     */
    protected function setError($key, $value)
    {
        $this->errors->add($key, $value);
    }

    /**
     * Add multiple errors to the message bag
     * @param Illuminate\Support\MessageBag $errors
     * @return void
     */
    protected function setErrors($errors) 
    {
        $this->errors->merge($errors);
    }

    /**
     * Commits the current DB transaction and returns a value.
     * @param mixed $return
     * @return mixed $return
     */
    protected function commitReturn($return = true)
    {
        DB::commit();
        return $return;
    }

    /**
     * Rolls back the current DB transaction and returns a value.
     * @param mixed $return
     * @return mixed $return
     */
    protected function rollbackReturn($return = false)
    {
        DB::rollback();
        return $return;
    }

    /**
     * Returns the current field if it is numeric, otherwise searches for a field if it is an array or object.
     * @param mixed $data
     * @param string $field
     * @return mixed 
     */
    protected function getNumeric($data, $field = 'id')
    {
        if(is_numeric($data)) return $data;
        elseif(is_object($data)) return $data->$field;
        elseif(is_array($data)) return $data[$field];
        else return 0;
    }

    public function remember($key = null, $fn = null)
    {
        if(isset($this->cache[$key])) return $this->cache[$key];
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

}
