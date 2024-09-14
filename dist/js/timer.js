document.addEventListener('DOMContentLoaded', function () {
  const intervalValueElement = document.querySelector('.auto_logout_jst_timer .interval_value');
  const intervalSetElement = document.querySelector('.auto_logout_jst_timer .interval-set');
  let remainingTime = parseInt(intervalValueElement.textContent, 10);

  function formatTime(seconds) {
    let hrs = Math.floor(seconds / 3600);
    let mins = Math.floor((seconds % 3600) / 60);
    let secs = seconds % 60;
    return `${hrs.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
  }

  function updateTimer() {
    if (remainingTime > 0) {
      remainingTime--;
      intervalSetElement.textContent = formatTime(remainingTime);
    } else {
      clearInterval(timerInterval);
      intervalSetElement.textContent = "Time's up!";
    }
  }

  intervalSetElement.textContent = formatTime(remainingTime);
  const timerInterval = setInterval(updateTimer, 1000);
});
