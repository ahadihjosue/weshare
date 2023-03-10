<?php

$con = mysqli_connect("localhost","root","","weshare") or die("Connection was not established");

//function for inserting a post

function insertPost(){
	if(isset($_POST['sub'])){
		global $con;
		global $user_id;

		$content = htmlentities($_POST['content']);
		$upload_image = $_FILES['upload_image']['name'];
		$image_tmp = $_FILES['upload_image']['tmp_name'];
		
		if(strlen($content) > 250){
			echo "<script>alert('Maximum Limit reached! Try using not more than 250 characters.')</script>";
			echo "<script>window.open('home.php', '_self')</script>";
		}else{

			// if the user uploads an image and types something
			if(strlen($upload_image) >= 1 && strlen($content) >= 1){
				move_uploaded_file($image_tmp, "uploads/images/$upload_image");
				$insert = "insert into posts (user_id, post_content, upload_image, post_date) values('$user_id', '$content', '$upload_image', NOW())";

				$run = mysqli_query($con, $insert);

				// If the post is updated, refresh and return to the current page
				if($run){
					echo "<script>window.open('home.php', '_self')</script>";

					$update = "update users set posts='yes' where user_id='$user_id'";
					$run_update = mysqli_query($con, $update);
				}

				exit();
			}else{
				if($upload_image=='' && $content == ''){
					
				}else{
					// if the user uploads only the image
					if($content==''){
						move_uploaded_file($image_tmp, "uploads/images/$upload_image");
						$insert = "insert into posts (user_id,post_content,upload_image,post_date) values ('$user_id','No','$upload_image',NOW())";
						$run = mysqli_query($con, $insert);

						// If the post is updated, refresh and return to the current page
						if($run){
							echo "<script>window.open('home.php', '_self')</script>";

							$update = "update users set posts='yes' where user_id='$user_id'";
							$run_update = mysqli_query($con, $update);
						}

						exit();

					// if the user uploads only the content without image
					}else{
						$insert = "insert into posts (user_id, post_content, post_date) values('$user_id', '$content', NOW())";
						$run = mysqli_query($con, $insert);

						if($run){
							// If the post is updated, refresh and return to the current page
							echo "<script>window.open('home.php', '_self')</script>";

							$update = "update users set posts='yes' where user_id='$user_id'";
							$run_update = mysqli_query($con, $update);
						}
					}
				}
			}
		}
	}
}

function get_posts(){
	global $con;

	$per_page = 100;

	if(isset($_GET['page'])){
		$page = $_GET['page'];
	}else{
		$page=1;
	}

	$start_from = ($page-1) * $per_page;

	// From the latest posts
	$get_posts = "select * from posts ORDER by 1 DESC LIMIT $start_from, $per_page";

	$run_posts = mysqli_query($con, $get_posts);

	while($row_posts = mysqli_fetch_array($run_posts)){

		$post_id = $row_posts['post_id'];
		$user_id = $row_posts['user_id'];

		// We will only show a maximum of 200 words obn each post
		$content = substr($row_posts['post_content'], 0,200);

		$upload_image = $row_posts['upload_image'];
		$post_date = $row_posts['post_date'];

		$user = "select *from users where user_id='$user_id' AND posts='yes'";
		$run_user = mysqli_query($con,$user);
		$row_user = mysqli_fetch_array($run_user);

		$f_name = $row_user['f_name'];
		$l_name = $row_user['l_name'];
		$user_image = $row_user['user_image'];

		/* now displaying posts from database */

		// if the user uploads an image without text
		if($content=="No" && strlen($upload_image) >= 1){
			echo"

				<div id='posts' class='col-sm-6'>

					<div class='row'>
						<div class='col-sm-2'>
							<img src='users/$user_image' class='img-circle'></<img>
						</div>
						
						<div id='postedby' class='col-sm-6'>
							<p><a href='user_profile.php?u_id=$user_id'>$f_name $l_name</a></p>
							<p><i class='fa fa-clock-o'></i> $post_date</p>
						</div>
					</div>

					<div class='row'>
						<div class='col-sm-12'>
							<img id='posts-img' src='uploads/images/$upload_image'>
						</div>
					</div>
					
					<div class='post-footer'>
						<a href='#' class='text-dark'><i class='fa fa-heart-o fa-lg'></i> Like</a>
						<a href='#' class='text-dark'><i class='fa fa-link fa-lg'></i> Share</a>
						<a href='single.php?post_id=$post_id' class='text-dark'><i class='fa fa-comment-o fa-lg'></i> Comment</a>
					</div>

				</div><br><br>
			";
		}

		else if(strlen($content) >= 1 && strlen($upload_image) >= 1){
			echo"

				<div id='posts' class='col-sm-6'>
					<div class='row'>
						<div class='col-sm-2'>
							<img src='users/$user_image' class='img-circle'></img>
						</div>

						<div id='postedby' class='col-sm-6'>
							<p><a href='user_profile.php?u_id=$user_id'>$f_name $l_name</a></p>
							<p><i class='fa fa-clock-o'></i> $post_date</p>
						</div>
					</div>

					<div class='row'>
						<div class='col-sm-12'>
							<p class='content'>$content</p>
							<img id='posts-img' src='uploads/images/$upload_image'>
						</div>
					</div>

					<div class='post-footer'>
						<a href='#' class='text-dark'><i class='fa fa-heart-o fa-lg'></i> Like</a>
						<a href='#' class='text-dark'><i class='fa fa-link fa-lg'></i> Share</a>
						<a href='single.php?post_id=$post_id' class='text-dark'><i class='fa fa-comment-o fa-lg'></i> Comment</a>
					</div>

				</div><br><br>
			";
		}

		else{
			echo"
			
				<div id='posts' class='col-sm-6'>
					<div class='row'>
						<div class='col-sm-2'>
							<img src='users/$user_image' class='img-circle'></img>
						</div>

						<div id='postedby' class='col-sm-6'>
							<p><a href='user_profile.php?u_id=$user_id'>$f_name $l_name</a></p>
							<p><i class='fa fa-clock-o'></i> $post_date</small></p>
						</div>
					</div>

					<div class='row'>
						<div class='col-sm-12'>
							<h4><p>$content</p></h4>
						</div>
					</div>

					<div class='post-footer'>
						<a href='#' class='text-dark'><i class='fa fa-heart-o fa-lg'></i> Like</a>
						<a href='#' class='text-dark'><i class='fa fa-link fa-lg'></i> Share</a>
						<a href='single.php?post_id=$post_id' class='text-dark'><i class='fa fa-comment-o fa-lg'></i> Comment</a>
					</div>

				</div><br><br>
			";
		}
	}

	// include("pagination.php");
}

function single_post()
{
	if (isset($_GET['post_id'])) {
		global $con;

		$get_id = $_GET['post_id'];

		$get_posts = "select * from posts where post_id='$get_id'";

		$run_posts = mysqli_query($con, $get_posts);

        $row_posts = mysqli_fetch_array($run_posts);

        $post_id = $row_posts['post_id'];
        $user_id = $row_posts['user_id'];
        $content = $row_posts['post_content'];
        $upload_image = $row_posts['upload_image'];
        $post_date = $row_posts['post_date'];

        $user = "select * from users where user_id='$user_id' AND posts='yes'";

        $run_user = mysqli_query($con, $user);
        $row_user = mysqli_fetch_array($run_user);

        $f_name = $row_user['f_name'];
		$l_name = $row_user['l_name'];
        $user_image = $row_user['user_image'];

        // Comments
        $user_com = $_SESSION['user_email'];
        $get_com = "select * from users where user_email='$user_com'";

        $run_com = mysqli_query($con, $get_com);
        $row_com = mysqli_fetch_array($run_com);

        $user_com_id = $row_com['user_id'];
        $user_com_f_name = $row_com['f_name'];
        $user_com_l_name = $row_com['l_name'];

        if (isset($_GET['post_id'])) {
        	$post_id = $_GET['post_id'];
        }

        $get_posts = "select * from users where post_id='$post_id'";
        $run_user = mysqli_query($con, $get_posts);

        $post_id = $_GET['post_id'];

        $post = $_GET['post_id'];

        $get_user = "select * from posts where post_id='$post'";
        $run_user = mysqli_query($con, $get_user);
        $row = mysqli_fetch_array($run_user);

        $p_id = $row['post_id'];

        if ($p_id != $post_id) {
        	echo "<script>alert('ERROR OCCURED!')</script>";
        	echo "<script>window.open('home.php', '_self')</script>";

        }else {

        	if($content=="No" && strlen($upload_image) >= 1){
				echo"
	
					<div id='posts' class='col-sm-6'>
	
						<div class='row'>
							<div class='col-sm-2'>
								<img src='users/$user_image' class='img-circle'></<img>
							</div>
							
							<div id='postedby' class='col-sm-6'>
								<p><a href='user_profile.php?u_id=$user_id'>$f_name $l_name</a></p>
								<p><i class='fa fa-clock-o'></i> $post_date</p>
							</div>
						</div>
	
						<div class='row'>
							<div class='col-sm-12'>
								<img id='posts-img' src='uploads/images/$upload_image'>
							</div>
						</div>
						
						<div class='post-footer'>
							<a href='#' class='text-dark'><i class='fa fa-heart-o fa-lg'></i> Like</a>
							<a href='#' class='text-dark'><i class='fa fa-link fa-lg'></i> Share</a>
							<a href='single.php?post_id=$post_id' class='text-dark'><i class='fa fa-comment-o fa-lg'></i> Comment</a>
						</div>
	
					</div><br><br>
				";
			}
	
			else if(strlen($content) >= 1 && strlen($upload_image) >= 1){
				echo"
	
					<div id='posts' class='col-sm-6'>
						<div class='row'>
							<div class='col-sm-2'>
								<img src='users/$user_image' class='img-circle'></img>
							</div>
	
							<div id='postedby' class='col-sm-6'>
								<p><a href='user_profile.php?u_id=$user_id'>$f_name $l_name</a></p>
								<p><i class='fa fa-clock-o'></i> $post_date</p>
							</div>
						</div>
	
						<div class='row'>
							<div class='col-sm-12'>
								<p class='content'>$content</p>
								<img id='posts-img' src='uploads/images/$upload_image'>
							</div>
						</div>
	
						<div class='post-footer'>
							<a href='#' class='text-dark'><i class='fa fa-heart-o fa-lg'></i> Like</a>
							<a href='#' class='text-dark'><i class='fa fa-link fa-lg'></i> Share</a>
							<a href='single.php?post_id=$post_id' class='text-dark'><i class='fa fa-comment-o fa-lg'></i> Comment</a>
						</div>
	
					</div><br><br>
				";
			}
	
			else{
				echo"
				
					<div id='posts' class='col-sm-6'>
						<div class='row'>
							<div class='col-sm-2'>
								<img src='users/$user_image' class='img-circle'></img>
							</div>
	
							<div id='postedby' class='col-sm-6'>
								<p><a href='user_profile.php?u_id=$user_id'>$f_name $l_name</a></p>
								<p><i class='fa fa-clock-o'></i> $post_date</small></p>
							</div>
						</div>
	
						<div class='row'>
							<div class='col-sm-12'>
								<h4><p>$content</p></h4>
							</div>
						</div>
	
						<div class='post-footer'>
							<a href='#' class='text-dark'><i class='fa fa-heart-o fa-lg'></i> Like</a>
							<a href='#' class='text-dark'><i class='fa fa-link fa-lg'></i> Share</a>
							<a href='single.php?post_id=$post_id' class='text-dark'><i class='fa fa-comment-o fa-lg'></i> Comment</a>
						</div>
	
					</div><br><br>
				";
			}
			// else condition ending

			include("comments.php");

			echo "
            	<div class='row'>
            		<div class='col-sm-6 col-md-offset-3'>
            			<div class='panel panel-info'>
            				<div class='panel-body'>
            					<form action='' method='post' class='form-inline'>
            						<textarea placeholder='Write your comment here!' class='pb-cmnt-textarea' name='comment'></textarea>
            						<button class='btn btn-info pull-right' name='reply'>Comment</button>
            					</form>
            				</div>
            			</div>
            		</div>
            	</div>
			";	

			if(isset($_POST['reply'])){

				// The htmlentinties will stop some kind of php or html code to execute in our database
				$comment = htmlentities($_POST['comment']);

				if ($comment == "") {
					echo "<script>alert('Enter your comment!')</script>";
					echo "<script>window.open('single.php?post_id=$post_id', '_self')</script>";
				}else{
					$insert = "insert into comments (post_id,user_id,comment,author_f_name,author_l_name,date) values ('$post_id','$user_id','$comment','$user_com_f_name','$user_com_l_name',NOW())";

					$run = mysqli_query($con, $insert);
					// If the comment is added, refresh and return to the current page
					echo "<script>window.open('single.php?post_id=$post_id', '_self')</script>";
				}

			}
        
        }

	}

}


function user_posts() {
	global $con;

	if (isset($_GET['u_id'])) {
		$u_id = $_GET['u_id'];
	}

	$get_posts = "select * from posts where user_id='$u_id' ORDER by 1 DESC LIMIT 5";

	$run_posts = mysqli_query($con, $get_posts);

	while ($row_posts = mysqli_fetch_array($run_posts)) {
	    
	    $post_id = $row_posts['post_id'];
	    $user_id = $row_posts['user_id'];
	    $content = $row_posts['post_content'];
	    $upload_image = $row_posts['upload_image'];
	    $post_date = $row_posts['post_date'];

	    $user = "select * from users where user_id='$user_id' AND posts='yes'";
	    $run_user = mysqli_query($con, $user);
	    $row_user = mysqli_fetch_array($run_user);

	    $user_name = $row_user['user_name'];
	    $f_name = $row_user['f_name'];
	    $l_name = $row_user['l_name'];
	    $user_image = $row_user['user_image'];

	    if (isset($_GET['u_id'])) {
		$u_id = $_GET['u_id'];

		}

		$getuser = "select user_email from users where user_id='$u_id'";
		$run_user = mysqli_query($con, $getuser);
		$row = mysqli_fetch_array($run_user);

		$user_email = $row['user_email'];
		$user = $_SESSION['user_email'];
		$get_user = "select * from users where user_email='$user'";
		$run_user = mysqli_query($con, $get_user);
		$row = mysqli_fetch_array($run_user);

		$user_id = $row['user_id'];
		$u_email = $row['user_email'];

		if ($u_email != $user_email) {
			echo "<script>window.open('my_posts.php?u_id=$user_id', '_self')</script>";
		}
		else {
			if($content=="No" && strlen($upload_image) >= 1) {
				echo"
				<div class='row'>
					<div class='col-sm-3'>
					</div>

					<div id='posts' class='col-sm-6'>

						<div class='row'>
							<div class='col-sm-2'>
								<p><img src='users/$user_image' class='img-circle' width='100px' height='100px'></p>
							</div>
							
							<div id='postedby' class='col-sm-6'>
								<h4>
									<a style='text-decoration:none; cursor:pointer;color #3897f0;' href='user_profile.php?u_id=$user_id'>$f_name $l_name</a>
								</h4>
								<h4><small style='color:black;'>Updated a post on <strong>$post_date</strong></small></h4>
							</div>
							
							<div class='col-sm-4'>
							</div>
						</div>

						<div class='row'>
							<div class='col-sm-12'>
								<img id='posts-img' src='uploads/images/$upload_image' style='height:350px;'>
							</div>
						</div>
					</div>

					<div class='col-sm-3'>
					</div>
				</div><br><br>
				";
			}

			else if(strlen($content) >= 1 && strlen($upload_image) >= 1){
				echo"
				<div class='row'>
					<div class='col-sm-3'>
					</div>

					<div id='posts' class='col-sm-6'>
						<div class='row'>
							<div class='col-sm-2'>
								<p><img src='users/$user_image' class='img-circle' width='100px' height='100px'></p>
							</div>

							<div id='postedby' class='col-sm-6'>
								<h4><a style='text-decoration:none; cursor:pointer;color #3897f0;' href='user_profile.php?u_id=$user_id'>$f_name $l_name</a></h4>
								<h4><small style='color:black;'>Updated a post on <strong>$post_date</strong></small></h4>
							</div>

							<div class='col-sm-4'>
							</div>
						</div>
						
						<div class='row'>
							<div class='col-sm-12'>
								<p>$content</p>
								<img id='posts-img' src='uploads/images/$upload_image' style='height:350px;'>
							</div>
						</div>
					</div>

					<div class='col-sm-3'>
					</div>
				</div><br><br>
				";
			}

			else {
				echo"
				<div class='row'>
					<div class='col-sm-3'>
					</div>

					<div id='posts' class='col-sm-6'>
						<div class='row'>
							<div class='col-sm-2'>
								<p><img src='users/$user_image' class='img-circle' width='100px' height='100px'></p>
							</div>

							<div id='postedby' class='col-sm-6'>
								<h4><a style='text-decoration:none; cursor:pointer;color #3897f0;' href='user_profile.php?u_id=$user_id'>$f_name $l_name</a></h4>
								<h4><small style='color:black;'>Updated a post on <strong>$post_date</strong></small></h4>
							</div>

							<div class='col-sm-4'>
							</div>
						</div>

						<div class='row'>
							<div class='col-sm-12'>
								<h4><p>$content</p></h4>
							</div>
						</div>

					</div>

					<div class='col-sm-3'>
					</div>
				</div><br><br>
				";
			}

		}

	}

}


function results() {
	global $con;

	if (isset($_GET['search'])) {
		
		$search_query = htmlentities($_GET['user_query']);
	}

	$get_posts = "select * from posts where post_content like '%$search_query%' OR upload_image like '%$search_query%'";

	$run_posts = mysqli_query($con, $get_posts);

	while($row_posts = mysqli_fetch_array($run_posts)){

		$post_id = $row_posts['post_id'];
		$user_id = $row_posts['user_id'];

		// We will only show a maximum of 200 words obn each post
		$content = substr($row_posts['post_content'], 0,200);

		$upload_image = $row_posts['upload_image'];
		$post_date = $row_posts['post_date'];

		$user = "select *from users where user_id='$user_id' AND posts='yes'";
		$run_user = mysqli_query($con,$user);
		$row_user = mysqli_fetch_array($run_user);

		$f_name = $row_user['f_name'];
		$l_name = $row_user['l_name'];
		$user_image = $row_user['user_image'];

		/* now displaying posts from database */

		// if the user uploads an image without text
		if($content=="No" && strlen($upload_image) >= 1){
			echo"

				<div id='posts' class='col-sm-6'>

					<div class='row'>
						<div class='col-sm-2 text-center'>
							<img src='users/$user_image' class='img-circle'></<img>
						</div>
						
						<div id='postedby' class='col-sm-6'>
							<p><a href='user_profile.php?u_id=$user_id'>$f_name $l_name</a></p>
							<p><i class='fa fa-clock-o'></i> $post_date</p>
						</div>
					</div>

					<div class='row'>
						<div class='col-sm-12'>
							<img id='posts-img' src='uploads/images/$upload_image'>
						</div>
					</div>
					
					<div class='post-footer'>
						<a href='#' class='text-dark'><i class='fa fa-heart-o fa-lg'></i> Like</a>
						<a href='#' class='text-dark'><i class='fa fa-link fa-lg'></i> Share</a>
						<a href='single.php?post_id=$post_id' class='text-dark'><i class='fa fa-comment-o fa-lg'></i> Comment</a>
					</div>

				</div><br><br>
			";
		}

		else if(strlen($content) >= 1 && strlen($upload_image) >= 1){
			echo"
			
				

				<div id='posts' class='col-sm-6'>
					<div class='row'>
						<div class='col-sm-2 text-center'>
							<img src='users/$user_image' class='img-circle'></img>
						</div>

						<div id='postedby' class='col-sm-6'>
							<p><a href='user_profile.php?u_id=$user_id'>$f_name $l_name</a></p>
							<p><i class='fa fa-clock-o'></i> $post_date</p>
						</div>
					</div>

					<div class='post-footer'>
						<a href='#' class='text-dark'><i class='fa fa-heart-o fa-lg'></i> Like</a>
						<a href='#' class='text-dark'><i class='fa fa-link fa-lg'></i> Share</a>
						<a href='single.php?post_id=$post_id' class='text-dark'><i class='fa fa-comment-o fa-lg'></i> Comment</a>
					</div>

				</div><br><br>
			";
		}

		else{
			echo"
			
				<div id='posts' class='col-sm-6'>
					<div class='row'>
						<div class='col-sm-2 text-center'>
							<img src='users/$user_image' class='img-circle'></img>
						</div>

						<div id='postedby' class='col-sm-6'>
							<p><a href='user_profile.php?u_id=$user_id'>$f_name $l_name</a></p>
							<p><i class='fa fa-clock-o'></i> $post_date</small></p>
						</div>
					</div>

					<div class='row'>
						<div class='col-sm-12'>
							<h4><p>$content</p></h4>
						</div>
					</div>

					<div class='post-footer'>
						<a href='#' class='text-dark'><i class='fa fa-heart-o fa-lg'></i> Like</a>
						<a href='#' class='text-dark'><i class='fa fa-link fa-lg'></i> Share</a>
						<a href='single.php?post_id=$post_id' class='text-dark'><i class='fa fa-comment-o fa-lg'></i> Comment</a>
					</div>

				</div><br><br>
			";
		}
	}
}


// FIND PEOPLE

function search_user() {
	global $con;

	if (isset($_GET['search_user_btn'])) {
		$search_query = htmlentities($_GET['search_user']);
		$get_user = "select * from users where f_name like '%$search_query%' OR l_name like '%$search_query%' OR user_name like '%$search_query%'";
	}

	else {
		$get_user = "select * from users";
	}

	$run_user = mysqli_query($con, $get_user);

	while ($row_user=mysqli_fetch_array($run_user)) {
	    $user_id = $row_user['user_id'];
	    $f_name = $row_user['f_name'];
	    $l_name = $row_user['l_name'];
	    $username = $row_user['user_name'];
	    $user_image = $row_user['user_image'];
        $user_bio = $row_user['describe_user'];
	    echo "
	    	<div class='row' style='margin-left:3%;'>
				<div class='col-sm-11'>

					<div class='row listuser' id='find_people'>
					
	    				<div class='col-sm-2'>
	    					<a href='user_profile.php?u_id=$user_id'>
	    						<img src='users/$user_image' width='70px' height='70px' title='$username' style='float:left;margin-top:20px;border-radius:50%;'/>
	    					</a>
	    				</div>
	    				<div class='col-sm-10'>
	    					<a style='text-decoration:none;cursor:pointer;color:#3897f0;' href='user_profile.php?u_id=$user_id'>
	    						<strong><h3>$f_name $l_name</h3></strong>
							</a>
							<p>$user_bio</p><br>
	    				</div>
	    				<div class='col-sm-3'>
	    				</div>
	    			</div>
	    		</div>
	    		<div class='col-sm-4'>
	    		</div>
			</div>
			<hr>
	    ";

	}

}

?>