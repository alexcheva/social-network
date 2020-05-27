# social-network
PHP, MySQL, jQuery, Bootstrap, FontAwesome

The database I use called "**social**."<br/>
There are 4 tables so far:<br/><br/>
a. **Users**:<br/>
	1	***id***  Primary	int(11)	AUTO_INCREMENT<br/>
	2	***first_name***	varchar(25)	<br/>
        3       ***last_name*** varchar(30)<br/>
	4	***username***	varchar(100)<br/>
	5	***email***	varchar(100)<br/>
	6	***password***	varchar(225)<br/>
	7	***signup_date***	date<br/>
	8	***profile_pic***	varchar(225)<br/>
	9	***num_posts***	int(11)<br/>
	10	***num_likes***	int(11)	<br/>
	11	***user_closed***	varchar(3)<br/>
	12	***friend_array***	text<br/><br/>
b. **posts**:<br/>
        1	***id***  Primary	int(11)	AUTO_INCREMENT	<br/>
	2	***body***	text<br/>
	3	***added_by***	varchar(200)	<br/>
	4	***user_to*** varchar(200)<br/>
	5	***date_added***	datetime<br/>
	6	***user_closed***	varchar(3)	<br/>
	7	***deleted***	varchar(3)	<br/>
	8	***likes***	int(11)	<br/><br/>
c. **comments**:<br/>
        1	***id*** Primary int(11)	AUTO_INCREMENT<br/>
	2	***body***	text<br/>
	3	***posted_by***	varchar(200)<br/>
	4	***posted_to***	varchar(200)<br/>
	5	***date_added***	datetime<br/>
	6	***removed***	varchar(3)<br/>
	7	***post_id***	int(11)<br/><br/>
d. **likes**:<br/>
        1	***id*** Primary	int(11)	 AUTO_INCREMENT<br/>
	2	***username***	varchar(225)<br/>
	3	***post_id***	int(11)<br/><br/>

I've changed iframes to embed elements for submit/show comments and likes after posts.<br/><br/>
The main files that are used for handling ***comments*** are:<br/>
  *comment_frame.php  <br/>
  /includes/classes/Post.php<br/>
  /includes/ajax_load_posts.php<br/>*<br/>
For ***likes***:<br/>
  *like.php<br/>
  /includes/classes/Post.php<br/>
  <br/>*
Following files can be ignored, I've used them to try to experiment:<br/>
  ~~comments.php<br/>
  includes/classes/Comments.php<br/>
  includes/classes/Post_copy2.php<br/>~~
  <br/><br/>
I would like to used AJAX to handle comments and likes loading and execution.<br/>
<br/>
Down the line, I would like to implement the option to post posts as global or friends only, and show only posts that are avaliable to see for a certain user. And, therefore, show global posts even if user is not logged in, without the option to like or comment. Plus, add login/register instead of top right user menu.<br/>
<br/>
Thanks for looking it over, Gabriel!<br/>

SQL:

CREATE TABLE users(
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(25) NOT NULL,
    last_name VARCHAR(30) NOT NULL,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    PASSWORD VARCHAR(225) NOT NULL,
    signup_date DATE NOT NULL,
    profile_pic VARCHAR(225) NOT NULL,
    num_posts INT(11) NOT NULL,
    num_likes INT(11) NOT NULL,
    user_closed VARCHAR(3) NOT NULL,
    friend_array TEXT NOT NULL
); 
CREATE TABLE posts(
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    body TEXT NOT NULL,
    added_by VARCHAR(200) NOT NULL,
    user_to VARCHAR(200) NOT NULL,
    date_added DATETIME NOT NULL,
    user_closed VARCHAR(3) NOT NULL,
    deleted VARCHAR(3) NOT NULL,
    likes INT(11) NOT NULL
); 
CREATE TABLE comments(
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    body TEXT NOT NULL,
    posted_by VARCHAR(200) NOT NULL,
    posted_to VARCHAR(200) NOT NULL,
    date_added DATETIME NOT NULL,
    removed VARCHAR(3) NOT NULL,
    post_id INT(11) NOT NULL
); 
CREATE TABLE likes(
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(225) NOT NULL,
    post_id INT(11) NOT NULL
); 
CREATE TABLE messages(
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_to VARCHAR(200) NOT NULL,
    user_from VARCHAR(200) NOT NULL,
    body TEXT NOT NULL,
    DATE DATETIME NOT NULL,
    opened VARCHAR(3) NOT NULL,
    viewed VARCHAR(3) NOT NULL,
    deleted VARCHAR(3) NOT NULL
);
