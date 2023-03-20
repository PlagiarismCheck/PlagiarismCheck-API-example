import requests
import json
import time

url = "https://plagiarismcheck.org/api/v1/text"
# individual token 
token = 'Pio....'

# create entity
payload = {
    "language": "en",
    "text":  """ 
Plagiarism is taking credit for someone else's writing as your own, including their language and ideas, without providing adequate credit.[1] 
The University of Cambridge defines plagiarism as: "submitting as one's own work, irrespective of intent to deceive, that which derives in part or in its entirety from the work of others without due acknowledgement."[2]
Wikipedia has three core content policies, of which two make it easy to plagiarize inadvertently. No original research prohibits us from adding our own ideas to articles, and Verifiability requires that articles be based on reliable published sources. These policies mean that Wikipedians are highly vulnerable to accusations of plagiarism because we must stick closely to sources, but not too closely. 
Because plagiarism can occur without an intention to deceive, concerns should focus on educating the editor and cleaning up the article.
    """}
headers = {
  'X-API-TOKEN': token,
}

response = requests.request("POST", url, headers=headers, data=payload)
responseJson = json.loads(response.text)

# entity id
textId = responseJson['data']['text']['id']
print("Text ID: {}".format(textId));
url = "https://plagiarismcheck.org/api/v1/text/{}".format(textId)

payload={}
headers = {
  'X-API-TOKEN': token
}
maxTries = 30
attempt = 1
isChecked = False
while attempt < maxTries and not isChecked:
    # get status of check
    response = requests.request("GET", url, headers=headers, data=payload)
    responseJson = json.loads(response.text)
    state = responseJson['data']['state']
    isChecked = state == 5
    if state == 5:
        stateLabel = "checked"
    if state == 3:
        stateLabel = "check in progress" 
    print("Attempt {} of {} Status: {} {}".format(attempt, maxTries, state, stateLabel))
    if isChecked:
        print("Checked")
    else:
        attempt += 1
        time.sleep(10)

if isChecked:
    url = "https://plagiarismcheck.org/api/v1/text/report/{}".format(textId)
    payload={}
    headers = {
    'X-API-TOKEN': token
    }
    response = requests.request("GET", url, headers=headers, data=payload)
    responseJson = json.loads(response.text);

    print("Result: {}%".format(responseJson['data']['report']['percent']))
