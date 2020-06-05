# social-network
PHP, MySQL, jQuery, Bootstrap, FontAwesome

Need to install XAMPP for it to work.<br/>
Then copy git into htdocs folder.<br/>
Then create database in phpmyadmin called "**social**."<br/>
Here is SQL to create tables:<br/>

CREATE TABLE users(
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(25) NOT NULL,
    last_name VARCHAR(30) NOT NULL,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(225) NOT NULL,
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
    date DATETIME NOT NULL,
    opened VARCHAR(3) NOT NULL,
    viewed VARCHAR(3) NOT NULL,
    deleted VARCHAR(3) NOT NULL
);
CREATE TABLE friend_requests(
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_to VARCHAR(225) NOT NULL,
    user_from VARCHAR(225) NOT NULL
); 

<br/>
I would like to implement the option to post posts as global or friends only, and show only posts that are avaliable to see for a certain user. And, therefore, show global posts even if user is not logged in, without the option to like or comment.<br/> Plus, add login/register instead of top right user menu.<br/>
<br/>
Thanks for looking it over, Gabriel!<br/>
