# social-network
PHP, MySQL

The database I use called "social."
There are 4 tables so far:
a. Users:
	1	id  Primary	int(11)	AUTO_INCREMENT
	2	first_name	varchar(25)	
	3	last_name	varchar(30)
	4	username	varchar(100)
	5	email	varchar(100)
	6	password	varchar(225)
	7	signup_date	date
	8	profile_pic	varchar(225)
	9	num_posts	int(11)
	10	num_likes	int(11)	
	11	user_closed	varchar(3)
	12	friend_array	text
b. posts:
  1	id  Primary	int(11)	AUTO_INCREMENT	
	2	body	text
	3	added_by	varchar(200)	
	4	user_to varchar(200)
	5	date_added	datetime
	6	user_closed	varchar(3)	
	7	deleted	varchar(3)	
	8	likes	int(11)	
c. comments:
  1	id Primary	int(11)	AUTO_INCREMENT
	2	body	text
	3	posted_by	varchar(200)
	4	posted_to	varchar(200)
	5	date_added	datetime
	6	removed	varchar(3)
	7	post_id	int(11)
d. likes:
  1	id Primary	int(11)
	2	username	varchar(225)
	3	post_id	int(11)

I changed iframes to embed elements for submit/show comments and likes after posts.
The main files that are used for handling comments are:
  comment_frame.php  
  /includes/classes/Post.php
  /includes/ajax_load_posts.php
For likes:
  like.php
  /includes/classes/Post.php
  
Following files can be ignored, I've used them to try to experiment:
  comments.php
  includes/classes/Comments.php
  includes/classes/Post_copy2.php
  
I would like to used AJAX to handle comments and likes loading and execution.

Down the line, I would like to implement the option to post posts as global or friends only, and show only posts that are avaliable to see for a certain user.
And, therefore, show global posts even if your is not logged in, without the option to like or comment. Plus, add login/register instead of top right user menu.

Thanks for looking it over, Gabriel!
