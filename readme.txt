===Ocamba Hood===

Requires at least: 3.8
Tested up to: 6.6
Stable tag: 1.0.4
Donate link: https://delsystems.net
Tags: customers, push, web push, notifications
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Send targeted web push notifications to drive traffic to your website & build a loyal subscriber base with our lightweight, quick WP integration.

== Description ==

**Hood Engage Plugin for WordPress**

Hood Engage simplifies driving traffic to your website, connecting with visitors, and creating a loyal subscriber base. Implement and use advanced Hood Engage features directly from your WP dashboard in just a few clicks.

=== Features ===

* Supports **Chrome** (Desktop & Android), **Safari** (Mac OS X), **Microsoft Edge** (Desktop & Android), **Opera** (Desktop & Android), and **Firefox** (Desktop & Android) on HTTPS sites.
* User-friendly tag management system  - Ocamba Hood's tag management system allows you to easily organize, update, and monitor tags, ensuring accurate data collection and streamlined site performance.
* Advanced scheduler - Schedule tasks based on data-driven customer behavior patterns to reach users when they're most likely to engage, optimize delivery based on user's time zones, set quiet hours, and more. 
* Trigger-based event targeting - Address your customers' needs at precisely the right moment based on their actions on your website. 
* Opt-in customization - Personalize when and how you want your visitors to opt in for browser notifications. 
* Real-time analytics - Gain actionable insights with 100+ metrics to fine-tune your content strategy for that extra result. 
* Advanced segmentation - Apply refined targeting techniques to deliver messages that resonate most with each customer segment. 

=== Benefits ===
**Integrated Experience**
No need for multiple tools or platforms. Get all the essential web services in one comprehensive package.

**User-Friendly**
Built with WordPress users in mind, Ocamba Hood guarantees easy setup and management.

**Optimized Performance**
Ocamba Hood's services are designed to run smoothly without affecting your website load times or user experience.

Whether you're a beginner or an expert, the Ocamba Hood WP plugin is quick to install. Follow these simple instructions, and you can begin driving traffic and engaging visitors immediately. 


== Installation ==

**Installing the Plugin**
1. Navigate to your WordPress dashboard.
2. Go to Plugins > Add New.
3. In the search bar, type **Ocamba Hood**.
4. Find the plugin in the search results, and click Install Now.
5. After installation, click Activate.

**Retrieve Your Code Key**
1. Open a new tab and go to the Ocamba website where you can find the Code Key (e.g., WORKSPACE.ocamba.app).
2. Navigate to the **Hood** => **Tags**, click on the tag you want to integrate.
3. Locate and copy your unique Code Key.

**Configure the Plugin with Your Code Key**
1. Go back to your WordPress dashboard.
2. Navigate to the configuration page of “[Ocamba]”. It is located directly in the left-hand sidebar.
3. Find the input field labeled "**Code Key**".
4. Paste or type in your copied Code Key.
5. Click "Save Changes".
6. By default, the plugin is set to activate. When you input a valid Code Key, and if the plugin is set to active, a javascript code snippet with Code Key will be rendered on your website.
7. When you toggle switch to off, a code snippet with Code Key won't be rendered on your website.

That's all! 

Your plugin is now configured and ready to use. 

Enjoy the features and benefits of **Ocamba Hood**!


== Frequently Asked Questions ==

= Does the Ocamba Hood plugin use 3rd party services? =

Yes. Ocamba Hood relies on Ocamba services located at https://cdn.ocmtag.com and https://cdn.ocmhood.com. We are using https://cdn.ocmtag.com to determine if the inputted Code Key is valid, and we are using https://cdn.ocmhood.com to import the main scripts for Service Worker handlings.

= Does the Ocamba Hood plugin create files outside its plugin directory? =

Yes. When you input the Ocamba Hood Code Key for the first time, the Ocamba Hood Plugin creates a sw.js file inside the public_html directory. File sw.js is used to install and register service workers. The importScripts() javascript function is inside the file. importScripts() is importing all logic from our servers. When the plugin is deleted, sw.js is deleted also.

== Screenshots ==

1. Ocamba Hood Core Dashboard, where you can see your performance.
2. Ocamba Hood WordPress Plugin -> Settings page, where you can configure your Hood Code Key.
3. Notifications on OSX.
4. Notifications on Windows.
5. Ocamba Hood Audience, where you can target your audience for engagment.
6. Ocamba Hood Campaign Scheduler, where you can schedule campaigns.
7. Ocamba Hood Core Users, where you can see subscribed users and their engagment.


== Changelog ==

= 1.0.4 =
- Update: Variable nameing changed: "Tag" -> "Code Key"
- Update: Documentation URL updated
- Bug Fix: ACTIVATE/DEACTIVATE switch can only be used before there is any changes to the inputted Code Key
- Bug Fix: If there is no inputted "Code Key" then ACTIVATE/DEACTIVATE switch will be inactive

= 1.0.3 =
- Update: Removed unused functions

= 1.0.2 =
- Update: "Tested up to" Wordpress version to 6.6
- Update: Hood Engage plugin icon

= 1.0.1 =
* Release Date - 7th August, 2024*
* Initial release 
