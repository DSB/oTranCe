<?php
class Application_Model_FileTree
{
    /**
     * Base path for the iteration.
     * @var string
     */
    private $_basePath;

    /**
     * Directory tree for use with jQuery plugin "jsTree".
     *
     * @var null
     */
    private $_jsTreeData = null;

    /**
     * Number of entries in tree.
     *
     * @var int
     */
    private $_entryCount = 0;

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
            $this->_entryCount = 0;
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
            $this->_entryCount++;
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
                $entryData['attr'] = array(
                    'href' => '#',
                    'onclick' => 'loadFileContent("' . htmlspecialchars($pathname) . '");',
                );
                $entryData['icon'] = 'file';
            }
            $entry['data'] = $entryData;
            $entries[] = $entry;
        }
        usort($entries, array($this, '_sortEntries'));
        return $entries;
    }

    /**
     * Returns the entry count from the directory tree.
     *
     * @return int
     */
    public function getEntryCount()
    {
        return $this->_entryCount;
    }

    /**
     * Sorts the directory entries by entry type and entry title.
     *
     * @param array $prevEntry Previous directory entry
     * @param array $nextEntry Next directory entry
     *
     * @return int
     */
    private function _sortEntries($prevEntry, $nextEntry)
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
