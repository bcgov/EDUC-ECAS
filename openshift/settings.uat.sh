# The UAT environment only exists in the 'test' environment.
export FULLY_QUALIFIED_NAMESPACE=pvpywj-test

export DEPLOYMENT_ENV_NAME="test"
export DEV="test"
export TEST="test"
export PROD="test"

# The UAT environment uses the builds and pipelines from the tools environment.
export SKIP_PIPELINE_PROCESSING=1