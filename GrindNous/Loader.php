<?php
/**
 * Copyright (c) 2012 https://github.com/circunspecter
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace GrindNous;

class Loader
{
    /**
     * Namespace : ClassPath pairs.
     *
     * @var array
     */
    protected static $_pairs = array();

    /**
     * Register an autoload function with possibility of assign
     * different paths to specific namespaces.
     *
     *      \GrindNous\Loader::register_autoloader(array(
     *          'MyNamespace' => '/overwrite/path/to/namespace',
     *          'MyNamespace\Models' => '/diferent/path/to/models',
     *      );
     *
     * @param   array   Namespace : Path pairs.
     */
    public static function register_autoloader(array $pairs = array())
    {
        // Merge base pair with user pairs
        self::$_pairs = array_merge(array(
            __NAMESPACE__ => __DIR__
        ), $pairs);

        // Remove trailing slash from paths
        array_walk(self::$_pairs, function (&$path) {
            $path = rtrim($path, DIRECTORY_SEPARATOR);
        });

        // Register autload function
        spl_autoload_register(__NAMESPACE__ . "\\Loader::autoload");
    }

    /**
     * Autoload function.
     */
    public static function autoload($class_name)
    {
        if(strstr($class_name, '\\') === FALSE) $class_name = str_replace('_', '\\', $class_name);

        $class_name = ltrim($class_name, '\\');

        $class_pieces = explode("\\", $class_name);
        $class_name = array_pop($class_pieces);
        $class_namespace = implode("\\", $class_pieces);

        // Search path to use
        $match = FALSE;
        $match_coincidences = 0;
        foreach(self::$_pairs as $namespace => $path)
        {
            $class_namespace_pieces = explode("\\", $class_namespace);
            $pair_namespace_pieces = explode("\\", $namespace);

            if(count($class_namespace_pieces) < count($pair_namespace_pieces)) continue;

            $coincidences = array_intersect($class_namespace_pieces, $pair_namespace_pieces);
            $coincidences_count = count($coincidences);

            if($coincidences_count > 0)
            {
                $correct = true;
                $coincidences_values = array_values($coincidences);
                array_walk($coincidences_values, function ($value, $index) use ($class_namespace_pieces, &$correct) {
                    $correct = ($correct === true AND $class_namespace_pieces[$index] === $value);
                });
                if($correct === true AND $coincidences_count > $match_coincidences)
                {
                    $match = $namespace;
                    $match_coincidences = $coincidences_count;
                }
            }
        }

        // If path matched
        if($match !== FALSE)
        {
            // Remove namespace from the matched pair on the requested class namespace
            $class_namespace = preg_replace('/^'.preg_quote($match, '/').'\\\?/', '', $class_namespace, 1);

            // Convert backslash to slash
            $class_namespace = str_replace('\\', DIRECTORY_SEPARATOR, $class_namespace);

            // Build class path
            $file_name = self::$_pairs[$match].DIRECTORY_SEPARATOR;
            if( !empty($class_namespace))
            {
                $file_name .= $class_namespace.DIRECTORY_SEPARATOR;
            }
            $file_name .= $class_name.'.php';

            // Require file
            if (file_exists($file_name))
            {
                require $file_name;
            }
        }
    }

    /**
     * Load all files located at specific folders.
     *
     * @param   array   Folders paths to load.
     */
    public static function folders(array $folders = array())
    {
        foreach($folders as $folder)
        {
            $c_folder = rtrim($folder, DIRECTORY_SEPARATOR);
            if(is_dir($c_folder))
            {
                if( ($files = glob($folder."/*.php")) !== false)
                {
                    self::files($files);                    
                }
            }
        }
    }

    /**
     * Load all given files.
     *
     * @param   array   Files paths to load.
     */
    public static function files(array $files = array())
    {
        foreach($files as $file) self::file($file);
    }

    /**
     * Load given file.
     *
     * @param   array   File to load.
     * @param   mixed   Return value if file not exists.
     * @param   array   Associative array with variables to use in required file.
     * @param   array   Indexed array with variables to return from required file.
     */
    public static function file($file, $default = NULL, array $args = array(), array $return_vars = array())
    {
        // Verify that the file exists 
        if( ! is_file($file)) return $default;

        // Import arguments ($args) into the current symbol table
        if( ! empty($args)) extract($args);
        
        // Require file
        $ret = require $file;

        // Loop through desired variables to capture from file
        if( ! empty($return_vars))
        {
            $ret = ($ret !== 1) ? array('__returned' => $ret) : array() ;
            foreach($return_vars as $var)
            {
                $ret[$var] = (isset($$var)) ? $$var : NULL ;
            }
        }

        return $ret; 
    }
}