It's time to move forward with the plugin review "liaison"!

Your plugin is not yet ready to be approved, you are receiving this email because the volunteers have manually checked it and have found some issues in the code / functionality of your plugin.

Please check this email thoroughly, address any issues listed, test your changes, and upload a corrected version of your code if all is well.

List of issues found


## Not permitted files

A plugin typically consists of files related to the plugin functionality (php, js, css, txt, md) and maybe some multimedia files (png, svg, jpg) and / or data files (json, xml).

We have detected files that are not among of the files normally found in a plugin, are they necessary? If not, then those won't be allowed.

Optionally, you can use the wp dist-archive command from WP-CLI in conjunction with a .distignore file. This prevents unwanted files from being included in the distribution archive.

Example(s) from your plugin:
minifystuff/tests/test-minify.php.bak
minifystuff/tests/bootstrap.php.bak
minifystuff/README.txt.bak
minifystuff/minifystuff.php.bak



## Use wp_enqueue commands

Your plugin is not correctly including JS and/or CSS. You should be using the built in functions for this:

When including JavaScript code you can use:
wp_register_script() and wp_enqueue_script() to add JavaScript code from a file.
wp_add_inline_script() to add inline JavaScript code to previous declared scripts.

When including CSS you can use:
wp_register_style() and wp_enqueue_style() to add CSS from a file.
wp_add_inline_style() to add inline CSS to previously declared CSS.

Note that as of WordPress 6.3, you can easily pass attributes like defer or async: https://make.wordpress.org/core/2023/07/14/registering-scripts-with-async-and-defer-attributes-in-wordpress-6-3/

Also, as of WordPress 5.7, you can pass other attributes by using this functions and filters: https://make.wordpress.org/core/2021/02/23/introducing-script-attributes-related-functions-in-wordpress-5-7/

If you're trying to enqueue on the admin pages you'll want to use the admin enqueues.

https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/
https://developer.wordpress.org/reference/hooks/admin_print_scripts/
https://developer.wordpress.org/reference/hooks/admin_print_styles/

Example(s) from your plugin:
tests/test-minify.php:33 $post_buffer = '<script>
tests/test-minify.php:30 $pre_buffer  = '<script>
tests/test-minify.php:42 $post_buffer = '<style></style>';
admin/class-minifystuff-admin.php:135 <style>
tests/test-minify.php:39 $pre_buffer  = '<style>



## Nonces and User Permissions Needed for Security

Please add a nonce check to your input calls ($_POST, $_GET, $REQUEST) to prevent unauthorized access.

If you use wp_ajax_ to trigger submission checks, remember they also need a nonce check.

👮 Checking permissions: Keep in mind, a nonce check alone is not bulletproof security. Do not rely on nonces for authorization purposes. When needed, use it together with current_user_can() in order to prevent users without the right permissions from accessing things they shouldn't.

Also make sure that the nonce logic is correct by making sure it cannot be bypassed. Checking the nonce with current_user_can() is great, but mixing it with other checks can make the condition more complex and, without realising it, bypassable, remember that anything can be sent through an input, don't trust any input.

Keep performance in mind. Don't check for post submission outside of functions. Doing so means that the check will run on every single load of the plugin, which means that every single person who views any page on a site using your plugin will be checking for a submission. This will make your code slow and unwieldy for users on any high traffic site, leading to instability and eventually crashes.

The following links may assist you in development:

https://developer.wordpress.org/plugins/security/nonces/
https://developer.wordpress.org/plugins/javascript/ajax/#nonce
https://developer.wordpress.org/plugins/settings/settings-api/

From your plugin:
admin/class-minifystuff-admin.php:112 No nonce check was found validating the origin of inputs in the lines 112-126 - in the context of the classMethod minifyStuff_Admin::minify_stuff_menu_options()
# ↳ Line 126: if ( isset( $_POST[ 'minify_stuff_active' ] ) ) $minify_stuff_active = filter_var ( wp_unslash( $_POST[ 'minify_stuff_active' ] ), FILTER_SANITIZE_FULL_SPECIAL_CHARS ); else $minify_stuff_active = 'yes';
# ↳ Line 127: if ( isset( $_POST[ 'minify_javascript' ] ) ) $minify_javascript = filter_var ( wp_unslash( $_POST[ 'minify_javascript' ] ), FILTER_SANITIZE_FULL_SPECIAL_CHARS ); else $minify_javascript = 'yes';
# ↳ Line 128: if ( isset( $_POST[ 'minify_comments' ] ) ) $minify_comments = filter_var ( wp_unslash( $_POST[ 'minify_comments' ] ), FILTER_SANITIZE_FULL_SPECIAL_CHARS ); else $minify_comments = 'yes';


Please, make sure that the nonce logic is correct. It's important to be cautious when structuring conditional checks around nonces.
admin/class-minifystuff-admin.php:123 if ( isset( $_POST['minify_html_nonce'] ) && !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['minify_html_nonce'] ) ), 'minify-html-nonce' ) ) {



Security note: In the current implementation, the nonce is only verified if all other conditions are met first. This means that if those other conditions aren’t satisfied, the nonce check is completely skipped — effectively making the nonce optional and opening up a potential security bypass.

This is particularly risky when you use an isset() or !empty() check combined with !wp_verify_nonce() in an AND condition. In practice, if the nonce is simply omitted from the request, the condition won’t be evaluated at all, defeating the purpose of using nonces to prevent CSRF.

To enforce nonce validation properly, you should fail early if the nonce is missing. For example:
if ( ! isset( $_POST['minist_nonce'] ) || ! wp_verify_nonce( $_POST['minist_nonce'], 'minist_nonce' ) ) {
    wp_die();
}
// Nonce is present and valid — continue execution

You can also check this with an AND condition, in that case the code would look like this:
if ( isset( $_POST['minist_nonce'] ) && wp_verify_nonce( $_POST['minist_nonce'], 'minist_nonce' ) ) {
    // Nonce is present and valid inside this condition.
}

Example(s) from your plugin:
admin/class-minifystuff-admin.php:123 if ( isset( $_POST['minify_html_nonce'] ) && !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['minify_html_nonce'] ) ), 'minify-html-nonce' ) ) {

## Forcing PHP Sessions on all pages

Using session_start() or ob_start() in your plugin without having it encapsulated in a function means that it will run on every single page load when your plugin is active. Sadly the way PHP Sessions work is they indicate the visitor using sessions is unique and should have a non-cached view of the website.

That means the use of sessions breaks server based caching such as nginx and Varnish. Those types of services are heavily used by managed WordPress hosts, which means your plugin may wind up prohibited on those hosts.

We would greatly prefer that not to happen for you, as it could be detrimental to your plugin’s adoption and user base.

Please remove this from your plugin, or put it only in the function that absolutely must have it.

If you cannot remove it, then you are required to document that use of your plugin may conflict with server based cache services, and you cannot support it’s use on those servers.

This is for your own protection.

Example(s) from your plugin:
ob_start( array($this, 'minify_stuff_output') );

👉 Your next steps

Please, read this email thoroughly.

Take time to fully understand the issues we've raised. Review the examples provided, read the relevant documentation, and research as needed. Our goal is for you to gain a clear understanding of the problems so you can address them effectively and avoid similar issues when maintaining your plugin in the future.
Note that there may be false positives - we are humans and make mistakes, we apologize if there is anything we have gotten wrong. If you have doubts you can ask us for clarification, when asking us please be clear, concise, direct and include an example.

The new review process

Fix the issues in your plugin based on the feedback and your own review as we may not be sharing all the cases where the same issue happens. Use available tools like Plugin Check, PHPCS + WPCS, or similar utilities to help identify problems in your code.
Test your updated plugin on a clean WordPress installation with WP_DEBUG set to true.
⚠️ Do not skip this step. Testing is essential to make sure your fixes actually work and that you haven’t introduced new issues.
Go to "Add your plugin" and upload the updated version. You can continue updating the code there throughout the review process — we'll always check the latest version.
Reply to this email. Please be concise and do not list the changes — we will review the entire plugin again — but do share any clarifications or important context you want us to know.

ℹ️ To make this process as quick as possible and to avoid burden on the volunteers devoting their time to review this plugin's code, we ask you to thoroughly check all shared issues and fix them before sending the code back to us. I know we already asked you to do so, and it is because we are really trying to make it very clear.

While we try to make our reviews as exhaustive as possible we, like you, are humans and may have missed things. We appreciate your patience and understanding.

Review ID: R minify-stuff/liaison/15Aug25/T2 29Aug25/3.6B


--
WordPress Plugins Team | plugins@wordpress.org
https://make.wordpress.org/plugins/
https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/
https://wordpress.org/plugins/plugin-check/
{#HS:3037540491-838267#} 
On Thu, Aug 28, 2025 at 3:37 AM UTC, WordPress.org Plugin Directory <plugins@wordpress.org> wrote:
This is an automated message to confirm that we have received your updated plugin file.

File updated by liaison, version 1.0.0.

https://wordpress.org/plugins/files/2025/08/28_03-37-28_minifystuff.zip