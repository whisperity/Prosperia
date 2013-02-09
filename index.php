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
// DUMP
var_dump($_POST);
echo "<hr>";
var_dump($_FILES);
?>
        
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
                for ($i = 0; $i < count($_FILES['images']['name']); $i++)
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
                    
                    echo "File uploaded successfully. Token is: <strong>" . $token->getName() . "</strong>.<br>";
                    
                    $retrieve = str_replace(basename(__FILE__), "t/" . $token->getName(), selfURL());
                    echo "Retrieve as: <a href=\"$retrieve\" target=\"_blank\">$retrieve</a>";
                    
                    exit;
                }
            }
        ?>
        <form method="POST" action="index.php" enctype="multipart/form-data">
            <label for="images[]">Images to upload:</label>
                <input type="file" name="images[]" multiple=""/>
            <input type="submit" value="Upload"/>
        </form>
    </body>
</html>