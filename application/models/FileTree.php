<?php
/**
 * This file is part of oTranCe released under the GNU GPL 2 license
 * http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Models
 * @version         SVN: $
 * @author          $Author: $
 */

/**
 * File-Tree model
 *
 * @package         oTranCe
 * @subpackage      Models
 */
class Application_Model_FileTree
{
    /**
     * Base path for the iteration.
     *
     * @var string
     */
    private $_basePath;

    /**
     * Directory tree that contains the filenames (inclusive paths), file size and creation time.
     * Filenames are relative to the base dir.
     *
     * @var array
     */
    private $_simpleTree;

    /**
     * Number of entries in tree.
     *
     * @var int
     */
    private $_simpleEntryCount = 0;

    /**
     * Directory tree for use with jQuery plugin "jsTree".
     *
     * @var null
     */
    private $_jsTreeData = null;

    /**
     * Number of entries in jsTree tree.
     *
     * @var int
     */
    private $_jsTreeEntryCount = 0;

    /**
     * Class constructor.
     * Sets the base path for the iteration.
     *
     * @param string $basePath Base path for iteration.
     */
    public function __construct($basePath)
    {
        $this->_basePath = (string) $basePath;
    }

    /**
     * Retrieves a simple FS tree.
     *
     * @param bool $rebuild Force rebuild of the tree.
     *
     * @return array
     */
    public function getSimpleTree($rebuild = false)
    {
        if ($this->_simpleTree === null || $rebuild == true) {
            $this->_simpleEntryCount = 0;
            $this->_simpleTree = $this->_buildSimpleTree($this->_basePath);
        }

        return $this->_simpleTree;
    }

    /**
     * Builds a simple directory tree.
     *
     * @param string $baseDir Base path for iteration.
     * @param string $prefix  prefix for the entry (internally used).
     *
     * @return array
     */
    private function _buildSimpleTree($baseDir, $prefix = '')
    {
        $tree = array();
        $dir = new DirectoryIterator($baseDir);
        for (;$dir->valid();$dir->next()) {
            $filename = $dir->getFilename();
            if ($dir->isDot() || $filename == '.svn') {
                continue;
            }
            $this->_simpleEntryCount++;
            if ($dir->isDir()) {
                $tree = array_merge(
                    $tree,
                    $this->_buildSimpleTree(
                        $dir->getPathname(),
                        $prefix . '/' . $dir->getFilename()
                    )
                );
            } else {
                $tree[] = ltrim($prefix . '/' . $dir->getFilename(), '/');
            }
        }

        usort($tree, array($this, '_sortEntries'));
        return $tree;
    }

    /**
     * Returns an array, that includes the iterated directory tree.
     * If the tree isn't build, the function initiates the iteration.
     *
     * @param bool $rebuild Rebuild the tree
     *
     * @return null
     */
    public function getJsTreeData($rebuild = false)
    {
        if ($this->_jsTreeData === null || $rebuild) {
            $this->_jsTreeEntryCount = 0;
            $this->_jsTreeData = $this->_buildJsTreeData($this->_basePath);
        }

        return $this->_jsTreeData;
    }

    /**
     * Build the directory tree for use with jQuery plugin "jsTree".
     *
     * @param string $baseDir Base path for iteration.
     *
     * @return array
     */
    private function _buildJsTreeData($baseDir)
    {
        $entries = array();
        $dir = new DirectoryIterator($baseDir);
        for (;$dir->valid();$dir->next()) {
            $filename = $dir->getFilename();
            if ($dir->isDot() || $filename == '.svn') {
                continue;
            }
            $this->_jsTreeEntryCount++;
            $entry = array();
            $entryData['title'] = $dir->getFilename();
            if ($dir->isDir()) {
                $entryData['icon'] = 'folder';
                $entryData['attr'] = array(
                    'href' => '#',
                    'onclick' => "\$(this).parent().children('ins').click();",
                );
                $entry['children'] = $this->_buildJsTreeData($dir->getPathname());
            } else {
                $pathname = str_replace($this->_basePath, '', $dir->getPathname());
                $pathname = addslashes($pathname);
                $entryData['attr'] = array(
                    'href' => '#',
                    'onclick' => 'loadFileContent("' . htmlspecialchars($pathname, ENT_COMPAT, 'UTF-8') . '");',
                );
                $entryData['icon'] = 'file';
            }
            $entry['data'] = $entryData;
            $entries[] = $entry;
        }
        usort($entries, array($this, '_sortJsTreeEntries'));
        return $entries;
    }

    /**
     * Returns the entry count from the directory tree.
     *
     * @return int
     */
    public function getJsTreeEntryCount()
    {
        return $this->_jsTreeEntryCount;
    }

    /**
     * Returns the entry count from the directory tree.
     *
     * @return int
     */
    public function getSimpleEntryCount()
    {
        return $this->_simpleEntryCount;
    }

    /**
     * Sorts the directory entries by file name (directories first).
     *
     * @param array $prevEntry Previous directory entry
     * @param array $nextEntry Next directory entry
     *
     * @return int
     */
    private function _sortEntries($prevEntry, $nextEntry)
    {
        $prevSlashCount = substr_count($prevEntry, '/');
        $nextSlashCount = substr_count($nextEntry, '/');

        // Previous entry contains a directory and the next one not.
        if ($prevSlashCount > 0 && $nextSlashCount == 0) {
            return 1;
        }

        // The next entry contains a directory and the previous one not.
        if ($prevSlashCount == 0 && $nextSlashCount > 0) {
            return -1;
        }

        // Previous and next entry contains directories. The directory depth is significant.
        if ($prevSlashCount > $nextSlashCount) {
            return 1;
        }

        // Previous and next entry contains directories. The directory depth is significant.
        if ($prevSlashCount < $nextSlashCount) {
            return -1;
        }

        // The directory depth is equal. Now we compare the full entries.
        return strcmp($prevSlashCount, $nextSlashCount);
    }

    /**
     * Sorts the directory entries by entry type and title.
     *
     * @param array $prevEntry Previous directory entry
     * @param array $nextEntry Next directory entry
     *
     * @return int
     */
    private function _sortJsTreeEntries($prevEntry, $nextEntry)
    {
        if (
            (isset($prevEntry['children']) && isset($nextEntry['children'])) ||
            (!isset($prevEntry['children']) && !isset($nextEntry['children']))
        ) {
            return strcmp($prevEntry['data']['title'], $nextEntry['data']['title']);
        }
        return isset($prevEntry['children']) ? -1 : 1;
    }
}
