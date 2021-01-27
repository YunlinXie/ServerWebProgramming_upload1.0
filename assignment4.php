<?php
/* *
 * author: Yunlin Xie
 * assignment 4:
 * A webpage with three functions: input text, upload file, print table
 * DB name: publications
 * table name: a4
 * */
#######################################################################################################
echo <<<_END
<html>
    <head>
        <title>File Upload</title>
    </head>
    <body>
        <form action = "?" method = "POST" enctype = "multipart/form-data">
            <label for="text">Name:</label>
            <input type = "text" name="name" id="name">
            <p>
            <label for="file">Content:</label>
            <input type="file" name="file"/></p>
            <p><input type="submit" name="submit" value="submit"></p>

        </form>
_END;

if ($_FILES) {
    // File properties
    $fileName = $_FILES['file']['name'];
    $fileTmpName = $_FILES['file']['tmp_name'];
    $fileSize = $_FILES['file']['size'];
    $fileError = $_FILES['file']['error'];
    // File extension
    $fileExt = explode('.', $fileName);
    $fileExt = strtolower(end($fileExt));
    // Allowed file type
    $allowed = array('txt');



    if (in_array($fileExt, $allowed)) {// Check file type
        if ($fileError === 0) {// Check file error
            if ($fileSize < 1000000) {// Check file size
                if (is_uploaded_file($fileTmpName)) {
                    $fileData = "";
                    $fp = fopen($fileTmpName, 'rb');
                    // Store file in a string without whitespaces and line breaks
                    while ( ($line = fgets($fp)) !== false) {
                        $line = preg_replace("/[ \t]+/", "", preg_replace("/\s*/m", "", $line));
                        $fileData = $fileData.$line;
                    }
                    ####################################################################################
                    require_once "login.php";
                    $conn = new mysqli("localhost","root","","publications");
                    if($conn->connect_error){
                        die("Connection is failed!!!");
                    }
                    $dbname = mysqli_real_escape_string($conn, $_POST['name']);
                    $dbname = sanitizeMySQL($conn, $dbname);
                    $content = $fileData;
                    $content = sanitizeMySQL($conn, $content);

                    echo "New inserted:<br>";
                    echo "name: ";
                    printf($dbname);
                    echo "<br>";
                    echo "content: ";
                    echo $content;
                    echo "<br><br>";

                    $insert_sql = "INSERT INTO a4 (Name, Content) VALUES ('".$dbname."', '".$content."')";
                    //$insert_sql = sanitizeMySQL($conn, $insert_sql);
                    $insert = mysqli_query($conn, $insert_sql);

                    $select_sql = "SELECT * FROM a4";
                    //$select_sql = sanitizeMySQL($conn, $select_sql);
                    $select = mysqli_query($conn, $select_sql);

                    echo '<table border="1"><tr><th>Name</th><th>Content</th>';
                        while($row=mysqli_fetch_assoc($select)){
                            echo '<tr><td>'.$row['name'].'</td><td>'.$row['content'].'</td>';
                        }
                    echo '</table>';
                    $conn->close();
                    ####################################################################################
                }
            } else {
                echo "Your file is too big!";
            }
        } else {
            echo "There was an error uploading your file!";
        }
    } else {
        echo "You cannot upload files of this type!";
    }

}
echo "</body></html>";

####################################################################################
function sanitizeString($var)
{
    $var = stripslashes($var);
    $var = strip_tags($var);
    $var = htmlentities($var);
    return $var;
}

function sanitizeMySQL($conn, $var)
{
    $var = $conn->real_escape_string($var);
    $var = sanitizeString($var);
    return $var;
}

?>


