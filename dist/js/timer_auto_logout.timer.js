(function ($, Drupal, once) {
  console.log('timerAutoLogout');
  Drupal.behaviors.timerAutoLogout = {
    attach: function (context, settings) {

      // Ensure all the code runs only once
      once('timerAutoLogout', 'body').forEach(function () {

        // Start the timer

        const intervalValue = +document.querySelector('.interval_value').innerHTML;
        let timeoutPadding = +document.querySelector('#auto_logout_timeout_padding').innerHTML;
        timeoutPadding = Number(timeoutPadding) < 5 ? 5 : Number(timeoutPadding) + 2;
        let dateNow = new Date();
        dateNow.setSeconds(dateNow.getSeconds() + intervalValue);

        // get modal selector
        const parentModalTimer = document.querySelector(".parent-modal");
        const modalTimer = document.querySelector(".modal");
        const timerText = document.querySelector(".timer-text");

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
            console.log('Time left: ', formatSeconds(newTime));

            // Handle click event for resetting the timer, using once
            once('timerAutoLogoutClick', '#timer_auto_logout_reset-timer', context).forEach(function (element) {
              $(element).on('click', function () {
                handleClick();
              });
            });
            // Handle click event for resetting the timer, using once
            once('timer_auto_logout_reset-timer_modal', '.timer_auto_logout_reset-timer_modal', context).forEach(function (element) {
              $(element).on('click', function () {
                console.log('Resetting timer');
                handleClick();
              });
            });
            function handleClick() {
              console.log('Resetting timer function');
              const updateTime = new Date();

              // Ensure the request is only sent once
              if (!isAjaxRequestSent) {
                isAjaxRequestSent = true; // Mark the request as sent
                $.ajax({
                  url: '/autologout_ajax_set_last',
                  type: 'GET',
                  success: function (data) {
                    isAjaxRequestSent = false; // Reset the flag after successful request
                  },
                  error: function () {
                    isAjaxRequestSent = false; // Reset the flag on error
                  },
                });
                parentModalTimer.style.display = "none";
                modalTimer.style.display = "none";

                // Extend the timestamp on reset
                newTimestamp = new Date(updateTime.setSeconds(updateTime.getSeconds() + intervalValue));
                localStorage.setItem('timer_auto_logout_reset-timer', newTimestamp);
              }
            }
            function handleModalTimer() {
              let second = timeoutPadding;

              parentModalTimer.style.display = "grid";
              modalTimer.style.display = "grid";

              const timerInterval = setInterval(() => {
                second--;
                timerText.innerText = `${second}s`;

                if (second < 1) {
                  clearInterval(timerInterval);
                }
              }, 1000);
            }

            if (newTime === 0) {
              handleModalTimer();
              clearInterval(interval);
              setTimeout(() => {
                window.location.href = '/user/login';
              }, timeoutPadding * 1000);
            }

          }, 1000);
        }

        checkTime();
      });
    }
  };
})(jQuery, Drupal, once);
