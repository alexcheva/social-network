# social-network
PHP, MySQL, jQuery, Bootstrap, FontAwesome
-------------
Need to install XAMPP for it to work.<br/>
Then copy git into htdocs folder.<br/>
Then create database in phpmyadmin called "**social**."<br/>
Here is SQL to create tables:<br/>
```
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
    user_blocked VARCHAR(3) NOT NULL,
    admin VARCHAR(3) NOT NULL,
    friend_array TEXT NOT NULL
); 
CREATE TABLE posts(
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    body TEXT NOT NULL,
    added_by VARCHAR(200) NOT NULL,
    user_to VARCHAR(200) NOT NULL,
    date_added DATETIME NOT NULL,
    global VARCHAR(3) NOT NULL,
    user_closed VARCHAR(3) NOT NULL,
    likes INT(11) NOT NULL,
    image VARCHAR(500) NOT NULL
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
    deleted VARCHAR(3) NOT NULL,
    image VARCHAR(500) NOT NULL
);
CREATE TABLE friend_requests(
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_to VARCHAR(225) NOT NULL,
    user_from VARCHAR(225) NOT NULL
); <br/>

CREATE TABLE notifications(
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_to VARCHAR(200) NOT NULL,
    user_from VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(100) NOT NULL,
    datetime DATETIME NOT NULL,
    opened VARCHAR(3) NOT NULL,
    viewed VARCHAR(3) NOT NULL
);

CREATE TABLE details(
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(200) NOT NULL,
    about TEXT NOT NULL,
    interests TEXT NOT NULL,
    bands TEXT NOT NULL,
    edited VARCHAR(3) NOT NULL
); 
```
<br/>
I would like to:<br/>
✔ 1. Add option to post globally or to friends only.<br/>
✔ 2. Show global posts to everyone.<br/>
3. Unregistered users can't comment or like.<br/>
4. If user is not logged in, add login/register to top right nav bar.<br/>
✔ 5. Add media queries for mobile devices.<br/>
✔ 6. Add notifications.<br/>
✔ 7. Add about user.<br/>
✔ 8. Add user settings.<br/>
✔ 9. Add live search.<br/>
✔ 10. Add emoji to posts.<br/>
✔ 11. Add videos/pictures to posts.<br/>
✔ 12. Edit post functionality.<br/>
✔ 13. Delete comments.<br/>
✔ 14. Edit comments.<br/>
✔ 15. Delete messages.<br/>
✔ 16. Put all js into one file.<br/>
✔ 17. Add paste image url.<br/>
✔ 18. Add block user option.<br/>
✔ 19. Add admins.<br/>
✔ 20. Choose username.<br/>
21. Change username.<br/>
