# timer auto logout module

In the autologout module, one issue was the lack of a proper timer to display the remaining time before the user is logged out due to inactivity on the site. This module solve the problem using a block.

Additionally, if you have two browser tabs open and remain inactive in one tab, without this module, you wouldn’t be able to know the exact logout time. However, by utilizing local storage in the browser, this module can synchronize the timer across all tabs.
The issues related to the autologout module are as follows:

1.	Timer issue: There wasn’t a proper timer to show the remaining time until logout.
2.	Timer synchronization between tabs: Timers were not syncing across different browser tabs.
3.	AJAX issue: At times, clicking on the “Reset Timer” button in the module didn’t work properly.
4.	Logout failure: When two tabs were open, the logout process sometimes failed.
5.	Reset Timer button issue: In some cases, instead of resetting the timer, more time was added to the timer.

Benefits of the timer auto logout module:
1. Timer Synchronization: By utilizing local storage, the timer is synchronized across all tabs, allowing users to know the exact logout time.
2. Customizable Block: The module uses a block to display the remaining time until logout, which can be easily placed on various pages.
3. Timer Reset: Users have the option to reset the timer by clicking a button, effectively restarting their session if needed.


All Issues with the Auto Logout Timer Module Have Been Resolved
## Table of contents

- Requirements
- Installation
- Configuration

## Requirements

This module requires the following autologout module to be installed:

- [autologout](https://www.drupal.org/project/autologout)


## Installation

you can for install this module using drush:

```
drush en timer_auto_logout
```

## Configuration

1. Go to the block layout page at `admin/config/people/autologout/timer-auto-logout`.
2. Add the block to the desired region.
