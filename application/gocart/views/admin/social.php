<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tcballer
 * Date: 7/22/13
 * Time: 11:10 PM
 * To change this template use File | Settings | File Templates.
 */
include('header.php');?>

    <div>
        <h2>Welcome to a Twitter OAuth PHP for CodeIgniter/Gocart example.</h2>

        <p>This site is a basic showcase of Twitters OAuth authentication method. If you are having issues try <a href='./clearsessions'>clearing your session</a>.</p>

        <p>
            Links:
            <a href='http://github.com/abraham/twitteroauth'>Source Code</a> &amp;
            <a href='http://wiki.github.com/abraham/twitteroauth/documentation'>Documentation</a> |
            Contact @<a href='http://twitter.com/abraham'>abraham</a>
        </p>
        <hr />
        <?php if (isset($menu)) { ?>
            <?php echo $menu; ?>
        <?php } ?>
    </div>
<?php if (isset($status_text)) { ?>
    <?php echo '<h3>'.$status_text.'</h3>'; ?>
<?php } ?>
    <p>
    <pre>
        <?php print_r($content); ?>
      </pre>
    </p>


<?php include('footer.php');?>