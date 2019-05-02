These instructions assume you have installed the [OpenShift scripts](https://github.com/BCDevOps/openshift-developer-tools/blob/master/bin/README.md) and that they are accessible via your PATH environment variable.

# Param management
Run the genParams.sh whenever a new template is created or a change adds a new param
```
genParams.sh
```
Generate pipeline param files whenever you have a pipeline script to test and deploy.
```
genPipelineParams.sh
```

# Build
```
genBuilds.sh     # when you need to add a new build artifact 
genBuilds.sh -u  # when you have want to deploy modified param

```

# Deployment
```
getDepls.sh       # when you need to add a new deployment artifact
getDepls.sh -u    # when you need want to deploy a modified param

```

# Pipeline
Pipeline builds are managed by the build script phase.

