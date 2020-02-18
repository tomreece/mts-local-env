import boto3

client = boto3.client('codebuild')

client.start_build(projectName='code-coverage-two')
