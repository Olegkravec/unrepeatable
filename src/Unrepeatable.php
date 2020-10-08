<?php
namespace OlegKravec\Unrepeatable;

trait Unrepeatable
{
    /**
     * Default providers states file
     * @var string
     */
    private static $filename = "instances.log";
    /**
     * Preloaded providers states
     * @var array
     */
    private $providers = [];



    /**
     * Retrieving path to file with saved providers states
     * @return string
     */
    public function getFilepath() : string{
        if(!empty(env("PROVIDER_STATES_FILE"))) {
            return env("PROVIDER_STATES_FILE");
        }

        return "../" . static::$filename;
    }

    /**
     * Save file with runned providers
     * @return void
     */
    private function dumpProviders() : void {
        file_put_contents($this->getFilepath(), json_encode($this->providers));
    }

    /**
     * Checks if the class was already runned with the given key.
     *
     * @param string $key
     * @return bool
     */
    public function isPreviouslyRunned(string $key = "default") : bool {
        try{
            // Checks if presets is present
            if(file_exists($this->getFilepath())){
                // Load file with presets
                $file = file_get_contents($this->getFilepath());

                // Parses file with presets
                $this->providers = json_decode($file, true);

                // Checks if a cat was already added in dumps
                if(!empty($this->providers[get_class($this)])){

                    // returns if a dog is already stored as "runned"
                    return !empty($this->providers[get_class($this)][$key]);
                }
            }

            // return false anyway
            return false;
        } finally {
            // now we must save the key with the class namespace for future non-using

            // checks if the traitable class is already added to presets
            if(!empty($this->providers[get_class($this)])){

                // checks if the given key is already added to presets, and adds it
                if(empty($this->providers[get_class($this)][$key]))
                    $this->providers[get_class($this)][$key] = time();

            }else{
                // If the given class wan't already added to presets we just putting timestamp with the given key to presets
                $this->providers[get_class($this)] = [$key => time()];
            }
        }
    }

    /**
     * Anyway save the class as "already runned"
     *
     * @return void
     */
    public function __destruct()
    {
        $this->dumpProviders();
    }
}