import boto3

client = boto3.client('codebuild')

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
            'value': '25',
            'type': 'PLAINTEXT'
        }
    ]
)

print(response1)
print("---")
print(response2)
