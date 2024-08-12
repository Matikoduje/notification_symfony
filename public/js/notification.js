function testNotification(type, csrfToken) {
  const message = prompt('Enter the message to send:', 'This is a test ' + type + ' message');
  fetch('/send-notification/' + type, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-Token': csrfToken
    },
    body: JSON.stringify({
      message: message
    })
  })
    .then(response => response.json())
    .then(data => alert(data.success || data.error))
    .catch(error => console.error('Error:', error));
}

document.addEventListener('DOMContentLoaded', function () {
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  document.getElementById('sms-button').addEventListener('click', function() {
    testNotification('sms', csrfToken);
  });

  document.getElementById('email-button').addEventListener('click', function() {
    testNotification('email', csrfToken);
  });

  document.getElementById('all-button').addEventListener('click', function() {
    testNotification('all', csrfToken);
  });
});
