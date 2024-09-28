(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.timerAutoLogout = {
    attach: function (context, settings) {
      // Start the timer
      const intervalValue = +document.querySelector('.interval_value').innerHTML;
      let timeoutPadding = +document.querySelector('#auto_logout_timeout_padding').innerHTML;
      timeoutPadding = Number(timeoutPadding) < 5 ? 5 : Number(timeoutPadding) + 5;
      let dateNow = new Date();
      dateNow.setSeconds(dateNow.getSeconds() + intervalValue);

      // Store the timestamp in localStorage
      localStorage.setItem('timer_auto_logout_reset-timer', dateNow);

      let isAjaxRequestSent = false; // Flag to prevent multiple requests

      // Format seconds to HH:MM:SS
      function formatSeconds(seconds) {
        const hrs = Math.floor(seconds / 3600);
        const mins = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;

        const formattedHrs = String(hrs).padStart(2, '0');
        const formattedMins = String(mins).padStart(2, '0');
        const formattedSecs = String(secs).padStart(2, '0');

        return `${formattedHrs}:${formattedMins}:${formattedSecs}`;
      }

      // Function to check time and update interval
      function checkTime() {
        const timestamp = new Date(localStorage.getItem('timer_auto_logout_reset-timer'));

        const interval = setInterval(() => {
          const updateDate = new Date();
          let newTimestamp = new Date(localStorage.getItem('timer_auto_logout_reset-timer'));
          const newTime = Math.floor((newTimestamp - updateDate) / 1000);

          // Update time display
          document.querySelector('.interval-set').innerText = formatSeconds(newTime);

          // Handle click event for resetting the timer
          $('#timer_auto_logout_reset-timer', context).on('click', function () {
            const updateTime = new Date();

            if (!isAjaxRequestSent) {
              // Check if the request is already sent
              isAjaxRequestSent = true; // Mark the request as sent
              $.ajax({
                url: '/autologout_ajax_set_last', type: 'GET', success: function (data) {
                  isAjaxRequestSent = false; // Reset the flag after successful request
                }, error: function () {
                  isAjaxRequestSent = false; // Reset the flag on error
                },
              });

              // Extend the timestamp on reset
              newTimestamp = new Date(updateTime.setSeconds(updateTime.getSeconds() + intervalValue));
              localStorage.setItem('timer_auto_logout_reset-timer', newTimestamp);
            }
          });
          if (newTime === 0) {
            clearInterval(interval);
            setTimeout(() => {
              window.location.href = '/user/login';
            }, timeoutPadding*1000);
          }

        }, 1000);
      }

      checkTime();

      // End the timer
    }
  };
})(jQuery, Drupal, drupalSettings);
