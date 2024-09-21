document.addEventListener('DOMContentLoaded', function () {
  const intervalValue = +document.querySelector('.interval_value').innerHTML;

  const openModal = +document.querySelector('.open_modal').innerHTML;
  const modal = document.querySelector('.modal-auto-logout')
  const parentModal = document.querySelector('.modal-auto-logout-parent')
  let dateNow = new Date()
  dateNow.setSeconds(dateNow.getSeconds() + intervalValue)

  localStorage.setItem('timestamp', dateNow)

  function formatSeconds(seconds) {
    // Calculate hours, minutes, and seconds
    const hrs = Math.floor(seconds / 3600);
    const mins = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;

    // Format each component with leading zeros if necessary
    const formattedHrs = String(hrs).padStart(2, '0');
    const formattedMins = String(mins).padStart(2, '0');
    const formattedSecs = String(secs).padStart(2, '0');

    // Return the formatted time
    return `${formattedHrs}:${formattedMins}:${formattedSecs}`;
  }

  function checkTime() {
    const timestamp = new Date(localStorage.getItem('timestamp'));
    let reduceTimestamp = timestamp.setSeconds(timestamp.getSeconds() - openModal);

    const interval = setInterval(() => {
      const updateDate = new Date();
      const isOpenModal = updateDate > reduceTimestamp;

      // Check if localstorage updated
      let newTimestamp = new Date(localStorage.getItem('timestamp'));

      const newTime = (Math.floor((newTimestamp - updateDate) / 1000));
      document.querySelector('.add-timer').addEventListener('click', () => {
        modal.style.display = 'none';
        parentModal.style.display = 'none';
        newTimestamp = new Date(newTimestamp.setSeconds(newTimestamp.getSeconds() + intervalValue));
        localStorage.setItem('timestamp', newTimestamp)
        reduceTimestamp = newTimestamp.setSeconds(newTimestamp.getSeconds() - openModal);
      })

      document.querySelector('.interval-set').innerText = formatSeconds(newTime);


      // Handle If timestamp changed
      if (timestamp !== newTimestamp) {
        reduceTimestamp = newTimestamp.setSeconds(newTimestamp.getSeconds() - openModal);
      }

      if (intervalValue && isOpenModal && modal && modal.style) {
        modal.style.display = 'grid';
        parentModal.style.display = 'grid';
      }
      // if tome finished
      newTime < 1 && clearInterval(interval);
    }, 1000);
  }

  checkTime();
});
