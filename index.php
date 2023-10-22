<?php
//load catalog
if (file_exists('catalog.xml')) {
    $records = array();
    foreach(simplexml_load_file('catalog.xml') as $node) {
        $records[] = $node;
    }
} else{
    exit('Unable to load catalog.xml');
}

// check if single item has been selected
if(isset($_GET['id'])) {
    //$records = $records$_GET['id']];
    //echo "<div>$records</div>"
    //var_dump($records);
    foreach($records as $record) {
        if($record->attributes() == $_GET['id']) {
            $records = $record;
            break;
        }
    }
    //var_dump(array_search($_GET['id'], $records));
}

//sort and filter list
else if(isset($_GET['submit'])) {
    // sort the array
    if($_GET['sort_order'] != "unsorted") {
        usort($records, function($a, $b) {
            switch ($_GET['sort_order']) {
                case "title":
                    return (strcmp(strtoupper($a->title), strtoupper($b->title))) * (isset($_GET['sort_direction']) ? -1 : 1);

                case "author":
                    return (strcmp(strtoupper($a->author), strtoupper($b->author))) * (isset($_GET['sort_direction']) ? -1 : 1);

                case "genre":
                    return (strcmp(strtoupper($a->genre), strtoupper($b->genre))) * (isset($_GET['sort_direction']) ? -1 : 1);

                case "published_year":
                    return ($a->published_year - $b->published_year) * (isset($_GET['sort_direction']) ? -1 : 1);

                case "price":
                    return ($a->price - $b->price) * (isset($_GET['sort_direction']) ? -1 : 1);
            }
        });
    }

    if(isset($_GET['search'])) {
        $search_query = explode(' ', strtoupper($_GET['search']));

        foreach($search_query as $item) {
            for($i = 0; $i < count($records); $i++) {
                if(!str_contains(strtoupper($records[$i]->title), $item)) {
                    unset($records[$i]);
                    $records = array_values($records); 
                    $i--;
                }
            }
        }
    }
    

}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- change title -->
    <title>Book Catalog</title>
    <!-- change page icon -->
    <link rel="icon" href="icons/icon.png">
    <!-- insert stylesheet -->
    <link rel="stylesheet" href="stylesheets/global.css">
    <!-- insert javascript -->
    <script src="javascript/global.js" defer></script>
</head>
<body>
    <!-- title -->
    <div class="card title">
        <h1>
            <a href='?'>Book Catalog</a>
        </h1>
    </div>

    <?php
    if(is_array($records)) {
        // using get method so that user can bookmark filter
        echo '
        <form class="card" style="height:auto;" action="?" method="get">
            <h2>Sorting & Searching</h2><br>
            <input type="text" name="search" placeholder="Search...">

            <select name="sort_order" id="sort_order">
                <option value="unsorted">Unsorted</option>
                <option value="title">Title</option>
                <option value="author">Author</option>
                <option value="genre">Genre</option>
                <option value="published_year">Published Year</option>
                <option value="price">Price</option>
            </select>

            <input type="checkbox" id="sort_direction" name="sort_direction" value="true">
            <label for="sort_direction">Descending</label>

            <input type="submit" name="submit">
        </form>';


        foreach($records as $record) {
            echo "
            <div class='grid-container card'>
                <div class='_header'>
                    <h2>
                        <a href='?id=".$record->attributes()."'>$record->title</a>
                    </h2>
                    <h3>- $record->author</h3>
                </div>

                <div class='_main'>
                    <div class='item'><b>ID:</b> ".$record->attributes()."</div>
                    <div class='item'><b>Genre:</b> $record->genre</div>
                    <div class='item'><b>Published Year:</b> $record->published_year</div>
                    <div class='item'><b>Price:</b> $$record->price</div>
                </div>

                <div class='_right'>
                    <a href='?id=".$record->attributes()."'>
                        <img src='images/$record->img'>
                    </a>
                
                </div>

            </div>";
        }
    }
    else{
        echo "
        <div class='grid-container card' style='max-width:800px;'>
            <div class='_header'>
                <h2>$records->title</h2>
                <h3>- $records->author</h3>
            </div>

            <div class='_main'>
                <div class='item'><b>ID:</b> ".$records->attributes()."</div>
                <div class='item'><b>Genre:</b> $records->genre</div>
                <div class='item'><b>Published Year:</b> $records->published_year</div>
                <div class='item'><b>Price:</b> $$records->price</div>
                <div class='item'><b>Description:</b>$records->description</div>
            </div>

            <div class='_right'><img src='images/$records->img'></div>

        </div>";
    }
    ?>


</body>