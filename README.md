# CPSC 431 - Assignment-2

## Team members:
- Sagar Joshi | CWID: 805532256
- Emiliano Arranaga | CWID: 888006756

## Project URL(s):
- http://ecs.fullerton.edu/~cs431s41/Assignment2/

## Contribution's:
- Emiliano - edited the sorting method, edited gallery layout, debugging 
- Sagar - connecting the upload information into the database, created functions to sort the layout of phpmyadmin

## gallery.php code
```
<!DOCTYPE html>
<?php
// slightly changed from Assignment 1, but pretty similar
$tmp_name = "temp"; 
$name = $_POST['photoName'];
$date = $_POST['dateTaken'];
$photographer = $_POST['photographer'];
$location = $_POST['location'];
$input = $_GET['choice']; 
$filename = $_FILES["fileToUpload"]["name"];
$imageFileType = $_FILES["fileToUpload"]["type"];
$errorImageUpload = False;

// connecting to the database, if statement just states what happens if unable to
@$db = new mysqli('mariadb', 'cs431s41', 'EeChe9sh', 'cs431s41');
if (mysqli_connect_errno()) {
    echo "<p>Error: Cannot connect to database!</p>";
    exit;
 }
	
// This moves the information to the database, collecting the uploads information
if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],"uploads/".$filename)) {
        $query = "INSERT INTO Images VALUES (?, ?, ?, ?, ?)";
        // prepare and bind
        $statement = $db->prepare($query);
        $statement->bind_param('sssss', $filename, $name, $date, $photographer, $location);
        $statement->execute();

        if ($statement->affected_rows > 0) {
            echo  "<p>Image successfully included to the database.</p>";
        } else {
            echo "<p>An error has occurred. Image failed to upload into the database. Please try again</p>";
        }

        $db->close(); 
}

    //Array positions (in order as it is in phpMyAdmin): 0 - Filename. 1 - Name. 2 - Date. 3 - Photographer. 4 - Location.
    function nameData($x, $y) {
        if (strtolower($x[1]) == strtolower($y[1])) {
            return 0;
        } else if (strtolower($x[1]) < strtolower($y[1])) {
            return -1;
        } else {
            return 1;
        }
    }
    function dateData($x, $y) {
        if (strtolower($x[2]) == strtolower($y[2])) {
            return 0;
        } else if (strtolower($x[2]) < strtolower($y[2])) {
            return -1;
        } else {
            return 1;
        }
    }
    function photographerData($x, $y) {
        if (strtolower($x[3]) == strtolower($y[3])) {
            return 0;
        } else if (strtolower($x[3]) < strtolower($y[3])) {
            return -1;
        } else {
            return 1;
        }
    }
    function locationData($x, $y) {
        if (strtolower($x[4]) == strtolower($y[4])) {
            return 0;
        } else if (strtolower($x[4]) < strtolower($y[4])) {
            return -1;
        } else {
            return 1;
        }
    }
?>

<html>
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

<form action = "gallery.php" method="GET" enctype="multipart/form-data">
    <table> 
        <tr> 
            <td> 
            <div class="form-group">
                <h2 class="display-5">Sort By:</h2>
                <select id="sortby" class="form-control" name="choice">
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

// connecting to the database, if statement just states what happens if unable to
@$db = new mysqli('mariadb', 'cs431s41', 'EeChe9sh', 'cs431s41');
        if (mysqli_connect_errno()) {
            echo "<p>Error: Cannot connect to database!</p>";
            exit;
}

// sorting method
$query = "SELECT Filename, Name, Date, Photographer, Location FROM Images";
if ($input == "name") {
                $query = "SELECT Filename, Name, Date, Photographer, Location FROM Images ORDER BY Name";
}
else if ($input == "date") {
                $query = "SELECT Filename, Name, Date, Photographer, Location FROM Images ORDER BY Date";
}
else if ($input == "photographer") {               
                $query = "SELECT Filename, Name, Date, Photographer, Location FROM Images ORDER BY Photographer";
}
else if ($input == "location") {
                $query = "SELECT Filename, Name, Date, Photographer, Location FROM Images ORDER BY Location";
}

        $statement = $db->prepare($query);
        $statement->execute();
        $statement->store_result();

        $statement->bind_result($filename, $name, $date, $photographer, $location);
        
        // takes information from the database and pastes in on the webpage
        while($statement->fetch()) {
             echo '<div class ="col-12 col-md-4 mb-5">';
             echo '<div class="card-deck">';
             echo '<div class="card" style="width: 18rem;">';
             echo '<div class="list-content">'; 
             echo '<div class="card-body">'; 
             echo'<img class="picture-content card-img-top" src="uploads/'.$filename .'"/ alt="Card img cap" style="width:100%;object-fit:cover;"></img>';
             echo'<p class="data-box card-text">Name: '.$name.'</p>'; // name
             echo'<p class="data-box card-text">Date: '.$date.'</p>'; // date
             echo'<p class="data-box card-text">Photographer: '.$photographer.'</p>'; // photographer
             echo'<p class="data-box card-text">Location: '.$location.'</p>'; // location
             echo'</div>';
             echo'</div>';
             echo'</div>';
             echo'</div>';
             echo'</div>';
        }
        $statement->free_result();
        $db->close();
    ?>
</div>
</body>
</html>
```
