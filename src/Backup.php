<?php

namespace pxgamer\SQLBak;

/**
 * Class Backup
 * @package pxgamer\SQLBak
 */
class Backup
{
    /**
     * @var array
     */
    public $options;
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $password;
    /**
     * @var array
     */
    private $databases = [];

    /**
     * Backup constructor.
     *
     * @param string $username
     * @param string $password
     * @param array  $options
     */
    public function __construct(string $username, string $password, $options = [])
    {
        $this->options = array_merge([
            'compress'        => true,
            'outputDirectory' => getcwd()
        ], $options);

        $this->username = $username ?? null;
        $this->password = $password ?? null;
    }

    /**
     * Add a database to the backup list
     *
     * @param string $name
     * @return bool
     */
    public function addDatabase($name)
    {
        if (!in_array($name, $this->databases)) {
            return ($this->databases[] = $name);
        }

        return true;
    }

    /**
     * Remove a database from the backup list
     *
     * @param string $name
     * @return bool
     */
    public function removeDatabase($name)
    {
        if (($key = array_search($name, $this->databases)) !== false) {
            unset($this->databases[$key]);
        }

        return true;
    }

    /**
     * Execute the backup for all databases in array
     *
     * @param null|string $suffix
     * @param bool $boolAsString
     * @return array
     * @throws \Exception
     */
    public function execute($suffix = null, $boolAsString = false)
    {
        $results = [];
        if (!$suffix) {
            $suffix = date('Y-m-d');
        }

        foreach ($this->databases as $database) {
            $result = $this->backup($database, $suffix);
            $results[] = [
                $database,
                $boolAsString ? $result : $result ? 'true' : 'false',
            ];
        }

        return $results;
    }

    /**
     * Backup and Gzip a database
     *
     * @param string $name
     * @param string $suffix
     * @return bool
     * @throws \Exception
     */
    protected function backup(string $name, string $suffix)
    {
        if (!is_dir($this->options['outputDirectory'] . DIRECTORY_SEPARATOR . $suffix)) {
            mkdir($this->options['outputDirectory'] . DIRECTORY_SEPARATOR . $suffix);
        }

        $cmd = 'mysqldump' .
               ' -u ' . $this->username .
               ' -p' . $this->password .
               ' ' . $name .
               ' > ' . $this->options['tmpPath'] . DIRECTORY_SEPARATOR . $name . '.sql';
        exec($cmd, $result, $status);

        if ($status === 0) {
            $gz = gzopen(
                $this->options['outputDirectory'] . DIRECTORY_SEPARATOR . $suffix . DIRECTORY_SEPARATOR . $name . '.sql.gz',
                $this->options['compress'] ? 'w9' : 'w'
            );

            $bytes = gzwrite($gz, file_get_contents($this->options['tmpPath'] . DIRECTORY_SEPARATOR . $name . '.sql'));

            return ($bytes > 0);
        } else {
            throw new \Exception('An error occurred while backing up the database "' . $name . '"');
        }

    }
}