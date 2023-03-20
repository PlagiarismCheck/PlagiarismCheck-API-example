<!doctype html>
<html lang="en">
<head>
    <title>PlagiarismCheck example</title>
</head>
<body>
<form id="plagiarism-form">
    <div>
        <label for="content">Content</label>
    </div>
    <div>
        <textarea name="content" id="content" rows="15" cols="70"></textarea>
    </div>
    <div>
        <input type="submit" id="submit" value="Check">
    </div>
    <div id="status"></div>
</form>
<script>
  window.addEventListener('DOMContentLoaded', () => {
    document.getElementById('plagiarism-form').addEventListener('submit', e => {
      e.stopPropagation();
      e.preventDefault();
      document.getElementById('content').disabled = true;
      document.getElementById('submit').disabled = true;
      document.getElementById('status').innerHTML = 'Sending';
      window
        .fetch('send.php', {
          method: 'POST',
          body: new URLSearchParams({
            content: document.getElementById('content').value
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('status').innerHTML = `Report ID #${data.id}. Check in progress.`;
            const checkInterval = setInterval(() => {
              window
                .fetch('status.php', {
                  method: 'POST',
                  body: new URLSearchParams({
                    id: data.id
                  })
                })
                .then(response => response.json())
                .then(response => {
                  if (response.success) {
                    if (response.checked) {
                      clearInterval(checkInterval);
                      // fetch report
                      window
                        .fetch('report.php', {
                          method: 'POST',
                          body: new URLSearchParams({
                            id: data.id
                          })
                        })
                        .then(response => response.json())
                        .then(response => {
                          if (response.success) {
                            document.getElementById('status').innerHTML = `Report ID #${data.id}. Plagiarism percent is ${response.percent}%. `;
                            document.getElementById('content').disabled = false;
                            document.getElementById('submit').disabled = false;
                          }
                        });
                    } else {
                      document.getElementById('status').innerHTML += '.';
                    }
                  } else {
                    clearInterval(checkInterval);
                    document.getElementById('status').innerHTML = 'Error: ' + (response.error || 'Check is failed');
                    document.getElementById('content').disabled = false;
                    document.getElementById('submit').disabled = false;
                  }
                })
            }, 15000)
          } else {
            document.getElementById('status').innerHTML = 'Error: ' + data.error;
            document.getElementById('content').disabled = false;
            document.getElementById('submit').disabled = false;
          }
        });
      document.getElementById('status').innerHTML = 'Sent';
    });
  });
</script>
</body>
</html>