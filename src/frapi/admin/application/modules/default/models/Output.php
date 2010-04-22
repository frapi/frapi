<?php
class Default_Model_Output extends Lupin_Model
{
    protected $config;
    
    public function __construct()
    {
        $this->config = new Lupin_Config_Xml('outputs');
    }
    
    public function sync()
    {
        $path = ROOT_PATH . '/library/Frapi/Output/';
        $dir  = new DirectoryIterator($path);
        $data = array();
        foreach ($dir as $d) {
            if ($d->isDot() ||
                $d->isDir() ||
                $d->getFilename() === 'Interface.php' ||
                substr($d->getFilename(), 0, 1) === '.'
            ) {
                continue;
            }

            $file = substr($d->getFilename(), 0, -4);

            $output = $this->config->getByField('output', 'name', $file);

            if (empty($output)) {
                $this->config->add('output', array(
                    'name'    => $file,
                    'enabled' => '0',
                    'default' => '0',
                ));
            }
        }

        return true;
    }

    public function makeDefault($id)
    {
        // Update the value with default set to 1 to 0
        try {
            $this->config->update('output', 'default', '1', array('default' => '0'));
        } catch (Exception $e) {}

        // Now set the requested format to default.
        try {
            $this->config->update('output', 'name', $id, array('default' => '1'));
        } catch (Exception $e) {}
        
        $this->refreshAPCCache();
    }

    public function getAll()
    {
        $outputs = $this->config->getAll('output');
        return $outputs;
    }

    public function enable($id)
    {
        // Now set the requested format to enabled
        try {
            $this->config->update('output', 'name', $id, array('enabled' => '1'));
        } catch (Exception $e) {}
        
        $this->refreshAPCCache();
    }

    public function disable($id)
    {
        // Now set the requested .
        try {
            $this->config->update('output', 'name', $id, array('enabled' => '0'));
        } catch (Exception $e) {}
        
        $this->refreshAPCCache();
    }

    /**
     * Refresh the APC cache by deleting APC entries.
     *
     * @return void
     **/
    public function refreshAPCCache()
    {
        apc_delete('Output.default-format');
    }
}