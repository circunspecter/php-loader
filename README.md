# GrindNous\Loader

A PSR-0 compliance autoloader. It also offers the possibility of requiring multiple files at once and do it individually "sending" and "receiving" variables from the required file.

## Use

    <?php
    require 'path/to/GrindNous/Loader.php';
    GrindNous\Loader::register_autoloader();
  
You can also specify paths for namespaces.
  
    <?php
    require 'path/to/GrindNous/Loader.php';
    GrindNous\Loader::register_autoloader(array(
      'MyNamespace' => '/path/to/namespace',
      'MyNamespace\Models' => '/diferent/path/to/models',
      'AnotherNamespace' => '/path/to/AnotherNamespace'
    ));
__

Visit "test" folder to view an example of working with different folders and namespaces.