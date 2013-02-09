<?php
namespace Prosperia;

include("lib/Prosperia/bootstrap.php");
use Prosperia\Tokn\ToknData as ToknData;
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
function selfURL()
{
	/**
	 * This function generates the full URL of the current request.
	*/
	
	// Define whether HTTPS (secure HTTP) is on
	$s = empty($_SERVER["HTTPS"]) ? ''
		: ($_SERVER["HTTPS"] == "on") ? "s"
		: "";
	
	// Get the protocol itself
	$protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0, strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")).$s;
	
	// Get the port or use HTTP 80 by default
	$port = ($_SERVER["SERVER_PORT"] == "80") ? ""
		: (":".$_SERVER["SERVER_PORT"]);
	
	// Fetch a proper URL from the data and the current request
	return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
}

if ( isset($_FILES['images']) )
{
    echo "<span style=\"color: darkorange; font-weight: bold;\">Uploading " .count($_FILES['images']['name']).
        " files.</span><br />";
    for ($i = 0; $i < count($_FILES['images']['name']); $i++)
    {
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
        
        if (in_array($_FILES['images']['type'][$i], $allowed_types, true))
        {
            $chars = "012345abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $random = null;
            
            for ($j = 0; $j <= 8; $j++)
            {
                $random .= $chars[rand(0, strlen($chars) - 1)];
            }
            
            $token = new ToknData($random, str_shuffle(sha1(time())));
            
            $upload_content = file_get_contents($_FILES['images']['tmp_name'][$i]);
            
            $stor = new Stor(new StorFromData(
                $_FILES['images']['name'][$i],
                $_FILES['images']['type'][$i],
                $_FILES['images']['size'][$i],
                $upload_content
            ));
            
            $writer = new StorToFile($stor, "var/stor/" . $token->getReference());
            
            $token->write();
            $writer->write();
            
            $retrieve = str_replace(basename(__FILE__), "t/" . $token->getName(), selfURL());
            
            echo "<span style=\"color: darkgreen; font-weight: bold;\">Successfully uplodaded <i>" .
                $_FILES['images']['name'][$i]. "</i>.</span> Retrieve URL: <a href=\"$retrieve\" " .
                "target=\"_blank\">$retrieve</a>.<br />";
        }
        else
        {
            echo "<span style=\"color: red; font-weight: bold;\">Won't upload <i>" .$_FILES['images']['name'][$i].
                "</i>.</span> Type <i>" .$_FILES['images']['type'][$i]. "</i> is not allowed.<br />";
        }
    }
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