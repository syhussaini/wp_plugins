=== Admin Welcome Message ===
Contributors: Syed Hussaini
Tags: admin, modal, welcome, notification, admin-bar, dashboard
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Plugin URI: https://www.zaha.in

**Note:** This is the plugin's readme.txt file. For repository overview, see the main [README.md](https://github.com/syhussaini/wp_plugins#readme) in the repository root.

A customizable admin modal that displays welcome messages with configurable content, styling, and session behavior.

== Description ==

Admin Welcome Message is a powerful WordPress plugin that allows site administrators to create and customize welcome modals for their admin area. Perfect for onboarding new users, displaying important announcements, or providing quick access to help resources.

**Key Features:**

* **Fully Customizable Content**: Set custom titles, messages, CTA buttons, and footer notes
* **Rich Text Editor**: Use the WordPress editor for message content with HTML support
* **Flexible Session Management**: Choose between per-session dismissal or configurable cooldown periods
* **Role-Based Targeting**: Restrict modal display to specific user roles
* **Screen-Specific Display**: Show modals only on specific admin screens
* **Color Customization**: Full control over header, body, footer, and button colors
* **Responsive Design**: Mobile-friendly modal that works on all devices
* **Accessibility Features**: Keyboard navigation, focus management, and screen reader support
* **Developer Friendly**: Hooks and filters for customization

**Perfect For:**
* Onboarding new administrators
* Displaying important site updates
* Providing quick access to help documentation
* Announcing new features or changes
* Training and guidance for content editors

== Installation ==

1. Upload the `admin-welcome-message` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings → Admin Welcome Message to configure your modal
4. Customize the content, colors, and behavior settings
5. Save your changes and test the modal

== Frequently Asked Questions ==

= Can I show different modals to different user roles? =

Yes! You can restrict modal display to specific user roles in the Targeting Settings section. Leave the roles field empty to show the modal to all users with appropriate capabilities.

= How does the session management work? =

The plugin offers two dismissal modes:
* **Per Session**: Modal stays hidden until the user logs out or closes their browser tab
* **Cooldown Minutes**: Modal reappears after a specified number of minutes (1-1440)

= Can I restrict the modal to specific admin screens? =

Absolutely! In the Targeting Settings, you can enter screen IDs (one per line) to restrict where the modal appears. Common screen IDs include: dashboard, post, edit-post, page, edit-page, upload, users, etc.

= Is the modal mobile-friendly? =

Yes! The modal is fully responsive and works great on all devices. It automatically adjusts its size and layout for mobile screens.

= Can developers customize the plugin behavior? =

Yes! The plugin provides several hooks and filters:
* `awm_should_show_modal` - Control when the modal should display
* `awm_modal_options` - Modify modal options before rendering
* `awm_modal_rendered` - Execute code after modal is rendered

== Screenshots ==

1. Settings page with content configuration
2. Appearance settings with color pickers
3. Behavior and targeting options
4. Live preview of the modal
5. Modal displayed in admin area

== Changelog ==

= 1.0.0 =
* Initial release
* Full customization options
* Role and screen targeting
* Session management
* Responsive design
* Accessibility features

== Upgrade Notice ==

= 1.0.0 =
Initial release of Admin Welcome Message plugin.

== Developer Documentation ==

**Filters:**

```php
// Control when modal should show
add_filter('awm_should_show_modal', function($should_show, $screen, $user) {
    // Your logic here
    return $should_show;
}, 10, 3);

// Modify modal options
add_filter('awm_modal_options', function($options) {
    // Modify options before rendering
    return $options;
});

// Execute code after modal renders
add_action('awm_modal_rendered', function($options) {
    // Your code here
});
```

**Helper Functions:**

```php
// Get plugin options with defaults
$options = awm_get_options();

// Check if user can see modal
$can_see = awm_user_can_see_modal();

// Check if current screen should show modal
$screen_ok = awm_screen_should_show_modal();

// Get modal display conditions
$conditions = awm_get_modal_conditions();
```

**JavaScript API:**

```javascript
// Reset modal session data (for testing)
window.awmResetModal();

// Access modal options
console.log(window.awmModalData.options);
```

== Support ==

For support, feature requests, or bug reports, please visit our GitHub Issues page: https://github.com/syhussaini/wp_plugins/issues

== Credits ==

Developed by Syed Hussaini (https://www.zaha.in) with ❤️ for the WordPress community.

== License ==

This plugin is licensed under the GPL v2 or later.
