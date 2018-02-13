<?php
/*
Plugin Name: Subscribe email kodino
Description: This plugin helps you to get subscriptions through email and you can get email for every user signup for news letters.
Version: 1.0
*/

if (!defined('WPINC')) {
die;
}

function activate_plugin_subscribe()
{
require_once plugin_dir_path(__FILE__) . 'includes/SubEmailKodino.php';
SubEmailKodino::activate();
}

register_activation_hook(__FILE__, 'activate_plugin_subscribe');

function delete_plugin_subscribe()
{
require_once plugin_dir_path(__FILE__) . 'includes/SubEmailKodino.php';
SubEmailKodino::delete();
}

register_uninstall_hook(__FILE__, 'delete_plugin_subscribe');

/* Find solution how to save users email from form in database
if (isset($_POST["submit_form"]) && isset($_POST['submit_subscription']) && isset($_POST["subscriber_email"]) != "") {
$table = $wpdb->prefix . "email_subscribers";
$email = strip_tags($_POST["subscriber_email"], "");
$wpdb->insert(
$table,
array(
'subscriber_email' => $email
)
);
}
*/

add_action('init', 'register_shortcode_for_newsletter');

function register_shortcode_for_newsletter()
{

add_shortcode('email_subscriptions', 'email_subscription');
}

class Subscription_widget extends WP_Widget
{
public function __construct()
{
$widget_ops = array(
'classname' => 'email_subscription',
'description' => 'A Simple Email Subscription Widget to get subscribers info',
);
parent::__construct('my_widget', 'Subscriptions', $widget_ops);
}

public function widget($args, $instance)
{
echo '<aside>';
do_action('email_subscription');
echo '</aside>';
}
}

add_action('widgets_init', function () {
register_widget('Subscription_widget');
});

if (!function_exists('email_subscription')) {
add_action('email_subscription', 'email_subscription');

function email_subscription()
{
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_subscription'])) {

if (filter_var($_POST['subscriber_email'], FILTER_VALIDATE_EMAIL)) {

$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

$subject = sprintf(__('New Subscription on %s', 'user'), $blogname);

$to = get_option('admin_email');

$headers = 'From: ' . sprintf(__('%s Admin', 'user'), $blogname) . ' <No-repy@' . $_SERVER['SERVER_NAME'] . '>' . PHP_EOL;

$message = sprintf(__('Hi ,', 'user')) . PHP_EOL . PHP_EOL;
$message .= sprintf(__('You have a new subscription on your %s website.', 'user'), $blogname) . PHP_EOL . PHP_EOL;
$message .= __('Email Details', 'user') . PHP_EOL;
$message .= __('-----------------') . PHP_EOL;
$message .= __('User E-mail: ', 'user') . stripslashes($_POST['subscriber_email']) . PHP_EOL;
$message .= __('Regards,', 'user') . PHP_EOL . PHP_EOL;
$message .= sprintf(__('Your %s Team', 'user'), $blogname) . PHP_EOL;
$message .= trailingslashit(get_option('home')) . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;

if (wp_mail($to, $subject, $message, $headers)) {

echo '<script>
alert("Your e-mail (' . $_POST['subscriber_email'] . ') has been added to our mailing list!");
</script>';
echo '<script>window.location.href="index.php";</script>';
} else {
echo '<script>
alert("There was a problem with your e-mail ( ' . $_POST['subscriber_email'] . ')");</script>';
echo '<script>window.location.href="index.php";</script>';
}
} else {
echo '<script>alert("There was a problem with your e-mail ( ' . $_POST['subscriber_email'] . ')");</script>';
echo '<script>window.location.href="index.php";</script>';
}
} ?>
<div class="block block-gray block-inner-sm-top block-inner-btm">
<div class="container">
<div class="block-header align-center">
<div class="h3"><?= __('Freshest Coupons to your e-mail', KODINO_TEXTDOMAIN) ?></div>
<div class="block-perex"><?= __('Fill in your e-mail and you`ll never miss another deal.', KODINO_TEXTDOMAIN)
?></div>
</div>
<form action="" id="newsletter-footer" method="POST" class="form get-coupon">
<fieldset>
<div class="control-group">
<div class="control-field">
<div class="append">
<div class="append-main">
<input type="email" name="subscriber_email" class="input input-sign"
placeholder="<?= __('E-mail Address', KODINO_TEXTDOMAIN) ?>">
</div>
<div class="append-side">
<input type="hidden" name="submit_subscription" value="Submit">

<input type="submit" name="submit_form"
value=" <?= __('Subscribe', KODINO_TEXTDOMAIN) ?>"
class="btn btn-primary btn-gradient btn-sign btn-wide">
</div>
</div>
</div>
</div>
</fieldset>
</form>
</div>
</div>
<?php }
} ?>
