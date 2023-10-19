<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		admingallery.php
Description:	This is the parent page
Developer: 		Ynet Interactive 
Date: 			5/11/2015
*/
global $server;

function countPhotos($album) {
		global $server;
		$sql=$query= "SELECT * FROM gallery WHERE album_id = '".mysqli_real_escape_string($server, $album)."'";
			$result = mysqli_query($server, $query);
		return	$num = mysqli_num_rows($result);
}
function albumName($album){
		global $server;
		$sql=$query="SELECT * FROM album WHERE id = '".mysqli_real_escape_string($server, $album)."'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		return $row['title'];
}
$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());


if(isset($_GET['keyword']))
{
$category = filterinp($_GET['category']);
	$added = '';
	if(isset($_GET['album']))
	{
	$album = mysqli_real_escape_string($server, $_GET['album']);
	$added = " AND (album_id = '".$album."')";
	}

	$school_id = $school_id;

	$searchword = filterinp($_GET['keyword']);
	$forword = filterinp($_GET['keyword']);
	$term = mysqli_real_escape_string($server, $_GET['keyword']);
	$ser = explode(' ', mysqli_real_escape_string($server, $_GET['keyword']));
	$clauses = array();
	$clauses2 = array();
	$clauses3 = array();
	foreach($ser as $look)
	{
	    $term = trim(preg_replace('/[^a-z0-9]/i', '', $look));
	    if (!empty($term))
	    {
	         $clauses[] = "p.title LIKE '%$term%' OR p.url LIKE '%$term%'".$added;
	    }
	    else
	    {
	         $clauses[] = "p.title LIKE '%%'".$added;
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query= "select * FROM gallery p WHERE p.school_id = '$school_id' AND $filter ";

 	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No photos found! Please try another search term.";
		$class="blue";
		}
} else {
	//display albums
	$sql=$query = "SELECT * FROM album WHERE school_id = '$school_id' ORDER BY title ASC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "There are currently no albums in your school";
		$class="blue";
		}
}

if(isset($_REQUEST['album']) && $_REQUEST['album'] > 0) {
	//display photos
	$album = mysqli_real_escape_string($server, $_GET['album']);
	$sql=$query = "SELECT * FROM gallery WHERE album_id = '$album' ORDER BY id DESC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "There are currently no photos in the selected album";
		$class="blue";
		}
}
?>

<div class="wrapper">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
	<div id="search-pan">
    	<form action="" method="get">
        <input type="search" name="keyword" placeholder="Search Photos" style="" />
        <button class="submit"><i class="fa fa-search"></i></button>
        </form>
    </div>
 <?php if($album > 0) { ?>
    <h3>Showing Photos in <?php echo albumName($album) ?> &nbsp;&nbsp;<small style="float:right;"><a class="btn btn-default" href="gallery">[Back to Albums]</a></small></h3>
 <?php } ?>
	<div class="panel row" style="border-color: transparent;">
        <div class="panel-body">
<script>$("a.fancybox").fancybox();</script>
               <?php
if($album > 0) {
	//display photos
				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$title = $row['title'];
					$image = $row['url'];
					$start_mark = $row['album_id'];

				?>
              <div class="col-sm-12 col-md-4 photos">
                <a class="fancybox" rel="gallery" href="<?php echo 'media/uploads/'.$image;?>"><img title="<?php echo $title; ?>" src="<?php echo 'media/uploads/'.$image;?>" /></a>
                <a class="fancybox" rel="gallery" href="<?php echo 'media/uploads/'.$image;?>"><button>View Photo</button></a>
              </div>
              <?php
					$i++;
				}
} else {
//display albums
			$i=0;
			while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$title = $row['title'];

				?>
              <div class="col-sm-12 col-md-4 photos">
                <a href="gallery?album=<?php echo $id;?>"><album><i class="fa fa-photo"></i><br /><?php echo $title.'<br>('.countPhotos($id).' Photos)'; ?></album></a>
                <a href="gallery?album=<?php echo $id;?>"><button>View Photos</button></a>
              </div>
              <?php
					$i++;
			}

}
		  ?>

<?php displayPagination($setLimit,$page,$query) ?>

        </div>
    </div>
</div>
