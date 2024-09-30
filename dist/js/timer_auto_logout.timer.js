(function ($, Drupal, once) {

  Drupal.behaviors.timerAutoLogout = {
    attach: function (context, settings) {

      // Ensure all the code runs only once
      once('timerAutoLogout', 'body').forEach(function () {

        // Start the timer

        // select interval value and timeout padding
        const intervalValue = +document.querySelector('.interval_value').innerHTML;
        let timeoutPadding = +document.querySelector('#auto_logout_timeout_padding').innerHTML;
        // set default value for timeout padding
        const timeoutForOpenModal = Number(timeoutPadding) < 5 ? 5 : Number(timeoutPadding);
        // get current date and add interval value
        let dateNow = new Date();
        // add seconds to current date
        dateNow.setSeconds(dateNow.getSeconds() + intervalValue);
        // get modal selector
        const parentModalTimer = document.querySelector(".parent-modal");
        const modalTimer = document.querySelector(".modal");
        const timerText = document.querySelector(".timer-text");
        // Store the timestamp in localStorage
        localStorage.setItem('timer_auto_logout_reset-timer', dateNow);
        // Flag to prevent multiple requests
        let isAjaxRequestSent = false;
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
          // Set interval to update the time
            const interval = setInterval(() => {
            const updateDate = new Date();
            // Get the timestamp from localStorage
            let newTimestamp = new Date(localStorage.getItem('timer_auto_logout_reset-timer'));
            let newTime = Math.floor((newTimestamp - updateDate) / 1000);

            // Update time display
              document.querySelector('.interval-set').innerText = formatSeconds(newTime > 0 ? newTime : 0)
            // Handle click event for resetting the timer, using once
            once('timerAutoLogoutClick', '#timer_auto_logout_reset-timer', context).forEach(function (element) {
              $(element).on('click', function () {
                handleClick();
              });
            });
            // Handle click event for resetting the timer, using once
            once('timer_auto_logout_reset-timer_modal', '.timer_auto_logout_reset-timer_modal', context).forEach(function (element) {
              $(element).on('click', function () {
                handleClick();
              });
            });
            function handleClick() {
              const updateTime = new Date()
              console.log('updateTime', updateTime);
              // Ensure the request is only sent once
              if (!isAjaxRequestSent) {
                isAjaxRequestSent = true; // Mark the request as sent
                $.ajax({
                  url: '/autologout_ajax_set_last',
                  type: 'GET',
                  success: function (data) {
                    // Reset the flag after successful request
                    isAjaxRequestSent = false;
                  },
                  error: function () {
                    // Reset the flag on error
                    isAjaxRequestSent = false;
                  },
                });
                parentModalTimer.style.display = "none";
                modalTimer.style.display = "none";

                // Extend the timestamp on reset
                newTimestamp = new Date(updateTime.setSeconds(updateTime.getSeconds() + intervalValue));
                localStorage.setItem('timer_auto_logout_reset-timer', newTimestamp);
              }
            }

            /*
             * Handle modal timer for auto logout
             */
            function handleModalTimer() {
              let second = timeoutForOpenModal;

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
            const finalTime = newTime +timeoutForOpenModal;
              console.log('timerAutoLogout', finalTime);

            if (newTime === 0) {
              handleModalTimer();
            }
            if (finalTime === 0) {
              clearInterval(interval);
              setTimeout(() => {
                window.location.href = '/user/login';
              }, 2000);
            }
          }, 1000);
        }
        checkTime();
      });
    }
  };
})(jQuery, Drupal, once);
