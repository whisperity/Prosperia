<?php

namespace Prosperia;

include("lib/Prosperia/bootstrap.php");

use Prosperia\Tokn\ToknFile as ToknFile;
use Prosperia\Stor\StorFromFile as StorFromFile;

define('PRINT_CHUNK_SIZE', 8192);

if (!isset($_REQUEST['token']) || empty($_REQUEST['token']))
{
    header("HTTP/1.1 400 Bad Request");
    exit;
}

if (file_exists("var/tokn/".$_REQUEST['token']))
{
    $token = new ToknFile($_REQUEST['token']);
    
    if (file_exists("var/stor/".$token->getReference()))
    {
        $stor = new Stor(new StorFromFile("var/stor/".$token->getReference()));
        
        header("Content-Disposition: inline;");
        header("Content-Type: " . $stor->getType());
        header("Content-Length: " . $stor->getSize());
        
        $location = 0;
        set_time_limit(0);
        while ($location <= $stor->getSize())
        {
            print(substr($stor->getContent(), $location, PRINT_CHUNK_SIZE));
            ob_flush();
            flush();
            
            $location += PRINT_CHUNK_SIZE;
        }
    }
    else
    {
        header("HTTP/1.1 410 Gone");
        exit;
    }
}
else
{
    header("HTTP/1.1 404 Not Found");
    exit;
}
?>