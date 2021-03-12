<!DOCTYPE html>
<?php
// this determines if the variable were declared once you submit
if(isset($_POST["submit"])) {     
    $file = $_FILES['fileToUpload'];
    $fileName = $_FILES['fileToUpload']['name'];
    $fileTmpName = $_FILES['fileToUpload']['tmp_name'];
    $fileError = $_FILES['fileToUpload']['error'];
    $fileType = $_FILES['fileToUpload']['type']; // Obtain the extension of the file/image
    $document_root = $_SERVER['DOCUMENT_ROOT'];

    // Error
    if($fileError > 0){ 
        echo 'Problem: '.$fileError;
        exit;
    } 
    
    // Checks if the file extension is correct
    if($fileType != 'image/jpeg' && $fileType != 'image/png' && $fileType != 'image/gif' && $fileType != 'image/jpg'){
        echo 'Problem: file is not a PNG, JPEG, GIF, or JPG: ';
        exit;
    } 
     $uploaded_file = 'uploads/'.$fileName;

     if(is_uploaded_file($fileTmpName)){
         if(!move_uploaded_file($fileTmpName,$uploaded_file)){
             echo 'Problem: Could not move file to destination directory';
             exit;
         }
    }
    else {
        echo 'Problem: Possible file upload attack. Filename: '. $fileName;
        exit;
    }
    ?>

    <?php
    //Save meta data and name of image file to gallery.txt
    $fp = fopen("gallery.txt", 'ab');
    
    if(!$fp){ // if fopen fails exit
        echo '<p><strong> Your order could not be processed. 
        .Please try again later.</strong></p></body></html>';
        exit;
    }
    
    // inputs trimed and uppercase
    $getPhotoName = strtoupper(trim($_POST['photoName']));  
    $getDateTaken = trim($_POST['dateTaken']); 
    $getPhotographer = strtoupper(trim($_POST['photographer']));
    $getLocation = strtoupper(trim($_POST['location']));

    $outputString = $fileName."\t".$getPhotoName."\t".$getDateTaken."\t".$getPhotographer."\t".$getLocation."\n";
	
	
    file_put_contents("gallery.txt", $outputString, FILE_APPEND); // append data
    //May be unncessary, but used rewind() to move the pointer to the start of the file
    rewind($fp);
    fclose($fp);
    ?>
	
<?php
    // Read file and add data to array and show pictures.
    $fp = fopen("gallery.txt", 'rb');

    if(!$fp){
        echo 'error reading file!';
        exit;
    }

    $galleryarray = [];  //empty array to store information from index.html

    while(!feof($fp)){
        $lines = fgets($fp); 
        if($lines === false) {
             break; // removes empty lines
        }
        $line = explode("\t",$lines); // explodes new line into the array, creating separate variables
        $temparray = [$line[0],$line[1],$line[2],$line[3],$line[4]]; // push -> array
        array_push($galleryarray,$temparray);
    }

    fclose($fp); // close file
}

?>

<html lang="en" dir="ltr">
<head>
<meta charset="utf-8">
    <title>The Gallery</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</head>
<body>
    <header>
        <h1 class="display-5">View All Photos</h1>   
    </header>
    <br>

<form action = "gallery.php" method="post" enctype="multipart/form-data">
    <table> 
        <tr> 
            <td> 
            <div class="form-group">
                <h2 class="display-5">Sort By:</h2>
                <select id="sortby" class="form-control" name="sort">
                    <option value="name">Name</option>
                    <option value="date">Date</option>
                    <option value="photographer">Photographer</option>
                    <option value="location">Location</option>
                </select>
                <!-- slight problem: images don't appear immediately until you press "OK" -->
                <button type="submit" name="ok">Ok</button>                 
            </div>
            <form action = "gallery.php" method = "post" enctype = "multipart/form-data">
                <td> 
                   <button type="submit" style="position:absolute; right:0" formaction="$document_root/../index.html"> Add Another Picture</button>
                </td>
            </form>
        </tr>
    </table>
</form>
    <div class="row">
        <?php
        $input='name';

if (isset($_POST["ok"])) {

//Here I have gallery.txt be read into $galleryarray as the form gets refreshed...
$fp = fopen("gallery.txt", 'rb');

    if(!$fp){
        echo 'Error: Unable to reading file';
    }
	
    $input = $_POST["sort"];
    $galleryarray = [];

    while(!feof($fp)){
        $lines = fgets($fp); 
        if($lines === false) {
             break; // removes empty lines
        }
        $line = explode("\t",$lines); // explodes new line into the array, creating separate variables
        $temparray = [$line[0],$line[1],$line[2],$line[3],$line[4]]; // push -> array
        array_push($galleryarray,$temparray);
    }
    fclose($fp); 
}

// Sort the array according to which "sort" method the user selected in the dropdown
if($input === 'name'){
    array_multisort( array_column( $galleryarray, 1),SORT_ASC,  $galleryarray);
} else if($input === 'date'){
    array_multisort( array_column( $galleryarray, 2),SORT_ASC, SORT_NUMERIC, $galleryarray);
} else if($input === 'photographer'){
    array_multisort( array_column( $galleryarray, 3),SORT_ASC, $galleryarray);
} else if($input === 'location'){
    array_multisort( array_column( $galleryarray, 4),SORT_ASC, $galleryarray);
}

// Using a for loop and echo data-boxes to display the images on screen in a card format, from bootstrap
$len = count($galleryarray); // 
for($row = 0; $row < $len; $row++) {
echo '<div class ="col-12 col-md-4 mb-5">';
    echo '<div class="card-deck">';
       echo '<div class="card" style="width: 18rem;">';
         echo '<div class="list-content">'; // coming from fileName
         echo '<div class="card-body">'; 
           echo'<img class="picture-content card-img-top" src="uploads/'.$galleryarray[$row][0].'"/ alt="Card img cap" style="width:100%;object-fit:cover;"></img>';
             echo'<p class="data-box card-text">Name: '.$galleryarray[$row][1].'</p>'; // name
             echo'<p class="data-box card-text">Date: '.$galleryarray[$row][2].'</p>'; // date
             echo'<p class="data-box card-text">Photographer: '.$galleryarray[$row][3].'</p>'; // photographer
             echo'<p class="data-box card-text">Location: '.$galleryarray[$row][4].'</p>'; // location
           echo'</div>';
         echo'</div>';
         echo'</div>';
       echo'</div>';
    echo'</div>';
}?>
</div>
</main>
</body>
</html>
