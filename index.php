<?php
require_once "lib/Prosperia/bootstrap.php";
require_once "inc/misc.php";

use Prosperia\Tokn\ToknData as ToknData;
use Prosperia\Stor as Stor;
use Prosperia\Stor\StorFromData as StorFromData;
use Prosperia\Stor\StorToFile as StorToFile;

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Prosperia</title>
    </head>
    <body>
<?php
if ( isset($_FILES['images']) )
{
    echo "\t\t<span style=\"color: darkorange; font-weight: bold;\">Uploading " .count($_FILES['images']['name']).
        " files</span>...<br />\n";
    
    echo "\t\t<table border=\"1\" style=\"width: 100%;\">\n";
    echo "\t\t\t<tr>\n";
    echo "\t\t\t\t<th>Filename</th>\n";
    echo "\t\t\t\t<th>Status</th>\n";
    echo "\t\t\t\t<th>Thumbnail</th>\n";
    echo "\t\t\t\t<th>Retrieve URL</th>\n";
    echo "\t\t\t\t<th>Delete URL</th>\n";
    echo "\t\t\t</tr>\n";
    
    for ($i = 0; $i < count($_FILES['images']['name']); $i++)
    {
        echo "\t\t\t<tr>\n";
        
        $allowed_types = array('image/jpeg',
            'image/png',
            'image/gif'
        );
        
        if (in_array($_FILES['images']['type'][$i], $allowed_types, true))
        {
            $token = new ToknData(generateRandomChars(8), str_shuffle(sha1(time())));
            
            $stor = new Stor(new StorFromData(
                $_FILES['images']['name'][$i],
                $_FILES['images']['type'][$i],
                $_FILES['images']['size'][$i],
                file_get_contents($_FILES['images']['tmp_name'][$i])
            ));
            
            $writer = new StorToFile($stor, "var/stor/" . $token->getReference());
            
            $token->write();
            $writer->write();
            
            $retrieve = str_replace(basename(__FILE__), "t/" . $token->getName(), selfURL());
            
            echo "\t\t\t\t<td>" .$_FILES['images']['name'][$i]. "</td>\n";
            echo "\t\t\t\t<td style=\"color: darkgreen; font-weight: bold;\">Successfully uploaded</td>\n";
            echo "\t\t\t\t<td>" . "thumbnail placeholder" . "</td>\n";
            echo "\t\t\t\t<td><a href=\"$retrieve\" target=\"_blank\">$retrieve</a></td>\n";
            echo "\t\t\t\t<td>" . "delete placeholder" . "</td>\n";
        }
        else
        {
            echo "\t\t\t\t<td>" .$_FILES['images']['name'][$i]. "</td>\n";
            echo "\t\t\t\t<td style=\"color: red; font-weight: bold;\">Won't upload</td>\n";
            echo "\t\t\t\t<td colspan=\"3\">" .
                "Type <i>" .$_FILES['images']['type'][$i]. "</i> is not allowed." . "</td>\n";
        }
        
        echo "\t\t\t</tr>\n";
    }
    echo "\t\t</table>\n";
    
    echo "\t\t<span style=\"color: darkgreen; font-weight: bold;\">Uploading finished.</span><br />\n";
}
else
{
?>
        <form method="POST" action="index.php" enctype="multipart/form-data">
            <label for="images[]">Images to upload: <small>(you can select multiple files)</small></label>
                <input type="file" name="images[]" multiple=""/>
            <input type="submit" value="Upload"/>
        </form>
<?php
}
?>
    </body>
</html>