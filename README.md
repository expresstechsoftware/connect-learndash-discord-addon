![](https://www.expresstechsoftwares.com/wp-content/uploads/learndash_discord_addon_banner.png)

# [Connect LearnDash to Discord](https://www.expresstechsoftwares.com/step-by-step-documentation-guide-on-how-to-connect-learndash-and-discord-server) #
![](https://img.shields.io/badge/build-passing-green) ![License](https://img.shields.io/badge/license-GPL--2.0%2B-red.svg)

### Welcome to the ExpressTech LearnDash Discord Add On GitHub Repository

Create a community of your students by connecting your LearnDash Website to your Discord server.
This add-on enables connecting your LearnDash-enabled website to your discord server. With this plugin, you can create a discord community of your students and assign them discord roles in the server according to the course they are learning.
private access to course content plus discord's ability to add fun and creativity in community engagement will help create a thriving community, discord is safe and designed to help fight spam and promote healthy community discussions.
This plugin promotes Engagement, Upsell and cross-sell opportunities, Advocacy, and referrals that ultimately help increase revenues.

# [Step By Step guide on how to set-up plugin](https://www.expresstechsoftwares.com/step-by-step-documentation-guide-on-how-to-connect-learndash-and-discord-server)


## Installation
- You can find the plugin inside the LearnDash LMS settings Add-ons and click install from there
- OR Upload the `connect-learndash-discord-addon` folder to the `/wp-content/plugins/` directory.
- Activate the plugin through the 'Installed Plugins' page in WordPress admin.

## Connecting the plugin to your Discord Server.
- Inside WP Admin, you will find Discord Settings sub-menu under top-level LearnDash menu in the left hand side.
- Login to your dsicord account and open this url: https://discord.com/developers/applications
- Click Top right button "New Appliaction", and name your Application.
- New screen will load, you need to look at left hand side and see "oAuth".
- See right hand side, you will see "CLIENT ID and CLIENT SECRET" values copy them.
- Open the discord settings page.
- Paste the copied ClientID and ClientSecret.
- Add a Redirect URL, this should be LearnDash Profile of learner. 
- Bot Auth Redirect URL: Add this URL inside Application Redirect settings.
- Now again see inside discord left hand side menu, you will see "Bot" page link.
- This is very important, you need to name your bot and click generate, this will generate "Bot Token".
- Copy the "Bot Token" and paste into "Bot Token" setting of Discord aa-on Plugin.
- Now the last and most important setting, "Server ID".
- - Open https://discord.com/ and go inside your server.
- - Enable Developer mode by going into Advanced setting of your account.
- - Then you should right click on your server name and you will see "Copy ID"
- - Copy and paste into "Guild ID" Settings
- Now you will see "Connect your bot" button on your plugin settings page.
- Click Connect your bot button and this will take you to the Discord authorisation page.
- Here you need to select the Server of which Guild ID you just did copy in above steps.
- Once successfully connect you should see Bot Authorized screen.
- Open again the discord server settings and see Roles menu.
- Please make sure your bot role has the highest priority among all other roles in your discord server roles settings otherwise you will see 5000:Missing Access Error in your plugin error logs.

## Some features
This plugin provides the following features: 
1) Allow any student to connect their discord account with their LearnDash website account.

2) Shortcode [learndash_discord] to display connect/disconnect button.

3) Mapping of courses and discord roles.

4) Send custom welcome message when student joins the server.

5) Send custom message when student completes a course.

6) Send custom message when student completes a lesson.

7) Send custom message when student completes a topic.

8) Send custom message when student completes a quiz.

9) Send custom message when assignment is approved.

## Solution of Missing Access Error
- Inside the log tab you will see "50001:Missing Access", which is happening because the new BOT role need to the TOP priroty among the other roles.
- - The new created BOT will add a ROLE with the same name as it is given to the BOT itself.
- So, Go inside the "Server Settings" from the TOP left menu.
- Go inside the "Roles" and Drag and Drop the new BOT over to the TOP all other roles.
- Do not for forget to save the roles settings

# Fequently Asked Questions
- I'm getting an error in error Log 'Missing Access'
- - Please make sure your bot role has the highest priority among all other roles in your discord server roles settings.
- Role Settings is not appearing.
- - Clear browser cache, to uninstall and install again.
- - Try the disabling cache
- - Try Disabling other plugins, there may be any conflict with another plugin.
- Learner are not being added spontaneously. 
- - Due to the nature of Discord API, we have to use schedules to precisely control API calls, that why actions are delayed. 
- Learner roles are not being assigned spontaneously.
- - Due to the nature of Discord API, we have to use schedules to precisely control API calls, that why actions are delayed. 
- Some learned are not getting their role and there is no error in the log.
- - It is suggested to TRY again OR use another discord account.

# Checkout Our Other Plugins
- [Connect MemberPress and Discord](https://wordpress.org/plugins/expresstechsoftwares-memberpress-discord-add-on/)
- [Connect PaidmembershipPro and Discord](https://wordpress.org/plugins/pmpro-discord-add-on/)
- [Product Questions & Answers for WooCommerce](https://wordpress.org/plugins/product-questions-answers-for-woocommerce/)
- [Webhook For WCFM Vendors](https://wordpress.org/plugins/webhook-for-wcfm-vendors/)
- [Inspect HTTP Requests](https://wordpress.org/plugins/inspect-http-requests/)