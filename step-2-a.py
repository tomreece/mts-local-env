import boto3
import json
import time

client = boto3.client('codebuild')

print("Triggering build 1")

response1 = client.start_build(
    projectName='code-coverage-two',
    environmentVariablesOverride=[
        {
            'name': 'PREV_BUILD_ID',
            'value': '7e375873-1fbe-48d6-b555-80b90b768423',
            'type': 'PLAINTEXT'
        },
        {
            'name': 'GROUP_NUMBER',
            'value': '11',
            'type': 'PLAINTEXT'
        }
    ]
)

print("Triggering build 2")

response2 = client.start_build(
    projectName='code-coverage-two',
    environmentVariablesOverride=[
        {
            'name': 'PREV_BUILD_ID',
            'value': '7e375873-1fbe-48d6-b555-80b90b768423',
            'type': 'PLAINTEXT'
        },
        {
            'name': 'GROUP_NUMBER',
            'value': '11',
            'type': 'PLAINTEXT'
        }
    ]
)

done = False

while not done:
    print("Polling...")
    response = client.batch_get_builds(
        ids=[
            response1['build']['id'],
            response2['build']['id']
        ]
    )

    print(response)

    allDone = True

    for build in response['builds']:
        status = build['buildStatus']
        if status == 'IN_PROGRESS':
            allDone = False
            break

    if allDone:
        break

    time.sleep(30)

print("ALL DONE!")
