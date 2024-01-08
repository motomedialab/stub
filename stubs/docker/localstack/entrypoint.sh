#!/bin/sh

# boot up localstack
echo "🔥 Firing up localstack s3..."

echo $BUCKET_NAME

# create our bucket
echo "Setting up your bucket ${BUCKET_NAME} in ${AWS_DEFAULT_REGION}!"
awslocal s3 mb s3://$BUCKET_NAME --region $AWS_DEFAULT_REGION
