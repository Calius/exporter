<?php

/**
 * This file is released as free software: You can redistribute it and/or modify
 * it under the terms of the GNU General Public License v2 as published by
 * the Free Software Foundation, version 2 of the License.
 *
 * It is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with. If not, see <http://www.gnu.org/licenses>.
 *
 * @author      Christian André Lewin <christi@nlew.in>
 * @copyright   (c) 2005-2015 Lewinlabs. All Rights Reserved.
 * @link        https://www.lewinlabs.de
 * @license     GNU GPL v2
 */
class LewinlabsAbstractExporter
{
    /**
     * The database
     *
     * @var mysqli
     */
    protected $db;

    /**
     * The configuration
     *
     * @var array
     */
    protected $configuration;

    /**
     * The entry to be written
     *
     * @var array
     */
    protected $entry;

    /**
     * @var resource
     */
    protected $fileHandler;


    /**
     * LewinlabsAbstractExporter constructor
     */
    public function __construct()
    {
        require_once '../../../../config.inc.php';
        $this->setExporterConfiguration();
        $this->initializeFile();
        $this->writeFileHeader();
    }

    /**
     *
     * Initializes the necessary configuration
     *
     * @throws Exception
     */
    protected function setExporterConfiguration()
    {
        // Set up DB connection
        $this->db = new mysqli($this->dbHost, $this->dbUser, $this->dbPwd, $this->dbName);
        if ($this->db->connect_errno) {
            throw new DatabaseConnectionException('Could not connect to database.', 2015485465);
        }
        if ($this->iUtfMode) {
            $this->db->set_charset('utf8');
        }
        // Check sShopURL from config.inc.php
        if (substr($this->sShopURL, -1) === '/') {
            $this->sShopURL = substr($this->sShopURL, 0, -1);
        }
    }

    /**
     * Initializes file handling
     */
    protected function initializeFile()
    {
        if (file_exists($this->configuration['filename'])) {
            unlink($this->configuration['filename']);
        }
        $this->fileHandler = fopen($this->configuration['filename'], "w+");
    }

    /**
     * Writes the header to the file if requested
     */
    protected function writeFileHeader()
    {
        if ($this->configuration['header']) {
            $data = $this->entry['header'];
            $replace = explode(';', $data);
            $newData = implode($this->entry['separator'], $replace);

            if (!$this->iUtfMode) {
                $newData = utf8_decode($newData);
            }
            fputs($this->fileHandler, $newData . "\n");
        }
    }


}