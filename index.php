<?php
function fread_until_delimiter($handle, $delimiter)
{
    $buffer = null;
    $char = null;
    
    while ( $char != $delimiter )
    {
        $char = fread($handle, 1);
        $buffer .= $char;
    }
    
    return substr($buffer, 0, strlen($buffer) - 1);
}

if ( isset($_GET['token']))
{
    $storkey = null;
    if ( file_exists("var/tokn/" . $_GET['token']))
    {
        $storkey = file_get_contents("var/tokn/" . $_GET['token'], false, NULL, -1, 40);
    
        if ( file_exists("var/stor/" . $storkey) )
        {
            $handle = fopen("var/stor/" . $storkey, "rb");
            
            $name = fread_until_delimiter($handle, "\x01");
            $type = fread_until_delimiter($handle, "\x02");
            $size =  fread_until_delimiter($handle, "\x03");
            $hash  =  fread_until_delimiter($handle, "\x04");
            
            $content = fread($handle, (int)$size);
            
            fclose($handle);
            
            if ( sha1($content) == (string)$hash )
            {
                header("Content-Disposition: inline;");
                header("Content-Type: " .$type);
                header("Content-Length: " .$size);
                
                $location = 0;
                set_time_limit(0);
                while ( $location <= strlen($content) )
                {
                    print( substr($content, $location, 8192) );
                    ob_flush();
                    flush();
                    
                    $location += 8192;
                }
            }
        }
    }
    die();
}
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
                for ($i = 0; $i < count($_FILES['images']['name']); $i++)
                {
                    $image = array(
                        'name'  =>  $_FILES['images']['name'][$i],
                        'type'  =>  $_FILES['images']['type'][$i],
                        'tmp_name'  =>  $_FILES['images']['tmp_name'][$i],
                        'error'  =>  $_FILES['images']['error'][$i],
                        'size'  =>  $_FILES['images']['size'][$i]
                    );
                    
                    $extension = null;
                    switch($_FILES['images']['type'][$i])
                    {
                        case "image/jpeg":
                            $extension = "jpg";
                            break;
                        case "image/png":
                            $extension = "png";
                            break;
                        case "image/gif":
                            $extension = "gif";
                            break;
                        default:
                            echo "Invalid file type";
                            break;
                    }
                    
                    $chars = "012345abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
                    $token = null;
                    
                    for ($j = 0; $j <= 8; $j++)
                    {
                        $token .= $chars[rand(0, strlen($chars) - 1)];
                    }
                    
                    echo "Token: " .$token."<br>";
                    
                    $storkey = str_shuffle(sha1(time()));
                    
                    $handle = fopen("var/stor/" .$storkey, "w+b");
                    $upload_content = file_get_contents($_FILES['images']['tmp_name'][$i]);
                    
                    fwrite($handle, $_FILES['images']['name'][$i] . "\x01");
                    fwrite($handle, $_FILES['images']['type'][$i] . "\x02");
                    fwrite($handle, $_FILES['images']['size'][$i] . "\x03");
                    fwrite($handle, sha1($upload_content) . "\x04");
                    
                    fwrite($handle, $upload_content);
                         
                    fclose($handle);
                    
                    file_put_contents("var/tokn/" . $token, $storkey . "\n");
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

<?php
// DUMP
var_dump($_POST);
echo "<hr>";
var_dump($_FILES);
echo "<hr>";
?>